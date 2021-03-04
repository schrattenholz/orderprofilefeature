<?php

namespace Schrattenholz\OrderProfileFeature;

use Schrattenholz\Order\Preis;


use Silverstripe\ORM\DataExtension;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Security\Security;
use Silverstripe\Security\Group;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;
class ProductOption_PreisExtension extends DataExtension{

	private static $many_many=[
        'ProductOptions' => [
			'through' => ProductOptions_Preis::class,
            'from' => 'Preis',
            'to' => 'ProductOption'
        ]
    ];


	public function updateCMSFields(FieldList $fields){
			$gridFieldConfig=GridFieldConfig::create()
				->addComponent(new GridFieldButtonRow('before'))
				->addComponent($dataColumns=new GridFieldDataColumns)
				->addComponent($editableColumns=new GridFieldEditableColumns())
				->addComponent(new GridFieldSortableHeader())
				->addComponent(new GridFieldFilterHeader())
				->addComponent(new GridFieldPaginator())
				
			;
			$dataColumns->setDisplayFields([
				'ProductOption.Title' => 'Produktoption'
			]);
			$priceField=new NumericField("Price","Preis");
			$priceField->setLocale("DE_De");
			$priceField->setScale(2);
			
			$editableColumns->setDisplayFields(array(

				'Price'  =>array(
						'title'=>'Preis',
						'callback'=>function($record, $column, $grid) {
							return NumericField::create($column)->setScale(2);
					}),
				'Active'  =>array(
						'title'=>'Wird angezeigt',
						'callback'=>function($record, $column, $grid) {
							return CheckboxField::create($column);
					}),
				'AutoCalc'  =>array(
						'title'=>'Voreingestellter Preis',
						'callback'=>function($record, $column, $grid) {
							return CheckboxField::create($column);
					})
			));
			/*$fields->addFieldToTab('Root.Produktoptionen', GridField::create(
				'ProductOptions_Preis',
				'Preise',
				ProductOptions_Preis::get()->filter('PreisID',$this->getOwner()->ID),
				$gridFieldConfig
			));
			*/
			$fields->removeFieldFromTab('Root.Main','Price');
			$fields->removeByName('Preise');
    }
	public function onAfterWrite(){
		foreach(ProductOption::get() as $po){
			if(!$this->getOwner()->ProductOptions()->byID($po->ID)){
				$this->getOwner()->ProductOptions()->add($po);
			}
				$option=ProductOptions_Preis::get()->where(["ProductOptionID"=>$po->ID,"PreisID"=>$this->owner->ID])->First();
				$productID=Preis::get()->byID($option->PreisID)->ProductID;
				if(!$option->Price){
					$option->Price=ProductOptions_Product::get()->where(["ProductOptionID"=>$po->ID,"ProductID"=>$productID])->First()->Price;
					$option->write();
				}else if ($option->Price && $option->AutoCalc){
					$option->Price=ProductOptions_Product::get()->where(["ProductOptionID"=>$po->ID,"ProductID"=>$productID])->First()->Price;
					$option->write();
				}
		}
		parent::onAfterWrite();
	}
}