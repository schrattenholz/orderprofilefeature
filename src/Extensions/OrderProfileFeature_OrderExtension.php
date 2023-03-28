<?php

namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\Order\OrderExtension;
use Schrattenholz\Order\Product;
use Schrattenholz\Order\OrderConfig;
use Silverstripe\ORM\DataExtension;
use SilverStripe\Security\Security;
use Silverstripe\Security\Group;
use Silverstripe\Forms\TextField;
use Silverstripe\Forms\OptionsetField;
use Silverstripe\Forms\ConfirmedPasswordField ;
use Silverstripe\ORM\ArrayList;
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
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;
use SilverStripe\SiteConfig\SiteConfig;
use Schrattenholz\Order\Preis;
class OrderProfileFeature_OrderExtension extends DataExtension{

	private static $allowed_actions = array (
		'TestCall',
		'LogOut',
		'CurrentGroup',
		'RegistrationForm',
		'getBasket',
		'keepBasket',
		'setCheckoutAddress',
		'loginMember',
		'registerMember',
		'addProductFromOrderList',
		'expandBasketLiveTime',
		'getBasketLiveTime',
		'ClearOutdatedBaskets',
		'SetOrderStatusOfOutdatedOrders',
        'getBasketNavList',
		'getHandheldToolbar',
		'getProductBadge',
		'removeProductFromBasket',
		'removeProductFromBasketByID',
		'saveOrderModel',
		'searchProducts',
		'CurrentOrderCustomerGroup',
		'getDiscountScale',
		'deleteInactiveBasket',
		'CheckoutChain'
	);
	public function CheckoutChain(){
		$ocg=$this->owner->CurrentOrderCustomerGroup();
		$pages=new ArrayList();

		$pages->push(
			$this->owner->getBasketPage()
		);
		$pages->push(
			$this->owner->getCheckoutAddressPage()
		);
		if($this->owner->DeliveryIsActive()){
			$pages->push($this->owner->getCheckoutDeliveryPage());
		}
		if($this->owner->PaymentIsActive() && !$this->owner->DeliveryIsActive()){
			$pages->push($this->owner->getCheckoutDeliveryPage());
		}
		$pages->push($this->owner->getCheckoutSummaryPage());
		// Finde die Seite vor und nach der momentanen für den Vor/Zzurück-Button
		$n=false;
		$next=false;
		$l=false;
		foreach($pages as $page){
			if($n){
				$next=$page;
				break;
			}
			if($page->ID==$this->owner->ID){
				$last=$l;
				$n=true;
			}
			$l=$page;
			
		}
		$chain=new ArrayData(array("Pages"=>$pages,"Current"=>$this->owner,"Last"=>$last,"Next"=>$next));
		return $chain;
	}
	
