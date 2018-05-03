<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/map.js');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/events.js');

// following translations will be used in js
JText::script('LNG_HIDE_MAP');
JText::script('LNG_SHOW_MAP');

class JBusinessDirectoryViewEvents extends JViewLegacy
{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function display($tpl = null)
	{
		$state = $this->get('State');
		$this->params = $state->get("parameters.menu");
		
		$events = $this->get('Events');
		$this->assignRef('events', $events);
		
		$categoryId= JRequest::getVar('categoryId');
		$this->assignRef('categoryId', $categoryId);
		
		$this->searchkeyword= JRequest::getVar('searchkeyword');		
		$this->appSettings =  JBusinessUtil::getInstance()->getApplicationSettings();
		$this->zipCode = JRequest::getVar('zipcode');
		$this->location = $this->get("Location");
		$this->orderBy = JRequest::getVar("orderBy");

		$this->orderBy = JRequest::getVar("orderBy", $this->appSettings->order_search_events);

		$this->categorySearch = JRequest::getVar('categorySearch',null);
		$this->citySearch = JRequest::getVar('citySearch',null);
		$this->regionSearch = JRequest::getVar('regionSearch',null);
        $this->provinceSearch = JRequest::getVar('provinceSearch',null);

		$this->typeSearch = JRequest::getVar('typeSearch',null);
		$this->startDate = JRequest::getVar('startDate',null);
		$this->endDate = JRequest::getVar('endDate',null);
        $this->countrySearch = JRequest::getVar('countrySearch',null);
        $this->country = $this->get('Country');


		$this->selectedParams = $this->get('SelectedParams');
		$this->type = $this->get("EventType");
		$categories = $this->get('Categories');
		$this->assignRef('categories', $categories);

		$this->categories = implode(";", $this->get("SelectedCategories"));
		if(!empty($this->categories)){
			$this->categories.=";";
		}

		$this->selectedCategories = $this->get("SelectedCategories");

		if($this->appSettings->enable_search_filter_events){
			$serachFilter = $this->get('SeachFilter');
			$this->assignRef('searchFilter', $serachFilter);
		}

		$session = JFactory::getSession();
		$this->radius= $session->get('ev-radius');
		
		$categoryId= $this->get('CategoryId');
		if(!empty($categoryId) && $this->appSettings->search_type != 1){
		    $this->categoryId=$categoryId;
		    $this->category = $this->get('Category');
		}	
		
		$this->pagination = $this->get('Pagination');
		$this->sortByOptions = $this->get('SortByConfiguration');
		

		parent::display($tpl);
	}
}
?>
