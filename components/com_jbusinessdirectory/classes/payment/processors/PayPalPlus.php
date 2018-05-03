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

class PayPalPlus implements IPaymentProcessor{

	private $clientID = "";
	private $clientSecret = "";

	var $type;
	var $name;
	var $mode;

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

		$this->clientID = $data->client_id;
		$this->clientSecret = $data->client_secret;
	}

	/**
	 * Generates the payment processor html
	 */
	public function getPaymentProcessorHtml($data){
		$html ="<ul id=\"payment_form_$this->type\" style=\"display:none\" class=\"form-list\">
			<li>
					<table valign=top class='payment-details-data'>
						<tr style='background-color:##CCCCCC'>
								<td>
									".JText::_('LNG_CREDIT_CARD_TYPE',true)." <span class='mand'>*</span>
								</td>
								<td align=left>
									<select name='card_type' id ='card_type' class='validate[required]'>
										<option value='visa'>Visa</option>
										<option value='mastercard'>Mastercard</option>
										<option value='discover'>Discover</option>
										<option value='amex'>American Express</option>
									</select>	 
								</td>
							</tr>
							<tr style='background-color:##CCCCCC'>
								<td>
									".JText::_('LNG_FIRST_NAME',true)."<span class='mand'>*</span>
								</td>
								<td colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'card_first_name'
										id				= 'card_first_name'
										autocomplete	= 'off'
										size			= 25
										value			= ''
										class = 'validate[required] text-input'
									>
								</td>
							</tr>
							<tr style='background-color:##CCCCCC'>
								<td>
									".JText::_('LNG_LAST_NAME',true)."<span class='mand'>*</span>
								</td>
								<td colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'card_last_name'
										id				= 'card_last_name'
										autocomplete	= 'off'
										size			= 25
										value			= ''
										class = 'validate[required] text-input'
									>
								</td>
							</tr>
							<tr style='background-color:##CCCCCC'>
								<td>
									".JText::_('LNG_CREDIT_CARD_NUMBER',true)." <span class='mand'>*</span>
								</td>
								<td colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'card_number'
										id				= 'card_number'
										autocomplete	= 'off'
										size			= 25
										value			= ''
										class= 'validate[required,creditCard]'
									>
								</td>
							</tr>
							<tr style='background-color:##CCCCCC'>
								<td>
									".JText::_('LNG_EXPIRATION_DATE',true)." <span class='mand'>*</span>
								</td>
								<td align=left>
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
								</td>
							</tr>
							<tr style='background-color:##CCCCCC'>
								<td>
									".JText::_('LNG_SECURITY_CODE',true)."<span class='mand'>*</span>
								</td>
								<td colspan=2 align=left>
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
								</td>
							</tr>
						</TABLE>
		    </li>
		</ul>";
		
		return $html;
	}

	/**
	 * Process the transaction by calling the payment gateway
	 * @param unknown_type $data
	 * @throws Exception
	 */
	public function processTransaction($data, $controller = "payment"){
		
		// After Step 1
		$apiContext = new \PayPal\Rest\ApiContext(
				new \PayPal\Auth\OAuthTokenCredential(
						$this->clientID,     // ClientID
						$this->clientSecret  // ClientSecret
				)
		);
		
		$card_type = JRequest::getVar("card_type",null);
		$card_first_name = JRequest::getVar("card_first_name",null);
		$card_last_name = JRequest::getVar("card_last_name",null);
		$card_number = JRequest::getVar("card_number",null);
		$card_expiration_year = JRequest::getVar("card_expiration_year",null);
		$card_expiration_month = JRequest::getVar("card_expiration_month",null);
		$card_security_code = JRequest::getVar("card_security_code",null);
		
		$card = new CreditCard(); 
		$card->setType($card_type) 
			->setNumber($card_number) 
			->setExpireMonth($card_expiration_month) 
			->setExpireYear($card_expiration_year) 
			->setCvv2($card_security_code) 
			->setFirstName($card_first_name) 
			->setLastName($card_last_name);
		
		$fi = new FundingInstrument(); 
		$fi->setCreditCard($card);
		
		$payer = new Payer(); 
		$payer->setPaymentMethod("credit_card") ->setFundingInstruments(array($fi));
		
		$item1 = new Item();
		$item1->setName($data->service." ".$data->description)
		->setDescription($data->service." ".$data->description)
		->setCurrency($data->currency)
		->setQuantity(1)
		->setPrice($data->amount);	
		
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
		->setDescription("Payment description")
		->setInvoiceNumber(uniqid());
		
		$payment = new Payment();
		$payment->setIntent("sale")
		->setPayer($payer)
		->setTransactions(array($transaction));
		
		$result = new stdClass();
		$result->order_id = $data->id;
		$result->processor_type = $this->type;
		$result->payment_date = date("Y-m-d");
		
		
		try {
			$payment->create($apiContext);
		} catch (Exception $ex) {
			$result->status = PAYMENT_ERROR;
			$result->payment_status = PAYMENT_STATUS_FAILURE;
			$result->error_message =  $ex->getData();
			return $result;
		}

		$result->status = PAYMENT_SUCCESS;
		$result->payment_status = PAYMENT_STATUS_PAID;
		$result->transaction_id = $payment->getId();
		$result->response_code = $payment->getState();
		
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
		return "";
	}
}
?>