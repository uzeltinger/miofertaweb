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
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/map.js');

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
class JBusinessDirectoryViewCatalog extends JViewLegacy
{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function display($tpl = null)
	{
		
		$state = $this->get('State');
		$this->params = $state->get("parameters.menu");
		
		$categoryId= JRequest::getVar('categoryId');
		$this->letter = $this->get('Letter');
		
		$this->companies = $this->get('CompaniesByLetter');
		$this->letters = $this->get('UsedLetter');
		
		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);
		
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$this->location = $this->get("Location");
		
		$pagination = $this->get('Pagination');
		$this->assignRef('pagination', $pagination);
		
		parent::display($tpl);
	}
}
?>
