<?php

/*

Join-Tabelle für die many_many Beziehung der Preise mit der entsprechenden Kundengruppen und den jeweiligen GruppenEinstellungen (Preise/Active/AutoCalc)

*/

namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\Order\Preis;
use Silverstripe\ORM\DataObject;
use SilverStripe\Security\Permission;

class OrderCustomerGroups_Preis extends DataObject{
	private static $table_name="ordercustomergroups_preis";
	private static $db = [
		'Price' => 'Decimal(6,2)',
		'Active'=>'Boolean(1)',
		'AutoCalc'=>'Boolean(1)'
	];
	private static $has_one = [
		'OrderCustomerGroup' => OrderCustomerGroup::class,
		'Preis' => Preis::class,
	];
	public function BasePrice(){
		if($this->Preis()->Portionable){
			return $this->Preis()->Product()->KiloPrice()->Price/1000;
		}else{
			return $this->Price;
		}
	}
	public function getNetto($vat){
		$vat=$this->getIncludedVAT($vat);
		return ($this->BasePrice()-$vat);
	}
	public function getBrutto($vat){
		$vat=$this->getExcludedVAT($vat);
		return ($this->BasePrice()+$vat);
	}
	public function getIncludedVAT($vat){
		return round($this->BasePrice()/100*$vat,2);
	}
	public function getExcludedVAT($vat){
		return round($this->BasePrice()*($vat/100),2);
	}
	public function onBeforeWrite(){
		if(!$this->Price){
			$this->Price=$referencePrice=OrderCustomerGroups_Product::get()->filter([
				"ProductID"=>$this->Preis()->Product()->ID,
				"OrderCustomerGroupID"=>$this->OrderCustomerGroupID
				])->First()->Price;
		}
		// An und Abschalten der AutoClac Funktion beim Anlegen
		// jenachdem ob im Produkt ein Preis gesetz ist oder nicht
		/*
		$referencePrice=OrderCustomerGroups_Product::get()->filter([
		"ProductID"=>$this->Preis()->Product()->ID,
		"OrderCustomerGroupID"=>$this->OrderCustomerGroupID
		])->First()->Price;
		if($referencePrice>0){
			$this->AutoCalc=true;
		}else if($referencePrice){
			$this->AutoCalc=false;
		}
		*/
		parent::onBeforeWrite();
	}
 public function canView($member = null) 
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canEdit($member = null) 
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDelete($member = null) 
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canCreate($member = null, $context = []) 
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
}
