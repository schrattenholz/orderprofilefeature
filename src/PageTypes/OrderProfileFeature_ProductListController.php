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
			$products=$this->CategoryProducts($categoryID,$products);
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
		return $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\Layout\\ProductListFilterAside",
				SSViewer::config()->uninherited('themes')
			));
	}

	public function getLayout(){
		if($this->owner->Design=="Produktfilter"){
			return $this->getOwner()->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\Layout\\ProductListFilter",
				SSViewer::config()->uninherited('themes')
			));
		}else if($this->owner->Design=="KategorieMosaik"){
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
	public function AllProductsOfCategory($categoryID=0,$nextPageStart=0){
		if($categoryID==0)$categoryID=$this->owner->ID;
		$cat=SiteTree::get()->byID($categoryID);
		//return $cat->ClassName;//new ArrayList($cat);
		$products=new ArrayList();
		
		if($cat->ClassName=="Schrattenholz\Order\Product"){
			
			$products->push($cat);
			//return "muh";//new ArrayList($cat);
		}else{
			$products=$this->CategoryProducts($categoryID,$products);
		}
		$paginatedProducts=new PaginatedList($products, ['start'=>$nextPageStart]);
		$paginatedProducts->setPageLength(9);
		return $paginatedProducts;
	}
	public function CategoryProducts($categoryID,$productList){
		foreach(SiteTree::get()->byID($categoryID)->Children() as $subPage){
			if($subPage->ClassName=="Schrattenholz\Order\Product"){
				$productList->push($subPage);

			}else if($subPage->Children()){
				$productList=$this->CategoryProducts($subPage->ID,$productList);
			}
		}
		return $productList;
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