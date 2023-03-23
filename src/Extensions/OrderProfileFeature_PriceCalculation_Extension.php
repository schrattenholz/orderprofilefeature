<?php

namespace Schrattenholz\OrderProfileFeature;


use Silverstripe\ORM\DataExtension;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\Security\Security;
use Silverstripe\Security\Group;
use Schrattenholz\Order\Product;
use Schrattenholz\OrderProfile\Preis;
use Schrattenholz\OrderProfileFeature\OrderProfileFeature_ProductContainer;

//Debugging
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

class OrderProfileFeature_PriceCalculation_Extension extends DataExtension{
	public function TotalPrice(){
		$orderCustomerGroup=$this->getOwner()->ActiveCustomerGroup();
		$price=0;
		$vat=0;
		$caPrice=false;
		foreach($this->owner->ProductContainers() as $po){
			//Injector::inst()->get(LoggerInterface::class)->error('OrderProfileFeature_PriceCalculation_Extension::TotalPrice ->CompletePrice');
			$cP=$po->CompletePrice();
			$price+=($cP->Price);
			$vat+=($cP->Vat);
			if($cP->CaPrice){
				$caPrice=true;
			}
		}
		$vat=$this->getIncludedVat($orderCustomerGroup->Vat,$price);
		$deliveryVat=false;
		if($this->owner->DeliveryType->Price>0){
			$deliveryVat=$this->getIncludedVat(19,$this->owner->DeliveryType->Price);
			$price+=$this->owner->DeliveryType->Price;
		}
		return new ArrayData(array("Price"=>$price,"CaPrice"=>$caPrice,"Vat"=>$vat,"DeliveryVat"=>$deliveryVat));
	}
	public function ActiveCustomerGroup(){
			return OrderCustomerGroup::get()->filter('GroupID',$this->CurrentGroup()->ID)->First();
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
			//Injector::inst()->get(LoggerInterface::class)->error('is not logged in get default group:'.OrderCustomerGroup::get()->filter('IsDefault',true)->First()->GroupID);
			return Group::get()->byID(OrderCustomerGroup::get()->filter('IsDefault',true)->First()->GroupID);
		}
	}
	public function getNetto($vat,$price){
		$vat=$this->getIncludedVAT($vat);
		return ($price-$vat);
	}
	public function getBrutto($vat,$price){
		$vat=$this->getExcludedVAT($vat);
		return ($price+$vat);
	}
	public function getIncludedVAT($vat,$price){
		return round($price/100*$vat,2);
	}
	public function getExcludedVAT($vat,$price){
		return round($price*($vat/100),2);
	}
}
