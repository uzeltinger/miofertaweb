<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryControllerBusinessUser extends JControllerLegacy
{
	
	function __construct()
	{
		parent::__construct();
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	}

	function checkUser(){
		
		$user = JFactory::getUser();
		$filterParam = "";
		$filter_package = JFactory::getApplication()->input->get("filter_package");
		
		if(!empty($filter_package)){
			$filterParam ="&filter_package=".$filter_package;
		}
		
		if($user->id == 0 && $this->appSettings->allow_user_creation==0){
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=businessuser'.$filterParam, false));
		}else{
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&showSteps=true&layout=edit'.$filterParam, false));
		}
		
		return;
	}
	
	function loginUser(){
		$model = $this->getModel("businessuser");
		
		$filterParam = "";
		$filter_package = JFactory::getApplication()->input->get("filter_package");
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$post = JRequest::get('post');
		
		if(!empty($filter_package)){
			$filterParam ="&filter_package=".$filter_package;
		}
		
		if(true !== $model->loginUser()){
		    $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&showOnlyLogin=1&view=businessuser'.$filterParam, false));
		}else{
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&showSteps=true&layout=edit'.$filterParam, false));
		}
	}
	
	function addUser(){
		$model = $this->getModel("businessuser");
		$filterParam = "";
		$filter_package = JFactory::getApplication()->input->get("filter_package");
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$post = JRequest::get('post');
		if($appSettings->captcha){
			$namespace="jbusinessdirectory.contact";
			$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
			if(!$captcha->checkAnswer($captchaAnswer)){
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=businessuser'.$filterParam, false));
				return;
			}
		}
		
		if(!empty($filter_package)){
			$filterParam ="&filter_package=".$filter_package;
		}

		$data = JRequest::get('post');
		
		if(!$model->addJoomlaUser($data)){
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=businessuser'.$filterParam, false));
		}else{
		    $params = JComponentHelper::getParams('com_users');
		    $useractivation = $params->get('useractivation');
		    if ($useractivation == 1){
		        $this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
		        $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&showOnlyLogin=1&view=businessuser'.$filterParam, false));
		    }else{
		        if(true !== $model->loginUser()){
		            $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&showOnlyLogin=1&view=businessuser'.$filterParam, false));
		        }else{
		            $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&showSteps=true&layout=edit'.$filterParam, false));
		        }
		    }
		}
	}
}