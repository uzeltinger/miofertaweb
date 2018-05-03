<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath('/administrator/components/com_jbusinessdirectory/tables');

class CompanyBookingService
{
    /**
     * Create the summary for the service that has been booked
     *
     * @param $serviceDetails object containing the booked service details
     * @return string
     */
    public static function getServiceSummary($serviceDetails)
    {
        $result = "";
        $result .= "<div class=\"service-booking-details\">";
        $result .= "<table>";
        $result .= "<tr><td colspan=2><strong>" . JText::_("LNG_SERVICE_RESERVATION_DETAILS") . "<strong></td></tr>";
        $result .= "<tr><td>" . JText::_("LNG_SERVICE_NAME") . "</td><td>" . $serviceDetails->name . "</td></tr>";
        $result .= "<tr><td>" . JText::_("LNG_PROVIDER_NAME") . "</td><td>" . $serviceDetails->providerName . "</td></tr>";
        $result .= "<tr><td>" . JText::_("LNG_COMPANY") . "</td><td>" . $serviceDetails->companyName . "</td></tr>";
        $result .= "<tr><td>" . JText::_("LNG_DURATION") . "</td><td>" . JBusinessUtil::formatTimePeriod($serviceDetails->duration, 1) . "</td></tr>";
        $result .= "<tr><td>" . JText::_("LNG_DATE_AND_TIME") . "</td><td>" . $serviceDetails->date . ' - ' . JBusinessUtil::convertTimeToFormat($serviceDetails->hour) . "</td></tr>";
        $result .= "<tr><td>" . JText::_("LNG_PRICE") . "</td><td>" . JBusinessUtil::getPriceFormat($serviceDetails->price, $serviceDetails->currency_id) . "</td></tr>";

        $result .= "</table>";
        $result .= "</div>";

        return $result;
    }

    /**
     * Create the buyer details summary
     *
     * @param $buyerDetails object containing the buyer details
     * @return string
     */
    public static function getBuyerDetailsSummary($buyerDetails)
    {
        $result = "";
        $result .= "<div class=\"service-booking-details\">";
        $result .= "<table>";
        $result .= "<tr><td colspan=2><strong>" . JText::_("LNG_BUYER_DETAILS") . "<strong></td></tr>";
        $result .= "<tr><td>" . JText::_("LNG_NAME") . "</td><td>" . $buyerDetails->first_name . " " . $buyerDetails->last_name . "</td></tr>";
        $result .= "<tr><td>" . JText::_("LNG_ADDRESS") . "</td><td>" . JBusinessUtil::getAddressText($buyerDetails) . "</td></tr>";
        $result .= "<tr><td>" . JText::_("LNG_EMAIL") . "</td><td>" . $buyerDetails->email . "</td></tr>";
        $result .= "<tr><td>" . JText::_("LNG_PHONE") . "</td><td>" . $buyerDetails->phone . "</td></tr>";
        $result .= "</table>";
        $result .= "</div>";

        return $result;
    }

    /**
     * Method that retrieves additional details from the database for the service that
     * has been booked
     *
     * @param $serviceDetails object containing existing details about the services
     * @return mixed
     */
    public static function getServiceDetails($serviceDetails)
    {
        $db = JFactory::getDbo();
        $query = "select cs.*, sp.name as providerName, sp.id as providerId, cs.duration, cp.name as companyName
				  from #__jbusinessdirectory_company_services as cs
				  left join #__jbusinessdirectory_company_provider_services as cps on cs.id = cps.service_id
				  left join #__jbusinessdirectory_company_providers as sp on sp.id = cps.provider_id
				  left join #__jbusinessdirectory_companies as cp on cp.id = cs.company_id
				  where cs.id = $serviceDetails->serviceId and sp.id = $serviceDetails->providerId";
        $db->setQuery($query);

        $result = $db->loadObject();

        return $result;
    }

    /**
     * Save bookings information
     *
     * @param $bookingDetails
     * @return bool
     * @throws Exception
     */
    public static function saveBooking($bookingDetails)
    {
        $serviceBookingTable = JTable::getInstance('CompanyServiceBookings', 'JTable');

        // Create a booking record on the table
        $serviceBookingTable->service_id = $bookingDetails->serviceDetails->id;
        $serviceBookingTable->provider_id = $bookingDetails->serviceDetails->providerId;
        $serviceBookingTable->date = JBusinessUtil::convertToMysqlFormat($bookingDetails->serviceDetails->date);
        $serviceBookingTable->time = JBusinessUtil::convertTimeToFormat($bookingDetails->serviceDetails->hour);
        $serviceBookingTable->first_name = $bookingDetails->buyerDetails->first_name;
        $serviceBookingTable->last_name = $bookingDetails->buyerDetails->last_name;
        $serviceBookingTable->address = $bookingDetails->buyerDetails->address;
        $serviceBookingTable->city = $bookingDetails->buyerDetails->city;
        $serviceBookingTable->region = $bookingDetails->buyerDetails->county;
        $serviceBookingTable->country = $bookingDetails->buyerDetails->country_name;
        $serviceBookingTable->postal_code = $bookingDetails->buyerDetails->postalCode;
        $serviceBookingTable->phone = $bookingDetails->buyerDetails->phone;
        $serviceBookingTable->email = $bookingDetails->buyerDetails->email;

        $serviceBookingTable->amount = $bookingDetails->amount;
        $serviceBookingTable->status = SERVICE_BOOKING_CREATED;

        if (!$serviceBookingTable->store()) {
            $application = JFactory::getApplication();
            $application->enqueueMessage($serviceBookingTable->getDbo()->getErrorMsg(), 'error');
            return false;
        }

        $bookingId = $serviceBookingTable->id;

        return $bookingId;
    }

    /**
     * Method that gets the booking details
     *
     * @param $bookingId int ID of the booking
     * @return array containing the booking details
     */
    public static function getBookingDetails($bookingId)
    {
        $serviceBookingTable = JTable::getInstance('CompanyServiceBookings', 'JTable', array());
        $result = $serviceBookingTable->getBookingDetails($bookingId);

        return $result;
    }
}