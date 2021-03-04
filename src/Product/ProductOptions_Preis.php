<?php

/*

Join-Tabelle für die many_many Beziehung der Produkte mit den entsprechenden Produktoptionen und den jeweiligen GruppenEinstellungen (Preise/Active)

*/

namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\Order\Preis;
use Silverstripe\ORM\DataObject;
use SilverStripe\View\ArrayData;
use SilverStripe\Security\Permission;
class ProductOptions_Preis extends DataObject{
	private static $table_name="ProductOptions_Preis";
	private static $db = [
		'Price' => 'Decimal(6,2)',
		'Active'=>'Boolean(0)',
		'AutoCalc'=>'Boolean(1)'
	];
	private static $has_one = [
		'ProductOption' => ProductOption::class,
		'Preis' => Preis::class,
	];
	public function PriceObject(){
		$orderCustomerGroup=$this->Preis()->ActiveCustomerGroup();
		if($orderCustomerGroup){
			$ocg_preis=OrderCustomerGroups_Preis::get()->filter('PreisID',$this->getOwner()->ID)->filter('OrderCustomerGroupID',$orderCustomerGroup->ID)->First();

			if($orderCustomerGroup->VatExluded){
				//Nettopreis anzeigen
				$price=$this->getNetto($orderCustomerGroup->Vat);
			}else{
				//Bruttopreis anzeigen
				$price=$this->Price;
			}
			return new ArrayData(["Brutto"=>$this->Price,"Netto"=>$this->getNetto($orderCustomerGroup->Vat),"Price"=>$price,"IncludedVat"=>$this->getIncludedVAT($orderCustomerGroup->Vat),"VatExluded"=>$this->getIncludedVAT($orderCustomerGroup->VatExluded)]);
		}else{
			return false;
		}
	}
	public function getNetto($vat){
		$netto=$this->getIncludedVAT($vat);
		return ($this->Price-$netto);
	}
	public function getIncludedVAT($vat){
		return round($this->Price/100*$vat,2);
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