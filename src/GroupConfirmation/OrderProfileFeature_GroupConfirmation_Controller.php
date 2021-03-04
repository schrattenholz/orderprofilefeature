<?php

namespace Schrattenholz\OrderProfileFeature;

use PageController;
use Silverstripe\Security\Group;
use Silverstripe\Security\Member;
use SilverStripe\View\ArrayData;
use SilverStripe\Control\Email\Email;
use Schrattenholz\Order\OrderConfig;

use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

class OrderProfileFeature_GroupConfirmation_Controller extends PageController{
	 private static $allowed_actions = array (
		'edit'
	);
	 private static $url_handlers = array(        
	 	'' => 'GroupConfirmation',
        '$Action/$ClientID' => 'edit'
    );
	public function edit() {
			$member=Member::get()->byID($this->getRequest()->param('ClientID'));
			$doc=new ArrayData
			(
				array(
					'Content'=>"test",'Client'=>$member
				)
			);
			$client=Member::get()->byID($this->getRequest()->param('ClientID'));
			$defaultCostumerGroup=OrderCustomerGroup::get()->filter('IsDefault',true)->First();
			if($client->RequestedGroupID>0){
				if($this->getRequest()->param('Action')=='confirm'){
					$client->Groups()->add(Group::get()->byID($client->RequestedGroupID));
					$client->Groups()->remove(Group::get()->byID($defaultCostumerGroup->GroupID));
					$doc->Content="Sie haben den Status des Kunden als Wiederverkäufer bestätigt.<br/>Er wird per E-Mail informiert.";
					$message="confirmed";
				}else if($this->getRequest()->param('Action')=='deny'){					
					$doc->Content="Der Kunde wird weiterhin als Privatkunde geführt. Er wird per E-Mail informiert.";
					$message="denied";
				}
				$this->sendGroupConfirmation($client,$message);
				$client->RequestedGroupID=0;
				$client->write();
			}else{
				$doc->Content="Die Überprüfung ist bereits abgeschlossen.";
			}
			
			return $doc->renderWith('Schrattenholz\\OrderProfileFeature\\Layout\\GroupConfirmation'); 

	}
	public function sendGroupConfirmation($client,$message)
	{
		
		if($message=="confirmed"){
			$subject="Kundenkonto";
		}else{
			$subject="Kundenkonto";
		}
		$email = Email::create()
			->setHTMLTemplate('Schrattenholz\\OrderProfileFeature\\Layout\\Email_GroupConfirmationToClient') 
			->setData([
				//'BaseHref' => $_SERVER['DOCUMENT_ROOT'],
				'CheckoutAdress' =>$client,
				'OrderConfig'=>OrderConfig::get()->First(),
				'Message'=>$message
				
			])
			->setFrom(OrderConfig::get()->First()->InfoEmail)
			->setTo($client->Email)
			->setSubject($subject);
		if($email->send()){
			return true;
		}else{
				return false;
		}
	}
}