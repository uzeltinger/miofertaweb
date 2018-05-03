<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelitem');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');


class JBusinessDirectoryModelOrders extends JModelItem
{ 
	
	function __construct()
	{
		$this->log = Logger::getInstance();
		parent::__construct();

		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

	}

	function getOrder($orderId){
		$orderTable = JTable::getInstance("Order", "JTable", array());
		$orderTable->load($orderId);
		
		$properties = $orderTable->getProperties(1);
		$order = JArrayHelper::toObject($properties, 'JObject');
		
		$discountCode = JRequest::getVar("discount_code");
		$resetDiscount = JRequest::getVar("reset_discount");
		
		$ignoreCount = false;
		if(empty($discountCode) && !empty($order->discount_code)&& empty($resetDiscount)){
			$discountCode = $order->discount_code;
			$ignoreCount = true;
		}

		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$order->discount_amount = 0 ;
		if(!empty($discountCode)){
			$discount_applied = false;
			$discountTable = JTable::getInstance("Discount", "JTable", array());
			$discount = $discountTable->getDiscount($discountCode, $ignoreCount);
			if(!empty($discount)){
				$discount->package_ids = explode(",",$discount->package_ids);
				if(in_array($order->package_id,$discount->package_ids)){
					$order->discount = $discount;
					if($discount->price_type==1){
						$order->discount_amount = $discount->value;
					}else{
						$order->discount_amount = $order->initial_amount * $discount->value/100;
					}
					$discount_applied = true;
				}
			}
			if (!empty($resetDiscount)) {
                if (!$discount_applied) {
                    JFactory::getApplication()->enqueueMessage(JText::_('LNG_ERROR_DISCOUNT_CODE'), 'warning');
                } else {
                    JFactory::getApplication()->enqueueMessage(JText::_('LNG_SUCCES_DISCOUNT_CODE'), 'success');
                }
            }
		}
		
		$order->amount = $order->initial_amount - $order->discount_amount;
		$order->vat_amount =$appSettings->vat*$order->amount/100;
		$order->amount +=  $order->vat_amount;
	
		$packageTable = JTable::getInstance("Package", "JTable", array());
		$order->package = $packageTable->getPackage($order->package_id);
		
		$user = JFactory::getUser();
		$billingDetailsTable = JTable::getInstance("BillingDetails", "JTable", array());
		$order->billingDetails = $billingDetailsTable->getBillingDetails($user->id);

		$taxesTable = JTable::getInstance("Taxes", "Table", array());
		$order->taxes = $taxesTable->getTaxes();
		if (!empty($order->taxes)){
			foreach ($order->taxes as &$tax){
				if($appSettings->enable_multilingual){
					JBusinessDirectoryTranslations::updateEntityTranslation($tax, TAX_DESCRIPTION_TRANSLATION);
				}
				if ($tax->tax_type==1) {
					$order->amount += $tax->tax_amount;
				}else{
					$tax->percentage = $tax->tax_amount;
					$taxAmount = $tax->tax_amount*($order->initial_amount-$order->discount_amount)/100;
					$order->amount += $taxAmount;
					$tax->tax_amount = $taxAmount;
				}
			}
		}

		return $order;
	}
	
	function saveOrder($order){
		
		$table = JTable::getInstance("Order", "JTable", array());
		
		$initialDiscountCode = null;
		if(!empty($order->discount_code)){
			$initialDiscountCode = $order->discount_code;
		}
		if(!empty($order->discount)){
			$order->discount_code= $order->discount->code;
		}
		
		// Bind the data.
		if (!$table->bind($order))
		{
			$this->setError($table->getError());
			return false;
		}
		
		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());
			return false;
		}
		
		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());
			return false;
		}
		
		if($initialDiscountCode!=$order->discount_code){
			$discountTable = JTable::getInstance("Discount", "JTable", array());
			$discountTable->increaseUse($order->discount_code);
		}
		
	}
	
	function getLastCompanyOrder($companyId){
		$orderTable = JTable::getInstance("Order", "JTable", array());
		$order = $orderTable->getLastNonPaidCompanyOrder($companyId);
		
		return $order;
	}
	
	function getOrders(){
		$user = JFactory::getUser();
		$orderTable = JTable::getInstance("Order", "JTable", array());
		$orders = $orderTable->getOrders($user->id, $this->getState('limitstart'), $this->getState('limit'));
		return $orders;
	}
	
	function updateOrder($data, $processor){
		$orderTable = JTable::getInstance("Order", "JTable", array());
		$orderTable->load($data->order_id);
		
		$orderTable->transaction_id = $data->transaction_id;
		$orderTable->amount_paid = $data->amount;
		$orderTable->paid_at = date("Y-m-d h:m:s");
		
		// set start_date
		$packageTable = JTable::getInstance("Package", "JTable", array());
		$lastPaidPackage = $packageTable->getLastPaidPackage($orderTable->company_id);
		
		$paidPackage = $packageTable->getPackage($orderTable->package_id);
		if(isset($data->reccuring) && $data->reccuring==1){
			$intialPaymentDetails = PaymentService::getPaymentDetails($data->order_id);
			if($intialPaymentDetails->payment_status==PAYMENT_STATUS_PENDING && $data->payment_status==PAYMENT_STATUS_PAID){
				$this->log->LogDebug("Set trial date ");
				$orderTable->start_trial_date = date("Y-m-d");
				$orderTable->start_date = "";
			}else{
				$this->log->LogDebug("Set date ");
				$orderTable->start_date = date("Y-m-d");
			}
		}else{
			$this->log->LogDebug("Set default date ");
			$orderTable->start_date = date("Y-m-d");
		}
		$orderTable->state = 1;
		
		if(!$orderTable->store()){
			throw  new Exception(JText::_("LNG_ERROR_ADDING_ORDER").$this->_db->getErrorMsg());
			$this->log->LogError("Error updating order. Order ID: ".$data->order_id);
		}
		
		$this->log->LogDebug("Order has been successfully updated. Order ID: ".$data->order_id);
		
		return $orderTable;
	}

	function getPagination()
	{
		$user = JFactory::getUser();
		$orderTable = JTable::getInstance("Order", "JTable", array());
		if (empty($pagination)) {
			jimport('joomla.html.pagination');
			$pagination = new JPagination($orderTable->getTotalOrdersByUserId($user->id), $this->getState('limitstart'), $this->getState('limit'));
		}
		return $pagination;
	}
	
}
?>

