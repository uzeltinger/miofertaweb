<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');


JBusinessUtil::includeValidation();

class JBusinessDirectoryViewBusinessUser extends JViewLegacy
{

	function __construct()
	{
		parent::__construct();
	}
	
	function display($tpl = null){
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$this->filter_package = JFactory::getApplication()->input->get("filter_package");
		$this->package = JBusinessUtil::getPackage($this->filter_package);
		$this->packageFeatures = JBusinessDirectoryHelper::getDefaultPackageFeatures($this->package);
		$this->customAttributes = JBusinessUtil::getPackagesAttributes($this->package);

		parent::display($tpl);
	}
	
}
?>
