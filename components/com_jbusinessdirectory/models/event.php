<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelitem');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');
require_once( JPATH_COMPONENT_ADMINISTRATOR.'/library/category_lib.php');

class JBusinessDirectoryModelEvent extends JModelItem
{
	var $event = null;
	
	function __construct()
	{
		parent::__construct();
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$this->eventId = JFactory::getApplication()->input->get('eventId');
		$this->eventId = intval($this->eventId);
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Event', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	function getEvent(){
		$eventsTable = JTable::getInstance("Event", "JTable");
		$event =  $eventsTable->getActiveEvent($this->eventId);
		
		if(empty($event))
			return $event;

		$this->event = $event;
		
		$event->pictures = $eventsTable->getEventPictures($this->eventId);
		$this->increaseViewCount($this->eventId);
		
		$companiesTable = JTable::getInstance("Company", "JTable");
		$company = $companiesTable->getCompany($event->company_id);
		$event->company=$company;

		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEntityTranslation($event, EVENT_DESCRIPTION_TRANSLATION);
			if(!empty($event->company)){
				JBusinessDirectoryTranslations::updateEntityTranslation($event->company, BUSSINESS_DESCRIPTION_TRANSLATION);
			}
		}

		$dates = '';
		if(!JBusinessUtil::emptyDate($event->booking_open_date) && !JBusinessUtil::emptyDate($event->booking_close_date) && $event->booking_open_date!=$event->booking_close_date){
			$dates = JBusinessUtil::getDateGeneralFormat($event->booking_open_date).' - '.JBusinessUtil::getDateGeneralFormat($event->booking_close_date);
		}
		else if($event->booking_open_date==$event->booking_close_date){
			$dates = JBusinessUtil::getDateGeneralFormat($event->booking_open_date);
		}
		else if(!JBusinessUtil::emptyDate($event->booking_open_date)){
			$dates = JText::_('LNG_STARTING_FROM').' '.JBusinessUtil::getDateGeneralFormat($event->booking_open_date);
		}
		else if(!JBusinessUtil::emptyDate($event->booking_close_date)){
			$dates = JText::_('LNG_UNTIL').' '.JBusinessUtil::getDateGeneralFormat($event->booking_close_date);
		}

		$event->dates = $dates;

		$event->attachments = JBusinessDirectoryAttachments::getAttachments(EVENTS_ATTACHMENTS, $this->eventId, true);
		if (!empty($event->attachments)) {
			$event->attachments = array_slice($event->attachments,0, $this->appSettings->max_attachments);
			foreach ($event->attachments as $attach) {
				$attach->properties = JBusinessUtil::getAttachProperties($attach);
			}
		}

        $attributeConfig = JBusinessUtil::getAttributeConfiguration();
        $event->company = JBusinessUtil::updateItemDefaultAtrributes($event->company, $attributeConfig);
		if ($this->appSettings->apply_attr_events) {
			$event = JBusinessUtil::updateItemDefaultAtrributes($event, $attributeConfig);
		}

		//dispatch load event
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onAfterJBDLoadEvent', array($event));

