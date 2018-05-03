<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
require_once JPATH_COMPONENT_SITE.'/assets/defines.php';
require_once JPATH_COMPONENT_SITE.'/assets/utils.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/library/category_lib.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/translations.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/attachments.php';
require_once JPATH_COMPONENT_SITE.'/assets/logger.php';
JHtml::_('behavior.framework');

JHTML::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/common.css');
JHTML::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/forms.css');
JHTML::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/responsive.css');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/font-awesome.css');

JHtml::_('jquery.framework', true, true);
define('J_JQUERY_LOADED', 1);

JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.raty.min.js');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.blockUI.js');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/utils.js');

if( !defined('COMPONENT_IMAGE_PATH') )
	define("COMPONENT_IMAGE_PATH", JURI::base()."components/com_jbusinessdirectory/assets/images/");

JBusinessUtil::loadClasses();
JBusinessUtil::loadSiteLanguage();
$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

$jsSettings = JBusinessUtil::addJSSettings();
$jsSettings->isProfile = 1;
$jsSettings->componentImagePath = COMPONENT_IMAGE_PATH;

$document  =JFactory::getDocument();
$document->addScriptDeclaration('
    jQuery(document).ready(function () {
       renderRadioButtons();
    });
 	'
);

if(!defined('JBD_UTILS_LOADED')) {
	$document->addScriptDeclaration('
	        var jbdUtils = new JBDUtils;
	        jbdUtils.construct('.json_encode($jsSettings).');
			'
			);
	define('JBD_UTILS_LOADED', 1);
}



$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);

// Execute the task.
$controller	= JControllerLegacy::getInstance('JBusinessDirectory');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();