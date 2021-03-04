<?php

namespace Schrattenholz\OrderProfileFeature;

use SChrattenholz\Order\ProductList;
use Silverstripe\ORM\DataExtension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

class OrderProfileFeature_ProductList extends DataExtension{
	private static $db=[
		"Design"=>"Enum('KategorieListe,Produktfilter','KategorieListe')"
	];
	public function updateCMSFields( FieldList $fields){
		$fields->addFieldToTab("Root.Main", new DropdownField( 'Design', 'Design', singleton(ProductList::class)->dbObject('Design')->enumValues()),'Content'); 
	}
}