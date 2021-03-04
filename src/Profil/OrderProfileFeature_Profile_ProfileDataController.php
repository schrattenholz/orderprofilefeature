<?php

namespace Schrattenholz\OrderProfileFeature;

use PageController;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\Form;
use Silverstripe\Forms\PasswordField;
use SilverStripe\ORM\ArrayList;
use UncleCheese\DisplayLogic\Wrapper;
use SilverStripe\View\Requirements;
use Schrattenholz\Order\Product;
use SilverStripe\Security\Security;
use SilverStripe\Security\Member;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;
class OrderProfileFeature_Profile_ProfileDataController extends PageController{
	private static $allowed_actions = ['SaveProfileData','SavePassword'];
	public function SaveProfileData($data){
		$personenDaten=json_decode($this->getOwner()->utf8_urldecode($data['profiledata']),true);
		$returnValues=new ArrayList(['Status'=>'error','Message'=>false,'Value'=>false]);
		$client=Member::get()->filter('Email',$personenDaten['Email'])->First();
		if($client){
			$client->Email=$personenDaten['Email'];
			$client->Surname=$personenDaten['Surname'];
			$client->FirstName=$personenDaten['FirstName'];
			$client->Gender=$personenDaten['Gender'];
			$client->PhoneNumber=$personenDaten['PhoneNumber'];
			$client->Company=$personenDaten['Company'];
			$client->Street=$personenDaten['Street'];
			$client->ZIP=$personenDaten['ZIP'];
			$client->City=$personenDaten['City'];
			if($client->write()){
				$returnValues->Status='good';
				$returnValues->Message="Die Benutzerdaten wurden gespeichert";
				if($personenDaten['Company']==""){
					$value=$personenDaten['FirstName'].' '.$personenDaten['Surname'];
				}else{
					$value=$personenDaten['Company'].", ".$personenDaten['FirstName'].' '.$personenDaten['Surname'];
				}
				$returnValues->Value=$value;
				return json_encode($returnValues);
			}else{
				$returnValues->Status='error';
				$returnValues->Message="Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.";
				$returnValues->Value="";
				return json_encode($returnValues);
			}
		}else{
			$returnValues->Status='error';
			$returnValues->Message="Der Benutzer wurde nicht gefunden";
			$returnValues->Value="";
			return json_encode($returnValues);
		}
	}
	public function SavePassword($data){
		$personenDaten=json_decode($this->getOwner()->utf8_urldecode($data['passwords']),true);
		$returnValues=new ArrayList(['Status'=>'error','Message'=>false,'Value'=>false]);
		$client=Security::getCurrentUser();
		if($personenDaten['Password']==$personenDaten['PasswordConfirm']){
			$validateResult=$client->changePassword($personenDaten['Password'],1);
			if($validateResult->isValid()){
					$returnValues->Status='good';
					$returnValues->Message="Das Passwort wurde erfolgreich geändert.";
					$returnValues->Value='';
					return json_encode($returnValues);
			}else{
					$returnValues->Status='error';
					$returnValues->Message="Das Passwort wurde nicht geändert.";
					$returnValues->Value='';
					$messages=$validateResult->getMessages();
					foreach($messages as $m){
					
					$returnValues->Status='error';
					$returnValues->Message=$m['message'];
					$returnValues->Value='';
						return json_encode($returnValues);
					}
			}
			$returnValues->Status='good';
					$returnValues->Message="Das Passwort wurde erfolgreich geändert.";
					$returnValues->Value='';
					return json_encode($returnValues);
			/*if(strlen($personenDaten['Password'])>=8){
				if($client->changePassword($personenDaten['Password'],1))
				{
					$returnValues->Status='good';
					$returnValues->Message="Das Passwort wurde erfolgreich geändert.";
					$returnValues->Value='';
				}else{
					$returnValues->Status='error';
					$returnValues->Message="Das Passwort muss mindestens 8 Zeichen haben!";
					$returnValues->Value='';

				}
				return json_encode($returnValues);
			}else{
				$returnValues->Status='error';
				$returnValues->Message="Das Passwort muss mindestens 8 Zeichen haben!";
				$returnValues->Value='';
				return json_encode($returnValues);

			}*/
		}else{
			$returnValues->Status='error';
			$returnValues->Message="Die Passwörter stimmen nicht überein!";
			$returnValues->Value='';
			return json_encode($returnValues);
		}
	}
	
