<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');
require_once( JPATH_COMPONENT_ADMINISTRATOR.'/library/category_lib.php');

class JBusinessDirectoryModelEvents extends JModelList{ 
	
	function __construct(){
		
		parent::__construct();

        $this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $jinput = JFactory::getApplication()->input;

		$this->searchFilter = array();
		
		$this->keyword = $jinput->getString('searchkeyword');
		
		$this->categoryId = $jinput->getInt('categoryId',null);
		if(empty($this->categoryId)){
			$this->categoryId = $jinput->getInt('eventCategoryId',null);
		}
		$this->typeSearch = $jinput->get('typeSearch',null);
		$this->categorySearch = $jinput->get('categorySearch',null);
		$this->menuCategoryId = $jinput->getString('menuCategoryId',null);
        $this->areaSearch = $jinput->getString('areaSearch',null);
        $this->provinceSearch = $jinput->getString('provinceSearch',null);
        $this->startDate = $jinput->get('startDate');
		$this->endDate = $jinput->get('endDate');
		$this->zipCode = $jinput->getString('zipcode');
		$this->preserve = $jinput->getString('preserve',null);
		$this->companyId = $jinput->get('companyId',null);
		$this->citySearch = $jinput->getString('citySearch',null);
		$this->regionSearch = $jinput->getString('regionSearch',null);
        $this->countrySearch = $jinput->get('countrySearch',null);
        $this->radius = $jinput->get('radius');
		$this->orderBy = $jinput->get("orderBy", $this->appSettings->order_search_events);
		$this->days = $jinput->get('days');
		
		$allowedValues = $this->getSortByConfiguration();
		if(!JBusinessUtil::validateOrderBy($this->orderBy, $allowedValues)){
		    $this->orderBy = $this->appSettings->order_search_events;
		}
		
		
		$resetSearch = $jinput->get('resetSearch',null);
		
		
		if(isset($this->categorySearch) && empty($this->categoryId) &&  isset($this->preserve)){
			$this->categoryId = $this->categorySearch;
		}
		
		if(!empty($this->menuCategoryId) && empty($this->categoryId) && !isset($this->preserve)){
			$this->categoryId = $this->menuCategoryId;
		}
		
		if(isset($this->categoryId)){
			$this->categoryId = intval($this->categoryId);
		}
		
		$session = JFactory::getSession();
		if(isset($this->categoryId) || !empty($resetSearch)){
			$session->set('ev-categorySearch', $this->categoryId);
			$session->set('ev-searchkeyword', "");
			$session->set('ev-typeSearch',"");
			$session->set('ev-startDate',"");
            $session->set('ev-countrySearch',"");
            $session->set('ev-endDate',"");
			$session->set('ev-citySearch',"");
            $session->set('ev-regionSearch',"");
            $session->set('ev-zipcode',"");
            $session->set('ev-provinceSearch',"");
        }
		
		if(!empty($resetSearch)){
			$session->set('categoryId', $this->categoryId);
		}
		
		if(isset($this->typeSearch)){
			$this->typeSearch = intval($this->typeSearch);
			$session->set('ev-typeSearch', $this->typeSearch);
		}
		
		if(isset($this->startDate)){
			$session->set('ev-startDate', $this->startDate);
		}else if(!empty($this->days)){
			$this->startDate = date($this->appSettings->dateFormat);
			$session->set('ev-startDate', $this->startDate);
		}
		
		if(isset($this->endDate)){
			$session->set('ev-endDate', $this->endDate);
		}else if(!empty($this->days)){
			$this->endDate = date($this->appSettings->dateFormat, strtotime("+$this->days days"));
			$session->set('ev-endDate', $this->endDate);
		}
		
		if(isset($this->keyword)){
			$session->set('ev-searchkeyword', $this->keyword);
		}
		
		if(isset($this->citySearch)){
			$session->set('ev-citySearch', $this->citySearch);
		}

        if(isset($this->provinceSearch)){
            $session->set('ev-provinceSearch', $this->provinceSearch);
        }

		if(isset($this->regionSearch)){
			$session->set('ev-regionSearch', $this->regionSearch);
		}

        if(isset($this->countrySearch)){
            $this->countrySearch = intval($this->countrySearch);
            $session->set('ev-countrySearch', $this->countrySearch);
        }
		
		if(isset($this->zipCode)){
			$session->set('ev-zipcode', $this->zipCode);
		}
		
		if(isset($this->radius)){
			$this->radius = intval($this->radius);
			$session->set('ev-radius', $this->radius);
		}
		
		
		$this->keyword = $session->get('ev-searchkeyword');
		$this->startDate = $session->get('ev-startDate');
		$this->typeSearch = $session->get('ev-typeSearch');
		$this->endDate = $session->get('ev-endDate');
		$this->categorySearch = $session->get('ev-categorySearch');
		$this->citySearch = $session->get('ev-citySearch');
		$this->regionSearch = $session->get('ev-regionSearch');
		$this->zipCode = $session->get('ev-zipcode');
		$this->radius = $session->get('ev-radius');
        $this->countrySearch = $session->get('ev-countrySearch');
        $this->provinceSearch = $session->get('ev-provinceSearch');

        $this->location = null;

		$geolocation = $jinput->get('geolocation',null);
		if(isset($geolocation)){
			$session->set("geolocation",$geolocation);
		}
		$geolocation = $session->get("geolocation");
		// test if geo location is determined and set location array
		if($geolocation){
			$geoLatitutde = $jinput->get('geo-latitude',null);
			$geoLongitude = $jinput->get('geo-longitude',null);

			if(!empty($geoLatitutde)){
				$session->set('geo-latitude', $geoLatitutde);
			}
			if(!empty($geoLongitude)){
				$session->set('geo-longitude', $geoLongitude);
			}
			$geoLatitutde = $session->get('geo-latitude');
			$geoLongitude = $session->get('geo-longitude');

			if(!empty($geoLatitutde) && !empty($geoLongitude)){
				$this->location =  array();
				$this->location["latitude"] = $geoLatitutde;
				$this->location["longitude"] = $geoLongitude;
			}
		}
		
		if($this->appSettings->metric==0){
			$this->radius  = $this->radius * 0.621371;
		}
		
		$mainframe = JFactory::getApplication();
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$this->enablePackages = $appSettings->enable_packages;
		$this->showPendingApproval = $appSettings->show_pending_approval==1;
		
		if(isset($this->zipCode) && $this->zipCode!=""){
			$this->location = JBusinessUtil::getCoordinates($this->zipCode);
		}

		if(!empty($this->location)){
			$session->set("location",$this->location);
		}
		
	}

