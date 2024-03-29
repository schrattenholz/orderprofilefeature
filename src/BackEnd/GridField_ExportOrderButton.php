<?php

namespace Schrattenholz\OrderProfileFeature\BackEnd;
use Schrattenholz\OrderProfileFeature\OrderProfileFeature_ClientOrder;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Extensible;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_URLHandler;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\SSViewer;

use SilverStripe\View\ThemeResourceLoader;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;
/**
 * Adds an "Print" button to the bottom or top of a GridField.
 */
class GridField_ExportOrderButton implements GridField_HTMLProvider, GridField_ActionProvider, GridField_URLHandler
{
    use Extensible;

    /**
     * @var array Map of a property name on the printed objects, with values
     * being the column title in the CSV file.
     *
     * Note that titles are only used when {@link $csvHasHeader} is set to TRUE
     */
    protected $printColumns;

    /**
     * @var boolean
     */
    protected $printHasHeader = true;

    /**
     * Fragment to write the button to.
     *
     * @var string
     */
    protected $targetFragment;

    /**
     * @param string $targetFragment The HTML fragment to write the button into
     * @param array $printColumns The columns to include in the print view
     */
    public function __construct($targetFragment = "before", $printColumns = null)
    {
        $this->targetFragment = $targetFragment;
        $this->printColumns = $printColumns;
    }

    /**
     * Place the print button in a <p> tag below the field
     *
     * @param GridField
     *
     * @return array
     */
    public function getHTMLFragments($gridField)
    {
        $button = new GridField_FormAction(
            $gridField,
            'print',
            _t('Schrattenholz\\Order\\OrderAdmin\\GridField.ExportOrders', 'Bestellungen Exportieren'),
            'print',
            null
        );
        $button->setForm($gridField->getForm());

        $button->addExtraClass('font-icon-print grid-print-button btn btn-secondary');

        return [
            $this->targetFragment =>  $button->Field(),
        ];
    }

    /**
     * Print is an action button.
     *
     * @param GridField
     *
     * @return array
     */
    public function getActions($gridField)
    {
        return ['print'];
    }

    /**
     * Handle the print action.
     *
     * @param GridField $gridField
     * @param string $actionName
     * @param array $arguments
     * @param array $data
     * @return DBHTMLText
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'print') {
            return $this->handleOrderExport($gridField);
        }
    }

    /**
     * Print is accessible via the url
     *
     * @param GridField
     * @return array
     */
    public function getURLHandlers($gridField)
    {
        return [
            'print' => 'handleOrderExport',
        ];
    }

    /**
     * Handle the print, for both the action button and the URL
     *
     * @param GridField $gridField
     * @param HTTPRequest $request
     * @return DBHTMLText
     */
    public function handleOrderExport($gridField, $request = null)
    {
        set_time_limit(60);
        Requirements::clear();

        $data = $this->generatePrintData($gridField);

        $this->extend('updatePrintData', $data);
		
		// START CSV EXPORT
		$headerArray=array();
		foreach($data->Header as $headerItem){			
			array_push($headerArray,$headerItem->CellString);
		}
		$dataArray=array();
		array_push($dataArray,$headerArray);
		foreach($data->ItemRows as $item){		
		
			$itemArray=array();
			
			foreach($item->ItemRow as $itemCell){
				array_push($itemArray,urldecode($itemCell->CellString));
			}
			array_push($dataArray,$itemArray);
		}
		$array=$this->array_to_csv_download($dataArray, // this array is going to be the second row
			"orders.csv"
		);
		// ENDA CSV EXPORT
		/*
        if ($test) {
            return $test->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\OrderProfileFeature\\BackEnd\\OrderExport",
				SSViewer::config()->uninherited('themes')
			));
        }
*/
        return " ";
    }
 
	public function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
		header("Content-type: text/csv");
		header("charset:UTF-8");
		header("Content-Disposition: attachment; filename=".$filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		ob_get_clean();
		// open the "output" stream
		// see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
		$f = fopen('php://output', 'w');

		foreach ($array as $line) {
			fputcsv($f, $line, $delimiter);
		}
		fclose($f);
		return $f;
		
	}  
    /**
     * Return the columns to print
     *
     * @param GridField
     *
     * @return array
     */
    protected function getPrintColumnsForGridField(GridField $gridField)
    {
        if ($this->printColumns) {
            return $this->printColumns;
        }

        /** @var GridFieldDataColumns $dataCols */
        $dataCols = $gridField->getConfig()->getComponentByType(GridFieldDataColumns::class);
        if ($dataCols) {
            return $dataCols->getDisplayFields($gridField);
        }

        return DataObject::singleton($gridField->getModelClass())->summaryFields();
    }

    /**
     * Return the title of the printed page
     *
     * @param GridField
     *
     * @return array
     */
    public function getTitle(GridField $gridField)
    {
        $form = $gridField->getForm();
        $currentController = $gridField->getForm()->getController();
        $title = '';

        if (method_exists($currentController, 'Title')) {
            $title = $currentController->Title();
        } else {
            if ($currentController->Title) {
                $title = $currentController->Title;
            } elseif ($form->getName()) {
                $title = $form->getName();
            }
        }

        if ($fieldTitle = $gridField->Title()) {
            if ($title) {
                $title .= " - ";
            }

            $title .= $fieldTitle;
        }

        return $title;
    }

