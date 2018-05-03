<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

require_once 'header.php';
?>

<div id="offer-detail-1" itemscope itemtype="http://schema.org/Offer">

    <?php require_once JPATH_COMPONENT_SITE."/include/social_share.php" ?>
    <div><a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=offers'); ?>"><?php echo JText::_("BACK") ?></a></div>
    
    <div id="offer-container" class="offer-container row-fluid">
        <div class="row-fluid">
            <?php if(!empty($this->offer->pictures)){?>
                <div id="offer-image-container" class="offer-image-container span6">
                    <?php
                    $this->pictures = $this->offer->pictures;
                    require_once JPATH_COMPONENT_SITE.'/include/image_gallery.php';
                    ?>
                </div>
            <?php }?>
        <div id="offer-content" class="offer-content span6">
            <div class="dir-offer-links">
            	<div class="link-item">
            		<a href="javascript:printOffer(<?php echo $this->offer->id ?>, '<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=offer&tmpl=component"); ?>')"><i class="dir-icon-print"></i> <?php echo JText::_("LNG_PRINT")?></a>
            		<?php if($this->appSettings->enable_bookmarks) { ?>
                        <?php if(!empty($this->offer->bookmark)){?>
                            <a href="javascript:showUpdateBookmarkDialog(<?php echo $user->id==0?"1":"0"?>)"  title="<?php echo JText::_("LNG_UPDATE_BOOKMARK")?>" class="bookmark right"><i class="dir-icon-heart"></i></a>
                        <?php }else{?>
                            <a href="javascript:addBookmark(<?php echo $user->id==0?"1":"0"?>)" title="<?php echo JText::_("LNG_ADD_BOOKMARK")?>" class="bookmark right"><i class="dir-icon-heart-o"></i></a>
                        <?php } ?>
                    <?php } ?>
            		
            	</div>
                <div class="link-item">
                    <?php if($this->appSettings->enable_offer_coupons) { ?>
                        <?php if($this->offer->checkOffer) { ?>
                            <?php if($user->id !=0) { ?>
                                <a class="btn btn-primary btn-xs" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=offer.generateCoupon&id='.$this->offer->id) ?>" target="_blank">
                                    <?php echo JText::_("LNG_GENERATE_COUPON")?>
                                </a>
                            <?php } else { ?>
                                <a class="btn btn-primary btn-xs" href="javascript:showLoginNotice()">
                                    <?php echo JText::_('LNG_GENERATE_COUPON')?>
                                </a>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>

            <h3 itemprop="name">
                <?php echo $this->escape($this->offer->subject)?>
            </h3>
            <div class="offer-details">
            	<table>
		            <?php $priceClass = !empty($this->offer->specialPrice)?"price-old":""  ?>
                    <?php if(!empty($this->offer->price)){?>
                        <tr>
                            <th><?php echo JText::_('LNG_PRICE') ?>:</th>
                            <td class="<?php echo $priceClass ?>" itemprop="price"><?php echo JBusinessUtil::getPriceFormat($this->offer->price, $this->offer->currencyId) ?></td>
                            <?php if(!empty($this->offer->price_base)){?>
                                <td class="<?php echo $priceClass ?>">
                                    (<?php echo  JBusinessUtil::getPriceFormat($this->offer->price_base, $this->offer->currencyId)?>/<?php echo $this->offer->price_base_unit?>)
                                </td>
                            <?php }?>
                        </tr>
                    <?php } ?>

                    <?php if(!empty($this->offer->specialPrice)){?>
                        <tr>
                            <th><?php echo JText::_('LNG_SPECIAL_PRICE') ?>:</th>
                            <td><?php echo JBusinessUtil::getPriceFormat($this->offer->specialPrice, $this->offer->currencyId)?></td>
                            <?php if(!empty($this->offer->special_price_base)){?>
                                <td>
                                    (<?php echo  JBusinessUtil::getPriceFormat($this->offer->special_price_base, $this->offer->currencyId)?>/<?php echo $this->offer->special_price_base_unit?>)
                                </td>
                            <?php }?>
                        </tr>
                    <?php } ?>
                    <?php if(!empty($this->offer->specialPrice) && !empty($this->offer->price) && $this->offer->specialPrice < $this->offer->price){ ?>
                    <tr>
                        <th><?php echo JText::_('LNG_DISCOUNT') ?>:</th>
                        <td><?php echo JBusinessUtil::getPriceDiscount($this->offer->specialPrice, $this->offer->price) ?>%</td>
                    </tr>
                    <?php } ?>
                    <?php if (!empty($this->offer->price_text)) { ?>
                </table>
                        <div class="price-text"><h5><?php echo $this->offer->price_text;?></h5></div>
                    <?php }elseif (empty($this->offer->price) && empty($this->offer->specialPrice) && ($this->appSettings->show_offer_free)){ ?>
                        <tr>
                            <th><?php echo JText::_('LNG_PRICE') ?>:</th>
                            <td class="free-text"><?php echo JText::_('LNG_FREE') ?></td>
                        </tr>
                </table>
                    <?php }else{?>
                </table>
                    <?php }?>
                    <!-- Offer Selling Section -->
                <?php if ($this->appSettings->enable_offer_selling && $this->offer->enable_offer_selling && JBusinessUtil::checkDateInterval($this->offer->startDate, $this->offer->endDate) && $this->offer->quantity > 0) { ?>
					<div class="offer-cart-container">              		
                       		<span>
                                <?php echo JText::_('LNG_QUANTITY') ?>
                            </span>
                            <select name="quantity" id="quantity">
                                <?php
                                if($this->offer->min_purchase > 0){
                                    echo '<option value="0">0</option>';
                                }

                                $maximum = $this->offer->max_purchase;
                                if($this->offer->quantity < $this->offer->max_purchase)
                                    $maximum = $this->offer->quantity;

                                for($i=$this->offer->min_purchase;$i<=$maximum;$i++)
                                    echo '<option value="' . $i . '">' . $i . '</option>';
                                ?>
                            </select>

	                    <button class="ui-dir-button ui-dir-button-green search-dir-button" onclick="addToCart(<?php echo $this->offer->id; ?>)">
							<span class="ui-button-text">
								<i class="dir-icon-shopping-cart"></i> <?php echo JText::_('LNG_ADD_TO_CART'); ?>
							</span>
	                    </button>
               	 	</div><br/>
                <?php } ?>

                <?php $address = JBusinessUtil::getAddressText($this->offer); ?>
                <?php if(!empty($address)) { ?>
                    <div class="offer-location">
                        <span><i class="dir-icon-map-marker dir-icon-large"></i> <?php echo $address ?></span>
                    </div>
                <?php } ?>

                <?php if((!empty($this->offer->startDate) && $this->offer->startDate!="0000-00-00") || (!empty($this->offer->endDate) && $this->offer->endDate!="0000-00-00")){?>
                    <div class="offer-dates">
                        <i class="dir-icon-calendar"></i>
                        <?php
                            echo  JBusinessUtil::getDateGeneralFormat($this->offer->startDate)." - ". JBusinessUtil::getDateGeneralFormat($this->offer->endDate);
                        ?>
                    </div>
                <?php } ?>

                <?php if(!empty($this->offer->show_time) && JBusinessUtil::getRemainingtime($this->offer->endDate)!=""){?>
                    <div class="offer-dates">
                        <span ><i class="dir-icon-clock-o dir-icon-large"></i> <?php echo JBusinessUtil::getRemainingtime($this->offer->endDate)?></span>
                    </div>
                <?php } ?>

                <?php if(!empty($this->offer->categories)){?>
                    <div class="offer-categories">
                        <div><strong><?php echo JText::_("LNG_CATEGORIES")?></strong></div>
                        <?php
                        $categories = explode('#|',$this->offer->categories);
                        foreach($categories as $i=>$category){
                            $category = explode("|", $category);
                            ?>
                          	  <a rel="nofollow" href="<?php echo JBusinessUtil::getOfferCategoryLink($category[0], $category[2]) ?>"><?php echo $this->escape($category[1])?></a><?php echo $i<(count($categories)-1)? ',&nbsp;':'' ?>
                        <?php } ?>
                    </div>
                <?php }?>

                <?php if($this->appSettings->enable_attachments) { ?>
                    <?php if(!empty($this->offer->attachments)) { ?>
                        <div><strong><?php echo JText::_("LNG_ATTACHMENTS")?></strong></div>
                        <?php require "offer_attachments.php"?>
                        <div class="clear"></div>
                    <?php } ?>
                <?php } ?>
            </div>
            <?php if(!empty($this->offer->company)){?>
	            <div class="company-details" itemprop="offeredBy" itemscope itemtype="http://schema.org/Organization">
	                <table>
	                    <tr>
	                        <td><strong><?php echo JText::_('LNG_COMPANY_DETAILS') ?></strong></td>
	                    </tr>
	                    <tr>
	                        <td><a href="<?php echo JBusinessUtil::getCompanyLink($this->offer->company)?>"> <span itemprop="name"><?php echo $this->escape($this->offer->company->name)?></span></a></td>
	                    </tr>
	                    <?php $address = JBusinessUtil::getAddressText($this->offer->company); ?>
	                    <?php if(!empty($address)) { ?>
	                    <tr>
	                        <td itemprop="address"><i class="dir-icon-map-marker dir-icon-large"></i> <?php echo $this->escape($address) ?></td>
	                    </tr>
	                    <?php } ?>
	                    <?php if(!empty($this->offer->company->phone) || !empty($this->offer->company->mobile)){?>
	                        <tr>
	                            <td itemprop="telephone">
	                              <?php if(!empty($this->offer->company->phone)){ ?>
	                            	<i class="dir-icon-phone dir-icon-large"></i> <a href="tel:<?php  echo $this->escape($this->offer->company->phone); ?>"><?php  echo $this->escape($this->offer->company->phone); ?></a> &nbsp;&nbsp;
	                              <?php }?> 
	                              <?php if(!empty($this->offer->company->mobile)){ ?>
	                            	<i class="dir-icon-mobile-phone dir-icon-large"></i> <a href="tel:<?php  echo $this->escape($this->offer->company->mobile); ?>"><?php  echo $this->escape($this->offer->company->mobile); ?></a>
	                              <?php } ?>
	                              </td>
	                        </tr>
	                    <?php } ?>
	                    <?php if(!empty($this->offer->company->website)){?>
	                        <tr>
	                            <td itemprop="url">
	                            	<a target="_blank" itemprop="url" title="<?php echo $this->escape($this->offer->company->name)?> Website" onclick="increaseWebsiteClicks(<?php echo $this->offer->company->id ?>)" href="<?php echo $this->escape($this->offer->company->website) ?>"><i class="dir-icon-link "></i> <?php echo JText::_('LNG_WEBSITE') ?></a>
	                            </td>
	                        </tr>
	                    <?php } ?>
	
	                    <?php
	                    if(!empty($this->offer->company->email) && $this->appSettings->show_contact_form){ ?>
	                        <tr>
	                            <td itemprop="email" ><a href="javascript:contactCompany(<?php echo $showData?1:0 ?>)" ><i class="dir-icon-envelope"></i> <?php echo JText::_('LNG_CONTACT'); ?></a> </td>
	                        </tr>
	                        <?php
	                    } ?>
	                </table>
	            </div>
            <?php } ?>
        </div>
    </div>
    <div>
        <div class="classification">
            <?php require_once 'offer_attributes.php'; ?>
        </div>
        <div class="offer-description" itemprop="description">
             <?php echo JHTML::_("content.prepare", $this->offer->description); ?>
        </div>
        
        <?php if(!empty($this->offer->latitude) && !empty($this->offer->longitude)){ ?>
	        <div id="offer-map">
	            <?php require_once 'map.php';?>
	            <div class="clear"></div>
	        </div>
        <?php } ?>
        <span style="display: none;" itemprop="validFrom"><?php echo JBusinessUtil::getDateGeneralFormat($this->offer->startDate)?></span>
        <span style="display: none;" itemprop="validThrough"><?php echo JBusinessUtil::getDateGeneralFormat($this->offer->endDate)?></span>
    </div>
    <div class="clear"></div>
</div>

 <?php  if(!empty($this->videos)){ ?>
	<div id="offer-videos" class="company-cell">
	    <h3><?php echo JText::_("LNG_OFFER_VIDEOS")?></h3>
	    <?php require_once 'offer_videos.php';?>
	</div>
	<div class="clear"></div>
 <?php } ?>

<?php
if($this->appSettings->enable_reviews){
    ?>
    <div id="offer-reviews" class="company-cell">
        <h3><?php echo JText::_("LNG_OFFER_REVIEWS")?></h3>
        <?php require_once 'offer_reviews.php';?>
    </div>
    <div class="clear"></div>
<?php } ?>

<div id="offer-dialog" class="offer" style="display:none">
    <div id="dialog-container">
        <div class="titleBar">
            <span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
        </div>
        <div class="dialogContent">
            <iframe id="offerIfr" height="500" src="about:blank">
            </iframe>
        </div>
    </div>
</div>

</div>

<?php require_once 'offer_util.php'; ?>