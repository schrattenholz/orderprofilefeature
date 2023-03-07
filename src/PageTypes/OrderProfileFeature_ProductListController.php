<?php

namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\Order\Product;

use Silverstripe\ORM\DataExtension;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\ORM\PaginatedList;
use Psr\Log\LoggerInterface;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\View\Requirements;
class OrderProfileFeature_ProductListController extends DataExtension{
	private static $allowed_actions=[
		"getFilteredProductList",
		"loadShopPage",
	];
	 public function onAfterInit(){
		$vars = [
			"Link"=>$this->getOwner()->Link(),
			"ID"=>$this->owner->ID
		];
		Requirements::javascriptTemplate("schrattenholz/orderprofilefeature:javascript/masonry.pkgd.min.js",$vars);
		
	}
	public function loadShopPage($data){
		$page=$data['page'];
		$categoryID=$data['categoryID'];
		$cat=SiteTree::get()->byID($categoryID);
		//return $cat->ClassName;//new ArrayList($cat);
		$products=new ArrayList();
		
		if($cat->ClassName=="Schrattenholz\Order\Product"){
			
			$products->push($cat);
			//return "muh";//new ArrayList($cat);
		}else{
			$products=$this->CategoryProducts($categoryID,$products,$this->owner->CurrentOrderCustomerGroup());
		}
		$paginatedProducts=new PaginatedList($products, ['start'=>$page]);
		$paginatedProducts->setPageLength(9);
		
		$data=new ArrayData(['CurrentPageStart'=>$page,'CategoryID'=>$categoryID]);
		return $this->owner->customise($data)->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\Includes\\ProductListFilter_ProductList",
				SSViewer::config()->uninherited('themes')
			));
		//return $paginatedProducts;
	}
	public function index(HTTPRequest $request){
		return $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
			  "Page",
			SSViewer::config()->uninherited('themes')
		));
	}
	public function getAside(){
		if($this->owner->Design=="Produktfilter"){
		return $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\Layout\\ProductListFilterAside",
				SSViewer::config()->uninherited('themes')
			));
		}else if($this->owner->Design=="Abverkaufliste"){
		return $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\Includes\\ProductPreSaleListAside",
				SSViewer::config()->uninherited('themes')
			));
		}
	}

	public function getLayout(){
		if($this->owner->Design=="Produktfilter"){
			return $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\Layout\\ProductListFilter",
				SSViewer::config()->uninherited('themes')
			));
		}else if($this->owner->Design=="Abverkaufliste"){
			return $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\Layout\\ProductPreSaleList",
				SSViewer::config()->uninherited('themes')
			));
		}	else if($this->owner->Design=="KategorieMosaik"){
			return $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\Layout\\ProductListMasonry",
				SSViewer::config()->uninherited('themes')
			));
		}else{
			return $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\Layout\\ProductList",
				SSViewer::config()->uninherited('themes')
			));
		}
	}
	public function PaginationPos(){
		return $this->owner->getRequest()['start'];
	}
	public function AllProductsOfCategory($categoryID,$nextPageStart,$customerGroup){
		if($categoryID=="" && $categoryID==false){$categoryID=$this->owner->ID;}
		if(!isset($nextPageStart)){$nextPageStart=0;}
		$cat=SiteTree::get()->byID($categoryID);
		//new ArrayList($cat);
		$products=new ArrayList();
		
		if($cat->ClassName=="Schrattenholz\Order\Product"){
			$cat->Available=$cat->getAvailability($customerGroup);
			$products->push($cat);
			//return "muh";//new ArrayList($cat);
		}else{
			$products=$this->CategoryProducts($categoryID,$products,$customerGroup);
		}
		$paginatedProducts=new PaginatedList($products->Sort("Available","DESC"), ['start'=>$nextPageStart]);
		$paginatedProducts->setPageLength(9);
		return $paginatedProducts;
	}
	public function CategoryProducts($categoryID,$productList,$customerGroup){
		
		foreach(SiteTree::get()->byID($categoryID)->Children() as $subPage){
			if($subPage->ClassName=="Schrattenholz\Order\Product"){
				//Injector::inst()->get(LoggerInterface::class)->error($customerGroup.' CategoryProducts='.$subPage->getAvailability($customerGroup));
				$subPage->Available=$subPage->getAvailability($customerGroup);
				$productList->push($subPage);

			}else if($subPage->Children()){
				$productList=$this->CategoryProducts($subPage->ID,$productList,$customerGroup);
			}
		}
		return $productList->Sort("Available","DESC");
	}
	public function CategoryItems($customerGroup){
		$itemList=new ArrayList();
		foreach ($this->owner->Children() as $item){
			if($item->ClassName="Schrattenholz\Order\Product" || $item->ClassName=="Schrattenholz\Order\ProductList"){
				if($item->ClassName=="Schrattenholz\Order\Product"){
					//Injector::inst()->get(LoggerInterface::class)->error($customerGroup.' CategoryProducts='.$subPage->getAvailability($customerGroup));
					$item->Available=$item->getAvailability($customerGroup);
				}else{
					$item->Available=true;
					
				}
				$itemList->push($item);
			}
			
		}
		return $itemList->Sort("Available","DESC");
	}
	public function getFilteredProductList($data){
		//return $data['id'];
		
		//return $list->MenuTitle;
		$templateData=new ArrayData(['Products'=>$this->owner->AllProductsOfCategory($data['id'])]);
		return $this->owner->customise($templateData)->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\Layout\\ProductListFilter_Items",
				SSViewer::config()->uninherited('themes')
			));
	}
}