    public function OrderProfileFeature_Profile_ProfileData_Form() 
    { 
		$client=Security::getCurrentUser();
		$c2=CompositeField::create(
			$header2=HeaderField::create('Header2', 'Adressdaten'),
			$company=TextField::create('Company', 'Firmenname (ggf.)',$client->Company),
			$street=TextField::create('Street', 'Strasse/Nr',$client->Street),
			$zip=TextField::create('ZIP', 'Postleizahl',$client->ZIP),
			$city=TextField::create('City', 'Ort',$client->City)
		);
		$c3=CompositeField::create(
			$header3=HeaderField::create('Header3', 'Kontaktdaten'),
			$gender=OptionSetField::create('Gender', 'Anrede:', ['Herr'=>'Herr','Frau'=>'Frau'],$client->Gender),
			$firstname=TextField::create('FirstName', 'Vorname',$client->FirstName),
			$surname=TextField::create('Surname', 'Nachname',$client->Surname),
			$phonenumber=TextField::create('PhoneNumber', 'Telefon',$client->PhoneNumber),
			$email=TextField::create('Email', 'E-Mail',$client->Email)
		);
		//$agb->setAllowHTML(1);
        $fields = new FieldList(
			$c3,
			$c2
			
		);
        $actions = new FieldList( 
            $submit=new FormAction('submit', 'Änderungen speichern') 
        ); 
		$submit->addExtraClass('btn btn-primary');
		$c2->addExtraClass('row');
		$c3->addExtraClass('row');

		//$c4->addExtraClass('col-6 pt-5');

		$header2->addExtraClass('col-12');
		$header3->addExtraClass('col-12');
		$company->addExtraClass('col-sm-6');
		$street->addExtraClass('col-sm-6');
		$zip->addExtraClass('col-sm-6');
		$zip->setAttribute("oninput",'if(this.value.charAt(0)=="0"){this.value=this.value.replace(/\D/g,"");}else{this.value=this.value.replace(/\D/g,"");}');
		$zip->setAttribute("pattern",".{4,5}");
		$city->addExtraClass('col-sm-6');
		$gender->addExtraClass('col-12');
		$surname->addExtraClass('col-sm-6');
		$surname->setAttribute("pattern","[a-zA-ZöäüÖÄÜß \-]*");
		$firstname->addExtraClass('col-sm-6');
		$firstname->setAttribute("pattern","[a-zA-ZöäüÖÄÜß \-]*");
		$phonenumber->addExtraClass('col-sm-6');
		$phonenumber->setAttribute("oninput",'if(this.value.charAt(0)=="0"){this.value=this.value.replace(/\D/g,"");}else{this.value=this.value.replace(/\D/g,"");}');
		$email->addExtraClass('col-sm-6');
		$email->setAttribute("pattern","[^@\s]+@[^@\s]+\.[^@\s]+");
		$email->setAttribute("placeholder","max@mustermann.de");
		$required = new RequiredFields([
			'Street',
			'ZIP',
			'City',
			'CustomerGroup',
			'Gender',
			'Surname',
			//'PhoneNumber',
			'Email',
			'AGB',
			'DSGVO'
        ]);
        return new Form($this, 'OrderProfileFeature_Profile_ProfileData_Form', $fields, $actions, $required); 
    }
	public function OrderProfileFeature_Profile_ProfileData_PasswordForm(){
		$password=PasswordField::create ("Password","Passwort");
		$passwordConfirm=PasswordField::create ("PasswordConfirm","Passwort-Wiederholung");
		$password->addExtraClass(' col-sm-6');
		$passwordConfirm->addExtraClass(' col-sm-6');
		$c2=CompositeField::create(
			$password,
			$passwordConfirm

		);
		$c2->addExtraClass('col-12 ');
		$header=HeaderField::create('Header3', 'Passwort ändern');
		$header->addExtraClass('mt-5');
		$fields = new FieldList(
			$header,
			$c2
		);
		$required = new RequiredFields([
			'Password',
			'PasswordConfirm'
		]);
		$actions = new FieldList( 
			$submit=new FormAction('submit', 'Passwort speichern') 
		);  
				$submit->addExtraClass('btn btn-primary');

		return new Form($this, 'OrderProfileFeature_Profile_ProfileData_PasswordForm', $fields, $actions, $required); 
	}
    protected function init()
    {
        parent::init();
		//Requirements::javascript('public/resources/vendor/schrattenholz/order/template/javascript/order.js');
	}
	
}