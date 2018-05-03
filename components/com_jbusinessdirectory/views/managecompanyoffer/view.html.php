<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.upload.js');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/joomlatabs.css');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/chosen.jquery.min.js');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/chosen.css');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/dropzone/dropzone.js');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/dropzone/jbusinessImageUploader.js');

JHTML::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/dropzone.css');
JHTML::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/basic.css');

JHTML::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/jquery.timepicker.css');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.timepicker.min.js');

if(!defined('J_JQUERY_UI_LOADED')) {
	JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/jquery-ui.css');
	JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery-ui.js');

	define('J_JQUERY_UI_LOADED', 1);
}

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/font-awesome.css');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/admin_offers.js');

// following translations will be used in js
JText::script('LNG_VIDEO');
JText::script('LNG_MISSING_OFFER_COMPANY');
JText::script('LNG_MISSING_DELETED_COMPANY');
JText::script('LNG_IMAGE_SIZE_WARNING');

JBusinessUtil::includeValidation();

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

class JBusinessDirectoryViewManageCompanyOffer extends JViewLegacy
{
	function __construct()
	{
		parent::__construct();
	}
	
	function display($tpl = null)
	{
		$this->companies = $this->get('UserCompanies');
		$this->currencies = $this->get('Currencies');
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');
		$this->states = JBusinessDirectoryHelper::getStatuses();
		
		$this->translations = JBusinessDirectoryTranslations::getAllTranslations(OFFER_DESCRIPTION_TRANSLATION,$this->item->id);
		$this->languages = JBusinessUtil::getLanguages();
		$this->actions = JBusinessDirectoryHelper::getActions();
		
		//check if user has access to offer
		$user = JFactory::getUser();
		$found = false;
		foreach($this->companies as $company){
		    if($company->userId == $user->id && $this->item->companyId == $company->id || $this->item->user_id == $user->id){
				$found = true;
			}
		}
		
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_OFFER);

		$lang = JFactory::getLanguage()->getTag();
		$key="";
		if(!empty($this->appSettings->google_map_key))
			$key="&key=".$this->appSettings->google_map_key;
		JHtml::_('script', "https://maps.googleapis.com/maps/api/js?libraries=places&language=".$lang.$key);
		
		//redirect if the user has no access and the event is not new
		if(!$found &&  $this->item->id !=0){
			$msg = JText::_("LNG_ACCESS_RESTRICTED");
			$app =JFactory::getApplication();
			$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffers', $msg));
		}
		parent::display($tpl);
	}
	
}
?>
