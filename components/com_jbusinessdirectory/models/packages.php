<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelitem');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

class JBusinessDirectoryModelPackages extends JModelItem
{ 
	
	function __construct()
	{
		parent::__construct();
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	}

	/**
	 * Get available packages with included features.
	 * @return array
	 */
	function getPackages(){
		$user = JFactory::getUser();
		$groups = $user->get('groups');
		$packageTable = JTable::getInstance("Package", "JTable");
		$packages = $packageTable->getPackages();
		
		foreach($packages as $index => $package){
			$package->features = explode(",", $package->featuresS);
			$package->features[]= "multiple_categories";
			$packageUsergroup = explode(',',$package->package_usergroup);
			$intersect = array_intersect($groups,$packageUsergroup);
			if (empty($intersect) && !in_array('8',$groups ) && !in_array('1',$packageUsergroup)){ //8 is the id of super user, he has the right to show all the packages, '1' is id for public usergroup
				unset($packages[$index]);
			}
			//dump($package->features);
			//$position = array_search(WEBSITE_ADDRESS,$package->features);
			//dump($position);
			//$package->features = array_merge(array_slice($package->features, 0, $position), array("multiple_categories"), array_slice($package->features, $position));
		}
				
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updatePackagesTranslation($packages);
		}

		if (empty($packages)){
			JFactory::getApplication()->enqueueMessage(JText::_('LNG_NO_ACTIVE_PACKAGE'), 'warning');
		}
		
		return $packages;
	}
	
}
?>

