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

<div class='grid-content events-container grid4 row-fluid'>
    <?php
    if(isset($this->associatedEvents) && count($this->associatedEvents)){
        $index = 0;
        foreach ($this->associatedEvents as $event){
            $index++;
        	$dateError = (strtotime($event->end_date) < strtotime($event->start_date))?true:false;
            ?>
            <article id="post-<?php echo  $event->id ?>" class="post clearfix span4">
                <div class="post-inner">
                    <figure class="post-image ">
                        <a href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>" target="_blank">
                            <?php if(!empty($event->pictures[0]->picture_path) ){?>
                                <img title="<?php echo $this->escape($event->name) ?>" alt="<?php echo $this->escape($event->name) ?>" src="<?php echo JURI::root().PICTURES_PATH.$event->pictures[0]->picture_path ?>" >
                            <?php }else{ ?>
                                <img title="<?php echo $this->escape($event->name) ?>" alt="<?php echo $this->escape($event->name) ?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" >
                            <?php } ?>
                        </a>
                    </figure>
					<div class="entry-date">
						<div class="day"><?php echo JBusinessUtil::getDayOfMonth($event->start_date) ?></div>
						<span class="month"><?php echo JBusinessUtil::getMonth($event->start_date) ?></span>
						<span class="year"><?php echo JBusinessUtil::getYear($event->start_date) ?></span>
					</div>
                    <div class="post-content">
                        <h2 class="post-title"><a href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>"><span ><?php echo $this->escape($event->name) ?></span></a></h2>
                        <span class="post-date"><i class="fa dir-icon-map-marker"></i> <?php echo $this->escape($event->address) ?>, <?php echo $this->escape($event->city) ?>, <?php echo $this->escape($event->county) ?></span>
                    </div>
                    <!-- /.post-content -->
					<?php if($this->appSettings->enable_event_appointments && !$dateError && (!JBusinessUtil::emptyDate($event->start_date) || !JBusinessUtil::emptyDate($event->end_date))) { ?>
						<a href="javascript:void(0)" class="btn btn-xs btn-success btn-panel" onclick="makeAppointment(<?php echo $event->id; ?>, '<?php echo $event->start_date; ?>', '<?php echo $event->end_date; ?>')">
							<?php echo JText::_('LNG_BOOK_APPOINTMENT'); ?>
						</a>
				    <?php } ?>
                </div>
                <!-- /.post-inner -->
            </article>
            <?php if($index%3==0){?>
        		</div>
        		<div class="grid-content events-container row-fluid grid4">
      		<?php } ?>
  	  <?php } ?>
    
	<?php 
    }else{
        echo JText::_("LNG_NO_ASSOCIATED_EVENTS");
    }
    ?>

</div>
<div class="clear"></div>


<?php if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$this->appSettings->enable_packages)
&& isset($this->associatedEvents) && count($this->associatedEvents) && $this->appSettings->enable_events){ ?>
	<div id="event-appointment" style="display:none">
		<div id="dialog-container">
			<div class="titleBar">
				<span class="dialogTitle" id="dialogTitle"></span>
				<span title="Cancel" class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
			</div>
			<div class="dialogContent">
				<h3 class="title"><?php echo JText::_('LNG_BOOK_APPOINTMENT') ?></h3>
				<div class="dialogContentBody" id="dialogContentBody">
					<form id="leaveAppointmentFrm" name ="leaveAppointmentFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" method="post">
						<p>
							<?php echo JText::_('LNG_BOOK_APPOINTMENT_TEXT') ?>
						</p>
						<div class="review-repsonse">
							<fieldset>

								<div class="form-item">
									<label><?php echo JText::_('LNG_FIRST_NAME') ?></label>
									<div class="outer_input">
										<input type="text" name="first_name" id="first_name-appoint" class="input_txt  validate[required]" ><br>
									</div>
								</div>

								<div class="form-item">
									<label><?php echo JText::_('LNG_LAST_NAME') ?></label>
									<div class="outer_input">
										<input type="text" name="last_name" id="last_name-appoint" class="input_txt  validate[required]" ><br>
									</div>
								</div>


								<div class="form-item">
									<label><?php echo JText::_('LNG_COMPANY_NAME') ?></label>
									<div class="outer_input">
										<input type="text" name="company_name" id="company_name-appoint" class="input_txt" ><br>
									</div>
								</div>

								<div class="form-item">
									<label><?php echo JText::_('LNG_PHONE') ?></label>
									<div class="outer_input">
										<input type="text" name="phone" id="phone-appoint" class="input_txt  validate[required]"><br>
										<span class="error_msg" id="frmPhone_error_msg" style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD')?></span>
									</div>
								</div>

								<div class="form-item">
									<label><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
									<div class="outer_input">
										<input type="text" name="email" id="email-appoint" class="input_txt  validate[required,custom[email]]"><br>
									</div>
								</div>

								<div class="form-item">
									<label for="time"><?php echo JText::_('LNG_PREFERRED_TIME') ?></label>
										<input type="text" name="time" id="time-appoint" class="input_txt timepicker ui-timepicker-input" value="" />

								</div>

								<div class="form-item">
									<label for="date"><?php echo JText::_('LNG_DATE') ?></label>
									<div class="outer_input">
										<select name="date" id="date-appoint" class="validate[required]">

										</select>
									</div>
								</div>
								
								<div class="form-item">
									<label for="date"><?php echo JText::_('LNG_REMARKS') ?></label>
									<div class="outer_input">
										<textarea name="remarks" id="remarks" ></textarea>
									</div>
								</div>

								<?php if($this->appSettings->captcha){?>
									<div class="form-item">
										<?php
										$namespace="jbusinessdirectory.contact";
										$class=" required";

										$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

										if(!empty($captcha)){
											echo $captcha->display("captcha", "captcha-div-appointment", $class);
										}
										?>
									</div>
								<?php } ?>

								<div class="clearfix clear-left">
									<div class="button-row ">
										<button type="submit" class="ui-dir-button" onclick="saveForm('leaveAppointmentFrm' , document.getElementById('leave_appointment_task').value )">
											<span class="ui-button-text"><?php echo JText::_("LNG_BOOK_APPOINTMENT")?></span>
										</button>
										<button type="button" class="ui-dir-button ui-dir-button-grey" onclick="jQuery.unblockUI()">
											<span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
										</button>
									</div>
								</div>
							</fieldset>
						</div>

						<input type='hidden' name='task' id="leave_appointment_task" value='companies.leaveAppointment'/>
						<input type='hidden' name='userId' value='<?php echo $user->id?>'/>
						<input type='hidden' name='controller' value='companies' />
						<input type='hidden' name='view' value='companies' />
						<input type="hidden" name="company_id" value="<?php echo $this->company->id?>" />
						<input type="hidden" name="event_id" id="eventId-appoint" value="" />
					</form>
				</div>
			</div>
		</div>
	</div>
<?php } ?>