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

$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
$enableSEO = $appSettings->enable_seo;
$enablePackages = $appSettings->enable_packages;
$enableRatings = $appSettings->enable_ratings;
$enableNumbering = $appSettings->enable_numbering;
$user = JFactory::getUser();

$showData = !($user->id==0 && $appSettings->show_details_user == 1);
?>

<div id="results-container" itemscope itemtype="http://schema.org/ItemList" class="list-contact" <?php echo $this->appSettings->search_view_mode?'style="display: none"':'' ?>>
<?php 
if(!empty($this->companies)){
    $itemCount = 1;
	foreach($this->companies as $index=>$company){
	?>
		<div class="result <?php echo isset($company->featured) && $company->featured==1?"featured":"" ?>">
			<?php if(!empty($company->featured)){?>
			
			<div class="business-container row-fluid" itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                <span itemscope itemprop="item" itemtype="http://schema.org/Organization">
                    <div class="business-info span4">
                        <?php if(isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO,$company->packageFeatures) || !$enablePackages){ ?>
                        <div class="company-image" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
                            <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>">
                                <?php if(isset($company->logoLocation) && $company->logoLocation!=''){?>
                                    <img title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo JURI::root().PICTURES_PATH.$company->logoLocation ?>" itemprop="contentUrl" />
                                <?php }else{ ?>
                                    <img title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl" />
                                <?php } ?>
                            </a>
                        </div>
                        <?php } ?>

                        <div>
                            <span class="company-address">
                                <span itemprop="address"><?php echo JBusinessUtil::getAddressText($company) ?></span>
                            </span>

                            <?php if( $showData && (isset($company->packageFeatures) && in_array(PHONE, $company->packageFeatures) || !$enablePackages )){ ?>
                                <?php if(!empty($company->phone)) { ?>
                                    <span class="phone" itemprop="telephone">
                                            <a href="tel:<?php  echo $this->escape($company->phone); ?>"><?php  echo $this->escape($company->phone); ?></a>
                                    </span>
                                <?php } ?>
                            <?php } ?>

                        </div>
                        <div class="company-rating" <?php echo !$enableRatings? 'style="display:none"':'' ?>>
                            <div style="display:none" class="rating-awareness tooltip">
                                <div class="arrow">�</div>
                                <div class="inner-dialog">
                                <a href="javascript:void(0)" class="close-button" onclick="jQuery(this).parent().parent().hide()"><?php echo JText::_('LNG_CLOSE') ?></a>
                                <strong><?php echo JText::_('LNG_INFO') ?></strong>
                                    <p>
                                        <?php echo JText::_('LNG_YOU_HAVE_TO_BE_LOGGED_IN') ?>
                                    </p>
                                </div>
                            </div>
                            <?php if($this->appSettings->enable_ratings) { ?>
                                <div class="rating">
                                    <p class="rating-average" title="<?php echo $company->review_score?>" id="<?php echo $company->id?>" style="display: block;"></p>
                                </div>
                                <div class="review-count">
                                    <a <?php echo $company->review_score == 0 ? 'style="display:none"':'' ?>></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="business-details span8">
                        <div class="result-content">
                            <h3 class="business-name">
                                <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>" ><span itemprop="name"> <?php echo $company->name?> </span></a>
                            </h3>
                            <span style="display:none;" itemprop="url"><?php echo JBusinessUtil::getCompanyLink($company) ?></span>

                            <div class="company-short-description">
                                <?php echo $company->short_description?>
                            </div>

                            <div class="company-options">
                                <ul>
                                    <?php if(!empty($company->email)){?>
                                    	<?php if($appSettings->show_contact_form){?>
	                                        <li><a  href="javascript:showContactCompanyList(<?php echo $company->id?>,<?php echo $showData?"1":"0"?>)"><?php echo JText::_('LNG_CONTACT') ?></a></li>
	                                     <?php } ?>
                                        <li><a  href="javascript:showQuoteCompany(<?php echo $company->id?>,<?php echo $showData?"1":"0"?>)"><?php echo JText::_('LNG_QUOTE') ?></a></li>
                                    <?php } ?>
                                    <li><a  href="<?php echo JBusinessUtil::getCompanyLink($company)?>"><?php echo JText::_('LNG_MORE_INFO') ?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </span>
                <span style="display:none;" itemprop="position"><?php echo $itemCount ?></span>
            </div>
			<?php }else{?>
				<div class="row-fluid">
					<div class="span12">
						<div class="company-options right">
							<ul>
								<li><a href="<?php echo JBusinessUtil::getCompanyLink($company)?>"><?php echo JText::_('LNG_MORE_INFO') ?></a></li>
							</ul>
						</div>	
						<h3 class="business-name">
							<a href="<?php echo JBusinessUtil::getCompanyLink($company)?>" ><span itemprop="name"> <?php echo $company->name?> </span></a>
						</h3>
						
						<div class="company-address">
							<span itemprop="address"><?php echo JBusinessUtil::getAddressText($company) ?></span>
						</div>
						
						<div class="clear"></div>
						<div class="company-options left">
							<?php if(!empty($company->latitude) && !empty($company->longitude) && $showData && (isset($company->packageFeatures) && in_array(GOOGLE_MAP,$company->packageFeatures) || !$enablePackages)){?>
								<a target="_blank" href="<?php echo JBusinessUtil::getDirectionURL($this->location, $company) ?>"><?php echo JText::_("LNG_GET_MAP_DIRECTIONS")?></a>
							<?php }?>
						</div>

                        <?php if(!empty($company->distance)){?>
                            <div class="company-options right">
                                <?php echo JText::_("LNG_DISTANCE").": ".round($company->distance,1)." ". ($this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM")) ?>
                            </div>
                        <?php } ?>
						
					</div>	
				</div>
			<?php }?>
			
				<?php if(isset($company->featured) && $company->featured==1){ ?>
						<div class="featured-text">
							<?php echo JText::_("LNG_FEATURED")?>
						</div>
					<?php } ?>
			
			<div class="result-actions">
				<ul>
					<li> </li>
				</ul>
			</div>
			<div class="clear"></div>
		</div>
	<?php
        $itemCount++;
	}
}
?>
</div>

<script>
	jQuery(document).ready(function () {
		jQuery("#search-contact-terms-conditions-link").click(function () {
			jQuery("#search_contact_term_conditions_text").toggle();
		});

		jQuery("#company-quote-terms-conditions-link").click(function () {
			jQuery("#company_quote_term_conditions_text").toggle();
		});
	});
</script>