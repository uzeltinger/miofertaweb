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
JTable::addIncludePath(DS.'components'.DS.'com_jbusinessdirectory'.DS.'tables');
require_once( JPATH_SITE.'/administrator/components/com_jbusinessdirectory/library/category_lib.php');

class JBusinessDirectoryModelSearch extends JModelList
{ 
	
	function __construct()
	{
		parent::__construct();
		$this->context="com_jbusinessdirectory.listing.search";
		
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$this->searchFilter = array();
		
		$this->prepareSearchAttribtues();
		
		$mainframe = JFactory::getApplication();
		$app = JFactory::getApplication();
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $app->input->get('limitstart', 0, 'uint');
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Companies', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	
	public function prepareSearchAttribtues(){
		$jinput = JFactory::getApplication()->input;

		if (!empty($jinput->get('categorySuggestion'))){
            $this->keyword = '';
            $this->categorySearch = $jinput->get('categorySuggestion');
		}

        $this->keyword = $jinput->getString('searchkeyword');
		$this->keywordLocation = $jinput->getString('searchkeywordLocation');
		$this->categoryId = $jinput->get('categoryId',null);
		$this->menuCategoryId = $jinput->get('menuCategoryId',null);
		$this->citySearch = $jinput->getString('citySearch',null);
		$this->typeSearch = $jinput->get('typeSearch',null);
        $this->regionSearch = $jinput->getString('regionSearch',null);
        $this->provinceSearch = $jinput->getString('provinceSearch',null);
		$this->countrySearch = $jinput->get('countrySearch',null);
		if (empty($this->categorySearch))
			$this->categorySearch = $jinput->get('categorySearch');
		$this->zipCode = $jinput->getString('zipcode');
		$this->radius = $jinput->get('radius');
		$this->preserve = $jinput->get('preserve',null);
		$this->letter = $jinput->getString('letter',null);
        $this->areaSearch = $jinput->getString('areaSearch',null);
        $this->starRating = $jinput->getString('starRating',null);
		$this->filterByFav = $jinput->get('filter-by-fav',null);
		
		$this->resetSearch = $jinput->get('resetSearch',null);
		
		if(isset($this->categorySearch) && empty($this->categoryId) && isset($this->preserve)){
			$this->categoryId = $this->categorySearch;
		}
		
		if(!empty($this->menuCategoryId) && empty($this->categoryId) && !isset($this->preserve)){
			$this->categoryId = $this->menuCategoryId;
		}
		
		$session = JFactory::getSession();
		if(isset($this->categoryId) || !empty($this->resetSearch)){
			$session->set('categorySearch', $this->categoryId);
			$session->set('searchkeyword', "");
			$session->set('searchkeywordLocation',"");
			$session->set('typeSearch',"");
            $session->set('citySearch',"");
            $session->set('starRating',"");
			$session->set('regionSearch',"");
			$session->set('countrySearch',"");
			$session->set('zipcode',"");
			$session->set('customAtrributes',"");
			$session->set('letter',"");
			$session->set('radius',"");
			$session->set('provinceSearch',"");
			$session->set('areaSearch',"");
			$session->set('filter-by-fav', "");
			$session->set('filterParams', "");
		}
		
		if(!empty($this->resetSearch)){
			$session->set('categoryId', $this->categoryId);
		}

		$session->set("listing-search",true);
		$activeMenu = JFactory::getApplication()->getMenu()->getActive();
		if(isset($activeMenu)){
			$session->set("menuItemId", $activeMenu->id);
		}
				
		if(isset($this->categoryId)){
			$this->categoryId = intval($this->categoryId);
			$session->set('categoryId', $this->categoryId);
		}
		
		if(isset($this->typeSearch)){
			$this->typeSearch = intval($this->typeSearch);
			$session->set('typeSearch', $this->typeSearch);
		}
		
		if(isset($this->citySearch)){
			$session->set('citySearch', $this->citySearch);
		}

        if(isset($this->starRating)){
            $session->set('starRating', $this->starRating);
        }
		
		if(isset($this->regionSearch)){
			$session->set('regionSearch', $this->regionSearch);
		}

        if(isset($this->provinceSearch)){
            $session->set('provinceSearch', $this->provinceSearch);
        }
		
		if(isset($this->countrySearch)){
			$this->countrySearch = intval($this->countrySearch);
			$session->set('countrySearch', $this->countrySearch);
		}
		
		if(isset($this->keyword)){
			$session->set('searchkeyword', $this->keyword);
		}
		if(isset($this->keywordLocation)){
			$session->set('searchkeywordLocation', $this->keywordLocation);
		}
		
		if(isset($this->zipCode)){
			$session->set('zipcode', $this->zipCode);
		}
		
		if(isset($this->radius)){
			$this->radius = intval($this->radius);
			$session->set('radius', $this->radius);
		}
		
		if(isset($this->letter)){
			$session->set('letter', $this->letter);
		}
		
		if(isset($this->filterByFav)){
			$session->set('filter-by-fav', $this->filterByFav);
		}
		
		$this->categoryId = $session->get('categoryId');
		
		$this->keyword = $session->get('searchkeyword');
		$this->keywordLocation = $session->get('searchkeywordLocation');
		$this->typeSearch = $session->get('typeSearch');
        $this->citySearch = $session->get('citySearch');
        $this->starRating = $session->get('starRating');
		$this->letter = $session->get('letter');
        $this->regionSearch = $session->get('regionSearch');
        $this->provinceSearch = $session->get('provinceSearch');
		$this->countrySearch = $session->get('countrySearch');
		$this->categorySearch = $session->get('categorySearch');
		
		$this->zipCode = $session->get('zipcode');
		$this->radius = $session->get('radius');
		$this->filterByFav = $session->get('filter-by-fav');

		$this->location = null;
        $this->geoCountryCode = "";
		
		$geolocation = $jinput->get('geolocation',null);
		if(isset($geolocation)){
			$session->set("geolocation",$geolocation);
		}
		$geolocation = $session->get("geolocation");
		// test if geo location is determined and set location array
		if($geolocation){
			$geoLatitutde = $jinput->get('geo-latitude',null);
			$geoLongitude = $jinput->get('geo-longitude',null);
            $geoCountry = JRequest::getVar('geo-country',null);
		
			if(!empty($geoLatitutde)){
				$session->set('geo-latitude', $geoLatitutde);
			}
			if(!empty($geoLongitude)){
				$session->set('geo-longitude', $geoLongitude);
			}
            if(!empty($geoCountry)){
                $session->set('geo-country', $geoCountry);
            }
			$geoLatitutde = $session->get('geo-latitude');
			$geoLongitude = $session->get('geo-longitude');
            $geoCountry = $session->get('geo-country');

            $this->geoCountryCode = !empty($geoCountry)?JBusinessUtil::getCountryIDByCode($geoCountry):"";

			if(!empty($geoLatitutde) && !empty($geoLongitude)){
				$this->location =  array();
				$this->location["latitude"] = $geoLatitutde;
				$this->location["longitude"] = $geoLongitude;
			}
		}
		
		$this->featured = $jinput->get('featured',null);
		
		$this->enablePackages = $this->appSettings->enable_packages;
		$this->showPendingApproval =  $this->appSettings->show_pending_approval==1;
		$this->showSecondayLocationsMap =  $this->appSettings->show_secondary_locations;
		
		if(isset($this->zipCode) && $this->zipCode!=""){
			$this->location = JBusinessUtil::getCoordinates($this->zipCode);
		}
		
		if(!empty($this->location)){
			$session->set("location",$this->location);
		}
		
		//prepare custom attributes
		$data = JRequest::get('post');
		if(empty($data)){
			$data = JRequest::get('get');
		}
		
		//custom attributes preparation
		if(isset($this->preserve)){
			$session->set('customAtrributes',"");
		}
		
		$this->customAtrributes = array();
		foreach($data as $key=>$value){
			if(strpos($key,"attribute")===0){
				$attributeId = explode("_", $key);
				$attributeId = $attributeId[1];
				if(!empty($value)){
					$this->customAtrributes[$attributeId] = $value;
				}
				$session->set('customAtrributes',"");
			}
		}
		
		if(!empty($this->customAtrributes)){
			foreach($this->customAtrributes as &$customAttribute){
				if(is_array($customAttribute)){
					$customAttribute = implode(",", $customAttribute);
				}
			}
			
			$session->set('customAtrributes', $this->customAtrributes);
		}
		
		$this->customAtrributes = $session->get('customAtrributes');
		
		$session->set("searchType",1);
	}
	
	function getCategoryId(){
		return $this->categoryId;
	}
	
	function getSearchParams(){
		$categories = $this->getSelectedCategories();
		
		$categoryService = new JBusinessDirectorCategoryLib();
		$categoriesIds = array();
		if(!empty($categories) && ($this->appSettings->search_type==1 && $this->appSettings->enable_search_filter==1)) {
			foreach($categories as $category){
				$categoriesLevel= array();
				$cats = $categoryService->getCategoryLeafs($category, CATEGORY_TYPE_BUSINESS);
				//dump($category);
				//dump($cats);
				if(isset($cats)){
					$categoriesLevel = array_merge($categoriesLevel,$cats);
				}
				$categoriesLevel[] = $category;
				$categoriesIds[] = implode(",",$categoriesLevel);
			}
		}else if(!empty($this->categoryId) && ($this->appSettings->search_type==0 || $this->appSettings->enable_search_filter==0)){
			$categoriesIds = $categoryService->getCategoryLeafs($this->categoryId, CATEGORY_TYPE_BUSINESS);
				
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
		
		$searchDetails = array();
		$searchDetails["keyword"] = $this->keyword;
		$searchDetails["keywordLocation"] = $this->keywordLocation;
		$searchDetails["categoriesIds"] = $categoriesIds;
		if(!empty($this->location)){
			$searchDetails["latitude"] = $this->location["latitude"];
			$searchDetails["longitude"] = $this->location["longitude"];
		}

		if (!empty($this->geoCountryCode)){
            $this->countrySearch = $this->geoCountryCode;
		}
		
		$radius = $this->radius;
		if($this->appSettings->metric==0){
			$radius  = $radius * 0.621371;
		}

		$params = $this->getSelectedParams();
		$db =JFactory::getDBO();
		if(isset($params["type"]))
		    $this->typeSearch = $db->escape($params["type"][0]);

		if(isset($params["country"]))
		    $this->countrySearch = $db->escape($params["country"][0]);

		if(isset($params["region"]))
		    $this->regionSearch = $db->escape($params["region"][0]);

        if(isset($params["province"]))
            $this->provinceSearch = $db->escape($params["province"][0]);

		if(isset($params["city"]))
		    $this->citySearch = $db->escape($params["city"][0]);

        if(isset($params["starRating"]))
            $this->starRating = $params["starRating"][0];

		if(isset($params["area"]))
		    $this->areaSearch = $db->escape($params["area"][0]);

		if (isset($params["letter"]))
		    $this->letter = $db->escape($params["letter"][0]);

		$orderBy = JFactory::getApplication()->input->getString("orderBy", $this->appSettings->order_search_listings);

		$allowedValues = $this->getSortByConfiguration();
		if(!JBusinessUtil::validateOrderBy($orderBy, $allowedValues)){
			$orderBy = $this->appSettings->order_search_listings;
		}
		
		$searchDetails["radius"] = $radius;
		$searchDetails["typeSearch"] = $this->typeSearch;
		$searchDetails["citySearch"] = $this->citySearch;
        $searchDetails["starRating"] = $this->starRating;
		$searchDetails["regionSearch"] = $this->regionSearch;
		$searchDetails["countrySearch"] = $this->countrySearch;
		$searchDetails["enablePackages"] = $this->enablePackages;
		$searchDetails["showPendingApproval"] = $this->showPendingApproval;
		$searchDetails["orderBy"] = $orderBy;
		$searchDetails["facetedSearch"] = $this->appSettings->search_type;
		$searchDetails["zipcCodeSearch"] = $this->appSettings->zipcode_search_type;
		$searchDetails["limit_cities"] = $this->appSettings->limit_cities;
		$searchDetails["customAttributes"] = $this->customAtrributes;
		$searchDetails["featured"] = $this->featured;
		$searchDetails["showSecondayLocationsMap"] = $this->showSecondayLocationsMap;
		$searchDetails["multilingual"] = $this->appSettings->enable_multilingual;
		$searchDetails["letter"] = $this->letter;
        $searchDetails["areaSearch"] = $this->areaSearch;
        $searchDetails["provinceSearch"] = $this->provinceSearch;

		if(!empty($this->filterByFav)){
			$searchDetails["bookmarks"] = $this->getBookmarks();
		}

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
	
	/**
	 * Retrieve search results items
	 * @return multitype:
	 */
	function getItems(){
		$companiesTable = $this->getTable("Company");
		$searchDetails = $this->getSearchParams();
		
		$companies = array();
		if($this->appSettings->enable_cache){
			$cacheIdentifier = $this->getCacheId($searchDetails,"Items");
			try
			{
				$cache = JCache::getInstance();
				$companies = $cache->get($cacheIdentifier);
				if(empty($companies)){
					$companies = $companiesTable->getCompaniesByNameAndCategories($searchDetails, $this->getState('limitstart'), $this->getState('limit'));
					$cache->store($companies, $cacheIdentifier);
				}
			}catch (RuntimeException $e){
				$this->setError($e->getMessage());
				return null;;
			}
		}
		
		if(empty($companies)){
			$companies = $companiesTable->getCompaniesByNameAndCategories($searchDetails, $this->getState('limitstart'), $this->getState('limit'));
		}
		
		foreach($companies as $company){
			$company->packageFeatures = explode(",", $company->features);
		}

		$attributeConfig = JBusinessUtil::getAttributeConfiguration();

		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateBusinessListingsTranslation($companies);
			JBusinessDirectoryTranslations::updateBusinessListingsSloganTranslation($companies);
		}

		foreach($companies as $company){
			$company->packageFeatures = explode(",", $company->features);
			$attributesTable = $this->getTable('CompanyAttributes');
            $categoryId = null;
            if($this->appSettings->enable_attribute_category){
                $categoryId = -1;
                if(!empty($company->mainSubcategory))
                    $categoryId= $company->mainSubcategory;
            }
			$company->customAttributes = $attributesTable->getCompanyAttributes($company->id, $categoryId);
			$company = JBusinessUtil::updateItemDefaultAtrributes($company,$attributeConfig);
			
			if(empty($company->latitude) && empty($company->longitude) ){
				$company->distance = 0;
			}
			
			if(!empty($company->distance) && $this->appSettings->metric == 0){
			    $company->distance = $company->distance * 1.6;
			}
			
			if(!empty($company->pictures)){
				$company->pictures = explode(",", $company->pictures);
				$company->pictures = array_slice($company->pictures, 0, 5);
			}
		}
		
		if($searchDetails["orderBy"]=="rand()"){
			shuffle($companies);
		}
		
		JRequest::setVar("search-results",$companies);
		
		return $companies;
	}
	
	function getTotalCompaniesByNameAndCategory(){
		// Load the content if it doesn't already exist
		
		if (empty($this->_total)) {
			$searchDetails = $this->getSearchParams();
			$companiesTable = $this->getTable("Company");
				
			if($this->appSettings->enable_cache){
				$cacheIdentifier = $this->getCacheId($searchDetails,"getTotal");
				try
				{
					$cache = JCache::getInstance();
					$this->_total = $cache->get($cacheIdentifier);
					if(empty($this->_total)){
						$this->_total = $companiesTable->getTotalCompaniesByNameAndCategories($searchDetails);
						$cache->store($this->_total, $cacheIdentifier);
					}
				}catch (RuntimeException $e){
					$this->setError($e->getMessage());
					return null;;
				}
			}
		
			if(empty($this->_total)){
				$this->_total = $companiesTable->getTotalCompaniesByNameAndCategories($searchDetails);
			}
		}
		
		return $this->_total;
	}
	
	function getPagination(){
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
		    require_once( JPATH_SITE.'/components/com_jbusinessdirectory/libraries/dirpagination.php');
			$this->_pagination = new JBusinessDirectoryPagination($this->getTotalCompaniesByNameAndCategory(), $this->getState('limitstart'), $this->getState('limit') );
			$this->_pagination->setAdditionalUrlParam('option','com_jbusinessdirectory');
			$this->_pagination->setAdditionalUrlParam('controller','search');
			
			if(isset($this->categoryId) && $this->categoryId!='')
				$this->_pagination->setAdditionalUrlParam('categoryId',$this->categoryId);
			$this->_pagination->setAdditionalUrlParam('categoryId',$this->categoryId);
			
			if(isset($this->categorySearch) && $this->categorySearch!='')
				$this->_pagination->setAdditionalUrlParam('categorySearch',$this->categorySearch);
			$categories = JRequest::getVar("categories");
			if(!empty($categories))
				$this->_pagination->setAdditionalUrlParam('categories',$categories);
			
			$orderBy = JRequest::getVar("orderBy", $this->appSettings->order_search_listings);
			if(!empty($orderBy))
				$this->_pagination->setAdditionalUrlParam('orderBy',$orderBy);
			
			if(!empty($this->keyword))
				$this->_pagination->setAdditionalUrlParam('searchkeyword',$this->keyword);

			if(!empty($this->citySearch))
				$this->_pagination->setAdditionalUrlParam('citySearch',$this->citySearch);

            if(!empty($this->starRating))
                $this->_pagination->setAdditionalUrlParam('starRating',$this->starRating);
			
			if(!empty($this->zipCode))
				$this->_pagination->setAdditionalUrlParam('zipcode',$this->zipCode);
			
			if(!empty($this->regionSearch))
				$this->_pagination->setAdditionalUrlParam('regionSearch',$this->regionSearch);

            if(!empty($this->provinceSearch))
                $this->_pagination->setAdditionalUrlParam('provinceSearch',$this->provinceSearch);
			
			if(!empty($this->countrySearch))
				$this->_pagination->setAdditionalUrlParam('countrySearch',$this->countrySearch);
			
			if(!empty($this->typeSearch))
				$this->_pagination->setAdditionalUrlParam('typeSearch',$this->typeSearch);
			
			if(!empty($this->letter))
				$this->_pagination->setAdditionalUrlParam('letter',$this->letter);
			
			if(!empty($this->radius))
				$this->_pagination->setAdditionalUrlParam('radius',$this->radius);

			if(!empty($this->resetSearch))
				$this->_pagination->setAdditionalUrlParam('resetSearch',$this->resetSearch);

			if(!empty($this->customAtrributes)){
				foreach($this->customAtrributes as $key=>$val){
				    $this->_pagination->setAdditionalUrlParam('attribute_'.$key,$val);
				}
			}			
			
			if(!empty($this->preserve))
				$this->_pagination->setAdditionalUrlParam('preserve',$this->preserve);
			
			$this->_pagination->setAdditionalUrlParam('view','search');
		}
		return $this->_pagination;
	}
	
	function getSearchFilter(){
		if (empty($this->appSettings->search_filter_fields)) {
			return;
		}
		
		$companiesTable = $this->getTable("Company");
		$searchDetails = $this->getSearchParams();
		$searchDetails["facetedSearch"] = $this->appSettings->search_type;
		$searchDetailsCategories = $searchDetails["categoriesIds"];

		if (!empty($this->appSettings->search_filter_fields))
			$this->appSettings->search_filter_fields = explode(",", $this->appSettings->search_filter_fields);

		if (in_array("categories", $this->appSettings->search_filter_fields)) {
			$categoryService = new JBusinessDirectorCategoryLib();
			$category = array();
			if (!empty($this->categoryId) && $this->appSettings->search_type != 1) {
				$category = $categoryService->getCompleteCategoryById($this->categoryId, CATEGORY_TYPE_BUSINESS);
			} else {
				$category["subCategories"] = $categoryService->getCategories();
				$category["path"] = array();
			}
			
			//dump($category);

			if (empty($category["subCategories"])) {
				$searchDetails["categoriesIds"] = array($category[0]->parent_id);
			}

			if ($this->appSettings->search_type == 1) {
				$searchDetails["categoriesIds"] = null;
			}
			$categoriesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, 'category');
			if(empty($categoriesTotal) && !(empty($category["subCategories"]) && !empty($category[0])) && $this->appSettings->search_type != 1){
			    if(isset($category[0]) && $category[0]->parent_id != 1){
			        $category = $categoryService->getCompleteCategoryById($category[0]->parent_id , CATEGORY_TYPE_BUSINESS);
			    } else {
			        $category["subCategories"] = $categoryService->getCategories(CATEGORY_TYPE_BUSINESS);
			        $category["path"] = array();
			    }
			    if(isset($category[0])){ 
			       $searchDetails["categoriesIds"] = array($category[0]->parent_id);
			    }
			    $categoriesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, 'category');
			}
			//dump($categoriesTotal);
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
						$subcategories = $categoryService->getCategories();
						$this->searchFilter["enableSelection"] = 1;
						$enableSelection = true;
					} else if (count($parentCategories) > 0) {
						$categoryId = $parentCategories[count($parentCategories)][0];
						//dump($categoryId);
						$parentCategory = $categoryService->getCompleteCategoryById($categoryId, CATEGORY_TYPE_BUSINESS);
						$subcategories = $parentCategory["subCategories"];
						$this->searchFilter["enableSelection"] = 1;
						$enableSelection = true;
					}
				}
			}
			//dump($subcategories);
			$categories = array();
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
					}

					$companies = array();
					$companiesNumber = 0;
					foreach ($categoriesTotal as $categoryTotal) {
						if (in_array($categoryTotal->id, $childCategoryIds)) {
							$companiesNumber += $categoryTotal->nr_listings;
						}
					}

					if ($companiesNumber > 0)
						$this->searchFilter["categories"][] = array($cat, $companiesNumber);
				}
			}

			$searchDetails["categoriesIds"] = $searchDetailsCategories;
			//dump($this->searchFilter["categories"]);
			$searchDetails["facetedSearch"] = 0;

            //$startTime = microtime(true); // Gets current microtime as one long string

			if(!empty($this->searchFilter["categories"]) && $this->appSettings->category_order == ORDER_ALPHABETICALLY){
                $this->searchFilter["categories"] = $categoryService->sortCategories($this->searchFilter["categories"],false,false);
			}

            //$endTime = microtime(true) - $startTime; // And this at the end of your code
            //echo 'Sort operation ended successfully and took '.round($endTime, 4).' seconds to run!';

		}

		if (in_array("cities", $this->appSettings->search_filter_fields)) {
			$citiesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "city");
			$cities = array();
			foreach ($citiesTotal as $city) {
				if(!empty($city->cityName)){
					if (!isset($cities[$city->cityName])) {
						$cities[$city->cityName] = $city;
						$cities[$city->cityName]->nr_listings = (int)$city->nr_listings;
					} else {
						$cities[$city->cityName]->nr_listings += $city->nr_listings;
					}
				}
				
				if (!empty($city->secCityNames) && isset($city->secCityNames)) {
					$secLocationCities = explode(',', $city->secCityNames);
					foreach ($secLocationCities as $secLocationCity) {
						if($secLocationCity != $city->cityName){
							if (isset($cities[$secLocationCity])) {
								$cities[$secLocationCity]->nr_listings += 1;
							} else {
								$cityObj = new stdClass();
								$cityObj->cityName = $secLocationCity;
								$cityObj->nr_listings = 1;
								$cities[$secLocationCity] = $cityObj;
							}
						}
					}
				}
			}
			
			$this->searchFilter["cities"] = $cities;
		}

		if (in_array("regions", $this->appSettings->search_filter_fields)) {
			$regionsTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "region");
			$regions = array();
			foreach ($regionsTotal as $region) {
					if(!empty($region->regionName)){
					if (!isset($regions[$region->regionName])) {
						$regions[$region->regionName] = $region;
						$regions[$region->regionName]->nr_listings = (int)$region->nr_listings;
					} else {
						$regions[$region->regionName]->nr_listings += $region->nr_listings;
					}
				}

				if (!empty($region->secRegionNames) && isset($region->secRegionNames)) {
					$secondaryRegions = explode(',', $region->secRegionNames);
					foreach ($secondaryRegions as $secLocationRegion) {
						if ($secLocationRegion != $region->regionName) {
							if (isset($regions[$secLocationRegion])) {
								$regions[$secLocationRegion]->nr_listings += 1;
							} else {
								$regionObj = new stdClass();
								$regionObj->regionName = $secLocationRegion;
								$regionObj->nr_listings = 1;
								$regions[$secLocationRegion] = $regionObj;
							}
						}
					}
				}
			}
			$this->searchFilter["regions"] = $regions;
		}

        if (in_array("starRating", $this->appSettings->search_filter_fields)) {
            $ratingsTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "starRating");
            $ratings = array();
            foreach ($ratingsTotal as $rating) {
                if(!empty($rating->reviewScore)){
                    if (!isset($ratings[$rating->reviewScore])) {
                        $ratings[$rating->reviewScore] = $rating;
                        $ratings[$rating->reviewScore]->nr_listings = (int)$rating->nr_listings;
                    } else {
                        $ratings[$rating->reviewScore]->nr_listings += $rating->nr_listings;
                    }
                }
            }

            $this->searchFilter["starRating"] = $ratings;
        }

		if (in_array("countries", $this->appSettings->search_filter_fields)) {
			$countriesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "country");
			$countries = array();
			foreach ($countriesTotal as $country) {
				if(!empty($country->countryName)){
					if (!isset($countries[$country->countryId])) {
						$countries[$country->countryId] = $country;
						$countries[$country->countryId]->nr_listings = (int)$country->nr_listings;
					} else {
						$countries[$country->countryId]->nr_listings += $country->nr_listings;
					}
				}

				if (!empty($country->secCountryNames) && isset($country->secCountryNames)) {
					$allCountries = $this->getCountries();
					$secondaryCountry = explode(',', $country->secCountryNames);
					foreach ($secondaryCountry as $secLocationCountry) {
						if ($secLocationCountry != $country->countryId) {
							if (isset($countries[$secLocationCountry])) {
								$countries[$secLocationCountry]->nr_listings += 1;
							} else {
								$countryObj = new stdClass();
								$countryObj->countryName = $allCountries[$secLocationCountry]->country_name;
								$countryObj->countryId = $secLocationCountry;
								$countryObj->nr_listings = 1;
								$countries[$secLocationCountry] = $countryObj;
							}
						}
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

		if (in_array("types", $this->appSettings->search_filter_fields)) {
			$typesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "type");
			$types = array();
			if (!empty($typesTotal)) {
				if ($this->appSettings->enable_multilingual) {
					JBusinessDirectoryTranslations::updateTypesTranslation($typesTotal);
				}
                $companiesTable = $this->getTable("Company");
                $companyTypes = $companiesTable->getCompanyTypes();

                $typeArray = array();
                foreach($typesTotal as $key => $type){
                    $ids = explode(',', $type->typeId);
                	if (count($ids)>1) {
                        foreach ($companyTypes as $index => $compType) {
                            if (in_array($compType->value, $ids)) {
                                $newObject = new stdClass();
                                $newObject->typeName = $compType->text;
                                $newObject->nr_listings = $type->nr_listings;
                                $newObject->activity_radius = $type->activity_radius;
                                $newObject->typeId = $compType->value;
                                $typeArray[] = $newObject;
                            }
                        }
                        unset($typesTotal[$key]);
                    }
                }

				foreach ($typeArray as $array){
                    $typesTotal[] = $array;
				}

				foreach ($typesTotal as $type) {
					if(!empty($type->typeName)){
						if (!isset($types[$type->typeId])) {
							$types[$type->typeId] = $type;
							$types[$type->typeId]->nr_listings = (int)$type->nr_listings;
						} else {
							$types[$type->typeId]->nr_listings += $type->nr_listings;
						}
					}
				}
			}

			$this->searchFilter["types"] = $types;
		}

		if (in_array("area", $this->appSettings->search_filter_fields)) {
			$areasTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "area");
			$areas = array();
			if (!empty($areasTotal)) {
				foreach ($areasTotal as $area) {
					if(!empty($area->areaName)){
						if (!isset($areas[$area->areaName])) {
							$areas[$area->areaName] = $area;
							$areas[$area->areaName]->nr_listings = (int)$area->nr_listings;
						} else {
							$areas[$area->areaName]->nr_listings += $area->nr_listings;
						}
					}
					if (!empty($area->secAreaNames) && isset($area->secAreaNames)) {
						$secondaryArea = explode(',', $area->secAreaNames);
						foreach ($secondaryArea as $secLocationArea) {
							if ($secLocationArea != $area->areaName) {
								if (isset($areas[$secLocationArea])) {
									$areas[$secLocationArea]->nr_listings += 1;
								} else {
									$areaObj = new stdClass();
									$areaObj->areaName = $secLocationArea;
									$areaObj->nr_listings = 1;
									$areas[$secLocationArea] = $areaObj;
								}
							}
						}
					}
				}
			}
			$this->searchFilter["areas"] = $areas;
		}

        if (in_array("province", $this->appSettings->search_filter_fields)) {
            $provincesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "province");
            $provinces = array();
            if (!empty($provincesTotal)) {
                foreach ($provincesTotal as $province) {
                    if(!empty($province->provinceName)){
                        if (!isset($provinces[$province->provinceName])) {
                            $provinces[$province->provinceName] = $province;
                            $provinces[$province->provinceName]->nr_listings = (int)$province->nr_listings;
                        } else {
                            $provinces[$province->provinceName]->nr_listings += $province->nr_listings;
                        }
                    }
                    if (!empty($province->secProvinceNames) && isset($province->secProvinceNames)) {
                        $secondaryProvince = explode(',', $province->secProvinceNames);
                        foreach ($secondaryProvince as $secLocationProvince) {
                            if ($secLocationProvince != $province->provinceName) {
                                if (isset($provinces[$secLocationProvince])) {
                                    $provinces[$secLocationProvince]->nr_listings += 1;
                                } else {
                                    $provinceObj = new stdClass();
                                    $provinceObj->provinceName = $secLocationProvince;
                                    $provinceObj->nr_listings = 1;
                                    $provinces[$secLocationProvince] = $provinceObj;
                                }
                            }
                        }
                    }
                }
            }
            $this->searchFilter["provinces"] = $provinces;
        }

		return $this->searchFilter;

	}
	
	function getCategory(){
		$categoryTable = $this->getTable("Category", "JBusinessTable");
		$category = $categoryTable->getCategoryById($this->categoryId);
		
		if(empty($category))
			return $category;
		
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEntityTranslation($category, CATEGORY_TRANSLATION);
		}
		
		if(!empty($category->description) && $category->description==strip_tags($category->description)){
			$category->description = str_replace("\n", "<br/>", $category->description);
		}
		
		return $category;
	}

	function getSelectedParams(){
		$session = JFactory::getSession();
		$params = array();
		$values = array();
		
		$this->letter = JRequest::getVar("letter");
		$selectedParams = JRequest::getVar("selectedParams");
		
		if(empty($selectedParams)){
			$selectedParams = $session->get("filterParams");
		}
		
		if(!empty($selectedParams)){
			$session->set("filterParams",$selectedParams);
		}
		
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

		if(!empty($this->countrySearch) && !isset($params["country"]))
			$params["country"][] = $this->countrySearch;

		if(!empty($this->regionSearch) && !isset($params["region"]))
			$params["region"][] = $this->regionSearch;

        if(!empty($this->provinceSearch) && !isset($params["province"]))
            $params["province"][] = $this->provinceSearch;

		if(!empty($this->citySearch) && !isset($params["city"]))
			$params["city"][] = $this->citySearch;

		if(!empty($this->typeSearch) && !isset($params["type"]))
			$params["type"][] = $this->typeSearch;

        if(!empty($this->areaSearch) && !isset($params["area"]))
            $params["area"][] = $this->areaSearch;

        if(!empty($this->starRating) && !isset($params["starRating"]))
            $params["starRating"][] = $this->starRating;

		if (!empty($this->letter) && !isset($params["letter"]))
			$params["letter"][] = $this->letter;
		
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
	
	function getCustomAttributeValues(){
		$attributeTable = $this->getTable("Attribute","JTable");
		
		if(empty($this->customAtrributes)){
			return null;
		}
		
		$result = array();
		
		$customAttributes = implode(",",$this->customAtrributes);
		$customAttributes = explode(",",$customAttributes);
		//remove string values
		foreach($customAttributes as $key=>$value){
			if(is_numeric($value)){
				$result[$key]=$value;
			}
		}
		$attributeIds = implode(",",$result);
		$customAttributeValues = $attributeTable->getCustomAttributeValues($attributeIds);
	
		//add string values
		foreach($customAttributes as $key=>$value){
			if(!is_numeric($value)){
				$obj = new stdClass();
				$obj->name = $value;
				$customAttributeValues[]=$obj;
			}
		}
		
		return $customAttributeValues;
	}
	
	/**
	 * Generate order by values
	 * 
	 * @return multitype:stdClass
	 */
	function getSortByConfiguration(){
		
		$states = array();
		$state = new stdClass();
		$state->value = 'packageOrder desc';
		$state->text = JTEXT::_("LNG_RELEVANCE");
		$states[] = $state;
		
		$state = new stdClass();
		$state->value = 'id desc';
		$state->text = JTEXT::_("LNG_LAST_ADDED");
		$states[] = $state;

		$state = new stdClass();
		$state->value = 'id asc';
		$state->text = JTEXT::_('LNG_FIRST_ADDED');
		$states[] = $state;
		
		$state = new stdClass();
		$state->value = 'companyName';
		$state->text = JTEXT::_("LNG_NAME");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'city asc';
		$state->text = JTEXT::_("LNG_CITY");
		$states[] = $state;

		if ($this->appSettings->enable_reviews == 1) {
			$state = new stdClass();
			$state->value = 'review_score desc';
			$state->text = JTEXT::_("LNG_REVIEW");
			$states[] = $state;
		}

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

	function getCompanyType() {
		$type = null;
		if(!empty($this->typeSearch)) {
			$companyTypesTable = $this->getTable("CompanyTypes","JTable");
			$type = $companyTypesTable->getCompanyType($this->typeSearch);

			if($this->appSettings->enable_multilingual) {
				JBusinessDirectoryTranslations::updateEntityTranslation($type, TYPE_TRANSLATION);
			}
		}
		return $type;
	}

	function getRegionsByCountryAjax($countryId) {
		$countryTable = $this->getTable("Country","JTable");
		$results = $countryTable->getRegionsByCountry($countryId);
		$options = '';
		if($results) {
			$options .= '<option value="0" selected>'.JText::_("LNG_ALL_REGIONS").'</option>';
			foreach($results as $region) {
				$options .= '<option value="'.$region->county.'">'.$region->county.'</option>';
			}
		}
		return $options;
	}

	function getCitiesByRegionAjax($region) {
		$countryTable = $this->getTable("Country","JTable");
		$results = $countryTable->getCitiesByRegion($region);
		$options = '';
		if($results) {
			$options .= '<option value="0" selected>'.JText::_("LNG_ALL_CITIES").'</option>';
			foreach($results as $city) {
				$options .= '<option value="'.$city->city.'">'.$city->city.'</option>';
			}
		}
		return $options;
	}

	function getCitiesByCountryAjax($countryId) {
		$countryTable = $this->getTable("Country","JTable");
		$results = $countryTable->getCitiesByCountry($countryId);
		$options = '';
		if($results) {
			$options .= '<option value="0" selected>'.JText::_("LNG_ALL_CITIES").'</option>';
			foreach($results as $city) {
				$options .= '<option value="'.$city->city.'">'.$city->city.'</option>';
			}
		}
		return $options;
	}


	function getUsedLetter()
	{
		$companiesTable = $this->getTable("Company");

		$letters = $companiesTable->getUsedLettersForActiveBusiness();
		$result = array();
		foreach ($letters as $letter) {
			$result[$letter->letter] = $letter->letter;
		}

		return $result;
	}
	
	function getBookmarks(){
		$user = JFactory::getUser();
		$bookmarkTable = $this->getTable("Bookmark","JTable");
		$bookmarks = $bookmarkTable->getBookmarks($user->id);
		
		$result = array();
		if(!empty($bookmarks)){
			foreach($bookmarks as $bookmark){
				$result[]=$bookmark->company_id;
			}
		}
		
		return $result;
	}

	function getCountries(){
		$result = array();
		$countriesTable = $this->getTable("Country");
		$countries = $countriesTable->getCountries();
		foreach($countries as $country){
			$result[$country->id] = $country;
		}

		return $result;
	}
}
?>

