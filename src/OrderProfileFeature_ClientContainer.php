<?php

namespace Schrattenholz\OrderProfileFeature;
use Silverstripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
class OrderProfileFeature_ClientContainer extends DataObject{
	private static $table_name='OrderProfileFeature_ClientContainer';
	private static $db=[
		'Surname'=>'Varchar(255)',
		'FirstName'=>'Varchar(255)',
		'PhoneNumber' => 'Varchar(255)',
		'Street'=>'Varchar(255)',
		'ZIP'=>'Varchar(255)',
		'City'=>'Varchar(255))',
		'DSGVO'=>'Boolean',
		'Company'=>'Varchar(255)',
		'Gender'=>'Enum("Herr,Frau","Herr")',
		'Email'=>'Text'
	];
	private static $summary_fields = [
        
        'Surname' => 'Nachname',
		'FirstName' => 'Vorname',
    ];
	private static $has_one=[
		'OrderCustomerGroup'=>OrderCustomerGroup::class,
		'Client'=>Member::class,
		'Order'=>ClientOrder::class
	];

}