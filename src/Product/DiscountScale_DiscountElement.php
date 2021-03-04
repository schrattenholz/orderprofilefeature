<?php

namespace Schrattenholz\OrderProfileFeature;

use SilverStripe\ORM\DataObject;
use Silverstripe\Forms\TextField;
use Silverstripe\Forms\NumericField;
use Silverstripe\Forms\CheckboxField;
use Silverstripe\Forms\DropdownField;
use Silverstripe\Forms\HiddenField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridField;
use Silverstripe\ORM\ArrayList;
use Silverstripe\Security\Group;
use Schrattenholz\Order\Preis;
use SilverStripe\Security\Permission;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

class DiscountScale_DiscountElement extends DataObject
{
	private static $default_sort=['SortOrder'];
	private static $db = array (
		'Min'=>'Int',
		'Max'=>'Int',
		'FixedDiscount'=>'Decimal(6,2)',
		'PercentageDiscount'=>'Decimal(6,2)',
		'SortOrder'=>'Int'
	);
	private static $summary_fields = [
		'Min' => 'Minimale Menge',
		'Max' => 'Maximale Menge',
		'FixedDiscount' => 'Abzuziehender Betrag (ohne €-Zeichen)',
		'PercentageDiscount' => 'Nachlass in Prozent (ohne %-Zeichen)',
    ];
	private static $has_one=[
		'PriceBlockElement'=>Preis::class
	];
	private static $many_many=[
        'OrderCustomerGroups' => [
			'through' => OrderCustomerGroups_DiscountElement::class,
            'from' => 'DiscountElement',
            'to' => 'OrderCustomerGroup'
        ]
    ];
 	private static $singular_name="Rabattelement";
	private static $plural_name="Rabattelemente";
	private static $table_name="DiscountScale_DiscountElement";

