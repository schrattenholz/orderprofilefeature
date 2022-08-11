<?php


namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\Order\Product;

use SilverStripe\ORM\Queries\SQLUpdate;
use Silverstripe\ORM\DataObject;
use Silverstripe\Forms\TextField;
use Silverstripe\Forms\NumericField;

use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

class ProductOption extends DataObject{
	private static $table_name='ProductOption';
	private static $db=[
		'Title'=>'Varchar(255)',
		'Shortcode'=>'Varchar(10)',
		'Price'=>'Decimal(6,2)',
		'Content'=>'Text',
		'EnrollToAll'=>'Boolean'
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
	parent::onAfterWrite();
		foreach(ProductOption::get() as $pO){	
			foreach(Product::get() as $p){			
				if($p->ProductOptions()->filter('ProductOptionID',$pO->ID)->Count==0){
					Injector::inst()->get(LoggerInterface::class)->error('productoption anelegen');
					$p->ProductOptions()->add($pO);
				}
				foreach($p->Preise() as $pbe){
					if($pbe->ProductOptions()->filter('ProductOptionID',$pO->ID)->Count==0){
						$pbe->ProductOptions()->add($pO);
					}					
				}				
			}
		}
		if($this->EnrollToAll){
					
					//$pOID=$p->ProductOptions()->Filter("ProductOptionID",$pO->ID)->First->ID;	
					
					$update = SQLUpdate::create();
					$update->setTable("ProductOptions_Product");
					$update->addWhere(array('ProductOptionID' => $this->ID));
					$update->addAssignments(['Price'=>$this->Price]);
					$update->execute();
					$update = SQLUpdate::create();
					$update->setTable("ProductOptions_Preis");
					$update->addWhere(array('ProductOptionID' => $this->ID));
					$update->addAssignments(['Price'=>$this->Price]);
					$update->execute();
				}
		
		$update = SQLUpdate::create('ProductOption')->addWhere(['ID' => $this->ID]);
		$update->addAssignments(['EnrollToAll'=> 0]);
		$update->execute();
	}
}
?>