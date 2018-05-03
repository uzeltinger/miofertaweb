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
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'bookmark.php');

class JBusinessDirectoryModelManageBookmark extends JBusinessDirectoryModelBookmark
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
		$bookmarksTable = JTable::getInstance("Bookmark", "JTable", array());
		
		$bookmarkId = JRequest::getVar("id",0);
		
		$this->setState('bookmark.id', $bookmarkId);
			
		$packageId = JRequest::getInt('filter_package');
		if(isset($packageId)){
			$this->setState('bookmark.packageId', $packageId);
		}
	}
	
}
?>