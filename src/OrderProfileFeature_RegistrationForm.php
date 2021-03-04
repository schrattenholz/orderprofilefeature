<?php

namespace Schrattenholz\OrderProfileFeature;

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
class OrderProfileFeature_RegistrationForm extends Form 
{

    /**
     * Our constructor only requires the controller and the name of the form
     * method. We'll create the fields and actions in here.
     *
     */
	 public static function getCheckoutAdress($controller){
		$basket=$controller->getBasket();
		
		if(Security::getCurrentUser()){
				$client=Security::getCurrentUser();
				$client->ShowReg=false;
				$client->Readonly=true;
				return $client;

		}else if($basket){
			if($basket->ClientContainerID>0){
				$cc=$basket->ClientContainer();
				$cc->ShowReg=true;
				$cc->Readonly=false;
				return $cc;
			}else{
				return false;
			}
		}
	}
    public function __construct($controller, $name) 
    {
		//$currentClient=new OrderProfileFeature_OrderExtension();
		$currentClient=$this->getCheckoutAdress($controller);
		$basket=$controller->getBasket();

			if($currentClient && $currentClient->Readonly){
				$c2=CompositeField::create(
					$header2=HeaderField::create('Header2', 'Adressdaten'),
					$company=TextField::create('Company', 'Firmenname (ggf.)',$currentClient->Company)->setAttribute('readonly','readonly'),
					$street=TextField::create('Street', 'Strasse/Nr',$currentClient->Street)->setAttribute('readonly','readonly'),
					$zip=TextField::create('ZIP', 'Postleizahl',$currentClient->ZIP)->setAttribute('readonly','readonly'),
					$city=TextField::create('City', 'Ort',$currentClient->City)->setAttribute('readonly','readonly')
				);
				$c3=CompositeField::create(
					$header3=HeaderField::create('Header3', 'Kontaktdaten'),
					$gender=OptionSetField::create('Gender', 'Anrede:', ['Herr'=>'Herr','Frau'=>'Frau'],$currentClient->Gender)->setDisabled(true),
					$firstname=TextField::create('FirstName', 'Vorname',$currentClient->FirstName)->setAttribute('readonly','readonly'),
					$surname=TextField::create('Surname', 'Nachname',$currentClient->Surname)->setAttribute('readonly','readonly'),
					$phonenumber=TextField::create('PhoneNumber', 'Telefon',$currentClient->PhoneNumber)->setAttribute('readonly','readonly'),
					$email=EmailField::create('Email', 'E-Mail',$currentClient->Email)->setAttribute('readonly','readonly')
				);
			}else if($currentClient && !$currentClient->Readonly){
				$c2=CompositeField::create(
					$header2=HeaderField::create('Header2', 'Adressdaten'),
					$company=TextField::create('Company', 'Firmenname (ggf.)',$currentClient->Company),
					$street=TextField::create('Street', 'Strasse/Nr',$currentClient->Street),
					$zip=TextField::create('ZIP', 'Postleizahl',$currentClient->ZIP),
					$city=TextField::create('City', 'Ort',$currentClient->City)
				);
				$c3=CompositeField::create(
					$header3=HeaderField::create('Header3', 'Kontaktdaten'),
					$gender=OptionSetField::create('Gender', 'Anrede:', ['Herr'=>'Herr','Frau'=>'Frau'],$currentClient->Gender),
					$firstname=TextField::create('FirstName', 'Vorname',$currentClient->FirstName),
					$surname=TextField::create('Surname', 'Nachname',$currentClient->Surname),
					$phonenumber=TextField::create('PhoneNumber', 'Telefon',$currentClient->PhoneNumber),
					$email=EmailField::create('Email', 'E-Mail',$currentClient->Email)
				);

			}else{
				$c2=CompositeField::create(
					$header2=HeaderField::create('Header2', 'Adressdaten'),
					$company=TextField::create('Company', 'Firmenname (ggf.)'),
					$street=TextField::create('Street', 'Strasse/Nr'),
					$zip=TextField::create('ZIP', 'Postleizahl'),
					$city=TextField::create('City', 'Ort')
				);
				$c3=CompositeField::create(
					$header3=HeaderField::create('Header3', 'Kontaktdaten'),
					$gender=OptionSetField::create('Gender', 'Anrede:', ['Herr'=>'Herr','Frau'=>'Frau']),
					$firstname=TextField::create('FirstName', 'Vorname'),
					$surname=TextField::create('Surname', 'Nachname'),
					$phonenumber=TextField::create('PhoneNumber', 'Telefon'),
					$email=EmailField::create('Email', 'E-Mail')
				);

			}
			
		$tabset=new TabSet( $name = "useraccounttab", 
				
				$registrieren=new Tab( 
					$title='Benutzerdaten', 
					//new HeaderField("Registrieren"), 
					$c1=CompositeField::create(
							
							$c3,
							$c2,
							$cUA=CompositeField::create(
							$useraccount=CheckboxField::create('CreateUserAccount','Möchten Sie einen Nutzerkonto anlegen?'),
							$customerGroup=OptionSetField::create('CreateUserAccountCustomerGroup', 'Sie sind:', OrderCustomerGroup::get()->filter("SelectableForFrontendUser",1)),
							$password=PasswordField::create ("CreateUserAccountPassword","Passwort"),
							$passwordConfirm=PasswordField::create ("CreateUserAccountPasswordConfirm","Passwort-Wiederholung")
						)
						
					) 
				),
				$einloggen=new Tab( 
					$title='Einloggen', 
					//new HeaderField("Einloggen"), 
					$cRegister=CompositeField::create(
						$cPW=CompositeField::create(
							$useraccountEmail=TextField::create('UserAccountEmail','E-Mail-Adresse'),
							$useraccountPassword=PasswordField::create ("UserAccountPassword","Passwort"),
							$loginBtn=LiteralField::create('LoginMember','<button name="loginMember" value="Einloggen" class="action btn btn-primary btn-shadow mb-2 mr-1" id="loginMember" type="button">Einloggen</button>')
						)
					) 
				)
			);
			if($currentClient && !$currentClient->ShowReg){
				$fields = new FieldList(
					$c3,
					$c2
				);
			}else{
				$fields = new FieldList(
					$tabset
					
				);
			}
		



		
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

		$registrieren->addExtraClass('active');
		
		$header2->addExtraClass('w-100 font-size-lg');
		$header3->addExtraClass('w-100 font-size-lg');
		$gender->addExtraClass('col-12');
		$useraccount->addExtraClass('col-12');
		$customerGroup->addExtraClass('col-12');
		$cPW->addExtraClass('row');
		$cUA->addExtraClass('row');
		$c2->addExtraClass('row');
		$c3->addExtraClass('row');
		
		
		$password->addExtraClass('col-sm-6');
		$passwordConfirm->addExtraClass('col-sm-6');
		$useraccountEmail->addExtraClass('col-sm-6');
		$useraccountPassword->addExtraClass('col-sm-6');
		$useraccount->addExtraClass('');
		/*$actions = new FieldList(
		$back=FormAction::create('back', 'Zurück zum Warenkorb')->setUseButtonTag(true)
                ->addExtraClass('action btn btn-secondary btn-shadow mb-2 mr-1'),
			$continue=FormAction::create('continue', 'Weiter zur Bestellübersicht')->setUseButtonTag(true)
                ->addExtraClass('action btn btn-primary btn-shadow mb-2 mr-1')
        );*/
		// Get the actions
    // As actions is a FieldList, push, insertBefore, removeByName and other
    // methods described for `Fields` also work for actions.

    /*$actions->push(
        FormAction::create('toCheckOutAdress', 'Another Button')
    );*/
	//$actions->addExtraClass('col-12 d-flex justify-content-between');
	//	$continue->addExtraClass('col-12');
	//	$back->addExtraClass('col-12');
		$required = new RequiredFields([
			'Password',
			'PasswordConfirm',
			'Street',
			'ZIP',
			'City',
			'CustomerGroup',
			'Gender',
			'Surname',
			//'PhoneNumber',
			'Email'
        ]);

        // now we create the actual form with our fields and actions defined
        // within this class
        parent::__construct($controller, $name, $fields, NULL, $required);

        // any modifications we need to make to the form.
        $this->setFormMethod('GET');

        $this->addExtraClass('no-action-styles');
        $this->disableSecurityToken();
        $this->loadDataFrom($_REQUEST);
    }
	public function toCheckOutAdress($data, $form){
		$this->redirect('adresse');
	}
}