<?php

namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\Order\Product;
use Schrattenholz\OrderProfile\Preis;
use Schrattenholz\OrderProfileFeature\OrderProfileFeature_ProductContainer;


use Silverstripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use Silverstripe\ORM\DataExtension;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\ReadonlyField; 
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\CheckboxField;
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
use SilverStripe\Security\Security;
use Silverstripe\Security\Group;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

use UncleCheese\DisplayLogic;

class OrderProfileFeature_PreisExtension extends DataExtension{
	private static $allowed_actions=[
		'FullTitle'
	];
	private static $db=[
		'BlockedQuantity'=>'Int',
		'SoldQuantity'=>'Float',
		'Portionable'=>'Boolean',
		'Portion'=>'Int',
		'PortionMin'=>'Int',
		'PortionMax'=>'Int'
	];
	private static $many_many=[
        'OrderCustomerGroups' => [
			'through' => OrderCustomerGroups_Preis::class,
            'from' => 'Preis',
            'to' => 'OrderCustomerGroup'
        ]
    ];

public function getAvailability($customerGroup=0){
	$availability=false;
	if($this->owner->IsActive()){	
		// Das Element ist für die aktive Benutzgruppe aktiviert
		if($this->owner->Inventory>0 || $this->owner->InfiniteInventory){
			//Injector::inst()->get(LoggerInterface::class)->error(' getAvailability='.$p->Title);
			$availability=true;
		}
	}
	return $availability;
}
public function CurrentGroup(){
		$member = Security::getCurrentUser();
		$customerGroups=OrderCustomerGroup::get();	
		if($member){
				foreach($customerGroups as $cg){
					if($member->inGroup($cg->Group)){
						return Group::get()->byID($cg->GroupID);
					}
				}
				//Injector::inst()->get(LoggerInterface::class)->error('is not in customerGroups');
				return Group::get()->byID(OrderCustomerGroup::get()->filter('IsDefault',true)->First()->GroupID);
		} else {
			//Injector::inst()->get(LoggerInterface::class)->error('is not logged in get default group:'.OrderCustomerGroup::get()->filter('IsDefault',true)->First()->GroupID);
			return Group::get()->byID(OrderCustomerGroup::get()->filter('IsDefault',true)->First()->GroupID);
		}
	}
	public function ActiveCustomerGroup(){
			return $this->getOwner()->OrderCustomerGroups()->filter('GroupID',$this->getOwner()->CurrentGroup()->ID)->First();
	}
	public function PriceObject(){
		$orderCustomerGroup=$this->getOwner()->ActiveCustomerGroup();
		if($orderCustomerGroup){
			$ocg_preis=OrderCustomerGroups_Preis::get()->filter('PreisID',$this->getOwner()->ID)->filter('OrderCustomerGroupID',$orderCustomerGroup->ID)->First();
				//Bruttopreis anzeigen
				$price=$ocg_preis->Price;
			return new ArrayData(["Brutto"=>$ocg_preis->Price,"Netto"=>$ocg_preis->getNetto($orderCustomerGroup->Vat),"Price"=>$price,"IncludedVat"=>$ocg_preis->getIncludedVAT($orderCustomerGroup->Vat),"VatExluded"=>$ocg_preis->getIncludedVAT($orderCustomerGroup->VatExluded)]);
		}else{
			return false;
		}
	}
	public function CMSPrice(){
		$cmsPrice="";
		foreach($this->getOwner()->OrderCustomerGroups() as $ocg){
			$relPreis=OrderCustomerGroups_Preis::get()->filter('PreisID',$this->getOwner()->ID)->filter('OrderCustomerGroupID',$ocg->ID)->First();
			$group=Group::get()->byID($ocg->GroupID);
			$cmsPrice.=$ocg->ShortCode.": ". str_replace ("." , ",", $relPreis->Price)."€   ";
		}
		return $cmsPrice;
	}
	public function IsActive(){
		Injector::inst()->get(LoggerInterface::class)->error("Finde Active in Preis_OrderCostumerGroup    ");
		$orderCustomerGroup=$this->getOwner()->OrderCustomerGroups()->filter('GroupID',$this->getOwner()->CurrentGroup()->ID)->First();
		
		if($orderCustomerGroup){
			
			$relPreis=OrderCustomerGroups_Preis::get()->filter('PreisID',$this->getOwner()->ID)->filter('OrderCustomerGroupID',$orderCustomerGroup->ID)->First();
			return $relPreis->Active;
		}else{
			return false;
		}
	}
	public function HasAutoCalc(){
//		$this->getOwner()->OrderCustomerGroups()->where('"OrderCustomerGroups_Preis"."OrderCustomerGroupsID"'=>$id);
		return $this->getOwner()->OrderCustomerGroups()->filter('GroupID',$this->getOwner()->CurrentGroup()->ID)->First()->AutoCalc;
	}
	public function updateCMSFields(FieldList $fields){
			$fields->removeFieldFromTab("Root.Main","Amount");
			$fields->removeFieldFromTab("Root.Main","Inventory");
			$fields->removeFieldFromTab('Root.Main','Price');
			$fields->removeByName('Preise');
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
			$priceField=new NumericField("Price","Preis");
			$priceField->setLocale("DE_De");
			$priceField->setScale(2);
			$editableColumns->setDisplayFields(array(
				'Price'  =>array(
						'title'=>'Preis',
						'callback'=>function($record, $column, $grid) {
							return NumericField::create($column)->setScale(2);
					}),
				'Active'  =>array(
						'title'=>'Wird angezeigt',
						'callback'=>function($record, $column, $grid) {
							return CheckboxField::create($column);
					}),
				'AutoCalc'  =>array(
						'title'=>'Automatische Preisberechnung',
						'callback'=>function($record, $column, $grid) {
							return CheckboxField::create($column);
					})
			));
			$num1=new TextField("SoldQuantity","Verkaufte Stückzahl");
			$portionable=new CheckboxField("Portionable","Wird portioniert angeboten");
			$portion=NumericField::create("Portion","Größe einer Portion")->displayIf('Portionable')->isChecked()->end();
			$portionableInfo=LiteralField::create("PortionableInfo", "<p><strong>Alle Eingaben in Gramm. 'Stückzahl' sollte durch 'Größe einer Portion' teilbar sein</strong></p>")->displayIf('Portionable')->isChecked()->end();
			$portionMin=NumericField::create("PortionMin","Mindestmenge")->displayIf('Portionable')->isChecked()->end();
			$portionMax=NumericField::create("PortionMax","Maximale Menge")->displayIf('Portionable')->isChecked()->end();
			$amount=TextField::create('Amount','Menge pro Einheit (Gewicht in Gramm)')->displayIf('Portionable')->isNotChecked()->end();
			$fields->addFieldToTab('Root.Main',$amount,'Portionable');
			//$fields->addFieldToTab('Root.Main',$num1,'');
			$fields->addFieldsToTab('Root.Main',array($portionable,$portionableInfo,$portion,$portionMin,$portionMax),"OrderCustomerGroups_Preis");
			$fields->addFieldToTab('Root.Main', GridField::create(
				'OrderCustomerGroups_Preis',
				'Preise',
				OrderCustomerGroups_Preis::get()->filter('PreisID',$this->getOwner()->ID),
				$gridFieldConfig
			));
			//$fields->changeFieldOrder(['Content','Attributes','CaPrice','ShowAmount','Unit','Amount','Portionable','Portion','PortionMax','PortionMin','Inventory','OrderCustomerGroups_Preis']);


    }
	public function updateFullTitle($data){
		$title=$data->Title="";
		if($this->owner->Content){
			$title.=$this->owner->Content;
		}

		foreach($this->owner->Attributes() as $a){
		if($title!=""){
					$title.=", ";
				}
			$title.=$a->Title;
		}
		if(!$this->owner->Portionable){
			if(!$this->owner->Content && $this->owner->ShowAmount || $this->owner->Product()->ShowBasePrice){
					if($title!=""){
						$title.=", ";
					}
				if($this->owner->CaPrice){

					$title.="ca. ";
				}
				$title.=$this->owner->getDisplayAmount();
			}
		}
		if($this->owner->Product()->ShowBasePrice && $data->ShowBasePrice){
			if($title!=""){
				$title.=", ";
			}
			$title.=$this->owner->formattedNumber($this->owner->Product()->KiloPrice()->Price) ."€/".$this->owner->Product()->Unit->Shortcode;
		}
$data->Title=$title;
		//return $title;
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
	public function updateGroupPrices(){

			if(!$this->getOwner()->OrderCustomerGroups()){
				foreach(OrderCustomerGroup::get() as $ocg){
					if($this->getOwner()->OrderCustomerGroups()->filter('ID',$ocg->ID)){
						$this->getOwner()->OrderCustomerGroups()->add($ocg);
					}
				}
			}
			foreach(OrderCustomerGroup::get() as $ocg){
				$relPreis=OrderCustomerGroups_Preis::get()->filter('PreisID',$this->getOwner()->ID)->filter('OrderCustomerGroupID',$ocg->ID)->First();
				$relProduct=OrderCustomerGroups_Product::get()->filter('ProductID',$this->getOwner()->ProductID)->filter('OrderCustomerGroupID',$ocg->ID)->First();
				if($relProduct && $relProduct->Price>0){
					if($relPreis->AutoCalc){
						//$productID=$this->getOwner()->Product()->ID;
						if($this->getOwner()->Amount>0 && $this->getOwner()->Unit=="weight"){
							//Kilopreis
							if($this->getOwner()->Portionable){
								$price=($relProduct->Price)*($this->getOwner()->PortionMin/1000);
							}else{
								$price=($relProduct->Price)*($this->getOwner()->Amount/1000);
							}
							
						}else if($this->getOwner()->Amount>0 && $this->getOwner()->Unit=="piece"){
							//Stückpreis
							if($this->getOwner()->Portionable){
								$price=($relProduct->Price)*($this->getOwner()->PortionMin);
							}else{
								$price=($relProduct->Price)*($this->getOwner()->Amount);
							}
						}else{
							$price=($relProduct->Price);
						}
						$groupPrice=$this->getOwner()->OrderCustomerGroups()->add($ocg,array('Price' => $price)); 
					}
				}else{
					//throw new ValidationException("Bitte geben Sie für das Produkt zuvor Kilopreise an.");
					return false;
				}
			}

	}
	
}
