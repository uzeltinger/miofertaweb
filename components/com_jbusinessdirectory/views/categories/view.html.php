<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryViewCategories extends JViewLegacy
{
	function display($tpl = null)
	{
		$state = $this->get('State');
		$this->params = $state->get("parameters.menu");
		
		$categories = $this->get('Categories');
		$this->assignRef('categories', $categories);

		$this->categoryType = $this->get('CategoryType');
        $this->categoryIds =  JRequest::getVar("CategoryID");  //$this->get('categoryIds');

		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		parent::display($tpl);
	}
}
?>
