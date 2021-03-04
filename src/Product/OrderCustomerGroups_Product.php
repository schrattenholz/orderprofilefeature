<?php

/*

Join-Tabelle für die many_many Beziehung der Produkte mit der entsprechenden Kundengruppen und den jeweiligen GruppenEinstellungen (Preise/Active)

*/

namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\Order\Product;
use Silverstripe\ORM\DataObject;
use SilverStripe\Security\Permission;
class OrderCustomerGroups_Product extends DataObject{
	private static $table_name="ordercustomergroups_product";
	private static $db = [
		'Price' => 'Decimal(6,2)',
		'Active'=>'Boolean(1)'
	];
	private static $has_one = [
		'OrderCustomerGroup' => OrderCustomerGroup::class,
		'Product' => Product::class,
	];
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