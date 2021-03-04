<?php

namespace Schrattenholz\OrderProfileFeature;
use SilverStripe\Core\Extension;
use Schrattenholz\Order\OrderConfig;
use Schrattenholz\Order\Unit;
use Schrattenholz\Order\Ingredient;
use Schrattenholz\Order\Addon;


class OrderProfileFeature_OrderAdmin extends Extension
{
    private static $managed_models = [
		OrderProfileFeature_ClientOrder::class,
		OrderConfig::class,
		Unit::class,
		Ingredient::class,
		Addon::class,
		ProductOption::class
    ];
	 private static $field_labels = [
		'OrderProfileFeature_ClientOrder'=>'Bestellungen',
		'OrderConfig' => 'Shopkonfiguration',
		'Unit'=>'Größeneinheiten',
		'Ingredient'=>'Zutatenliste',
		'Addon'=>'Produkteigenschaften',
		'ProductOption'=>'Produktoptionen'
   ];

}

