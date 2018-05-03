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

<div id="events-list-view"> 
	<ul class="event-list"  >
	<?php 
		if(isset($this->events) && count($this->events)>0){
			foreach ($this->events as $event){ ?>
				<li  >
					<div class="event-box row-fluid <?php echo !empty($event->featured)?"featured":"" ?>">
						<div class="event-img-container span3">
							<a class="event-image"
								href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>">
								
								<?php if(isset($event->picture_path) && $event->picture_path!=''){?>
									<img  alt="<?php echo $this->escape($event->name) ?>" src="<?php echo JURI::root()."/".PICTURES_PATH.$event->picture_path?>" >
								<?php }else{?>
									<img title="<?php echo $this->escape($event->name)?>" alt="<?php echo $this->escape($event->name)?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" >
								<?php } ?>
							</a>
						</div>
						<div class="event-content span8">
							<div class="event-subject" >
								<a title="<?php echo $this->escape($event->name)?>"
									href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>"><span ><?php echo $this->escape($event->name)?></span>
								</a>
							</div>

							<?php $address =JBusinessUtil::getAddressText($event); ?>
                            <?php if(!empty($address)){ ?>
								<div class="event-location" >
									<i class="dir-icon-map-marker dir-icon-large"></i><span ><span > <?php echo $address ?></span></span>
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
							<?php if ($event->start_date != '0000-00-00'){ ?>
								<div class="event-date">
									<i class="dir-icon-calendar"></i>
									<?php $dates = JBusinessUtil::getDateGeneralFormat($event->start_date).(!empty($event->start_date) && $event->start_date!=$event->end_date && $event->show_end_date?" - ".JBusinessUtil::getDateGeneralFormat($event->end_date):""); echo $dates; ?>
									<?php echo (empty($dates) || ($event->show_start_time==0  && $event->show_end_time==0))?"":"," ?>
									<?php echo ($event->show_start_time?JBusinessUtil::convertTimeToFormat($event->start_time):"")." ".(!empty($event->end_time)&&$event->show_end_time?JText::_("LNG_UNTIL"):"")." ".($event->show_end_time?JBusinessUtil::convertTimeToFormat($event->end_time):""); ?>
								</div>
							<?php } ?>

							<?php if(!empty($event->eventType)){?>
								<div class="event-type">
									<?php echo JText::_("LNG_TYPE")?>: <strong><?php echo $this->escape($event->eventType)?></strong>
								</div>
							<?php } ?>

							<?php if(!empty($event->categories)){?>
								<div class="event-categories">
									<div style="float:left"><?php echo JText::_('LNG_CATEGORIES')?>:&nbsp;</div>
									<ul class="event-categories">
										<?php
										foreach($event->categories as $i=>$category){ ?>
											<li> <a href="<?php echo JBusinessUtil::getEventCategoryLink($category[0], $category[2]) ?>"><?php echo $this->escape($category[1])?></a><?php echo $i<(count($event->categories)-1)? ',&nbsp;':'' ?></li>
											<?php
										}
										?>
									</ul>
								</div>
							<?php } ?>

							<div class="event-desciption" >
								<?php echo $event->short_description ?>
							</div>
						</div>
						<?php if(isset($event->featured) && $event->featured==1){ ?>
							<div class="featured-text">
								<?php echo JText::_("LNG_FEATURED")?>
							</div>
						<?php } ?>
					</div>
					<div class="clear"></div>
				</li>
			<?php }
		} ?>
	</ul>
	<div class="clear"></div>
</div>