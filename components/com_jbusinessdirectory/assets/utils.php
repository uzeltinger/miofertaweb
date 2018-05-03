<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

if (!function_exists('dump')) {
	function dump()
	{
		$args = func_get_args();

		echo '<pre>';

		foreach ($args as $arg) {
			var_dump($arg);
		}
		echo '</pre>';
		//exit;
	}
}
if (!function_exists('dbg')) {
	function dbg( $text )
	{
		echo "<pre>";
		var_dump($text);
		echo "</pre>";
	}
}

class JBusinessUtil{

	var $applicationSettings ;

	private function __construct()
	{

	}

	public static function getInstance()
	{
		static $instance;
		if ($instance === null) {
			$instance = new JBusinessUtil();
		}
		return $instance;
	}

	public static function getApplicationSettings(){
		$instance = JBusinessUtil::getInstance();

		if(!isset($instance->applicationSettings)){
			$instance->applicationSettings = self::getAppSettings();
		}
		return $instance->applicationSettings;
	}
	
	private static function getAppSettings(){
		$db		= JFactory::getDBO();
		$query	= "	SELECT fas.*, df.*, c.currency_name, c.currency_id FROM #__jbusinessdirectory_applicationsettings fas
					inner join  #__jbusinessdirectory_date_formats df on fas.date_format_id=df.id
					inner join  #__jbusinessdirectory_currencies c on fas.currency_id=c.currency_id";
	
		//dump($query);
		$db->setQuery( $query );
		if (!$db->query() )
		{
			JError::raiseWarning( 500, JText::_("LNG_UNKNOWN_ERROR") );
			return true;
		}
		return  $db->loadObject();
	}

	public static function loadClasses(){
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		//load payment processors
		$classpath = JPATH_COMPONENT_SITE  .DS.'classes'.DS.'payment'.DS.'processors';
		foreach( JFolder::files($classpath) as $file ) {
			JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
		}

		//load payment processors
		$classpath = JPATH_COMPONENT_SITE  .DS.'classes'.DS.'payment';
		foreach( JFolder::files($classpath) as $file ) {
			JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
		}

		//load services
		$classpath = JPATH_COMPONENT_SITE  .DS.'classes'.DS.'services';
		foreach( JFolder::files($classpath) as $file ) {
			JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
		}
	}


