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

<div id="offer-detail-2">
    <div class="section group">
        <div class="col span_4_of_12 left-side">
            <div>
                <?php if(!empty($this->offer->categories)){?>
                    <?php
                    $categories = explode('#|',$this->offer->categories);
                    foreach($categories as $i=>$category){
                        $category = explode("|", $category);
                        ?>
                        <?php if(isset($category[3]) && !empty($category[3])) { ?>
                            <div class="aio-icon-component">
                                <a class="aio-icon-box-link"
                                   href="<?php echo JBusinessUtil::getOfferCategoryLink($category[0], $category[2]) ?>">
                                    <div class="aio-icon-box">
                                        <div class="aio-icon-top">
                                            <h4 style="color:<?php echo $category[4]; ?>;">
                                                <i class="dir-icon-<?php echo $category[3]; ?>"></i>
                                            </h4>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                    }
                    ?>
                <?php } ?>
            </div>
            <div>
                <?php if(!empty($this->offer->pictures)){
                    ?>
                    <div id="hover-effect" style="background: url('<?php echo JURI::root().PICTURES_PATH.$this->offer->pictures[0]->picture_path ?>') no-repeat center center ;"></div>
                <?php }else{?>
                    <div style="background: url('<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>') no-repeat center center ;"></div>
                <?php } ?>
            </div>
            <div class="row-fluid start-date">
                <div class="date-event">
                    <strong>
                        <h4><i class="dir-icon-calendar">&nbsp;<?php echo JText::_('LNG_START')?>: </i></h4>
                        <h4>
                            <?php if((!empty($this->offer->startDate) && $this->offer->startDate!="0000-00-00")){
                                echo  JBusinessUtil::getDateGeneralFormat($this->offer->startDate);
                            } ?>
                        </h4>
                    </strong>
                </div>
                <div class="date-event">
                    <strong>
                        <h4>
                            <?php if((!empty($this->offer->publish_start_time))){
                                echo  JBusinessUtil::getTimeText($this->offer->publish_start_time);
                            } ?>
                        </h4>
                    </strong>
                </div>
            </div>
            <div class="row-fluid end-date">
                <div class="date-event">
                    <strong>
                        <h4><i class="dir-icon-calendar">&nbsp;<?php echo JText::_('LNG_END')?>: </i></h4>
                        <h4>
                            <?php if((!empty($this->offer->endDate) && $this->offer->endDate!="0000-00-00")){
                                echo  JBusinessUtil::getDateGeneralFormat($this->offer->endDate);
                            } ?>
                        </h4>
                    </strong>
                </div>
                <div class="date-event">
                    <strong>
                        <h4>
                            <?php if((!empty($this->offer->publish_end_time))){
                                echo  JBusinessUtil::getTimeText($this->offer->publish_end_time);
                            } ?>
                        </h4>
                    </strong>
                </div>
            </div>
            <?php if($this->appSettings->enable_offer_coupons) { ?>
                <?php if($this->offer->checkOffer) { ?>
                    <div class="coupon-offer">
                    <?php if($user->id !=0 ) { ?>
                        <h5><a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=offer.generateCoupon&id='.$this->offer->id) ?>" target="_blank">
                            <?php echo JText::_("LNG_GENERATE_COUPON_UPCASE")?>
                        </a></h5>
                    <?php } else { ?>
                        <h5>
                        	<a href="javascript:showCouponLoginDialog()">
                            	<?php echo JText::_('LNG_GENERATE_COUPON_UPCASE')?>
                        	</a>
                        </h5>
                    <?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>
            <div class="event-locat">
                <h5><?php echo JText::_('LNG_OFFER_VENUE')?></h5>
                <p><?php echo JBusinessUtil::getAddressText($this->offer)?></p>
                <p class="gps-event">
                    GPS: <span><?php echo $this->offer->latitude?><br>
                    <?php echo $this->offer->longitude?></span>
                </p>
                <?php if(!empty($this->offer->company->email) && $this->appSettings->show_contact_form) { ?>
                    <div class="row-fluid" style="text-align: center;">
                        <a href="javascript:contactCompany(<?php echo $showData?1:0 ?>)" ><i class="dir-icon-envelope"></i> <?php echo JText::_('LNG_CONTACT'); ?></a>
                    </div>
                <?php } ?>
            </div>
            
            <?php if($this->appSettings->enable_attachments) { ?>
                <?php if(!empty($this->offer->attachments)) { ?>
                    <div class="event-locat">
                        <h5 style="text-align: left !important;"><?php echo JText::_('LNG_ATTACHMENTS')?></h5>
                        <?php require "offer_attachments.php"?>
                        <div class="clear"></div>
                    </div>
                <?php } ?>
            <?php } ?>
            
        </div>
        <div class="col span_8_of_12 right-side">
            <div class="offer-name-simple">
                <h1>
                    <?php echo $this->offer->subject?>
                </h1>
            </div>
            <div class="offer-description">
                <?php echo $this->offer->description?>
            </div>
            <div class="price-offer">
                <hr>
                <?php $priceClass = !empty($this->offer->specialPrice)?"price-old":""  ?>
                <?php if(!empty($this->offer->price)){?>
                    <div class="row-fluid price-detail">
                        <div class="span1"><div><i class="dir-icon-caret-right"></i></div></div>
                        <div class="span8"><div><?php echo JText::_('LNG_PRICE')?></div></div>
                        <div class="span3">
                            <div <?php if(!empty($this->offer->specialPrice)){?> style="text-decoration: line-through;"<?php } ?>>
                                <?php echo JBusinessUtil::getPriceFormat($this->offer->price, $this->offer->currencyId) ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if(!empty($this->offer->specialPrice)){?>
                    <div class="row-fluid specialprice-detail">
                        <div class="span1"><div><i class="dir-icon-caret-right"></i></div></div>
                        <div class="span8"><div><?php echo JText::_('LNG_SPECIAL_PRICE')?></div></div>
                        <div class="span3"><div><?php echo JBusinessUtil::getPriceFormat($this->offer->specialPrice, $this->offer->currencyId) ?></div></div>
                    </div>
                <?php } ?>
                <?php if(empty($this->offer->specialPrice) && empty($this->offer->price) && ($this->appSettings->show_offer_free)){?>
                    <div>
                        <div class="free-text"><h4><?php echo JText::_('LNG_FREE') ?></h4></div>
                    </div>
                <?php } ?>
            </div>

		  <div class="price-offer">
	            <?php
	            $renderedContent = AttributeService::renderAttributesFront($this->offerAttributes,false, array());
	            echo $renderedContent;
	            ?>
	        </div>
	            
            
            <div id="offer-map">
                <?php require_once 'map.php';?>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="organizer section group business-offer">
        <div class="col span_4_of_12">
            <a href="<?php echo JBusinessUtil::getCompanyLink($this->offer->company)?>">
                <?php if(isset($this->offer->company->logoLocation) && $this->offer->company->logoLocation!=''){?>
                    <div class="hover-offer" style="background: url('<?php echo JURI::root().PICTURES_PATH.$this->offer->company->logoLocation ?>')  no-repeat center center ;">
                        <div>
                            <?php echo JText::_('LNG_ORGANIZER')?>
                        </div>
                    </div>
                <?php }else{ ?>
                    <div style="background: url('<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>')  no-repeat center center;"></div>
                <?php } ?>
            </a>
        </div>
        <div class="col span_8_of_12">
            <div class="section group">
                <div class="col span_6_of_12 title-event">
                    <h4><a href="<?php echo JBusinessUtil::getCompanyLink($this->offer->company)?>"> <span><?php echo $this->offer->company->name?></span></a></h4>
                    <h5><span class="business-slogan"><?php echo JBusinessUtil::truncate($this->offer->company->slogan, 50); ?> </span></h5>
                </div>
                <div class="col span_6_of_12">
                    <div class="column-social">
                        <?php if(!empty($this->offer->company->facebook)) { ?>
                            <a href="<?php echo $this->offer->company->facebook ?>">
                                <div class="social-event face-event">
                                        <i class="dir-icon-facebook"></i>
                                </div>
                            </a>
                        <?php } ?>
                        <?php if(!empty($this->offer->company->twitter)) { ?>
                            <a href="<?php echo $this->offer->company->twitter ?>">
                                <div class="social-event"><i class="dir-icon-twitter"></i></div>
                            </a>
                        <?php } ?>
                        <?php if(!empty($this->offer->company->googlep)) { ?>
                            <a href="<?php echo $this->offer->company->googlep ?>">
                                <div class="social-event"><i class="dir-icon-google-plus"></i></div>
                            </a>
                        <?php } ?>
                        <?php if(!empty($this->offer->company->linkedin)) { ?>
                            <a href="<?php echo $this->offer->company->linkedin ?>">
                                <div class="social-event"><i class="dir-icon-linkedin"></i></div>
                            </a>
                        <?php } ?>
                        <?php if(!empty($this->offer->company->skype)) { ?>
                            <a href="<?php echo $this->offer->company->skype ?>">
                                <div class="social-event"><i class="dir-icon-skype"></i></div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="desc-event">
                <?php if(isset($this->offer->company->short_description)) { ?>
                    <div>
                        <?php echo JBusinessUtil::truncate($this->offer->company->short_description, 700);?>
                    </div>
                <?php } ?>
            </div>
            <div class="contact-event">
                <?php if(!empty($this->offer->company->phone)) { ?>
                    <div class="row-fluid">
                        <div class="span4"><?php echo JText::_("LNG_TELEPHONE")?>: </div>
                        <div class="span8"><a href="tel:<?php  echo $this->escape($this->offer->company->phone); ?>"><?php echo $this->escape($this->offer->company->phone); ?></a></div>
                    </div>
                <?php } ?>
                <?php if(!empty($this->offer->company->website)) {?>
                    <div class="row-fluid">
                        <div class="span4"><?php echo JText::_("LNG_WEB")?>: </div>
                        <div class="span8"><a target="_blank" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=companies&task=companies.showCompanyWebsite&companyId='.$this->offer->company->id) ?>"><?php echo $this->escape($this->offer->company->website); ?></a></div>
                    </div>
                <?php } ?>
                <?php $address = JBusinessUtil::getAddressText($this->offer->company); ?>
                <?php if(!empty($address)) { ?>
                    <div class="row-fluid">
                        <div class="span4"><?php echo JText::_("LNG_LOCATION")?>: </div>
                        <div class="span8"><a href="javascript:void(0)"> <?php echo $this->escape($address) ?></a></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div id="offer-videos" class="company-cell">
        <h2><?php echo JText::_("LNG_OFFER_VIDEOS")?></h2>
        <?php require_once 'offer_videos.php';?>
    </div>
    <div class="clear"></div>

    <?php if($this->appSettings->enable_reviews){ ?>
        <div class="offer-reviews-simple">
            <div class="row-fluid">
                <div class="span1">
                    <h4><i class="dir-icon-comments"></i></h4>
                </div>
                <div class="span11">
                    <h3><?php echo JText::_("LNG_LEAVE_REPLY")?></h3>
                </div>
            </div>
            <div class="review-event">
                <?php require_once 'offer_reviews.php';?>
            </div>
        </div>
        <div class="clear"></div>
    <?php } ?>
</div>

<?php require_once 'offer_util.php'; ?>