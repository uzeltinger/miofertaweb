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
		
							<div class="form-item">
								<label for="firstName"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
								<div class="outer_input">
									<input type="text" name="firstName" id="firstName" size="50" class="input_txt  validate[required]"><br>
								</div>
							</div>
		
							<div class="form-item">
								<label for="lastName"><?php echo JText::_('LNG_LAST_NAME') ?></label>
								<div class="outer_input">
									<input type="text" name="lastName" id="lastName" size="50" class="input_txt  validate[required]"><br>
								</div>
							</div>
		
							<div class="form-item">
								<label for="email"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
								<div class="outer_input">
									<input type="text" name="email" id="email" size="50" class="input_txt  validate[required,custom[email]]"><br>
								</div>
							</div>

							<div class="form-item">
								<label for="description" ><?php echo JText::_('LNG_CONTACT_TEXT')?>:</label>
								<div class="outer_input">
									<textarea rows="5" name="description" id="description" cols="50" class="input_txt  validate[required]"></textarea><br>
								</div>
							</div>

                            <div class="form-item">
                                <input type="checkbox"  name="search-contact-terms-conditions" id="search-contact-quote-terms-conditions" value="1" class="validate[required]"> <a href="javascript:void(0)" id="search-contact-terms-conditions-link"><?php echo JText::_('LNG_TERMS_AGREAMENT')?></a>
                            </div>

                            <div id="search_contact_term_conditions_text" style="display: none;">
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
									<button type="button" class="ui-dir-button" onclick="contactCompanyList('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=companies.contactCompanyAjax', false); ?>')">
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
						<input type='hidden' name='task' id="contact_company_task" value='companies.contactCompany'/>
						<input type='hidden' name='userId' value=''/>
						<input type="hidden" id="companyId" name="companyId" value="" />
					</form>
				</div>
		</div>
	</div>
</div>	

<div id="company-quote" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			<h3 class="title"><?php echo JText::_('LNG_QUOTE_COMPANY') ?></h3>
		  		<div class="dialogContentBody" id="dialogContentBody">
				
					<form id="quoteCompanyFrm" name="quoteCompanyFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
						<p>
							<?php echo JText::_('LNG_COMPANY_QUTE_TEXT') ?>
						</p>
						<div class="review-repsonse">
						<fieldset>
		
							<div class="form-item">
								<label for="firstName"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
								<div class="outer_input">
									<input type="text" name="firstName" id="firstName-quote" size="50" class="input_txt  validate[required]"><br>
								</div>
							</div>
		
							<div class="form-item">
								<label for="lastName"><?php echo JText::_('LNG_LAST_NAME') ?></label>
								<div class="outer_input">
									<input type="text" name="lastName" id="lastName-quote" size="50" class="input_txt  validate[required]"><br>
								</div>
							</div>
		
							<div class="form-item">
								<label for="email"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
								<div class="outer_input">
									<input type="text" name="email" id="email-quote" size="50" class="input_txt  validate[required,custom[email]]"><br>
								</div>
							</div>

							<div class="form-item">
								<label for="description" ><?php echo JText::_('LNG_CONTACT_TEXT')?>:</label>
								<div class="outer_input">
									<textarea rows="5" name="description" id="description-quote" cols="50" class="input_txt  validate[required]" ></textarea><br>
								</div>
							</div>

							<div class="form-item">
								<input type="checkbox"  name="company-quote-terms-conditions" id="company-quote-terms-conditions" value="1" class="validate[required]"> <a href="javascript:void(0)" id="company-quote-terms-conditions-link"><?php echo JText::_('LNG_TERMS_AGREAMENT')?></a>
							</div>

							<div id="company_quote_term_conditions_text" style="display: none;">
								<?php echo $this->appSettings->terms_conditions ?>
							</div>
					
							<?php if($this->appSettings->captcha){?>
								<div class="form-item">
									<?php 
									$namespace="jbusinessdirectory.contact";
									$class=" required";
									
									$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
																		
									if(!empty($captcha)){	
										echo $captcha->display("captcha", "captcha-div-quote", $class);
									}
									?>
									
								</div>
							<?php } ?>
							
							<div class="clearfix clear-left">
								<div class="button-row ">
									<button type="button" class="ui-dir-button" onclick="requestQuoteCompany('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=companies.requestQuoteCompanyAjax', false); ?>')">
											<span class="ui-button-text"><?php echo JText::_("LNG_REQUEST_QUOTE")?></span>
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
						<input type='hidden' name='userId' value=''/>
						<input type="hidden" id="companyId" name="companyId" value="" />
					</form>
				</div>
		</div>
	</div>
</div>

<script>
    var contactListUrl = '<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=companies.contactCompanyAjax', false); ?>';
</script>