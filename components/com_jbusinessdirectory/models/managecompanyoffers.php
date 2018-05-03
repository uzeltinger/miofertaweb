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
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'offers.php');

class JBusinessDirectoryModelManageCompanyOffers extends JBusinessDirectoryModelOffers{
	
	
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
	function getOffers()
	{
		// Load the data	
		$offersTable = $this->getTable("Offer");
		$packagesTable = $this->getTable("Package");
		$user = JFactory::getUser();
		
		if (empty( $this->_data )) {
			$this->_data = $offersTable->getUserOffers($user->id, $this->getCompaniesByUserId(), $this->getState('limitstart'), $this->getState('limit'));
			
			if(!empty($this->_data)){
				foreach($this->_data as $offer) {
	
					$offer->allow_offers = false;
					$offer->expired = false;
					$offer->not_visible = false;
	
					if(!$this->appSettings->enable_packages || $this->appSettings->item_decouple) {
						$offer->allow_offers = true;
					} else if(!empty($offer->companyId)){
                        $package = $packagesTable->getCurrentActivePackage($offer->companyId);

                        if (!empty($package->features)) {
                            $offer->features = $package->features;
                        } else {
                            $offer->features = array();
                        }

                        if (in_array(COMPANY_OFFERS, $offer->features))
                            $offer->allow_offers = true;
                    }
                    if ((!JBusinessUtil::emptyDate($offer->publish_end_date) && strtotime($offer->publish_end_date) && (strtotime(date("Y-m-d")) > strtotime($offer->publish_end_date)) )
                        || (!JBusinessUtil::emptyDate($offer->publish_start_date) && strtotime($offer->publish_start_date) && (strtotime(date("Y-m-d")) < strtotime($offer->publish_start_date)))) {
	                    $offer->not_visible = true;
                    }

					if ((!JBusinessUtil::emptyDate($offer->endDate)) && strtotime($offer->endDate) && (strtotime(date("Y-m-d")) > strtotime($offer->endDate))) {
						$offer->expired = true;
					}

                    $offer->checklist = JBusinessUtil::getCompletionProgress($offer, 2);
                    $offer->progress = 0;

                    if(count($offer->checklist) > 0) {
                        // calculate percentage of completion
                        $count = 0;
                        $completed = 0;
                        foreach ($offer->checklist as $key => $val) {
                            if ($val->status)
                                $completed++;
                            $count++;
                        }
                        $offer->progress = (float)($completed / $count);
                    }
                    $offer->progress = round($offer->progress, 4);
				}
			}
		}

		if(empty($this->_data)){
			$this->_data = array();
		}
		
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateOffersTranslation($this->_data);
		}

		return $this->_data;
	}
	
	/**
	 * Check if offer creation is allowed
	 * @return boolean
	 */
	function getCreateOfferPermission(){
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
					
					if (!empty($package->features) && in_array(COMPANY_OFFERS, $package->features))
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
			$offersTable = $this->getTable("Offer");
			$this->_total = $offersTable->getTotalUserOffers($this->getCompaniesByUserId(),  $user->id);
		}
		return $this->_total;
	}
}
?>