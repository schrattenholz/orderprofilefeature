<?php

namespace Schrattenholz\OrderProfileFeature\PageTypes;
use Schrattenholz\Order\BasketController;
use SilverStripe\ORM\DataExtension;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;

class OrderProfileFeature_BasketControllerExtension extends DataExtension {
    public function onAfterInit(){
      $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
            "Schrattenholz\\OrderProfileFeature\\Layout\\Basket",
            SSViewer::config()->uninherited('themes')
        ));
    }
}
