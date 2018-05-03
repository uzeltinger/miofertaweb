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
 * PayPal Plus payment processor class
 *
 * process the payment using PayPal Plus payment gateway
 */

//include the main library
use PayPal\Api\PayerInfo;

use PayPal\Api\Address;

require_once JPATH_COMPONENT_SITE.'/classes/payment/processors/paypal/autoload.php';
use PayPal\Api\Amount;
use PayPal\Api\CreditCard;
use PayPal\Api\Details;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;

JHTML::_('script', 'https://www.paypalobjects.com/webstatic/ppplus/ppplus.min.js');

class PayPalPlusWall implements IPaymentProcessor{

	private $clientID = "";
	private $clientSecret = "";

	var $type;
	var $name;
	var $mode;
	
	var $noRedirectMessage = true;

	/**
	 * Initialize the payment processor
	 * @param unknown_type $data
	 * @throws Exception
	 */
	public function initialize($data){
		if (!function_exists('curl_init')) {
			throw new Exception('paypal plus needs the CURL PHP extension.');
		}

		$this->type = $data->type;
		$this->name = $data->name;
		$this->mode = $data->mode;

		if($this->mode=="test"){
			$this->mode="sanbox";
		}

		$this->clientID = $data->client_id;
		$this->clientSecret = $data->client_secret;
	}

	public function getPaymentGatewayUrl(){
		return "";
	}
	
	
	/**
	 * Generates the payment processor html
	 */
	public function getPaymentProcessorHtml(){
		$html ="<ul id=\"payment_form_$this->type\" style=\"display:none\" class=\"form-list\">
		<li>
		".JText::_('LNG_PROCESSOR_PAYPAL_PLUS_INFO',true)."
				</li>
				</ul>";
		
				return $html;
	}

	/**
	 * Create payment
	 * @param unknown_type $data
	 * @throws Exception
	 */
	public function createPayment(){
		$data = $this->data;
		$apiContext = new \PayPal\Rest\ApiContext(
				new \PayPal\Auth\OAuthTokenCredential(
						$this->clientID,     // ClientID
						$this->clientSecret  // ClientSecret
				)
		);
		
		if($this->mode=="live"){
			$apiContext->setConfig(
					array(
							'log.LogEnabled' => true,
							'log.FileName' => 'PayPal.log',
							'log.LogLevel' => 'FINE',
							'mode' => 'live',
					)
			);
		}
		
		$billingAddress = new Address();
		$billingAddress->setLine1($data->billingDetails->address);
		$billingAddress->setCity($data->billingDetails->city);
		$billingAddress->setCountryCode("DE");
		$billingAddress->setState($data->billingDetails->region);
		$billingAddress->setPostalCode($data->billingDetails->postal_code);
		$billingAddress->setPhone($data->billingDetails->phone);
		
		$payerInfo = new PayerInfo();
		$payerInfo->setBillingAddress($billingAddress);
		$payerInfo->setEmail($data->billingDetails->email);
		
		$payer = new Payer();
		$payer->setPaymentMethod('paypal')
		->setPayerInfo($payerInfo);

		$item1 = new Item();
		$item1->setName($data->service." ".$data->description)
		->setDescription($data->service." ".$data->description)
		->setCurrency($data->currency)
		->setQuantity(1)
		->setPrice($data->amount - $data->vat_amount);
		
		$itemList = new ItemList();
		$itemList->setItems(array($item1));
		
		$details = new Details();
		$details->setTax($data->vat_amount)
		->setSubtotal($data->amount - $data->vat_amount);
		
		$amount = new Amount();
		$amount->setCurrency($data->currency)
		->setTotal($data->amount)
		->setDetails($details);
		
		$transaction = new Transaction();
		$transaction->setAmount($amount)
		->setItemList($itemList)
		->setDescription($data->service." ".$data->description);
		
		$transaction->setInvoiceNumber($data->id);
		
		$redirectUrl = new \PayPal\Api\RedirectUrls();
		$redirectUrl->setReturnUrl($this->returnUrl);
		$redirectUrl->setCancelUrl($this->cancelUrl);
		
		$payment = new Payment();
		$payment->setIntent("sale")
		->setPayer($payer)
		->setRedirectUrls($redirectUrl)
		->setTransactions(array($transaction));
		
		try {
			$payment->create($apiContext);
		} catch (Exception $ex) {
			JFactory::getApplication()->enqueueMessage($ex->getData(), 'warning');
		}
		
		return $payment;	
	
	}
	
	/**
	 * Process the transaction by calling the payment gateway
	 * @param unknown_type $data
	 * @throws Exception
	 */
	public function processTransaction($data, $controller = "payment"){
		
		$this->returnUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.processResponse&processor=paypalpluswall",false,-1);
		$this->cancelUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.processCancelResponse",false,-1);;
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
		$result->status = PAYMENT_REDIRECT;
		$result->payment_status = PAYMENT_STATUS_PENDING;
		

		return $result;

	}

	/**
	 * Process payment gateway response
	 * @param unknown_type $data
	 * @return stdClass
	 */
	public function processResponse($data){
		$result = new stdClass();
		$data = JRequest::get('get');
		$result->transaction_id = $data["paymentId"];
		
		$apiContext = new \PayPal\Rest\ApiContext(
				new \PayPal\Auth\OAuthTokenCredential(
						$this->clientID,     // ClientID
						$this->clientSecret  // ClientSecret
				)
		);
		
		try { 
			$payment = Payment::get($data["paymentId"], $apiContext); 
		}catch (Exception $ex) {
			dump($ex);
			exit;
		}
		
		$transaction = $payment->getTransactions();
		$transaction = $transaction[0];
	
		$result->amount = $transaction->getAmount()->getTotal();
		$result->transactionTime = $payment->getCreateTime();
		$result->order_id = $transaction->getInvoiceNumber();
		$result->processor_type = $this->type;
		$result->payment_method = $payment->getPayer()->getPaymentMethod();
		
		$paymentState = $payment->getState();
		$result->response_code = $payment->getState();
		$result->response_message = "";
		$result->processAutomatically = true;
		
		switch($paymentState){
			case "created":
				$result->status = PAYMENT_SUCCESS;
				$result->payment_status = PAYMENT_STATUS_PAID;
				break;
			case "approved":
				$result->status = PAYMENT_SUCCESS;
				$result->payment_status = PAYMENT_STATUS_PAID;
				break;
			case "failed": 
				$result->status = PAYMENT_ERROR;
				$result->payment_status = PAYMENT_STATUS_FAILURE;
				break;
			case "partially_completed": 
				$result->status = PAYMENT_WAITING;
				$result->payment_status = PAYMENT_STATUS_WAITING;
			case "in_progress":
				$result->status = PAYMENT_WAITING;
				$result->payment_status = PAYMENT_STATUS_PENDING;
		}
	
		return $result;
	}

	/**
	 * Get the payment details
	 * @param unknown_type $paymentDetails
	 * @param unknown_type $amount
	 * @param unknown_type $cost
	 */
	public function getPaymentDetails($paymentDetails){
		return JText::_('LNG_PROCESSOR_PAYPAL_PLUS',true);
	}

	/**
	 * There are no html field
	 */
	public function getHtmlFields() {
		$payment = $this->createPayment();

		$approval_url = $payment->getApprovalLink();
		$html ='<div id="ppplus"></div>';
		$html .='<script type="application/javascript">
				var ppp = PAYPAL.apps.PPP({
				"approvalUrl": "'.$approval_url.'",
				"placeholder": "ppplus",
				"mode": "'.$this->mode.'",
				
				"country": "DE",
				});
				</script>';
		
		return $html;
	}
}
?>