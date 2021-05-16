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
use Silverstripe\Forms\PasswordField ;
use UncleCheese\DisplayLogic\Wrapper;
use SilverStripe\View\Requirements;
use Schrattenholz\Order\Product;
use SilverStripe\Security\Security;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;
class OrderProfileFeature_ProfilController extends PageController{
	private static $allowed_actions = ['loadForm','OrderProfileFeature_ProfilRegistrationForm','OrderProfileFeature_ProfilLoginForm'];
	public function loadForm(){
		return $this->OrderProfileFeature_ProfilLoginForm();
	}
	 public static function getCheckoutAddress($controller){
		$basket=$controller->getBasket();
		
		if(Security::getCurrentUser()){
			$client=Security::getCurrentUser();
			$client->ShowReg=false;
			$client->Readonly=true;
			return $client;
		}
	}
	public function OrderProfileFeature_ProfilLoginForm(){
		$cPW=CompositeField::create(
			$useraccountEmail=EmailField::create('UserAccountEmail','E-Mail-Adresse'),
			$useraccountPassword=PasswordField::create ("UserAccountPassword","Passwort")
		);
		$useraccountEmail->addExtraClass('form-control');
		$useraccountPassword->addExtraClass('form-control');
		$fields = new FieldList(
			$cPW
		);
        $actions = new FieldList( 
            new FormAction('submit', 'Einloggen') 
        ); 
		$required = new RequiredFields([
			'UserAccountEmail',
			'UserAccountPassword'
			
        ]);
		return new Form($this, 'OrderProfileFeature_ProfilLoginForm', $fields, $actions, $required); 
	}
    public function OrderProfileFeature_ProfilRegistrationForm() 
    { 
		$c1=CompositeField::create(
				$cPW=CompositeField::create(
				$customerGroup=OptionSetField::create('CustomerGroup', 'Sie sind:', OrderCustomerGroup::get()),
				$password=PasswordField::create ("Password","Passwort"),
				$passwordConfirm=PasswordField::create ("PasswordConfirm","Passwort-Wiederholung")
			)
		);
		$c2=CompositeField::create(
			$header2=HeaderField::create('Header2', 'Adressdaten'),
			$company=TextField::create('Company', 'Firmenname'),
			$street=TextField::create('Street', 'Strasse/Nr'),
			$zip=TextField::create('ZIP', 'Postleizahl'),
			$city=TextField::create('City', 'Ort')
		);
		$c3=CompositeField::create(
			$header3=HeaderField::create('Header3', 'Kontaktdaten'),
			$gender=OptionSetField::create('Gender', 'Anrede:', ['Herr'=>'Herr','Frau'=>'Frau']),
			$surname=TextField::create('Surname', 'Nachname'),
			$firstname=TextField::create('FirstName', 'Vorname'),
			$phonenumber=TextField::create('PhoneNumber', 'Telefon'),
			$email=EmailField::create('Email', 'E-Mail')
		);
		$c4=CompositeField::create(
			//$header3=HeaderField::create('Header3', 'Kontaktdaten'),
			$agb=CheckboxField::create('AGB', 'Ich habe die Allgemeinen Geschäftsbedingungen gelesen und verstanden.'),
			$agbText=LiteralField::create('agbtext', '<p class="pl-5">Hier finden Sie unsere <a href="https://hof-lehnmuehle.de/agb" target="_blank" style="font-weight:bold;">AGBs</a>.</p>'),
			$dsgvo=CheckboxField::create('DSGVO', 'Ich stimme zu, dass meine Angaben zur Nutzung meines Kundenkontos erhoben und verarbeitet werden dürfen. Die Daten werden ausschließlich zur Nutzung des Internetangebotes von hof-lehnmuehle.de und der für die Bearbeitung meiner Bestellungen notwendigen Prozesse verwendet.'),
			$dsgvoText=LiteralField::create('dsgvotext', '<p class="pl-5">Detaillierte Informationen zum Umgang mit Nutzerdaten finden Sie in unserer <a href="https://hof-lehnmuehle.de/datenschutzerklaerung" target="_blank" style="font-weight:bold;">Datenschutzerklärung</a></p>.')
		);
		//$agb->setAllowHTML(1);
        $fields = new FieldList(
			$c2,
			$c3,
			$c1,
			$c4
		);
        $actions = new FieldList( 
            new FormAction('submit', 'Registrieren') 
        ); 
		
		$c2->addExtraClass('col-6 pt-5');
		$c3->addExtraClass('col-6 pt-5');
		$c1->addExtraClass('col-12 pt-5');
		//$c4->addExtraClass('col-6 pt-5');

		$agb->addExtraClass('form-control');
		$agbText->addExtraClass('form-control');
		$dsgvo->addExtraClass('form-control');
		$dsgvoText->addExtraClass('form-control');
		$company->addExtraClass('form-control');
		$street->addExtraClass('form-control');
		$zip->addExtraClass('form-control');
		$city->addExtraClass('form-control');
		$gender->addExtraClass('form-control');
		$surname->addExtraClass('form-control');
		$firstname->addExtraClass('form-control');
		$phonenumber->addExtraClass('form-control');
		$email->addExtraClass('form-control');
		$customerGroup->addExtraClass('form-control col-6');
		$password->addExtraClass('form-control col-6');
		$passwordConfirm->addExtraClass('form-control col-6');
		$required = new RequiredFields([
			'Password',
			'PasswordConfirm',
			'Street',
			'ZIP',
			'City',
			'CustomerGroup',
			'Gender',
			'Surname',
			'FirstName',
			'PhoneNumber',
			'Email',
			'AGB',
			'DSGVO'
        ]);
        return new Form($this, 'OrderProfileFeature_ProfilRegistrationForm', $fields, $actions, $required); 
    }
    protected function init()
    {
        parent::init();
		//Requirements::javascript('public/resources/vendor/schrattenholz/order/template/javascript/order.js');
	}
	
}