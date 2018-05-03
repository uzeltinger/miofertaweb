<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');


JBusinessUtil::includeValidation();

class JBusinessDirectoryViewPayment extends JViewLegacy
{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function display($tpl = null)
	{
	
		$layout = JRequest::getVar('layout',null);
		if(isset($layout)){
			$tpl = $layout;
		}
		$this->paymentMethods =  $this->get('paymentMethods');
		$this->order = $this->get('Order');
		$this->state = $this->get('State');
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$this->companyId = JRequest::getVar("companyId");
		$this->discount_code = JRequest::getVar("discount_code","");
		
		parent::display($tpl);
		
	}
	
	function getPaymentMethodFormHtml($paymentMethod){
		return JText::_("LNG_PAYMENT_REDIRECT");
	}
}
?>
