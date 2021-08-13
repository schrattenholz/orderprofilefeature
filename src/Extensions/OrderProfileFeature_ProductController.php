<?php

namespace Schrattenholz\OrderProfileFeature;

use Silverstripe\Core\Extension;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;
use Schrattenholz\Order\OrderConfig;

class OrderProfileFeature_ProductController extends Extension{
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