		return $event;
	}

	function getEventAttributes(){
		$attributesTable = $this->getTable('EventAttributes');
        $categoryId = null;
        if($this->appSettings->enable_attribute_category) {
            $categoryId = -1;
            if(!empty($this->event->main_subcategory))
                $categoryId = $this->event->main_subcategory;
        }
        $result = $attributesTable->getEventAttributes($this->eventId, $categoryId);

        return $result;
	}

	function getEventTickets(){
		$ticketsTable = $this->getTable("EventTickets");
		$eventTickets = $ticketsTable->getTicketsByEvent($this->eventId);

		$ticketsBooked = EventBookingService::getNuberOfAvailableTickests($this->eventId);
		$result = array();
		foreach($eventTickets as &$eventTicket){
			if(empty($eventTicket->max_booking) || ($eventTicket->max_booking > $eventTicket->quantity)){
				$eventTicket->max_booking = $eventTicket->quantity;
			}
			
			if(isset($ticketsBooked[$eventTicket->id]) && $eventTicket->max_booking > ($eventTicket->quantity - $ticketsBooked[$eventTicket->id])){
				$eventTicket->max_booking = $eventTicket->quantity - $ticketsBooked[$eventTicket->id];
			}
			
			if($eventTicket->max_booking < 0){
				$eventTicket->max_booking = 0;
			}
			
			if($eventTicket->max_booking > 0){
				$result[] = $eventTicket;
			}
		}
		
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEventTicketsTranslation($result);
		}

		return $result;
	}

	
	function reserveTickets(){
		EventUserDataService::initializeUserData();
		
		$eventsTable = JTable::getInstance("Event", "JTable");
		$event =  $eventsTable->getEvent($this->eventId);
		
		$ticket_quantity = JFactory::getApplication()->input->get('ticket_quantity');
		$ticket_id = JRequest::getVar('ticket_id');
		$tickets = array();
		$totalTickets = 0;
		foreach ($ticket_quantity as $key => $val) {
			if($val>0){
				$ticket = new stdClass();
				$ticket->id = $ticket_id[$key];
				$ticket->quantity = $val;
				$tickets[] = $ticket;
				$totalTickets+=$val;
			}
		}
		
		$ticketsBooked = EventBookingService::getNuberOfAvailableTickests($this->eventId);
		
		$totalTicketsBooked = 0;
		foreach($ticketsBooked as $t){
			$totalTicketsBooked+=$t;
		}
		
		if($totalTickets>($event->total_tickets-$totalTicketsBooked)){
			JError::raiseWarning(500, JText::_('LNG_TICKET_MAX_NUMBER_EXCEED'));
			return false;
		}
		
		if($totalTickets>($event->total_tickets-$totalTicketsBooked)){
			JError::raiseWarning(500, JText::_('LNG_TICKET_MAX_NUMBER_EXCEED'));
			return false;
		}
		
		if(empty($tickets)){
			JError::raiseWarning(500, JText::_('LNG_NO_TICKET_SELECTED'));
			return false;
		}
		
		$result = EventUserDataService::reserveTickests($this->eventId, $tickets);
		
		return $result;
	}

	/**
	 * Get the events that are about to expire and send an email to the event owners
	 */
	function checkEventsAboutToExpire(){
		$eventTable = $this->getTable("Event");
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$nrDays = $appSettings->expiration_day_notice;
		$events = $eventTable->getEventsAboutToExpire($nrDays);
		foreach($events as $event){
			echo "sending expiration e-mail to: ".$event->name;
			$result = EmailService::sendEventExpirationEmail($event, $nrDays);
			if($result)
				$eventTable->updateExpirationEmailDate($event->id);
		}
		exit;
	}

    function deleteExpiredEvents(){
        $eventTable = $this->getTable("Event");
        $eventTable->deleteExpiredEvents();
        exit;
    }

	function getPlainEvent($eventid){
		$eventsTable = $this->getTable("Event");
		$event = $eventsTable->getEvent($eventid);
		return $event;
	}

	function contactEventCompany($data){
		$company = $this->getTable("Company");
		$company->load($data['companyId']);

		if (!empty($data['contact_id_event']))
			$company->email = $data['contact_id_event'];

		$data["description"] = nl2br(htmlspecialchars($data["description"], ENT_QUOTES));

		$ret = EmailService::sendContactEventCompanyEmail($company, $data);

		return $ret;
	}

	/**
	 * Method that gets the appointment data from the form and saves them in the table
	 *
	 * @param $data array holding the data input from the user
	 * @return bool
	 */
	function leaveAppointment($data){
		$eventsTable = $this->getTable("Event");
		$data["date"] = JBusinessUtil::convertToMysqlFormat($data["date"]);
		$data["time"] = JBusinessUtil::convertTimeToMysqlFormat($data["time"]);

		if($eventsTable->storeAppointment($data)){
			return true;
		}
		return false;
	}
	
	
	/**
	 * Retrieve the associated companies related to this particular event
	 *
	 * @return mixed
	 */
	function getAssociatedCompanies()
	{
		$table = $this->getTable("EventAssociatedCompanies");
		
		$searchDetails = array();
		$searchDetails["eventId"] = $this->eventId;

		$companies = $table->getAssociatedCompaniesDetails($searchDetails);
		return $companies;
	}

	/**
	 * Method to retrieve all companies belonging to a certain user
	 *
	 * @return mixed
	 */
	function getCompaniesByUserId() {
		$user = JFactory::getUser();
		if($user->id == 0)
			return null;

		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

		$searchDetails = array();
		$searchDetails['userId'] = $user->id;
		$searchDetails['enablePackages'] = $appSettings->enable_packages;
		$searchDetails['showPendingApproval'] = $appSettings->show_pending_approval;

		$companiesTable = $this->getTable("Company");
		$companies =  $companiesTable->getCompaniesByNameAndCategories($searchDetails);

		return $companies;
	}

	/**
	 * Retrieve the associated companies which belong to a certain user
	 * and related to this particular event
	 *
	 * @param $eventId int ID of the event
	 * @return mixed
	 */
	function getUserAssociatedCompanies($eventId=null) {
		$user = JFactory::getUser();
		if($user->id == 0)
			return null;

		$table = $this->getTable("EventAssociatedCompanies");
		if(empty($eventId))
			$eventId = $this->eventId;
		$searchDetails = array();

		$searchDetails["eventId"] = $eventId;
		$searchDetails["userId"] = $user->id;

		$companies = $table->getAssociatedCompaniesDetails($searchDetails);

		return $companies;
	}

	/**
	 * Method that gets a list of company ids and the ID of the event, and creates
	 * new associations between the companies and the event. Sends an email to the event owner
	 * notifying about the new associations
	 *
	 * @param $eventId int ID of the event
	 * @param $companyIds string concatenated ID's of companies
	 */
	function associateCompaniesAjax($eventId, $companyIds) {
		$companyIds = explode(',', $companyIds);

		$associatedTable = $this->getTable('EventAssociatedCompanies', 'JTable');
		$associatedTable->storeAssociatedCompanies($eventId, $companyIds);

		$companies = $this->getUserAssociatedCompanies($eventId);

		$eventsTable = JTable::getInstance("Event", "JTable");
		$event =  $eventsTable->getEvent($eventId);

		if(!empty($companies)) {
			// prepare the company names to be sent in the email
			$companyNames = '';
			foreach ($companies as $company) {
                $companyNames .= '<p>';
                $companyLink = '<a href="'.JBusinessUtil::getCompanyLink($company).'">'.$company->name.'</a>';
				$companyNames .= $companyLink;
				$companyNames .= '<p>';
			}

			// send email to event owner
			EmailService::sendCompanyAssociationNotification($event, $companyNames);
		}
	}

	/**
	 * Get all event videos
	 *
	 * @return mixed
	 */
	function getEventVideos() {
		$table = $this->getTable("EventVideos");
		$videos = $table->getEventVideos($this->eventId);

		if(!empty($videos)) {
			$data = array();
			foreach($videos as $video) {
				$data = JBusinessUtil::getVideoDetails($video->url);
				$video->url = $data['url'];
				$video->videoType = $data['type'];
				$video->videoThumbnail = $data['thumbnail'];
			}
		}

		return $videos;
	}

    /**
	 * Method to increase the view count of the event, both on the
	 * event and statistics table
	 *
     * @param $eventId int ID of the event
     * @return bool
     */
	function increaseViewCount($eventId) {
		$eventsTable = $this->getTable();
        $eventsTable->increaseViewCount($eventId);

        // prepare the array with the table fields
        $data = array();
        $data["id"] = 0;
        $data["item_id"] = $eventId;
        $data["item_type"] = STATISTIC_ITEM_EVENT;
        $data["date"] = JBusinessUtil::convertToMysqlFormat(date('Y-m-d')); //current date
        $data["type"] = STATISTIC_TYPE_VIEW;
        $statisticsTable = $this->getTable("Statistics", "JTable");
        if(!$statisticsTable->save($data))
            return false;

        return true;
	}
}
?>

