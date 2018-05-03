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
			foreach ($this->offers as $offer){
    ?>
				<li itemscope itemprop="itemListElement" itemtype="http://schema.org/Offer">
					<div class="offer-box row-fluid <?php echo !empty($offer->featured)?"featured":"" ?>">
						<div class="offer-img-container span3">
							<a class="offer-image" href="<?php echo $offer->link ?>" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
								<?php if(isset($offer->picture_path) && $offer->picture_path!=''){?>
									<img  alt="<?php $this->escape($offer->subject) ?>" src="<?php echo JURI::root()."/".PICTURES_PATH.$offer->picture_path?>" itemprop="contentUrl">
								<?php }else{?>
									<img title="<?php echo $this->escape($offer->subject)?>" alt="<?php echo $this->escape($offer->subject)?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl">
								<?php } ?>
							</a>
						</div>
						<div class="offer-content span9">
							<div class="offer-price" style="width: 60%;">
                                <div style="float: right; width: 100%; text-align: right">
								<?php if(!empty($offer->price) ){ ?>
									<span class="<?php echo $offer->specialPrice>0 ?"old-price":"" ?>"><?php echo JBusinessUtil::getPriceFormat($offer->price, $offer->currencyId) ?></span>
								<?php } ?>
								<?php if(!empty($offer->specialPrice)){?>
									<span class="price red"><?php echo JBusinessUtil::getPriceFormat($offer->specialPrice, $offer->currencyId); ?></span>
								<?php }?>
								<?php if(!empty($offer->specialPrice) && !empty($offer->price) && $offer->specialPrice < $offer->price){ ?>
									<br />
									<span class="price"><?php echo JText::_('LNG_DISCOUNT') ?></span>
									<span class="price red"><?php echo JBusinessUtil::getPriceDiscount($offer->specialPrice, $offer->price) ?>%</span>
								<?php } ?>
                                </div>
                                <br/>
                                <div class="price-text-list">
                                    <?php if (!empty($offer->price_text)) { ?>
                                        <div class="clear"></div>
                                            <span  class="price red"><?php echo $offer->price_text ?></span>
                                    <?php }elseif (empty($offer->price) && empty($offer->specialPrice) && ($appSettings->show_offer_free)){ ?>
                                        <div class="clear"></div>
                                        <span class="price red"><?php echo JText::_('LNG_FREE') ?></span>
                                    <?php } ?>
                                </div>
                            </div>
						
							<div class="offer-subject" itemprop="url">
								<a title="<?php echo $this->escape($offer->subject)?>"
									href="<?php echo $this->escape($offer->link) ?>"><span itemprop="name"><?php echo $this->escape($offer->subject)?></span>
								</a>
							</div>
							<div class="offer-company" itemprop="offeredBy" itemscope itemtype="http://schema.org/Organization">
								<span><i class="dir-icon-building dir-icon-large"></i><span itemprop="name"> <?php echo $this->escape($offer->company_name) ?></span></span>
							</div>
											
							<?php $address =JBusinessUtil::getAddressText($offer); ?>
                            <?php if(!empty($address)){ ?>
								<div class="offer-location">
									<span><i class="dir-icon-map-marker dir-icon-large"></i> <?php echo $address ?></span>
								</div>
							<?php } ?>
							
							<?php if((!empty($offer->startDate) && $offer->startDate!="0000-00-00") || (!empty($offer->endDate) && $offer->endDate!="0000-00-00")){?>
								<div class="offer-dates">
									<i class="dir-icon-calendar"></i>
									<?php 
										echo JBusinessUtil::getDateGeneralFormat($offer->startDate)." - ". JBusinessUtil::getDateGeneralFormat($offer->endDate);
									?>
								</div>
							<?php } ?>
							<?php if(!empty($offer->show_time) && JBusinessUtil::getRemainingtime($offer->endDate)!=""){?>
								<div class="offer-dates">
									<span ><i class="dir-icon-clock-o dir-icon-large"></i> <?php echo JBusinessUtil::getRemainingtime($offer->endDate)?></span>
								</div>
							<?php } ?>
					
							<?php if(!empty($offer->categories)){?>
								<div class="offer-categories">
									<?php foreach($offer->categories as $i=>$category){ ?>
                                        <a href="<?php echo JBusinessUtil::getOfferCategoryLink($category[0], $category[2]) ?>"><?php echo $this->escape($category[1])?></a><?php echo $i<(count($offer->categories)-1)? ',&nbsp;':'' ?>
                                    <?php } ?>
								</div>
							<?php } ?>
							
							<?php if(!empty($offer->distance)){?>
                                <div>
                                    <?php echo JText::_("LNG_DISTANCE").": ".round($offer->distance,1)." ". ($this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM")) ?>
                                </div>
                            <?php } ?>
							
							
							<div class="offer-desciption" itemprop="description">
								<?php echo $offer->short_description ?>
							</div>
						</div>
						<?php if(isset($offer->featured) && $offer->featured==1){ ?>
							<div class="featured-text">
								<?php echo JText::_("LNG_FEATURED")?>
							</div>
						<?php } ?>
					</div>
					<div class="clear"></div>
				</li>
			<?php }
		}?>
	</ul>
</div>