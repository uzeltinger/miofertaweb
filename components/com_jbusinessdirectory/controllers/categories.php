<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryControllerCategories extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
	}

	function displayCategories(){
		
		parent::display();
	}
	
	function getCategories(){
		$keyword = JRequest::getVar('term',null);
		$type = JRequest::getVar('type', CATEGORY_TYPE_BUSINESS);

		//dmp($keyword);
		if(empty($keyword)){
			JFactory::getApplication()->close();
		}
		
		$categoriesModel = $this->getModel("Categories");
		
		$categoriesList = $categoriesModel ->getCategoriesList($keyword, $type);
		header('Content-Type: application/json');
		echo $categoriesList;
		
		JFactory::getApplication()->close();
		}

	/**
	 * Get's the id, type, level and parentId of a category and calls the respective method in the model
	 * The level is used to determine the order the select box is shown on the view
	 * The parentId is used to fetch all the subcategories in order to populate the drop down list
	 * The category id is used to determine which category is selected by default inside this drop down list
	 * The type is used to fetch only the categories of that type
	 */
	function getSubcategoriesByParentIdAjax(){
		$parentId = JRequest::getVar('parentId', null);
		$type = JRequest::getVar('categoryType');
		$level = JRequest::getVar('level');
		$catId = JRequest::getVar('categoryId', null);
		$token = JRequest::getVar('token', null);
		
		$model = $this->getModel('Categories');
		$result = $model->getSubcategoriesByParentIdAjax($parentId, $type, $level, $catId, $token);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	function getAllParentsByIdAjax(){
		$id = JRequest::getVar('categoryId', null);

		$model = $this->getModel('Categories');
		$result = $model->getAllParentsById($id);

		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateCategoriesTranslation($result);
		}
		
		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}
}