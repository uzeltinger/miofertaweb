<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');

JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JBusinessDirectoryModelCatalog extends JModelList
{ 
	
	function __construct()
	{
		
		parent::__construct();
		$jinput = JFactory::getApplication()->input;
		$mainframe = JFactory::getApplication();
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$this->enablePackages = $appSettings->enable_packages;
		$this->showPendingApproval = $appSettings->show_pending_approval==1;
		
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$session = JFactory::getSession();
		$this->letter = $jinput->getString('letter');
		if(isset($this->letter)){
			$session->set('letter', $this->letter);
		}
		
		$this->letter = $session->get('letter');
		$session->set("searchType",2);
		$session->set("listing-search",true);
		$activeMenu = JFactory::getApplication()->getMenu()->getActive();
		if(isset($activeMenu)){
			$session->set("menuItemId", $activeMenu->id);
		}
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
	
	function &getCompanies(){
		return $this->companies;
	}
	
	function getLetter(){
		return $this->letter;
	}
	
	function getUsedLetter(){
		$companiesTable = $this->getTable("Company");
		
		$letters =  $companiesTable->getUsedLetters();
		$result = array();
		foreach($letters as $letter){
			$result[$letter->letter]=$letter->letter;
		}
		
		return $result;
	}
	
	function getCompaniesByLetter(){
		$companiesTable = $this->getTable("Company");
		$categoryId = JRequest::getVar('categoryId');
	
		$companies =  $companiesTable->getCompaniesByLetter($this->letter,$this->enablePackages, $this->showPendingApproval, $this->getState('limitstart'), $this->getState('limit'));
		$attributeConfig = JBusinessUtil::getAttributeConfiguration();
        foreach($companies as $company){
			$company->packageFeatures = explode(",", $company->features);
			$attributesTable = $this->getTable('CompanyAttributes');
			$company->customAttributes = $attributesTable->getCompanyAttributes($company->id);
            $company = JBusinessUtil::updateItemDefaultAtrributes($company,$attributeConfig);
        }

		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateBusinessListingsTranslation($companies);
			JBusinessDirectoryTranslations::updateBusinessListingsSloganTranslation($companies);		
		}
		
		
		return $companies;
	}
	
	function getTotalCompaniesByLetter()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$categoryId= JRequest::getVar('categoryId');
			$companiesTable = $this->getTable("Company");
			$this->_total = $companiesTable->getTotalCompaniesByLetter($this->letter, $this->enablePackages, $this->showPendingApproval);
		}
		return $this->_total;
	}
	
	
	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			
		    require_once( JPATH_SITE.'/components/com_jbusinessdirectory/libraries/dirpagination.php');
		    $this->_pagination = new JBusinessDirectoryPagination($this->getTotalCompaniesByLetter(), $this->getState('limitstart'), $this->getState('limit'));
			$this->_pagination->setAdditionalUrlParam('option','com_jbusinessdirectory');
			$this->_pagination->setAdditionalUrlParam('controller','catalog');
			$this->_pagination->setAdditionalUrlParam('view','catalog');
		}
		return $this->_pagination;
	}
	
	function getCategory(){
		$categoryTable = $this->getTable("Category","JBusinessTable");
		$categoryId = JRequest::getVar('categoryId');
		return  $categoryTable->getCategoryById($categoryId);
	}
	
	/**
	 * Get current user location
	 */
	function getLocation(){
		$session = JFactory::getSession();
		$location= $session->get("location");
		return $location;
	}
}
?>