	function getSearchParameters(){
        $categories = $this->getSelectedCategories();
		$categoryService = new JBusinessDirectorCategoryLib();

        $categoriesIds = array();

        if(!empty($categories) && ($this->appSettings->search_type==1 && $this->appSettings->enable_search_filter==1)) {
            foreach($categories as $category){
                $categoriesLevel= array();
                $cats = $categoryService->getCategoryLeafs($category, CATEGORY_TYPE_EVENT);
                //dump($category);
                //dump($cats);
                if(isset($cats)){
                    $categoriesLevel = array_merge($categoriesLevel,$cats);
                }
                $categoriesLevel[] = $category;
                $categoriesIds[] = implode(",",$categoriesLevel);
            }
        }else if(!empty($this->categoryId) && ($this->appSettings->search_type==0 || $this->appSettings->enable_search_filter==0)){
            $categoriesIds = $categoryService->getCategoryLeafs($this->categoryId, CATEGORY_TYPE_EVENT);

            if(isset($this->categoryId) && $this->categoryId !=0){
                if(isset($categoriesIds) && count($categoriesIds) > 0 ){
                    $categoriesIds[] = $this->categoryId;
                }else{
                    $categoriesIds = array($this->categoryId);
                }
            }
            $categoriesIds = array_filter($categoriesIds);
            $categoriesIds = array(implode(",", $categoriesIds));
        }
        
        $params = $this->getSelectedParams();
        if(isset($params["type"]))
            $this->typeSearch = $params["type"][0];

        if(isset($params["region"]))
            $this->regionSearch = $params["region"][0];

        if(isset($params["city"]))
            $this->citySearch = $params["city"][0];

        if(isset($params["area"]))
            $this->areaSearch = $params["area"][0];

        if(isset($params["country"]))
            $this->countrySearch = $params["country"][0];

        if(isset($params["province"]))
            $this->provinceSearch = $params["province"][0];

		$searchDetails = array();
		$searchDetails["keyword"] = $this->keyword;
		$searchDetails["categoriesIds"] = $categoriesIds;
		$searchDetails["startDate"] = JBusinessUtil::convertToMysqlFormat($this->startDate);
		$searchDetails["endDate"] = JBusinessUtil::convertToMysqlFormat($this->endDate);
		$searchDetails["typeSearch"] = $this->typeSearch;
		$searchDetails["companyId"] = $this->companyId;
		$searchDetails["citySearch"] = $this->citySearch;
		$searchDetails["regionSearch"] = $this->regionSearch;
        $searchDetails["areaSearch"] = $this->areaSearch;
        $searchDetails["facetedSearch"] = $this->appSettings->search_type;
        $searchDetails["countrySearch"] = $this->countrySearch;
        $searchDetails["provinceSearch"] = $this->provinceSearch;


        if(!empty($this->location)){
			$searchDetails["latitude"] = $this->location["latitude"];
			$searchDetails["longitude"] = $this->location["longitude"];
		}

		$searchDetails["radius"] = $this->radius;
		$searchDetails["enablePackages"] = $this->enablePackages;
		$searchDetails["showPendingApproval"] = $this->showPendingApproval;
		$searchDetails["orderBy"] = $this->orderBy;
		$searchDetails["multilingual"] = $this->appSettings->enable_multilingual;
	
		return $searchDetails;
	}
	
