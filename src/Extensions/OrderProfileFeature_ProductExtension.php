<?php

namespace Schrattenholz\OrderProfileFeature;

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
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Silverstripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Security;
use Silverstripe\Security\Group;
use SilverStripe\ORM\ValidationException;
use Schrattenholz\Order\OrderConfig;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;


class OrderProfileFeature_ProductExtension extends DataExtension{
		public function getAvailability($customerGroup=0){

		// Pruefen ob das Preiselement eine Bestand hat und für die aktive Nutzergruppe aktiviert ist.
		$availability=false;
		if(!$this->owner->OutOfStock){
			foreach($this->owner->Preise() as $p){
				
				if($p->IsActive()){
					
					// Das Element ist für die aktive Benutzgruppe aktiviert
					if($p->Inventory>0 || $p->InfiniteInventory){
						Injector::inst()->get(LoggerInterface::class)->error(' getAvailability='.$p->Title);
						$availability=true;
					}
				}
			}
		}
		return $availability;
	}
	private static $many_many=[
        'OrderCustomerGroups' => [
			'through' => OrderCustomerGroups_Product::class,
            'from' => 'Product',
            'to' => 'OrderCustomerGroup'
        ],
		'ProductOptions' => [
			'through' => ProductOptions_Product::class,
            'from' => 'Product',
            'to' => 'ProductOption'
        ]
    ];
	public function addExtension(FieldList $fields){
		

		//Produktoptionen
		$gridFieldConfig=GridFieldConfig::create()
			->addComponent(new GridFieldButtonRow('before'))

			->addComponent($editableColumns=new GridFieldEditableColumns())
			
		;

		$priceField=new NumericField("Price","Aufpreis");
		$priceField->setLocale("DE_De");
		$priceField->setScale(2);
		
		$editableColumns->setDisplayFields(array(

			'ProductOption.Title'  =>array(
					'title'=>'Aktiv',
					'callback'=>function($record, $column, $grid) {
						return TextField::create($column);
				}),
			'Active'  =>array(
					'title'=>'Aktiv',
					'callback'=>function($record, $column, $grid) {
						return CheckboxField::create($column);
				}),
			'Price'  =>array(
					'title'=>'',
					'callback'=>function($record, $column, $grid) {
						return NumericField::create($column)->setScale(2);
				})
		));
		$fields->addFieldToTab('Root.Produktoptionen', GridField::create(
			'ProductOptions_Product',
			'Produktoptionen',
			ProductOptions_Product::get()->filter('ProductID',$this->getOwner()->ID),
			$gridFieldConfig
		));
		
		$fields->addFieldToTab('Root.Produktoptionen',new LiteralField("po","<p>Wählen Sie die benötigten Produktoptionen aus.</p><p>Wenn Sie Produktvarianten verwenden, können Sie die Produktoptionen in den einzelnen Produktvariantenn zusätzlich individualisieren.</p><p>&nbsp;</p>"),"ProductOptions_Product");
		
		
		
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
				})
		));
		$fields->addFieldToTab('Root.Shop', GridField::create(
			'OrderCustomerGroups_Preis',
			'Preise',
			OrderCustomerGroups_Product::get()->filter('ProductID',$this->getOwner()->ID),
			$gridFieldConfig
		),'Quantity');
		
		
		//Produktvarianten
		$fields->addFieldToTab('Root.Produktvarianten', $gridfield=GridField::create(
		'Preise',
		'Staffelelemente muh',
		$this->getOwner()->Preise()->sort('SortOrder'),
		GridFieldConfig_RecordEditor::create()
		),"Content");
		
			
		$config = $gridfield->getConfig();
		$config->addComponent(new GridFieldOrderableRows('SortOrder'));
	
		$dataColumns = $config->getComponentByType(GridFieldDataColumns::class);
		$dataColumns->setDisplayFields([
			'DisplayAmount' => 'Menge',
			'CMSPrice'=>'Preise'
		]);
		
		
		$fields->removeFieldFromTab('Root.Shop','Price');
		$fields->removeFieldFromTab('Root.Shop','Amount');
		//$fields->removeFieldFromTab('Root.Shop','UnitID');

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
				return Group::get()->byID(OrderCustomerGroup::get()->filter('IsDefault',true)->First()->GroupID);
		} else {
			return Group::get()->byID(OrderCustomerGroup::get()->filter('IsDefault',true)->First()->GroupID);
		}
	}
	public function ActiveCustomerGroup(){
			return $this->getOwner()->OrderCustomerGroups()->filter('GroupID',$this->getOwner()->CurrentGroup()->ID)->First();
	}

	public function KiloPrice(){
		$orderCustomerGroup=$this->getOwner()->ActiveCustomerGroup();
		if($orderCustomerGroup){
			$ocg_product=OrderCustomerGroups_Product::get()->filter('ProductID',$this->getOwner()->ID)->filter('OrderCustomerGroupID',$this->getOwner()->ActiveCustomerGroup()->ID)->First();
			if($orderCustomerGroup->VatExluded){
				//Nettppreis anzeigen
				$price=$ocg_product->Price;
			}else{
				//Bruttopreis anzeigen
				$price=$ocg_product->Price;
			}
			return new ArrayData(["Price"=>$price,"BasePrice"=>$price]);
		}else{
			return false;
		}
	}
	public function GroupPreise(){
		$list=new ArrayList();
		foreach($this->getOwner()->Preise() as $price){
			
			if($price->IsActive()){
				
				$price->Price=$price->PriceObject()->Price;
				$list->push($price);
			}
		}
		return $list;
	}
	public function getSummaryTitle(){
		$sumTitle=$this->Title;
		if($this->Addons()->Count()>0){
			$sumTitle.=", ".$this->Addons()->First()->Title;
		}
		if($this->Ingredients()->Count()>0){
			$sumTitle.=", ".$this->Ingredients()->First()->Title;
		}
		return $sumTitle;//." mit ".$this->Wheels." Rädern";
	}
	public function onAfterWrite(){
		
		foreach(OrderCustomerGroup::get() as $ocg){
			if($this->getOwner()->OrderCustomerGroups()->filter('ID',$ocg->ID)){
				$this->getOwner()->OrderCustomerGroups()->add($ocg);
			}
		}
		foreach(ProductOption::get() as $po){
			if($this->getOwner()->ProductOptions()->filter('ID',$po->ID)){
				$this->getOwner()->ProductOptions()->add($po);
				$option=ProductOptions_Product::get()->where(["ProductOptionID"=>$this->getOwner()->ProductOptions()->Last()->ID,"ProductID"=>$this->owner->ID])->First();
				if(!$option->Price){
					$option->Price=$po->Price;
					$option->write();
				}
			}
		}
		foreach($this->getOwner()->OrderCustomerGroups() as $ocg){
			$relProduct=OrderCustomerGroups_Product::get()->filter('ProductID',$this->getOwner()->ID)->filter('OrderCustomerGroupID',$ocg->ID)->First();
			if($relProduct->Price>0){
				foreach($this->getOwner()->Preise() as $p){
					$p->updateGroupPrices();
				}
			}else{
				//throw new ValidationException("Bitte geben Sie zuvor Kilopreise für die verschiedenen Kundengruppen an.");
			}
		}
		
		parent::onAfterWrite();
	}
	public function BasicExtension_DefaultImage($defaultImage){
		
		
			Injector::inst()->get(LoggerInterface::class)->error('OrderProfileFeature_ProductExtension.php BasicExtension_DefaultImage TeaserImage()->ID='.$this->owner->MainImage()->Filename);
			
		if ($defaultImage->DefaultImage->ID>0){
			$defaultImage=$this->owner->MainImage();
			Injector::inst()->get(LoggerInterface::class)->error('OrderProfileFeature_ProductExtension.php BasicExtension_DefaultImage ProductImage()->ID= default vorhanden');
		//	Injector::inst()->get(LoggerInterface::class)->error('BlogExtension.php BasicExtension_DefaultImage ImageID='.$defaultImage->ID);
		
		}else if($this->owner->ProductImages()->Count()>0){
			Injector::inst()->get(LoggerInterface::class)->error('OrderProfileFeature_ProductExtension.php BasicExtension_DefaultImage erstes produktbild='.$this->owner->ProductImages()->First()->Filename);
			$defaultImage->DefaultImage= $this->owner->ProductImages()->Sort("SortOrder","ASC")->First();
		}else{
			Injector::inst()->get(LoggerInterface::class)->error('OrderProfileFeature_ProductExtension.php BasicExtension_DefaultImage kein produktbild='. OrderConfig::get()->First()->ProductImage()->Filename);
			$defaultImage->DefaultImage= OrderConfig::get()->First()->ProductImage();
		}
		return $defaultImage;
	}
	/*public function DefaultImage(){
		Injector::inst()->get(LoggerInterface::class)->error('ProductExtension.php BasicExtension_DefaultImage TeaserImage()->ID=');
		return "muh";
	}*/
}
