<?php

namespace Schrattenholz\OrderProfileFeature;

use PageController;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\Form;
use Silverstripe\Forms\PasswordField ;
use UncleCheese\DisplayLogic\Wrapper;
use SilverStripe\View\Requirements;
use Schrattenholz\Order\Product;
use SilverStripe\Security\Security;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;
class OrderProfileFeature_Profile_OrdersController extends PageController{
	private static $allowed_actions = [];

    protected function init()
    {
        parent::init();
		//Requirements::javascript('public/resources/vendor/schrattenholz/order/template/javascript/order.js');
	}
	
}