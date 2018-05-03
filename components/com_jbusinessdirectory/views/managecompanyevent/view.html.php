<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.upload.js');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/joomlatabs.css');

JHtml::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/jquery.timepicker.css');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.timepicker.min.js');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/chosen.jquery.min.js');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/chosen.css');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/dropzone/dropzone.js');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/dropzone/jbusinessImageUploader.js');
JHtml::_('script', 	'components/com_jbusinessdirectory/assets/js/ajax-chosen.min.js');

JHtml::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/dropzone.css');
JHtml::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/basic.css');

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/jquery-ui.css');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery-ui.js');

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/font-awesome.css');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/admin_events.js');

// following translations will be used in js
JText::script('LNG_VIDEO');
JText::script('LNG_MISSING_EVENT_COMPANY');
JText::script('LNG_MISSING_DELETED_COMPANY');
JText::script('LNG_IMAGE_SIZE_WARNING');


JBusinessUtil::includeValidation();

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

class JBusinessDirectoryViewManageCompanyEvent extends JViewLegacy
{
	function __construct()
	{
		parent::__construct();
	}
	
	function display($tpl = null)
	{
		$this->companies = $this->get('UserCompanies');
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');
		$this->states = JBusinessDirectoryHelper::getStatuses();

		$this->translations = JBusinessDirectoryTranslations::getAllTranslations(EVENT_DESCRIPTION_TRANSLATION,$this->item->id);
		$this->languages = JBusinessUtil::getLanguages();
		$this->actions = JBusinessDirectoryHelper::getActions();
		
		//check if user has access to offer
		$user = JFactory::getUser();
		$found = false;
		foreach($this->companies as $company){
		    if($company->userId == $user->id && $this->item->company_id == $company->id || $this->item->user_id == $user->id){
				$found = true;
			}
		}
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_EVENT);
		$this->currencies = $this->get('Currencies');
		
		
		$lang = JFactory::getLanguage()->getTag();
		$key="";
		if(!empty($this->appSettings->google_map_key))
			$key="&key=".$this->appSettings->google_map_key;
		JHtml::_('script', "https://maps.googleapis.com/maps/api/js?libraries=places&language=".$lang.$key);
		
		//redirect if the user has no access and the event is not new
		if(!$found &&  $this->item->id !=0){
			$msg = JText::_("LNG_ACCESS_RESTRICTED");
			$app =JFactory::getApplication();
			$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyevents', $msg));
		}
		
		parent::display($tpl);
	}
}
?>
