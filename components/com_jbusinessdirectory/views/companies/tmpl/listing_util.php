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

<style>
	.ui-timepicker-wrapper {
		z-index: 99999!important;
	}
</style>
<?php JBusinessUtil::includeValidation(); ?>
<?php if((!isset($this->company->userId) || $this->company->userId == 0) && $this->appSettings->claim_business){ ?>
<div id="company-claim" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		<div class="dialogContent">
			<h3 class="title"><?php echo JText::_('LNG_CLAIM_COMPANY') ?></h3>
		  		<div class="dialogContentBody" id="dialogContentBody">
					<form id="claimCompanyFrm" name ="claimCompanyFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" method="post">
						<p>
							<?php echo JText::_('LNG_COMPANY_CLAIM_TEXT') ?>
						</p>
						<div class="review-repsonse">
						<fieldset>
		
							<div class="form-item">
								<label><?php echo JText::_('LNG_FIRST_NAME') ?></label>
								<div class="outer_input">
									<input type="text" name="firstName" id="firstName-claim" class="input_txt validate[required]" value="<?php echo $user->id>0?$user->name:""?>">
								</div>
							</div>
		
							<div class="form-item">
								<label><?php echo JText::_('LNG_LAST_NAME') ?></label>
								<div class="outer_input">
									<input type="text" name="lastName" id="lastName-claim" class="input_txt  validate[required]" >
								</div>
							</div>
		
		
							<div class="form-item">
								<label><?php echo JText::_('LNG_FUNCTION') ?></label>
								<div class="outer_input">
									<input type="text" name="function" id="function-claim" class="input_txt  validate[required]" >
								</div>
							</div>
		
							<div class="form-item">
								<label><?php echo JText::_('LNG_PHONE') ?></label>
								<div class="outer_input">
									<input type="text" name="phone" id="phone-claim" class="input_txt  validate[required]">
									<span class="error_msg" id="frmPhone_error_msg" style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD')?></span>
								</div>
							</div>
		
							<div class="form-item">
								<label><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
								<div class="outer_input">
									<input type="text" name="email" id="email-claim" class="input_txt  validate[required,custom[email]]" <?php echo $user->id>0?$user->email:""?>>
								</div>
							</div>

							<div class="form-item">
								<input type="checkbox"  name="claim-company-agreament" id="claim-company-agreament" value="1" class="validate[required]"> <?php echo JText::_('LNG_COMPANY_CLAIM_DECLARATION')?>
							</div>

							<div class="form-item">
								<input type="checkbox"  name="claim-terms-conditions" id="claim-terms-conditions" value="1" class="validate[required]"> <a href="javascript:void(0)" id="agreementLink"><?php echo JText::_('LNG_TERMS_AGREAMENT')?></a>
							</div>

							<div id="termAgreement" style="display: none;">
								<?php echo $this->appSettings->terms_conditions ?>
							</div>
							
							<?php if($this->appSettings->captcha){?>
								<div class="form-item">
									<?php 
										$namespace="jbusinessdirectory.contact";
										$class=" required";
										
										$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
																			
										if(!empty($captcha)){	
											echo $captcha->display("captcha", "captcha-div-claim", $class);
										}
									?>
								</div>
							<?php } ?>
		
							<div class="clearfix clear-left">
								<div class="button-row ">
									<button type="submit" class="ui-dir-button" onclick="saveForm('claimCompanyFrm')">
											<span class="ui-button-text"><?php echo JText::_("LNG_CLAIM_COMPANY")?></span>
									</button>
									<button type="button" class="ui-dir-button ui-dir-button-grey" onclick="jQuery.unblockUI()">
											<span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
									</button>
								</div>
							</div>
						</fieldset>
						</div>
						
						<input type='hidden' name='task' id="task" value='companies.claimCompany'/>
						<input type='hidden' name='userId' value='<?php echo $user->id?>'/>
						<input type='hidden' name='controller' value='companies' />
						<input type='hidden' name='view' value='companies' />
						<input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
					</form>
				</div>
		</div>
	</div>
</div>
<?php } ?>

