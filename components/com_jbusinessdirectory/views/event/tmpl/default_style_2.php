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
require_once JPATH_COMPONENT_SITE.'/classes/attributes/attributeservice.php';
?>

<div id="offer-detail-2">
    <div class="section group">
        <div class="col span_4_of_12 left-side offer-simple">
            <div>
            </div>
            <div>
                <?php if(!empty($this->event->pictures)){
                    ?>
                    <div id="hover-effect" style="background: url('<?php echo JURI::root().PICTURES_PATH.$this->event->pictures[0]->picture_path ?>') no-repeat center center ;"></div>
                <?php }else{?>
                    <div style="background: url('<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>') no-repeat center center ;"></div>
                <?php } ?>
            </div>
            <div class="row-fluid start-date">
                <div class="date-event">
                    <strong>
                        <h4><i class="dir-icon-calendar">&nbsp;<?php echo JText::_('LNG_START')?>: </i></h4>
                        <h4>
                            <?php if((!empty($this->event->start_date) && $this->event->start_date!="0000-00-00")){
                                echo  JBusinessUtil::getDateGeneralFormat($this->event->start_date);
                            } ?>
                        </h4>
                    </strong>
                </div>
                <div class="date-event">
                    <strong>
                        <h4>
                            <?php if((!empty($this->event->start_time))){
                                echo  JBusinessUtil::getTimeText($this->event->start_time);
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
                            <?php if((!empty($this->event->end_date) && $this->event->end_date!="0000-00-00")){
                                echo  JBusinessUtil::getDateGeneralFormat($this->event->end_date);
                            } ?>
                        </h4>
                    </strong>
                </div>
                <div class="date-event">
                    <strong>
                        <h4>
                            <?php if((!empty($this->event->end_time))){
                                echo  JBusinessUtil::getTimeText($this->event->end_time);
                            } ?>
                        </h4>
                    </strong>
                </div>
            </div>
            <?php if(!empty($this->event->doors_open_time) && $this->event->show_doors_open_time) { ?>
                <div class="open-hours">
                    <h5>
                        <?php echo JText::_('LNG_EVENT_DOORS_OPEN') ?>:
                        <strong><i class="dir-icon-clock-o"></i>&nbsp;<span itemprop="doorTime"><?php echo JBusinessUtil::convertTimeToFormat($this->event->doors_open_time) ?></span></strong>
                    </h5>
                </div>
            <?php } ?>
            <div class="event-locat">
                <h5><?php echo JText::_('LNG_EVENT_VENUE')?></h5>
                <p><?php echo JBusinessUtil::getAddressText($this->event)?></p>
                <p class="gps-event">
                    GPS: <span><?php echo $this->event->latitude?><br>
                        <?php echo $this->event->longitude?></span>
                </p>
                <?php if((!empty($this->event->contact_email) || !empty($this->event->company->email)) && $this->appSettings->show_contact_form) { ?>
                    <div class="event-contact" style="text-align: center;">
                        <a href="javascript:contactCompany(<?php echo $showData?1:0 ?>)" ><i class="dir-icon-envelope"></i> <?php echo JText::_('LNG_CONTACT'); ?></a></div>
                <?php } ?>
            </div>

            <?php if($this->appSettings->enable_attachments) { ?>
                <?php if(!empty($this->event->attachments)) { ?>
                    <div><strong><?php echo JText::_("LNG_ATTACHMENTS")?></strong></div>
                    <?php require "event_attachments.php"?>
                    <div class="clear"></div><br/>
                <?php } ?>
            <?php } ?>

        </div>
        <div class="col span_8_of_12 right-side">
            <div class="offer-name-simple">
                <h1 itemprop="name">
                    <?php echo $this->escape($this->event->name)?>
                </h1>
                <?php require_once JPATH_COMPONENT_SITE."/include/social_share.php"; ?>
            </div>
            <div class="offer-description" itemprop="description">
                <?php echo $this->event->description?>
                <?php if($this->appSettings->enable_event_subscription && $this->event->enable_subscription) { ?>
                    <div class="span6">
                        <button class="ui-dir-button ui-dir-button-green right" onclick="joinEvent(<?php echo ($user->id == 0)?0:1 ?>)">
                            <?php echo JText::_('LNG_JOIN'); ?>
                        </button>
                    </div>
                <?php } ?>
            </div>
            <?php if(!empty($this->event->price) && intval($this->event->price)!=0){?>
                <div class="event-price">
                    <?php echo JText::_("LNG_PRICE")?>: <strong><?php echo JBusinessUtil::getPriceFormat($this->event->price, $this->event->currency_id) ?></strong>
                </div>
            <?php } ?>
            <div class="event-type">
                <?php echo JText::_("LNG_TYPE")?>: <strong><?php echo $this->event->eventType?></strong>
            </div>

            <div class="price-offer">
                <?php if($this->appSettings->enable_event_reservation)
                    require_once 'event_tickets.php';
                ?>
            </div>
            
            <div class="price-offer">
		        <?php
		        $renderedContent = AttributeService::renderAttributesFront($this->eventAttributes,false, array());
		        echo $renderedContent;
		        ?>
		    </div>
            
            
            <div id="event-map">
                <?php require_once 'map.php';?>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="organizer section group business-offer">
        <div class="col span_4_of_12">
            <a href="<?php echo JBusinessUtil::getCompanyLink($this->event->company)?>">
                <?php if(isset($this->event->company->logoLocation) && $this->event->company->logoLocation!=''){?>
                    <div class="hover-offer" style="background: url('<?php echo JURI::root().PICTURES_PATH.$this->event->company->logoLocation ?>')  no-repeat center center ;">
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
                    <h4><a href="<?php echo JBusinessUtil::getCompanyLink($this->event->company)?>"> <span itemprop="name"><?php echo $this->event->company->name?></span></a></h4>
                    <h5><span class="business-slogan"><?php echo JBusinessUtil::truncate($this->event->company->slogan, 50); ?> </span></h5>
                </div>
                <div class="col span_6_of_12">
                    <div class="column-social">

                        <?php if(!empty($this->event->company->facebook)) { ?>
                            <a href="<?php echo $this->escape($this->event->company->facebook) ?>">
                                <div class="social-event face-event">
                                    <i class="dir-icon-facebook"></i>
                                </div>
                            </a>
                        <?php } ?>
                        <?php if(!empty($this->event->company->twitter)) { ?>
                            <a href="<?php echo $this->escape($this->event->company->twitter) ?>">
                                <div class="social-event"><i class="dir-icon-twitter"></i></div>
                            </a>
                        <?php } ?>
                        <?php if(!empty($this->event->company->googlep)) { ?>
                            <a href="<?php echo $this->escape($this->event->company->googlep) ?>">
                                <div class="social-event"><i class="dir-icon-google-plus"></i></div>
                            </a>
                        <?php } ?>
                        <?php if(!empty($this->event->company->linkedin)) { ?>
                            <a href="<?php echo $this->escape($this->event->company->linkedin) ?>">
                                <div class="social-event"><i class="dir-icon-linkedin"></i></div>
                            </a>
                        <?php } ?>
                        <?php if(!empty($this->event->company->skype)) { ?>
                            <a href="<?php echo $this->escape($this->event->company->skype) ?>">
                                <div class="social-event"><i class="dir-icon-skype"></i></div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="desc-event">
                <?php if(isset($this->event->company->short_description)) { ?>
                    <div>
                        <?php echo JBusinessUtil::truncate($this->event->company->short_description, 700);?>
                    </div>
                <?php } ?>
            </div>
            <div class="contact-event">
                <?php if(!empty($this->event->company->phone)) { ?>
                    <div class="row-fluid">
                        <div class="span4"><?php echo JText::_("LNG_TELEPHONE")?>: </div>
                        <div class="span8"><a href="tel:<?php  echo $this->escape($this->event->company->phone); ?>"><?php echo $this->escape($this->event->company->phone); ?></a></div>
                    </div>
                <?php } ?>
                <?php if(!empty($this->event->company->email)) { ?>
                    <div class="row-fluid">
                        <div class="span4"><?php echo JText::_("LNG_EMAIL")?>: </div>
                        <div class="span8"><a href=javascript:void(0)" ><?php echo $this->escape($this->event->company->email); ?></a></div>
                    </div>
                <?php } ?>
                <?php if(!empty($this->event->company->website)) {?>
                    <div class="row-fluid">
                        <div class="span4"><?php echo JText::_("LNG_WEB")?>: </div>
                        <div class="span8"><a target="_blank" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=companies&task=companies.showCompanyWebsite&companyId='.$this->event->company->id) ?>"><?php echo $this->escape($this->event->company->website); ?></a></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div><br/>
    <div id="tabs-11" class="dir-tab ui-tabs-panel" >
        <h3><?php echo JText::_("LNG_ASSOCIATED_COMPANIES")?></h3>
        <?php require_once 'event_associated_companies.php';?>
    </div>
    <div id="tabs-11" class="dir-tab ui-tabs-panel" >
        <h3><?php echo JText::_("LNG_EVENT_VIDEOS")?></h3>
        <?php require_once 'event_videos.php';?>
    </div><br />
</div>

<?php require_once 'event_util.php'; ?>