<?php

namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\Order\OrderExtension;
use Schrattenholz\Order\Product;
use Schrattenholz\Order\OrderConfig;
use SilverStripe\Core\Extension;
use SilverStripe\Security\Security;
use SilverStripe\Security\Group;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\ConfirmedPasswordField ;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use Schrattenholz\OrderSale\OrderSale_ClientContainer;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Security\Member;
use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Control\Controller;
use Psr\Log\LoggerInterface;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Control\Email\Email;
use SilverStripe\Model\ArrayData;
use SilverStripe\View\Requirements;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;
use SilverStripe\SiteConfig\SiteConfig;
use Schrattenholz\Order\Preis;
class OrderProfileFeature_ProductDiscountExtension extends Extension{

	private static $allowed_actions = array (
		'ProductDiscountScale',
	);
	public function ProductDiscountScale($productID=0,$priceBlockElementID=0){
		if(isset($_GET['v'])){
			$priceBlockElementID=$_GET['v'];
		}
		$values=new ArrayList();
		
		$basket=$this->owner->getBasket();
		$groupID=$this->owner->CurrentOrderCustomerGroup()->ID;
		$priceBlockElements=Product::get()->byID($this->owner->ID)->GroupPreise();
		Injector::inst()->get(LoggerInterface::class)->error(Product::get()->byID($this->owner->ID)->Title."   ProductDiscountScale priceBlockElements=".$priceBlockElements->Count());
		// Hole das DeliverySetup der Produktvariante
		if(!$priceBlockElementID && $priceBlockElements->Count()>0){
			$priceBlockElementID=$priceBlockElements->Sort('SortID','ASC')->First()->ID;
		}
		$priceBlockElement=Preis::get()->byID($priceBlockElementID);
		if(isset($priceBlockElement) && $priceBlockElement->DiscountElements()->Count()>0){
			//$values->DiscountElements=new ArrayList();
			// Hole gruppenspezifischen Preis des aktuellne DiscountElements dE
			
			foreach($priceBlockElement->DiscountElements() as $dE){
				
				$values->push($dE->getDiscountElementString($groupID));
			}
			return $values;
		}else{
			// Kein Discount
			Injector::inst()->get(LoggerInterface::class)->error("Kein Discount");
			$values->DiscountElements=false;
			return $values;
		}
		//DeliverySetup der Produktvriante gefunden
	}
}
