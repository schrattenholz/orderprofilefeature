<?php

namespace Schrattenholz\OrderProfileFeature;
use Schrattenholz\OrderProfileFeature\OrderCustomerGroups;
use Schrattenholz\Order\Product;
use Schrattenholz\Order\Preis;
use Silverstripe\ORM\DataObject;
use SilverStripe\View\ArrayData;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;
use SilverStripe\Security\Permission;
class OrderProfileFeature_ProductContainer extends DataObject{
	private static $table_name="OrderProfileFeature_ProductContainer";
	private static $db=[
		'Quantity'=>'Int',
		'ProductSort'=>'Varchar(20)' 
	];
	private static $has_one=[
		'Basket'=>OrderProfileFeature_Basket::class,
		'ClientOrder'=>OrderProfileFeature_ClientOrder::class,
		'Product'=>Product::class,							//Produkt
		'PriceBlockElement'=>Preis::class, 					//Staffelement -> Vakuumierbar?
		//'GroupPrice'=>OrderCustomerGroup_Preis::class, 			//Staffelpreis der CustomerGroup
	];
	private static $many_many=[
        'ProductOptions' => [
			'through' => ProductOptions_ProductContainer::class,
            'from' => 'OrderProfileFeature_ProductContainer',
            'to' => 'ProductOption'
        ]
    ];
	
