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

$total_page_string = $this->pagination->getPagesCounter();
$current_page = substr($total_page_string,5,1);
$limitStart = JFactory::getApplication()->input->get('limitstart');
if(empty($limitStart) || ($current_page == 1) || $total_page_string==null ) {
	$limitStart = 0;
}

$showData = !($user->id==0 && $appSettings->show_details_user == 1);
?>

<div id="results-container" itemscope itemtype="http://schema.org/ItemList" <?php echo $this->appSettings->search_view_mode?'style="display: none"':'' ?> class="list-intro">
<?php 
if(!empty($this->companies)){
    $itemCount = 1;
	foreach($this->companies as $index=>$company){
	?>
		<div class="result <?php echo isset($company->featured) && $company->featured==1?"featured":"" ?>">
			<div class="business-container" itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                <span itemscope itemprop="item" itemtype="http://schema.org/Organization">
                    <div class="business-info">
                    </div>

                    <div class="business-details row-fluid">
                        <?php if(isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO,$company->packageFeatures) || !$enablePackages){ ?>
                        <div class="company-image span3" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
                            <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>">
                                <?php if(isset($company->logoLocation) && $company->logoLocation!=''){?>
                                    <img title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo JURI::root().PICTURES_PATH.$company->logoLocation ?>" itemprop="contentUrl">
                                <?php }else{ ?>
                                    <img title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl">
                                <?php } ?>
                            </a>
                        </div>
                        <?php } ?>

                        <div class="result-content span9">
                            <?php if(isset($company->ad_image) && $company->ad_image!=''){?>
                                <div class="span8">
                            <?php } ?>
                            <h3 class="business-name">
                                <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>" ><?php echo $enableNumbering? "<span>".($index+ $limitStart + 1).". </span>":""?><span itemprop="name"><?php echo $company->name ?></span></a>
                            </h3>
                            <span style="display:none;" itemprop="url"><?php echo JBusinessUtil::getCompanyLink($company) ?></span>

                            <div class="company-rating" <?php echo !$enableRatings? 'style="display:none"':'' ?>>
                                <div style="display:none" class="thanks-rating tooltip">
                                    <div class="arrow">�</div>
                                    <div class="inner-dialog">
                                    <a href="javascript:void(0)" class="close-button"><?php echo JText::_('LNG_CLOSE') ?></a>
                                        <p>
                                        <strong><?php echo JText::_('LNG_THANKS_FOR_YOUR_RATING') ?></strong>
                                        </p>
                                        <p><?php echo JText::_('LNG_REVIEW_TXT') ?></p>
                                        <p class="buttons">
                                        <a onclick="" class="review-btn track-write-review no-tracks" href="">Write a Review</a>
                                        <a href="javascript:void(0)" class="close-button">X <?php echo JText::_('LNG_NOT_NOW') ?></a>
                                        </p>
                                    </div>
                                </div>

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
                            <div class="clear"></div>
                            <div class="company-info">
                                <?php if(!empty($company->comercialName)){?>
                                <h3 class="business-comercial-name" style="display:none;width: 150px;">
                                    <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>"><?php echo $company->comercialName?></a>
                                </h3>
                                <?php } ?>

                                <?php if (!empty($company->contact_name)){ ?>
                                    <span class="company-contact" >
                                        <strong><?php echo JText::_('LNG_CONTACT') ?>: </strong><span ><?php echo $company->contact_name?></span>
                                    </span>
                                <?php } ?>

                                <?php $address = JBusinessUtil::getAddressText($company);?>
                                <?php if(!empty($address)) { ?>
                                <span class="company-address">
                                    <strong><?php echo JText::_('LNG_ADDRESS') ?>: </strong><span itemprop="address"><?php echo $address ?></span>
                                </span>
                                <?php } ?>

                                <?php if(!empty($company->distance)){?>
                                    <div>
                                        <?php echo JText::_("LNG_DISTANCE").": ".round($company->distance,1)." ". ($this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM")) ?>
                                    </div>
                                <?php } ?>

                                <?php if(!empty($company->latitude) && !empty($company->longitude) && $showData && (isset($company->packageFeatures) && in_array(GOOGLE_MAP,$company->packageFeatures) || !$enablePackages)){?>
                                    <a target="_blank" class="nowrap" href="<?php echo JBusinessUtil::getDirectionURL($this->location, $company) ?>"><?php echo JText::_("LNG_GET_MAP_DIRECTIONS")?></a>
                                <?php }?>

                                <?php if( $showData && (isset($company->packageFeatures) && in_array(PHONE, $company->packageFeatures) || !$enablePackages )){ ?>
                                    <div class="comany-contact-details">
                                        <?php if(!empty($company->phone)) { ?>
                                            <span itemprop="telephone">
                                                <strong><?php echo JText::_('LNG_PHONE') ?>: </strong><a href="tel:<?php  echo $this->escape($company->phone); ?>"><?php  echo $this->escape($company->phone); ?></a>
                                            </span>
                                        <?php } ?>
                                        <?php if(!empty($company->fax)) { ?>
                                            <span  itemprop="faxNumber">
                                                <strong><?php echo JText::_('LNG_FAX') ?>: </strong><?php  echo $company->fax; ?>
                                            </span>
                                            <?php } ?>
                                        <?php if(!empty($company->mobile)) { ?>
                                            <span itemprop="telephone">
                                                <strong><?php echo JText::_('LNG_MOBILE') ?>: </strong><a href="tel:<?php  echo $this->escape($company->mobile); ?>"><?php  echo $this->escape($company->mobile); ?></a>
                                            </span>
                                        <?php } ?>
                                    </div>
                                <?php } ?>

                            </div>
                            <div class="company-intro">
                                <?php  echo $company->short_description; ?>
                                <a class="" href="<?php echo JBusinessUtil::getCompanyLink($company)?>">» <?php echo JText::_('LNG_MORE_INFO') ?></a>
                            </div>

                            <?php if(!empty($company->customAttributes)){?>
                            <div class="clear"></div>
                            <div class="classification">
                                <?php
                                    $renderedContent = AttributeService::renderAttributesFront($company->customAttributes,$enablePackages, $company->packageFeatures);
                                    echo $renderedContent;
                                ?>
                            </div>
                            <?php } ?>
                            <?php if(isset($company->ad_image) && $company->ad_image!=''){?>
                                </div>
                            <?php } ?>

                            <?php if(isset($company->ad_image) && $company->ad_image!=''){?>
                                <div class="span4">
                                    <div class="company-image" style="float: right" itemprop="add" itemscope itemtype="http://schema.org/ImageObject">
                                        <a href="javascript:void()">
                                            <?php if(isset($company->ad_image) && $company->ad_image!=''){?>
                                                <img title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo JURI::root().PICTURES_PATH.$company->ad_image ?>" itemprop="contentUrl">
                                            <?php } ?>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
            			<?php if(isset($company->featured) && $company->featured==1){ ?>
            				<div class="featured-text">
                                <?php echo JText::_("LNG_FEATURED")?>
                            </div>
            			<?php } ?>
                    </div>
                </span>
                <span style="display:none;" itemprop="position"><?php echo $itemCount ?></span>
            </div>
			
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