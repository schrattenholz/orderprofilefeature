<?php 

namespace Schrattenholz\OrderProfileFeature;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\ConfirmedPasswordField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\OptionsetField;
use UndefinedOffset\NoCaptcha\Forms\NocaptchaField;
use SilverStripe\Security\Group;
class OrderProfileFeatureMemberExtension extends Extension 
{

	  private static $db = array(
		'PhoneNumber' => 'Text',
		'Street'=>'Text',
		'ZIP'=>'Text',
		'City'=>'Text',
		'DSGVO'=>'Boolean',
		'Company'=>'Text',
		'Gender'=>'Enum("Herr,Frau","Herr")',
		'DoubleOptIn'=>'Boolean',
		'NeedsDoubleOptIn'=>'Boolean'
	);
	private static $has_one=
	[
			'RequestedGroup'=>Group::class
	];
	  public function updateMemberFormFields(FieldList $fields) {
		$literal=new LiteralField("Spacer","&nbsp;");
		$literal->addExtraClass('col-12');
		$company=new TextField('Company', 'Firma');
		$company->addExtraClass('col-6');
		$gender=new OptionsetField("Gender","Anrede", $this->getOwner()->dbObject('Gender')->enumValues());
		$gender->addExtraClass('col-12');
		$firstname=new TextField('FirstName', 'Vorname');
		$firstname->addExtraClass('col-6');
		$surname=new TextField('Surname', 'Nachname');
		$surname->addExtraClass('col-6');
		$email=new EmailField('Email', 'Email');
		$email->addExtraClass('col-6');
		$phone=new TextField('PhoneNumber', 'Telefonnummer');
		$phone->addExtraClass('col-6');
		$street=new TextField('Street', 'Strasse');
		$street->addExtraClass('col-6');
		$zip=new TextField('ZIP', 'PLZ');
		$zip->addExtraClass('col-6');
		$city=new TextField('City', 'City');
		$city->addExtraClass('col-6');
		$dsgvo=new CheckboxField('DSGVO', '');
		$dsgvo->addExtraClass('col-6 pt-3 autowidth');
		$dsgvo->required_identifier = true; 
		$password=new ConfirmedPasswordField('Password');
		$password->addExtraClass('col-6');
		$password->setCanBeEmpty(true);
		$nocaptcha=new NocaptchaField('Captcha');
		$nocaptcha->addExtraClass('col-6');
		
		
		$fields->push($gender);
		$fields->push($firstname);
		$fields->push($surname);
		$fields->push($company);
		$fields->push($email); 
		$fields->push($phone);
		$fields->push($street);
		$fields->push($zip);
		$fields->push($city);
		$fields->push($dsgvo);
		$fields->push($password);
		$fields->push($nocaptcha);
		
	  }
		public function getShortSalutation(){
			$name="";
			if($this->owner->FirstName){
				return "Hallo ".$this->owner->FirstName;
			}else{
				return "Guten Tag ".$this->owner->Gender." ".$this->owner->Surname;
			}
		}
		public function getFullSalutation(){
			$name="";
			if($this->owner->FirstName){
				return "Hallo ".$this->owner->FirstName." ".$this->owner->Surname;
			}else{
				return "Guten Tag ".$this->owner->Gender." ".$this->owner->Surname;
			}
		}
}