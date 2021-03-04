<?php

namespace Schrattenholz\OrderProfileFeature;

use Silverstripe\ORM\DataExtension;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;
use SilverStripe\Control\HTTPRequest;


class OrderProfileFeature_ProductController extends DataExtension{
	public function index(HTTPRequest $request){
		return $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
			  "Page",
			SSViewer::config()->uninherited('themes')
		));
	}
	public function getLayout(){
			return $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\Layout\\Product",
				SSViewer::config()->uninherited('themes')
			));

	}
	public function getAvailability($customerGroup=0){
		Injector::inst()->get(LoggerInterface::class)->error("=====getAvailability======");
		$availability=false;
		if(!$this->owner->OutOfStock){
			foreach($this->owner->Preise() as $p){
				if($p->Inventory>0 || $p->InfiniteInventory && $p->IsActive()){
					$availability=true;
				}
			}
		}
		return $availability;
	}
}