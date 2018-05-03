<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JBusinessDirectoryModelBusinessUser extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Populate state
	 * @param unknown_type $ordering
	 * @param unknown_type $direction
	 */
	protected function populateState($ordering = null, $direction = null){
		$app = JFactory::getApplication('administrator');	
	}
	
	function addJoomlaUser($details){
	
		// "generate" a new JUser Object
		$user = JFactory::getUser(0); // it's important to set the "0" otherwise your admin user information will be loaded

		// Get the applicaiton settings
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

		jimport('joomla.application.component.helper');
		$usersParams = JComponentHelper::getParams( 'com_users' ); // load the Params
			
		$userdata = array(); // place user data in an array for storing.
		$userdata['name'] = $details["name"];
		$userdata['email'] = $details["email"];
		$userdata['username'] = $details["username"];
	
		//set password
		$userdata['password'] =$details["password"];
		$userdata['password2'] = $details["passwordc"];
			
		//set default group.
		$usertype = $appSettings->usergroup;
		if (!$usertype)
		{
			$usertype = 2;  // 'Registered' ID in usergroup table is 2
		}
			
		//default to defaultUserGroup i.e.,Registered
		$userdata['groups']=array($usertype);
		$useractivation = $usersParams->get('useractivation'); 					// in this example, we load the config-setting
		$sendpassword = $usersParams->get('sendpassword', 1);
		
		$userdata['block'] = 0; // don't block the user
		
		// Check if the user needs to activate their account.
		if (($useractivation == 1) || ($useractivation == 2))
		{
		    $userdata['activation'] = JApplicationHelper::getHash(JUserHelper::genRandomPassword());
		    $userdata['block'] = 1;
		}
		
		//now to add the new user to the dtabase.
		if (!$user->bind($userdata)) {
			JError::raiseWarning('', JText::_( $user->getError())); // something went wrong!!
		}
		if (!$user->save()) {
			// now check if the new user is saved
			JError::raiseWarning('', JText::_( $user->getError())); // something went wrong!!
			return false;
		}
		
		
		if($useractivation == 1){
		    
		    // Compile the notification mail values.
		    $data = $user->getProperties();
		    $config = JFactory::getConfig();
		    $data['fromname'] = $config->get('fromname');
		    $data['mailfrom'] = $config->get('mailfrom');
		    $data['sitename'] = $config->get('sitename');
		    $data['siteurl'] = JUri::root();
		    
		    // Set the link to activate the user account.
		    $uri = JUri::getInstance();
		    $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		    $data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);
		    
		    // Remove administrator/ from activate URL in case this method is called from admin
		    if (JFactory::getApplication()->isClient('administrator'))
		    {
		        $adminPos         = strrpos($data['activate'], 'administrator/');
		        $data['activate'] = substr_replace($data['activate'], '', $adminPos, 14);
		    }
		    
		    $emailSubject = JText::sprintf(
		        'COM_USERS_EMAIL_ACCOUNT_DETAILS',
		        $data['name'],
		        $data['sitename']
		        );
		    $emailBody ="";
		    
		    if ($sendpassword)
		    {
		        $emailBody = JText::sprintf(
		            'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
		            $data['name'],
		            $data['sitename'],
		            $data['activate'],
		            $data['siteurl'],
		            $data['username'],
		            $data['password_clear']
		            );
		    }
		    else
		    {
		        $emailBody = JText::sprintf(
		            'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW',
		            $data['name'],
		            $data['sitename'],
		            $data['activate'],
		            $data['siteurl'],
		            $data['username']
		            );
		    }
		    // Send the registration email.
		    $result = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
		}
     
		return $user->id;
	}
	
	function loginUser(){
	    $app = JFactory::getApplication();
		
		$input  = $app->input;
		$method = $input->getMethod();
		
		$data['username']  = $input->$method->get('username', '', 'USERNAME');
		$data['password']  = $input->$method->get('password', '', 'RAW');
		$data['secretkey'] = $input->$method->get('secretkey', '', 'RAW');
		
		// Get the log in credentials.
		$credentials = array();
		$credentials['username']  = $data['username'];
		$credentials['password']  = $data['password'];
		$credentials['secretkey'] = $data['secretkey'];
		
		$options=array();
		try{
		  $result = $app->login($credentials, $options);
		}catch(Exception $e){
		    return false;
		}
		
		return $result;
	}
}

?>