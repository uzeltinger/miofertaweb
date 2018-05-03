<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The HTML Menus Menu Menus View.
 *
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory

 */
require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.upload.js');

if(!defined('J_JQUERY_UI_LOADED')) {
    JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/jquery-ui.css');
    JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery-ui.js');

    define('J_JQUERY_UI_LOADED', 1);
}

class JBusinessDirectoryViewManageBookmarks extends JBusinessDirectoryFrontEndView
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
