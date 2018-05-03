<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');


class JBusinessDirectoryControllerEvents extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
	}

	function getCalendarEvents(){
		JRequest::setVar('limitstart',0);
		JRequest::setVar('limit',0);
		JRequest::setVar('startDate',JRequest::setVar("start"));
		JRequest::setVar('endDate',JRequest::setVar("end"));
		
		$model = $this->getModel('events');
		$events = $model->getCalendarEvents();
		

		echo json_encode($events);		
		exit;
	}
	
}