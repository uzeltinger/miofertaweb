<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');


class JBusinessDirectoryModelUserOptions extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 
	 * @return object with data
	 */
	function getCompanies()
	{
		$user = JFactory::getUser();
		$companiesTable = $this->getTable("Company");
		$companies =  $companiesTable->getCompaniesByUserId($user->id);
		return $companies;
	}
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Companies', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getStatistics(){
		$statistics = new stdClass();
	
		$user = JFactory::getUser();
		
		$companyTable = JTable::getInstance('Company','JTable');
		$statistics->totalListings = $companyTable->getTotalListings($user->id);
		$statistics->listingsTotalViews = $companyTable->getListingsViewsOnFront($user->id);
	
		$offersTable = JTable::getInstance('Offer','JTable');
		$statistics->totalOffers = $offersTable->getTotalNumberOfOffers($user->id);
		$statistics->offersTotalViews = $offersTable->getOfferViewsOnFront($user->id);
	
		$eventsTable = JTable::getInstance('Event','JTable');
		$statistics->totalEvents = $eventsTable->getTotalNumberOfEvents($user->id);
		$statistics->eventsTotalViews = $eventsTable->getEventViewsOnFront($user->id);
	
		$statistics->totalViews = $statistics->listingsTotalViews + $statistics->offersTotalViews + $statistics->eventsTotalViews;
	
		return $statistics;
	}

    public function getNewCompanies() {
        $user = JFactory::getUser();
        $start_date = JRequest::getVar('start_date');
        $start_date = date("Y-m-d", strtotime($start_date));
        $end_date = JRequest::getVar('end_date');
        $end_date = date("Y-m-d", strtotime($end_date));

        $companyTable = JTable::getInstance('Company','JTable');
        $result = $companyTable->getNewCompanyViews($start_date, $end_date, $user->id);

        //add start date element if it does not exists
        if (!empty($result)) {
            if ($result[0]->date != $start_date) {
                $item = new stdClass();
                $item->date = $start_date;
                $item->value = 0;
                array_unshift($result, $item);
            }

            //add end date element if it does not exists
            if (end($result)->date != $end_date) {
                $item = new stdClass();
                $item->date = $end_date;
                $item->value = 0;
                array_push($result, $item);
            }
        }else{
            $firstItem = new stdClass();
            $firstItem->date = $start_date;
            $firstItem->value = 0;
            array_unshift($result, $firstItem);

            $endItem = new stdClass();
            $endItem->date = $end_date;
            $endItem->value = 0;
            array_push($result, $endItem);
        }

        return $result;
    }

    public function getNewOffers() {
        $user = JFactory::getUser();
        $start_date = JRequest::getVar('start_date');
        $start_date = date("Y-m-d", strtotime($start_date));
        $end_date = JRequest::getVar('end_date');
        $end_date = date("Y-m-d", strtotime($end_date));

        $offerTable = JTable::getInstance('Offer','JTable');
        $result = $offerTable->getNewOffersViews($start_date, $end_date, $user->id);

        //add start date element if it does not exists
        if (!empty($result)) {
            if ($result[0]->date != $start_date) {
                $item = new stdClass();
                $item->date = $start_date;
                $item->value = 0;
                array_unshift($result, $item);
            }

            //add end date element if it does not exists
            if (end($result)->date != $end_date) {
                $item = new stdClass();
                $item->date = $end_date;
                $item->value = 0;
                array_push($result, $item);
            }
        }else{
            $firstItem = new stdClass();
            $firstItem->date = $start_date;
            $firstItem->value = 0;
            array_unshift($result, $firstItem);

            $endItem = new stdClass();
            $endItem->date = $end_date;
            $endItem->value = 0;
            array_push($result, $endItem);
        }

        return $result;
    }

    public function getNewEvents() {
        $user = JFactory::getUser();
        $start_date = JRequest::getVar('start_date');
        $start_date = date("Y-m-d", strtotime($start_date));
        $end_date = JRequest::getVar('end_date');
        $end_date = date("Y-m-d", strtotime($end_date));

        $eventTable = JTable::getInstance('Event','JTable');
        $result = $eventTable->getNewEventsViews($start_date, $end_date, $user->id);

        //add start date element if it does not exists

        if (!empty($result)) {
            if ($result[0]->date != $start_date) {
                $item = new stdClass();
                $item->date = $start_date;
                $item->value = 0;
                array_unshift($result, $item);
            }

            //add end date element if it does not exists
            if (end($result)->date != $end_date) {
                $item = new stdClass();
                $item->date = $end_date;
                $item->value = 0;
                array_push($result, $item);
            }
        }else{
            $firstItem = new stdClass();
            $firstItem->date = $start_date;
            $firstItem->value = 0;
            array_unshift($result, $firstItem);

            $endItem = new stdClass();
            $endItem->date = $end_date;
            $endItem->value = 0;
            array_push($result, $endItem);
        }

        return $result;
    }

}
?>