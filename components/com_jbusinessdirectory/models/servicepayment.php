<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(DS . 'components/com_jbusinessdirectory/tables');

class JBusinessDirectoryModelServicePayment extends JModelLegacy
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Populate state
     *
     * @param null $ordering
     * @param null $direction
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication('administrator');

        $paymentMethod = JRequest::getVar('payment_method');
        $this->setState('payment.payment_method', $paymentMethod);

    }

    /**
     * Get payment methods
     *
     * @return array
     */
    function getPaymentMethods()
    {
        $paymentMethods = PaymentService::getPaymentProcessors();
        return $paymentMethods;
    }

    /**
     * Method that prepares the booking details into a single object containing all the information needed
     *
     * @return stdClass
     */
    public function getOrder()
    {
        $orderDetails = new stdClass();

        $buyerDetails = CompanyUserDataService::getUserData();
        $serviceDetails = CompanyBookingService::getServiceDetails($buyerDetails->serviceDetails);
        $serviceDetails->date = $buyerDetails->serviceDetails->date;
        $serviceDetails->hour = $buyerDetails->serviceDetails->hour;

        $orderDetails->buyerDetails = $buyerDetails->buyerDetails;
        $orderDetails->serviceDetails = $serviceDetails;
        $orderDetails->amount = $orderDetails->serviceDetails->price;

        return $orderDetails;
    }

    /**
     * Prepares the summary with the billing info and details for the items that have been purchased
     *
     * @return stdClass
     */
    function getOrderDetails()
    {
        $orderDetails = new stdClass();

        $buyerDetails = CompanyUserDataService::getUserData();
        $serviceDetails = CompanyBookingService::getServiceDetails($buyerDetails->serviceDetails);
        $serviceDetails->date = $buyerDetails->serviceDetails->date;
        $serviceDetails->hour = $buyerDetails->serviceDetails->hour;

        $buyerDetails = $buyerDetails->buyerDetails;

        $orderDetails->buyerDetailsSummary = CompanyBookingService::getBuyerDetailsSummary($buyerDetails);
        $orderDetails->serviceSummary = CompanyBookingService::getServiceSummary($serviceDetails);

        return $orderDetails;
    }

    /**
     * Update the service booking status
     *
     * @param object $paymentDetails
     */
    function updateBookingStatus($paymentDetails)
    {
        $serviceBookingsTable = JTable::getInstance('CompanyServiceBookings', 'JTable', array());

        if ($paymentDetails->status == PAYMENT_SUCCESS) {
            $serviceBookingsTable->updateBookingStatus($paymentDetails->order_id, SERVICE_BOOKING_CONFIRMED);
        }
    }

    /**
     * Method that prepares all the booking details once it's completed
     *
     * @param null $orderId int ID of the service booking
     * @return mixed
     */
    function getCompleteBookingDetails($orderId = null)
    {
        if (empty($orderId)) {
            $orderId = JRequest::getVar("orderId");
        }

        $serviceBookingsTable = JTable::getInstance('CompanyServiceBookings', 'JTable', array());
        $serviceBooking = $serviceBookingsTable->getBookingDetails($orderId);

        $serviceBooking->buyerDetails = CompanyBookingService::getBuyerDetailsSummary($serviceBooking);

        if (!isset($serviceBooking->providerId))
            $serviceBooking->providerId = $serviceBooking->provider_id;

        if (!isset($serviceBooking->serviceId))
            $serviceBooking->serviceId = $serviceBooking->service_id;


        $serviceDetails = CompanyBookingService::getServiceDetails($serviceBooking);
        $serviceDetails->date = $serviceBooking->date;
        $serviceDetails->hour = $serviceBooking->time;
        $serviceBooking->serviceDetails = CompanyBookingService::getServiceSummary($serviceDetails);

        return $serviceBooking;
    }
}

?>