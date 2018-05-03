<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
$user = JFactory::getUser();

$showData = !($user->id==0 && $appSettings->show_details_user == 1);

?>

<!-- layout -->
<div id="layout" class="pagewidth clearfix grid4 grid-view-1" <?php echo !$this->appSettings->offers_view_mode?'style="display: none"':'' ?>>

<div id="grid-content" class="grid-content row-fluid grid4" itemscope itemtype="http://schema.org/OfferCatalog">

	<?php 
	if(!empty($this->offers)){
    $index = 0;
    foreach ($this->offers as $index => $offer){
        $index++;
    ?>
    <article id="post-<?php echo $offer->id ?>" class="post clearfix span3" itemscope itemprop="itemListElement"
             itemtype="http://schema.org/Offer">
        <div class="post-inner">
            <figure class="post-image " itemprop="url">
                <a href="<?php echo $offer->link ?>" itemprop="image" itemscope
                   itemtype="http://schema.org/ImageObject">
                    <?php if (!empty($offer->picture_path)) { ?>
                        <img title="<?php echo $this->escape($offer->subject) ?>"
                             alt="<?php echo $this->escape($offer->subject) ?>"
                             src="<?php echo JURI::root() . PICTURES_PATH . $offer->picture_path ?>"
                             itemprop="contentUrl">
                    <?php } else { ?>
                        <img title="<?php echo $this->escape($offer->subject) ?>"
                             alt="<?php echo $this->escape($offer->subject) ?>"
                             src="<?php echo JURI::root() . PICTURES_PATH . '/no_image.jpg' ?>" itemprop="contentUrl">
                    <?php } ?>
                </a>
            </figure>

            <div class="post-content">
                <h2 class="post-title"><a href="<?php echo $offer->link ?>"><span
                                itemprop="name"><?php echo $this->escape($offer->subject) ?></span></a></h2>
                <?php if (!empty($offer->company_name)) { ?>
                    <div class="offer-company" itemprop="offeredBy" itemscope itemtype="http://schema.org/Organization">
                        <span><i class="dir-icon-building"></i><span
                                    itemprop="name"><?php echo $this->escape($offer->company_name) ?></span></span>
                    </div>
                <?php } ?>
                <?php $address = JBusinessUtil::getAddressText($offer); ?>
                <?php if (!empty($address)) { ?>
                    <div class="post-date"><span><i
                                    class="dir-icon-map-marker dir-icon-large"></i> <?php echo JBusinessUtil::getAddressText($offer) ?></span>
                    </div>
                <?php } ?>
                <?php if (!JBusinessUtil::emptyDate($offer->startDate) || !JBusinessUtil::emptyDate($offer->endDate)) { ?>
                    <div class="offer-dates">
                        <i class="dir-icon-calendar"></i>
                        <?php
                        echo JBusinessUtil::getDateGeneralShortFormat($offer->startDate) . " - " . JBusinessUtil::getDateGeneralShortFormat($offer->endDate);
                        ?>
                    </div>
                <?php } ?>

                <?php if (!empty($offer->show_time) && JBusinessUtil::getRemainingtime($offer->endDate) != "") { ?>
                    <div class="offer-dates">
                        <span><i class="dir-icon-clock-o"></i> <?php echo JBusinessUtil::getRemainingtime($offer->endDate) ?></span>
                    </div>
                <?php } ?>

                <?php if (!empty($offer->categories) && false) { ?>
                    <p class="company-clasificaiton">
							<span class="post-category">
								<?php foreach ($offer->categories as $i => $category) { ?>
                                    <a
                                    href="<?php echo JBusinessUtil::getOfferCategoryLink($category[0], $category[2]) ?>"><?php echo $this->escape($category[1]) ?></a><?php echo $i < (count($categories) - 1) ? ',&nbsp;' : '' ?>
                                <?php } ?>
							</span> <br/>
                    </p>
                <?php } ?>
                <div class="offer-price">
                    <?php if (!empty($offer->price)) { ?>
                        <span class="<?php echo $offer->specialPrice>0 ?"old-price":"" ?>"><?php echo JBusinessUtil::getPriceFormat($offer->price, $offer->currencyId) ?></span>
                    <?php } ?>
                    <?php if (!empty($offer->specialPrice)) { ?>
                        <span class="price red"><?php echo JBusinessUtil::getPriceFormat($offer->specialPrice, $offer->currencyId); ?></span>
                    <?php } ?>
                    <?php if (!empty($offer->specialPrice) && !empty($offer->price) && $offer->specialPrice < $offer->price) { ?>
                        <br/>
                        <span class="price"><?php echo JText::_('LNG_DISCOUNT') ?></span>
                        <span class="price red"><?php echo JBusinessUtil::getPriceDiscount($offer->specialPrice, $offer->price) ?>
                            %</span>
                    <?php } ?>
                    <?php if (!empty($offer->price_text)) { ?>
                        <br/>
                        <span  class="price red"><?php echo $offer->price_text ?></span>
                    <?php }elseif (empty($offer->price) && empty($offer->specialPrice) && ($appSettings->show_offer_free)){ ?>
                        <span class="price red"><?php echo JText::_('LNG_FREE') ?></span>
                    <?php } ?>
                </div>
            </div>
            <!-- /.post-content -->
        </div>
        <!-- /.post-inner -->
    </article>
    <?php if ($index % 4 == 0){ ?>
</div>
    <div id="grid-content" class="grid-content row-fluid grid4" itemscope itemtype="http://schema.org/OfferCatalog">
        <?php
        }
        }
        }
?>
<div class="clear"></div>
</div>
</div>	
