<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$config = new JConfig();
$user = JFactory::getUser();
$uri = JURI::getInstance();
$url = $uri->toString( array('scheme', 'host', 'port', 'path'));

$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

$showData = !($user->id==0 && $appSettings->show_details_user == 1);
$title = stripslashes($this->event->name)." | ".$config->sitename;
JHtml::_('stylesheet', 'https://fonts.googleapis.com/css?family=Raleway:700');

if(!empty($this->event->meta_title))
    $title = stripslashes($this->event->meta_title)." | ".$config->sitename;

$description = $appSettings->meta_description;
if(!empty($this->event->short_description)){
    $description = htmlspecialchars(strip_tags($this->event->short_description), ENT_QUOTES);
}else if(!empty($this->event->description)){
    $description = htmlspecialchars(JBusinessUtil::truncate(strip_tags($this->event->description),150), ENT_QUOTES);
}
if(!empty($this->event->meta_description))
    $description = $this->event->meta_description;

$keywords = $appSettings->meta_keywords;
if(!empty($this->event->meta_keywords))
    $keywords = $this->event->meta_keywords;

$document->setTitle($this->escape($title));
$document->setDescription($description);
$document->setMetaData('keywords', $keywords);

$document->addCustomTag('<meta property="og:title" content="'.$title.'"/>');
$document->addCustomTag('<meta property="og:description" content="'.$description.'"/>');

if(!empty($this->event->pictures)){
    $document->addCustomTag('<meta property="og:image" content="'.JURI::root().PICTURES_PATH.$this->event->pictures[0]->picture_path .'" /> ');
}
$document->addCustomTag('<meta property="og:type" content="website"/>');
$document->addCustomTag('<meta property="og:url" content="'.$url.'"/>');
$document->addCustomTag('<meta property="og:site_name" content="'.$config->sitename.'"/>');

$menuItemId="";
if(!empty($appSettings->menu_item_id)){
    $menuItemId = "&Itemid=".$appSettings->menu_item_id;
}
?>
<?php require_once JPATH_COMPONENT_SITE."/include/fixlinks.php"; ?>