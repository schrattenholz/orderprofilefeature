<?php

namespace Schrattenholz\OrderProfileFeature\PageTypes;
use Schrattenholz\Order\BasketController;
use SilverStripe\Core\Extension;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;

class OrderProfileFeature_BasketControllerExtension extends Extension {
    public function onAfterInit(){
      $this->getOwner()->renderWith("Schrattenholz\\OrderProfileFeature\\Layout\\Basket");
    }
}