<?php if((isset($this->package->features) && in_array(CONTACT_FORM,$this->package->features) || $showData && !$appSettings->enable_packages) && !empty($company->email)){ ?>
						
<div id="company-contact" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			<h3 class="title"><?php echo JText::_('LNG_CONTACT_COMPANY') ?></h3>
		  		<div class="dialogContentBody" id="dialogContentBody">
					<form id="contactCompanyFrm" name="contactCompanyFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
						<p>
							<?php echo JText::_('LNG_COMPANY_CONTACT_TEXT') ?>
						</p>
						<div class="review-repsonse">
							<fieldset>

								<?php if(!empty($this->companyContactsEmail)){?>
									<div class="form-item">
										<label><?php echo JText::_('LNG_COMPANY_CONTACT') ?></label>
										<div class="outer_input">
											<select name="contact_id" id="contact_id" class="inputbox">
												<option value=""><?php echo JText::_('LNG_JOPTION_SELECT_CONTACT');?></option>
												<?php echo JHtml::_('select.options', $this->companyContactsEmail, 'id', 'contact_name');?>
											</select>
										</div>
									</div>
								<?php } ?>

								<div class="form-item">
									<label><?php echo JText::_('LNG_FIRST_NAME') ?></label>
									<div class="outer_input">
										<input type="text" name="firstName" id="firstName" class="input_txt  validate[required]" value="<?php echo $user->id>0?$user->name:""?>">
									</div>
								</div>
			
								<div class="form-item">
									<label><?php echo JText::_('LNG_LAST_NAME') ?></label>
									<div class="outer_input">
										<input type="text" name="lastName" id="lastName" class="input_txt  validate[required]" >
									</div>
								</div>
			
								<div class="form-item">
									<label><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
									<div class="outer_input">
										<input type="text" name="email" id="email" class="input_txt  validate[required,custom[email]]" <?php echo $user->id>0?$user->email:""?>>
									</div>
								</div>
	
								<div class="form-item">
									<label><?php echo JText::_('LNG_CONTACT_TEXT')?>:</label>
									<div class="outer_input">
										<textarea rows="5" name="description" id="description" class="input_txt  validate[required]"></textarea>
									</div>
								</div>
								
								<div class="form-item">
									<input type="checkbox"  name="copy-me" id="copy-me" value="1"> <?php echo JText::_('LNG_COPY_ME')?>
								</div>
						
								<div class="form-item">
									<input type="checkbox"  name="terms-conditions" id="terms-conditions" value="1" class="validate[required]"> <a href="javascript:void(0)" id="terms-conditions-link"><?php echo JText::_('LNG_TERMS_AGREAMENT')?></a>
								</div>

								<div id="term_conditions_text" style="display: none;">
									<?php echo $this->appSettings->contact_terms_conditions ?>
								</div>

								<?php if($this->appSettings->captcha){?>
									<div class="form-item">
										<?php 
											$namespace="jbusinessdirectory.contact";
											$class=" required";
											
											$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
																				
											if(!empty($captcha)){	
												echo $captcha->display("captcha", "captcha-div-contact", $class);
											}
										?>
									</div>
								<?php } ?>

								<div class="clearfix clear-left">
									<div class="button-row ">
										<button type="submit" class="ui-dir-button" onclick="saveForm('contactCompanyFrm')">
												<span class="ui-button-text"><?php echo JText::_("LNG_CONTACT_COMPANY")?></span>
										</button>
										<button type="button" class="ui-dir-button ui-dir-button-grey" onclick="jQuery.unblockUI()">
												<span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
										</button>
									</div>
								</div>
							</fieldset>
						</div>
						
						<?php echo JHTML::_( 'form.token' ); ?>
						<input type='hidden' name='task' id="task" value='companies.contactCompany'/>
						<input type='hidden' name='userId' value='<?php echo $user->id?>'/>
						<input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
					</form>
				</div>
		</div>
	</div>
</div>	
<?php } ?>

