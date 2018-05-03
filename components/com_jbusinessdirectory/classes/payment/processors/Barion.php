<?php 
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

/**
* Barion payment processor class
*
* process the payment using Barion payment gateway
*/
require_once 'barion/BarionClient.php';

class Barion implements IPaymentProcessor {
	
	var $type;
	var $name;
	
	var $paypal_email;
	var $mode;
	var $paymentUrlTest = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	var $paymentUrl = 'https://www.paypal.com/cgi-bin/webscr';
	
	var $notifyUrl;
	var $returnUrl;
	var $cancelUrl;
	
	var $currencyCode;
	var $amount;
	var $itemNumber;
	var $itemName;
	var $payment;

	public function initialize($data){
		$this->type =  $data->type;
		$this->name =  $data->name;
		$this->mode = $data->mode;

		$this->email = $data->email;
		$this->posKey = $data->pos_key;
	}
	
	public function getPaymentGatewayUrl(){
		if($this->mode=="test"){
			return BARION_WEB_URL_TEST . "?id=" . $this->payment->PaymentId;
		}else{
			return BARION_WEB_URL_PROD . "?id=" . $this->payment->PaymentId;
		}
	}
	
	public function getPaymentProcessorHtml(){
		$html ="<ul id=\"payment_form_$this->type\" style=\"display:none\" class=\"form-list\">
		<li>
		    ".JText::_('LNG_PROCESSOR_BARION_INFO',true)."
		    </li>
		</ul>";
		
		return $html;
	}
	
	public function getHtmlFields() {
		$html  = '';
		$html .= sprintf('<input type="hidden" name="id" id="id" value="%s">',  $this->payment->PaymentId);
		return $html;
	}
	
	/**
	 * Process the transaction by calling the payment gateway
	 * @param unknown_type $data
	 * @throws Exception
	 */
	public function processTransaction($data, $controller = "payment"){
	
		$this->amount = $data->amount;
		$this->itemName = $data->service." ".$data->description;
		$this->itemNumber = $data->id;
		$this->currencyCode = $data->currency;
		$this->data = $data;
	
		$result = new stdClass();
		$result->transaction_id = 0;
		$result->amount =  $data->amount;
		$result->payment_date = date("Y-m-d");
		$result->response_code = 0;
		$result->order_id = $data->id;
		$result->currency=  $data->currency;
		$result->processor_type = $this->type;
		
		$this->payment = $this->createPayment($data);
		
		if(!empty($this->payment->Errors)){
			$result->status = PAYMENT_ERROR;
			$result->payment_status = PAYMENT_STATUS_FAILURE;
			$result->error_message =  $this->payment->Errors[0]->Description;
			return $result;
		}

		$result->status = PAYMENT_REDIRECT;
		$result->payment_status = PAYMENT_STATUS_PENDING;
		
		return $result;
	
	}
	
	public function createPayment($data){
		
		// Barion Client that connects to the TEST environment
		$env = BarionEnvironment::Test;
		if($this->mode=="live"){
			$env = BarionEnvironment::Prod;
		}
		
		$BC = new BarionClient($this->posKey, 2, $env);
		
		// create the item model
		$item = new ItemModel();
		$item->Name = $data->service; // no more than 250 characters
		$item->Description = $data->description; // no more than 500 characters
		$item->Quantity = 1;
		$item->Unit = "package"; // no more than 50 characters
		$item->UnitPrice = intval(($data->amount - $data->vat_amount)*100);
		$item->ItemTotal = intval($data->amount*100);
		$item->SKU = "Item-".$data->id; // no more than 100 characters
		
		// create the transaction
		$trans = new PaymentTransactionModel();
		$trans->POSTransactionId = $data->service."-".$data->id;
		$trans->Payee = $this->email; 
		$trans->Total = intval($data->amount*100);
		$trans->AddItem($item); // add the item to the transaction
		// create the request model
		$ppr = new PreparePaymentRequestModel();
		$ppr->GuestCheckout = true; // we allow guest checkout
		$ppr->PaymentType = PaymentType::Immediate; // we want an immediate payment
		$ppr->PaymentRequestId = $data->id;
		$ppr->FundingSources = array(FundingSourceType::All); // both Barion wallet and bank card accepted
		$ppr->PayerHint = $data->billingDetails->email; // no more than 256 characters
		$ppr->Locale = UILocale::EN; // the UI language will be English
		$ppr->OrderNumber = $data->id; // no more than 100 characters
		
		$ppr->RedirectUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=payment.processResponse&processor=barion",false,-1);
		$ppr->CallbackUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=payment.procesAutomaticResponse&processor=barion",false,-1);
		
		$ppr->AddTransaction($trans); // add the transaction to the payment
		
		// send the request
		$payment = $BC->PreparePayment($ppr);
		
		return $payment;
	}
	
	public function processResponse($data){
		$result = new stdClass();
		
		$env = BarionEnvironment::Test;
		if($this->mode=="live"){
			$env = BarionEnvironment::Prod;
		}

		$BC = new BarionClient($this->posKey, 2, $env);
		$paymentDetails = $BC->GetPaymentState($data["paymentId"]);

		$result->transaction_id = $data["paymentId"];
		$result->amount = floatval($paymentDetails->Total/100);
		$result->transactionTime = date("Y-m-d", strtotime($paymentDetails->CompletedAt));
		$result->response_code = $paymentDetails->Status;
		$result->response_message = "";
		$result->order_id = $paymentDetails->PaymentRequestId;
		$result->processor_type = $this->type;
		$result->payment_method = "";
		
		if($paymentDetails->Status=="Succeeded"){
			$result->status = PAYMENT_SUCCESS;
			$result->payment_status = PAYMENT_STATUS_PAID;
		}else{
			$result->status = PAYMENT_ERROR;
            $result->payment_status = PAYMENT_STATUS_FAILURE;
		}
		
		$result->processAutomatically = true;
		
		return $result;
	}

	public function getPaymentDetails($paymentDetails){
		return JText::_('LNG_PROCESSOR_PAYPAL',true);
	}
}