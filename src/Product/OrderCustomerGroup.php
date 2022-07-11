<?php

// Shopinterne Gruppenspezifikation, die eine Relation auf eine Gruppe aus dem Framework hat


namespace Schrattenholz\OrderProfileFeature;

use Silverstripe\ORM\DataObject;
use SilverStripe\Security\Group;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Permission;

class OrderCustomerGroup extends DataObject{
	private static $table_name='ordercustomergroup';
	private static $db=[
		'ShortCode'=>'Varchar(2)',
		'IsDefault'=>'Boolean',
		'VatExluded'=>'Boolean',
		'Vat'=>'Decimal(6,2)',
		'VatNote'=>'Text',
		'SelectableForFrontendUser'=>'Boolean(1)'
	];
	private static $has_one=[
		'Group'=>Group::class,
		'OrderConfig'=>'Schrattenholz\\Order\\OrderConfig'
	];
	private static $belongs_many_many=[
		'Preise'=>'Schrattenholz\\Order\\Preis'
		
	];
	private static $summary_fields = [
      'Title'
	];
	public function Title(){
		if($this->GroupID){
			return Group::get()->byID($this->GroupID)->Title;
		}else{
			return false;
		}
	}
	public function getCMSFields(){
		$fields=parent::getCMSFields();
		$fields->removeByName('Preise');
		$fields->addFieldToTab ('Root.Main',new TextField('ShortCode','Abkürzung'));
		return $fields;
    }

}