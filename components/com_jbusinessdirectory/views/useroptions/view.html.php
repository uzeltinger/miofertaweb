<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
JHtml::_('script',  'components/com_jbusinessdirectory/assets/js/jquery-ui.js');
JHtml::_('script',  'components/com_jbusinessdirectory/assets/js/raphael-min.js');
JHtml::_('script',  'components/com_jbusinessdirectory/assets/js/morris.min.js');
JHtml::_('script',  'components/com_jbusinessdirectory/assets/js/prettify.min.js');
JHtml::_('stylesheet',  'components/com_jbusinessdirectory/assets/css/prettify.min.css');
JHtml::_('stylesheet',  'components/com_jbusinessdirectory/assets/css/morris.css');


require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

class JBusinessDirectoryViewUserOptions extends JBusinessDirectoryFrontEndView
{
	function __construct()
	{
		parent::__construct();
	}
	
	function display($tpl = null)
	{
		
		$this->companies = $this->get('Companies');
		
		$this->actions = JBusinessDirectoryHelper::getActions();
		$this->appSettings =  JBusinessUtil::getInstance()->getApplicationSettings();
		
		$this->statistics = $this->get("Statistics");
		
		parent::display($tpl);
		
	}
}
?>
