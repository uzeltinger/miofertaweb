<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'event.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'event.php');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JBusinessDirectoryControllerManageCompanyEvent extends JBusinessDirectoryControllerEvent
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
		$this->log = Logger::getInstance();
	}

	/**
	 * Dummy method to redirect back to standard controller
	 *
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyevents', false));
	}
	
	protected function allowEdit($data = array(), $key = 'id'){
		return true;
	}
	
	protected function allowAdd($data = array())
	{
		return true;
	}
	
	public function add()
	{
		$app = JFactory::getApplication();
		$context = 'com_jbusinessdirectory.edit.managecompanyevent';
	
		$result = parent::add();
		if ($result)
		{
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyevent'. $this->getRedirectToItemAppend(), false));
		}
	
		return $result;
	}
	
	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	
	 */
	public function cancel($key = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		$app = JFactory::getApplication();
		$context = 'com_jbusinessdirectory.edit.managecompanyevent';
		$result = parent::cancel();
	
	}
	
	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 */
	public function edit($key = null, $urlVar = null)
	{
		$app = JFactory::getApplication();
		$result = parent::edit();
	
		return true;
	}
	
	function chageState()
	{
		$model = $this->getModel('managecompanyevent');
	
		if ($model->changeState())
		{
			$msg = JText::_( '' );
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_STATE');
		}
	
	
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyevents', $msg));
	}

    /**
     * Method to retrieve attributes by ajax
     */
    function getAttributesAjax() {
        $categoryId = JRequest::getVar('categoryId');
        $eventId = JRequest::getVar('eventId');

        $model = $this->getModel('ManageCompanyEvent');
        $result = $model->getAttributesAjax($categoryId, $eventId);

        /* Send as JSON */
        header("Content-Type: application/json", true);
        echo json_encode($result);
        exit;
    }

    /**
     * Method to retrieve listing by ajax
     */
    function getListingAddressAjax(){
        $companyId = JRequest::getVar('companyId');

        $model = $this->getModel('Event');
        $result = $model->getListing($companyId);

        /* Send as JSON */
        header("Content-Type: application/json", true);
        echo json_encode($result);
        exit;
    }
}