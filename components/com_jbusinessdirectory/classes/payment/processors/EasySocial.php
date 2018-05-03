<?php 
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

class EasySocialProcessor implements IPaymentProcessor {
	
	var $type;
	var $name;
	var $ratio;
	
	
	public function initialize($data){
		if(isset($data->type))
			$this->type =  $data->type;
		if(isset($data->name))
			$this->name =  $data->name;	
		if(isset($data->ratio))
			$this->ratio =  $data->ratio;
		else 
			$this->ratio =  1;
	}
	
	public function getPaymentGatewayUrl(){
	
	}
	
	public function getPaymentProcessorHtml(){
		$html ="<ul id=\"payment_form_$this->type\" style=\"display:none\" class=\"form-list\">
		<li>
		    ".JText::_('LNG_EASYSOCIAL_PROC_INFO',true)."
		    </li>
		</ul>";
		
		return $html;
	}
	
	public function getHtmlFields() {
		$html  = '';
		return $html;
	}
	
	public function processTransaction($data){
		$app = JFactory::getApplication();
		if(file_exists(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php'))
			require_once(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php');
		else
			$app->enqueueMessage(JText::_('LNG_EASYSOCIAL_NOT_INSTALLED'),'warning');
				
		$userPoints  = Foundry::user()->getPoints();
		$pricePoints  = $data->amount * $this->ratio; 

		$result = new stdClass();
		if($pricePoints > $userPoints)
		{
		    $app->enqueueMessage(JText::_('LNG_EASYSOCIAL_NOT_ENOUGH_POINTS')."(".$pricePoints.")",'warning');
        	    $result->status = PAYMENT_ERROR;
        	    $result->payment_status = PAYMENT_STATUS_FAILURE;
		}
		else{
			$user = JFactory::getUser();
			Foundry::points()->assignCustom($user->id, "-{$pricePoints}", JText::_('LNG_EASYSOCIAL_CHARGE')." ".$data->package->name);
			$result->payment_status = PAYMENT_STATUS_PAID;
			$result->status = PAYMENT_SUCCESS;
		}
		$result->transaction_id = 0;
		$result->amount =   $data->amount;
		$result->payment_date = date("Y-m-d");
		$result->response_code = 0;
		$result->order_id = $data->id;
		$result->currency=  $data->currency;
		$result->processor_type = $this->type;
		
		return $result;
	}
	

	public function getPaymentDetails($paymentDetails){
		echo JText::_('LNG_EASYSOCIAL_PROCESSOR');
	}
}