	public function Title(){
		$this->Product()->Title;
	}
	private static $summary_fields = [
        'Product.getSummaryTitle' => 'Produkt',
		'PriceBlockElement.getSummaryTitle'=>'Variante',
		'Quantity'=>'Menge'
    ];
	private static $singular_name="Produkt";
	private static $plural_name="Produkte";
	public function getCMSFields()
	{
		$fields=parent::getCMSFields();
		$fields->removeByName('PriceBlockElementID');
		$fields->removeByName('BasketID');
		$fields->removeByName('ClientOrderID');
		$fields->removeByName('ProductSort');
		$fields->removeByName("ProductID");
		$fields->removeByName("Quantity");
		
		$fields->addFieldToTab('Root.Main',new DropdownField('ProductID','Produkt',Product::get()->map('ID', 'SummaryTitle')));
		if($this->Product()->InPreSale){
			$fields->addFieldToTab('Root.Main',new LiteralField('Test','<h2>Der Artikel wird abverkauft.</h2> <p>Die eingebene Menge wird eventuell an den aktuellen Bestand angepasst.</p>'));
		}
		if ($this->isInDB() && $this->ProductID && !$this->PriceBlockElementID){
			$fields->addFieldToTab('Root.Main',new DropdownField('PriceBlockElementID','Variante',Preis::get()->map('ID', 'CompleteProductTitle')));
			$fields->addFieldToTab('Root.Main',new LiteralField("Info","<h3>Bitte wählen Sie eine Produktvariante und speichern Sie die Eingabe um weiteren Einstellungen vornehmen zu können.</h3>"));
		}else if ($this->isInDB() && $this->ProductID && $this->PriceBlockElementID){
			$freeQuantity=Preis::get()->byID($this->PriceBlockElementID)->Inventory;
			$fields->addFieldToTab('Root.Main',new TextField('Quantity','Menge','Produkt'));
		}else{			
			$fields->addFieldToTab('Root.Main',new LiteralField("Info","<h3>Bitte wählen Sie ein Produkt und speichern Sie die Eingabe um weiteren Einstellungen vornehmen zu können.</h3>"));			
		}

		
		return $fields;
	}
	 public function getCMSValidator()
    {	
		if($this->ProductID!=0 && $this->PriceBlockElementID!=0)
		{
			return new RequiredFields([
				'Quantity'
			]);
		}else{
			return new RequiredFields([
				'Quantity'
			]);
		}
    }
	public function onBeforeWrite(){
		if($this->ProductID!=0 && $this->PriceBlockElementID!==0 && $this->Quantity<=0)
		{
			 throw new ValidationException('Bitte geben Sie eine Mengenangabe ein.');
		}
		parent::onBeforeWrite();
	}
public function CompletePrice(){
	//Injector::inst()->get(LoggerInterface::class)->error('OrderProfileFeature_ProductContainer::CompletePrice-'.$this->Product()->Title);
			$productPrice=$this->ProductPrice();
			$basePrice=$productPrice->Price;//:floatval
			
			$additionalPrice=0;
			$caPrice=false;
			foreach($this->ProductOptions() as $po){
				$po_pc=ProductOptions_ProductContainer::get()->where(["ProductOptions_ProductContainer.ProductOptionID=".$po->ID,"ProductOptions_ProductContainer.OrderProfileFeature_ProductContainerID=".$this->owner->ID])->First();
				if($po_pc->Active){
					
					$additionalPrice+=floatval($po_pc->Price);
				}
				
				 
			}
			if($this->PriceBlockElementID>0){
					if(Preis::get()->byID($this->PriceBlockElementID)->CaPrice){
						$caPrice=true;
					}
				}else{
					
				}
			$price=new ArrayData(array("Price"=>number_format(($basePrice+$additionalPrice)*$this->Quantity,2, ".", ""),"CaPrice"=>$caPrice,"Vat"=>number_format($productPrice->Vat*$this->Quantity,2, ".", "")));
			return $price;
	}
	public function ProductPrice(){
		if($this->PriceBlockElementID){
		//	return $this->Product()->ActiveCustomerGroup();
			if($this->PriceBlockElement()->DiscountElements()->Count()>0){
				//Preis aus einem Elemente der Rabattstaffel
				
				$ocg_price=$this->getDiscount();
			}else{
				
				//Normaler Preis aus dem PriceBlockElement
				$ocg_price=OrderCustomerGroups_Preis::get()->filter([
					"PreisID"=>$this->PriceBlockElementID,
					"OrderCustomerGroupID"=>$this->Product()->ActiveCustomerGroup()->ID
				])->First();
			}
			$orderCustomerGroup=$this->Product()->ActiveCustomerGroup();
			if($orderCustomerGroup->VatExluded){
				return new ArrayData(["Netto"=>$ocg_price->BasePrice($orderCustomerGroup->Vat),"Brutto"=>$ocg_price->getBrutto($orderCustomerGroup->Vat),"Price"=>$ocg_price->BasePrice(),"Vat"=>$ocg_price->getExcludedVAT($orderCustomerGroup->Vat)]);
				
			}else{
				return new ArrayData(["Netto"=>$ocg_price->getNetto($orderCustomerGroup->Vat),"Brutto"=>$ocg_price->BasePrice($orderCustomerGroup->Vat),"Price"=>$ocg_price->BasePrice(),"Vat"=>$ocg_price->getIncludedVAT($orderCustomerGroup->Vat)]);
			}
		}else{
			Injector::inst()->get(LoggerInterface::class)->error('OrderProfileFeature_ProductContainer::ProductPrice----------------- kein Staffelement'.$this->Product()->Title);
			return $this->Product()->KiloPrice();
		}
	}
	public function getDiscount(){
				$discountElement=$this->PriceBlockElement()->DiscountElements()->filter([
				'Min:LessThanOrEqual' => $this->Quantity,
				'Max:GreaterThanOrEqual'=>$this->Quantity
				])->First();
				
				return OrderCustomerGroups_DiscountElement::get()->filter([
					"DiscountElementID"=>$discountElement->ID,
					"OrderCustomerGroupID"=>$this->Product()->ActiveCustomerGroup()->ID
				])->First();
	}
		public function formattedNumber($val){
			return number_format($val, 2, ',', '.');
	}
	public function formattedWeight($val){
		if($val>=1000){
			return number_format(round($val/1000,2),2,',','.')."kg";
		}else{
			return number_format($val,2,',','.')."g";
		}
	}

}
