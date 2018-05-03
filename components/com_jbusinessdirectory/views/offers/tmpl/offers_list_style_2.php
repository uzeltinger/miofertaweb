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

<div id="offer-list-view" itemscope itemtype="http://schema.org/OfferCatalog" class='offer-container <?php echo $fullWidth ?'full':'noClass' ?>' <?php echo $this->appSettings->offers_view_mode?'style="display: none"':'' ?>>
    <ul class="offer-list">
        <?php
        if(isset($this->offers) && count($this->offers)>0){
            foreach ($this->offers as $offer){ ?>
                <li itemscope itemprop="itemListElement" itemtype="http://schema.org/Offer">
                    <div id="offer-style2" class="offer-box section group <?php echo !empty($offer->featured)?"featured":"" ?>">
                        <div class="offer-img-container col span_3_of_12">
                            <a class="offer-image" href="<?php echo $this->escape($offer->link) ?>" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
                                <?php if(isset($offer->picture_path) && $offer->picture_path!=''){?>
                                    <div id="hover-effect" style="background: url('<?php echo JURI::root().PICTURES_PATH.$offer->picture_path ?>')  no-repeat center center ;"></div>
                                <?php }else{?>
                                    <div style="background: url('<?php echo JURI::root().PICTURES_PATH.$offer->picture_path.'/no_image.jpg' ?>')  no-repeat center center ;"></div>
                                <?php } ?>
                            </a>
                        </div>
                        <div class="offer-content col span_9_of_12">
                            <div class="section group">
                                <div class="col span_8_of_12 event-left">
                                    <div class="offer-subject" itemprop="url">
                                        <h3 class="offer-name">
                                            <a title="<?php echo $this->escape($offer->subject)?>"
                                               href="<?php echo $this->escape($offer->link) ?>"><span itemprop="name"><?php echo $this->escape($offer->subject)?></span>
                                            </a>
                                        </h3>
                                    </div>

                                    <div class="offer-desciption short-desc" itemprop="description">
                                        <?php echo JBusinessUtil::truncate($offer->short_description, 200); ?>
                                    </div>

                                    <?php if(empty($offer->show_time) && JBusinessUtil::getRemainingtime($offer->endDate)!=""){?>
                                        <div class="offer-remaining">
                                            <span ><i class="dir-icon-clock-o dir-icon-large"></i> <?php echo JBusinessUtil::getRemainingtime($offer->endDate)?></span>
                                        </div>
                                    <?php } ?>

                                    <?php $address =JBusinessUtil::getAddressText($offer); ?>
                                    <?php if(!empty($address)){ ?>
                                        <div class="offer-location">
                                            <span><i class="dir-icon-map-marker dir-icon-large light-blue-marker"></i> <?php echo JBusinessUtil::getAddressText($offer)?></span>
                                        </div>
                                    <?php } ?>
                                </div>

                                <?php if(!empty($offer->distance)){?>
	                                <div>
	                                    <?php echo JText::_("LNG_DISTANCE").": ".round($offer->distance,1)." ". ($this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM")) ?>
	                                </div>
	                            <?php } ?>
                                
                                <div class="col span_4_of_12 event-right">
                                    <div class="offer-company" itemprop="offeredBy" itemscope itemtype="http://schema.org/Organization">
                                        <a href="<?php echo $offer->link ?>"><span><i class="dir-icon-building dir-icon-large"></i><span itemprop="name"> <?php echo $this->escape($offer->company_name) ?></span></span></a>
                                    </div>

                                    <?php if((!empty($offer->startDate) && $offer->startDate!="0000-00-00") || (!empty($offer->endDate) && $offer->endDate!="0000-00-00")){?>
                                        <div class="offer-dates">
                                            <a href="<?php echo $offer->link ?>">
                                                <i class="dir-icon-calendar"></i>
                                                <?php
                                                    echo JBusinessUtil::getDateGeneralFormat($offer->startDate)." - ". JBusinessUtil::getDateGeneralFormat($offer->endDate);
                                                ?>
                                            </a>
                                        </div>
                                    <?php } ?>

                                    <?php $priceClass = !empty($offer->specialPrice)?"price-old":""  ?>
                                    
                                    <?php if(!empty($offer->price)){?>
                                        <div class="price-offers">
                                            <?php echo JText::_('LNG_OLD_PRICE') ?>:&nbsp;
                                            <div class="line-through">
                                                <?php echo JBusinessUtil::getPriceFormat($offer->price, $offer->currencyId) ?>
                                            </div>
                                            <?php if(!empty($offer->price_base)){?>

                                                    (<?php echo  JBusinessUtil::getPriceFormat($offer->price_base)?>/<?php echo $offer->price_base_unit?>)
                                            <?php }?>
                                        </div>
                                    <?php } ?>

                                    <?php if(!empty($offer->specialPrice)){?>
                                        <div class="specialprice-offers">
                                            <?php echo JText::_('LNG_NEW_PRICE') ?>:
                                            <?php echo JBusinessUtil::getPriceFormat($offer->specialPrice, $offer->currencyId)?>
                                            <?php if(!empty($offer->special_price_base)){?>
                                                <span>
                                                    (<?php echo  JBusinessUtil::getPriceFormat($offer->special_price_base)?>/<?php echo $offer->special_price_base_unit?>)
                                                </span>
                                            <?php }?>
                                        </div>
                                    <?php } ?>

                                    <?php if(!empty($offer->specialPrice) && !empty($offer->price) && $offer->specialPrice < $offer->price){ ?>
                                        <br />
                                        <span class="price"><?php echo JText::_('LNG_DISCOUNT') ?></span>
                                        <span class="price red"><?php echo JBusinessUtil::getPriceDiscount($offer->specialPrice, $offer->price) ?>%</span>
                                    <?php } ?>

                                    <?php if(empty($offer->price) && empty($offer->specialPrice) && ($this->appSettings->show_offer_free)){ ?>
                                        <span class="price red"><?php echo JText::_('LNG_FREE') ?></span>
                                    <?php }?>

                                    <?php if(!empty($offer->categories)){?>

                                        <div class="offer-categories">
                                            <?php foreach($offer->categories as $i=>$category) { ?>
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
                                                <?php }
                                            } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            <?php }
        }?>
    </ul>
</div>