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
		"Design"=>"Enum('KategorieListe,Produktfilter,KategorieMosaik','KategorieListe')"
	];
	public function updateCMSFields( FieldList $fields){
		$fields->addFieldToTab("Root.Main", new DropdownField( 'Design', 'Design', singleton(ProductList::class)->dbObject('Design')->enumValues()),'Content'); 
	}
	public function BasicExtension_DefaultImage($defaultImage){
		//Injector::inst()->get(LoggerInterface::class)->error('OrderProfileFeature_ProductList.php BasicExtension_DefaultImage ');
		if($defaultImage){
		}else if($this->owner->ProductImages->Count>0){
			return $defaultImage->DefaultImage=$this->owner->ProductImages()->First;	
		}else if($this->owner->MainImageID>0){
			return $defaultImage->DefaultImage=$this->owner->MainImage();
		}else if($this->owner->Children()->First()->DefaultImage()){
			return $defaultImage->DefaultImage=$this->owner->Children()->First()->DefaultImage();
		}
	}
}