 	public function getCMSFields()
	{
		$fields=parent::getCMSFields();
		$fields->removeByName('OrderCustomerGroups');
		$fields->removeByName('PriceBlockElementID');
		$fields->removeByName('SortOrder');
		$min=new NumericField("Min","Mindestmenge");
		$min->setLocale("DE_De");
		$min->setScale(0);
		$max=new NumericField("Max",utf8_encode("Höchstmenge"));
		$max->setLocale("DE_De");
		$max->setScale(0);
		$fixedDiscount=new NumericField("FixedDiscount",utf8_encode("Abzuziehender Betrag (ohne EUR-Zeichen)"));
		$fixedDiscount->setLocale("DE_De");
		$fixedDiscount->setScale(2);
		$percentageDiscount=new NumericField("PercentageDiscount","Prozentualer Rabatt (ohne %-Zeichen)");
		$percentageDiscount->setLocale("DE_De");
		$percentageDiscount->setScale(1);
		$fields->addFieldsToTab('Root.Main', [
			$min,
			$max,
            $fixedDiscount,
			$percentageDiscount
        ]);
		$this->extend('updateCMSFields', $fields);
		
		
		//Kilopreise pro Kundengruppe
		$gridFieldConfig=GridFieldConfig::create()
			->addComponent(new GridFieldButtonRow('before'))
			->addComponent($dataColumns=new GridFieldDataColumns)
			->addComponent($editableColumns=new GridFieldEditableColumns())
			->addComponent(new GridFieldSortableHeader())
			->addComponent(new GridFieldFilterHeader())
			->addComponent(new GridFieldPaginator())
			
		;
		$dataColumns->setDisplayFields([
			'OrderCustomerGroup.Title' => 'Kundengruppe'
		]);
		$priceField=new NumericField("Price","Preis Grundeinheit");
		$priceField->setLocale("DE_De");
		$priceField->setScale(2);
		
		$editableColumns->setDisplayFields(array(
			/*'Active'  =>array(
					'title'=>'Wird angezeigt',
					'callback'=>function($record, $column, $grid) {
						return CheckboxField::create($column);
				}),*/
			'Price'  =>array(
					'title'=>'Preis Grundeinheit',
					'callback'=>function($record, $column, $grid) {
						return NumericField::create($column)->setScale(2);
				}),
			'AutoCalc'  =>array(
					'title'=>'Automatische Preisberechnung',
					'callback'=>function($record, $column, $grid) {
						return CheckboxField::create($column);
			})
		));
		$fields->addFieldToTab('Root.Main', GridField::create(
			'OrderCustomerGroups_DiscountElement',
			'Preise',
			OrderCustomerGroups_DiscountElement::get()->filter('DiscountElementID',$this->getOwner()->ID),
			$gridFieldConfig
		));
		return $fields;
	}	
	public function onAfterWrite(){
		
		foreach(OrderCustomerGroup::get() as $ocg){
			if($this->getOwner()->OrderCustomerGroups()->filter('ID',$ocg->ID)){
				$this->getOwner()->OrderCustomerGroups()->add($ocg);
			}
		}
		$this->updateGroupPrices();
		parent::onAfterWrite();
	}
	public function CMSPrice(){
		$cmsPrice="";
		foreach($this->OrderCustomerGroups() as $ocg){
			$relPreis=OrderCustomerGroups_DiscountElement::get()->filter(['DiscountElementID'=>$this->ID,'OrderCustomerGroupID'=>$ocg->ID])->First();
			$group=Group::get()->byID($ocg->GroupID);
			$cmsPrice.=$ocg->ShortCode.": ". str_replace ("." , ",", number_format($relPreis->Price,2))." EUR     ";
			
		}
		return $cmsPrice;
	}
	public function updateGroupPrices(){
		
			if(!$this->getOwner()->OrderCustomerGroups()){
				foreach(OrderCustomerGroup::get() as $ocg){
					if($this->getOwner()->OrderCustomerGroups()->filter('ID',$ocg->ID)){
						$this->getOwner()->OrderCustomerGroups()->add($ocg);
					}
				}
			}
			foreach(OrderCustomerGroup::get() as $ocg){
				$relDiscoutElement=OrderCustomerGroups_DiscountElement::get()->filter('DiscountElementID',$this->getOwner()->ID)->filter('OrderCustomerGroupID',$ocg->ID)->First();
			//	$relPriceBlockElement=Preis::get()->filter(['ID'=>$this->PriceBlockElementID,'OrderCustomerGroupID'=>$ocg->ID])->First();
				$relPriceBlockElement=OrderCustomerGroups_Preis::get()->filter('PreisID',$this->PriceBlockElementID)->filter('OrderCustomerGroupID',$ocg->ID)->First();
				
				if($relPriceBlockElement && $relPriceBlockElement->Price>0){
					if($relDiscoutElement->AutoCalc){
						//$productID=$this->getOwner()->Product()->ID;
						if($this->FixedDiscount>0){
						//Kilopreis
							$price=($relPriceBlockElement->Price)-$this->FixedDiscount;
						}else if($this->PercentageDiscount>0){
							$price=($relPriceBlockElement->Price)-($relPriceBlockElement->Price/100*$this->PercentageDiscount);
						}else{
							$price=($relPriceBlockElement->Price);
						}
						$groupPrice=$this->getOwner()->OrderCustomerGroups()->add($ocg,array('Price' => $price)); 
					}
				}else{
					//throw new ValidationException("Bitte geben Sie für das Produkt zuvor Kilopreise an.");
					return false;
				}
			}

	}
	public function getDiscountElementString($groupID){
		$relDiscoutElement=OrderCustomerGroups_DiscountElement::get()->filter([
			'DiscountElementID'=>$this->getOwner()->ID,
			'OrderCustomerGroupID'=>$groupID
			])->First();
		//Injector::inst()->get(LoggerInterface::class)->error(" ab ".$this->Min." bis ".$this->Max.": ".$relDiscoutElement->Price);
		return array("Range"=>"ab ".$this->Min." bis ".$this->Max,"Price"=>$this->formattedNumber($relDiscoutElement->Price)."&euro;");
	}
 	public function formattedNumber($val){
			return number_format(floatval($val), 2, ',', '.');
	}
	public function formattedWeight($val){
		if($val>=1000){
			return str_replace(".",",",round($val/1000,2)."kg");
		}else{
			return str_replace(".",",",$val."g");
		}
	}
}