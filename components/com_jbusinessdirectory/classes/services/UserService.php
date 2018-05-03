<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');


class UserService{

	/**
	 * Add new user if it does not existings on database
	 * @param unknown_type $data
	 * @return unknown
	 */
	public static function addUser($data){
		$user = JFactory::getUser();
		if(!$user->id || $user->guest==1){
			$userObj = self::getUserByEmail($data["email"]);
			if(isset($userObj->id))
				$userId = $userObj->id;
			else
				$userId = self::addJoomlaUser($data);
		}
		else
			$userId = $user->id;
		 
		return $userId;
	}
	 
	/**
	 * Get user by its email address
	 *
	 * @param $email
	 * @return mixed
	 */
	public static function getUserByEmail($email){
		$db		= JFactory::getDBO();
		$query = " SELECT * FROM #__users WHERE username = 	'".trim($email)."' OR email = '".trim($email)."'";
		$db->setQuery( $query );
		$user =  $db->loadObject();

		return $user;
	}

	/**
	 * Returns the super user
	 *
	 * @param $userId
	 * @return mixed
	 */
	public static function isSuperUser($userId){
		$user	= JFactory::getUser();
		$isroot	= $user->get('isRoot');
		return $isroot;
	}

	/**
	 * Generate a random password.
	 *
	 * @param $text
	 * @param bool $is_cripted
	 * @return mixed
	 */
	public static function generatePassword($text, $is_cripted = false)
	{
		$password 	=  $text;
		if( $is_cripted ==false )
			return $password;
		jimport('joomla.user.helper');
		$salt 		= JUserHelper::genRandomPassword(8);
		$crypt 		= JUserHelper::getCryptedPassword($password, $salt);
		$password 	= $salt;
		return $password;
	}

	/**
	 * Add user details and create a Joomla User
	 *
	 * @param $data
	 * @return mixed
	 */
	public static function addJoomlaUser($data){

		//prepare user object
		$userdata = array(); // place user data in an array for storing.
		$name = explode('@',$data['email']);
		$userdata['name']  = $name[0];
		$userdata['email'] = $data["email"];
		$userdata['username'] = $data["email"];

		//set password
		$userdata['password'] = UserService::generatePassword( $data["email"], true );
		$userdata['password2'] = $userdata['password'];

		//create the user
		$userId = UserService::createJoomlaUser($userdata);

		if(!is_numeric($userId))
			JError::raiseWarning('', JText::_($userId)); // something went wrong!!

		return $userId;
	}

	/**
	 *   Get any component's model
	 **/
	public static function getModel($name, $path = JPATH_COMPONENT_ADMINISTRATOR, $component = 'yourcomponentname')
	{
		// load some joomla helpers
		JLoader::import('joomla.application.component.model');
		// load the model file
		JLoader::import( $name, $path . '/models' );
		// return instance
		return JModelLegacy::getInstance( $name, $component.'Model' );
	}

	/**
	 * Greate user and update given table
	 */
	public static function createJoomlaUser($new)
	{
		// load the user registration model
		$model = self::getModel('registration', JPATH_ROOT. '/components/com_users', 'Users');
		if (JLanguageMultilang::isEnabled())
		{
			JForm::addFormPath(JPATH_ROOT . '/components/com_users/models/forms');
			JForm::addFieldPath(JPATH_ROOT . '/components/com_users/models/fields');
		}
		// lineup new user data
		$data = array(
				'username' => $new['username'],
				'name' => $new['name'],
				'email1' => $new['email'],
				'password1' => $new['password'], // First password field
				'password2' => $new['password2'], // Confirm password field
				'block' => 0 );
		// register the new user
		$userId = $model->register($data);

		// if user is created
		if ($userId > 0)
		{
			return $userId;
		}
		return $model->getError();
	}
}
?>