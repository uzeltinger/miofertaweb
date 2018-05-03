<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.opacityrollover.js');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/chosen.jquery.min.js');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/chosen.css');

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/magnific-popup.css');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.magnific-popup.min.js');

if(!defined('J_JQUERY_UI_LOADED')) {
	JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/jquery-ui.css');
	JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery-ui.js');

	define('J_JQUERY_UI_LOADED', 1);
}

JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/map.js');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/events.js');

JBusinessUtil::includeValidation();
class JBusinessDirectoryViewEvent extends JViewLegacy {

	function __construct()
	{
		parent::__construct();
	}
	
	function display($tpl = null){
		$this->appSettings =  JBusinessUtil::getInstance()->getApplicationSettings();
		$this->defaultAttributes = JBusinessUtil::getAttributeConfiguration();
		
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, REVIEWS_TERMS_CONDITIONS_TRANSLATION);
		}
		
		$eventId= JRequest::getVar('eventId');
		$this->assignRef('eventId', $eventId);
		if($this->appSettings->enable_event_reservation){
			$this->eventTickets = $this->get('EventTickets');
		}
		$this->event = $this->get('Event');
        $this->eventAttributes = $this->get('EventAttributes');
		$this->associatedCompanies = $this->get('AssociatedCompanies');
		$this->userCompanies = $this->get('CompaniesByUserId');
		$this->userAssociatedCompanies = $this->get('UserAssociatedCompanies');
		$this->videos = $this->get('EventVideos');
		if ($this->appSettings->apply_attr_events) {
			$this->videos = $this->defaultAttributes['video'] != ATTRIBUTE_NOT_SHOW? $this->videos : array();
		}
		$session = JFactory::getSession();
		$this->location = $session->get('location');

		if(empty($this->event) || ($this->event->state == EVENT_CREATED && !$this->appSettings->show_pending_approval)){
			$tpl="inactive";
		}
		
		parent::display($tpl);
	}
}
?>
