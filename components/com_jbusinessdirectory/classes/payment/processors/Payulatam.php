<?php 

/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

class Payulatam implements IPaymentProcessor {
	
	var $type;
	var $name;
	
	var $mode;
	var $paymentUrlTest = 'https://sandbox.gateway.payulatam.com/ppp-web-gateway';
	var $paymentUrl = 'https://gateway.payulatam.com/ppp-web-gateway';
	
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
		
		$this->apiKey = $data->api_key;
		$this->merchantId = $data->merchant_id;
		$this->accountId = $data->account_id;
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
		    ".JText::_('LNG_PROCESSOR_PAYPAL_PAYUTALAM',true)."
		    </li>
		</ul>";
		
		return $html;
	}
	
	public function getHtmlFields() {
		$html  = '';
		$html .= sprintf('<input type="hidden" name="merchantId" id="merchantId" value="%s">', $this->merchantId);
		$html .= sprintf('<input type="hidden" name="accountId" id="accountId" value="%s">', $this->accountId);
		
		$this->itemNumber = $this->itemNumber + 11111;
		$html .= sprintf('<input type="hidden" name="description" id="description" value="%s">', $this->itemName);
		$html .= sprintf('<input type="hidden" name="referenceCode" id="referenceCode" value="%s">', $this->itemNumber);
		
		$html .= sprintf('<input type="hidden" name="buyerEmail" id="buyerEmail" value="%s">', $this->billingDetails->email);
		
		if($this->mode=="test"){
			$html .= sprintf('<input type="hidden" name="test" id="test" value="1">');
		}
		
		$signature = "$this->apiKey~$this->merchantId~$this->itemNumber~$this->amount~$this->currencyCode";
		$signature = md5($signature);
		
		$html .= sprintf('<input type="hidden" name="signature" id="signature" value="%s">', $signature);
		
		$html .= sprintf('<input type="hidden" name="confirmationUrl" id="confirmationUrl" value="%s">', $this->notifyUrl);
		$html .= sprintf('<input type="hidden" name="responseUrl" id="responseUrl" value="%s">', $this->returnUrl);
		
		$html .= sprintf('<input type="hidden" name="amount" value="%.2f" />', $this->amount);
		$html .= sprintf('<input type="hidden" name="currency_code" value="%s" />', $this->currencyCode);
		$html .= sprintf('<input type="hidden" name="custom" value="%s" />', $this->itemNumber);
	
		return $html;
	}
	
	public function processTransaction($data, $controller = "payment"){
		$this->returnUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.processResponse&processor=payulatam",false,-1);
		$this->notifyUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.processAutomaticResponse&processor=payulatam",false,-1);
		$this->cancelUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.processCancelResponse",false,-1);;
		$this->amount = $data->amount;
		$this->itemName = $data->service." ".$data->description;
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
		
		$result->transaction_id = $data["transactionId"];
		$TX_VALUE = $_REQUEST['TX_VALUE'];
		$amount = number_format($TX_VALUE, 1, '.', '');
		$result->amount = $amount;
		$result->transactionTime = date("Y-m-d", strtotime($data["payment_date"]));
		$result->response_code = $data["transactionState"];
		$result->order_id = $data["referenceCode"];
		$result->currency= $data["currency"];
		$result->processor_type = $this->type;
		$result->payment_method = $data["lapPaymentMethod"];
		
		$signatureC = "$this->apiKey~$this->merchantId~$result->order_id~$result->amount~$result->currency~$result->response_code";
		$signatureC = md5($signatureC);
		$signature = $data['signature'];
		
		if(strtoupper($signature) == strtoupper($signatureC)){
		
			
			$estadoTx="";
			if ($data['transactionState'] == 4 ) {
				$estadoTx = "Transaction approved";
				$result->status = PAYMENT_SUCCESS;
				$result->payment_status = PAYMENT_STATUS_PAID;
			}
			
			else if ($data['transactionState'] == 6 ) {
				$estadoTx = "Transaction rejected";
				$result->status = PAYMENT_ERROR;
				$result->payment_status = PAYMENT_STATUS_FAILURE;
			}
			
			else if ($data['transactionState'] == 104 ) {
				$estadoTx = "Error";
				$result->status = PAYMENT_ERROR;
				$result->payment_status = PAYMENT_STATUS_FAILURE;
			}
			
			else if ($data['transactionState'] == 7 ) {
				$estadoTx = "Pending payment";
				$result->status = PAYMENT_WAITING;
				$result->payment_status = PAYMENT_STATUS_PENDING;
			}
			else {
				$estadoTx=$data['mensaje'];
			}
			
			$result->response_message = $estadoTx;
		}else{
			$result->status = PAYMENT_ERROR;
			$result->payment_status = PAYMENT_STATUS_FAILURE;
			$result->response_message = "Error validating digital signature";
		}			
		
		
		dump($result);
		exit;
		return $result;
	}

	public function getPaymentDetails($paymentDetails){
		return JText::_('LNG_PROCESSOR_PAYUTALAM',true);
	}
}