<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
require_once JPATH_COMPONENT_SITE.'/classes/attributes/attributeservice.php';
$user = JFactory::getUser();

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
	$title = JText::_("LNG_CATALOG").' | '.$config->sitename;
}
$document->setTitle($title);

//set page meta description and keywords
$description = $this->appSettings->meta_description;
$document->setDescription($description);
$document->setMetaData('keywords', $this->appSettings->meta_keywords);

if(!empty($params) && $params->get('menu-meta_description') != ''){
	$document->setMetaData( 'description', $params->get('menu-meta_description') );
	$document->setMetaData( 'keywords', $params->get('menu-meta_keywords') );
}

jimport('joomla.application.module.helper');
// this is where you want to load your module position
$modules = JModuleHelper::getModules('categories-catalog');
$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
$fullWidth = true;

//add the possibility to chage the view and layout from http params
$list_layout = JRequest::getVar('list_layout');
if(!empty($list_layout)) {
	$this->appSettings->search_result_view = $list_layout;
}
$view_mode = JRequest::getVar('view_mode');
if(!empty($view_mode)) { 
	$this->appSettings->search_view_mode = $view_mode;
}

$menuItemId="";
if(!empty($this->appSettings->menu_item_id)){
	$menuItemId = "&Itemid=".$this->appSettings->menu_item_id;
}
?>

<?php if (!empty($this->params) && $this->params->get('show_page_heading', 1)) { ?>
    <div class="page-header">
        <h1 class="title"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
<?php } ?>

<?php if(isset($modules) && count($modules)>0) { ?>
	<div class="company-categories">
		<?php 
		$fullWidth = false;
		foreach($modules as $module) {
			echo JModuleHelper::renderModule($module);
		} ?>
	</div>
<?php } ?>

<div id="search-results" class="search-results <?php echo $fullWidth ?'search-results-full':'search-results-normal' ?>">

	<div class="search-header">
    	<div id="search-details">
    		<?php
    		require_once JPATH_COMPONENT_SITE.'/include/letterfilter.php';
    		?>
    		<div class="result-counter"><?php echo $this->pagination->getResultsCounter()?></div>
    		<?php if($this->appSettings->search_result_view != 5) { ?>
    			<div class="search-toggles">
    				<p class="view-mode">
    					<label><?php echo JText::_('LNG_VIEW')?></label>
    					<a id="grid-view-link" class="grid" title="Grid" href="javascript:showGrid()"><?php echo JText::_("LNG_GRID")?></a>
    					<a id="list-view-link" class="list active" title="List" href="javascript:showList()"><?php echo JText::_("LNG_LIST")?></a>
    				</p>
    				<?php if($appSettings->show_search_map && !empty($this->companies)){?>
    					<p class="view-mode">
    						<a id="map-link" class="map" title="Grid" href="javascript:showMap(true)"><?php echo JText::_("LNG_SHOW_MAP")?></a>
    					</p>
    				<?php }?>
    				
    				<?php if($this->appSettings->enable_rss == 1) { ?>
    					<p class="view-mode">
    						<a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=directoryrss.getCompaniesRss') ?>" target="_blank">
    							<img alt="<?php echo JTEXT::_("LNG_RSS") ?>" src="<?php echo JURI::base() ."components/".JBusinessUtil::getComponentName().'/assets/images/rss-icon.png' ?>" />
    						</a>
    					</p>
    				<?php } ?>
    			</div>
    		<?php } ?>
    		<div class="clear"></div>
    	</div>	
	</div>

	<div class="clear"></div>
	
	<?php if($this->appSettings->search_result_view != 5 && $appSettings->show_search_map) { ?>
		<div id="companies-map-container" style="display:none">
			<?php require_once JPATH_COMPONENT_SITE.'/include/search-map.php' ?>
		</div>
	<?php } ?>

	<?php 
	require_once JPATH_COMPONENT_SITE.'/include/listings_grid_view.php';

	if($this->appSettings->search_result_view == 1) {
		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_1.php';
	} else if($this->appSettings->search_result_view == 2) {
		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_2.php';
	} else if($this->appSettings->search_result_view == 3) {
		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_3.php';
	} else if($this->appSettings->search_result_view == 4) {
		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_4.php';
	} else if($this->appSettings->search_result_view == 5) {
		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_5.php';
	} else if($this->appSettings->search_result_view == 6){
		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_6.php';
	} else if($this->appSettings->search_result_view == 7){
		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_7.php';
	}
	else {
		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_1.php';
	} ?>
	
	<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
		<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post" name="adminForm" id="adminForm">
			<input type='hidden' name='view' value='catalog' />
			<input type='hidden' name='letter' id="letter" value='<?php echo $this->letter ?>' />
			<?php echo $this->pagination->getListFooter(); ?>
		</form>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>

<?php 
if($this->appSettings->search_result_view == 3) {
	require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_3_util.php';
}
?>

<script type="text/javascript">
	window.onload = function()	{

		<?php if($this->appSettings->enable_ratings){?>
	        renderSearchAverageRating();
		<?php } ?>
		
		jQuery('.button-toggle').click(function() {  
			if(!jQuery(this).hasClass("active")) {       
				jQuery(this).addClass('active');
			}
			jQuery('.button-toggle').not(this).removeClass('active'); // remove buttonactive from the others
		});

		<?php if ($appSettings->map_auto_show == 1) { ?>
			showMap(true);
		<?php } ?>

		<?php if ($this->appSettings->search_view_mode == 1) { ?>
			showGrid();
		<?php } else { ?>
			showList();
		<?php } ?>
	};

	function showMap(display) {
		jQuery("#map-link").toggleClass("active");

		if(jQuery("#map-link").hasClass("active")) {
			jQuery("#companies-map-container").show();
			jQuery("#map-link").html("<?php echo JText::_("LNG_HIDE_MAP")?>");
			loadMapScript();
		} else {
			jQuery("#map-link").html("<?php echo JText::_("LNG_SHOW_MAP")?>");
			jQuery("#companies-map-container").hide();
		}
	}

	function showList() {
		jQuery("#results-container").show();
		jQuery("#jbd-grid-view").hide();
		jQuery("#grid-view-link").removeClass("active");
		jQuery("#list-view-link").addClass("active");
	}

	function showGrid() {
		jQuery("#results-container").hide();
		jQuery("#jbd-grid-view").show();
		applyIsotope();
		jQuery(window).resize();
		jQuery("#grid-view-link").addClass("active");
		jQuery("#list-view-link").removeClass("active");
	}
</script>