<?php if($user->id>0){?>
<div id="add-bookmark" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			<h3 class="title"><?php echo JText::_('LNG_ADD_BOOKMARK') ?></h3>
		  		<div class="dialogContentBody" id="dialogContentBody">				
					<form id="bookmarkFrm" name="bookmarkFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
						<div class="review-repsonse">
						<fieldset>
							<div class="form-item">
								<label><?php echo JText::_('LNG_NOTE')?>:</label>
								<div class="outer_input">
									<textarea rows="5" name="note" id="note" cols="50" ></textarea><br>
								</div>
							</div>

							<div class="clearfix clear-left">
								<div class="button-row ">
									<button type="submit" class="ui-dir-button">
											<span class="ui-button-text"><?php echo JText::_("LNG_ADD")?></span>
									</button>
									<button type="button" class="ui-dir-button ui-dir-button-grey" onclick="jQuery.unblockUI()">
											<span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
									</button>
								</div>
							</div>
						</fieldset>
						</div>
						
						<?php echo JHTML::_( 'form.token' ); ?>
						<input type='hidden' name='task' value='companies.addBookmark'/>
						<input type='hidden' name='user_id' value='<?php echo $user->id?>'/>
                        <input type='hidden' name='item_type' value='<?php echo BOOKMARK_TYPE_BUSINESS ?>'/>
                        <input type="hidden" name="item_id" value="<?php echo $this->company->id?>" />
					</form>
				</div>
		</div>
	</div>
</div>	
<?php } ?>


<?php if($user->id>0){?>
<div id="update-bookmark" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			<h3 class="title"><?php echo JText::_('LNG_UPDATE_BOOKMARK') ?></h3>
		  		<div class="dialogContentBody" id="dialogContentBody">				
					<form id="updateBookmarkFrm" name="bookmarkFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
						<div class="review-repsonse">
						<fieldset>
							<div class="form-item">
								<a href="javascript:removeBookmark('companies')" class="red"> <?php echo JText::_("LNG_REMOVE_BOOKMARK")?></a>
							</div>
							<div class="form-item">
								<label><?php echo JText::_('LNG_NOTE')?>:</label>
								<div class="outer_input">
									<textarea rows="5" name="note" id="note" cols="50" ><?php echo isset($this->company->bookmark)?$this->escape($this->company->bookmark->note):"" ?></textarea>
								</div>
							</div>

							<div class="clearfix clear-left">
								<div class="button-row ">
									<button type="submit" class="ui-dir-button">
											<span class="ui-button-text"><?php echo JText::_("LNG_UPDATE")?></span>
									</button>
									<button type="button" class="ui-dir-button ui-dir-button-grey" onclick="jQuery.unblockUI()">
											<span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
									</button>
								</div>
							</div>
						</fieldset>
						</div>
						
						<?php echo JHTML::_( 'form.token' ); ?>
						<input type='hidden' id="task" name='task' value='companies.updateBookmark'/>
						<input type='hidden' name='id' value='<?php echo $this->company->bookmark->id ?>'/>
						<input type='hidden' name='user_id' value='<?php echo $user->id?>'/>
                        <input type='hidden' name='item_type' value='<?php echo BOOKMARK_TYPE_BUSINESS ?>'/>
                        <input type="hidden" name="item_id" value="<?php echo $this->company->id?>" />
					</form>
				</div>
		</div>
	</div>
</div>	
<?php } ?>

<div id="login-notice" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel" class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		<div class="dialogContent">
			<h3 class="title"><?php echo JText::_('LNG_INFO') ?></h3>
	  		<div class="dialogContentBody" id="dialogContentBody">				
				<p>
					<?php echo JText::_('LNG_YOU_HAVE_TO_BE_LOGGED_IN') ?>
				</p>
				<p>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($url)); ?>"><?php echo JText::_('LNG_CLICK_LOGIN') ?></a>
				</p>
			</div>
		</div>
	</div>
</div>


