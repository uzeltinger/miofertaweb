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

<div id="busienss_hours">
	<fieldset class="fieldset-business_hours">
		<div>
			<div class="small right"><?php echo JText::_('LNG_GMT')." ".$this->company->time_zone  ;  ?></div>
			<h3><i class="dir-icon-clock-o"></i> <?php echo JText::_('LNG_OPENING_HOURS')?></h3>
			<?php $dayNames = array(JText::_("MONDAY"),JText::_("TUESDAY"),JText::_("WEDNESDAY"),JText::_("THURSDAY"),JText::_("FRIDAY"),JText::_("SATURDAY"),JText::_("SUNDAY")) ?>
			<?php 
			foreach($this->company->business_hours as $index=>$day) { ?>
				<div class="business-hour row-fluid" itemprop="openingHours">
					<div class="day span5"><?php echo $day->name ?></div>
					<div class="business-hour-time span7">
                        <?php if($day->workHours['status']) { ?>
                        	<div class="business-hours-wrap">
                                <span class="start">
                                    <?php echo $day->workHours['start_time'] ?>
                                </span>
                                <?php if(isset($day->breakHours)) { ?>
                                        <span class="end">
                                            - <?php echo JBusinessUtil::convertTimeToFormat($day->breakHours["start_time"][0]) ?>
                                        </span>
                                    </div>
                                    <div class="business-hours-wrap">
                                        <span class="start">
                                            <?php echo JBusinessUtil::convertTimeToFormat($day->breakHours["end_time"][0]) ?>
                                        </span>
                                <?php } ?>
                                <span class="end">
                                     - <?php echo $day->workHours['end_time'] ?>
                                </span>
                            </div>
                        <?php } else {?>
                            <span class="end"><?php echo JText::_('LNG_CLOSED'); ?></span>
                        <?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>

		<?php if(!empty($this->company->notes_hours)){ ?>
			<div style="display: block" class="business-notes"><?php echo $this->escape($this->company->notes_hours) ?></div>
		<?php } ?>
	</fieldset>
</div>
