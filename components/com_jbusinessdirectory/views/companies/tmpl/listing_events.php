<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

//$calendarSource = html_entity_decode(JRoute::_('index.php?option=com_jbusinessdirectory&task=events.getCalendarEvents&companyId='.$this->company->id));
//require_once JPATH_COMPONENT_SITE.'/libraries/calendar/calendar.php';
?>

<div class='events-container full' style="">
	<ul class="event-list">
	<?php
		if(!empty($this->events)){
			foreach ($this->events as $event){ ?>
				<li>
					<div class="event-box row-fluid">
						<div class="event-img-container span3">
							<a class="event-image"href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>">
								<?php if(!empty($event->picture_path)){?>
									<img title="<?php echo $this->escape($event->name) ?>" alt="<?php echo $this->escape($event->name) ?>" src="<?php echo JURI::root()."/".PICTURES_PATH.$event->picture_path?>">
								<?php }else{ ?>
									<img title="<?php echo $this->escape($event->name) ?>" alt="<?php echo $this->escape($event->name) ?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>">
								<?php } ?>
							</a>
						</div>
						<div class="event-content span7">
							<div class="event-subject">
								<a
									title="<?php echo $this->escape($event->name) ?>"
									href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>"><?php echo $this->escape($event->name) ?>
                                </a>
							</div>
							<?php $address = JBusinessUtil::getAddressText($event); ?>
                      	    <?php if(!empty($address)) { ?>
    							<div class="event-location">
    								<i class="dir-icon-map-marker dir-icon-large"></i>&nbsp;<span itemprop="name"><?php echo $this->escape($address) ?></span>
    							</div>
							<?php } ?>
							 
							<div class="event-location">
								<i class="dir-icon-calendar"></i>  <?php echo JBusinessUtil::getDateGeneralFormat($event->start_date)." ".JText::_("LNG_UNTIL")." ".JBusinessUtil::getDateGeneralFormat($event->end_date) ?>
							</div>
							<div class="event-type">
								<?php echo JText::_("LNG_TYPE")?>:  <?php echo $this->escape($event->eventType) ?>
							</div>
							<div class="event-desciption">
								<?php echo $this->escape($event->short_description) ?>
							</div>
						</div>
					</div>
				</li>
			<?php }
		}else{
			echo JText::_("LNG_NO_EVENT_FOUND");
		} ?>
	</ul>
</div>
		
<div class="clear"></div>	