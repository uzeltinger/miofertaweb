<?php 
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

class Cash implements IPaymentProcessor {
	
	var $type;
	var $name;
	
	public function initialize($data){
		if(isset($data->type))
			$this->type =  $data->type;
		if(isset($data->name))
			$this->name =  $data->name;	
	}
	
	public function getPaymentGatewayUrl(){
	
	}
	
	public function getPaymentProcessorHtml(){
		$html ="<ul id=\"payment_form_$this->type\" style=\"display:none\" class=\"form-list\">
		<li>
		    ".JText::_('LNG_CASH_PROC_INFO',true)."
		    </li>
		</ul>";
		
		return $html;
	}
	
	public function getHtmlFields() {
		$html  = '';
		return $html;
	}
	
	public function processTransaction($data, $controller){
	
		$result = new stdClass();
		$result->transaction_id = 0;
		$result->amount =   $data->amount;
		$result->payment_date = date("Y-m-d");
		$result->response_code = 0;
		$result->order_id = $data->id;
		$result->currency=  $data->currency;
		$result->processor_type = $this->type;
		$result->payment_status = PAYMENT_STATUS_PAID;
		$result->status = PAYMENT_SUCCESS;
		
		return $result;
	}
	

	public function getPaymentDetails($paymentDetails){
		echo JText::_('LNG_PROCESSOR_CASH');
	}
}