	/**
	 * Method to get a cache id based on the search results.
	 *
	 * This is necessary because the different search parameters are used
	 *
	 * @param   string  $id  An identifier string to generate the cache id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   12.2
	 */
	protected function getCacheId($params, $id = '')
	{
		if(!empty($params)){
			$params = array_filter($params);
			foreach($params as $param){
				if(is_array($param)){
					$id .= ':'.implode(",", $param);
				}else{
					$id .= ':'.$param;
				}
			}
		}
		// Add the list state to the store id.
		$id .= ':'.$this->getState('list.start');
		$id .= ':'.$this->getState('list.limit');
		$id .= ':'.$this->getState('list.ordering');
		$id .= ':'.$this->getState('list.direction');
	
		return md5($this->context . ':' . $id);
	}
	
	function getTotalEvents()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$eventsTable = JTable::getInstance("Event", "JTable");
			$searchDetails = $this->getSearchParameters();

			
			if($this->appSettings->enable_cache){
				$cacheIdentifier = $this->getCacheId($searchDetails,"getTotal");
				try
				{
					$cache = JCache::getInstance();
					$this->_total = $cache->get($cacheIdentifier);
					if(empty($this->_total)){
						$this->_total = $eventsTable->getTotalEventsByCategories($searchDetails);
						$cache->store($this->_total, $cacheIdentifier);
					}
				}catch (RuntimeException $e){
					$this->setError($e->getMessage());
					return null;;
				}
			}
				
