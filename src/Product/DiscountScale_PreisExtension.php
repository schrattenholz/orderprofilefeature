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
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SwiftDevLabs\DuplicateDataObject\Forms\GridField\GridFieldDuplicateAction;
use SilverStripe\Security\Security;
use Silverstripe\Security\Group;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;
class DiscountScale_PreisExtension extends DataExtension{
	private static $has_many=[
        'DiscountElements' => DiscountScale_DiscountElement::class
    ];
	

	public function updateCMSFields(FieldList $fields){
			$gridFieldConfig=GridFieldConfig::create()
			->addComponent(new GridFieldButtonRow('before'))
			->addComponent($dataColumns=new GridFieldDataColumns())
			->addComponent($editableColumns=new GridFieldEditableColumns())
			->addComponent(new GridFieldSortableHeader())
			->addComponent(new GridFieldPaginator())
			->addComponent(new GridFieldOrderableRows('SortOrder'))
			->addComponent(new GridFieldDuplicateAction())
			->addComponent(new GridFieldEditButton())
			->addComponent(new GridFieldDeleteAction())
			->addComponent(new GridFieldDetailForm())
			->addComponent(new GridField_ActionMenu())
			->addComponent(new GridFieldAddNewButton())
			;
			$editableColumns->setDisplayFields(array(
				'Min'  =>array(
						'title'=>'Von (in g/ml/stk)',
						'callback'=>function($record, $column, $grid) {
							return NumericField::create($column)->setScale(0);
					}),
				'Max'  =>array(
						'title'=>'Bis(in g/ml/stk)',
						'callback'=>function($record, $column, $grid) {
							return NumericField::create($column)->setScale(0);
					}),
				'FixedDiscount'  =>array(
						'title'=>'Abzuziehender Betrag (ohne €)',
						'callback'=>function($record, $column, $grid) {
							return NumericField::create($column)->setScale(2);
					}),
				'PercentageDiscount'  =>array(
						'title'=>'Nachlass in Prozent (ohne %)',
						'callback'=>function($record, $column, $grid) {
							return NumericField::create($column)->setScale(1);
					}),
			));
			$dataColumns->setDisplayFields([
				//'Content' => 'Freitext',
				//'DisplayAmount' => 'Menge',
				'CMSPrice'=>'Preise',
				//'Inventory'=>'Stückzahl'
			]);
			$fields->addFieldToTab('Root.Rabattstaffel',LiteralField::create("RS_Info","<p><strong>Info:</strong></p><p> Rabatte werden immer auf den Grundpreis der jeweiligen Kundengruppe angerechnet.</br>Mengen werden je nach Produkt in Gramm,Milliliter oder Stück angegeben.</p>"));
			$fields->addFieldToTab('Root.Rabattstaffel', GridField::create(
				'DiscountScale_DiscountElements',
				'Rabatte Ext',
				$this->owner->DiscountElements(),
				$gridFieldConfig
			));
			$fields->removeFieldFromTab('Root.Main','Price');
			$fields->removeByName('Preise');
    }
	
}