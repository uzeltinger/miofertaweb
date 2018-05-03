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

class JBusinessDirectoryModelCategories extends JModelList
{ 
	function __construct()
	{
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$this->categoryType = JRequest::getVar('categoryType', CATEGORY_TYPE_BUSINESS);
        $this->categoryId = JRequest::getVar('categoryId');
		parent::__construct();
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Category', $prefix = 'JBusinessTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * 
	 * @return object with data
	 */
    function getCategories(){

        $categoryService = new JBusinessDirectorCategoryLib();
        $categoryTable = $this->getTable();
        $categories = $categoryService->getAllCategories($this->categoryType);
        $categories = $categoryService->processCategories($categories);
        $startingLevel = 0;
        $path=array();
        $level =0;
        $categories["maxLevel"] = $categoryService->setCategoryLevel($categories,$startingLevel,$level,$path);
        
        //will check if the Category id is entered on menu item and it will return only the subcategories
        //if it is not it will show all categories based on category type
        if (!empty($this->categoryId)){
        	$categories = $categoryTable->getSubcategoriesByParentId($this->categoryId);
        }

        $cityName = JRequest::getVar('city');
        if (!empty($cityName)){
            $companiesTable = $this->getTable("Company",'JTable');
            $companies = $companiesTable->getCompaniesByCityName($cityName);
            if (empty($companies))
                return array();
            $categoryIds = array();
            foreach ($companies as $company) {
                if (!empty($company->categories)) {
                    $categories = explode('#|', $company->categories);
                    foreach ($categories as $category) {
                        $categoryDetails = explode("|", $category);
                        $categoryIds[] = $categoryDetails[0];
                    }
                }
            }
            if (count($categoryIds)) {
                $ids = $categoryIds;
                foreach ($categoryIds as $id){
                    $category = $categoryService->getCompleteCategoryById($id,CATEGORY_TYPE_BUSINESS);
                    if ($category[0]->parent_id!=1 && !in_array($category[0]->parent_id,$ids)){
                        $ids[] = $category[0]->parent_id;
                    }
                }
                $ids = implode(',', $ids);
                $categories = $categoryService->getCategories(CATEGORY_TYPE_BUSINESS, $ids);
            }else{
                return array();
            }
        }

        if($this->appSettings->show_total_business_count) {
            $details = array();
            $details["enablePackages"] = $this->appSettings->enable_packages;
            $details["showPendingApproval"] = $this->appSettings->show_pending_approval == 1;

            $listingsCount = $categoryTable->getCountPerCategory($details, $this->categoryType);
        }

        foreach($categories as $category){
            if(!is_array($category)){
                $category = array($category);
                $category["subCategories"] = array();
            }
            if(isset($category[0]->id)){
                $category[0]->nr_listings = isset($listingsCount[$category[0]->id]->nr_listings)?$listingsCount[$category[0]->id]->nr_listings:'0';

                switch($this->categoryType){
                    case CATEGORY_TYPE_OFFER:
                        $category[0]->link = JBusinessUtil::getOfferCategoryLink($category[0]->id,  $category[0]->alias);
                        if(!empty($category["subCategories"])){
	                        foreach($category["subCategories"] as $cat){
	                        	$cat[0]->link = JBusinessUtil::getOfferCategoryLink($cat[0]->id,  $cat[0]->alias);
	                        }
                        }
                        break;
                    case CATEGORY_TYPE_EVENT:
                        $category[0]->link = JBusinessUtil::getEventCategoryLink($category[0]->id,  $category[0]->alias);
                        if(!empty($category["subCategories"])){
                        	foreach($category["subCategories"] as $cat){
                        		$cat[0]->link = JBusinessUtil::getEventCategoryLink($cat[0]->id,  $cat[0]->alias);
                        	}
                        }
                        break;
                    default:
                        $category[0]->link = JBusinessUtil::getCategoryLink($category[0]->id,  $category[0]->alias);
                        if(!empty($category["subCategories"])){
	                        foreach($category["subCategories"] as $cat){
	                        	$cat[0]->link = JBusinessUtil::getCategoryLink($cat[0]->id,  $cat[0]->alias);
	                        }
                        }
                }
            }
        }
        
        if($this->appSettings->enable_multilingual){
        	JBusinessDirectoryTranslations::updateCategoriesTranslation($categories);
        }
        
        return $categories;
	}
	
	function getCategoriesList($keyword, $type=CATEGORY_TYPE_BUSINESS){
		$table = $this->getTable();
		$suggestionList = $table->getCategoriesList($keyword, $type);
		$suggestionList = json_encode($suggestionList);
		return $suggestionList;
	}

	function getCategoryType(){

		return $this->categoryType;
	}

	/**
	 * Retrieves the subcategories of a category and generates a drop down list populated by these subcategories
	 * The default selected category in this drop down list is determined by the $catId param
	 * @param $parentId
	 * @param $type
	 * @param $level
	 * @param $catId
	 * @return string
	 */
	function getSubcategoriesByParentIdAjax($parentId, $type, $level, $catId, $token){
		$categoriesTable = $this->getTable('Category', 'JBusinessTable');
		$categories = $categoriesTable->getSubcategoriesByParentId($parentId, $type);

		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateCategoriesTranslation($categories);
		}
		
		$output = '';
		if(!empty($categories)) {
			$level++;
			$output .= '<div class="form-field categories-form-field" id="' . $level . '">';
			$output .= '<select name="categorySearch" id="categories-' . $level . '" onchange="showCategorySelect'.$token.'(' . $level . ')">';
			$output .= '<option value="0">'.JText::_("LNG_ALL_CATEGORIES").'</option>';
			foreach ($categories as $category) {
				$selected = (!empty($catId)&&$catId==$category->id)?'selected':'';
				$output .= '<option value="' . $category->id . '" '.$selected.' >' . $category->name . '</option>';
			}
			$output .= '</select>';
			$output .= '</div>';
		}

		return $output;
	}

	/**
	 * Returns an array that represent a path in the category tree, which is comprised of all the parents of the
	 * category whose id is being passed as a param
	 * @param $id
	 * @param array $categories
	 * @return array
	 */
	function getAllParentsById($id, $categories = array()){
		$categoryTable = $this->getTable('Category', 'JBusinessTable');
		$category = $categoryTable->getCategoryById($id);

		if($category->parent_id!=0){
			array_push($categories, $category);
			return $this->getAllParentsById($category->parent_id, $categories);
		}
		else
			return $categories;

	}
}
?>