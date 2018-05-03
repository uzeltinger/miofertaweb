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
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'events.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'events.php');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JBusinessDirectoryControllerManageCompanyEvents extends JBusinessDirectoryControllerEvents
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
	 * Delete an event or the associated reccuring events.
	 */
	public function delete(){
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		// Get the model.
		$model = $this->getModel("ManageCompanyEvent");
		$this->deleteEvents($model);
	
		$this->setRedirect('index.php?option=com_jbusinessdirectory&view='.$this->input->get('view'));
	}
}