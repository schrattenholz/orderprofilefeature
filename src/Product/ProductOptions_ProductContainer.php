<?php

/*

Join-Tabelle für die many_many Beziehung der Produkte mit den entsprechenden Produktoptionen und den jeweiligen GruppenEinstellungen (Preise/Active)

*/

namespace Schrattenholz\OrderProfileFeature;

use Silverstripe\ORM\DataObject;

class ProductOptions_ProductContainer extends DataObject{
	private static $table_name="ProductOptions_ProductContainer";
	private static $db = [
		'Price' => 'Decimal(6,2)',
		'Active'=>'Boolean(0)'
	];
	private static $has_one = [
		'ProductOption' => ProductOption::class,
		'OrderProfileFeature_ProductContainer' => OrderProfileFeature_ProductContainer::class,
	];
}