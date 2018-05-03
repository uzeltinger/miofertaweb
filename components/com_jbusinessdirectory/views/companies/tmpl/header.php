<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();

$uri     = JURI::getInstance();
$current = $uri->toString( array('scheme', 'host', 'port', 'path'));

$company = $this->company;
$url = JBusinessUtil::getCompanyLink($company);

//set metainfo

$document = JFactory::getDocument();
$config = new JConfig();

$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

$title = stripslashes($company->name)." | ".$config->sitename;
if(!empty($company->meta_title))
	$title = stripslashes($company->meta_title);

$description = $appSettings->meta_description;
if(!empty($company->short_description)){
	$description = htmlspecialchars(strip_tags($company->short_description), ENT_QUOTES);	
}else if(!empty($company->description)){
	$description = htmlspecialchars(JBusinessUtil::truncate(strip_tags($company->description),150), ENT_QUOTES);
}
if(!empty($company->meta_description))
	$description = $company->meta_description;

$keywords = $appSettings->meta_keywords;
if(!empty($company->keywords))
	$keywords = $company->keywords;

$document->setTitle($title);
$document->setDescription($description);
$document->setMetaData('keywords', $keywords);
$document->addCustomTag('<meta property="og:title" content="'.$title.'"/>');
$document->addCustomTag('<meta property="og:description" content="'.$description.'"/>');

if(isset($this->company->logoLocation) && $this->company->logoLocation!=''){
	$document->addCustomTag('<meta property="og:image" content="'.JURI::root().PICTURES_PATH.$this->company->logoLocation .'" /> ');
}
$document->addCustomTag('<meta property="og:type" content="website"/>');
$document->addCustomTag('<meta property="og:url" content="'.$url.'"/>');
$document->addCustomTag('<meta property="og:site_name" content="'.$config->sitename.'"/>');

$showData = !($user->id==0 && $appSettings->show_details_user == 1);
$showNotice = ($appSettings->enable_reviews_users && $user->id ==0)?1:0;

$menuItemId="";
if(!empty($this->appSettings->menu_item_id)){
	$menuItemId = "&Itemid=".$this->appSettings->menu_item_id;
}
?>

<?php require_once JPATH_COMPONENT_SITE."/include/fixlinks.php"?>

<script>
    var url = jbdUtils.siteRoot + 'index.php?option=com_jbusinessdirectory';
</script>
