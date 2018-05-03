<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');



class JBusinessDirectoryControllerEvent extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Gets all the data about the tickets that are being booked 
	 * On success, it redirects to the event guest details page for the user to input further details for the booking.
	 */
	function reserveTickets(){
				
		$eventModel = $this->getModel("Event");
		$result = $eventModel->reserveTickets();
		$eventId = JRequest::getVar('eventId');
		
		if($result){
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=eventguestdetails&layout=edit', false));
		}else{
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=event&eventId='.$eventId, false));
		}
			
	}

	function checkEventsAboutToExpire(){
		$model = $this->getModel('event');
		$model->checkEventsAboutToExpire();
	}

    function deleteExpiredEvents(){
        $model = $this->getModel('event');
        $model->deleteExpiredEvents();
    }

    function contactCompany(){
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $eventmodel = $this->getModel('event');
        $data = JRequest::get( 'post' );

        $eventId = JRequest::getVar('event_Id');

        $contactId = JRequest::getVar('contactId');

        $event = $eventmodel->getPlainEvent($eventId);
        $captchaAnswer = !empty($data['recaptcha_response_field'])?$data['recaptcha_response_field']:$data['g-recaptcha-response'];

        if($appSettings->captcha){
            $namespace="jbusinessdirectory.contact";
            $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
            if(!$captcha->checkAnswer($captchaAnswer)){
                $error = $captcha->getError();
                $this->setMessage("Captcha error!", 'warning');
                $this->setRedirect(JBusinessUtil::getEventLink($eventId, $event->alias));
                return;
            }
        }

        $data['description'] = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $data['contact_id'] = $contactId;
        $data['event_name'] = $event->name;


        $result = $eventmodel->contactEventCompany($data);

        if($result){
            $this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_EVENT_CONTACTED'));
        }else{
            $this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED'));
        }

        $this->setRedirect(JBusinessUtil::getEventLink($eventId, $event->alias));
    }

    function associateCompaniesAjax() {
        $companyIds = JRequest::getVar('companyIds');
        $eventId = JRequest::getVar('eventId');

        $model = $this->getModel('Event');
        $result = $model->associateCompaniesAjax($eventId, $companyIds);

        /* Send as JSON */
        header("Content-Type: application/json", true);
        echo json_encode($result);
        exit;
    }
}