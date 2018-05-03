<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/imagesloaded.pkgd.min.js');
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/jquery.isotope.min.js');
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/isotope.init.js');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/companies.js');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/map.js');

// following translations will be used in js
JText::script('LNG_BAD');
JText::script('LNG_POOR');
JText::script('LNG_REGULAR');
JText::script('LNG_GOOD');
JText::script('LNG_GORGEOUS');
JText::script('LNG_NOT_RATED_YET');
JText::script('COM_JBUSINESS_ERROR');
JText::script('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED');
JText::script('LNG_HIDE_MAP');
JText::script('LNG_SHOW_MAP');

JBusinessUtil::includeValidation();
class JBusinessDirectoryViewSearch extends JViewLegacy
{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function display($tpl = null)
	{
		$session = JFactory::getSession();
		$jinput = JFactory::getApplication()->input;
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$this->companies = $this->get('Items');
		$this->viewType = JRequest::getVar("view-type",LIST_VIEW);
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$categoryId= $this->get('CategoryId');
		$this->filterActive = JRequest::getVar("filter_active");
		if(!empty($categoryId)  && $this->appSettings->search_type != 1){
			$this->categoryId=$categoryId;
			$this->category = $this->get('Category');
		}	
		
		$this->selectedCategories =  $this->get("SelectedCategories");
		$this->selectedParams = $this->get('SelectedParams');
		
		$this->categories = implode(";", $this->get("SelectedCategories"));
		if(!empty($this->categories)){
			$this->categories.=";";
		}	
		
		$this->country = $this->get('Country');
				
		$searchkeyword = $jinput->getString('searchkeyword');
		if(isset($searchkeyword)){
			$this->searchkeyword =  $searchkeyword;
		}
		
		$this->location = $this->get("Location");
		
		$this->radius= $session->get('radius');
		$this->customAtrributes = $session->get('customAtrributes');
		$this->customAtrributesValues = $this->get("CustomAttributeValues");
		
		if($this->appSettings->enable_search_filter){
			$this->searchFilter = $this->get('SearchFilter');
		}
		$this->pagination = $this->get('Pagination');
		
		$this->letters = $this->get('UsedLetter');
		$this->letter = JRequest::getVar('letter',null);
		$this->categorySearch = JRequest::getVar('categorySearch',null);
		$this->citySearch = JRequest::getVar('citySearch',null);
        $this->regionSearch = JRequest::getVar('regionSearch',null);
        $this->provinceSearch = JRequest::getVar('provinceSearch',null);
		$this->zipCode = JRequest::getVar('zipcode');
		$this->typeSearch = JRequest::getVar('typeSearch',null);
		$this->type = $this->get("CompanyType");
		$this->countrySearch = JRequest::getVar('countrySearch',null);
		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);
		$this->filterByFav = JRequest::getVar('filter-by-fav',null);
		$this->featured = JRequest::getVar('featured',null);
		$this->sortByOptions = $this->get('SortByConfiguration');
		$this->form_submited = JRequest::getVar('form_submited',null);
		$this->orderBy = JRequest::getVar("orderBy", $this->appSettings->order_search_listings);
		$this->preserve = JRequest::getVar('preserve',null);
		
		$session = JFactory::getSession();
		$this->location = $session->get('location');

		parent::display($tpl);
	}
}
?>
