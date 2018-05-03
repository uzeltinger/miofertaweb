<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
class JBusinessDirectoryControllerServicePayment extends JControllerLegacy
{
    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    function __construct()
    {
        parent::__construct();
    }

    function showPaymentOptions()
    {
        JRequest::setVar("view", "payment");
        parent::display();
    }

    function processTransaction()
    {
        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $paymentMethod = JRequest::getVar("payment_method", "nopayment");

        $servicePayment = $this->getModel("ServicePayment");
        $order = $servicePayment->getOrder();

        $orderId = CompanyBookingService::saveBooking($order);
        $order->id = $orderId;

        $servicePayment->setState('payment.orderId', $orderId);

        //create and login user(if not created)
        $user = JFactory::getUser();

        $processor = PaymentService::createPaymentProcessor($paymentMethod);
        $paymentDetails = $processor->processTransaction($order, "servicepayment");
        $paymentDetails->details = $processor->getPaymentDetails($paymentDetails);
        PaymentService::addPayment($paymentDetails);

        if ($paymentDetails->status == PAYMENT_REDIRECT) {
            $document = JFactory::getDocument();
            $viewType = $document->getType();
            $view = $this->getView("payment", $viewType, '', array('base_path' => $this->basePath, 'layout' => "redirect"));
            $view->paymentProcessor = $processor;
            $view->display("redirect");

        } else if ($paymentDetails->status == PAYMENT_SUCCESS) {
            $servicePaymentModel = $this->getModel("ServicePayment");
            $servicePaymentModel->updateBookingStatus($paymentDetails);

            $bookingDetails = $servicePaymentModel->getCompleteBookingDetails($paymentDetails);
            EmailService::sendServiceBookingNotification($order);
            $msg = JText::_("LNG_SERVICE_RESERVED_SUCCESSFULLY");
            $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies&companyId=' . $bookingDetails->company_id, false), $msg);
        } else if ($paymentDetails->status == PAYMENT_WAITING) {
            $servicePaymentModel = $this->getModel("ServicePayment");
            $servicePaymentModel->updateBookingStatus($paymentDetails);

            $bookingDetails = $servicePaymentModel->getCompleteBookingDetails($paymentDetails);
            EmailService::sendEventPaymentDetails($bookingDetails);
            $msg = JText::_("LNG_SERVICE_PAYMENT_DETAILS_SENT");
            $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies&companyId=' . $bookingDetails->company_id, false), $msg);
        } else if ($paymentDetails->status == PAYMENT_ERROR) {
            JFactory::getApplication()->enqueueMessage($paymentDetails->error_message, 'warning');
            JRequest::setVar('layout', null);
            JRequest::setVar("view", "payment");
            parent::display();
        }
//
//        $paymentDetails = new stdClass();
//        $paymentDetails->order_id = $orderId;
//        $paymentDetails->status = PAYMENT_REDIRECT;
//        $paymentDetails->payment_status = PAYMENT_STATUS_PENDING;
//
//        $servicePayment->updateBookingStatus($paymentDetails);
//
//        $order = $servicePayment->getCompleteBookingDetails($orderId);
//
//
//
//        //TODO: change redirect layout
//        $msg = JText::_("LNG_PAYMENT_PROCESSED_SUCCESSFULLY");
//        $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=servicepayment&orderId='.$orderId, false), $msg);
//        return;
    }

    function processResponse()
    {
        $this->log->LogDebug("process response");
        $data = JRequest::get('post');
        $this->log->LogDebug(serialize($data));
        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $ssl = 0;
        if ($appSettings->enable_https_payment)
            $ssl = 1;

        $processorType = JRequest::getVar("processor");
        if ($processorType == 'mollie') {
            $bookingId = JRequest::getVar("orderId");
            $data = PaymentService::getPaymentDetails($bookingId);
        }

        $processor = PaymentService::createPaymentProcessor($processorType);
        $paymentDetails = $processor->processResponse($data);
        $booking = CompanyBookingService::getBookingDetails($paymentDetails->order_id);

        if ($paymentDetails->status == PAYMENT_CANCELED || $paymentDetails->status == PAYMENT_ERROR) {
            PaymentService::updatePayment($paymentDetails);
            $msg = JText::_("LNG_TRANSACTION_FAILED");
            $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies&companyId=' . $booking->company_id, false, $ssl), $msg);
        } else {
            $msg = JText::_("LNG_PAYMENT_PROCESSED_SUCCESSFULLY");
            $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies&companyId=' . $booking->company_id, false), $msg);
            if ($appSettings->direct_processing) {
                $this->processDirectProccessing($paymentDetails);
            }

            $this->processAutomaticResponse();
        }
    }

    function processAutomaticResponse()
    {
        $this->log->LogDebug("process automatic response");
        $data = JRequest::get('post');
        $this->log->LogDebug(serialize($data));

        $processorType = JRequest::getVar("processor");
        $this->log->LogDebug("Processor: " . $processorType);
        $processor = PaymentService::createPaymentProcessor($processorType);
        $processorType = JRequest::getVar("processor");
        if ($processorType == 'mollie') {
            $orderId = JRequest::getVar("orderId");
            $data = PaymentService::getPaymentDetails($orderId);
        }
        $paymentDetails = $processor->processResponse($data);

        $this->log->LogDebug("Payment Details: " . serialize($paymentDetails));

        if (empty($paymentDetails->order_id)) {
            $this->log->LogDebug("Empty order Id");
            return;
        }

        $intialPaymentDetails = PaymentService::getPaymentDetails($paymentDetails->order_id);
        $this->log->LogDebug("Initial payment details: " . serialize($intialPaymentDetails));

        if ($intialPaymentDetails->payment_status == PAYMENT_STATUS_PAID) {
            $this->log->LogDebug("booking has been already paid");
            //return;
        }
        PaymentService::updatePayment($paymentDetails);

        if ($paymentDetails->status == PAYMENT_CANCELED || $paymentDetails->status == PAYMENT_ERROR) {

        } else {
            $servicePaymentModel = $this->getModel("ServicePayment");
            $servicePaymentModel->updateBookingStatus($paymentDetails);

            $bookingDetails = $servicePaymentModel->getCompleteBookingDetails($paymentDetails);
            EmailService::sendServiceBookingNotification($bookingDetails);
        }
    }

    function processCancelResponse()
    {
        $this->log->LogDebug("process cancel response ");
        $data = JRequest::get('post');
        $this->log->LogDebug(serialize($data));
        $this->setMessage(JText::_('LNG_OPERATION_CANCELED_BY_USER'));

        $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', $msg));
    }

    function processCardSaveResponse()
    {
        JRequest::setVar("processor", "cardsave");
        $this->processResponse();
    }

    function processCardSaveAutomaticResponse()
    {
        JRequest::setVar("processor", "cardsave");
        $this->processAutomaticResponse();
    }

    function processPaypalSubscriptionsResponse()
    {
        JRequest::setVar("processor", "paypalsubscriptions");
        $this->processResponse();
    }

    function processPaypalSubscriptionsAutomaticResponse()
    {
        JRequest::setVar("processor", "paypalsubscriptions");
        $this->processAutomaticResponse();
    }

    function processDirectProccessing($paymentDetails)
    {

        //TODO: apply changes for services
        $orderModel = $this->getModel("Orders");
        $order = $orderModel->getOrder($paymentDetails->order_id);

        $user = JFactory::getUser();
        $companyModel = $this->getModel("ManageCompany");
        $companyModel->updateCompanyOwner($order->company_id, $user->id);

        $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&layout=edit&id=' . $order->company_id . "", false));
    }
}

?>