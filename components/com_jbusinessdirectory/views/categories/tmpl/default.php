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

//retrieving current menu item parameters
$currentMenuId = null;
$activeMenu = JFactory::getApplication()->getMenu()->getActive();
if(isset($activeMenu))
	$currentMenuId = $activeMenu->id ; // `enter code here`
$document = JFactory::getDocument(); // `enter code here`
$app = JFactory::getApplication(); // `enter code here`
if(isset($activeMenu)){
	$menuitem   = $app->getMenu()->getItem($currentMenuId); // or get item by ID `enter code here`
	$params = $menuitem->params; // get the params `enter code here`
}else{
	$params = null;
}

//set page title
if(!empty($params) && $params->get('page_title') != ''){
	$title = $params->get('page_title', '');
}
if(empty($title)){
	$title = JText::_("LNG_COMPANY_CATEGORIES");
	if($this->categoryType == CATEGORY_TYPE_EVENT)
		$title = JText::_("LNG_EVENT_CATEGORIES");
	else if($this->categoryType == CATEGORY_TYPE_OFFER)
		$title = JText::_("LNG_OFFER_CATEGORIES");

	$title .=' | '.$config->sitename;
}
$document->setTitle($title);

//set page meta description and keywords
$description = $this->appSettings->meta_description;
$document->setDescription($description);
$document->setMetaData('keywords', $this->appSettings->meta_keywords);

$view_mode = JRequest::getVar('view_style');
if(!empty($view_mode)) {
	$this->appSettings->category_view = $view_mode;
}
?>

<?php if (!empty($this->params) && $this->params->get('show_page_heading', 1)) { ?>
    <div class="page-header">
        <h1 class="title"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
<?php } ?>
 
<?php
	$categories=$this->categories;
	$appSettings=$this->appSettings;

  	if($this->appSettings->category_view==1){
        require_once JPATH_SITE.'/components/com_jbusinessdirectory/include/categories_style_1.php';
	}else if($this->appSettings->category_view==3){
		require_once JPATH_SITE.'/components/com_jbusinessdirectory/include/categories_style_3.php';
	}
	else if($this->appSettings->category_view==4){
		require_once JPATH_SITE.'/components/com_jbusinessdirectory/include/categories_style_4.php';
	}
    else if($this->appSettings->category_view==5){
        require_once JPATH_SITE.'/components/com_jbusinessdirectory/include/categories_style_5.php';
    }
	else{
		require_once JPATH_SITE."/components/com_jbusinessdirectory/include/categories_style_2.php";
	}
?>