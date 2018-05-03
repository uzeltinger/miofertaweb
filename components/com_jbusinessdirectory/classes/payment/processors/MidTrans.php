<?php 
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . '/MidTrans/Veritrans.php');

class MidTrans implements IPaymentProcessor {
	
	var $paymentUrlTest = 'https://app.sandbox.midtrans.com/snap/v1/transactions';
	var $paymentUrl = 'https://app.midtrans.com/snap/v1/transactions';
	

	public function initialize($data){
		
		$this->type =  $data->type;
		$this->name =  $data->name;
		$this->mode = $data->mode;
		$this->serverKey = $data->server_key;
		Veritrans_Config::$serverKey = $this->serverKey;
		
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
		    ".JText::_('LNG_PROCESSOR_MIDTRANS_INFO')."
		    </li>
		</ul>";
		
		return $html;
	}
	
	public function getHtmlFields() {
		$html  = '';

	
		return $html;
	}
	
	public function processTransaction($data, $controller = "payment"){
		//set mode for transaction
		if($this->mode=="test")
			Veritrans_Config::$isProduction = false;
		else
			Veritrans_Config::$isProduction = true;
		
		$transaction_details = array(
				'order_id' => $data->id,
				'gross_amount' => $data->amount, // no decimal allowed for creditcard
		);
		// Optional
		$item1_details = array(
				'id' => $data->id,
				'price' =>$data->amount,
				'quantity' => 1,
				'name' => $data->service
		);
		
		// Optional
		$item_details = array ($item1_details);
		
		// Optional
		$billing_address = array(
				'first_name'    => $data->billingDetails->first_name,
				'last_name'     => $data->billingDetails->last_name,
				'address'       => $data->billingDetails->address,
				'city'          => $data->billingDetails->city,
				'postal_code'   => $data->billingDetails->postal_code,
				'phone'         => $data->billingDetails->phone,
				'country_code'  => 'CAN'
		);

		// Optional
		$customer_details = array(
				'first_name'    => $data->billingDetails->first_name,
				'last_name'     => $data->billingDetails->last_name,
				'email'         => $data->billingDetails->email,
				'phone'         => $data->billingDetails->phone,
				'billing_address'  => $billing_address
		);
		// Fill transaction details
		$transaction = array(
				'transaction_details' => $transaction_details,
				'customer_details' => $customer_details,
				'item_details' => $item_details,
		);
		try {
		
		  // Redirect to Veritrans VTWeb page
		  header('Location: ' . Veritrans_VtWeb::getRedirectionUrl($transaction));
		}
		catch (Exception $e) {
		  echo $e->getMessage();
		  if(strpos ($e->getMessage(), "Access denied due to unauthorized")){
		      echo "<code>";
		      echo "<h4>Please set real server key from sandbox</h4>";
		      echo "In file: " . __FILE__;
		      echo "<br>";
		      echo "<br>";
		      echo htmlspecialchars('Veritrans_Config::$serverKey = \'<your server key>\';');
		      die();
		   }
		}
		
	}
	
	
	public function processResponse($data){
		Veritrans_Config::$serverKey = $this->serverKey;
		Veritrans_Config::$isProduction = false;
		
		$notif = new Veritrans_Notification();
		$transaction = $notif->transaction_status;
		$fraud = $notif->fraud_status;
		$order_id = $notif->order_id;
		$transaction_id = $notif->transaction_id;
		$gross_amount = $notif->gross_amount;
		$status_message = $notif->status_message;
		$status_code = $notif->status_code;
		$payment_type = $notif->payment_type;
	
		$result = new stdClass();
		
		if ($transaction == 'capture') {
			if ($fraud == 'challenge') {
				$result->payment_status = PAYMENT_STATUS_PENDING;
				$result->status = PAYMENT_WAITING;
			}
			else if ($fraud == 'accept') {
				$result->transaction_id = $transaction;
				$result->amount = $gross_amount;
				$result->transactionTime = date("Y-m-d", strtotime($data["payment_date"]));
				$result->response_code =$status_code;
				$result->response_message = $status_message;
				$result->order_id = $order_id;
				$result->processor_type = $this->type;
				$result->payment_method = $payment_type;
				$result->status = PAYMENT_SUCCESS;
				$result->payment_status = PAYMENT_STATUS_PAID;	
			}
		}
		else if ($transaction == 'cancel') {
			if ($fraud == 'challenge') {
				$result->status = PAYMENT_ERROR;
				$result->payment_status = PAYMENT_STATUS_FAILURE;
			}
			else if ($fraud == 'accept') {
	    		$result->status = PAYMENT_ERROR;
	    		$result->payment_status = PAYMENT_STATUS_FAILURE;		
			}
		}
		else if ($transaction == 'deny') {
    		$result->status = PAYMENT_ERROR;
    		$result->payment_status = PAYMENT_STATUS_FAILURE;
		}
		
		
		return $result;
	}

	public function getPaymentDetails($paymentDetails){
		return JText::_('LNG_PROCESSOR_MIDTRANS',true);
	}
}