	public static function getURLData($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	public static function getCoordinates($zipCode) {

		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

		$limitCountries = array();
		$location = null;

		if(!empty($appSettings->country_ids)) {
			$countryIDs = explode(",", $appSettings->country_ids);

			JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
			$countryTable = JTable::getInstance("Country", "JTable");

			foreach ($countryIDs as $countryID) {
				$country = $countryTable->getCountry($countryID);
				array_push($limitCountries, $country->country_code);
			}
		}
		
		$key="";
		if(!empty($appSettings->google_map_key)){
			$key="&key=".$appSettings->google_map_key;
			if(!empty($appSettings->google_map_key_zipcode)){
				$key="&key=".$appSettings->google_map_key_zipcode;
			}
		}
		
		$countryParam = "";
		if(!empty($limitCountries)){
		    $countries = array();
		    foreach($limitCountries as $country){
		        $countries[]="country:".$country;
		    }
		   
		    $countries = implode("|",$countries);
			$countryParam ="&components=".$countries;
			
		}
		
		$url ="https://maps.googleapis.com/maps/api/geocode/json?sensor=false$key$countryParam&address=".urlencode($zipCode);
		$data = self::getURLData($url);
		$search_data = json_decode($data);
		if(!empty($search_data) && !empty($search_data->results)){
			$lat =  $search_data->results[0]->geometry->location->lat;
			$lng =  $search_data->results[0]->geometry->location->lng;
			
			if(!empty($limitCountries)){
				foreach($search_data->results as $result){
					$country = "";
					foreach($result->address_components as $addressCmp){
						if(!empty($addressCmp->types) && $addressCmp->types[0]=="country"){
							$country = $addressCmp->short_name;
						}
					}
					if(in_array($country, $limitCountries)){
						$lat =  $result->geometry->location->lat;
						$lng =  $result->geometry->location->lng;
					}
				}
			}
		
			$location =  array();
			$location["latitude"] = $lat;
			$location["longitude"] = $lng;
		}
		
		return $location;
	}


	public static function parseDays($days){
		$date1 = time();
		$date2 = strtotime("+$days day");

		$diff = abs($date2 - $date1);

		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			
		$result = new stdClass();

		$result->days = $days;
		$result->months = $months;
		$result->years = $years;

		return $result;
	}

	
	static function getComponentName(){
		$componentname = JRequest::getVar('option');
		return $componentname;
	}
	
	static function makePathFile($path){
		$path_tmp = str_replace( '\\', DIRECTORY_SEPARATOR, $path );
		$path_tmp = str_replace( '/', DIRECTORY_SEPARATOR, $path_tmp);
		return $path_tmp;
	}
	
	static function convertTimeToMysqlFormat($time){
		if(empty($time))
			return null;
        if($time == '12:00 AM')
            return '24:00:00';
		$strtotime = strtotime($time);
		$time = date('H:i:s',$strtotime);
		return $time;
	}
	
	static function convertTimeToFormat($time){
		if(empty($time))
			return null;
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$strtotime = strtotime($time);
		$time = date($appSettings->time_format,$strtotime);
		return $time;
	}
	
	static function convertToFormat($date){
		if(isset($date) && strlen($date)>6 && $date!="0000-00-00" && $date!="00-00-0000"){
			try{
				$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
				$date = substr($date,0,10);
				list($yy,$mm,$dd)=explode("-",$date);
				if (is_numeric($yy) && is_numeric($mm) && is_numeric($dd)){
					$date = date($appSettings->dateFormat, strtotime($date));
				}else{
					$date="";
				}
			}catch(Exception $e){
				$date="";
			}
		}
		return $date;
	}
	
	static function convertToMysqlFormat($date){
		if(isset($date) && strlen($date)>6){
			$date = date("Y-m-d", strtotime($date));
		}
		return $date;
	}
	
	static function getDateGeneralFormat($data){
		$dateS="";
		if(isset($data) && strlen($data)>6  && $data!="0000-00-00"){
			//$data =strtotime($data);
			//setlocale(LC_ALL, 'de_DE');
			//$dateS = strftime( '%e %B %Y', $data );
			$date = JFactory::getDate($data);
			$dateS = $date->format('j F Y');
			//$dateS = date( 'j F Y', $data );
		}
	
		return $dateS;
	}
	
	static function getDateGeneralShortFormat($data){
		$dateS="";
		if(isset($data) && strlen($data)>6  && $data!="0000-00-00"){
			//$data =strtotime($data);
			//$dateS = strftime( '%e %b %Y', $data );
			//$dateS = date( 'j M Y', $data );
			$date = JFactory::getDate($data);
			$dateS = $date->format('j M Y');
		}
	
		return $dateS;
	}
	
	static function getDateGeneralFormatWithTime($data){
		if(empty($data)){
			return null;
		}
		$data =strtotime($data);
		$dateS = date( 'j M Y  G:i:s', $data );
	
		return $dateS;
	}
	
	static function getShortDate($data){
		if(empty($data)){
			return null;
		}
		
		$date = JFactory::getDate($data);
		$dateS = $date->format('M j');
	
		return $dateS;
	}
	
	static function getTimeText($time){
		$result = date('g:iA', strtotime($time));
		
		return $result;
	}
	
	static function getRemainingTime($date){
		$now = new DateTime();
		$future_date = new DateTime($date);
		$timestamp = strtotime($date);
		$timestamp = strtotime('+1 day', $timestamp);
		if($timestamp  < time()){
			return "";
		}
		
		$interval = $future_date->diff($now);
		$result = JText::_("LNG_ENDS_IN");
		
		if($interval->format("%a")){
			$result .= " ".$interval->format("%a")." ".strtolower(JText::_("LNG_DAYS"));
		}
		
		if($interval->format("%h")){
			$result .= " ".$interval->format("%h")." ".strtolower(JText::_("LNG_HOURS"));
		}
		
		if($interval->format("%m")){
			$result .= " ".$interval->format("%m")." ".strtolower(JText::_("LNG_MINUTES"));
		}
		
		return $result;
	}
	
	static function loadModules($position){
		require_once(JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'application'.DS.'module'.DS.'helper.php');
		$document = JFactory::getDocument();
		$renderer = $document->loadRenderer('module');
		$db =JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__modules WHERE position='$position' AND published=1 ORDER BY ordering");
		$modules = $db->loadObjectList();
		if( count( $modules ) > 0 )
		{
			foreach( $modules as $module )
			{
				//just to get rid of that stupid php warning
				$module->user = '';
				$params = array('style'=>'xhtml');
				echo $renderer->render($module, $params);
			}
		}
	}
	
	/**
	 * Get company details
	 * @param int $companyId
	 */
	public static function getCompany($companyId){
		if(empty($companyId)){
			return null;
		}
		
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$companiesTable = JTable::getInstance("Company", "JTable");
		$company = $companiesTable->getCompany($companyId);
		
		return $company;
	}

	
	/**
	 * Get event details
	 * @param int $eventId
	 */
	public static function getEvent($eventId){
		if(empty($eventId)){
			return null;
		}
	
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$eventTable = JTable::getInstance("Event", "JTable");
		$event = $eventTable->getEvent($eventId);
	
		return $event;
	}
	
	/**
	 * Get offer details
	 * @param int $offerId
	 */
	public static function getOffer($offerId){
		if(empty($offerId)){
			return null;
		}
	
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$offerTable = JTable::getInstance("Offer", "JTable");
		$offer = $offerTable->getOffer($offerId);
	
		return $offer;
	}
	
	public static function getPackage($packageId){
		$packageTable = JTable::getInstance("Package", "JTable");
		$package = $packageTable->getPackage($packageId);
		
		$package->features = explode(",", $package->featuresS);
		$package->features[]= "multiple_categories";
		
		if(self::getInstance()->getApplicationSettings()->enable_multilingual){
			JBusinessDirectoryTranslations::updateEntityTranslation($package, PACKAGE_TRANSLATION);
		}
		
		return $package;
	}
	
	
	/**
	 * Get custom attributes and filter them based on package features
	 * @return array
	 */
	public static function getPackagesAttributes($packages){
		$attributesTable = JTable::getInstance('Attribute','JTable');
		$attributes = $attributesTable->getActiveAttributes();
	
		if(!is_array($packages)){
			$packages = array($packages);
		}
		
		$result = array();
		//check if the attribues are contained in at least one package. If not it will be removed.
		foreach($attributes as $attribute){
			$found = false;
			foreach($packages as $package){
				foreach($package->features as $feature){
					if($feature == $attribute->code){
						$found = true;
					}
				}
			}
				
			if($found){
				$result[] = $attribute;
			}
		}
	
		if(self::getInstance()->getApplicationSettings()->enable_multilingual){
			JBusinessDirectoryTranslations::updateAttributesTranslation($result);
		}
	
		return $result;
	}
	
	
	static function getItemIdS(){
		$app = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		$menu = $app->getMenu();
		$itemid="";
		
		$activeMenu = JFactory::getApplication()->getMenu()->getActive();
		if(isset($activeMenu)){
			$itemid= JFactory::getApplication()->getMenu()->getActive()->id;
		}
		
		$defaultMenu = $menu->getDefault($lang->getTag());
		if(!empty($defaultMenu) && $itemid == $defaultMenu->id){
			$itemid	= "";
		}
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		if(!empty($appSettings->menu_item_id) && empty($itemid)){
			$itemid = $appSettings->menu_item_id;
		}
		
		$itemidS="";
		if(!empty($itemid)){
			$itemidS = '&Itemid='.$itemid;
		}
		
		return $itemidS;
	}
	
	/**
	 * Get the current menu alias
	 */
	static function getCurrentMenuAlias(){
		$menualias =  "";
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		return $appSettings->url_menu_alias;
		/*
		$currentMenu = null;
		if(!empty($appSettings->menu_item_id)){
			$currentMenu = JFactory::getApplication()->getMenu()->getItem($appSettings->menu_item_id);
		}
		
		if(empty($currentMenu)){
			$currentMenu = JFactory::getApplication()->getMenu()->getActive();
		}
		
		if(!empty($currentMenu))
			$menualias = $currentMenu->alias;*/
		
		return $menualias;
	}
	
	/**
	 * Prevent the links to contain administrator keyword
	 * 
	 * @param unknown_type $url
	 */
	static function processURL($url){
		if(strpos($url, "/administrator/")!==false){
			$url = str_replace("administrator/", "", $url);
		}
		
		return $url;
	}
	
	/**
	 * Creates the business listing link
	 * 
	 * @param $company
	 * @param $addIndex
	 */
	static function getCompanyLink($company, $addIndex=null){
		$itemidS = self::getItemIdS();
	
		$companyAlias = trim($company->alias);
		$companyAlias = stripslashes(strtolower($companyAlias));
		$companyAlias = str_replace(" ", "-", $companyAlias);
	
		$conf = JFactory::getConfig();
		$index ="";
	
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
	
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		if(!$appSettings->enable_seo){
			$companyLink = $company->id;
			if(JFactory::getConfig()->get("sef")){
				$companyLink = $company->id;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=companies&companyId='.$companyLink.$itemidS,false,-1);
		}else{
			if($appSettings->add_url_id == 1){
				$companyLink = $company->id."-".htmlentities(urlencode($companyAlias));
			}else{
				$companyLink = htmlentities(urlencode($companyAlias));
			}
			
			$company->county = JApplication::stringURLSafe($company->county);
			$company->city = JApplication::stringURLSafe($company->city);
			$company->province = JApplication::stringURLSafe($company->province);
			
			if($appSettings->listing_url_type==2){
				$categoryPath = self::getBusinessCategoryPath($company);
				$path="";
				foreach($categoryPath as $cp){
					$path = $path. JApplication::stringURLSafe($cp->name)."/";
				}
				$companyLink=strtolower($path).$companyLink;
			}else if($appSettings->listing_url_type==3){
			  
				$companyLink= strtolower($company->county)."/".strtolower($company->city)."/".$companyLink;
			}else if($appSettings->listing_url_type==4){
			    $categoryPath = self::getBusinessCategoryPath($company);
			    $path="";
			    foreach($categoryPath as $cp){
			        $path = $path. JApplication::stringURLSafe($cp->name)."/";
			    }
			    $companyLink= $path.strtolower($company->province)."/".$companyLink;
			}else if($appSettings->listing_url_type==5){
			    $categoryPath = self::getBusinessCategoryPath($company);
			    $path = "";
			    if(!empty($categoryPath) && isset($categoryPath[0]->name)){
			        $path=JApplication::stringURLSafe($categoryPath[0]->name);
			    }
			    $companyLink= $path."/".strtolower($company->province)."/".$companyLink;
			}else if($appSettings->listing_url_type==6){
			    $categoryPath = self::getBusinessCategoryPath($company);
			    $path=JApplication::stringURLSafe($categoryPath[0]->name);
			   $countryName = "";
			   if(isset($company->countryName)){
			       $countryName = $company->countryName;
			   }else if (isset($company->country_name)){
			       $countryName = $company->country_name;
			   }
			   
			   $countryName = JApplication::stringURLSafe($countryName);
			   $companyLink= $path."/".strtolower($countryName)."/".$companyLink;
			}

			$base = JURI::base().$index;
			if($appSettings->add_url_language){
				$langTag = self::getCurrentLanguageCode();
				$base.= $langTag."/";
			}
			
			$url = $base.$companyLink;
			
			$menuAlias = self::getCurrentMenuAlias();
			if($appSettings->enable_menu_alias_url && !empty($menuAlias)){
				$url = $base.$menuAlias."/".$companyLink;
			}
		}
		
		$url = self::processURL($url);
	
		return $url;
	}
	
	/**
	 * Create the business listing link only for type one (only name in the link)
	 * 
	 * @param $companyId
	 * @param $companyAlias
	 * @param $addIndex
	 * @return String $url
	 */
	static function getCompanyDefaultLink($companyId, $companyAlias, $addIndex=null){
		$itemidS = self::getItemIdS(); 
		
		$companyAlias = trim($companyAlias);
		$companyAlias = stripslashes(strtolower($companyAlias));
		$companyAlias = str_replace(" ", "-", $companyAlias);
	
		$conf = JFactory::getConfig();
		$index ="";
	
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
	
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		if(!$appSettings->enable_seo){
			$companyLink = $companyId;
			if(JFactory::getConfig()->get("sef")){
				$companyLink = $companyId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=companies&companyId='.$companyLink.$itemidS,false,-1);
		}else{
			if($appSettings->add_url_id == 1){ 
				$companyLink = $companyId."-".htmlentities(urlencode($companyAlias));
			}else{
				$companyLink = htmlentities(urlencode($companyAlias));
			}
			
			$base = JURI::base().$index;
			if($appSettings->add_url_language){
				$langTag = self::getCurrentLanguageCode();
				$base.= $langTag."/";
			}
			
			$url = $base.$companyLink;
			$menuAlias = self::getCurrentMenuAlias();
			if($appSettings->enable_menu_alias_url && !empty($menuAlias)){
				$url = $base.$menuAlias."/".$companyLink;
			}
		}
	
		$url = self::processURL($url);
		
		return $url;
	}
	
	/**
	 * Create the link for categories
	 * 
	 * @param unknown_type $categoryId
	 * @param unknown_type $categoryAlias
	 * @param unknown_type $addIndex
	 */
	static function getCategoryLink($categoryId, $categoryAlias, $addIndex=null){
		$itemidS = self::getItemIdS();
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$categoryAlias = trim($categoryAlias);
		$categoryAlias = stripslashes(strtolower($categoryAlias));
		$categoryAlias = str_replace(" ", "-", $categoryAlias);
	
		$conf = JFactory::getConfig();
		$index ="";
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
	
		$categoryLink = $categoryId;
		
		if(!$appSettings->enable_seo){
			$categoryLink = $categoryId;
			if(JFactory::getConfig()->get("sef")){
				$categoryLink = $categoryId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=search&categoryId='.$categoryLink.$itemidS,false,-1);
		}else{
			
			if($appSettings->add_url_id == 1){ 
				$categoryLink = $categoryId."-".htmlentities(urlencode($categoryAlias));
			}else{
				$categoryLink = htmlentities(urlencode($categoryAlias));
			}

			$base = JURI::base().$index;
			if($appSettings->add_url_language){
				$langTag = self::getCurrentLanguageCode();
				$base.= $langTag."/";
			}
			
			$menuAlias = self::getCurrentMenuAlias();
			if($appSettings->category_url_type==2){
				$url = $base.$categoryLink;
				if($appSettings->enable_menu_alias_url && !empty($menuAlias)){
					$url = $base.$menuAlias."/".$categoryLink;
				}
			}else{
				$url = $base.CATEGORY_URL_NAMING."/".$categoryLink;
				if($appSettings->enable_menu_alias_url && !empty($menuAlias)){
					$url = $base.$menuAlias."/".CATEGORY_URL_NAMING."/".$categoryLink;
				}
			}

		}
		
		$url = self::processURL($url);
		
		return $url;
	}
	
	/**
	 * Create the link for category offers
	 * 
	 * @param $categoryId
	 * @param $categoryAlias
	 * @param $addIndex
	 */
	static function getOfferCategoryLink($categoryId, $categoryAlias, $addIndex=null){
		$itemidS = self::getItemIdS();
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$categoryAlias = trim($categoryAlias);
		$categoryAlias = stripslashes(strtolower($categoryAlias));
		$categoryAlias = str_replace(" ", "-", $categoryAlias);
	
		$conf = JFactory::getConfig();
		$index ="";
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
	
		$offerCategoryLink = $categoryId;
		
		if(!$appSettings->enable_seo){
			$offerCategoryLink = $categoryId;
			if(JFactory::getConfig()->get("sef")){
				$categoryLink = $categoryId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=offers&offerCategoryId='.$offerCategoryLink.$itemidS,false,-1);
		}else{
			if($appSettings->add_url_id == 1){
				$offerCategoryLink = $categoryId."-".htmlentities(urlencode($categoryAlias));
			}else{
				$offerCategoryLink =htmlentities(urlencode($categoryAlias));
			}
			
			$base = JURI::base().$index;
			if($appSettings->add_url_language){
				$langTag = self::getCurrentLanguageCode();
				$base.= $langTag."/";
			}
			
			$url = $base.OFFER_CATEGORY_URL_NAMING."/".$offerCategoryLink;
			
			$menuAlias = self::getCurrentMenuAlias();
			if($appSettings->enable_menu_alias_url && !empty($menuAlias)){
				$url = $base.$menuAlias."/".OFFER_CATEGORY_URL_NAMING."/".$offerCategoryLink;
			}
		}
		
		$url = self::processURL($url);
		
		return $url;
	}
	
	/**
	 * Create the link for event categories
	 * 
	 * @param $categoryId
	 * @param $categoryAlias
	 * @param $addIndex
	 */
	static function getEventCategoryLink($categoryId, $categoryAlias, $addIndex=null){
		$app = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		$menu = $app->getMenu();
		$itemid="";
		$activeMenu = JFactory::getApplication()->getMenu()->getActive();
		if(isset($activeMenu)){
			$itemid= JFactory::getApplication()->getMenu()->getActive()->id;
		}
		
		if($itemid == $menu->getDefault($lang->getTag())->id){
			$itemid	= "";
		}
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$categoryAlias = trim($categoryAlias);
		$categoryAlias = stripslashes(strtolower($categoryAlias));
		$categoryAlias = str_replace(" ", "-", $categoryAlias);
	
		$conf = JFactory::getConfig();
		$index ="";
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
	
		if(!$appSettings->enable_seo){
			$eventCategoryLink = $categoryId;
			if(JFactory::getConfig()->get("sef")){
				$categoryLink = $categoryId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=events&eventCategoryId='.$eventCategoryLink.'&Itemid='.$itemid,false,-1);
		}else{
			if($appSettings->add_url_id == 1){
				$eventCategoryLink = $categoryId."-".htmlentities(urlencode($categoryAlias));
			}else{
				$eventCategoryLink = htmlentities(urlencode($categoryAlias));
			}

			$base = JURI::base().$index;
			if($appSettings->add_url_language){
				$langTag = self::getCurrentLanguageCode();
				$base.= $langTag."/";
			}
			
			$url = $base.EVENT_CATEGORY_URL_NAMING."/".$eventCategoryLink;
			
			$menuAlias = self::getCurrentMenuAlias();
			if($appSettings->enable_menu_alias_url && !empty($menuAlias)){
				$url = $base.$menuAlias."/".EVENT_CATEGORY_URL_NAMING."/".$eventCategoryLink;
			}
		}
	
		$url = self::processURL($url);
		
		return $url;
	}
	
	/**
	 * Create the link for an offer
	 * 
	 * @param $offerId
	 * @param $offerAlias
	 * @param $addIndex
	 */	
	static function getOfferLink($offerId, $offerAlias, $addIndex=null){
		$itemidS = self::getItemIdS();
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$offerAlias = trim($offerAlias);
		$offerAlias = stripslashes(strtolower($offerAlias));
		$offerAlias = str_replace(" ", "-", $offerAlias);
	
		$conf = JFactory::getConfig();
		$index ="";
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
	
		$offerLink = $offerId;
		
		if(!$appSettings->enable_seo){
			$offerLink = $offerId;
			if(JFactory::getConfig()->get("sef")){
				$offerLink = $offerId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=offer&offerId='.$offerLink.$itemidS,false,-1);
		}else{
			if($appSettings->add_url_id == 1){
				$offerLink = $offerId."-".htmlentities(urlencode($offerAlias));
			}else{
				$offerLink = htmlentities(urlencode($offerAlias));
			}
			
			$base = JURI::base().$index;
			if($appSettings->add_url_language){
				$langTag = self::getCurrentLanguageCode();
				$base.= $langTag."/";
			}
			$url = $base.OFFER_URL_NAMING."/".$offerLink;
			
			$menuAlias = self::getCurrentMenuAlias();
			if($appSettings->enable_menu_alias_url && !empty($menuAlias)){
				$url = $base.$menuAlias."/".OFFER_URL_NAMING."/".$offerLink;
			}
		}
		
		$url = self::processURL($url);
		
		return $url;
	}
	
	/**
	 * Create the link for an event
	 * 
	 * @param $eventId
	 * @param $eventAlias
	 * @param $addIndex
	 */
	static function getEventLink($eventId, $eventAlias, $addIndex=null){
		$itemidS = self::getItemIdS();
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$eventAlias = trim($eventAlias);
		$eventAlias = stripslashes(strtolower($eventAlias));
		$eventAlias = str_replace(" ", "-", $eventAlias);
	
		$conf = JFactory::getConfig();
		$index ="";
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
	
		if(!$appSettings->enable_seo){
			$eventLink = $eventId;
			if(JFactory::getConfig()->get("sef")){
				$categoryLink = $eventId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=event&eventId='.$eventLink.$itemidS,false,-1);
		}else{
			if($appSettings->add_url_id == 1){
				$eventLink = $eventId."-".htmlentities(urlencode($eventAlias));
			}else{
				$eventLink = htmlentities(urlencode($eventAlias));
			}
			
			$base = JURI::base().$index;
			if($appSettings->add_url_language){
				$langTag = self::getCurrentLanguageCode();
				$base.= $langTag."/";
			}
			
			$url = $base.EVENT_URL_NAMING."/".$eventLink;
			
			$menuAlias = self::getCurrentMenuAlias();
			if($appSettings->enable_menu_alias_url && !empty($menuAlias)){
				$url = $base.$menuAlias."/".EVENT_URL_NAMING."/".$eventLink;
			}
		}
		
		$url = self::processURL($url);
	
		return $url;
	}
	
	/**
	 * Create the link for a conference
	 * 
	 * @param $conferenceId
	 * @param $conferenceAlias
	 * @param $addIndex
	 */
	static function getConferenceLink($conferenceId, $conferenceAlias, $addIndex=null){
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$itemid = JRequest::getInt('Itemid');
	
		$conferenceAlias = trim($conferenceAlias);
		$conferenceAlias = stripslashes(strtolower($conferenceAlias));
		$conferenceAlias = str_replace(" ", "-", $conferenceAlias);
	
		$conf = JFactory::getConfig();
		$index ="";
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
	
		if(!$appSettings->enable_seo){
			$conferenceLink = $conferenceId;
			if(JFactory::getConfig()->get("sef")){
				$conferenceLink = $conferenceId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=conference&conferenceId='.$conferenceId.'&Itemid='.$itemid,false,-1);
		}else{
			if($appSettings->add_url_id == 1){
				$conferenceLink = $conferenceId."-".htmlentities(urlencode($conferenceAlias));
			}else{
				$conferenceLink = htmlentities(urlencode($conferenceAlias));
			}
			
			$base = JURI::base().$index;
			if($appSettings->add_url_language){
				$langTag = self::getCurrentLanguageCode();
				$base.= $langTag."/";
			}
			$url = $base.CONFERENCE_URL_NAMING."/".$conferenceLink;
			
			$menuAlias = self::getCurrentMenuAlias();
			if($appSettings->enable_menu_alias_url && !empty($menuAlias)){
				$url = $base.$menuAlias."/".CONFERENCE_URL_NAMING."/".$conferenceLink;
			}
		}
	
		return $url;
	}
	
	/**
	 * Create the link for a conference session
	 * 
	 * @param $sessionId
	 * @param $sessionAlias
	 * @param  $addIndex
	 * @return string
	 */
	static function getConferenceSessionLink($sessionId, $sessionAlias, $addIndex=null){
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$itemid = JRequest::getInt('Itemid');
	
		$sessionAlias = trim($sessionAlias);
		$sessionAlias = stripslashes(strtolower($sessionAlias));
		$sessionAlias = str_replace(" ", "-", $sessionAlias);
	
		$conf = JFactory::getConfig();
		$index ="";
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
	
		if(!$appSettings->enable_seo){
			$sessionLink = $sessionId;
			if(JFactory::getConfig()->get("sef")){
				$sessionLink = $sessionId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=conferencesession&cSessionId='.$sessionLink.'&Itemid='.$itemid,false,-1);
		}else{
			if($appSettings->add_url_id == 1){
				$sessionLink = $sessionId."-".htmlentities(urlencode($sessionAlias));
			}else{
				$sessionLink = htmlentities(urlencode($sessionAlias));
			}
			
			$base = JURI::base().$index;
			if($appSettings->add_url_language){
				$langTag = self::getCurrentLanguageCode();
				$base.= $langTag."/";
			}
			
			$url = $base.CONFERENCE_SESSION_URL_NAMING."/".$sessionLink;
			
			$menuAlias = self::getCurrentMenuAlias();
			if($appSettings->enable_menu_alias_url && !empty($menuAlias)){
				$url = $base.$menuAlias."/".CONFERENCE_SESSION_URL_NAMING."/".$sessionLink;
			}
		}
	
		return $url;
	}
	
	/**
	 * Create the link for a speaker
	 * 
	 * @param unknown_type $speakerId
	 * @param unknown_type $speakerAlias
	 * @param unknown_type $addIndex
	 */
	static function getSpeakerLink($speakerId, $speakerAlias, $addIndex=null){
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$itemid = JRequest::getInt('Itemid');
	
		$speakerAlias = trim($speakerAlias);
		$speakerAlias = stripslashes(strtolower($speakerAlias));
		$speakerAlias = str_replace(" ", "-", $speakerAlias);
	
		$conf = JFactory::getConfig();
		$index ="";
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
	
		if(!$appSettings->enable_seo){
			$speakerLink = $speakerId;
			if(JFactory::getConfig()->get("sef")){
				$speakerLink = $speakerId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=speaker&speakerId='.$speakerLink.'&Itemid='.$itemid,false,-1);
		}else{
			if($appSettings->add_url_id == 1){
				$speakerLink = $speakerId."-".htmlentities(urlencode($speakerAlias));
			}else{
				$speakerLink = htmlentities(urlencode($speakerAlias));
			}
			
			$base = JURI::base().$index;
			if($appSettings->add_url_language){
				$langTag = self::getCurrentLanguageCode();
				$base.= $langTag."/";
			}
			$url = $base.SPEAKER_URL_NAMING."/".$speakerLink;
			
			$menuAlias = self::getCurrentMenuAlias();
			if($appSettings->enable_menu_alias_url && !empty($menuAlias)){
				$url = $base.$menuAlias."/".SPEAKER_URL_NAMING."/".$speakerLink;
			}
		}
	
		return $url;
	}
	
	static function isJoomla3(){
		$version = new JVersion();
		$versionA =  explode(".", $version->getShortVersion());
		if($versionA[0] =="3"){
			return true;
		}
		return false;
	}

	static function truncate($text, $length, $ending = '&hellip;', $considerHtml = true, $exact = false){
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
						// if tag is a closing tag
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
						// if tag is an opening tag
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if($total_length>= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}
	
	static function getAlias($title, $alias){
		if (empty($alias) || trim($alias) == ''){
			$alias = $title;
		}
		
		$alias = JApplication::stringURLSafe($alias);
		
		if (trim(str_replace('-', '', $alias)) == ''){
			$alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}
		
		return $alias;
	}
	
	/**
	 * Compose address based on address and city
	 * 
	 * @param unknown_type $address
	 * @param unknown_type $city
	 */
	static function composeAddress($address, $city){
		$result ="";
		if(!empty($address)){
			$result .=$address;
		}
		
		if(!empty($address) && !empty($city)){
			$result .=", ";
		}
		
		if(!empty($city)){
			$result .=$city;
		}
		
		return $result;
		
	}

    static function getAddressText($company){
        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $customAddress = $appSettings->custom_address;
        $address="";
      
        if(isset($company->publish_only_city) && $company->publish_only_city){
            $address=$company->city.' '.$company->county;
            return $address;
        }

        if(!isset($company->street_number)){
            $company->street_number="";
        }

        $addressParts = array();
        switch ($appSettings->address_format){
            case 1:
                $addressParts = array($company->street_number." ".$company->address, $company->area,$company->city." ".$company->postalCode,$company->county, $company->province);
                break;
            case 2:
                $addressParts = array($company->address." ".$company->street_number, $company->area,$company->city." ".$company->postalCode,$company->county, $company->province);
                break;
            case 3:
                $addressParts = array($company->street_number." ".$company->address, $company->area,$company->city,$company->county ." ".$company->postalCode, $company->province);
                break;
            case 4:
                $addressParts = array($company->address." ".$company->street_number, $company->area,$company->city,$company->county ." ".$company->postalCode, $company->province);
                break;
            case 5:
                $addressParts = array($company->street_number." ".$company->address, $company->area,$company->postalCode." ".$company->city,$company->county, $company->province);
                break;
            case 6:
                $addressParts = array($company->address." ".$company->street_number, $company->area,$company->postalCode." ".$company->city,$company->county, $company->province);
                break;
            case 7:
                $addressParts = array($company->postalCode." ".$company->province, $company->city, $company->area, $company->street_number);
                break;
            case 8:
                $customAddress = str_replace(ADDRESS_ADDRESS, $company->address, $customAddress);
                $customAddress = str_replace(ADDRESS_AREA, $company->area, $customAddress);
                $customAddress = str_replace(ADDRESS_CITY, $company->city, $customAddress);
                $customAddress = str_replace(ADDRESS_POSTAL_CODE, $company->postalCode, $customAddress);
                $customAddress = str_replace(ADDRESS_PROVINCE, $company->province, $customAddress);
                $customAddress = str_replace(ADDRESS_REGION, $company->county, $customAddress);
                $customAddress = str_replace(ADDRESS_STREET_NUMBER, $company->street_number, $customAddress);
                break;
        }
        
        $addressParts = array_filter($addressParts);
        foreach ($addressParts as $key=>$add){
            if ($add == " ")
                unset($addressParts[$key]);
        }
        $addressParts = array_map('trim',$addressParts);
        $addressParts = implode(', ',$addressParts);
        $addressParts = trim($addressParts);
        $address = $addressParts;

        $countryName ="";
        if($appSettings->add_country_address == 1) {
            if (!empty($address) || !empty($customAddress)) {
                if (!empty($company->country_name)) {
                    if(!empty($address)) {
                        $address .= ", " . $company->country_name;
                    }else {
                        $address = $company->country_name;
                    }
                    $countryName = $company->country_name;
                } else if (!empty($company->countryName)) {
                    if(!empty($address)) {
                        $address .= ", " . $company->countryName;
                    }else {
                        $address = $company->countryName;
                    }
                    $countryName = $company->countryName;
                } else if (!empty($company->countryId)) {
                    JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_jbusinessdirectory/tables');
                    $countryTable = JTable::getInstance("Country", "JTable");
                    $countryName = $countryTable->getCountry($company->countryId)->country_name;
                    if(!empty($address)) {
                        $address .= ", " . $countryTable->getCountry($company->countryId)->country_name;
                    }else {
                        $address = $countryTable->getCountry($company->countryId)->country_name;;
                    }
                }
            }
        }
        
        if($appSettings->address_format == 8){
            $customAddress = str_replace(ADDRESS_COUNTRY, $countryName, $customAddress);
            $address = $customAddress;
        }

        $address = explode(',',$address);
        $address = array_filter($address);
        foreach ($address as $key=>$add){
            if ($add == " ")
                unset($address[$key]);
        }
        $address = implode(',',$address);
        $address = trim($address);

        if(empty($address)) {
            return null;
        }

        return $address;
    }
	
	/**
	 * Return only the main items of the address
	 * @param unknown $company
	 * @return string
	 */
	static function getShortAddress($company){
	    $address="";
	    $addressParts = array($company->city,$company->county, $company->province);
	    $addressParts = array_filter($addressParts);
	    if(!empty($addressParts)){
	        $address = implode(", ", $addressParts);
	    }
	    
	    return $address;
	}
	
	static function getLocationAddressText($street_number,$address, $area, $city, $county, $province, $postalCode ){
		$locationItem = new stdClass();
		$locationItem->street_number = $street_number;
		$locationItem->address = $address;
		$locationItem->city = $city;
		$locationItem->county = $county;
		$locationItem->postalCode = $postalCode;
		$locationItem->province = $province;
		$locationItem->area = $area;
		return self::getAddressText($locationItem);
	}
	
	/**
	* @deprecated   12.1  Use JBusinesUtil::getAddressText
	**/
	static function getLocationText($item){
		$location="";
		
		if(!empty($item->address)){
			$location .= $item->address;
		}
		
		if(!empty($item->area)){
			if(!empty($location))
				$location .= ", ".$item->area;
				else
					$location = $item->area;
		}
		
		if(!empty($item->city)){
			if(!empty($location) && empty($item->area))
				$location .= ", ".$item->city;
				else
					$location .= " ".$item->city;
		}
		if(!empty($item->county)){
			if(!empty($location))
				$location .= ", ".$item->county;
				else
					$location = $item->county;
		}
		
		if(!empty($item->province)){
			if(!empty($location))
				$location .= ", ".$item->province;
				else
					$location = $item->province;
		}
		
		if(empty($item->address) && empty($item->city) && !empty($item->location)){
			$location = $item->location;
		}
		
		return $location;
	}
	
	
	
	public static function getBusinessListingCategory($company){
		$categoryId = 0;
		if(!empty($company->mainSubcategory)){
			$categoryId = $company->mainSubcategory;
		}else{
			if(!empty($company->categories)){
				$listingCategories = explode('#',$company->categories);
				$category = explode("|", $listingCategories[0]);
				$categoryId = $category[0];
			}
		}
		
		return $categoryId;
	} 
	
	/**
	 * Get business listing main category and retrieve the category path
	 * @param unknown_type $company
	 */
	static function getBusinessCategoryPath($company){
		$categoryId = self::getBusinessListingCategory($company);
		return self::getCategoryPath($categoryId);
	}
	
	/**
	 * Get the categor path as an array of categories 
	 * @param int $categoryId
	 * @return category path as array
	 */
	static function getCategoryPath($categoryId){
		
		if(empty($categoryId)){
			return array();
		}
		
		$categories = self::getCategories();
	
		$category = self::getCategory($categories, $categoryId);
		$path=array();
		if(!empty($category)){
			$path[]=$category;
		
			while($category->parent_id != 1){
				if(!$category->parent_id)
					break;
				$category=self::getCategory($categories, $category->parent_id);
				$path[] = $category;
			}
				
			$path = array_reverse($path);
		}
		
		return $path;
	}
	
	static function getCategories(){
		$instance = JBusinessUtil::getInstance();
		
		if(!isset($instance->categories)){
			JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
			$categoryTable =JTable::getInstance("Category","JBusinessTable");
			$categories = $categoryTable->getAllCategories();
			$instance->categories = $categories;
		}
		return $instance->categories;
	}
	
	static function getCategory($categories, $categoryId){
		if(empty($categories) || empty($categoryId))
			return null;
		
		foreach($categories as $category){
			if($category->id == $categoryId){
				return $category;
			}
		}
		return null;
	}
	
	static function getCategoryItem($categoryId){
		
		$categories = self::getCategories();
		if(empty($categories)){
			return null;
		}
		
		foreach($categories as $category){
			if($category->id == $categoryId){
				return $category;
			}
		}
		
		return null;	
	}
	
	static function getLanguages(){
		$languages = JLanguage::getKnownLanguages();
		$result = array();
		foreach ($languages as $key=>$language){
			$name = $language["name"];
			$name = substr($name,0,strpos($name, "(")-1);
			$result[$name]=$key;
		}
		asort($result);
		return $result;
	} 
	
	static function getCurrentLanguageCode(){
		$lang = JFactory::getLanguage()->getTag();
		$lang = explode("-",$lang);
		return $lang[0];
	}
	
	static function getCategoriesOptions($published, $type = null, $catId = null, $showRoot = false){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)		
		->select('a.id AS value, a.name AS text, a.level, a.published')
		->from('#__jbusinessdirectory_categories AS a')
		->join('LEFT', $db->quoteName('#__jbusinessdirectory_categories') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		
		if(!empty($catId)){
			$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_categories') . ' AS p ON p.id = ' . (int) $catId)
			->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');
		}
		
		if (($published)) {
			$query->where('a.published = 1');
		}

		if (($type)) {
			$query->where('(a.type IN (0,' . (int) $type.'))');
		}
		
		if(!$showRoot){
			$query->where('a.id >1');
		}
		
		$query->group('a.id, a.name, a.level, a.lft, a.rgt, a.parent_id, a.published')
		->order('a.lft ASC');
		
		$db->setQuery($query);
		$options = $db->loadObjectList();
		$categoryTranslations = JBusinessDirectoryTranslations::getCategoriesTranslations();
		
		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			if ($options[$i]->published == 1)
			{
				if(!empty($categoryTranslations[$options[$i]->value]))
					$options[$i]->text = $categoryTranslations[$options[$i]->value]->name;
				if($showRoot){
					$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
				}else{
					$options[$i]->text = str_repeat('- ', $options[$i]->level-1) . $options[$i]->text;
				}
			}
			else
			{
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . '[' . $options[$i]->text . ']';
			}
		}
		
		return $options;
	}

	static function getCompaniesOptions() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)		
		->select('id AS value, name AS text')
		->from('#__jbusinessdirectory_companies')
		->group('id')
		->order('name ASC');

		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		return $options;
	}

	static function getSpeakersOptions() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)		
		->select('id AS value, name AS text')
		->from('#__jbusinessdirectory_conference_speakers')
		->group('id')
		->order('title ASC');

		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		return $options;
	}

	static function getSessionsOptions() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)		
		->select('id AS value, name AS text')
		->from('#__jbusinessdirectory_conference_sessions')
		->group('id')
		->order('name ASC');

		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		return $options;
	}
	
	static function getConferenceOptions() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('id AS value, name AS text')
		->from('#__jbusinessdirectory_conferences')
		->group('id')
		->order('name ASC');
		
		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		return $options;
	}
	
	/**
	 * Get review question types
	 */
	static function getReviewQuestiosnTypes(){
		$types = array();
		$type = new stdClass();
		$type->value = 0;
		$type->text = JTEXT::_("LNG_TEXT");
		$types[] = $type;
		$type = new stdClass();
		$type->value = 1;
		$type->text = JTEXT::_("LNG_YES_NO_QUESTION");
		$types[] = $type;
		$type = new stdClass();
		$type->value = 2;
		$type->text = JTEXT::_("LNG_RATING");
		$types[] = $type;
	
		return $types;
	}
	
	static function getPriceFormat($amount, $currencyId = null) {
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$dec_point=".";
		$thousands_sep = ",";
		
		if($appSettings->amount_separator==2) {
			$dec_point=",";
			$thousands_sep = ".";
		}
		
		$currencyString = $appSettings->currency_name;
		if($appSettings->currency_display==2) {
			$currencyString = $appSettings->currency_symbol;
		}
		
		$amountString = number_format($amount , 2 , $dec_point,  $thousands_sep);

		if(!empty($currencyId)) {
			JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
			$currencyTable = JTable::getInstance("Currency", "JTable");
			$currency = $currencyTable->getCurrencyById($currencyId);
			$currencyString = $currency->currency_name;
			if($appSettings->currency_display==2)
				$currencyString = $currency->currency_symbol;
		}
		
		if($appSettings->currency_location==1) {
			$result = $currencyString." ".$amountString;
		} else {
			$result = $amountString." ".$currencyString;
		}

		return $result;
	}

	static function getPriceDiscount($specialPrice, $price){
        $percentChange = (1 - $specialPrice / $price) * 100;
        $discount =  number_format($percentChange, 1);
        return $discount;
    }
	
	static function convertPriceToMysql($price){
	    if (strpos($price,'.') && strpos($price,',')){
	        if(strlen($price)>strrpos($price,',') && strlen($price)>strrpos($price,'.')) {
                if (abs(strrpos($price, '.') - strrpos($price, ',')) == 4) {
                    if (strrpos($price, '.') < strrpos($price, ',')) {
                        $price = str_replace('.', '', $price);
                        $pos = strrpos($price, ',');
                        $price = substr_replace($price, '.', $pos,'1');
                        $price = str_replace(',', '', $price);
                    } else {
                        $price = str_replace(',', '', $price);
                        $pos = strrpos($price, '.');
                        $price = substr_replace($price, ',', $pos,'1');
                        $price = str_replace('.', '', $price);
                        $price = str_replace(',', '.', $price);
                    }
                } else {
                    $price = str_replace('.', '', $price);
                    $price = str_replace(',', '', $price);
                }
            }else{
                $price = str_replace('.', '', $price);
                $price = str_replace(',', '', $price);
            }
        }else {
            if (strlen($price) > (strrpos($price, ',')+1) || strlen($price) > (strrpos($price, '.')+1)) {
                if (substr_count($price, ',') > 1 || substr_count($price, '.') > 1) {
                    if (strpos($price,'.')){
                        if(strlen($price) > (strrpos($price,'.')+1)) {
                            $pos = strrpos($price, '.');
                            $price = substr_replace($price, ',', $pos, '1');
                            $price = str_replace('.', '', $price);
                            $price = str_replace(',', '.', $price);
                        }else{
                            $price = str_replace('.', '', $price);
                            $price = str_replace(',', '', $price);
                        }
                    }else{
                        if(strlen($price) > (strrpos($price,',')+1)) {
                            $pos = strrpos($price, ',');
                            $price = substr_replace($price, '.', $pos, '1');
                            $price = str_replace(',', '', $price);
                        }else{
                            $price = str_replace('.', '', $price);
                            $price = str_replace(',', '', $price);
                        }
                    }
                } else {
                    $price = str_replace('.', '.', $price);
                    $price = str_replace(',', '.', $price);
                }
            } else {
                $price = str_replace('.', '', $price);
                $price = str_replace(',', '', $price);
            }
        }
        $price = str_replace(' ', '', $price);

		return $price;
	}

    static function convertPriceFromMysql($price){
        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $dec_point=".";
        $thousands_sep = ",";

        if($appSettings->amount_separator==2) {
            $dec_point=",";
            $thousands_sep = ".";
        }

        $price = number_format($price , 2 , $dec_point,  $thousands_sep);

        return $price;
    }
	
	public static function loadAdminLanguage(){
		$language = JFactory::getLanguage();
		$language_tag 	= $language->getTag();
		//$language_tag = "nl-NL";
		$x = $language->load(
				'com_jbusinessdirectory' , dirname(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jbusinessdirectory'.DS.'language') ,
				$language_tag,true );
		
		/*$filename = JPATH_ADMINISTRATOR."/components/com_jbusinessdirectory/language/pl-PL/pl-PL.com_jbusinessdirectory.ini";
		dump($filename);
		$contents = file_get_contents($filename);
		$result = parse_ini_string($contents);
		dump($result);
		
		exit;*/
	}
	
	public static function loadSiteLanguage(){
		$language = JFactory::getLanguage();
		$language_tag 	= $language->getTag();
		
		$x = $language->load(
		'com_jbusinessdirectory' , dirname(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jbusinessdirectory'.DS.'language') ,
		$language_tag,true );
		
		//load users language, needed for emails
		$x = $language->load(   'com_users' ,
				dirname( JPATH_SITE.DS.'language') ,
				$language_tag,
				true
		);
					
		$language_tag = str_replace("-","_",$language->getTag());
		setlocale(LC_TIME , $language_tag.'.UTF-8');
	}
	
	
	/**
	 * Remove a directory
	 * 
	 * @param unknown_type $dir
	 */
	public static function removeDirectory($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
	
	/**
	 * Convert a day to a String
	 * @param unknown_type $day
	 * @param unknown_type $abbr
	 */
	public static function dayToString ($day, $abbr = false)
	{
		$date = new JDate();
		return addslashes($date->dayToString($day, $abbr));
	}
	
	public static function monthToString ($month, $abbr = false)
	{
		$date = new JDate();
		return addslashes($date->monthToString($month, $abbr));
	}
	
	static function getNumberOfDays($startData, $endDate){
	
		$nrDays = floor((strtotime($endDate) - strtotime($startData)) / (60 * 60 * 24));
	
		return $nrDays;
	}
	
	/**
	 * Get the day of month from provided date
	 * @param unknown_type $date
	 */
	public static function getDayOfMonth($date){
		if(empty($date))
			return "";
		
		return date("j", strtotime($date));
	}

	/**
	 * Get month as string from provided date
	 * @param unknown_type $date
	 */
	public static function getMonth($date){
		if(empty($date))
			return "";
		$date = JFactory::getDate($date);
		return $date->format('M');
	}
	
	/**
	 * Get year from provided date
	 * @param unknown_type $date
	 * @return string
	 */
	public static function getYear($date){
		if(empty($date))
			return "";
		
		$date = JFactory::getDate($date);
		return $date->format('Y');
	}
	
	/**
	 * Include validation required files
	 */
	static public function includeValidation(){
		JHTML::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/validationEngine.jquery.css');
		$tag = JBusinessUtil::getCurrentLanguageCode();
		
		if(!file_exists(JPATH_COMPONENT_SITE.'/assets/js/validation/jquery.validationEngine-'.$tag.'.js'))
			$tag ="en";
		
		JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/validation/jquery.validationEngine-'.$tag.'.js');
		JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/validation/jquery.validationEngine.js');
	}

	/**
	 * Calculate the elapsed time from a timestamp
	 * 
	 * @param unknown_type $datetime
	 * @param unknown_type $full
	 * @return string
	 */
	public static function convertTimestampToAgo($datetime, $full = false) {
	    $now = new DateTime;
	    $ago = new DateTime($datetime);
	    $diff = $now->diff($ago);

	    $diff->w = floor($diff->d / 7);
	    $diff->d -= $diff->w * 7;

	    $string = array(
	        'y' => 'year',
	        'm' => 'month',
	        'w' => 'week',
	        'd' => 'day',
	        'h' => 'hour',
	        'i' => 'minute',
	        's' => 'second',
	    );
	    foreach ($string as $k => &$v) {
	        if ($diff->$k) {
	            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
	        } else {
	            unset($string[$k]);
	        }
	    }

	    if (!$full) $string = array_slice($string, 0, 1);
	    return $string ? implode(', ', $string) . ' ago' : 'just now';
	}

	/**
	 * Remove files/images that are not linked anymore
	 * @param array $usedFiles
	 * @param unknown_type $rootFolder
	 * @param unknown_type $filesFolder
	 */
	public static function removeUnusedFiles(array $usedFiles, $rootFolder, $filesFolder) {
		
		$directoryPath = JBusinessUtil::makePathFile($rootFolder.$filesFolder);
		// $usedFiles -> array of the filename of the files
		// $filesFolder -> example: 'items/id/'
		// $rootFolder -> example: 'pictures'
		$usedFiles[]=JBusinessUtil::makePathFile($filesFolder)."index.html";
		
		foreach($usedFiles as &$file){
			$file = JBusinessUtil::makePathFile($file);
		}
		
		$allFiles = array();
		if(file_exists($directoryPath)){
			foreach (scandir($directoryPath, 1) as $singleFile) {
				array_push($allFiles, JBusinessUtil::makePathFile($filesFolder.$singleFile));
			}
		}
		
		$unusedFiles = array_diff($allFiles, $usedFiles);
		
		foreach ($unusedFiles as $unusedFile) {
			if(is_file($rootFolder.$unusedFile)) {
				unlink($rootFolder.$unusedFile);
			}
		}
	}
	
	
	public static function moveFile($picture_path,$itemId, $oldId, $type){
		
		$path_new = JBusinessUtil::makePathFile(JPATH_ROOT."/".PICTURES_PATH .$type.($itemId)."/");
		
		//prepare photos
		$path_old = JBusinessUtil::makePathFile(JPATH_ROOT."/".PICTURES_PATH .$type.($oldId)."/");
		if(!empty($picture_path)){
			$parts = explode("/",$picture_path);
			$oldId = $parts[2];
			$path_old = JBusinessUtil::makePathFile(JPATH_ROOT."/".PICTURES_PATH .$type.($oldId)."/");
		}
			
		$file_tmp = JBusinessUtil::makePathFile( $path_old.basename($picture_path) );
		//dump($file_tmp);
		if( !is_file($file_tmp) )
			return;
		//dump("is file");
		if( !is_dir($path_new) )
		{
			if( !@mkdir($path_new) )
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
		}
		
		//dbg(($path_old.basename($picture_path).",".$path_new.basename($picture_path)));
		// exit;
		if( $path_old.basename($picture_path) != $path_new.basename($picture_path)){
			if($oldId==0){
				if(@rename($path_old.basename($picture_path),$path_new.basename($picture_path)) )
				{
		
					$picture_path	 = $type.($itemId).'/'.basename($picture_path);
					//@unlink($path_old.basename($pic->room_picture_path));
				}
				else
				{
					throw( new Exception($this->_db->getErrorMsg()) );
				}
			}else{
				if(@copy($path_old.basename($picture_path),$path_new.basename($picture_path)) )
				{
		
					$picture_path	 = $type.($itemId).'/'.basename($picture_path);
					//@unlink($path_old.basename($pic->room_picture_path));
				}
				else
				{
					throw( new Exception($this->_db->getErrorMsg()) );
				}
			}
		}
		
		return $picture_path;
	}
	

	/**
	 * Get the type and thumbnail of the video based on the video url (Youtube and Vimeo supported).
	 * 
	 * @param $url
	 * @return array()
	 */
	public static function getVideoDetails($url) {
		$data = array();

		$iframe = strpos($url,'iframe');
		if (!empty($iframe)) { //if the $url is an iframe 
			preg_match('/src="([^"]+)"/', $url, $match);
			if(isset($match[1])){
				$url = $match[1];
			}
		}

		// If it's a youtube video
		if (strpos($url, 'youtu') > 0) {
			preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);

			$id = $matches[1]; // We need the video ID to find the thumbnail
			$thumbnail = 'https://img.youtube.com/vi/'.$id.'/0.jpg';
			
			$data = array(
				'url' => 'https://www.youtube-nocookie.com/embed/'.$id.'?rel=0',
				'type' => 'youtube',
				'thumbnail' => $thumbnail
			);
		}
		// If it's a vimeo video
		elseif (strpos($url, 'vimeo') > 0) {
			preg_match("/https?:\/\/(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/", $url, $matches);
			$id = $matches[3];
			$hash = unserialize(file_get_contents("https://vimeo.com/api/v2/video/".$id.".php"));
			$thumbnail = $hash[0]['thumbnail_large'];

			$data = array(
				'url' => 'https://vimeo.com/'.$id,
				'type' => 'vimeo',
				'thumbnail' => $thumbnail
			);
		} 
		// If it's not supported
		else {
			$data = array(
				'url' => 'https://www.youtube.com',
				'type' => 'unsupported',
				'thumbnail' => 'placehold.it/400x300?text=UNSUPPORTED+FORMAT'
			);
		}

		return $data;
	}
	
	/**
	 * Retrieve current version from manifest file
	 * @return  string versino number
	 */
	public static function getCurrentVersion(){
		$module = JComponentHelper::getComponent('com_jbusinessdirectory');
		$extension = JTable::getInstance('extension');
		$extension->load($module->id);
		$data = json_decode($extension->manifest_cache, true);
		return $data['version'];
	}

	/**
	 * Method that gets the booking open date-times of a particular event, compares their values
	 * to the current time and returns true or false depending on whether the current date-time is
	 * considered as valid.
	 *
	 * @param $event	Object containing the booking dates and times
	 * @return bool		Boolean value
	 */
	public static function isBookingAvailable($event)
	{
		// Booking hours
		$now = date('H:i:s');
		$startTime = $event->booking_open_time;
		$endTime = $event->booking_close_time;

		// Booking dates
		$today = date("Y-m-d H:i:s");
		$start = $event->booking_open_date;
		$end = $event->booking_close_date;

		// Create Y-m-d H:i:s format if start/end hour is available, if not, 00:00:00 is set as start hour and 23:59:59 as end hour
		if (!empty($event->booking_open_time))
			$start = $event->booking_open_date . ' ' . $event->booking_open_time;
		else
			$start = $event->booking_open_date . ' 00:00:00';

		if (!empty($event->booking_close_time))
			$end = $event->booking_close_date . ' ' . $event->booking_close_time;
		else
			$end = $event->booking_close_date . ' 23:59:59';

		// If date is empty or set to 0, empty the $start and $end values
		if (self::emptyDate($event->booking_open_date))
			$start = '';
		if (self::emptyDate($event->booking_close_date))
			$end = '';

		if (!empty($start) || !empty($end)) {
			$areHoursValid = false;
			// Check the Booking Hours
            if((empty($startTime) && empty($endTime)) || ($startTime == '00:00:00' && $endTime == '00:00:00'))
                $areHoursValid = true;
			else if (!empty($startTime) || !empty($endTime)) {
				if (!empty($startTime) && !empty($endTime)) {
					if (strtotime($startTime) <= strtotime($now) && strtotime($now) < strtotime($endTime))
						$areHoursValid = true;
					else
						$areHoursValid = false;
				} else if (!empty($startTime)) {
					if (strtotime($startTime) <= strtotime($now))
						$areHoursValid = true;
					else
						$areHoursValid = false;
				} else if (!empty($endTime)) {
					if (strtotime($endTime) > strtotime($now))
						$areHoursValid = true;
					else
						$areHoursValid = false;
				}
			}

			// Check the Booking Dates
			if (!empty($start) && !empty($end)) {
				if (strtotime($start) <= strtotime($today) && strtotime($today) < strtotime($end))
					return $areHoursValid;
				else
					return false;
			} else if (!empty($start)) {
				if (strtotime($start) <= strtotime($today))
					return $areHoursValid;
				else
					return false;
			} else if (!empty($end)) {
				if (strtotime($end) > strtotime($today))
					return $areHoursValid;
				else
					return false;
			}
		}

		return true;
	}

	/**
	 * Checks if a date is empty or equal to the date equivalent of zero, and if so, returns true.
	 *
	 * @param $date
	 * @return bool
	 */
	public static function emptyDate($date)
	{
		if (!empty($date)) {
			if ($date == '0000-00-00' || $date == '00-00-0000')
				return true;
			else
				return false;
		} else
			return true;
	}

	/**
	 * Checks if the current date is inside the date interval determined by the $startDate and $endDate
	 *
	 * @param $startDate string containing the start Date of the interval
	 * @param $endDate	string containing the end Date of the interval
	 * @param $currentDate string containing the date that will be checked, null if the date that needs to be checked is the actual day
	 * @param bool|true $includeEndDate determines whether the end date
	 * will be included in the interval
	 * @param bool|true $allowEmptyBoundaries determines
	 * whether start or end date may be empty
	 * @return bool If date is in between the specified interval, true is returned.
	 * False is returned otherwise or if one of the boolean parameters condition is violated
	 *
	 * Note: if both start and end values are empty, and $allowEmptyBoundaries is set to true,
	 * true will be returned.
	 */
	public static function checkDateInterval($startDate, $endDate, $currentDate = null, $includeEndDate=true, $allowEmptyBoundaries=true)
	{
		if($currentDate == null)
			$currentDate = date("Y-m-d H:i:s");

		if(!self::emptyDate($startDate) || $allowEmptyBoundaries){
			$start = $startDate;
		}
		else
			return false;

		if(!self::emptyDate($endDate) || $allowEmptyBoundaries){
			$end = $endDate;
		}
		else
			return false;

		if($includeEndDate && !self::emptyDate($end))
			$end .= ' 23:59:59';


		if(((strtotime($start) <= strtotime($currentDate)) || self::emptyDate($start))
			&& ((strtotime($currentDate) < strtotime($end)) || self::emptyDate($end)))
			return true;
		else
			return false;

	}

	/**
	 * Method that returns all dates between a provided date interval
	 *
	 * @param $first string First date of interval
	 * @param $last string Last date of interval
	 * @param string $step The iteration step (in days) between the dates
	 * @param string $output_format The output format of the dates
	 * @return array An array containing all the dates between the interval
	 */
	public static function getAllDatesInInterval($first, $last, $step = "+1 day", $output_format = "d-m-Y")
	{
		$dates = array();
		$current = strtotime($first);
		$last = strtotime($last);

		while( $current <= $last ) {

			$dates[] = date($output_format, $current);
			$current = strtotime($step, $current);
		}

		return $dates;
	}
	
	
	/**
	 * Get country name
	 */
	public static function getCountryName($countryId){
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_countries where id = $countryId ";
		$db->setQuery($query);
		$country = $db->loadObject();
		
		return $country->country_name;
	}
	
	/**
	 * Get type name
	 */
	public static function getTypeName($typeId){
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_types where id = $typeId ";
		$db->setQuery($query);
		$type = $db->loadObject();
		
		return $type->name;
	}

    /**
     * Validate order by field
     * @return true if valid or false if not
     */
    public static function validateOrderBy($orderBy, $allowedValues){
    	foreach($allowedValues as $item){
    		if($orderBy == $item->value)
    			return true;
    	}

    	return false;
    }

    /**
     * Get file size info
     * @param unknown_type $bytes
     * @param unknown_type $decimals
     */
    public static function getReadableFilesize($bytes, $decimals = 2) {
        $sz = array('b', 'Kb', 'Mb', 'Gb', 'Tb');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . $sz[$factor];
    }

    /**
     * Get number of pdf pages
     * @param unknown_type $pdfname
     */
    public static function getNumberOfPdfPages($pdfname) {
        $pdftext = file_get_contents($pdfname);
        $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
        return $num;
    }

    /**
     * Retrieve attachment properties
     * @param unknown_type $attach
     */
    public static function getAttachProperties($attachment){

    	$attachmentProperties = new stdClass();
        $fileProperties = pathinfo(JPATH_SITE . "/" . ATTACHMENT_PATH . $attachment->path);
        if (!isset($fileProperties['extension']))
            $fileProperties['extension'] = "FILE";
        $attachmentProperties->fileProperties = $fileProperties;
        
        if(!empty($attachment->path)){
            $attachmentProperties->size = JBusinessUtil::getReadableFilesize(filesize(JPATH_SITE . "/" . ATTACHMENT_PATH . $attachment->path), 2);
        }

        if (strcasecmp($fileProperties['extension'],'pdf') == 0  && !empty($attachment->path)) {
            $attachmentProperties->nrPages = JBusinessUtil::getNumberOfPdfPages(JPATH_SITE . "/" . ATTACHMENT_PATH . $attachment->path);
        } else {
            $attachmentProperties->nrPages = "";
        }

        switch(strtoupper($fileProperties['extension'])){
        	case "PDF": 
        		$attachmentProperties->icon = JURI::root() . "/" . ATTACHMENT_ICON_PATH . "pdf.png";
        		break;
        	case "BMP":
        	case "GIF":
        	case "JPEG":
        	case "JPG":
        	case "PNG":
        		 $attachmentProperties->icon = JURI::root() . "/" . ATTACHMENT_ICON_PATH . "photo.png";
        		break;
        	case "DOC":
        	case "DOCM":
        	case "DOCX":
        	case "GDOC":
        		$attachmentProperties->icon = JURI::root() . "/" . ATTACHMENT_ICON_PATH . "doc.png";
        		break;
        	case "XLS":
        	case "XLK":
        	case "XLSX":
        	case "XLR":
        	case "XLT":
        	case "XLW":
        		 $attachmentProperties->icon = JURI::root() . "/" . ATTACHMENT_ICON_PATH . "xls.png";
        		break;
        	case "TXT":
        		$attachmentProperties->icon = JURI::root() . "/" . ATTACHMENT_ICON_PATH . "txt.png";
        		break;
        	case "MP3":
        	case "WMA":
        	case "M4A":
        	case "MP4A":
        	case "AAC":
        		$attachmentProperties->icon = JURI::root() . "/" . ATTACHMENT_ICON_PATH . "sound.png";
        		break;
        	default:
        		$attachmentProperties->icon = JURI::root() . "/" . ATTACHMENT_ICON_PATH . "file.png";
        }

        return $attachmentProperties;
    }
    
    /**
     * Upload file in a specified folder
     * 
     * @param unknown_type $fileName
     * @param unknown_type $data
     * @param unknown_type $dest
     * @return boolean|string|NULL
     */
    public static function uploadFile($fileName, &$data, $dest){
    
    	//Retrieve file details from uploaded file, sent from upload form
    	$file = JRequest::getVar($fileName, null, 'files', 'array');
    
    	if($file['name']=='')
    		return true;
    
    	//Import filesystem libraries. Perhaps not necessary, but does not hurt
    	jimport('joomla.filesystem.file');
    		
    	//Clean up filename to get rid of strange characters like spaces etc
    	$fileNameSrc = JFile::makeSafe($file['name']);
    	$data[$fileName] =  $fileNameSrc;
    
    	$src = $file['tmp_name'];
    	$dest = $dest."/".$fileNameSrc;
    
    	$result =  JFile::upload($src, $dest);
    
    	if($result)
    		return $dest;
    
    	return null;
    }

    /**
     * Generate the direction URL
     * @param unknown_type $origin
     * @param unknown_type $destination
     */
    public static function getDirectionURL($origin, $destination){

        $db = JFactory::getDBO();
        $source = "";
    	if(!empty($origin) && isset($origin["latitude"]) && isset($origin["longitude"])){
    	    $source = "saddr=".$db->escape($origin["latitude"]).",".htmlspecialchars($origin["longitude"]);
    	}

    	$dest = "";
    	if(!empty($destination) && isset($destination->latitude) && isset($destination->longitude)){
    	    $dest = "daddr=".$db->escape($destination->latitude).",".$db->escape($destination->longitude);
    	}

    	$delimiter="";
    	if(!empty($source) && !empty($dest)){
    		$delimiter="&";
    	}

    	$url = "https://maps.google.com?".$source.$delimiter.$dest;

    	return $url;
    }

    /**
     * Method that returns the translated name of the weekday for a
     * given index that should range from 1-7
     *
     * @param $dayIndex int day of week index, should have values from 1 to 7
     * @param $translate bool if false, days will not be translated (will stay in english)
     * @return string the name of the weekday
     * @return boolean false if the dayIndex is outside the required range
     */
    public static function getWeekdayFromIndex($dayIndex, $translate=true)
    {
        if($translate) {
            $days = array(
                1 => JText::_('LNG_MONDAY'),
                2 => JText::_('LNG_TUESDAY'),
                3 => JText::_('LNG_WEDNESDAY'),
                4 => JText::_('LNG_THURSDAY'),
                5 => JText::_('LNG_FRIDAY'),
                6 => JText::_('LNG_SATURDAY'),
                7 => JText::_('LNG_SUNDAY')
            );
        }
        else {
            $days = array(
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
                7 => 'Sunday'
            );
        }

        if ($dayIndex >= 1 && $dayIndex <= 7)
            return $days[$dayIndex];
        else
            return false;
    }

    /**
     * Method that formats a timestamp duration or time period (minutes) into
     * a better looking time text like: 'hh Hour(s) mm Minute(s) ss Second(s)'
     *
     * @param $time string containing the time in the hh:mm:ss format
     * @param $timeFormat int 0-> 00:00:00, 1-> minutes
     * @return string formatted time
     */
    public static function formatTimePeriod($time, $timeFormat = 0)
    {
        if($timeFormat == 0) {
            $temp = explode(':', $time);
            $hours = $temp[0];
            $minutes = $temp[1];
            $seconds = $temp[2];
        }
        else if($timeFormat == 1) {
            if ($time < 1) {
                return false;
            }

            $hours = floor($time / 60);
            $minutes = ($time % 60);
            $seconds = 0;
        }

        $resultTime = '';
        if (isset($hours) && $hours != '00' || $hours != '0') {
            $hoursText = (int)$hours > 1 ? JText::_('LNG_HOURS') : JText::_('LNG_HOUR');
            $resultTime .= (int)$hours . ' ' . $hoursText . ' ';
        }

        if (isset($minutes) && $minutes != '00' || $minutes != '0') {
            $minutesText = (int)$minutes > 1 ? JText::_('LNG_MINUTES') : JText::_('LNG_MINUTE');
            $resultTime .= (int)$minutes . ' ' . $minutesText . ' ';
        }

        if (isset($seconds) && $seconds != '00' || $seconds != '0') {
            $secondsText = (int)$seconds > 1 ? JText::_('LNG_SECONDS') : JText::_('LNG_SECOND');
            $resultTime .= (int)$seconds . ' ' . $secondsText . ' ';
        }

        return $resultTime;
    }


    /**
     * Method that gets raw information about the vacations for a provider
     * and organizes them into dates which will define the free days
     *
     * @param $vacationData object containing availability and break days information
     * @return array organized array containing all the free days for a provider
     */
    public static function processProviderVacationDays($vacationData) {
        $vacations = array();
        $availability = explode(',', $vacationData->availability);
        $weekDays = explode(',', $vacationData->breakDays);

        if (!empty($availability)) {
            $startDates = array();
            $endDates = array();

            // get all start-end date pairs
            for ($i = 0; $i < count($availability); $i += 2) {
                $startDates[] = $availability[$i];
                $endDates[] = $availability[$i + 1];
            }

            // get all dates between each startDate - endDate pair
            foreach ($startDates as $key => $val) {
                $dates = self::getAllDatesInInterval($val, $endDates[$key], '+1 day', 'd-m-Y');
                $vacations = array_merge($vacations, $dates);
            }
        }

        $freeDays = array();
        foreach ($weekDays as $weekDay) {
            if(!empty($weekDay)) {
                // get the date of the nearest weekday at hand
                $day = date('d-m-Y', strtotime("next " . self::getWeekdayFromIndex($weekDay, false), strtotime(date('d-m-Y'))));

                // get all dates of the current weekday for the next 6 months
                $freeDays = array_merge($freeDays, self::getAllDatesInInterval($day, date('d-m-Y', strtotime("+6 months")), '+1 week', 'd-m-Y'));
            }
        }
        $vacations = array_merge($vacations, $freeDays);

        return $vacations;
    }

    /**
     * Method that gets the raw information about the work and break hours for a provider and
     * organizes them between the start and work hours (with an interval equal
     * to the service duration) excluding the break hours intervals, into an array.
     *
     * @param $hours array containing all information about the work, break hours and the service duration
     * @return array|bool organized array containing the available hours
     */
    public static function processProviderAvailableHours($hours) {
        $workHours = array();
        $breakHours = array();
        foreach($hours as $result){
            if($result->type == STAFF_WORK_HOURS) {
                $workHours["start_hour"] = $result->start_hour;
                $workHours["end_hour"] = $result->end_hour;
            }
            else {
                $breakHours["start_hour"][] =  $result->start_hour;
                $breakHours["end_hour"][] = $result->end_hour;
            }
        }

        $minutes = $hours[0]->duration;

        // if duration not available, default to 1 hour (60 minutes)
        if(empty($minutes))
            $minutes = 60;

        if(!isset($workHours['start_hour']))
            return false;

        $now = strtotime($workHours["start_hour"]);
        $end = strtotime($workHours["end_hour"]);

        $hours = array();
        while($now <= $end) {
            $hours[] = date('H:i:s', $now);
            $now = strtotime('+ '.$minutes.' minutes', $now);
        }

        $availableHours = array();

        $i = 0;
        $previousBreak = false;
        foreach($hours as $key=>$val) {
            if(isset($breakHours["start_hour"][$i])) {
                if (strtotime($val) >= strtotime($breakHours["start_hour"][$i]) && strtotime($val) <= strtotime($breakHours["end_hour"][$i])) {
                    unset($hours[$key]);
                    $previousBreak = true;

                } else {
                    if ($previousBreak) {
                        $i++;
                        $previousBreak = false;
                    }
                }
            }

            if(isset($hours[$key])) {
                if(strtotime($val) < strtotime('12:00:00'))
                    $availableHours["morning"][] = $val;
                else if(strtotime($val) >= strtotime('12:00:00') && strtotime($val) < strtotime('18:00:00'))
                    $availableHours["afternoon"][] = $val;
                else if(strtotime($val) >= strtotime('18:00:00'))
                    $availableHours["evening"][] = $val;
            }
        }

        return $availableHours;
    }

    /**
     *
     * update company,offer or events data based on the default attributes
     *
     * @param $item object contain all the company data
     * @param $defaultAttribute array with the application configuration
     * @return mixed the updated object of company,offer or events
     */
    public static function updateItemDefaultAtrributes(&$item,$defaultAttribute){

        if (isset($item->website) && !empty($item->website) )                             $item->website           = $defaultAttribute["website"]          !=ATTRIBUTE_NOT_SHOW?$item->website:"";
        if (isset($item->keywords) && !empty($item->keywords))                            $item->keywords          = $defaultAttribute["keywords"]         !=ATTRIBUTE_NOT_SHOW?$item->keywords:"";
        if (isset($item->categories) && !empty($item->categories))                        $item->categories        = $defaultAttribute["category"]         !=ATTRIBUTE_NOT_SHOW?$item->categories:"";
        if (isset($item->logoLocation) && !empty($item->logoLocation))                    $item->logoLocation      = $defaultAttribute["logo"]             !=ATTRIBUTE_NOT_SHOW?$item->logoLocation:"";
        if (isset($item->street_number) && !empty($item->street_number))                  $item->street_number     = $defaultAttribute["street_number"]    !=ATTRIBUTE_NOT_SHOW?$item->street_number:"";
        if (isset($item->address) && !empty($item->address))                              $item->address           = $defaultAttribute["address"]          !=ATTRIBUTE_NOT_SHOW?$item->address:"";
        if (isset($item->city) && !empty($item->city))                                    $item->city              = $defaultAttribute["city"]             !=ATTRIBUTE_NOT_SHOW?$item->city:"";
        if (isset($item->county) && !empty($item->county))                                $item->county            = $defaultAttribute["region"]           !=ATTRIBUTE_NOT_SHOW?$item->county:"";
        if (isset($item->countryId) && !empty($item->countryId))                          $item->countryId         = $defaultAttribute["country"]          !=ATTRIBUTE_NOT_SHOW?$item->countryId:"";
        if (isset($item->countryName) && !empty($item->countryName))                      $item->countryName       = $defaultAttribute["country"]          !=ATTRIBUTE_NOT_SHOW?$item->countryName:"";
        if (isset($item->country_name) && !empty($item->country_name))                    $item->country_name      = $defaultAttribute["country"]          !=ATTRIBUTE_NOT_SHOW?$item->country_name:"";
        if (isset($item->postalCode) && !empty($item->postalCode))                        $item->postalCode        = $defaultAttribute["postal_code"]      !=ATTRIBUTE_NOT_SHOW?$item->postalCode:"";
        if (isset($item->phone) && !empty($item->phone))                                  $item->phone             = $defaultAttribute["phone"]            !=ATTRIBUTE_NOT_SHOW?$item->phone:"";
        if (isset($item->mobile) && !empty($item->mobile))                                $item->mobile            = $defaultAttribute["mobile_phone"]     !=ATTRIBUTE_NOT_SHOW?$item->mobile:"";
        if (isset($item->fax) && !empty($item->fax))                                      $item->fax               = $defaultAttribute["fax"]              !=ATTRIBUTE_NOT_SHOW?$item->fax:"";
        if (isset($item->email) && !empty($item->email))                                  $item->email             = $defaultAttribute["email"]            !=ATTRIBUTE_NOT_SHOW?$item->email:"";
        if (isset($item->short_description) && !empty($item->short_description))          $item->short_description = $defaultAttribute["short_description"]!=ATTRIBUTE_NOT_SHOW?$item->short_description:"";
        if (isset($item->province) && !empty($item->province))                            $item->province          = $defaultAttribute["province"]         !=ATTRIBUTE_NOT_SHOW?$item->province:"";
        if (isset($item->typeName) && !empty($item->typeName))                            $item->typeName          = $defaultAttribute["company_type"]     !=ATTRIBUTE_NOT_SHOW?$item->typeName:"";
        if (isset($item->longitude) && !empty($item->longitude))                          $item->longitude         = $defaultAttribute["map"]              !=ATTRIBUTE_NOT_SHOW?$item->longitude:"";
        if (isset($item->latitude) && !empty($item->latitude))                            $item->latitude          = $defaultAttribute["map"]              !=ATTRIBUTE_NOT_SHOW?$item->latitude:"";
        if (isset($item->taxCode) && !empty($item->taxCode))                              $item->taxCode           = $defaultAttribute["tax_code"]         !=ATTRIBUTE_NOT_SHOW?$item->taxCode:"";
        if (isset($item->comercialName) && !empty($item->comercialName))                  $item->comercialName     = $defaultAttribute["comercial_name"]   !=ATTRIBUTE_NOT_SHOW?$item->comercialName:"";
        if (isset($item->slogan) && !empty($item->slogan))                                $item->slogan            = $defaultAttribute["slogan"]           !=ATTRIBUTE_NOT_SHOW?$item->slogan:"";
        if (isset($item->description) && !empty($item->description))                      $item->description       = $defaultAttribute["description"]      !=ATTRIBUTE_NOT_SHOW?$item->description:"";
        if (isset($item->pictures) && !empty($item->pictures))                            $item->pictures          = $defaultAttribute["pictures"]         !=ATTRIBUTE_NOT_SHOW?$item->pictures:array();
        if (isset($item->videos) && !empty($item->videos))                                $item->videos            = $defaultAttribute["video"]            !=ATTRIBUTE_NOT_SHOW?$item->videos:array();
        if (isset($item->facebook) && !empty($item->facebook))                            $item->facebook          = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW?$item->facebook:"";
        if (isset($item->twitter) && !empty($item->twitter))                              $item->twitter           = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW?$item->twitter:"";
        if (isset($item->googlep) && !empty($item->googlep))                              $item->googlep           = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW?$item->googlep:"";
        if (isset($item->skype) && !empty($item->skype))                                  $item->skype             = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW?$item->skype:"";
        if (isset($item->linkedin) && !empty($item->linkedin))                            $item->linkedin          = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW?$item->linkedin:"";
        if (isset($item->youtube) && !empty($item->youtube))                              $item->youtube           = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW?$item->youtube:"";
        if (isset($item->instagram) && !empty($item->instagram))                          $item->instagram         = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW?$item->instagram:"";
        if (isset($item->pinterest) && !empty($item->pinterest))                          $item->pinterest         = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW?$item->pinterest:"";
        if (isset($item->whatsapp) && !empty($item->whatsapp))                            $item->whatsapp          = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW?$item->whatsapp:"";
        if (isset($item->contacts) && !empty($item->contacts))                            $item->contacts          = $defaultAttribute["contact_person"]   !=ATTRIBUTE_NOT_SHOW?$item->contacts:array();
        if (isset($item->attachments) && !empty($item->attachments))                      $item->attachments       = $defaultAttribute["attachments"]      !=ATTRIBUTE_NOT_SHOW?$item->attachments:array();
        if (isset($item->custom_tab_name) && !empty($item->custom_tab_name))              $item->custom_tab_name   = $defaultAttribute["custom_tab"]       !=ATTRIBUTE_NOT_SHOW?$item->custom_tab_name:"";
        if (isset($item->custom_tab_content) && !empty($item->custom_tab_content))        $item->custom_tab_content= $defaultAttribute["custom_tab"]       !=ATTRIBUTE_NOT_SHOW?$item->custom_tab_content:"";
        if (isset($item->business_hours) && !empty($item->business_hours))                $item->business_hours    = $defaultAttribute["opening_hours"]    !=ATTRIBUTE_NOT_SHOW?$item->business_hours:array();
        if (isset($item->notes_hours) && !empty($item->notes_hours))                      $item->notes_hours       = $defaultAttribute["opening_hours"]    !=ATTRIBUTE_NOT_SHOW?$item->notes_hours:"";
        if (isset($item->meta_title) && !empty($item->meta_title))                        $item->meta_title        = $defaultAttribute["metadata_information"]!=ATTRIBUTE_NOT_SHOW?$item->meta_title:"";
        if (isset($item->meta_description) && !empty($item->meta_description))            $item->meta_description  = $defaultAttribute["metadata_information"]!=ATTRIBUTE_NOT_SHOW?$item->meta_description:"";
        if (isset($item->meta_keywords) && !empty($item->meta_keywords))                  $item->meta_keywords     = $defaultAttribute["metadata_information"]!=ATTRIBUTE_NOT_SHOW?$item->meta_keywords:"";
        if (isset($item->publish_start_date) && !empty($item->publish_start_date))        $item->publish_start_date= $defaultAttribute["publish_dates"]    !=ATTRIBUTE_NOT_SHOW?$item->publish_start_date:"";
        if (isset($item->publish_end_date) && !empty($item->publish_end_date))            $item->publish_end_date  = $defaultAttribute["publish_dates"]    !=ATTRIBUTE_NOT_SHOW?$item->publish_end_date:"";
        if (isset($item->area) && !empty($item->area))                                    $item->area              = $defaultAttribute["area"]             !=ATTRIBUTE_NOT_SHOW?$item->area:"";
        if (isset($item->business_cover_image) && !empty($item->business_cover_image))    $item->business_cover_image= $defaultAttribute["cover_image"]    !=ATTRIBUTE_NOT_SHOW?$item->business_cover_image:"";
        if (isset($item->contact_phone) && !empty($item->contact_phone))                  $item->contact_phone     = $defaultAttribute["phone"]            !=ATTRIBUTE_NOT_SHOW?$item->contact_phone:"";
        if (isset($item->contact_email) && !empty($item->contact_email))                  $item->contact_email     = $defaultAttribute["email"]            !=ATTRIBUTE_NOT_SHOW?$item->contact_email:"";
        if (isset($item->establishment_year) && !empty($item->establishment_year))        $item->establishment_year= $defaultAttribute["establishment_year"]!=ATTRIBUTE_NOT_SHOW?$item->establishment_year:"";
        if (isset($item->employees) && !empty($item->employees))                          $item->employees         = $defaultAttribute["employees"]        !=ATTRIBUTE_NOT_SHOW?$item->employees:"";
        if (isset($item->ad_image) && !empty($item->ad_image))                            $item->ad_image          = $defaultAttribute["ad_images"]        !=ATTRIBUTE_NOT_SHOW?$item->ad_image:"";

        return $item;
    }

    /**
     * This function get all the default business attribute configuration on general settings
     * @return array containing all the default attribute and their configuration
     */
    public static function getAttributeConfiguration(){
        $defaultAttributesTable = JTable::getInstance('DefaultAttributes','Table');
        $attributesConfiguration = $defaultAttributesTable->getAttributesConfiguration();
        $defaultAtrributes= array();
        if(isset($attributesConfiguration) && count($attributesConfiguration)>0){
            foreach($attributesConfiguration as $attrConfig){
                $defaultAtrributes[$attrConfig->name] = $attrConfig->config;
            }
        }

        return $defaultAtrributes;
    }

    /**
     * Method that returns a complex array organized in a way that it may
     * be simply used in a view to display the work and break hours.
     *
     * The main array will have 7 objects, one for each day of the week.
     * Each of these objects will contain the name of the day, and the
     * break and work hours for that particular day.
     *
     * @param $workHours array containing the work hours for all days of the week
     * @param $breakHours array containing the break hours for all days of the week
     *
     * @return array
     */
    public static function getWorkingDays($workHours, $breakHours)
    {
        $workingDays = array();

        for ($i = 1; $i <= 7; $i++) {
            $day = new stdClass();

            $day->workHours['start_time'] = '';
            $day->workHours['end_time'] = '';
            $day->workHours['id'] = '';
            $day->workHours['status'] = '';
            $day->workHours['start_time'] = '';
            $day->workHours['end_time'] = '';
            if (!empty($workHours) || !empty($breakHours)) {
                // Arrange the working hours
                $day->workHours['start_time'] = $workHours[$i - 1]->startHours;
                $day->workHours['end_time'] = $workHours[$i - 1]->endHours;
                $day->workHours['id'] = $workHours[$i - 1]->periodIds;
                $day->workHours['status'] = $workHours[$i - 1]->statuses;
                $day->workHours['start_time'] = self::convertTimeToFormat($day->workHours['start_time']);
                $day->workHours['end_time'] = self::convertTimeToFormat($day->workHours['end_time']);

                // Arrange the break hours
                if (!empty($breakHours[$i])) {
                    $day->breakHours = array();
                    $startHours = explode(',', $breakHours[$i]->startHours);
                    $endHours = explode(',', $breakHours[$i]->endHours);
                    $breakIds = explode(',', $breakHours[$i]->periodIds);
                    $n = count($startHours);
                    if ($n > 0) {
                        for ($j = 0; $j < $n; $j++) {
                            $day->breakHours['start_time'][$j] = $startHours[$j];
                            $day->breakHours['end_time'][$j] = $endHours[$j];
                            $day->breakHours['id'][$j] = $breakIds[$j];
                        }
                    } else {
                        $day->breakHours['start_time'][] = null;
                        $day->breakHours['end_time'][] = null;
                    }
                }
            } else {
                $day->workHours['start_time'] = null;
                $day->workHours['end_time'] = null;
                $day->workHours['id'] = null;
                $day->breakHours[0]['start_time'] = null;
                $day->breakHours[0]['end_time'] = null;
            }

            $day->name = self::getWeekdayFromIndex($i);
            $workingDays[$i] = $day;
        }

        return $workingDays;
    }

    /**
     * This function generate an array with all the timezones to be used for a good user experience
     *
     * @return array contain all the timezones
     */
    public static function timeZonesList() {

        return $timezoneTable = array(
                    "-11" => "(GMT -11:00) Midway Island, Samoa",
                    "-10" => "(GMT -10:00) Hawaii",
                    "-9" => "(GMT -9:00) Alaska",
                    "-8" => "(GMT -8:00) Pacific Time (US &amp; Canada)",
                    "-7" => "(GMT -7:00) Mountain Time (US &amp; Canada)",
                    "-6" => "(GMT -6:00) Central Time (US &amp; Canada), Mexico City",
                    "-5" => "(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima",
                    "-4" => "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz",
                    "-3" => "(GMT -3:00) Brazil, Buenos Aires, Georgetown",
                    "-1" => "(GMT -1:00) Azores, Cape Verde Islands",
                    "0" => "(GMT) Western Europe Time, London, Lisbon, Casablanca",
                    "1" => "(GMT +1:00) Brussels, Copenhagen, Madrid, Paris",
                    "2" => "(GMT +2:00) Kaliningrad, South Africa, Romania, Turkey, Greece",
                    "3" => "(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg",
                    "4" => "(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi",
                    "5" => "(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent",
                    "5.5" => "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi",
                    "7" => "(GMT +7:00) Bangkok, Hanoi, Jakarta",
                    "8" => "(GMT +8:00) Beijing, Perth, Singapore, Hong Kong",
                    "9" => "(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk",
                    "10" => "(GMT +10:00) Eastern Australia, Guam, Vladivostok",
                    "12" => "(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka"
                );

    }

    /**
     * Get all checklist elements organized in an array, each element
     * having a name and status field. Status field is set to 0 by
     * default.
     *
     * @param $type int business->1 offer->2 event->3
     * @return array checklist array
     */
    public static function getChecklist($type=1) {
        $checklist = array();
        $checklist['description'] = new stdClass();
        $checklist['address'] = new stdClass();

        // Business fields
        if($type == 1) {
            $checklist['email'] = new stdClass();
            $checklist['keywords'] = new stdClass();
            $checklist['website'] = new stdClass();
            $checklist['logo'] = new stdClass();
            $checklist['cover_image'] = new stdClass();
            $checklist['social_networks'] = new stdClass();
        }
        // Offer fields
        else if($type == 2) {
            $checklist['duration'] = new stdClass();
            $checklist['pictures'] = new stdClass();
            $checklist['price'] = new stdClass();
        }
        // Event fields
        else if($type == 3) {
            $checklist['email'] = new stdClass();
            $checklist['duration'] = new stdClass();
            $checklist['phone'] = new stdClass();
            $checklist['pictures'] = new stdClass();
        }

        foreach($checklist as $key=>$val) {
            $val->name = JText::_('LNG_'.strtoupper($key));
            $val->status = 0;
        }

        return $checklist;
    }

    /**
     * Check each field of the item (business/offer/event) relative to the checklist, and
     * change the status (to 1) if the respective field is already completed.
     *
     * @param $item array containing all relevant information about the company/offer/event
     * @param $type int business->1 offer->2 event->3, default business(1)
     * @return array checklist array with updated statuses
     */
    public static function getCompletionProgress($item, $type) {
        $progress = self::getChecklist($type);

        $attributes = self::getAttributeConfiguration();
        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        if ((!$appSettings->apply_attr_offers && $type==2) || (!$appSettings->apply_attr_events && $type==3)){
            foreach ($attributes as &$config)
                $config = ATTRIBUTE_OPTIONAL ;
        }

        $showAddress = $attributes["street_number"]!=ATTRIBUTE_NOT_SHOW || $attributes["address"]!=ATTRIBUTE_NOT_SHOW ||$attributes["area"]!=ATTRIBUTE_NOT_SHOW
            || $attributes["country"]!=ATTRIBUTE_NOT_SHOW || $attributes["city"]!=ATTRIBUTE_NOT_SHOW
            || $attributes["province"]!=ATTRIBUTE_NOT_SHOW || $attributes["region"]!=ATTRIBUTE_NOT_SHOW
            || $attributes["postal_code"]!=ATTRIBUTE_NOT_SHOW || $attributes["map"]!=ATTRIBUTE_NOT_SHOW;

        if($type == 1) {
            if (!empty($item->description))
                $progress['description']->status = 1;
            if (!empty($item->email))
                $progress['email']->status = 1;
            $address = self::getAddressText($item);
            if (!empty($address))
                $progress['address']->status = 1;
            if (!empty($item->logoLocation))
                $progress['logo']->status = 1;
            if (!empty($item->keywords))
                $progress['keywords']->status = 1;
            if (!empty($item->website))
                $progress['website']->status = 1;
            if (!empty($item->business_cover_image))
                $progress['cover_image']->status = 1;
            if (!empty($item->facebook) || !empty($item->twitter) || !empty($item->googlep) || !empty($item->skype)
                || !empty($item->linkedin) || !empty($item->youtube) || !empty($item->instagram) || !empty($item->pinterest))
                $progress['social_networks']->status = 1;
        }
        else if($type == 2) {
            if (!empty($item->description))
                $progress['description']->status = 1;
            if (!empty($item->price))
                $progress['price']->status = 1;
            $address = self::getAddressText($item);
            if (!empty($address))
                $progress['address']->status = 1;
            if (!empty($item->picture_path))
                $progress['pictures']->status = 1;
            if (!self::emptyDate($item->startDate) || !self::emptyDate($item->endDate))
                $progress['duration']->status = 1;
        }
        else if($type == 3) {
            if (!empty($item->description))
                $progress['description']->status = 1;
            if (!empty($item->contact_email))
                $progress['email']->status = 1;
            if (!empty($item->contact_phone))
                $progress['phone']->status = 1;
            $address = self::getAddressText($item);
            if (!empty($address))
                $progress['address']->status = 1;
            if (!empty($item->picture_path))
                $progress['pictures']->status = 1;
            if (!self::emptyDate($item->start_date) || !self::emptyDate($item->end_date))
                $progress['duration']->status = 1;
        }

        if(!$showAddress)
            unset($progress['address']);

        if($type == 1){
            $table = JTable::getInstance("Package", "JTable");
            $item->package = $table->getCurrentActivePackage($item->id);
        }

        foreach($progress as $key=>$val) {
            if ($type == 1){
                $checkFeature = false;
                if ($key == "logo"){
                    $feature = "company_logo";
                    $checkFeature = !(isset($item->package->features) && in_array($feature, $item->package->features) || !$appSettings->enable_packages);
                }elseif ($key == "website"){
                    $feature = "website_address";
                    $checkFeature = !(isset($item->package->features) && in_array($feature, $item->package->features) || !$appSettings->enable_packages);
                }elseif ($key == "description"){
                    $checkFeature = !(isset($item->package->features) && in_array($key, $item->package->features) || !$appSettings->enable_packages);
                }elseif ($key == "social_networks"){
                    $checkFeature = !(isset($item->package->features) && in_array($key, $item->package->features) || !$appSettings->enable_packages);
                }
                if($attributes[$key] == ATTRIBUTE_NOT_SHOW || $checkFeature) {
                    unset($progress[$key]);
                }
            }else{
                if(isset($attributes[$key]) &&  $attributes[$key] == ATTRIBUTE_NOT_SHOW)
                    unset($progress[$key]);
            }
        }

        return $progress;
    }

    /**
     * Creates an object containing all necessary fields that will be
     * mostly used by js functions. These fields will be included in the
     * JBDUtils object on js.
     *
     * @return stdClass
     */
    public static function addJSSettings() {
        $jsSettings = new stdClass();
        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $jsSettings->baseUrl = (JURI::base() . 'index.php?option=' . JBusinessUtil::getComponentName());
        $jsSettings->imageRepo = JURI::root() . 'administrator/components/com_jbusinessdirectory';
        $jsSettings->imageBaseUrl = (JURI::root() . PICTURES_PATH);
        $jsSettings->siteRoot = JURI::root();
        $jsSettings->componentName = JBusinessUtil::getComponentName();
        $jsSettings->timeFormat = $appSettings->time_format;
        $jsSettings->dateFormat = $appSettings->dateFormat;
        $jsSettings->mapMarker = $appSettings->map_marker;
        $jsSettings->mapDefaultZoom = (int)$appSettings->map_zoom;
        $jsSettings->enable_attribute_category = $appSettings->enable_attribute_category;
        $jsSettings->enable_packages = $appSettings->enable_packages;
        $jsSettings->isMultilingual = $appSettings->enable_multilingual ? true : false;
        $jsSettings->validateRichTextEditors = false;
        $jsSettings->logo_width = $appSettings->logo_width;
        $jsSettings->logo_height = $appSettings->logo_height;
        $jsSettings->cover_width = $appSettings->cover_width;
        $jsSettings->cover_height = $appSettings->cover_height;
        $jsSettings->gallery_width = $appSettings->gallery_width;
        $jsSettings->gallery_height = $appSettings->gallery_height;
        $jsSettings->enable_crop = $appSettings->enable_crop ? true : false;
        $jsSettings->enable_resolution_check = $appSettings->enable_resolution_check ? true : false;

        $langTab = JFactory::getLanguage()->getTag();
        $langTab = str_replace("-", "_", $langTab);
        $jsSettings->langTab = $langTab;

        $jsSettings->defaultLang = JFactory::getLanguage()->getTag();

        return $jsSettings;
    }

    /**
     * Get available user groups
     */
    public static function getUserGroups(){
        //$user = JFactory::getUser();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level')
                ->from($db->quoteName('#__usergroups') . ' AS a')
                ->join('LEFT', $db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
                ->group('a.id, a.title, a.lft, a.rgt')
                ->order('a.lft ASC');
        $db->setQuery($query);
        $options = $db->loadObjectList();

        foreach ($options as &$option){
            $option->name = str_repeat('- ', $option->level) . $option->text;
        }

        return $options;
    }

    /**
     * Method to retrieve the ID of an extension based by it's type and name
     *
     * @param $type string type of the extension
     * @param $name string name of the extension
     * @return mixed
     */
    public static function getExtensionID($type, $name) {
        $db = JFactory::getDbo();

        $query = "select ext.extension_id 
                  from #__extensions as ext
                  where ext.type='$type' and ext.element = '$name'
                  ";

        $db->setQuery($query);
        $result = $db->loadObject();

        return $result->extension_id;
    }

    /**
     *  Return the id of the country based on hic code
     * @param $countryCode
     * @return mixed
     */
    public static function getCountryIDByCode($countryCode){
        $db = JFactory::getDbo();

        $query = "select co.id 
                  from #__jbusinessdirectory_countries as co
                  where co.country_code = '$countryCode'
                  ";

        $db->setQuery($query);
        $result = $db->loadObject();

        return $result->id;
    }

    /**
     * This function get the content as a string and removes all rel attributes on anchor links if there is any
     *
     * @param $string string content that will be analyzed and where the rel attribute will be removed
     * @return mixed string content with rel attributes removed
     */
    public static function removeRelAttribute($string){

        $occurs = substr_count($string, 'rel=');
        for ($i=0;$i<$occurs;$i++){
            $pos = strpos($string, 'rel=', 0); // find the first position where the rel starts
            if ($pos === false)
                break;
            $pos2 = strpos($string, '"', $pos+6) + 1; // find the last position of " symbol where the rel attributes ends
            $length = $pos2 - $pos; // length of the rel attribute
            $text = substr($string, $pos, $length);
            $string = str_replace($text, '', $string);
        }

        return $string;
    }

    /**
     * Method that groups all attributes together in an array based on the group name.
     * The indexes of the final 2-dimensional array will correspond to the group, and all
     * attributes belonging to that group will be inside an array in that particular index.
     *
     * An extra index called ungrouped is created for the attributes with no group.
     *
     * @param $attributes array containing all attributes with their relevant fields
     *
     * @return array
     *
     * @since 4.9.0
     */
    public static function arrangeAttributesByGroup($attributes) {
        $groupedAttr = array();
        $groupedAttr['ungrouped'] = array();
        foreach ($attributes as $attribute) {
            if (!empty($attribute->group)) {
                if (!isset($groupedAttr[$attribute->group]))
                    $groupedAttr[$attribute->group] = array();

                $groupedAttr[$attribute->group][] = $attribute;
            } else {
                $groupedAttr['ungrouped'][] = $attribute;
            }
        }

        return $groupedAttr;
    }
}
?>