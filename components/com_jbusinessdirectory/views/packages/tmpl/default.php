<?php // no direct access
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
	$title = JText::_("LNG_PACKAGES").' | '.$config->sitename;
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

$user = JFactory::getUser();

if(!$this->appSettings->enable_packages){
	echo JText::_("LNG_PACKAGES_ARE_NOT_ENABLED");
	return;
}

$menuItemId="";
if(!empty($this->appSettings->menu_item_id)){
	$menuItemId = "&Itemid=".$this->appSettings->menu_item_id;
}

if (!empty($this->packages)){
    $nrPackages = count($this->packages);
    $offset= array(4,3,2);
    $cellOffset = $nrPackages<4?"offset".$offset[$nrPackages-1]:"";
    
?>
<div id="plans-container">
	<div id="process-container" class="process-container">
		<ol class="process-steps">
			<li class="is-active dir-icon-inbox" data-step="1">
				<p><?php echo JText::_("LNG_CHOOSE_PACKAGE")?></p>
			</li>
			<li class="dir-icon-user" data-step="2">
				<p><?php echo JText::_("LNG_BASIC_INFO")?></p>
			</li>
			<li class="progress__last dir-icon-file-text-o" data-step="3">
				<p><?php echo JText::_("LNG_LISTING_INFO")?></p>
			</li>
		</ol>
		<div class="clear"></div>
	</div>

    <?php if(!empty($this->packageInfo)) { ?>
        <div class="package-header">
            <p><?php echo $this->packageInfo ?></p>
        </div>
    <?php } ?>

	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.checkUser'); ?>" method="post" name="package-form" id="package-form" >
	<div class="featured-product-container row-fluid">
		<?php $k=0;?>
		<?php foreach($this->packages as $package){?>	
			<?php $k= $k+1; ?>
			
			<div class="featured-product-col higlight-enable span3 <?php echo $k==1?$cellOffset:"" ?>" >
				<?php if($package->popular){?>
					<div class="popular-plan"><?php echo JText::_("LNG_POPULAR")?></div>
				<?php } ?>
				
				<?php require "default_package.php" ?>
				
				<div class="select-buttons">
					<button type="button" class="ui-dir-button ui-dir-button-green" onclick="selectPackage(<?php echo $package->id?>)">
						<span class="ui-button-text"><?php echo JText::_("LNG_SELECT_PACKAGE")?></span>
					</button>
					<div class="clear"></div>
				</div>
			</div>
			
			<?php if($k%4==0){?>
				</div>
				<div class="featured-product-container row-fluid">
			<?php }?>
		<?php } ?>
		</div>
		<div class="clear"></div>
		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" /> 
		<input type="hidden" name="filter_package" id="filter_package" value="" />
		<input type="hidden" name="companyId" value="<?php echo $this->companyId ?>" />
		<input type="hidden" name="task" value="businessuser.checkUser" /> 
	</form>
</div>
<?php } ?>
<script>

jQuery(document).ready(function(){
	//jQuery('div.featured-product-col').removeClass("highlight");
	jQuery('div.higlight-enable').mouseenter(function() {
		jQuery(this).addClass("highlight");
	}).mouseleave(function() {
		jQuery(this).removeClass("highlight");
	});

	calibrateElements();
	jQuery(window).resize(function(){
		calibrateElements();
	});

});

function calibrateElements(){
	
	jQuery("#features > .featured-product-cell").each(function(index){
		jQuery("#hss > .featured-product-cell:nth-child("+(index+2)+")").height(jQuery(this).height()-1);
		jQuery("#hsp > .featured-product-cell:nth-child("+(index+2)+")").height(jQuery(this).height()-1);
		jQuery("#hms > .featured-product-cell:nth-child("+(index+2)+")").height(jQuery(this).height()-1);
		jQuery("#hmp > .featured-product-cell:nth-child("+(index+2)+")").height(jQuery(this).height()-1);
		jQuery("#hpp > .featured-product-cell:nth-child("+(index+2)+")").height(jQuery(this).height()-1);
	});

	var height=0;
	jQuery(".price .item2").each(function(){
		if(height<jQuery(this).height()){
			height = jQuery(this).height();
		}
	});

	jQuery(".price .item2").each(function(){
		jQuery(this).height(height);
	});

	var height=0;
	jQuery(".price .item1").each(function(){
		if(height<jQuery(this).height()){
			height = jQuery(this).height();
		}
	});

	jQuery(".price .item1").each(function(){
		jQuery(this).height(height);
	});
};	

function selectPackage(packageId){
		jQuery("#filter_package").val(packageId);
		jQuery("#package-form").submit();
}

</script>