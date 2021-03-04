<?php

namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\OrderProfileFeature\OrderCustomerGroup;

use SGN\HasOneEdit\ProvidesHasOneInlineFields;

use Silverstripe\ORM\DataObject;
use SilverStripe\Security\Security;
use Silverstripe\Security\Group;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Permission;
class OrderProfileFeature_ClientOrder extends DataObject{
	private static $table_name='OrderProfileFeature_ClientOrder';
	private static $db=[
		'Title'=>'Text',
		'AdditionalNotes'=>'Text',
		'OrderStatus'=>'Enum("offen,abgeschlossen","offen")',
		'IsModel'=>'Boolean'
	];
	private static $default_sort = 'Created DESC';
	public function getExportFields() {
		return array(
			'Title' => 'Title',
			'OrderStatus' => 'OrderStatus',
			'AdditionalNotes'=>'AdditionalNotes',
			'Products'=>'ProductContainers.Product.Title'
		);
	}
	private static $has_one=[
		'ClientContainer'=>OrderProfileFeature_ClientContainer::class
	];
	private static $has_many=[
		'ProductContainers'=>OrderProfileFeature_ProductContainer::class
	];
	private static $summary_fields = [
        'Created' => ' Datum',
		'OrderStatus' => ' Bestellstatus',
        'ClientContainer.Surname' => ' Nachname',
        'ClientContainer.PhoneNumber' => ' Telefon',
		'ClientContainer.Email'=>' E-Mail',
		'AdditionalNotes' => ' Anmerkungen'
    ];
	private static $singular_name="Bestellung";
	private static $plural_name="Bestellungen";
	public function getCMSFields(){
		$fields=parent::getCMSFields();
		$fields->removeByName('ClientContainerID');
		$fields->removeByName('ProductContainers');
		$fields->removeByName('IsModel');
		$fields->addFieldToTab('Root.Main',new TextField('Title','Titel'));
		$fields->addFieldToTab('Root.Main',new TextareaField('AdditionalNotes','Bemerkungen zur Bestellungen'));
		$fields->addFieldToTab('Root.Main',new DropdownField('OrderStatus','Bestellstatus',$this->dbObject('OrderStatus')->enumValues()));
		
		$config = GridFieldConfig_RecordEditor::create();
		$gridField = new GridField('ProductContainers', 'Produkte', $this->getOwner()->ProductContainers()->sort('ProductSort'));
		$gridField->setConfig($config);
		$fields->addFieldToTab('Root.Produkte',$gridField);
		
		/*
		'Surname'=>'Varchar(255)',
		'FirstName'=>'Varchar(255)',
		'PhoneNumber' => 'Varchar(255)',
		'Street'=>'Varchar(255)',
		'ZIP'=>'Varchar(255)',
		'City'=>'Varchar(255))',
		'DSGVO'=>'Boolean',
		'Company'=>'Varchar(255)',
		'Gender'=>'Enum("Herr,Frau","Herr")',
		'Email'=>'Text'
		
		*/
		if($this->ClientContainer()->ClientID){
			$fields->addFieldToTab('Root.Kundendaten',new ReadonlyField('ClientID','Kundennummer',$this->ClientContainer()->ClientID));
		}
		$fields->addFieldToTab('Root.Kundendaten',new DropdownField('ClientContainer-_1_-Gender','Anrede',$this->ClientContainer()->dbObject('Gender')->enumValues()));
		$fields->addFieldToTab('Root.Kundendaten',new TextField('ClientContainer-_1_-Surname','Nachname'));
		$fields->addFieldToTab('Root.Kundendaten',new TextField('ClientContainer-_1_-FirstName','Vorname'));
		$fields->addFieldToTab('Root.Kundendaten',new TextField('ClientContainer-_1_-PhoneNumber','Telefonnummer'));
		$fields->addFieldToTab('Root.Kundendaten',new TextField('ClientContainer-_1_-Email','E-Mail'));
		$fields->addFieldToTab('Root.Kundendaten',new TextField('ClientContainer-_1_-Street','Strasse'));
		$fields->addFieldToTab('Root.Kundendaten',new TextField('ClientContainer-_1_-ZIP','PLZ'));
		$fields->addFieldToTab('Root.Kundendaten',new TextField('ClientContainer-_1_-City','Ort'));
		

		return $fields;
		
	}
	
	public function formattedNumber($val){
			return number_format($val, 2, ',', '.');
	}
	
	public function formattedWeight($val){
		if($val>=1000){
			return str_replace(".",",",round($val/1000,2)."kg");
		}else{
			return str_replace(".",",",$val."g");
		}
	}

}
