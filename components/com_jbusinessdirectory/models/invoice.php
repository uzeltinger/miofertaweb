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


class JBusinessDirectoryModelInvoice extends JModelItem
{ 
	
	function __construct()
	{
		parent::__construct();

		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	}

	function getInvoice(){
		
		$invoiceId = JRequest::getInt("invoiceId");
		
		$orderTable = JTable::getInstance("Order", "JTable", array());
		$order = $orderTable->getOrder($invoiceId);
		
		$companyTable = $table = JTable::getInstance("Company", "JTable", array());
		$company = $companyTable->getCompany($order->company_id);
		if(!empty($company))
			$order->companyName = $company->name;
		else 
			$order->companyName ="";
		
		$user = JFactory::getUser();
		if(!empty($company->userId)){
			$user = JFactory::getUser($company->userId);
		}
		$billingDetailsTable = JTable::getInstance("BillingDetails", "JTable", array());
		$order->billingDetails = $billingDetailsTable->getBillingDetails($user->id);
		
		$order->company = $company;
		
		$packageTable = $table = JTable::getInstance("Package", "JTable", array());
		$package = $packageTable->getPackage($order->package_id);
		$order->packageName = $package->name;
		$order->package = $package;

		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEntityTranslation($order->company, BUSSINESS_DESCRIPTION_TRANSLATION);
		}
	
		$taxesTable = JTable::getInstance("Taxes", "Table", array());
		$order->taxes = $taxesTable->getTaxes();
		if (!empty($order->taxes)){
			foreach ($order->taxes as &$tax){
				if($this->appSettings->enable_multilingual){
					JBusinessDirectoryTranslations::updateEntityTranslation($tax, TAX_DESCRIPTION_TRANSLATION);
				}
				if ($tax->tax_type==2) {
					$tax->percentage = $tax->tax_amount;
					$tax->tax_amount = $tax->tax_amount * ($order->initial_amount - $order->discount_amount) / 100;
				}
			}
		}

		$attributeConfig = JBusinessUtil::getAttributeConfiguration();
		$order->company = JBusinessUtil::updateItemDefaultAtrributes($order->company,$attributeConfig);

		return $order;
	}
}
?>

