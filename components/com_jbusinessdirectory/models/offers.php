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

class JBusinessDirectoryModelOffers extends JModelList
{ 
	
	function __construct()
	{
		parent::__construct();
	
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$jinput = JFactory::getApplication()->input;
		
		$this->searchFilter = array();
	
		$this->keyword = $jinput->getString('searchkeyword');
		$this->categoryId = $jinput->getInt('categoryId',null);
		if(empty($this->categoryId)){
		    $this->categoryId = $jinput->getInt('offerCategoryId',null);
		}
		$this->citySearch = $jinput->getString('citySearch',null);
		$this->areaSearch = $jinput->getString('areaSearch',null);
		$this->regionSearch = $jinput->getString('regionSearch',null);
        $this->countrySearch = $jinput->get('countrySearch',null);
        $this->provinceSearch = $jinput->getString('provinceSearch',null);
        $this->categorySearch = $jinput->get('categorySearch');
		$this->menuCategoryId = $jinput->getString('menuCategoryId',null);
		$this->zipCode = $jinput->getString('zipcode');
		$this->radius = $jinput->get('radius');
		$this->minPrice = $jinput->getString('minprice');
		$this->maxPrice = $jinput->getString('maxprice');
		$this->priceRange = $jinput->getString('price-range');
		$this->radius = $jinput->get('radius');
		$this->preserve = $jinput->getString('preserve',null);
		$this->orderBy = $jinput->getString("orderBy", $this->appSettings->order_search_offers);
	
		$allowedValues = $this->getSortByConfiguration();
		if(!JBusinessUtil::validateOrderBy($this->orderBy, $allowedValues)){
			$this->orderBy = $this->appSettings->order_search_listings;
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
			$session->set('of-categorySearch', $this->categoryId);
			$session->set('of-searchkeyword', "");
            $session->set('of-countrySearch',"");
            $session->set('of-citySearch',"");
			$session->set('of-regionSearch',"");
            $session->set('of-provinceSearch',"");
        }
		
		if(!empty($resetSearch)){
			$session->set('categoryId', $this->categoryId);
		}

		if(isset($this->citySearch)){
			$session->set('of-citySearch', $this->citySearch);
		}
	
		if(isset($this->regionSearch)){
			$session->set('of-regionSearch', $this->regionSearch);
		}

        if(isset($this->countrySearch)){
            $this->countrySearch = intval($this->countrySearch);
            $session->set('of-countrySearch', $this->countrySearch);
        }

        if(isset($this->provinceSearch)){
            $session->set('of-provinceSearch', $this->provinceSearch);
        }

		if(isset($this->keyword)){
			$session->set('of-searchkeyword', $this->keyword);
		}
	
		if(isset($this->zipCode)){
			$session->set('of-zipcode', $this->zipCode);
		}
	
		if(isset($this->radius)){
			$this->radius = intval($this->radius);
			$session->set('of-radius', $this->radius);
		}

        if(isset($this->minPrice)){
            $this->minPrice = $this->minPrice;
            $session->set('of-minprice', $this->minPrice);
        }

        if(isset($this->maxPrice)){
            $this->maxPrice = $this->maxPrice;
            $session->set('of-maxprice', $this->maxPrice);
        }
        
        if(isset($this->priceRange)){
            $prices = explode(";",$this->priceRange);
            $minPrice = $prices[0];
            $maxPrice = $prices[1];
            $session->set('of-minprice', $minPrice);
            $session->set('of-maxprice', $maxPrice);
            $session->set('of-price-range', $this->priceRange);
        }
        
        
	
		$this->keyword = $session->get('of-searchkeyword');
		$this->citySearch = $session->get('of-citySearch');
		$this->regionSearch = $session->get('of-regionSearch');
		$this->categorySearch = $session->get('of-categorySearch');
        $this->countrySearch = $session->get('of-countrySearch');
        $this->provinceSearch = $session->get('of-provinceSearch');

		$this->zipCode = $session->get('of-zipcode');
		$this->radius = $session->get('of-radius');
		$this->location = null;

		$this->minPrice = $session->get('of-minprice');
        $this->maxPrice = $session->get('of-maxprice');

        $geolocation = $jinput->getString('geolocation',null);
		if(isset($geolocation)){
			$session->set("geolocation",$geolocation);
		}
		$geolocation = $session->get("geolocation");
		// test if geo location is determined and set location array
		if($this->appSettings->enable_geolocation && $geolocation){
		    $geoLatitutde = $jinput->getString('geo-latitude',null);
		    $geoLongitude = $jinput->getString('geo-longitude',null);

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
	
	
		$this->enablePackages = $this->appSettings->enable_packages;
		$this->showPendingApproval = $this->appSettings->show_pending_approval==1;
	
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
                $cats = $categoryService->getCategoryLeafs($category, CATEGORY_TYPE_OFFER);
                //dump($category);
                //dump($cats);
                if(isset($cats)){
                    $categoriesLevel = array_merge($categoriesLevel,$cats);
                }
                $categoriesLevel[] = $category;
                $categoriesIds[] = implode(",",$categoriesLevel);
            }
        }else if(!empty($this->categoryId) && ($this->appSettings->search_type==0 || $this->appSettings->enable_search_filter==0)){
            $categoriesIds = $categoryService->getCategoryLeafs($this->categoryId, CATEGORY_TYPE_OFFER);

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

		if(!empty($this->location)){
			$searchDetails["latitude"] = $this->location["latitude"];
			$searchDetails["longitude"] = $this->location["longitude"];
		}
		
		$searchDetails["radius"] = $this->radius;
		$searchDetails["minprice"] = $this->minPrice;
		$searchDetails["maxprice"] = $this->maxPrice;
		$searchDetails["citySearch"] = $this->citySearch;
		$searchDetails["regionSearch"] = $this->regionSearch;
		$searchDetails["enablePackages"] = $this->enablePackages;
		$searchDetails["showPendingApproval"] = $this->showPendingApproval;
		$searchDetails["orderBy"] = $this->orderBy;
		$searchDetails["multilingual"] = $this->appSettings->enable_multilingual;
        $searchDetails["areaSearch"] = $this->areaSearch;
        $searchDetails["facetedSearch"] = $this->appSettings->search_type;
        $searchDetails["countrySearch"] = $this->countrySearch;
        $searchDetails["provinceSearch"] = $this->provinceSearch;


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
	
	function getOffers(){
		
		$searchDetails = $this->getSearchParameters();
		$offersTable = JTable::getInstance("Offer", "JTable");
		
		
		$offers = array();
		if($this->appSettings->enable_cache){
			$cacheIdentifier = $this->getCacheId($searchDetails,"Items");
			try
			{
				$cache = JCache::getInstance();
				$offers = $cache->get($cacheIdentifier);
				if(empty($offers)){
					$offers =  $offersTable->getOffersByCategories($searchDetails, $this->getState('limitstart'), $this->getState('limit'));
					$cache->store($offers, $cacheIdentifier);
				}
			}catch (RuntimeException $e){
				$this->setError($e->getMessage());
				return null;;
			}
		}
		
		if(empty($offers)){
			$offers =  $offersTable->getOffersByCategories($searchDetails, $this->getState('limitstart'), $this->getState('limit'));
		}
		
        if($this->appSettings->enable_multilingual){
            JBusinessDirectoryTranslations::updateOffersTranslation($offers);
        }
	
		foreach($offers as $offer){
			switch($offer->view_type){
				case 1:
					$offer->link = JBusinessUtil::getofferLink($offer->id, $offer->alias);
					break;
				case 2:
					$itemId = JRequest::getVar('Itemid');
					$offer->link = JRoute::_("index.php?option=com_content&view=article&Itemid=$itemId&id=".$offer->article_id);
					break;
				case 3:
					$offer->link = $offer->url;
					break;
				default:
					$offer->link = JBusinessUtil::getofferLink($offer->id, $offer->alias);
			}
			
			if(empty($offer->latitude) && empty($offer->longitude) ){
				$offer->distance = 0;
			}
			
			if(!empty($offer->distance) && $this->appSettings->metric == 0){
			    $offer->distance = $offer->distance * 1.6;
			}

            if (!empty($offer->categories)) {
                $offer->categories = explode('#|', $offer->categories);
                foreach ($offer->categories as &$category) {
                    $category = explode("|", $category);
                    if (empty($category[0])) {
                        unset($category[0]);
                        $category = array_values($category);
                    }
                }
            }
		}

        if ($this->appSettings->apply_attr_offers) {
        	$attributeConfig = JBusinessUtil::getAttributeConfiguration();
            foreach ($offers as $offer) {
                $offer = JBusinessUtil::updateItemDefaultAtrributes($offer, $attributeConfig);
            }
        }
	
		if($searchDetails["orderBy"]=="rand()"){
			shuffle($offers);
		}

		JRequest::setVar("search-results",$offers);
	
		return $offers;
	}
	
	function getTotalOffers()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			
			$searchDetails = $this->getSearchParameters();
			$offersTable = JTable::getInstance("Offer", "JTable");
			
			
			if($this->appSettings->enable_cache){
				$cacheIdentifier = $this->getCacheId($searchDetails,"getTotal");
				try
				{
					$cache = JCache::getInstance();
					$this->_total = $cache->get($cacheIdentifier);
					if(empty($this->_total)){
						$this->_total = $offersTable->getTotalOffersByCategories($searchDetails);
						$cache->store($this->_total, $cacheIdentifier);
					}
				}catch (RuntimeException $e){
					$this->setError($e->getMessage());
					return null;;
				}
			}
			
			if(empty($this->_total)){
				$this->_total = $offersTable->getTotalOffersByCategories($searchDetails);
			}
		}
		return $this->_total;
	}
	
	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
		    require_once( JPATH_SITE.'/components/com_jbusinessdirectory/libraries/dirpagination.php');
		    $this->_pagination = new JBusinessDirectoryPagination($this->getTotalOffers(), $this->getState('limitstart'), $this->getState('limit') );
			$this->_pagination->setAdditionalUrlParam('controller','offers');
			if(!empty($this->categoryId))
				$this->_pagination->setAdditionalUrlParam('categoryId',$this->categoryId);
			$this->_pagination->setAdditionalUrlParam('categoryId',$this->categoryId);
			if(!empty($this->categorySearch))
				$this->_pagination->setAdditionalUrlParam('categorySearch',$this->categorySearch);
			if(!empty($this->keyword))
				$this->_pagination->setAdditionalUrlParam('searchkeyword',$this->keyword);

			if(!empty($this->citySearch))
				$this->_pagination->setAdditionalUrlParam('citySearch',$this->citySearch);
			
			if(!empty($this->zipCode))
				$this->_pagination->setAdditionalUrlParam('zipcode',$this->zipCode);

            if(!empty($this->countrySearch))
                $this->_pagination->setAdditionalUrlParam('countrySearch',$this->countrySearch);
			
			if(!empty($this->regionSearch))
				$this->_pagination->setAdditionalUrlParam('regionSearch',$this->regionSearch);

            if(!empty($this->provinceSearch))
                $this->_pagination->setAdditionalUrlParam('provinceSearch',$this->provinceSearch);

			if(!empty($this->radius))
				$this->_pagination->setAdditionalUrlParam('radius',$this->radius);
			if(!empty($this->startDate))
				$this->_pagination->setAdditionalUrlParam('startDate',$this->startDate);
			if(!empty($this->endDate))
				$this->_pagination->setAdditionalUrlParam('endDate',$this->endDate);
			
			if(!empty($this->preserve))
				$this->_pagination->setAdditionalUrlParam('preserve',$this->preserve);
		
			$orderBy = JRequest::getVar("orderBy", $this->appSettings->order_search_offers);
			if(!empty($orderBy))
				$this->_pagination->setAdditionalUrlParam('orderBy',$orderBy);
			
			$this->_pagination->setAdditionalUrlParam('view','offers');
		}
		return $this->_pagination;
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
		$offersTable = JTable::getInstance("Offer", "JTable");
		//dump($this->categoryId);
        if (!empty($this->appSettings->search_filter_fields))
            $this->appSettings->search_filter_fields = explode(",", $this->appSettings->search_filter_fields);

        if (in_array("categories", $this->appSettings->search_filter_fields)) {
        $categoryService = new JBusinessDirectorCategoryLib();
            $category = array();
            //dump($this->categoryId);
            if (!empty($this->categoryId)  && $this->appSettings->search_type != 1) {
                $category = $categoryService->getCompleteCategoryById($this->categoryId, CATEGORY_TYPE_OFFER);
            } else {
                $category["subCategories"] = $categoryService->getCategories(CATEGORY_TYPE_OFFER);
                $category["path"] = array();
                //dump($category["subCategories"]);
            }
            if (empty($category["subCategories"])) {
                $searchDetails["categoriesIds"] = array($category[0]->parent_id);
            }

            if ($this->appSettings->search_type == 1) {
                $searchDetails["categoriesIds"] = null;
            }
            $categoriesTotal = $offersTable->getTotalOffersByObject($searchDetails, 'category');
            if(empty($categoriesTotal) && !(empty($category["subCategories"]) && !empty($category[0])) && $this->appSettings->search_type != 1){
                if(isset($category[0]) && $category[0]->parent_id != 1){
                    $category = $categoryService->getCompleteCategoryById($category[0]->parent_id , CATEGORY_TYPE_OFFER);
                } else {
                    $category["subCategories"] = $categoryService->getCategories(CATEGORY_TYPE_OFFER);
                    $category["path"] = array();
                }
                if(isset($category[0])){
                    $searchDetails["categoriesIds"] = array($category[0]->parent_id);
                }
                $categoriesTotal = $offersTable->getTotalOffersByObject($searchDetails, 'category');
            }
            
            //dump($category);
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
                        $subcategories = $categoryService->getCategories(CATEGORY_TYPE_OFFER);
                        $this->searchFilter["enableSelection"] = 1;
                        $enableSelection = true;
                    } else if (count($parentCategories) > 0) {
                        $categoryId = $parentCategories[count($parentCategories)][0];
                        //dump($categoryId);
                        $parentCategory = $categoryService->getCompleteCategoryById($categoryId, CATEGORY_TYPE_OFFER);
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
                            $companiesNumber += $categoryTotal->nr_offers;
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
            $areasTotal = $offersTable->getTotalOffersByObject($searchDetails, "area");
            $areas = array();
            if (!empty($areasTotal)) {
                foreach ($areasTotal as $area) {
                    if(!empty($area->areaName)){
                        if (!isset($areas[$area->areaName])) {
                            $areas[$area->areaName] = $area;
                            $areas[$area->areaName]->nr_offers = (int)$area->nr_offers;
                        } else {
                            $areas[$area->areaName]->nr_offers += $area->nr_offers;
                        }
                    }
                }
            }
            $this->searchFilter["areas"] = $areas;
        }

        if (in_array("countries", $this->appSettings->search_filter_fields)) {
            $countriesTotal = $offersTable->getTotalOffersByObject($searchDetails, "country");
            $countries = array();
            foreach ($countriesTotal as $country) {
                if(!empty($country->countryName)){
                    if (!isset($countries[$country->countryId])) {
                        $countries[$country->countryId] = $country;
                        $countries[$country->countryId]->nr_offers = (int)$country->nr_offers;
                    } else {
                        $countries[$country->countryId]->nr_offers += $country->nr_offers;
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
            $citiesTotal = $offersTable->getTotalOffersByObject($searchDetails, "city");
            $cities = array();
            foreach ($citiesTotal as $city) {
                if(!empty($city->cityName)){
                    if (!isset($cities[$city->cityName])) {
                        $cities[$city->cityName] = $city;
                        $cities[$city->cityName]->nr_offers = (int)$city->nr_offers;
                    } else {
                        $cities[$city->cityName]->nr_offers += $city->nr_offers;
                    }
                }
            }

            $this->searchFilter["cities"] = $cities;
        }

        if (in_array("regions", $this->appSettings->search_filter_fields)) {
            $regionsTotal = $offersTable->getTotalOffersByObject($searchDetails, "region");
            $regions = array();
            foreach ($regionsTotal as $region) {
                if(!empty($region->regionName)){
                    if (!isset($regions[$region->regionName])) {
                        $regions[$region->regionName] = $region;
                        $regions[$region->regionName]->nr_offers = (int)$region->nr_offers;
                    } else {
                        $regions[$region->regionName]->nr_offers += $region->nr_offers;
                    }
                }
            }

            $this->searchFilter["regions"] = $regions;
        }

//      $mtime = microtime();
//      $mtime = explode(" ",$mtime);
//      $mtime = $mtime[1] + $mtime[0];
//      $endtime = $mtime;
//      $totaltime = ($endtime - $starttime);
//      echo "This function was done in ".$totaltime." seconds";

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

        if(!empty($this->provinceSearch) && !isset($params["province"]))
            $params["province"][] = $this->provinceSearch;

        if(!empty($this->areaSearch) && !isset($params["area"]))
            $params["area"][] = $this->areaSearch;

        if(!empty($this->countrySearch) && !isset($params["country"]))
            $params["country"][] = $this->countrySearch;

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
	
	function getCategories(){
		$categoryService = new JBusinessDirectorCategoryLib();
		return $categoryService->getCategories();
		
	}	
	
	function getSortByConfiguration(){
		$states = array();
		$state = new stdClass();
		$state->value = '';
		$state->text = JTEXT::_("LNG_RELEVANCE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'co.id desc';
		$state->text = JTEXT::_("LNG_LAST_ADDED");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'co.id asc';
		$state->text = JTEXT::_("LNG_FIRST_ADDED");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'co.startDate asc';
		$state->text = JTEXT::_("LNG_EARLIEST_DATE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'co.startDate desc';
		$state->text = JTEXT::_("LNG_LATEST_DATE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'co.subject';
		$state->text = JTEXT::_("LNG_NAME");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'co.city';
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