<div id="reportAbuseEmail" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>

		<div>
			<h3 class="title"><?php echo JText::_('LNG_REPORT_ABUSE') ?></h3>
			<div class="dialogContentBody" id="dialogContentBody">
				<div id="abuseMessageDiv" style="color: green"></div>
				<br/>

				<form id="report-listing" name="report-listing" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
					<p>
						<?php echo JText::_('LNG_REPORT_ABUSE_EXPLANATION') ?>
					</p>
					<div class="review-repsonse">
						<fieldset>

							<div class="form-item">
								<label><?php echo JText::_('LNG_CAUSE_REPORT') ?></label>
								<div class="outer_input">
									<label><input type="radio" name="report-cause" value="Outdated Information" checked><?php echo JText::_('LNG_OUTDATED_INFORMATION') ?></label><br/>
									<label><input type="radio" name="report-cause" value="Offensive Material"><?php echo JText::_('LNG_OFFENSIVE_MATERIAL') ?></label><br/>
									<label><input type="radio" name="report-cause" value="Inaccurate/Incorrect Information"><?php echo JText::_('LNG_INCORRECT_INFORMATION') ?></label><br/>
								</div>
							</div>

							<div class="form-item">
								<label><?php echo JText::_('LNG_EMAIL') ?></label>
								<div class="outer_input">
									<input type="text" name="reporterEmail" id="reporterEmail" class="validate[required, custom[email]]" value="<?php echo $user->id>0?$user->email:""?>"><br>
								</div>
							</div>

							<div class="form-item">
								<label><?php echo JText::_('LNG_MESSAGE')?>:</label>
								<div class="outer_input">
									<textarea rows="5" name="abuseMessage" id="abuseMessage" cols="50" class="validate[required]"></textarea><br>
								</div>
							</div>

							<?php if($this->appSettings->captcha){?>
								<div class="form-item">
									<?php 
											$namespace="jbusinessdirectory.contact";
											$class=" required";
											
											$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
																				
											if(!empty($captcha)){	
												echo $captcha->display("captcha", "captcha-div-report", $class);
											}
										?>
								</div>
							<?php } ?>
							
							<div class="clearfix clear-left">
								<div class="button-row">
									<button type="submit" class="ui-dir-button" onclick="saveForm('report-listing')">
										<span class="ui-button-text"><?php echo JText::_("LNG_REPORT")?></span>
									</button>
									<button type="button" class="ui-dir-button ui-dir-button-grey" onclick="jQuery.unblockUI()">
										<span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
									</button>
								</div>
							</div>

							<?php echo JHTML::_( 'form.token' ); ?>
							<input type='hidden' name='task' id="task" value='companies.reportListing'/>
							<input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
						</fieldset>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
    jQuery(document).ready(function () {
        jQuery("#agreementLink").click(function () {
            jQuery("#termAgreement").toggle();
        });

        jQuery("#terms-conditions-link").click(function () {
            jQuery("#term_conditions_text").toggle();
        });

	    jQuery("#report-terms-conditions-link").click(function () {
		    jQuery("#report_term_conditions_text").toggle();
	    });

        <?php if($this->appSettings->enable_ratings) { ?>
            renderAverageRating(<?php echo $this->company->review_score ?>);
            //renderUserRating(<?php echo isset($this->rating->rating) ? $this->rating->rating : '0' ?>, <?php echo $showNotice ?>, '<?php echo $this->company->id ?>');
        <?php } ?>

        <?php if(!$showData) { ?>
	        jQuery.blockUI({
    		      	message: "<?php echo JText::_('LNG_LOGIN_TO_VIEW_ALL');?>" ,
    		      	fadeIn: 700, 
                    fadeOut: 700, 
                    timeout: 7000, 
                    showOverlay: false, 
                    centerX: false, 
                    centerY: false, 
                    css: {width: '280px', height: "180px", 
                        bottom: '10px', 
                        left: '10px', 
                        right: '',
                        top: '', 
                        border: 'none', 
                        padding: '40px 15px', 
                        backgroundColor: '#FEFEFE', 
                        '-webkit-border-radius': '5px', 
                        '-moz-border-radius': '5px', 
                        color: '#555'  } 
             }); 
            
      	<?php } ?>     
       
        

        renderReviewRating();

        <?php if(!$showData) { ?>
	        jQuery.blockUI({
			      	message: "<?php echo JText::_('LNG_LOGIN_TO_VIEW_ALL');?>" ,
			      	fadeIn: 700, 
	                fadeOut: 700, 
	                timeout: 7000, 
	                showOverlay: false, 
	                centerX: false, 
	                centerY: false, 
	                css: {width: '270px', height: "180px", 
	                    bottom: '10px', 
	                    left: '10px', 
	                    right: '',
	                    top: '', 
	                    border: '1px solid #787878', 
	                    padding: '40px 15px', 
	                    backgroundColor: '#FEFEFE', 
	                    'box-shadow': '2px 2px 2px #888888',
	                    '-webkit-border-radius': '5px', 
	                    '-moz-border-radius': '5px', 
	                    color: '#232323'  } 
	         }); 
	  	<?php } ?>     
    });
</script>
