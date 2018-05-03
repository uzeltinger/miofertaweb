<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/circle.css');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/events.js');

// following translations will be used in js
JText::script('COM_JBUSINESS_DIRECTORY_EVENTS_CONFIRM_DELETE');

require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

class JBusinessDirectoryViewManageCompanyEvents extends JBusinessDirectoryFrontEndView
{

	protected $pagination;
	protected $item;
	protected $State;

	function __construct()
	{
		parent::__construct();
	}

	function display($tpl = null)
	{
		$this->companyId 	= $this->get('CompanyId');
		$this->items		= $this->get('Events');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->total		= $this->get('Total');

		$this->isCreateEventAllow = $this->get('CreateEventPermission');
		
		$this->actions = JBusinessDirectoryHelper::getActions();
		
		
		parent::display($tpl);
	}
}
?>
