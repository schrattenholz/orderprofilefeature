<?php
namespace Schrattenholz\OrderProfileFeature;

use Silverstripe\ORM\DataExtension;
use Schrattenholz\Order\OrderConfig;
use Schrattenholz\OrderProfileFeature\OrderCustomerGroup;
class OrderConfigExtension extends DataExtension{
	private static $has_many=[
		'CustomerGroups'=>'Schrattenholz\\OrderProfileFeature\\OrderCustomerGroup'
	];
	private static $has_one=array(
		"AcountRoot"=>OrderProfileFeature_Profile::class
	);
}