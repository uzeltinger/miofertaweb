<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
?>

<!-- layout -->
<div id="layout" class="pagewidth clearfix grid3">

<div id="grid-content" class="grid-content row-fluid grid3">

	<?php 
	if(isset($this->companies)){
    $index = 0;
    foreach ($this->companies as $index => $company){
    $index++;
    ?>

    <article class="post clearfix span3 <?php echo isset($company->featured) && $company->featured == 1 ? "featured" : "" ?>">
        <div class="post-inner">
            <?php if (isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO, $company->packageFeatures) || !$appSettings->enable_packages) { ?>
                <figure class="post-image">
                    <a href="<?php echo JBusinessUtil::getCompanyLink($company) ?>">
                        <?php if (isset($company->logoLocation) && $company->logoLocation != '') { ?>
                            <img title="<?php echo $this->escape($company->name) ?>"
                                 alt="<?php echo $this->escape($company->name) ?>"
                                 src="<?php echo JURI::root() . PICTURES_PATH . $company->logoLocation ?>">
                        <?php } else { ?>
                            <img title="<?php echo $this->escape($company->name) ?>"
                                 alt="<?php echo $this->escape($company->name) ?>"
                                 src="<?php echo JURI::root() . PICTURES_PATH . '/no_image.jpg' ?>">
                        <?php } ?>
                    </a>
                     <?php if(isset($company->featured) && $company->featured==1){ ?>
						<div class="featured-text">
	                        <?php echo JText::_("LNG_FEATURED")?>
                        </div>
		  	  		  <?php } ?>
                </figure>
            <?php } else { ?>
                <figure class="post-image">
                    <a href="<?php echo JBusinessUtil::getCompanyLink($company) ?>">
                        <img title="<?php echo $this->escape($company->name) ?>"
                             alt="<?php echo $this->escape($company->name) ?>"
                             src="<?php echo JURI::root() . PICTURES_PATH . '/no_image.jpg' ?>">
                    </a>
                      <?php if(isset($company->featured) && $company->featured==1){ ?>
						<div class="featured-text">
	                        <?php echo JText::_("LNG_FEATURED")?>
                        </div>
		  	  		  <?php } ?>
                </figure>
            <?php } ?>

            <div class="post-content">
                <h3 class="post-title"><a
                            href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"><span><?php echo $company->name ?></span></a>
                </h3>
                <span class="post-date"><span><?php echo JBusinessUtil::getAddressText($company) ?></span></span>
                <p class="company-clasificaiton">
                    <?php if (!empty($company->mainCategory)) { ?>
                        <span class="post-category">
								<a href="<?php echo JBusinessUtil::getCategoryLink($company->mainCategoryId, $company->mainCategoryAlias) ?>"><?php echo $company->mainCategory ?> </a>
							</span> <br/>
                    <?php } ?>
                <p style="word-wrap: break-word; width: auto">
                    <?php if (isset($company->typeName)) { ?>
                        <?php echo $company->typeName ?>
                    <?php } ?>
                </p>
                </p>

                <p>
                    <?php echo $company->slogan ?>
                </p>

                <?php if (($showData && (isset($company->packageFeatures) && in_array(SOCIAL_NETWORKS, $company->packageFeatures) || !$appSettings->enable_packages)
                    && ((isset($company->facebook) && strlen($company->facebook) > 3 || isset($company->twitter) && strlen($company->twitter) > 3 || isset($company->googlep) && strlen($company->googlep) > 3)))) { ?>
                    <div id="social-networks-container">

                        <ul class="social-networks">
                            <li>
                                <span class="social-networks-follow"><?php echo JText::_("LNG_FOLLOW_US") ?>
                                    : &nbsp;</span>
                            </li>
                            <?php if (isset($company->facebook) && strlen($company->facebook) > 3) { ?>
                                <li>
                                    <a title="Follow us on Facebook" target="_blank" class="share-social facebook"
                                       href="<?php echo $this->escape($company->facebook) ?>">Facebook</a>
                                </li>
                            <?php } ?>
                            <?php if (isset($company->twitter) && strlen($company->twitter) > 3) { ?>
                                <li>
                                    <a title="Follow us on Twitter" target="_blank" class="share-social twitter"
                                       href="<?php echo $this->escape($company->twitter) ?>">Twitter</a>
                                </li>
                            <?php } ?>
                            <?php if (isset($company->googlep) && strlen($company->googlep) > 3) { ?>
                                <li>
                                    <a title="Follow us on Google" target="_blank" class="share-social google"
                                       href="<?php echo $this->escape($company->googlep) ?>">Google</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

            </div>
            <!-- /.post-content -->
        </div>
        <!-- /.post-inner -->
    </article>
    <?php if ($index % 4 == 0){ ?>
</div>
<div id="grid-content" class="grid-content row-fluid grid3">
    <?php }
    }
    }?>
	 <div class="clear"></div>
</div>
</div>

	
