<?php 
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

class PayFast implements IPaymentProcessor {

	var $type;
	var $name;

	var $merchant_id;
	var $merchant_key;
	var $mode;
	var $paymentUrlTest = 'https://sandbox.payfast.co.za/eng/process';
	var $paymentUrl = 'https://www.payfast.co.za/eng/process';

	var $notifyUrl;
	var $returnUrl;
	var $cancelUrl;

	var $currencyCode;
	var $amount;
	var $itemNumber;
	var $itemName;


	public function initialize($data){
		$this->type =  $data->type;
		$this->name =  $data->name;
		$this->mode = $data->mode;
		$this->merchant_id = $data->merchant_id;
		$this->merchant_key = $data->merchant_key;
	}

	public function getPaymentGatewayUrl(){
		if($this->mode=="test"){
			return $this->paymentUrlTest;
		}else{
			return $this->paymentUrl;
		}
	}

	public function getPaymentProcessorHtml(){
		$html ="<ul id=\"payment_form_$this->type\" style=\"display:none\" class=\"form-list\">
		<li>
		".JText::_('LNG_PROCESSOR_PAYFAST_INFO',true)."
		</li>
		</ul>";

		return $html;
	}

	public function getHtmlFields() {
		$data = array(
           // Merchant details
          'merchant_id' => $this->merchant_id,
          'merchant_key' => $this->merchant_key,
          'return_url' =>  $this->returnUrl,
          'cancel_url' => $this->cancelUrl,
          'notify_url' => $this->notifyUrl,
           // Buyer details
	 	  'name_first' => $this->billingDetails->first_name,
	      'name_last'  => $this->billingDetails->last_name,
	      'email_address'=> $this->billingDetails->email,
           // Transaction details
          'm_payment_id' => $this->itemNumber, //Unique payment ID to pass through to notify_url
	      'amount' => number_format( sprintf( "%.2f", $this->amount), 2, '.', '' ), //Amount in ZAR
          'item_name' => $this->itemName,
          'item_description' => ''
         
          );        
 
      // Create GET string
      foreach( $data as $key => $val )
      {
          if(!empty($val))
          {
          	$pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
          }
  	  }
      // Remove last ampersand
      $getString = substr( $pfOutput, 0, -1 );
      if( isset( $passPhrase ) )
      {
          $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
      }	
      $data['signature'] = md5( $getString );
	  
	  $htmlForm = ''; 
          foreach($data as $name=> $value) 
          {
              $htmlForm .= '<input name="'.$name.'" type="hidden" value="'.$value.'" />';
          } 
       return $htmlForm;		  
	}
 
	public function processTransaction($data, $controller = "payment"){
		$this->returnUrl = urldecode(JRoute::_("index.php?option=com_jbusinessdirectory&view=orders&processor=payfast",false,-1));
		$this->notifyUrl = urldecode(JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.processAutomaticResponse&processor=payfast",false,-1));
		$this->cancelUrl = urldecode(JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.processCancelResponse",false,-1));
		$this->amount = $data->amount;
		$this->itemName = $data->service;
		$this->itemNumber = $data->id;
		$this->currencyCode = $data->currency;

		$this->billingDetails = $data->billingDetails;

		$result = new stdClass();
		$result->transaction_id = 0;
		$result->amount =  $data->amount;
		$result->payment_date = date("Y-m-d");
		$result->response_code = 0;
		$result->order_id = $data->id;
		$result->currency=  $data->currency;
		$result->processor_type = $this->type;
		$result->status = PAYMENT_REDIRECT;
		$result->payment_status = PAYMENT_STATUS_PENDING;

		return $result;
	}


	public function processResponse($data){

		$result = new stdClass();
		$result->transaction_id = $data["pf_payment_id"];
		$result->amount = $data["amount_gross"];
		$result->transactionTime = date("Y-m-d", strtotime($data["payment_date"]));
		$result->response_code = 0;
		$result->response_message = $data['payment_status'];
		$result->order_id = $data["m_payment_id"];
		$result->processor_type = $this->type;
		$result->payment_method = "";

		if($this->checkTransactionValidity()){
				
			switch( $data['payment_status'] )
			{
				case 'COMPLETE':
					$result->status = PAYMENT_SUCCESS;
					$result->payment_status = PAYMENT_STATUS_PAID;
					break;
				case 'FAILED':
					$result->status = PAYMENT_ERROR;
					$result->payment_status = PAYMENT_STATUS_FAILURE;
					break;
				case 'PENDING':
					$result->status = PAYMENT_WAITING;
					$result->payment_status = PAYMENT_STATUS_PENDING;
					// The transaction is pending, please contact a member of PayFast's support team for further assistance
					break;
				default:
					// If unknown status, do nothing (safest course of action)
					break;
			}
		}else{
			$result->status = PAYMENT_ERROR;
			$result->payment_status = PAYMENT_STATUS_FAILURE;
		}

		return $result;
	}

	public function getPaymentDetails($paymentDetails){
		return JText::_('LNG_PROCESSOR_PAYFAST',true);
	}

	public function checkTransactionValidity(){
		$validHosts = array(
				'www.payfast.co.za',
				'sandbox.payfast.co.za',
				'w1w.payfast.co.za',
				'w2w.payfast.co.za',
		);

		$validIps = array();
		foreach( $validHosts as $pfHostname )
		{
			$ips = gethostbynamel( $pfHostname );

			if( $ips !== false )
			{
				$validIps = array_merge( $validIps, $ips );
			}
		}

		// Remove duplicates
		$validIps = array_unique( $validIps );
		if( !in_array( $_SERVER['REMOTE_ADDR'], $validIps ) )
		{
			return false;
		}

		return true;
	}
}