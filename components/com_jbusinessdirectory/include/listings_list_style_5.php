<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');$idnt = rand(500, 1500);
$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
$enablePackages = $appSettings->enable_packages;
$user = JFactory::getUser();
$showData = !($user->id==0 && $appSettings->show_details_user == 1);
?>

<div id="map-view-container" class="grid-style2 extend-style5">
	<div class="row-fluid">
		<div class="span6 map-view">
			<div id="style5-map-container">
				<?php require JPATH_COMPONENT_SITE.'/include/search-map.php' ?>
			</div>
		</div>
		<div id="map-listing-container" class="span6 companies-view grid-content" itemscope itemtype="http://schema.org/ItemList">
			<?php 
			if(isset($this->companies)) {
			    $itemCount = 1;
				foreach($this->companies as $company) { ?>
					<div id="company<?php echo $company->id ?>" class="grid-item-holder span6" itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                        <span itemscope itemprop="item" itemtype="http://schema.org/Organization">
                            <div class="grid-item <?php echo isset($company->featured) && $company->featured==1?"featured":"" ?>">
                                <div >
                                	<div class="post-image">
                                    <?php if(isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO, $company->packageFeatures) || ! $appSettings->enable_packages) { ?>
                                        <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
                                            <?php if(isset($company->logoLocation) && $company->logoLocation!='') { ?>
                                                <img title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo JURI::root().PICTURES_PATH.$company->logoLocation ?>" itemprop="contentUrl" >
                                            <?php }else{ ?>
                                                <img title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl" >
                                            <?php } ?>
                                        </a>
                                    <?php } else { ?>
                                        <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>">
                                            <img title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>">
                                        </a>
                                    <?php } ?>
                                        <?php if(isset($company->featured) && $company->featured==1){ ?>
                                            <div class="featured-text">
                                                <?php echo JText::_("LNG_FEATURED")?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="info">
                                        <div class="hover_info">
                                            <h3><?php echo $company->name?></h3>
                                            <?php $address = JBusinessUtil::getShortAddress($company);?>
                                            <?php if(!empty($address)) { ?>
                                                <div class="">
                                                    <i class="dir-icon-map-marker"></i> <span itemprop="address"><?php echo $address ?></span>
                                                </div>
                                            <?php } ?>
                                            <?php if($showData && !empty($company->phone)) { ?>
                                                <div itemprop="telephone">
                                                    <i class="dir-icon-phone"></i> <?php echo $company->phone ?>
                                                </div>
                                            <?php } ?>
                                            <?php if($showData && !empty($company->website) && (isset($company->packageFeatures) && in_array(WEBSITE_ADDRESS,$company->packageFeatures) || !$enablePackages)){
                                                if ($appSettings->enable_link_following) {
                                                    $followLink = (isset($company->packageFeatures) && in_array(LINK_FOLLOW, $company->packageFeatures) && $enablePackages) ? 'rel="follow"' : 'rel="nofollow"';
                                                }else{
                                                    $followLink ="";
                                                }?>
                                                <div>
                                                    <a <?php echo $followLink ?> target="_blank" class="company-website" title="<?php echo $this->escape($company->name)?>" target="_blank" onclick="increaseWebsiteClicks(<?php echo $company->id ?>)" href="<?php echo $this->escape($company->website) ?>"><i class="dir-icon-globe"></i> <?php echo $this->escape($company->website) ?></a>
                                                </div>
                                            <?php } ?>
                                            <span style="display:none;" itemprop="url"><?php echo JBusinessUtil::getCompanyLink($company) ?></span>

                                            <?php if(!empty($company->distance)){?>
                                                <div>
                                                    <?php echo JText::_("LNG_DISTANCE").": ".round($company->distance,1)." ". ($this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM")) ?>
                                                </div>
                                            <?php } ?>
                                            <div class="item-vertical-middle">
                                                <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>" class="btn-view"><?php echo JText::_("LNG_VIEW")?></a>
                                                <a href="#" class="btn-view btn-show-marker"><?php echo JText::_("LNG_SHOW_ON_MAP")?></a>
                                                <?php if(!empty($company->latitude) && !empty($company->longitude) && $showData && (isset($company->packageFeatures) && in_array(GOOGLE_MAP,$company->packageFeatures) || !$appSettings->enable_packages)){?>
                                                    <a target="_blank" class="nowrap" href="<?php echo JBusinessUtil::getDirectionURL($this->location, $company) ?>"><?php echo JText::_("LNG_GET_MAP_DIRECTIONS")?></a>
                                                <?php }?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid-item-name">
                                    <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>"><h3 itemprop="name"><?php echo $company->name ?></h3></a>
                                    <?php if($appSettings->enable_ratings){?>
                                        <span title="<?php echo $company->review_score ?>" class="rating-review-<?php echo $idnt ?>"></span>
                                    <?php } ?>
                                </div>
                            </div>
                        </span>
                        <span style="display:none;" itemprop="position"><?php echo $itemCount ?></span>
					</div>
				<?php
                    $itemCount++;
				} ?>
			<?php } ?>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function() {
    <?php if($appSettings->enable_ratings){?>
        renderGridReviewRating(<?php echo $idnt ?>);
    <?php } ?>
	loadMapScript();

	if(jQuery(window).width()>1024){
		jQuery("#style5-map-container").height(700);
    	jQuery(window).scroll(function(){
    		var menuTop = jQuery("#sp-header-sticky-wrapper").height();
    		var containerTop = jQuery("#map-listing-container").offset().top;
    		var containerBottom= jQuery("#map-listing-container").offset().top + jQuery("#map-listing-container").height();
    		var mapContainerBottom = jQuery("#style5-map-container").offset().top+ jQuery("#style5-map-container").height();
    		
    		if(!menuTop){
    			menuTop = 90;
    		}
    
    		if(containerBottom<mapContainerBottom){
    			menuTop = 20 - (mapContainerBottom - containerBottom);
    		}
    		jQuery("#style5-map-container").css("top",Math.max(menuTop, containerTop-jQuery(this).scrollTop()));
    		
    	});

    	 jQuery("#style5-map-container").width(jQuery(".map-view").width());
	}else{
		jQuery("#style5-map-container").height(400);
	}

	
	
});
</script>