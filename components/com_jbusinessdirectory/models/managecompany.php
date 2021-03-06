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
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'company.php');

class JBusinessDirectoryModelManageCompany extends JBusinessDirectoryModelCompany
{ 
	
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record)
	{
		return true;
	}
	
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record)
	{
		return true;
	}
	
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEdit($record)
	{
		return true;
	}
	
	protected function populateState()
	{
		$app = JFactory::getApplication('administrator');
	
		$user = JFactory::getUser();
		$companiesTable = JTable::getInstance("Company", "JTable", array());
		
		$companyId = JRequest::getVar("id",0);
		$companyId = intval($companyId);
		$this->setState('company.id', $companyId);
			
		$packageId = JRequest::getInt('filter_package');
		if(isset($packageId)){
			$this->setState('company.packageId', $packageId);
		}
	}
	
	public function updateCompanyOwner($companyId, $userId){
		// Get a row instance.
		$table = $this->getTable("Company");
		$table->load($companyId);
		$table->userId = $userId;
		
		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());
			return false;
		}
	}

	function getTotal(){
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$user = JFactory::getUser();
			if($user->id==0)
				return 0;
			$companiesTable = $this->getTable("Company");
			$this->_total = $companiesTable->getTotalListings($user->id);
		}
		return $this->_total;
	}
}
?>