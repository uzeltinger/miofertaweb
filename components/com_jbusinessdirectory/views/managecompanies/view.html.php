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
JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/companies.js');

// following translations will be used in js
JText::script('COM_JBUSINESS_DIRECTORY_COMPANIES_CONFIRM_DELETE');

/**
 * The HTML Menus Menu Menus View.
 *
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory

 */
require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

class JBusinessDirectoryViewManageCompanies extends JBusinessDirectoryFrontEndView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->total 		= $this->get('Total');
		
		$layout = JRequest::getVar("layout");
		if(isset($layout)){
			$tpl = $layout;
		}
		
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$this->actions = JBusinessDirectoryHelper::getActions();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);
	}

}
