<?php


namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\Order\Product;


use Silverstripe\ORM\DataObject;
use Silverstripe\Forms\TextField;
use Silverstripe\Forms\NumericField;
class ProductOption extends DataObject{
	private static $table_name='ProductOption';
	private static $db=[
		'Title'=>'Varchar(255)',
		'Shortcode'=>'Varchar(10)',
		'Price'=>'Decimal(6,2)',
		'Content'=>'Text'
	];
	private static $belongs_many_many=[
		'Prices'=>'Schrattenholz\\Order\\Preis',
		'Products'=>'Schrattenholz\\Order\\Product',
		'ProductContainers'=>OrderProfileFeature_ProductContainer::class
	];
	private static $singular_name="Produktoption";
	private static $plural_name="Produktoptionen";
	public function getCMSFields(){
		$fields=parent::getCMSFields();
		$fields->removeByName('Prices');
		$fields->removeByName('Products');
		$fields->removeByName('ProductContainers');
		$num=new NumericField("Price","Preis");
		$num->setLocale("DE_De");
		$num->setScale(2);
		$fields->addFieldToTab ('Root.Main',new TextField('Title','Bezeichnung'));
		$fields->addFieldToTab ('Root.Main',new TextField('Shortcode','Kurzbezeichnung'));
		$fields->addFieldToTab ('Root.Main',$num);
		$fields->addFieldToTab ('Root.Main',new TextField('Content','Bezeichnung'));
		return $fields;
    }
	public function onAfterWrite(){	
		foreach(ProductOption::get() as $pO){	
			foreach(Product::get() as $p){			
				if($p->ProductOptions()->filter('ProductOptionID',$pO->ID)->Count==0){
					$p->ProductOptions()->add($pO);
				}
				foreach($p->Preise() as $pbe){
					if($pbe->ProductOptions()->filter('ProductOptionID',$pO->ID)->Count==0){
						$pbe->ProductOptions()->add($pO);
					}					
				}				
			}
		}
		parent::onAfterWrite();
		
	}
}
?>