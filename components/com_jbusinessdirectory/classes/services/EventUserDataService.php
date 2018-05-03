<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

class EventUserDataService{

	/**
	 *  Create user data for storing booking details
	 * @param $data
	 * @param $userData
	 * @return stdClass
	 */
	public static function createUserData($data, $userData){
		if(empty($userData))
			$userData = new stdClass();
		
		if(!isset($userData->first_name))
			$userData->first_name = '';
		if(!isset($userData->last_name))
			$userData->last_name = '';
		if(!isset($userData->address))
			$userData->address	= '';
		if(!isset($userData->city))
			$userData->city	= '';
		if(!isset($userData->state_name))
			$userData->state_name	= '';
		if(!isset($userData->country))
			$userData->country	= '';
		if(!isset($userData->postal_code))
			$userData->postal_code= '';
		if(!isset($userData->phone))
			$userData->phone = '';
		if(!isset($userData->email))
			$userData->email= '';
		if(!isset($userData->conf_email))
			$userData->conf_email = '';
		
		return $userData;
	}
	
	
	/**
	 * Initialiaze user data
	 */
	public static function initializeUserData($resetUserData = false){
	
		$get = JRequest::get( 'get' );
		$post = JRequest::get( 'post' );
		if(count($post)==0)
			$post = $get;
	
		$userData =  isset($_SESSION['userData'])?$_SESSION['userData']:null;
		if(!isset($userData) || $resetUserData){
			$userData = self::createUserData($post,$userData);
			$_SESSION['userData'] = $userData;
		}
	
		if(!isset($userData->guestDetails)){
			$guestDtls = new stdClass();
			$guestDtls->first_name = "";
			$guestDtls->last_name = "";
			$guestDtls->address	= "";
			$guestDtls->city	= "";
			$guestDtls->county	= "";
			$guestDtls->country_name	= "";
			$guestDtls->postalCode= "";
			$guestDtls->phone = "";
			$guestDtls->email="";
			$userData->guestDetails = $guestDtls;
		}
		
		$_SESSION['userData'] = $userData;
		return $userData;
	}
	
    /**
     * Get user data object created from session data
     * @return mixed|null|stdClass
     */
	public static function getUserData(){
		
		$session = self::getJoomlaSession();
		$userData =  isset($_SESSION['userData'])?$_SESSION['userData']:null;
		if(!isset($userData)){
			$userData = self::initializeUserData();
			$_SESSION['userData'] = $userData;
		}
		if(empty($userData->eventId)){
			$userData->eventId = 0;
			$_SESSION['userData'] = $userData;
		}
		
		return $userData;
	}

    /**
     * @param $hotelId
     * @param $reservedItem data of reserved items so far
     * @param $current identification for rooms that have the same current are considered as 1
     * @return string returns reserved Items by user on reservation steps
     */
	public static function reserveTickests($eventId, $reservedItems){
	
 		$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
 		$log->LogDebug("reserve event eventId= $eventId, reservedItems = ".serialize($reservedItems));
		
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];

		$userData->reservedItems = $reservedItems;
		$userData->eventId = $eventId;
		$log->LogDebug("Reserved items: ".serialize($userData->reservedItems));
		
		$_SESSION['userData'] = $userData;
		return true;
	}
	
	/**
	 * @param $guestDetails
	 */
	public static function addGuestDetails($guestDetails){
		$userData =  $_SESSION['userData'];
		$guestDtls = new stdClass();
		$guestDtls->first_name = ucfirst($guestDetails["first_name"]);
		$guestDtls->last_name = ucfirst($guestDetails["last_name"]);
		$guestDtls->address	= ucfirst($guestDetails["address"]);
		$guestDtls->city	= $guestDetails["city"];
		$guestDtls->county	= $guestDetails["region"];
		$guestDtls->country_name	= $guestDetails["country"];
		$guestDtls->postalCode= strtoupper($guestDetails["postal_code"]);
		$guestDtls->phone = $guestDetails["phone"];
		$guestDtls->email= $guestDetails["email"];
		
		$userData->guestDetails = $guestDtls;
		
		$_SESSION['userData'] = $userData;
	}
	
	/**
	 * 
	 * 
	 * @param unknown_type $currencyName
	 * @param unknown_type $currencySymbol
	 */
	public static function setCurrency($currencyName, $currencySymbol){
		$currency = new stdClass();
		$currency->name = $currencyName;
		$currency->symbol = $currencySymbol;
	
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		$userData->currency = $currency;
	
		if($userData->user_currency=="")
			$userData->user_currency = $currency->name;
		$_SESSION['userData'] = $userData;
	}
	
	private static function getJoomlaSession(){
		$session = JFactory::getSession();
		if ($session->getState() !== 'active') {
			$app = JFactory::getApplication();
			$msg = "Your session has expired";
			$app->redirect( 'index.php?option='.JBusinessUtil::getComponentName().'&view=events', $msg );
			$app->enqueueMessage("Your session has expired", 'warning');
		}
		else
			return $session;
	}
}