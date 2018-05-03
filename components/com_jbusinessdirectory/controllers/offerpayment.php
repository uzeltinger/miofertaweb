<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryControllerOfferPayment extends JControllerLegacy
{
    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    function __construct()
    {
        $this->log = Logger::getInstance();
        parent::__construct();
    }
    
    function showPaymentOptions(){
    	JRequest::setVar("view","offerpayment");
    	parent::display();
    }
    
	function processTransaction(){
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$paymentMethod = JRequest::getVar("payment_method","nopayment");
		
		$offerPayment = $this->getModel("OfferPayment");
		$order = $offerPayment->getOrder();

		$orderId = CartService::saveOrder($order);
	
		$paymentDetails = new stdClass();
		$paymentDetails->order_id = $orderId;
		$paymentDetails->status = PAYMENT_REDIRECT;
		$paymentDetails->payment_status = PAYMENT_STATUS_PENDING;
		
		$offerPayment->updateOrderStatus($paymentDetails);
			
		$order = $offerPayment->getCompleteOrderDetails($orderId);
		
		EmailService::sendOfferOrderNotification($order);
		
		$msg=JText::_("LNG_PAYMENT_PROCESSED_SUCCESSFULLY");
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=offerpayment&layout=confirmation&orderId='.$orderId, false), $msg);
		return;
		
		$processor = PaymentService::createPaymentProcessor($paymentMethod);
		$paymentDetails = $processor->processTransaction($order);
		$paymentDetails->details =  $processor->getPaymentDetails($orderId);
		PaymentService::addPayment($paymentDetails);
		
		if($paymentDetails->status==PAYMENT_REDIRECT){
			$document = JFactory::getDocument();
			$viewType = $document->getType();
			$view = $this->getView("payment", $viewType, '', array('base_path' => $this->basePath, 'layout' => "redirect"));
			$view->paymentProcessor = $processor;
			$view->display("redirect");
			
		}else if($paymentDetails->status==PAYMENT_SUCCESS){	
			$offerPayment= $this->getModel("OfferPayment");
			$order = $offerPayment->updateOrder($paymentDetails, $processor);
			
			$msg=JText::_("LNG_PAYMENT_PROCESSED_SUCCESSFULLY");
			$offerPayment->sendPaymentEmail($paymentDetails);
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=offer&offerId='.$order->offer_id, false), $msg);
		}else if($paymentDetails->status==PAYMENT_WAITING){
			$msg=JText::_("LNG_PAYMENT_WAITING");
			$paymentModel->sendPaymentDetailsEmail($paymentDetails);
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=offer&offerId='.$order->offer_id, false), $msg);
		}else if($paymentDetails->status==PAYMENT_ERROR){
			JFactory::getApplication()->enqueueMessage($paymentDetails->error_message, 'warning');
			JRequest::setVar('layout',null);
			JRequest::setVar("view","payment");
			parent::display();
		}
	
	}
	
	function processResponse(){
		$this->log->LogDebug("process response");
		$data = JRequest::get('post');
		if(empty($data))
			$data = JRequest::get('get');
		$this->log->LogDebug(serialize($data));
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

		$processorType = JRequest::getVar("processor");
		if($processorType == 'mollie'){
			$orderId = JRequest::getVar("orderId");
			$data =  PaymentService::getPaymentDetails($orderId);
		}
		
		$processor = PaymentService::createPaymentProcessor($processorType);
		$paymentDetails = $processor->processResponse($data);
		
		if($paymentDetails->status == PAYMENT_CANCELED || $paymentDetails->status == PAYMENT_ERROR){
			PaymentService::updatePayment($paymentDetails);
			$msg= JText::_("LNG_TRANSACTION_FAILED");
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=payment&orderId='.$paymentDetails->order_id, false),$msg);
		}else{
			$msg=JText::_("LNG_PAYMENT_PROCESSED_SUCCESSFULLY");
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=orders', false), $msg);
			if($appSettings->direct_processing){
				$this->processDirectProccessing($paymentDetails);
			}
			
			if(isset($paymentDetails->processAutomatically)){
				$this->processAutomaticResponse();
			}
		}
	}
	
	function processAutomaticResponse(){
		$this->log->LogDebug("process automatic response");
		$data = JRequest::get('post');
		if(empty($data))
			$data = JRequest::get('get');
		$this->log->LogDebug(serialize($data));

		$processorType = JRequest::getVar("processor");
		$this->log->LogDebug("Processor: ".$processorType);
		$processor = PaymentService::createPaymentProcessor($processorType);
		$processorType = JRequest::getVar("processor");
		
		if($processorType == 'mollie'){
			$orderId = JRequest::getVar("orderId");
			$data =  PaymentService::getPaymentDetails($orderId);
		}

		$paymentDetails = $processor->processResponse($data);
		$this->log->LogDebug("Payment Details: ".serialize($paymentDetails));
	
		if(empty($paymentDetails->order_id)){
			$this->log->LogDebug("Empty order Id");
			return;
		}
	
		$intialPaymentDetails = PaymentService::getPaymentDetails($paymentDetails->order_id);
		$this->log->LogDebug("Initial payment details: ".serialize($intialPaymentDetails));
	
		if($intialPaymentDetails->payment_status==PAYMENT_STATUS_PAID){
			$this->log->LogDebug("order has been already paid");
			//return;
		}

		PaymentService::updatePayment($paymentDetails);
		
		if($paymentDetails->status == PAYMENT_CANCELED || $paymentDetails->status == PAYMENT_ERROR){
			
		}else{
			$orderModel= $this->getModel("Orders");
			$order = $orderModel->updateOrder($paymentDetails, $processor);
			$paymentModel = $this->getModel("Payment");
			$paymentModel->sendPaymentEmail($paymentDetails);
		}
		
		
	}

	function processCancelResponse(){
		$this->log->LogDebug("process cancel response ");
		$data = JRequest::get( 'post' );
		$this->log->LogDebug(serialize($data));
		$this->setMessage(JText::_('LNG_OPERATION_CANCELED_BY_USER'));
		
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=orders', $msg));
	}
	
	function processCardSaveResponse(){
		JRequest::setVar("processor","cardsave");
		$this->processResponse();
	}
	
	function processCardSaveAutomaticResponse(){
		JRequest::setVar("processor","cardsave");
		$this->processAutomaticResponse();
	}
	
	function processPaypalSubscriptionsResponse(){
		JRequest::setVar("processor","paypalsubscriptions");
		$this->processResponse();
	}
	
	function processPaypalSubscriptionsAutomaticResponse(){
		JRequest::setVar("processor","paypalsubscriptions");
		$this->processAutomaticResponse();
	}
	
	function processDirectProccessing($paymentDetails){
		
		$orderModel= $this->getModel("Orders");
		$order = $orderModel->getOrder($paymentDetails->order_id);
		
		$user = JFactory::getUser();
		$companyModel =  $this->getModel("ManageCompany");
		$companyModel->updateCompanyOwner($order->company_id, $user->id);
		
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&layout=edit&id='.$order->company_id."", false));
	}
}