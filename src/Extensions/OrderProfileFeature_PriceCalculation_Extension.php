<?php

namespace Schrattenholz\OrderProfileFeature;


use Silverstripe\ORM\DataExtension;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

use Schrattenholz\Order\Product;
use Schrattenholz\OrderProfile\Preis;
use Schrattenholz\OrderProfileFeature\OrderProfileFeature_ProductContainer;

//Debugging
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

class OrderProfileFeature_PriceCalculation_Extension extends DataExtension{
	public function TotalPrice(){
		$price=0;
		$vat=0;
		$caPrice=false;
		foreach($this->owner->ProductContainers() as $po){
			$price+=($po->CompletePrice()->Price);
			$vat+=($po->CompletePrice()->Vat);
			if($po->CompletePrice()->CaPrice){
				$caPrice=true;
			}
		}
		return new ArrayData(array("Price"=>$price,"CaPrice"=>$caPrice,"Vat"=>$vat));
	}
}