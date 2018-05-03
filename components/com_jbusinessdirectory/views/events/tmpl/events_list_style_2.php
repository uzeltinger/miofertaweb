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

<div id="events-list-view-2">
    <ul class="event-list" itemscope itemtype="http://schema.org/ItemList">
        <?php
        if(isset($this->events) && count($this->events)>0){
            $itemCount = 1;
            foreach ($this->events as $event){ ?>
                <li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                    <span itemscope itemprop="item" itemtype="http://schema.org/Event">
                        <div id="event-style2" class="event-box section group <?php echo !empty($event->featured)?"featured":"" ?>">
                            <div class="event-img-container col span_3_of_12">
                                <a class="event-image" itemprop="image" itemscope itemtype="http://schema.org/ImageObject"
                                   href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>">

                                    <?php if(isset($event->picture_path) && $event->picture_path!=''){?>
                                        <div id="hover-effect" style="background: url('<?php echo JURI::root().PICTURES_PATH.$event->picture_path ?>')  no-repeat center center ;"></div>
                                        <span style="display:none;" itemprop="url"><?php echo JURI::root().PICTURES_PATH.$event->picture_path ?></span>
                                    <?php }else{?>
                                        <div style="background: url('<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>')  no-repeat center center ;"></div>
                                        <span style="display:none;" itemprop="url"><?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?></span>
                                    <?php } ?>
                                </a>
                            </div>
                            <div class="event-content col span_9_of_12">
                                <div class="section group">
                                    <div class="col span_8_of_12 event-left">
                                        <div class="event-subject">
                                            <a title="<?php echo $this->escape($event->name)?>"
                                               href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>"><span itemprop="name"><?php echo $this->escape($event->name)?></span>
                                            </a>
                                        </div>
                                        <span style="display:none;" itemprop="url"><?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?></span>

                                        <?php if ($event->start_date != '0000-00-00'){ ?>
                                            <div class="event-date">
                                                <i class="dir-icon-calendar"></i> <?php $dates = JBusinessUtil::getDateGeneralFormat($event->start_date).(!empty($event->start_date) && $event->start_date!=$event->end_date && $event->show_end_date?" - ".JBusinessUtil::getDateGeneralFormat($event->end_date):""); echo $dates; ?>
                                                <?php echo (empty($dates) || ($event->show_start_time==0  && $event->show_end_time==0))?"":"," ?>
                                                <?php echo ($event->show_start_time?JBusinessUtil::convertTimeToFormat($event->start_time):"")." ".(!empty($event->end_time)&&$event->show_end_time?JText::_("LNG_UNTIL"):"")." ".($event->show_end_time?JBusinessUtil::convertTimeToFormat($event->end_time):""); ?>
                                            </div>
                                        <?php } ?>

                                        <div class="event-desciption short-desc" itemprop="description">
                                            <?php echo JBusinessUtil::truncate($event->short_description, 200); ?>
                                        </div>
                                        <?php $address =JBusinessUtil::getAddressText($event); ?>
                                        <?php if(!empty($address)){ ?>
                                            <div class="event-location" itemprop="location" itemscope itemtype="http://schema.org/Place">
                                                <i class="dir-icon-map-marker dir-icon-large light-blue-marker"></i>&nbsp;<span itemprop="address"><span itemprop="name"><?php echo $address?></span></span>
                                            </div>
                                        <?php } ?>
                                        <?php if(!empty($event->latitude) && !empty($event->longitude)){?>
                                            <div class="event-location">
                                                <a target="_blank" href="<?php echo JBusinessUtil::getDirectionURL($this->location, $event) ?>"><?php echo JText::_("LNG_GET_MAP_DIRECTIONS")?></a>
                                            </div>
                                        <?php }?>

                                        <?php if(!empty($event->distance)){?>
                                             <div>
                                                <?php echo JText::_("LNG_DISTANCE").": ".round($event->distance,1)." ". ($this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM")) ?>
                                             </div>
                                        <?php } ?>
                                    </div>

                                    <div class="col span_4_of_12 event-right">
                                        <div class="event-company" itemprop="organizer" itemscope itemtype="http://schema.org/Organization">
                                            <a href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>"><span><i class="dir-icon-building dir-icon-large"></i><span itemprop="name"> <?php echo $this->escape($event->companyName) ?></span></span></a>
                                        </div>

                                        <?php if(!empty($event->start_date) && !empty($event->end_date)){?>
                                            <div class="event-date">
                                                <i class="dir-icon-calendar"></i>
                                                <?php $dates = JBusinessUtil::getDateGeneralFormat($event->start_date).(!empty($event->start_date) && $event->start_date!=$event->end_date && $event->show_end_date?" - ".JBusinessUtil::getDateGeneralFormat($event->end_date):""); echo $dates; ?>
                                            </div>
                                        <?php } ?>

                                        <?php if((!empty($event->start_time)) && ($event->show_start_time==1)){?>
                                            <div class="event-time">
                                                <i class="dir-icon-clock-o"></i>&nbsp;<?php echo $event->show_start_time?JBusinessUtil::convertTimeToFormat($event->start_time):""?>
                                            </div>
                                        <?php } ?>

                                        <?php if(!empty($event->eventType)){?>
                                            <div class="event-type">
                                                <?php echo $event->eventType?>
                                            </div>
                                        <?php } ?>

                                        <?php if(!empty($event->categories)){?>

                                            <div class="event-categories">
                                                <?php
                                                foreach($event->categories as $i=>$category){ ?>
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
                        <span style="display:none;" itemprop="startDate"><?php echo JBusinessUtil::getDateGeneralFormat($event->start_date) ?></span>
                        <span style="display:none;" itemprop="endDate"><?php echo JBusinessUtil::getDateGeneralFormat($event->end_date) ?></span>
                        <span style="display:none;" itemprop="organizer" itemscope itemtype="http://schema.org/Organization"><span itemprop="name"><?php echo $event->companyName ?></span></span>
                        <div class="clear"></div>
                    </span>
                    <span style="display:none;" itemprop="position"><?php echo $itemCount ?></span>
                </li>
            <?php
                $itemCount++;
            }
        } ?>
    </ul>
    <div class="clear"></div>
</div>