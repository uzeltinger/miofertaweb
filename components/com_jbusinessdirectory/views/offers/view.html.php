<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/categories.css');

JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/imagesloaded.pkgd.min.js');
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/jquery.isotope.min.js');
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/isotope.init.js');

JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/map.js');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/offers.js');

// following translations will be used in js
JText::script('LNG_HIDE_MAP');
JText::script('LNG_SHOW_MAP');

class JBusinessDirectoryViewOffers extends JViewLegacy
{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function display($tpl = null)
	{
		$this->appSettings =  JBusinessUtil::getInstance()->getApplicationSettings();
		$state = $this->get('State');
		$this->params = $state->get("parameters.menu");
		
		$this->offers = $this->get('Offers');
		$this->location = $this->get("Location");
		$this->zipCode = JRequest::getVar('zipcode');
		
		$this->orderBy = JRequest::getVar("orderBy", $this->appSettings->order_search_offers);
	
		$this->categorySearch = JRequest::getVar('categorySearch',null);
		$this->citySearch = JRequest::getVar('citySearch',null);
		$this->regionSearch = JRequest::getVar('regionSearch',null);
        $this->countrySearch = JRequest::getVar('countrySearch',null);
        $this->country = $this->get('Country');
        $this->provinceSearch = JRequest::getVar('provinceSearch',null);

		$searchkeyword = JRequest::getVar('searchkeyword');
		if(isset($searchkeyword)){
			$this->searchkeyword=  $searchkeyword;
		}
		$this->selectedParams = $this->get('SelectedParams');
		$this->categories = implode(";", $this->get("SelectedCategories"));
		if(!empty($this->categories)){
			$this->categories.=";";
		}

		$this->selectedCategories = $this->get("SelectedCategories");
		
		$this->preserve = JRequest::getVar('preserve',null);
		
		$categoryId= $this->get('CategoryId');
		if(!empty($categoryId) && $this->appSettings->search_type != 1){
			$this->categoryId=$categoryId;
			$this->category = $this->get('Category');
		}	
		
		if($this->appSettings->enable_search_filter_offers){
			$this->searchFilter = $this->get('SeachFilter');
		}

		$session = JFactory::getSession();
		$this->radius= $session->get('of-radius');
		
		$this->pagination = $this->get('Pagination');
		$this->sortByOptions = $this->get('SortByConfiguration');
		
		parent::display($tpl);
	}
}
?>
