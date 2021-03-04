<?php

namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\OrderProfileFeature\OrderCustomerGroup;

use SilverStripe\View\ArrayData;
use Silverstripe\ORM\DataObject;
use SilverStripe\Security\Security;
use Silverstripe\Security\Group;
use SilverStripe\Security\Permission;

class OrderProfileFeature_Basket extends DataObject{
	private static $table_name='OrderProfileFeature_Basket';
	private static $db=[
		'TimeStamp'=>'Varchar(255)',
		'AdditionalNotes'=>'Text'
	];
	private static $has_one=[
		'ClientContainer'=>OrderProfileFeature_ClientContainer::class
	];
	private static $has_many=[
		'ProductContainers'=>OrderProfileFeature_ProductContainer::class
	];
	public function onBeforeWrite(){
		if(!$this->TimeStamp){
			$this->TimeStamp=microtime(true);
		}
		parent::onBeforeWrite();
	}

}