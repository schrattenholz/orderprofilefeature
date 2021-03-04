<?php


namespace Schrattenholz\OrderProfileFeature;

use Silverstripe\ORM\DataObject;

class ProductOption extends DataObject{
	private static $table_name='ProductOption';
	private static $db=[
		'Title'=>'Varchar(255)',
		'Shortcode'=>'Varchar(10)',
		'Price'=>'Decimal(6,2)',
		'Content'=>'Text'
	];
	private static $belongs_many_many=[
		'Prices'=>'Schrattenholz\\Order\\Preis',
		'Products'=>'Schrattenholz\\Order\\Product',
		'ProductContainers'=>OrderProfileFeature_ProductContainer::class
	];
	private static $singular_name="Produktoption";
	private static $plural_name="Produktoptionen";
}
?>