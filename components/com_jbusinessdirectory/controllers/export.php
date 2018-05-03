<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');



class JBusinessDirectoryControllerExport extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
	}
	
	public function exportFiles(){
		$path = JRequest::getVar("path");
	
		if(empty($path))
			return;
		
		require_once( JPATH_COMPONENT_ADMINISTRATOR.'/models/companies.php');
		$companiesModel = new JBusinessDirectoryModelCompanies();
		$companiesModel->exportCompaniesCSVtoFile($path."/business_listings.csv");
	
		exit;
		
		require_once( JPATH_COMPONENT_ADMINISTRATOR.'/models/offers.php');
		$offersModel = new JBusinessDirectoryModelOffers();
		$offersModel->exportOffersCSVtoFile($path."/offers.csv");
	
		require_once( JPATH_COMPONENT_ADMINISTRATOR.'/models/events.php');
		$eventsModel = new JBusinessDirectoryModelEvents();
		$eventsModel->exportEventsCSVtoFile($path."/events.csv");

		require_once( JPATH_COMPONENT_ADMINISTRATOR.'/models/conferences.php');
		$conferencesModel = new JBusinessDirectoryModelConferences();
		$conferencesModel->exportConferencesCSVtoFile($path."/conferences.csv");

		require_once( JPATH_COMPONENT_ADMINISTRATOR.'/models/sessionlocations.php');
		$sessionLocationsModel = new JBusinessDirectoryModelSessionlocations();
		$sessionLocationsModel->exportSessionLocationsModelCSVtoFile($path."/sessionlocations.csv");
		
		exit;
	}

}