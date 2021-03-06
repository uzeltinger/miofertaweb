<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_SITE.DS.'classes'.DS.'payment'.DS.'processors'.DS.'authorize'.DS.'AuthorizeNet.php';

class Authorize{    

    private $apiLoginId= "";
    private $transactionKey = "";
    var $type;
    var $name;
  	var $mode;
	
	public function initialize($data){
		if (!function_exists('curl_init')) {
			throw new Exception('Authorize.net needs the CURL PHP extension.');
		}
		$this->type = $data->type; 
		$this->name = $data->name;
		$this->mode = $data->mode;
		
		$this->apiLoginId = $data->api_login_id;
		$this->transactionKey = $data->transaction_key;
		
	}
	      // Set multiple line items:
	
	public function processTransaction($data)
    {
    	 
    	//creditCard,$order,$customer
    	$customer = (object)array();
    	$customer->first_name = $data->billingDetails->first_name;
    	$customer->last_name = $data->billingDetails->last_name;
    	
    	$customer->company = $data->billingDetails->company_name;
    	$customer->address = $data->billingDetails->address;
    	$customer->city = $data->billingDetails->city;
    	$customer->state = $data->billingDetails->region;
    	$customer->zip = $data->billingDetails->postal_code;
    	$customer->country = $data->billingDetails->country;

    	$this->amount = $data->amount;
    	$this->itemName = $data->service." ".$data->description;
    	$this->itemNumber = $data->id;
    	
    	$order = array(
			'description' => $data->service." ".$data->description,
			'invoice_num' => $data->id
    	);
    	$result = new stdClass();
    	 
    	$result->card_name = JRequest::getVar("card_name",null);
    	$result->card_number = JRequest::getVar("card_number",null);
    	$result->card_expiration_year = JRequest::getVar("card_expiration_year",null);
    	$result->card_expiration_month = JRequest::getVar("card_expiration_month",null);
    	$result->card_security_code = JRequest::getVar("card_security_code",null);
    	$result->amount =  $data->amount;
    	
    	$creditCard = array(
			'exp_date' => $result->card_expiration_month."".substr($result->card_expiration_year,-2),
    		'card_num' => $result->card_number,
    		'amount' => $result->amount													            
    	);
    	
    	$authorize = new AuthorizeNetAIM($this->apiLoginId,$this->transactionKey);
    	if($this->mode=="test")
			$authorize->setSandbox(true);
		else 
			$authorize->setSandbox(false);
        $authorize->setFields($creditCard);
        $authorize->setFields($order);
        $authorize->setFields($customer);        

        $response = $authorize->authorizeAndCapture();

        if(isset($response->approved) && $response->approved==1){
	        $result->status = PAYMENT_SUCCESS;
	        $result->payment_status = PAYMENT_STATUS_PAID;
        }
        else{
        	$result->status = PAYMENT_ERROR;
        	$result->payment_status = PAYMENT_STATUS_FAILURE;
        	$result->error_message = $response->error_message." ".$response->response_reason_text;
        }
        
        $result->transaction_id = $response->transaction_id;
        $result->payment_date = date("Y-m-d");
        $result->response_code = $response->approved;
        $result->order_id = $data->id;
        $result->processor_type = $this->type;

		return $result;
    }
        
	public function getPaymentProcessorHtml(){
		$html ="<ul id=\"payment_form_$this->type\" style=\"display:none\" class=\"form-list\">
			<li>
					<TABLE valign=top class='payment-details-data'>
							<tr style='background-color:##CCCCCC'>
								<TD>
									".JText::_('LNG_NAME_OF_CARD',true)."<span class='mand'>*</span>
								</TD>
								<TD colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'card_name'
										id				= 'card_name'
										autocomplete	= 'off'
										size			= 25
										value			= ''
										class = 'validate[required] text-input'
									>
								</TD>
							</TR>
							<tr style='background-color:##CCCCCC'>
								<TD>
									".JText::_('LNG_CREDIT_CARD_NUMBER',true)." <span class='mand'>*</span>
								</TD>
								<TD colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'card_number'
										id				= 'card_number'
										autocomplete	= 'off'
										size			= 25
										value			= ''
										class= 'validate[required,creditCard]'
									>
								</TD>
							</TR>
							<tr style='background-color:##CCCCCC'>
								<TD>
									".JText::_('LNG_EXPIRATION_DATE',true)." <span class='mand'>*</span>
								</TD>
								<TD align=left>
									<select name='card_expiration_month' id = 'card_expiration_month' class= 'validate[required]'>
										";
										for( $i=1; $i<=12;$i++){
											$html .= "<option value='".$i."'>	".sprintf("%02d", $i)." </option>";
										}
									$html .= "</select>	/ 
									<select name='card_expiration_year' id = 'card_expiration_year' class= 'validate[required]'>
									";

										for( $i=date('Y'); $i<=date('Y')+5;$i++ ){
											$html .= "<option value='".$i."' > ".$i." </option>";
										}
						
										$html .= "</select>
								</TD>
							</TR>
							<tr style='background-color:##CCCCCC'>
								<TD>
									".JText::_('LNG_SECURITY_CODE',true)."<span class='mand'>*</span>
								</TD>
								<TD colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'card_security_code'
										id				= 'card_security_code'
										autocomplete	= 'off'
										size			= 4
										maxlength		= 4
										value			= ''
										class= 'validate[required]'
									>
								</TD>
							</TR>
						</TABLE>
		    </li>
		</ul>";
		
		return $html;
	}
	public function getPaymentDetails(){
		return JText::_('LNG_PROCESSOR_AUTHORIZE',true);
	}
	
    public function getHtmlFields() {
    	$html  = '';
    	return $html;
    }
}
?>