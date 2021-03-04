<?php

namespace Schrattenholz\OrderProfileFeature;
use SilverStripe\Core\Extension;
use Schrattenholz\Order\OrderConfig;
use Schrattenholz\Order\Unit;
use Schrattenholz\Order\Ingredient;
use Schrattenholz\Order\Addon;

use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use Terraformers\RichFilterHeader\Form\GridField\RichFilterHeader;
use SilverStripe\Forms\Form;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

class ProductAdmin_Extension extends Extension
{


      public function updateEditForm(&$form) 
    {
		
		//$form = parent::getEditForm($id, $fields);
		 $gridField = $form->Fields()->fieldByName('Schrattenholz-OrderProfileFeature-OrderProfileFeature_ClientOrder');
		
			//Injector::inst()->get(LoggerInterface::class)->error('-----------------____-----_____ Delivery_admin before');
			if($gridField) {
				$useExtendedConfig=true;
				// Injector::inst()->get(LoggerInterface::class)->error('gridField='.var_dump($gridField));
				$config = $gridField->getConfig();
				$config->removeComponentsByType(GridFieldFilterHeader::class);
				$filter = new RichFilterHeader();
				$filter->setFilterConfig([
				'Created.Nice' => [
					'title' => 'Created',
					'filter' => 'StartsWithFilter',
				]
			])
			->setFilterFields([
				'Created' => DateField::create('', ''),
			]);
			$config->addComponent($filter, GridFieldPaginator::class);
			}
			return $form;
    }
}
	?>