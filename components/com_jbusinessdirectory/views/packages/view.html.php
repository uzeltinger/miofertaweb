<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');


class JBusinessDirectoryViewPackages extends JViewLegacy
{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function display($tpl = null){
		
		$this->packages =  $this->get('Packages');
		$this->customAttributes = JBusinessUtil::getPackagesAttributes($this->packages);
		
		$this->companyId = JRequest::getVar("claimCompanyId");
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$this->packageFeatures = JBusinessDirectoryHelper::getDefaultPackageFeatures($this->packages);

        $this->packageInfo =  JFactory::getApplication()->input->getString("packageInfo");

        parent::display($tpl);
	}
}
?>