    /**
     * Export core.
     *
     * @param GridField $gridField
     * @return ArrayData
     */
    public function generatePrintData(GridField $gridField)
    {
		$ob=$gridField->getRequest();
		$searchedColumns = $this->getPrintColumnsForGridField($gridField);
		if(isset(json_decode($ob->getVars()["Schrattenholz-OrderProfileFeature-OrderProfileFeature_ClientOrder"] ["GridState"],true)["GridFieldFilterHeader"] ["Columns"])){
			$sFs=json_decode($ob->getVars()["Schrattenholz-OrderProfileFeature-OrderProfileFeature_ClientOrder"] ["GridState"],true)["GridFieldFilterHeader"] ["Columns"];
		}
		// Suchparmeter finden
		$searchedFields=new ArrayList();
		if(isset($sFs)){
			foreach($sFs as $field => $label){
				/*if($field=="Created" && $label!=""){
					$label="ab ".strftime("%d.%m.%Y",strtotime($label));
				}*/
				$searchedFields->push(array("Title"=>$searchedColumns[$field],"Value"=>$label,"Field"=>$field));
				//Injector::inst()->get(LoggerInterface::class)->error('-----------------____-----_____ Export before Value='.$label.' Field='.$field);
			}
		}
		
		// AusgabeFelder definieren
		 $printColumns=new ArrayList();
		 $printColumns['Created']="Bestelldatum";
		 $printColumns['ShippingDate']="Abhol/Lieferdatum";
		 $printColumns['ClientContainer.ID']="KDNR";
		 $printColumns['ProductTitle']="Produktname";
		 $printColumns['ProductQuantity']="Menge";
		 $printColumns['OrderStatus']="Bestellstatus";
		
		//Kopfzeile erstellen
        $header = null;
        if ($this->printHasHeader) {
            $header = new ArrayList();
            foreach ($printColumns as $field => $label) {
				//Kopfzeile
                $header->push(new ArrayData([
                    "CellString" => $label,
                ]));
            }
        }
        $items = $gridField->getList();
		// Ergebnis nch searchedFields filtern
		foreach($searchedFields as $sF){
			if($sF->Value!=""){
				$items=$items->filter($sF->Field,$sF->Value);
			}
		}
		$allItems=new ArrayList();
		// AusgabeListe erstellen
		foreach($items->limit(null) as $item){
			foreach($item->ProductContainers() as $product){
				$newItem=new OrderProfileFeature_ClientOrder();
				$newItem->Created=strftime("%d.%m.%Y",strtotime($item->Created));
				$newItem->ShippingDate=strftime("%d.%m.%Y",strtotime($item->ShippingDate));
				$newItem->ClientContainer=$item->ClientContainerID;
				$newItem->ProductTitle=$product->Product()->Title.": ".$product->PriceBlockElement()->getFullTitle(false);
				$newItem->ProductQuantity=$product->Quantity;
				$newItem->PriceBlockElementID=$product->PriceBlockElementID;
				$newItem->OrderStatus=$item->OrderStatus;
				$allItems->push($newItem);
			}
		}
        $itemRows = new ArrayList();

        /** @var GridFieldDataColumns $gridFieldColumnsComponent */
        $gridFieldColumnsComponent = $gridField->getConfig()->getComponentByType(GridFieldDataColumns::class);
        foreach ($allItems->limit(null)->sort("PriceBlockElementID") as $item) {
            $itemRow = new ArrayList();
            foreach ($printColumns as $field => $label) {
                $value = $gridFieldColumnsComponent
                    ? strip_tags($gridFieldColumnsComponent->getColumnContent($gridField, $item, $field))
                    : $gridField->getDataFieldValue($item, $field);
					//Injector::inst()->get(LoggerInterface::class)->error($field.' printColumns:'.$value);
                $itemRow->push(new ArrayData([
                    "CellString" => $value,
                ]));
            }
           $itemRows->push(new ArrayData([
                "ItemRow" => $itemRow
            ]));
            if ($item->hasMethod('destroy')) {
                $item->destroy();
            }
        }
        $ret = new ArrayData([
			"SearchedFields"=>$searchedFields,
            "Title" => SiteConfig::get()->First()->Title,
            "Header" => $header,
            "ItemRows" => $itemRows,
            "Datetime" => DBDatetime::now(),
            "Member" => Security::getCurrentUser(),
        ]);

        return $ret;
    }

    /**
     * @return array
     */
    public function getPrintColumns()
    {
        return $this->printColumns;
    }

    /**
     * @param array $cols
     * @return $this
     */
    public function setPrintColumns($cols)
    {
        $this->printColumns = $cols;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPrintHasHeader()
    {
        return $this->printHasHeader;
    }

    /**
     * @param bool $bool
     * @return $this
     */
    public function setPrintHasHeader($bool)
    {
        $this->printHasHeader = $bool;

        return $this;
    }
}
