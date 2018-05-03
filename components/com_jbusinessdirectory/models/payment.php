<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jbusinessdirectory/models', 'Orders');

class JBusinessDirectoryModelPayment extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	}
	
	/**
	 * Populate state
	 * @param unknown_type $ordering
	 * @param unknown_type $direction
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');
		
		$id = JRequest::getInt('orderId');
		$this->setState('payment.orderId', $id);
		
		$paymentMethod = JRequest::getVar('payment_method');
		$this->setState('payment.payment_method', $paymentMethod);
		
	}
	
	/**
	 * Get payment methods
	 * @return multitype:unknown
	 */
	function getPaymentMethods(){
		$paymentMethods = PaymentService::getPaymentProcessors();
		return $paymentMethods;
	}
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'PaymentProcessors', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Get current order
	 * @return unknown
	 */
	function getOrder(){
		$model = JModelLegacy::getInstance('Orders', 'JBusinessDirectoryModel', array('ignore_request' => true));
		$order = $model->getOrder($this->getState('payment.orderId'));
		
		return $order;
	}

	/**
	 * Send payment e-mail
	 * @param unknown_type $data
	 */
	function sendPaymentEmail($data){
		
		$orderTable = JTable::getInstance("Order", "JTable", array());
		$orderTable->load($data->order_id);
		
		$properties = $orderTable->getProperties(1);
		$order = JArrayHelper::toObject($properties, 'JObject');
		$order->details = $data;
		
		if($order->amount==0){
			$order->details->processor_type = JText::_("LNG_NO_PAYMENT_INFO_REQUIRED");
		}
		
		$companiesTable = $this->getTable("Company");
		$company = $companiesTable->getCompany($order->company_id);

		$packageTable =  $this->getTable("Package");
		$order->package = $packageTable->getPackage($order->package_id);

        $taxesTable = JTable::getInstance("Taxes", "Table", array());
        $order->taxes = $taxesTable->getTaxes();
        if (!empty($order->taxes)) {
            $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
            foreach ($order->taxes as &$tax) {
                if($appSettings->enable_multilingual){
                    JBusinessDirectoryTranslations::updateEntityTranslation($tax, TAX_DESCRIPTION_TRANSLATION);
                }
                if ($tax->tax_type == 2) {
                    $tax->percentage = $tax->tax_amount;
                    $tax->tax_amount = $tax->tax_amount * $order->initial_amount / 100;
                }
            }
        }
        $order->orderDetails = $this->prepareOrderEmail($order);

		if(!isset($company->email))
			return;
	
		return EmailService::sendPaymentEmail($company, $order);
	}

    /**
     * Prepare a html with detail for order
     *
     * @param $taxes object contain order details
     * @return string string return a html string for the order
     */
	public static function prepareOrderEmail($taxes){

	    $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $result = "<div>";
        $result .= "<div class=\"payment-items\">";
        $result  .= "<table border=\"0px\" cellpadding=\"5\">";
            $result .= "<thead>";
            $result .= "<tr bgcolor=\"#D9E5EE\">";
                $result .= "<td>".JText::_('LNG_PRODUCT_SERVICE')."</td>";
                $result .= "<td>".JText::_('LNG_UNIT_PRICE')."</td>";
                $result .= "<td>".JText::_('LNG_TOTAL')."</td>";
            $result .= "</tr>";
        $result .= "</thead>";

        $result .= "<tbody>";
            $result .= "<tr>";
                $result .= "<td align=\"left\">";
                    $result .= "<div class=\"left\">";
                    $result .= "<strong>".$taxes->service ."</strong><br/>";
                    $result .= $taxes->description;
                    $result .= "</div>";
                $result .= "</td>";
                $result .= "<td align=\"left\">".JBusinessUtil::getPriceFormat($taxes->initial_amount)."</td>";
                $result .= "<td align=\"left\">".JBusinessUtil::getPriceFormat($taxes->initial_amount)."</td>";
            $result .= "</tr>";
        if ($taxes->discount_amount>0){
            $result .= "<tr>";
                $result .= "<td></td>";
                $result .= "<td align=\"left\"><b>".JText::_("LNG_DISCOUNT")."</b></td>";
                $result .= "<td align=\"left\">".JBusinessUtil::getPriceFormat($taxes->discount_amount)."</td>";
            $result .= "</tr>";
        }
            $result .= "<tr>";
                $result .= "<td></td>";
                $result .= "<td align=\"left\"><b>".JText::_("LNG_SUB_TOTAL")."</b></td>";
                $result .= "<td align=\"left\"><b>".JBusinessUtil::getPriceFormat($taxes->initial_amount - $taxes->discount_amount)."</td>";
            $result .= "</tr>";
        if($appSettings->vat>0){
            $result .= "<tr>";
                $result .= "<td></td>";
                $result .= "<td align=\"left\"><b>".JText::_("LNG_VAT");
                $result .= "(".$appSettings->vat."%)";
                $result .= "</b></td>";
                $result .= "<td align=\"left\">".JBusinessUtil::getPriceFormat($taxes->vat_amount)."</td>";
            $result .= "</tr>";
        }
        if(!empty($taxes->taxes)) {
            foreach ($taxes->taxes as $tax) {
                $result .= "<tr>";
                    $result .= "<td></td>";
                    $result .= "<td>";
                        $result .= $tax->tax_name;
                        if ($tax->tax_type==2){
                            $result .= "(".$tax->percentage."%)";
                        } else {
                            $result .= " ";
                        }
                    $result .= "</td>";
                    $result .= "<td align=\"left\">".JBusinessUtil::getPriceFormat($tax->tax_amount)."</td>";
                $result .= "</tr>";
            }
        }
            $result .= "<tr>";
                $result .= "<td>&nbsp;</td>";
                $result .= "<td align=\"left\"><b>".JText::_('LNG_TOTAL')."</b></td>";
                $result .= "<td align=\"left\">".JBusinessUtil::getPriceFormat($taxes->amount)."</td>";
            $result .= "</tr>";
        $result .= "</tbody>";
        $result .= "</table>";
        $result .= "</div></div>";


        return $result;
    }
	
	function sendPaymentDetailsEmail($data){
        
		
		$orderTable = JTable::getInstance("Order", "JTable", array());
		$orderTable->load($data->order_id);
	
		$properties = $orderTable->getProperties(1);
		$order = JArrayHelper::toObject($properties, 'JObject');
		$order->details = $data;
		
		$companiesTable = $this->getTable("Company");
		$company = $companiesTable->getCompany($order->company_id);
		
		$packageTable =  $this->getTable("Package");
		$order->package = $packageTable->getPackage($order->package_id);

        $taxesTable = JTable::getInstance("Taxes", "Table", array());
        $order->taxes = $taxesTable->getTaxes();
        if($this->appSettings->enable_multilingual){
            JBusinessDirectoryTranslations::updateEntityTranslation($order->taxes, TAX_DESCRIPTION_TRANSLATION);
        }
        if (!empty($order->taxes)) {
            foreach ($order->taxes as &$tax) {
                if ($tax->tax_type == 2) {
                    $tax->percentage = $tax->tax_amount;
                    $tax->tax_amount = $tax->tax_amount * $order->initial_amount / 100;
                }
            }
            $order->taxesDetails = $this->prepareOrderEmail($order);
        }
		
		if(!isset($company->email))
			return;
	
		return EmailService::sendPaymentDetailsEmail($company, $order);
	}
	
}

?>