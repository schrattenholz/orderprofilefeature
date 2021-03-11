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

class OrderProfileFeature_DoubleOptIn_Controller extends PageController{
	 private static $allowed_actions = array (
		'edit'
	);
	 private static $url_handlers = array(        
	 	'' => 'DoubleOptIn',
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
			if($client->NeedsDoubleOptIn){
				if($this->getRequest()->param('Action')=='confirm'){
					$client->DoubleOptIn=true;
					$doc->Content="Deine Registrieung ist nun abgeschlossen. ";
					$defaultCostumerGroup=OrderCustomerGroup::get()->filter('IsDefault',true)->First();
					Injector::inst()->get(LoggerInterface::class)->error("defaultCostumerGroup->ID=".$defaultCostumerGroup->ID."client->RequestedGroupID=".$client->RequestedGroupID);
					if($defaultCostumerGroup->GroupID!=$client->RequestedGroupID){
						
						$doc->Content.="Da du dich als ".$client->RequestedGroup->Title." angemeldet hast, prüfen wir nun deine Gruppenzugehörigkeit und benachrichtigen dich dann umgehend per E-Mail. Bis dahin wirst Du im Shop als ".$defaultCostumerGroup->Group()->Title." behandelt. Natürlich werden wir die Preise auf der entgültigen Rechnung entsprechend deiner tatsächlichen Gruppenzugehörikeit anpassen.";
						$this->owner->sendGroupRequestToAdmin($client);
					}
					$client->NeedsDoubleOptIn=false;
					$message="confirmed";
				}else if($this->getRequest()->param('Action')=='deny'){
					$client->delete();
					$doc->Content="Die Registrierung wurde abgebrochen und Deine E-Mai-Adresse aus unserer Datenbank gelöscht.";
					$message="denied";
				}
				$client->write();
			}else{
				$doc->Content="Die Registrierung ist bereits abgeschlossen.";
			}
			
			return $doc->renderWith('Schrattenholz\\OrderProfileFeature\\DoubleOptIn'); 

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