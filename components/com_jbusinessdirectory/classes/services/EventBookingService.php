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

class EventBookingService{
	
	/**
	 * Create the booking details summary
	 * 
	 * @param unknown_type $bookingDetails
	 */
	public static function getBookingSummary($bookingDetails){
		$result = "";
		
		$result.="<div class=\"event-booking-details\">";
		$result.="<div class=\"event-details\">";
		if(isset($bookingDetails->event->pictures)){
			$result.="<div class=\"event-image\">";
			$result.= "<img src=\"".JURI::root().PICTURES_PATH.$bookingDetails->event->pictures[0]->picture_path."\" title=\"".$bookingDetails->event->name."\" />";
			$result.="</div>";
		}
		
		$result.="<h4 class=\"event-name\">".$bookingDetails->event->name."</h4>";
		$result.="<div class=\"event-adress\">";
		$result.= JBusinessUtil::getAddressText($bookingDetails->event);
		$result.="</div>";
		$result.="<div class=\"event-contact-details\">";
		
		if(!empty($bookingDetails->event->contact_phone)){
			$result.="<span><strong>".JText::_("LNG_PHONE")."</strong>: ".$bookingDetails->event->contact_phone."</span>&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		
		if(!empty($bookingDetails->event->contact_email)){
			$result.="<span><strong>".JText::_("LNG_EMAIL")."</strong>: ".$bookingDetails->event->contact_email."</span>";
		}
									
		$result.="</div>";
		$result.="<div class=\"clear\"></div>";
		$result.="</div>";
	
		$result.=self::getTicketsSummary($bookingDetails);
		
		$result.="</div>";		
		
		return $result;
	}
	
	/**
	 * Create the ticket summary for provided tickets
	 * 
	 * @param unknown_type $bookingDetails
	 */
	public static function getTicketsSummary($bookingDetails){
		$result = "";
		$result.="<div class=\"booking-tickets\">";
		$result.="<table width=\"100%\">";
		$result.="<tr><td><strong>".JText::_("LNG_TICKETS_INFO")."<strong></td></tr>";
		
		foreach($bookingDetails->tickets as $ticket){
			$result.="<tr><td>";
			$result.=$ticket->ticket_quantity." x ".$ticket->name."</td><td>".JBusinessUtil::getPriceFormat($ticket->price*$ticket->ticket_quantity, $bookingDetails->event->currency_id);
			$result.="</td></tr>";
		}
		
		$result.="<tr><td>&nbsp;</td></tr>";
		$result.="<tr><td><strong>".JText::_("LNG_BOOKING_TOTAL")."</strong></td><td>".JBusinessUtil::getPriceFormat($bookingDetails->amount, $bookingDetails->event->currency_id)."</td></tr>";
		$result.="<tr><td></td></tr>";
		
		$result.="</table>";
		$result.="</div>";
		
		return $result;
	}
	
	/**
	 * Create the guest details summary 
	 */
	public static function getGuestDetailsSummary($guestDetails){
		
		$result = "";
		$result.="<div class=\"booking-guest-details\">";
		$result.="<table>";
		$result.="<tr><td colspan=2><strong>".JText::_("LNG_GUEST_DETAILS")."<strong></td></tr>";
		$result.="<tr><td>".JText::_("LNG_NAME")."</td><td>".$guestDetails->first_name." ".$guestDetails->last_name."</td></tr>";
		$result.="<tr><td>".JText::_("LNG_ADDRESS")."</td><td>". JBusinessUtil::getAddressText($guestDetails)."</td></tr>";
		$result.="<tr><td>".JText::_("LNG_EMAIL")."</td><td>".$guestDetails->email."</td></tr>";
		$result.="<tr><td>".JText::_("LNG_PHONE")."</td><td>".$guestDetails->phone."</td></tr>";
		
		$result.="</table>";
		$result.="</div>";
		
		return $result;
	}
	
	/**
	 * Get number of booked tickets
	 * @param unknown_type $eventId
	 */
	public static function getNuberOfAvailableTickests($eventId){
		$eventBookingTable = JTable::getInstance('EventBookings','JTable', array());
		$ticketsNrBookings = $eventBookingTable->getNumberOfBookings($eventId);
		
		$result = array();
		foreach($ticketsNrBookings as $ticketBookigns){
			$result[$ticketBookigns->id]= $ticketBookigns->nr_tickets;
		}
		
		return $result;
		
	}
	
	/**
	 * Save bookings information
	 *
	 * @param $data
	 * @return bool
	 * @throws Exception
	 */
	public static function saveBookings($bookingDetails){

		$eventBookingTable = JTable::getInstance('EventBookings', 'JTable');
	
		// Create a booking record on the table
		$eventBookingTable->event_id = $bookingDetails->event->id;
		$eventBookingTable->first_name = $bookingDetails->guestDetails->first_name;
		$eventBookingTable->last_name = $bookingDetails->guestDetails->last_name;
		$eventBookingTable->address	= $bookingDetails->guestDetails->address;
		$eventBookingTable->city	= $bookingDetails->guestDetails->city;
		$eventBookingTable->region	= $bookingDetails->guestDetails->county;
		$eventBookingTable->country	= $bookingDetails->guestDetails->country_name;
		$eventBookingTable->postal_code = $bookingDetails->guestDetails->postalCode;
		$eventBookingTable->phone = $bookingDetails->guestDetails->phone;
		$eventBookingTable->email = $bookingDetails->guestDetails->email;
		
		$eventBookingTable->amount = $bookingDetails->amount;
		$eventBookingTable->status = EVENT_BOOKING_CREATED;
	
		if(!$eventBookingTable->store()) {
			$application = JFactory::getApplication();
			$application->enqueueMessage( $eventBookingTable->getDbo()->getErrorMsg(), 'error');
			return false;
		}
	
		$bookingId = $eventBookingTable->id;
		// Save all the ticket and booking data
		$ticketBookingTable = JTable::getInstance('EventBookingTickets', 'JTable');
		foreach($bookingDetails->tickets as $ticket){
			$ticketBookingTable->ticket_id = $ticket->id;
			$ticketBookingTable->booking_id = $bookingId;
			$ticketBookingTable->ticket_quantity = $ticket->ticket_quantity;
			if(!$ticketBookingTable->store()) {
				$application = JFactory::getApplication();
				$application->enqueueMessage( $eventBookingTable->getDbo()->getErrorMsg(), 'error');
				return false;
			}
		}

		return $bookingId;
	}
	
	
	/**
	 * Get booking details
	 * @param unknown_type $eventId
	 */
	public static function getBookingDetails($bookingId){
		$db =JFactory::getDBO();
		$query = "select eb.*, SUM(ticket_quantity) as nr_tickets
				from #__jbusinessdirectory_company_event_bookings eb
				left join #__jbusinessdirectory_company_event_booking_tickets ebt on ebt.booking_id = eb.id
				where eb.id = $bookingId";
		$db->setQuery($query);
		
		$result =  $db->loadObject();

		return $result;
	}
	
}