		public function getDiscountScale($data){
		$priceBlockElementID=$data['priceBlockElementID'];
		$productID=$data['productID'];

		$data=new ArrayData(['id'=>$productID,'v'=>$priceBlockElementID]);
		if($this->owner->ProductDiscountScale($productID,$priceBlockElementID)->Count()>0){
			return $this->owner->customise($data)->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\Includes\\Product_Info_DiscountScale",
				SSViewer::config()->uninherited('themes')
			));
		}else{
			return "nodiscount";
		}
		//return $paginatedProducts;
	}
	public function getLinkAcountRoot(){
		$orderConfig=OrderConfig::get()->byID(1);
		$acountRoot=SiteTree::get()->byID($orderConfig->AcountRootID);
		return $acountRoot->Title;
	}
	public function searchProducts($data){
		$searchTerm=utf8_encode($data['searchTerm']);
		/*$suggestions = ExtensibleSearchSuggestion::get()->filter(array(
					'Term:StartsWith' => $term,
					'Approved' => (int)$approved,
					'ExtensibleSearchPageID' => $pageID
				))->sort('Frequency', 'DESC')->limit($limit);
				*/
				
				$result=array();
				foreach(Product::get()->filter(['MenuTitle:PartialMatch' => $searchTerm]) as $p){
					$defaultImage=$p->DefaultImage()->Fill(128,128)->URL;
					array_push($result,array("ID"=>$p->ID,"Title"=>$p->MenuTitle,"Link"=>$p->Link(),"CoverImage"=>$defaultImage));
				}

				return json_encode($result);
		
	}
	public function saveOrderModel($data){
		$returnValues=new ArrayList(['Status'=>'error','Message'=>false,'Value'=>false]);
		$data=json_decode(utf8_encode($data['orderModel']),true);
		$clientOrderID=$data['id'];
		$orderName=$data['title'];

		$clientOrder=OrderProfileFeature_ClientOrder::get()->byID($clientOrderID);
				
		$clientOrder->Title=$orderName;
		$clientOrder->IsModel=true;
		if($clientOrder->write()){
			$returnValues->Status='good';
			$returnValues->Message="Die Vorlage wurde unter Deinen Vorlagen gespeichert.";
			$returnValues->Value=$clientOrderID;
		}else{
			$returnValues->Status='error';
			$returnValues->Message="Ein Fehler ist aufgetreten, bitte versuche es erneut.";
			$returnValues->Value=$clientOrderID;
		}
		return json_encode($returnValues);
	}
	public function TestCall(){
		return "MUH1";
	}
	public function removeProductFromBasket($data){
		
		$returnValues=new ArrayList(['Status'=>'error','Message'=>false,'Value'=>false]);
		$pd=$this->owner->genProductdata(json_decode(utf8_encode($data['orderedProduct']),true));
		$productDetails=$this->owner->getProductDetails($pd);
		$basket=$this->getOwner()->getBasket();
		if(isset($pd['variant01'])){
			$pC=OrderProfileFeature_ProductContainer::get()->filter(['PriceBlockElementID'=>$pd['variant01'],'BasketID'=>$basket->ID])->First();
			
		}else{
			$pC=OrderProfileFeature_ProductContainer::get()->filter(['BasketID'=>$basket->ID])->First();
		}
		// ExtensionHook
		$vars=new ArrayData(array("Basket"=>$basket,"ProductDetails"=>$productDetails));
		$this->owner->extend('removeProduct_basketSetUp', $vars);
		
		$pC->delete();
		$returnValues->Status='good';
		$returnValues->Message="Produkt wurde entfernt.";
		$returnValues->Value=$this->ProductsInBasket();
		return json_encode($returnValues);
	}
	public function removeProductFromBasketByID($data){
		
		$returnValues=new ArrayList(['Status'=>'error','Message'=>false,'Value'=>false]);
		$pC=OrderProfileFeature_ProductContainer::get()->byID($data['id']);
		$basket=$pC->Basket();
		$productDetails=$this->owner->getProductDetails(array('productID'=>$pC->ProductID,'variant01'=>$pC->PriceBlockElementID));
		// ExtensionHook
		$vars=new ArrayData(array("Basket"=>$basket,"ProductDetails"=>$productDetails));
		$this->owner->extend('removeProduct_basketSetUp', $vars);
		
		$pC->delete();
		$returnValues->Status='good';
		$returnValues->Message="Produkt wurde entfernt.";
		$returnValues->Value=$this->ProductsInBasket();
		return json_encode($returnValues);
	}
	public function ProductsInBasket(){
		if($this->getOwner()->getBasket()){
			$amount=OrderProfileFeature_ProductContainer::get()->filter(['BasketID'=>$this->getOwner()->getBasket()->ID])->Count();
			if($amount){
				return $amount;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
		// Anzahl der Produkte
	
	// Muss im Stammmodul angepasst werden
	public function getListCount(){
		return $this->ProductsInBasket();
	}
	public function keepBasket(){
		$this->expandBasketLiveTime();
	}
	 public function onAfterInit(){
		$vars = [
			"Link"=>$this->getOwner()->Link(),
			"ID"=>$this->owner->ID
		];
		if($this->getBasket() && $this->getBasket()->ProductContainers()){
			$vars["TotalProducts"] = $this->getBasket()->ProductContainers()->Count();
		}else{
			$vars["TotalProducts"]=0;
		}
		Requirements::javascriptTemplate("schrattenholz/orderprofilefeature:javascript/orderprofilefeature.js",$vars);
		
		$this->expandBasketLiveTime();
		
	}
	public function expandBasketLiveTime(){
		$basket=$this->getBasket();
		if($basket){
			$oldLastEdited=$basket->LastEdited;
			$basket->TimeStamp=microtime(true);
			$basket->write();
			return $oldLastEdited." - ".$this->getOwner()->ThemeDir();
		}else{
			return false;
		}
	}
	public function getBasketLiveTime(){
		$basket=$this->getBasket();
		return $basket->LastEdited."-".$this->getOwner()->ThemeDir();
	}
	public function ClearOutdatedBaskets(){
		$now = date("Y-m-d H:i:s");
		$timestamp = "2016-04-20 00:37:15";
		$start_date = date($now);
		$expires = strtotime('-11 minute', strtotime($now));
		$date_diff=($expires-strtotime($now)) / 86400;
		$baskets=OrderProfileFeature_Basket::get()->filter('LastEdited:LessThan',$expires);
		
		//Warenkorb und Produkte, die seit 11 Minuten inaktiv sind
		foreach($baskets as $b){
			foreach($b->ProductContainers() as $pc){
				$pc->delete();
			}
			if($b->ClientContainerID>0){
				$clientContainer=OrderProfileFeature_ClientContainer::get()->byID($b->ClientContainerID);
				if($clientContainer){$clientContainer->delete();}
			}
			$b->delete();
		}
		$productContainers=OrderProfileFeature_ProductContainer::get()->filter([
			'LastEdited:LessThan'=>$expires,
			'ClientOrderID'=>0
			]);
		foreach($productContainers as $pC){
			if(!OrderProfileFeature_Basket::get()->byID($pC->BasketID) && !$pC->ClientOrderID){
					$pC->delete();
			}
		
		}
		return true;
	}
	public function SetOrderStatusOfOutdatedOrders(){
		$count=0;
		$now = date("Y-m-d");
		foreach(OrderProfileFeature_ClientOrder::get()->filter([
			'ShippingDate:LessThan'=>strtotime($now),
			'OrderStatus:not'=>'abgeschlossen'
			]) as $order){
				$count++;
				$order->OrderStatus="abgeschlossen";
				$order->write();
		}
		return "Status für ".$count." Aufträge geändert"; 
	}
	public function CreateBasket(){
		$basket=new OrderProfileFeature_Basket();
		$basket->write();
		$this->getOwner()->getSession()->set('basketid', $basket->ID);
		return ;
	}
	public function registerMember($data){
		$personenDaten=json_decode($this->getOwner()->utf8_urldecode($data['person']),true);
		if(isset($data['sec'])){$personenDaten['NeedsDoubleOptIn']=true;}else{$personenDaten['NeedsDoubleOptIn']=false;}
		$returnValues=new ArrayList(['Status'=>'error','Message'=>false,'Value'=>false]);
		//Create UserAccount
		$client=$this->CreateClient($personenDaten);
			if($client->Status!="error"){
				//Kundenkonto angelegt oder vorhandes Konto zurueckgegeben
				//return $this->getOwner()->httpError(500, 'UserAccount erzeugen'.$client->Value->ID);
				
				
				$returnValues=$client;
			}else{
				//Fehler
				return json_encode($client);
			}
			
		return json_encode($returnValues);
	}
	public function loginMember($data){
		$personenDaten=json_decode($this->getOwner()->utf8_urldecode($data['person']),true);
		$returnValues=new ArrayList(['Status'=>'error','Message'=>false,'Value'=>false]);
		//$request = Injector::inst()->get(HTTPRequest::class);
		$member=Member::get()->filter('Email',$personenDaten['UserAccountEmail']);

		if($member->Count()>0){
			$member=$member->First();
			$memberAuth=new MemberAuthenticator();
			$validationResult=$memberAuth->checkPassword($member,$personenDaten['UserAccountPassword']);

			if($validationResult->isValid()){
				$identityStore = Injector::inst()->get(IdentityStore::class);
				$identityStore->logIn($member);
				$returnValues->Status='good';
				$returnValues->Message="Benutzer gefunden.";
				$returnValues->Value=$member;
				return json_encode($returnValues);

			}else{
				$returnValues->Status='error';
				$returnValues->Message="Die Eingaben sind nicht korrekt.";
				$returnValues->Value=$member;
				return json_encode($returnValues);
			}
		}else{
			$returnValues->Status='error';
			$returnValues->Message="Die Eingaben sind nicht korrekt.";
			$returnValues->Value="";
			return json_encode($returnValues);
		}
	}
	public function getSessionBasketID(){
		$request = Injector::inst()->get(HTTPRequest::class);
		$session = $request->getSession();
		return $this->getOwner()->getSession()->get('basketid');
	}
	public function getSessionOrderID(){
		$request = Injector::inst()->get(HTTPRequest::class);
		$session = $request->getSession();
		return $this->getOwner()->getSession()->get('orderid');
	}
	public function getBasket(){
		return OrderProfileFeature_Basket::get()->byID($this->getSessionBasketID());
	}
	public function getOrder(){
		return OrderProfileFeature_ClientOrder::get()->byID($this->getSessionOrderID());
	}
	public function getProductDetailsWrapper($productID,$variantID){
		$pd=array();
		$pd=['productID'=>$productID];
		if($variantID>0){
			$pd=['variant01'=>$variantID];
		}
		return $this->owner->getProductDetails($pd);
	}
	public function getProductDetails($pd){
		if($pd){
			if(isset($pd['variant01'])){
				
				$productDetails=Preis::get()->byID(intval($pd['variant01']));
				
			}else{
				$productDetails=Product::get()->byID($pd['productID']);				
			}
		}else{
			
			if($this->owner->Preise()){
				$productDetails=$this->owner->Preise()->First();
			}else{
				$productDetails=$this->owner;
			}
		}
		//$productDetails=$this->owner;
		return $productDetails;
	}
	public function getBasketNavList() 
    {
		$returnValues=new ArrayList(['Status'=>'error','Message'=>false,'Value'=>false]);
           $this->getOwner()->OrderConfig=$this->getOwner()->OrderConfig();
            $basket = $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
                "Schrattenholz\\OrderProfileFeature\\Includes\\OrderProfileFeature_BasketNavList",
                SSViewer::config()->uninherited('themes')
            ));
        $data=new ArrayData(array("MarkUp"=>$basket,"BasketObject"));
		// Es fehlen Eingaben
		return $basket;

    }
		public function getHandheldToolbar() 
    {
		$returnValues=new ArrayList(['Status'=>'error','Message'=>false,'Value'=>false]);
           $this->getOwner()->OrderConfig=$this->getOwner()->OrderConfig();
            $basket = $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
                "Includes\\HandheldToolbar",
                SSViewer::config()->uninherited('themes')
            ));
        
		// Es fehlen Eingaben
		return $basket;

    }
	public function getProductBadge($ajaxData) 
    {
	   if(isset($ajaxData['v'])){
			$data=new ArrayData(["VariantID"=>$ajaxData['v']]);
	   }else{
		   $data=array();
	   }
        $productBadge = $this->owner->customise($data)->renderWith(ThemeResourceLoader::inst()->findTemplate(
            "Schrattenholz\\OrderProfileFeature\\Includes\\OrderProfileFeature_ProductBadge",
            SSViewer::config()->uninherited('themes')
        ));
        return $productBadge;
    }
	public function BooleanVac($vac){
		if($vac=="on"){
			return 1;
		}else{
			return 0;
		}
	}

	public function addProductFromOrderList($data){

		$product=$this->owner->genProductdata(json_decode(utf8_encode($data['orderedProduct']),true));
		//return $this->getOwner()->httpError(500,'blockedFromOtherUsers='.$product['productID']);
		$p=Product::get()->byID($product['productID']);

		return $p->addToList($data);
	}
	public function getClientOrders(){
		$member = Security::getCurrentUser();
		return OrderProfileFeature_ClientOrder::get()->innerjoin('OrderProfileFeature_ClientContainer','OrderProfileFeature_ClientOrder.ClientContainerID=CC.ID','CC')->where("CC.ClientID=".$member->ID);
	}
	public function CurrentGroup(){
		$member = Security::getCurrentUser();
		if($member){
			if($member->inGroup('wiederverkaeufer')) {
				return Group::get()->filter('code','wiederverkaeufer')->First();
			} else {
				return Group::get()->filter('code','privatkunden')->First();
			}
		} else {
			return Group::get()->filter('code','privatkunden')->First();
		}
	}
	public function CurrentOrderCustomerGroup(){
		return OrderCustomerGroup::get()->filter("GroupID",$this->owner->CurrentGroup()->ID)->First();
	}
	/*public function getPrice(){
		return $this->getOwner()->OrderCustomerGroups()->filter('GroupID',$this->getOwner()->CurrentGroup()->ID)->First()->Price;
	}*/
	public function LogOut(){
		//Security::setCurrentUser(null);
	}
	public function getCustomerGroups(){
		return OrderCustomerGroup::get();
	}
	public function RegistrationForm(){

		return new OrderProfileFeature_RegistrationForm($this->getOwner(),'RegistrationForm');

	}
	public function setClientContainer($clientContainer,$personenDaten){
		$clientContainer->Surname=$personenDaten['Surname'];
		$clientContainer->FirstName=$personenDaten['FirstName'];
		$clientContainer->PhoneNumber=$personenDaten['PhoneNumber'];
		$clientContainer->Street=$personenDaten['Street'];
		$clientContainer->ZIP=$personenDaten['ZIP'];
		$clientContainer->City=$personenDaten['City'];
		//$clientContainer->DSGVO=$personenDaten['DSGVO'];
		$clientContainer->Company=$personenDaten['Company'];
		$clientContainer->Gender=$personenDaten['Gender'];
		$clientContainer->Email=$personenDaten['Email'];
		return $clientContainer;
	}
	public function setCheckoutAddress($data){
		$returnValues=new ArrayList(['Status'=>'error','Message'=>false,'Value'=>false]);
		$personenDaten=json_decode($this->getOwner()->utf8_urldecode($data['person']),true);
		if(isset($data['sec'])){$personenDaten['NeedsDoubleOptIn']=true;}else{$personenDaten['NeedsDoubleOptIn']=false;}
		$basket=$this->getBasket();
		$member = Security::getCurrentUser();
		if(isset($basket) && $basket->ClientContainerID>0){
		
			//Aktualisierung der Adresse
			$clientContainer=$basket->ClientContainer();
			$clientContainer=$this->setClientContainer($clientContainer,$personenDaten);
			if(!isset($personenDaten['UserAccount'])){
				$cg=OrderCustomerGroup::get()->filter('IsDefault',true)->First();
				$clientContainer->OrderCustomerGroupID=$cg->ID;
			}else{
				$clientContainer->OrderCustomerGroupID=$personenDaten['CustomerGroup'];
			}
			$returnValues->Status="good";
			$returnValues->Message="Benutzerdaten sind schon angelegt";
			$returnValues->Value='';
			if(isset($personenDaten['CreateUserAccount']) && $personenDaten['CreateUserAccount']==1){
			
				$client=$this->CreateClient($personenDaten);
				if($client->Status!="error"){
					//Kundenkonto angelegt oder vorhandes Konto zurueckgegeben
					//return $this->getOwner()->httpError(500, 'UserAccount erzeugen'.$client->Value->ID);
					
					$clientContainer->ClientID=$client->Value->ID;
					$returnValues=$client;
				}else{
					//Fehler
					return json_encode($client);
				}
				//Create UserAccount
			}else if($member){
				$clientContainer->ClientID=$member->ID;
			}
			$clientContainer->write();
			$basket->personenDaten=$personenDaten;
			$basket->AdditionalNotes=$personenDaten['AdditionalNotes'];
			$this->owner->extend('setCheckoutAddress_Basket',$basket);
			$basket->write();

			return json_encode($returnValues);
		}else if(!isset($basket)){
			// Die Eingabe kommt von einer Registrierungsseite
			// Kein WArenkorb vorhanden
			$client=$this->CreateClient($personenDaten);
				if($client->Status!="error"){
					//Kundenkonto angelegt oder vorhandes Konto zurueckgegeben
					//return $this->getOwner()->httpError(500, 'UserAccount erzeugen'.$client->Value->ID);
					
					$returnValues=$client;
					return json_encode($returnValues);
				}else{
					//Fehler
					return json_encode($client);
				}
		}else{
			//Neuanlage der Adresse
			
			$clientContainer=OrderProfileFeature_ClientContainer::create();
			$clientContainer=$this->setClientContainer($clientContainer,$personenDaten);

			$clientContainer->BasketID=$basket->ID;


			if(!isset($personenDaten['CreateUserAccount_Val']) || !isset($personenDaten['CreateUserAccount_Val'])==0){
				
				//Wenn keine Benutzeraccount angelegt werden soll wird die Gruppe auf die Default Gruppe eingestellt
				$cg=OrderCustomerGroup::get()->filter('IsDefault',true)->First();
				$clientContainer->OrderCustomerGroupID=$cg->ID;
			}else{
				//Vom Benutzer ausgewaehlte Benutzergruppe wird gesetzt, der Member wird mit der Defaultgruppe angelegt
				//und eine E-Mail zur Freigabe der gewaehlten Gruppe versenden $this->CreateClient()
				$clientContainer->OrderCustomerGroupID=$personenDaten['CreateUserAccountCustomerGroup'];
			}

			$returnValues->Status="good";
			$returnValues->Message="Benutzerdaten gespeichert. Kein Benutzerkonto angelegt";
			$returnValues->Value='';
			if(isset($personenDaten['CreateUserAccount_Val']) && $personenDaten['CreateUserAccount_Val']==1){

				$client=$this->CreateClient($personenDaten);
				if($client->Status!="error"){
					//Kundenkonto angelegt oder vorhandes Konto zurueckgegeben
					//return $this->getOwner()->httpError(500, 'UserAccount erzeugen'.$client->Value->ID);
					
					$clientContainer->ClientID=$client->Value->ID;
					$returnValues=$client;
				}else{
					//Fehler
					return json_encode($client);
				}
				//Create UserAccount

			}else if($member){
				$clientContainer->ClientID=$member->ID;
			}
			$clientContainer->write();

			$basket->AdditionalNotes=$personenDaten['AdditionalNotes'];
			$basket->ClientContainerID=$clientContainer->ID;
			$basket->write();

			return json_encode($returnValues);
		}
	}
	public function CreateClient($personenDaten){
		$returnValues=new ArrayList(['Status'=>'error','Message'=>false,'Value'=>false]);
		if(Member::get()->filter('Email',$personenDaten['Email'])->Count()>0){
			//Es besteht ein Account zu der E-Mail-Adresse
			$returnValues->Status='error';
			$returnValues->Message="Benutzer existiert bereits, bitte loggen Sie sich ein.";
			$returnValues->Value=Member::get()->filter('Email',$personenDaten['Email'])->First();
			return $returnValues;
		}else{
			$client=Member::create();
			$client->Email=$personenDaten['Email'];
			$client->Surname=$personenDaten['Surname'];
			$client->FirstName=$personenDaten['FirstName'];
			$client->Gender=$personenDaten['Gender'];
			$client->PhoneNumber=$personenDaten['PhoneNumber'];
			$client->Company=$personenDaten['Company'];
			$client->Street=$personenDaten['Street'];
			$client->ZIP=$personenDaten['ZIP'];
			$client->City=$personenDaten['City'];
			$client->NeedsDoubleOptIn=$personenDaten['NeedsDoubleOptIn'];
			$client->DoubleOptIn=false;
			
			if($personenDaten['CreateUserAccountPassword']==$personenDaten['CreateUserAccountPasswordConfirm']){
				$client->Password=$personenDaten['CreateUserAccountPassword'];
				if(strlen($personenDaten['CreateUserAccountPassword'])>=8){
				}else{
					$returnValues->Status='error';
					$returnValues->Message="Das Passwort muss mindestens 8 Zeichen haben!";
					$returnValues->Value='';
					return $returnValues;

				}
			}else{
				$returnValues->Status='error';
				$returnValues->Message="Die Passwörter stimmen nicht überein!";
				$returnValues->Value='';
				return $returnValues;
			}
			//return $this->getOwner()->httpError(500, 'UserAccount anlegen');

			$defaultCostumerGroup=OrderCustomerGroup::get()->filter('IsDefault',true)->First();
			$client->RequestedGroupID=Group::get()->byID(OrderCustomerGroup::get()->byID($personenDaten['CreateUserAccountCustomerGroup'])->GroupID)->ID;
			$client->write();
			$client->Groups()->add(Group::get()->byID($defaultCostumerGroup->GroupID)->ID);


			//return $this->getOwner()->httpError(500, $defaultCostumerGroup->GroupID);
			if(!$personenDaten['NeedsDoubleOptIn']){
				if($personenDaten['CreateUserAccountCustomerGroup']!=$defaultCostumerGroup->ID){
					$returnValues->Status='info';
					$returnValues->Message="Ihr Benutzerkonto wurde bis zur Prüfung vorerst als ".Group::get()->byID($defaultCostumerGroup->GroupID)->Title." angelegt. Sie erhalten eine E-Mail sobald wir Ihren Status geprüft haben. Es werden bis zur Bestätigung die Preise für Privatkunden angezeigt. Natürlich passen wir die Preise auf Ihrer Rechnung entsprechend Ihrer endgültigen Kundengruppe an.";
					$this->getOwner()->sendGroupRequestToAdmin($client);
					
					
				}else{
					$returnValues->Status='good';
					$returnValues->Message="Benutzer wurde angelegt";
				}
				//Der neu angelegte Benutzer kann eingeloggt werden nichtDSGVO - konform
				
					$identityStore = Injector::inst()->get(IdentityStore::class);
					$identityStore->logIn($client);
				
			}else{
					if($this->sendDoubleOptIn($client)){
						$returnValues->Status='good';
						$returnValues->Message="Du erhälst eine E-Mail mit der du deine Anmeldung Bestätigen musst.";
					}else{
						$returnValues->Status='error';
						$returnValues->Message="Bitte versuche es erneut. Sollte es wiederholt nicht funktionieren kontaktiere uns bitte per Telefon oder E-Mail.";

					}
			}
			$returnValues->Value=$client;
			return $returnValues;
		}

	}
	public function sendDoubleOptIn($client){
		$orderConfig=OrderConfig::get()->First();
		$email = Email::create()
			->setHTMLTemplate('Schrattenholz\\OrderProfileFeature\\Layout\\Email_DoubleOptIn')
			->setData([
				//'BaseHref' => $_SERVER['SERVER_NAME'],
				'CheckoutAddress' =>$client,
				'OrderConfig'=>OrderConfig::get()->First()

			])
			->setFrom($orderConfig->InfoEmail)
			->setTo($client->Email)
			->setSubject("Bitte bestätige die Registrierung bei ".SiteConfig::get()->byID(1)->Title);
		if($email->send()){
			return true;
		}else{
			return false;
		}
		//return $this->getOwner()->httpError(500, "stop");
	}
	public function sendGroupRequestToAdmin($client){

		$email = Email::create()
			->setHTMLTemplate('Schrattenholz\\OrderProfileFeature\\Layout\\Email_GroupRequestToAdmin')
			->setData([
				//'BaseHref' => $_SERVER['SERVER_NAME'],
				'CheckoutAddress' =>$client,
				'OrderConfig'=>OrderConfig::get()->First()

			])
			->setFrom(OrderConfig::get()->First()->InfoEmail)
			->setTo(OrderConfig::get()->First()->InfoEmail)
			->setSubject("Wiederverkäufer bittet um Bestätigung");
		$email->send();
		//return $this->getOwner()->httpError(500, "stop");
	}
	 public function getCheckoutAddress(){
		if($this->getBasket()){
			$basket=$this->getBasket();
		}else{
			$basket=$this->getOrder();
		}
		
		
		if(Security::getCurrentUser()){
			if($basket){
				if($basket->ClientContainerID>0){
					$cc=$basket->ClientContainer();
					$cc->ShowReg=false;
					return $cc;
				}else{
					$client=Security::getCurrentUser();
					$client->ShowReg=false;
					return $client;
				}
			}else{
				$client=Security::getCurrentUser();
				$client->ShowReg=false;
				return $client;
			}
		}else if($basket){
			if($basket->ClientContainerID>0){
				$cc=$basket->ClientContainer();
				$cc->ShowReg=true;
				return $cc;
			}else{
				return false;
			}
		}
	}
	public function makeOrder(){

		$basket= $this->getBasket();
		$email = Email::create()
		->setHTMLTemplate('Schrattenholz\\OrderProfileFeature\\Layout\\ConfirmationClient')
		->setData([
				'BaseHref' => $_SERVER['DOCUMENT_ROOT'],
				'Basket' => $basket,
				'CheckoutAddress' => $this->getCheckoutAddress(),
				'OrderConfig'=>OrderConfig::get()->First()
		])
		->setFrom(OrderConfig::get()->First()->OrderEmail)
		->setTo($this->getCheckoutAddress()->Email)
		->setSubject("Bestellbestätigung Hof Lehnmühle");
		$email->send();
		$email = Email::create()
		->setHTMLTemplate('Schrattenholz\\OrderProfileFeature\\Layout\\Confirmation')
		->setData([
			'BaseHref' => $_SERVER['DOCUMENT_ROOT'],
			'Basket' => $basket,
			'CheckoutAddress' => $this->getCheckoutAddress(),
			'OrderConfig'=>OrderConfig::get()->First()
		])
		->setFrom(OrderConfig::get()->First()->OrderEmail)
		->setTo(OrderConfig::get()->First()->OrderEmail)
		->setSubject("Neue Bestellung");
		//return $this->getOwner()->httpError(500, 'basketID'.$this->getBasket()->ID);
		$order=OrderProfileFeature_ClientOrder::get()->filter('ClientContainerID',$this->getBasket()->ClientContainerID);
		if($order->Count()==0){
			$order=OrderProfileFeature_ClientOrder::create();
			$order->ClientContainerID=$this->getBasket()->ClientContainerID;
			$order->AdditionalNotes=$this->getBasket()->AdditionalNotes;
			
			$order->write();
			foreach($this->getBasket()->ProductContainers() as $pc){
				$order->ProductContainers()->add($pc);
			}
		}else{
			$order=$order->First();
			$order->AdditionalNotes=$this->getBasket()->AdditionalNotes;
			$order->write();
		}
		$this->getOwner()->getSession()->set('orderid', $order->ID);
		//$this->ClearBasket();
		//HOOK-Punkt

		if($email->send()){
			//$this->ClearAddress();

		}
	}
	public function deleteInactiveBasket(){
		$basket=$this->getBasket();
		
		if($basket->ProductContainers()->Count()>0){
			foreach($basket->ProductContainers() as $pc){
				$pc->delete();
			}
			if($basket->ClientContainerID){
				$basket->ClientContainer()->delete();
			}
		}
		$basket->delete();
		
		
	}
	public function ClearBasket(){
		$basket=$this->getBasket();
		//Injector::inst()->get(LoggerInterface::class)->error('ClearBasket OrderProfile');
		if(isset($basket)){
			if($basket->ProductContainers()->Count()>0){
				

				foreach($basket->ProductContainers() as $pc){
					if($pc->ClientOrderID==0){
					$pc->delete();
					}
				}
				/*
				//Nur loeschen, wenn keine ClientOrder existiert
				if($basket->ClientContainerID){
					$basket->ClientContainer()->delete();
				}
				*/
				
				// ENDE NUR ZUM TESTEN
			}
			//$basket->delete();
		}
	}
		public function genProductData($data){
		$productData=Array();
		$productData['productID']=$data['id'];
		if(isset($data['variant01'])){
			$productData['variant01']=$data['variant01'];
		}
		$productData['productoptions']=$data['productoptions'];
		$productData['quantity']=$data['quantity'];
		return $productData;
	}
}
