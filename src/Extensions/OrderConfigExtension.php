<?php
namespace Schrattenholz\OrderProfileFeature;

use SilverStripe\Core\Extension;
use Schrattenholz\Order\OrderConfig;
use Schrattenholz\OrderProfileFeature\OrderCustomerGroup;
class OrderConfigExtension extends Extension{
	private static $has_many=[
		'CustomerGroups'=>'Schrattenholz\\OrderProfileFeature\\OrderCustomerGroup'
	];
	private static $has_one=array(
		"AcountRoot"=>OrderProfileFeature_Profile::class
	);
}