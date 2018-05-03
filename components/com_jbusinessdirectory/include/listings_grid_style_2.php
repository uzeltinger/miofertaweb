<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
$idnt = rand(500, 1500);
$enablePackages = $appSettings->enable_packages;
?>
<div class="grid-style2">
	<div class="grid-content row-fluid grid4">
	<?php 
		if(isset($this->companies)){
    $index = 0;
    foreach ($this->companies as $index => $company){
    $index++;
    ?>
        <div class="grid-item span4 <?php echo isset($company->featured) && $company->featured == 1 ? "featured" : "" ?>">
        	<div class="post-inner">
                <div class="post-image">
                    <?php if (isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO, $company->packageFeatures) || !$appSettings->enable_packages) { ?>
                        <a href="<?php echo JBusinessUtil::getCompanyLink($company) ?>">
                            <?php if (!empty($company->logoLocation)) { ?>
                                <img title="<?php echo $this->escape($company->name) ?>"
                                     alt="<?php echo $this->escape($company->name) ?>"
                                     src="<?php echo JURI::root() . PICTURES_PATH . $company->logoLocation ?>">
                            <?php } else { ?>
                                <img title="<?php echo $this->escape($company->name) ?>"
                                     alt="<?php echo $this->escape($company->name) ?>"
                                     src="<?php echo JURI::root() . PICTURES_PATH . '/no_image.jpg' ?>">
                            <?php } ?>
                        </a>
                    <?php } else { ?>
                        <a href="<?php echo JBusinessUtil::getCompanyLink($company) ?>">
                            <img title="<?php echo $company->name ?>" alt="<?php echo $this->escape($company->name); ?>"
                                 src="<?php echo JURI::root() . PICTURES_PATH . '/no_image.jpg' ?>">
                        </a>
                    <?php } ?>
                    
                     <?php if(isset($company->featured) && $company->featured==1){ ?>
						<div class="featured-text">
	                        <?php echo JText::_("LNG_FEATURED")?>
                        </div>
		  	  		  <?php } ?>
				</div>
                <div class="info"
                     onclick="document.location.href='<?php echo JBusinessUtil::getCompanyLink($company, true) ?>'">
                    <div class="hover_info">
                        <h3><?php echo $company->name ?></h3>
                        <?php $address = JBusinessUtil::getAddressText($company); ?>
                        <?php if (!empty($address)) { ?>
                            <div class="">
                                <i class="dir-icon-map-marker"></i> <?php echo JBusinessUtil::getAddressText($company) ?>
                            </div>
                        <?php } ?>

                        <?php if ($showData && !empty($company->phone)) { ?>
                            <div>
                                <i class="dir-icon-phone"></i> <?php echo $company->phone ?>
                            </div>
                        <?php } ?>

                        <?php if ($showData && !empty($company->website) && (isset($company->packageFeatures) && in_array(WEBSITE_ADDRESS, $company->packageFeatures) || !$enablePackages)) {
                            if ($appSettings->enable_link_following) {
                                $followLink = (isset($company->packageFeatures) && in_array(LINK_FOLLOW, $company->packageFeatures) && $enablePackages) ? 'rel="follow"' : 'rel="nofollow"';
                            } else {
                                $followLink = "";
                            } ?>
                            <div>
                                <a <?php echo $followLink ?> target="_blank"
                                                             title="<?php echo $this->escape($company->name) ?>"
                                                             target="_blank"
                                                             onclick="increaseWebsiteClicks(<?php echo $company->id ?>);event.stopPropagation();"
                                                             href="<?php echo $this->escape($company->website) ?>"><i
                                            class="dir-icon-globe"></i> <?php echo $company->website ?></a>
                            </div>
                        <?php } ?>

                        <div class="item-vertical-middle">
                            <a href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"
                               class="btn-view"><?php echo JText::_("LNG_VIEW") ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-item-name">
                <h3><?php echo $company->name ?></h3>
                <?php if ($appSettings->enable_ratings) { ?>
                    <span title="<?php echo $company->review_score ?>" class="rating-review-<?php echo $idnt ?>"></span>
                <?php } ?>
            </div>
        </div>
        <?php if ($index % 3 == 0){ ?>
    </div>
    <div class="grid-content row-fluid grid4">
        <?php } ?>
        <?php
        }
        }?>
	</div>
</div>

<script>
jQuery(document).ready(function(){
	<?php if($appSettings->enable_ratings){?>
        renderGridReviewRating(<?php echo $idnt ?>);
	<?php } ?>
});
</script>