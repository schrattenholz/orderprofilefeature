<?php

/*

Join-Tabelle für die many_many Beziehung der Produkte mit der entsprechenden Kundengruppen und den jeweiligen GruppenEinstellungen (Preise/Active)

*/

namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\Order\Product;
use Silverstripe\ORM\DataObject;

class OrderCustomerGroups_DiscountElement extends DataObject{
	private static $table_name="OrderCustomerGroups_DiscountElement";
	private static $db = [
		'Price' => 'Decimal(6,2)',
		'Active'=>'Boolean(1)',
		'AutoCalc'=>'Boolean(1)'
	];
	private static $has_one = [
		'OrderCustomerGroup' => OrderCustomerGroup::class,
		'DiscountElement' => DiscountScale_DiscountElement::class,
	];
	public function BasePrice(){
		if($this->DiscountElement()->PriceBlockElement()->Portionable){
			return $this->Price/1000;
		}else{
			return $this->Price;
		}
	}
	public function getNetto($vat){
		$netto=$this->getIncludedVAT($vat);
		return ($this->Price-$netto);
	}
	public function getBrutto($vat){
		$vat=$this->getExcludedVAT($vat);
		return ($this->BasePrice()+$vat);
	}
	public function getIncludedVAT($vat){
		return round($this->Price/100*$vat,2);
	}
	public function getExcludedVAT($vat){
		return round($this->BasePrice()*($vat/100),2);
	}
}