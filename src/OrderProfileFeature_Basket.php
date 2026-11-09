<?php

namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\OrderProfileFeature\OrderCustomerGroup;

use SilverStripe\Model\ArrayData;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Security;
use SilverStripe\Security\Group;
use SilverStripe\Security\Permission;

class OrderProfileFeature_Basket extends DataObject{
	private static $table_name='OrderProfileFeature_Basket';
	private static $db=[
		'TimeStamp'=>'Varchar(255)',
		'AdditionalNotes'=>'Text',
		// Entwurf aus der Bestell-App (api/erfassung/entwurf); die Schnittstelle
		// fasst nur solche Koerbe an, nie den Warenkorb eines Shop-Besuchers
		'Erfassung'=>'Boolean'
	];
	private static $has_one=[
		'ClientContainer'=>OrderProfileFeature_ClientContainer::class
	];
	private static $has_many=[
		'ProductContainers'=>OrderProfileFeature_ProductContainer::class
	];
	public function onBeforeWrite(){
		if(!$this->TimeStamp){
			$this->TimeStamp=(string)microtime(true);
		}
		parent::onBeforeWrite();
	}

}