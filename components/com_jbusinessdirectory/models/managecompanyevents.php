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
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'events.php');

class JBusinessDirectoryModelManageCompanyEvents extends JBusinessDirectoryModelEvents{
	
	function __construct(){
		parent::__construct();
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$this->_total = 0;

		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$offset = JRequest::getUInt('limitstart');
		$this->setState('list.offset', $offset);

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
	/**
	*
	* @return object with data
	*/
	function getEvents()
	{
		// Load the data
		$user = JFactory::getUser();
		$packagesTable = $this->getTable("Package"); 
		$companiesTable = $this->getTable("Company");
		$this->companyIds = $this->getCompaniesByUserId();
		$eventsTable = JTable::getInstance('Event','JTable', array());

		if (empty( $this->_data ) && !empty($this->companyIds)) {
			$this->_data = $eventsTable->getUserEvents($user->id, $this->companyIds, $this->getState('limitstart'), $this->getState('limit'));

			if(!empty($this->_data)){
				foreach($this->_data as $event) {
	
					$event->allow_events = false;
					$event->expired = false;
	
					if(!$this->appSettings->enable_packages || $this->appSettings->item_decouple) {
						$event->allow_events = true;
					} else if(!empty($event->company_id)) {
                        $package = $packagesTable->getCurrentActivePackage($event->company_id);

                        if (!empty($package->features)) {
                            $event->features = $package->features;
                        } else {
                            $event->features = array();
                        }

                        if (in_array(COMPANY_EVENTS, $event->features))
                            $event->allow_events = true;
                    }
                    if( !empty($event->end_date) && $event->end_date != '0000-00-00' && (strtotime(date("Y-m-d")) > strtotime($event->end_date)) )
                        $event->expired = true;

                    $event->checklist = JBusinessUtil::getCompletionProgress($event, 3);
                    $event->progress = 0;

                    if(count($event->checklist) > 0) {
                        // calculate percentage of completion
                        $count = 0;
                        $completed = 0;
                        foreach ($event->checklist as $key => $val) {
                            if ($val->status)
                                $completed++;
                            $count++;
                        }
                        $event->progress = (float)($completed / $count);
                    }
                    $event->progress = round($event->progress, 4);
				}
			}
		}
		
		if(empty($this->_data)){
			$this->_data = array();
		}
		
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEventsTranslation($this->_data);
		}
		
		return $this->_data;
	}
	
	/**
	 * Check if event creating is allowed
	 * 
	 * @return boolean
	 */
	function getCreateEventPermission(){
		$packagesTable = $this->getTable("Package");
	
		$user = JFactory::getUser();
		$companiesTable = $this->getTable("Company");
		$companies =  $companiesTable->getCompaniesByUserId($user->id);
		
		if(!empty($companies)){
			if(!$this->appSettings->enable_packages || $this->appSettings->item_decouple) {
				return true;
			}else{
				foreach ($companies as $company){
					$package = $packagesTable->getCurrentActivePackage($company->id);
					
					if(empty($package)){
						continue;
					}
					
					if(!empty($package->features)){
						$package->features = $package->features;
					} else {
						$package->features = array();
					}
					
					if (!empty($package->features) && in_array(COMPANY_EVENTS, $package->features))
						return true;
				}
			}
		}
	
		return false;
	}
	
	function getCompaniesByUserId(){
		$user = JFactory::getUser();
		$companiesTable = $this->getTable("Company");
		$companies =  $companiesTable->getCompaniesByUserId($user->id);
		$result = array();
		foreach($companies as $company){
			$result[] = $company->id;
		}
		return $result;
	}
	
	function getTotal()
	{
	    $user = JFactory::getUser();
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$offersTable = $this->getTable("Event");
			$this->_total = $offersTable->getTotalUserEvents($this->getCompaniesByUserId(),  $user->id);
		}
		
		return $this->_total;
	}
}
?>