			if(empty($this->_total)){
				$this->_total = $eventsTable->getTotalEventsByCategories($searchDetails);
			}
		}
		return $this->_total;
	}
	
	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
		    require_once( JPATH_SITE.'/components/com_jbusinessdirectory/libraries/dirpagination.php');
		    $this->_pagination = new JBusinessDirectoryPagination($this->getTotalEvents(), $this->getState('limitstart'), $this->getState('limit') );
			$this->_pagination->setAdditionalUrlParam('controller','search');
			if(isset($this->categoryId) && $this->categoryId!='')
				$this->_pagination->setAdditionalUrlParam('categoryId',$this->categoryId);
			$this->_pagination->setAdditionalUrlParam('categoryId',$this->categoryId);
			if(isset($this->categorySearch) && $this->categorySearch!='')
				$this->_pagination->setAdditionalUrlParam('categorySearch',$this->categorySearch);
			if(isset($this->keyword) && $this->keyword!='')
				$this->_pagination->setAdditionalUrlParam('searchkeyword',$this->keyword);

			$orderBy = JRequest::getVar("orderBy", $this->appSettings->order_search_events);
			if(!empty($orderBy))
				$this->_pagination->setAdditionalUrlParam('orderBy',$orderBy);
				
			if(!empty($this->citySearch))
				$this->_pagination->setAdditionalUrlParam('citySearch',$this->citySearch);
				
			if(!empty($this->zipCode))
				$this->_pagination->setAdditionalUrlParam('zipcode',$this->zipCode);
				
			if(!empty($this->regionSearch))
				$this->_pagination->setAdditionalUrlParam('regionSearch',$this->regionSearch);

            if(!empty($this->countrySearch))
                $this->_pagination->setAdditionalUrlParam('countrySearch',$this->countrySearch);

            if(!empty($this->provinceSearch))
                $this->_pagination->setAdditionalUrlParam('provinceSearch',$this->provinceSearch);

			if(!empty($this->typeSearch))
				$this->_pagination->setAdditionalUrlParam('typeSearch',$this->typeSearch);
				
			if(!empty($this->radius))
				$this->_pagination->setAdditionalUrlParam('radius',$this->radius);
			if(!empty($this->startDate))
				$this->_pagination->setAdditionalUrlParam('startDate',$this->startDate);
			if(!empty($this->endDate))
				$this->_pagination->setAdditionalUrlParam('endDate',$this->endDate);
				
			if(!empty($this->preserve))
				$this->_pagination->setAdditionalUrlParam('preserve',$this->preserve);
			
			$this->_pagination->setAdditionalUrlParam('view','events');
		}
		return $this->_pagination;
	}
	
	function getEvents(){
		$eventsTable = JTable::getInstance("Event", "JTable");
		$searchDetails = $this->getSearchParameters();
		
		$events = array();
		if($this->appSettings->enable_cache){
			$cacheIdentifier = $this->getCacheId($searchDetails,"Items");
			try
			{
				$cache = JCache::getInstance();
				$events = $cache->get($cacheIdentifier);
				if(empty($events)){
					$events = $eventsTable->getEventsByCategories($searchDetails, $this->getState('limitstart'), $this->getState('limit'));
					$cache->store($events, $cacheIdentifier);
				}
			}catch (RuntimeException $e){
				$this->setError($e->getMessage());
				return null;
			}
		}
		
		if(empty($events)){
			$events = $eventsTable->getEventsByCategories($searchDetails, $this->getState('limitstart'), $this->getState('limit'));
		}
		
		if($searchDetails["orderBy"]=="rand()"){
			shuffle($events);
		}
		
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEventsTranslation($events);
			JBusinessDirectoryTranslations::updateEventTypesTranslation($events);
		}
		
		foreach($events as $event){
			if(empty($event->latitude) && empty($event->longitude) ){
				$event->distance = 0;
			}

            if (!empty($event->categories)) {
                $event->categories = explode('#|', $event->categories);
                foreach ($event->categories as &$category) {
                    $category = explode("|", $category);
                }
            }
            
            if(!empty($event->distance) && $this->appSettings->metric == 0){
                $event->distance = $event->distance * 1.6;
            }
		}
	
        if ($this->appSettings->apply_attr_events) {
        	$attributeConfig = JBusinessUtil::getAttributeConfiguration();
            foreach ($events as $event) {
                $event = JBusinessUtil::updateItemDefaultAtrributes($event, $attributeConfig);
            }
        }

		JRequest::setVar("search-results",$events);
		
		return $events;
	}
	
	function getCalendarEvents(){
		$events = $this->getEvents();
		
		$calendarEvents = array();
		foreach($events as $event){
			$calendarEvent = array();
			$calendarEvent["id"] = $event->id;
			$calendarEvent["title"] = $event->name;
			$calendarEvent["allDay"] = false;
			$calendarEvent["start"] = $event->start_date."T".$event->start_time;
			$calendarEvent["end"] = $event->end_date."T".$event->end_time;
			$calendarEvent["url"] =  JBusinessUtil::getEventLink($event->id, $event->alias);
			$calendarEvent["editable"] = false;
			$calendarEvent["overlap"] = false;
			$calendarEvents[] = $calendarEvent;
		}
		
		return $calendarEvents;		
	}

	function getSeachFilter(){

        if (empty($this->appSettings->search_filter_fields)) {
            return;
        }
		
		$searchDetails = $this->getSearchParameters();
        $searchDetailsCategories = $searchDetails["categoriesIds"];
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$starttime = $mtime;

        $searchDetails["facetedSearch"] = $this->appSettings->search_type;
		$eventsTable = JTable::getInstance("Event", "JTable");

        if (!empty($this->appSettings->search_filter_fields))
            $this->appSettings->search_filter_fields = explode(",", $this->appSettings->search_filter_fields);

        if (in_array("categories", $this->appSettings->search_filter_fields)) {
            $categoryService = new JBusinessDirectorCategoryLib();
            $category = array();
            if (!empty($this->categoryId)  && $this->appSettings->search_type != 1) {
                $category = $categoryService->getCompleteCategoryById($this->categoryId, CATEGORY_TYPE_EVENT);
            } else {
                $category["subCategories"] = $categoryService->getCategories(CATEGORY_TYPE_EVENT);
                $category["path"] = array();
            }
            
            if (empty($category["subCategories"]) && !empty($category[0])) {
                $searchDetails["categoriesIds"] = array($category[0]->parent_id);
            }

            if ($this->appSettings->search_type == 1) {
                $searchDetails["categoriesIds"] = null;
            }
            
            $categoriesTotal = $eventsTable->getTotalEventsByObject($searchDetails, 'category');
            
            if(empty($categoriesTotal) && !(empty($category["subCategories"]) && !empty($category[0])) && $this->appSettings->search_type != 1){
                if(isset($category[0]) && $category[0]->parent_id != 1){
                    $category = $categoryService->getCompleteCategoryById($category[0]->parent_id , CATEGORY_TYPE_EVENT);
                } else {
                    $category["subCategories"] = $categoryService->getCategories(CATEGORY_TYPE_EVENT);
                    $category["path"] = array();
                }
                
                if(isset($category[0])){
                    $searchDetails["categoriesIds"] = array($category[0]->parent_id);
                }
                $categoriesTotal = $eventsTable->getTotalEventsByObject($searchDetails, 'category');
            }
            $subcategories = '';
            $enableSelection = false;

            if ($this->appSettings->enable_multilingual) {
                $categoryTranslations = JBusinessDirectoryTranslations::getCategoriesTranslations();
                foreach ($category["path"] as &$path) {
                    if (!empty($categoryTranslations[$path[0]])) {
                        $path[1] = $categoryTranslations[$path[0]]->name;
                    }
                }
            }

			if (isset($category["path"]))
				$this->searchFilter["path"] = $category["path"];
			
			if (isset($category["subCategories"]) && count($category["subCategories"]) > 0) {
				$subcategories = $category["subCategories"];
			} else {
				if (isset($category["path"])) {
					$parentCategories = $category["path"];

					if ($category[0]->parent_id == 1) {
						$subcategories = $categoryService->getCategories(CATEGORY_TYPE_EVENT);
						$this->searchFilter["enableSelection"] = 1;
						$enableSelection = true;
					} else if (count($parentCategories) > 0) {
						$categoryId = $parentCategories[count($parentCategories)][0];
						//dump($categoryId);
						$parentCategory = $categoryService->getCompleteCategoryById($categoryId, CATEGORY_TYPE_EVENT);
						$subcategories = $parentCategory["subCategories"];
						$this->searchFilter["enableSelection"] = 1;
						$enableSelection = true;
					}
				}
			}

            if (isset($subcategories) && $subcategories != '') {
                if ($this->appSettings->enable_multilingual) {
                    JBusinessDirectoryTranslations::updateCategoriesTranslation($subcategories);
                }
                foreach ($subcategories as $cat) {
                    if (!is_array($cat))
                        continue;

                    $childCategoryIds = $categoryService->getCategoryChilds($cat);
                    if (count($childCategoryIds) == 0) {
                        $childCategoryIds = array($cat[0]->id);
                    } else {
                        $mainCat = array($cat[0]->id);
                        $childCategoryIds = array_merge($mainCat, $childCategoryIds);
                        //$childCategoryIds[] = $cat[0]->id;
                    }

                    $companiesNumber = 0;
                    foreach ($categoriesTotal as $categoryTotal) {
                        if (in_array($categoryTotal->id, $childCategoryIds)) {
                            $companiesNumber += $categoryTotal->nr_events;
                        }
                    }

                    if ($companiesNumber > 0 || $enableSelection)
                        $this->searchFilter["categories"][] = array($cat, $companiesNumber);

                }
            }
            $searchDetails["categoriesIds"] = $searchDetailsCategories;
            $searchDetails["facetedSearch"] = 0;

            if(!empty($this->searchFilter["categories"]) && $this->appSettings->category_order == ORDER_ALPHABETICALLY){
                $this->searchFilter["categories"] = $categoryService->sortCategories($this->searchFilter["categories"],false,false);
            }
        }

        if (in_array("area", $this->appSettings->search_filter_fields)) {
            $areasTotal = $eventsTable->getTotalEventsByObject($searchDetails, "area");
            $areas = array();
            if (!empty($areasTotal)) {
                foreach ($areasTotal as $area) {
                    if(!empty($area->areaName)){
                        if (!isset($areas[$area->areaName])) {
                            $areas[$area->areaName] = $area;
                            $areas[$area->areaName]->nr_events = (int)$area->nr_events;
                        } else {
                            $areas[$area->areaName]->nr_events += $area->nr_events;
                        }
                    }
                }
            }
            $this->searchFilter["areas"] = $areas;
        }

        if (in_array("countries", $this->appSettings->search_filter_fields)) {
            $countriesTotal = $eventsTable->getTotalEventsByObject($searchDetails, "country");
            $countries = array();
            foreach ($countriesTotal as $country) {
                if(!empty($country->countryName)){
                    if (!isset($countries[$country->countryId])) {
                        $countries[$country->countryId] = $country;
                        $countries[$country->countryId]->nr_events = (int)$country->nr_events;
                    } else {
                        $countries[$country->countryId]->nr_events += $country->nr_events;
                    }
                }
            }

            if ($this->appSettings->enable_multilingual) {
                foreach ($countries as $countryData) {
                    $country = new stdClass();
                    $country->id = $countryData->countryId;
                    $country->country_name = $countryData->countryName;

                    JBusinessDirectoryTranslations::updateEntityTranslation($country, COUNTRY_TRANSLATION);
                    $countryData->countryName = $country->country_name;
                }
            }

            $this->searchFilter["countries"] = $countries;
        }

        if (in_array("cities", $this->appSettings->search_filter_fields)) {
            $citiesTotal = $eventsTable->getTotalEventsByObject($searchDetails, "city");
            $cities = array();
            foreach ($citiesTotal as $city) {
                if(!empty($city->cityName)){
                    if (!isset($cities[$city->cityName])) {
                        $cities[$city->cityName] = $city;
                        $cities[$city->cityName]->nr_events = (int)$city->nr_events;
                    } else {
                        $cities[$city->cityName]->nr_events += $city->nr_events;
                    }
                }
            }

            $this->searchFilter["cities"] = $cities;
        }

        if (in_array("regions", $this->appSettings->search_filter_fields)) {
            $regionsTotal = $eventsTable->getTotalEventsByObject($searchDetails, "region");
            $regions = array();
            foreach ($regionsTotal as $region) {
                if(!empty($region->regionName)){
                    if (!isset($regions[$region->regionName])) {
                        $regions[$region->regionName] = $region;
                        $regions[$region->regionName]->nr_events = (int)$region->nr_events;
                    } else {
                        $regions[$region->regionName]->nr_events += $region->nr_events;
                    }
                }
            }

            $this->searchFilter["regions"] = $regions;
        }

        if (in_array("types", $this->appSettings->search_filter_fields)) {
            $typesTotal = $eventsTable->getTotalEventsByObject($searchDetails, "type");
            $types = array();
            if (!empty($typesTotal)) {
                if ($this->appSettings->enable_multilingual) {
                    JBusinessDirectoryTranslations::updateEventTypesTranslation($typesTotal);
                }
                foreach ($typesTotal as $type) {
                    if(!empty($type->typeName)){
                        if (!isset($types[$type->typeId])) {
                            $types[$type->typeId] = $type;
                            $types[$type->typeId]->nr_events = (int)$type->nr_events;
                        } else {
                            $types[$type->typeId]->nr_events += $type->nr_events;
                        }
                    }
                }
            }
            $this->searchFilter["types"] = $types;
        } 

//		$mtime = microtime();
//	    $mtime = explode(" ",$mtime);
//	    $mtime = $mtime[1] + $mtime[0];
//	    $endtime = $mtime;
//	    $totaltime = ($endtime - $starttime);
	    //echo "This function was done in ".$totaltime." seconds";
	    
	   // dump($this->searchFilter);
		return $this->searchFilter;
	}

    function getSelectedParams(){
        $params = array();
        $values = array();
        $this->letter = JRequest::getVar("letter");
        $selectedParams = JRequest::getVar("selectedParams");
        if(!empty($selectedParams)){
            $values = explode(";", $selectedParams);
        }

        foreach($values as $val){
            $temp = explode("=", $val);
            if(!isset($params[$temp[0]]))
                $params[$temp[0]] = array();

            if(!empty($temp[0]))
                array_push($params[$temp[0]], $temp[1]);
        }

        if(!empty($this->categoryId) && !isset($params["category"]))
            $params["category"][] = $this->categoryId;

        if(!empty($this->regionSearch) && !isset($params["region"]))
            $params["region"][] = $this->regionSearch;

        if(!empty($this->citySearch) && !isset($params["city"]))
            $params["city"][] = $this->citySearch;

        if(!empty($this->typeSearch) && !isset($params["type"]))
            $params["type"][] = $this->typeSearch;

        if(!empty($this->areaSearch) && !isset($params["area"]))
            $params["area"][] = $this->areaSearch;

        if(!empty($this->countrySearch) && !isset($params["country"]))
            $params["country"][] = $this->countrySearch;

        if(!empty($this->provinceSearch) && !isset($params["province"]))
            $params["province"][] = $this->provinceSearch;

        foreach($params as $param){
            if(in_array('', $param)){
                unset($param[array_search('', $param)]);
            }
        }

        if(in_array('', $params)){
            unset($params[array_search('', $params)]);
        }

        $params["selectedParams"] = $selectedParams;

        return $params;

    }

    function getSelectedCategories(){
        $categories = array();
        $selectedCat = JRequest::getVar("categories");
        if(!empty($selectedCat)){
            $categories = explode(";", $selectedCat);
        }

        if(!empty($this->categoryId) && !isset($selectedCat)){
            $categories[]=$this->categoryId;
        }

        if (in_array('', $categories))
        {
            unset($categories[array_search('',$categories)]);
        }

        return $categories;
    }
	
	function getCategories(){
		$categoryService = new JBusinessDirectorCategoryLib();
		return $categoryService->getCategories(CATEGORY_TYPE_EVENT);
		
	}	
	
	function getLocation(){
		return $this->location;
	}
	
	function getCategoryId(){
		return $this->categoryId;
	}
	
	function getCategory(){
		$categoryTable = $this->getTable("Category", "JBusinessTable");
		$category = $categoryTable->getCategoryById($this->categoryId);
	
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEntityTranslation($category, CATEGORY_TRANSLATION);
		}
		
		return $category;
	}

	function getEventType(){
		$type = null;
		if(!empty($this->typeSearch)) {
			$eventTypesTable = $this->getTable("EventType","JTable");
			$type = $eventTypesTable->getEventType($this->typeSearch);

			if($this->appSettings->enable_multilingual) {
				JBusinessDirectoryTranslations::updateEntityTranslation($type, TYPE_TRANSLATION);
			}
		}
		return $type;
	}
	
	function getSortByConfiguration(){
		$states = array();
		$state = new stdClass();
		$state->value = '';
		$state->text = JTEXT::_("LNG_RELEVANCE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'id desc';
		$state->text = JTEXT::_("LNG_LAST_ADDED");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'id asc';
		$state->text = JTEXT::_("LNG_FIRST_ADDED");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'start_date asc';
		$state->text = JTEXT::_("LNG_EARLIEST_DATE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'start_date desc';
		$state->text = JTEXT::_("LNG_LATEST_DATE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'name';
		$state->text = JTEXT::_("LNG_NAME");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'city';
		$state->text = JTEXT::_("LNG_CITY");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'distance asc';
		$state->text = JTEXT::_("LNG_DISTANCE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'rand()';
		$state->text = JTEXT::_("LNG_RANDOM");
		$states[] = $state;
	
		return $states;
	}

    function getCountry(){
        $country = null;
        if(!empty($this->countrySearch)){
            $countryTable = $this->getTable("Country","JTable");
            $country =  $countryTable->getCountry($this->countrySearch);
            if($this->appSettings->enable_multilingual){
                JBusinessDirectoryTranslations::updateEntityTranslation($country, COUNTRY_TRANSLATION);
            }
        }

        return $country;
    }
}
?>