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
      <h3 class="title"><?php echo JText::_('LNG_CONTACT_EVENT_OWNER') ?></h3>
      <div class="dialogContentBody" id="dialogContentBody">
        <form id="contactCompanyFrm" name="contactCompanyFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
          <p>
            <?php echo JText::_('LNG_EVENT_CONTACT_TEXT') ?>
          </p>
          <div class="review-repsonse">
            <fieldset>

              <div class="form-item">
                <label><?php echo JText::_('LNG_FIRST_NAME') ?></label>
                <div class="outer_input">
                  <input type="text" name="firstName" id="firstName" class="input_txt  validate[required]">
                </div>
              </div>

              <div class="form-item">
                <label><?php echo JText::_('LNG_LAST_NAME') ?></label>
                <div class="outer_input">
                  <input type="text" name="lastName" id="lastName" class="input_txt  validate[required]">
                </div>
              </div>

              <div class="form-item">
                <label><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
                <div class="outer_input">
                  <input type="text" name="email" id="email" class="input_txt  validate[required,custom[email]]">
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
                  <button type="button" class="ui-dir-button" onClick="saveForm('contactCompanyFrm')">
                    <span class="ui-button-text"><?php echo JText::_("LNG_SEND_EMAIL")?></span>
                  </button>
                  <button type="button" class="ui-dir-button ui-dir-button-grey" onclick="jQuery.unblockUI()">
                    <span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
                  </button>
                </div>
              </div>
            </fieldset>
          </div>

          <?php echo JHTML::_( 'form.token' ); ?>
          <input type='hidden' name='task' value='event.contactCompany'/>
          <input type="hidden" name="contact_id_event" value="<?php echo $this->event->contact_email ?>" />
          <input type='hidden' name='userId' value='<?php echo $user->id?>'/>
          <input type="hidden" name="companyId" value="<?php echo $this->event->company->id?>" />
          <input type="hidden" name="event_Id" value="<?php echo $this->event->id?>" />
        </form>
      </div>
    </div>
  </div>
</div>


<div id="login-notice" style="display:none">
  <div id="dialog-container">
    <div class="titleBar">
      <span class="dialogTitle" id="dialogTitle"></span>
      <span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
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

<div id="company-list" style="display:none">
  <div id="dialog-container">
    <div class="titleBar">
      <span class="dialogTitle" id="dialogTitle"></span>
      <span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
        <span title="Cancel" class="closeText">x</span>
      </span>
    </div>
    <div class="dialogContent">
      <h3 class="title"><?php echo JText::_('LNG_SELECT_COMPANIES') ?></h3>
      <div class="dialogContentBody" style="padding-bottom:30px;" id="dialogContentBody">
        <p><?php echo JText::_('LNG_SELECT_COMPANIES_TO_ASSOCIATE'); ?></p>
        <select name="associatedCompanies[]" id="userAssociatedCompanies" multiple
                title="<?php echo JText::_('LNG_JOPTION_SELECT_COMPANY'); ?>"
                class="chosen-select validate[required]">
          <?php echo JHtml::_('select.options', $this->userCompanies, 'id', 'name', $this->userAssociatedCompanies); ?>
        </select><br/><br/>
        <button class="btn btn-xs btn-success btn-panel right" onclick="associateCompanies(<?php echo $this->event->id ?>)">
          <?php echo JText::_('LNG_SUBMIT'); ?>
        </button>
      </div>
      <div style="display:none;" id="associated-companies-message">
        <h3 style="color:#000"><?php echo JText::_('LNG_COMPANIES_JOINED_EVENT'); ?></h3>
        <br/>
        <p><?php echo JText::_('LNG_EVENT_OWNER_NOTIFIED'); ?></p>
        <a class="btn btn-xs btn-danger btn-panel" style="margin-bottom:5px;" onclick="jQuery.unblockUI()">
          <?php echo JText::_('LNG_CLOSE'); ?>
        </a>
      </div>
    </div>
  </div>
</div>

<script>
    var url = jbdUtils.siteRoot + 'index.php?option=com_jbusinessdirectory';
	jQuery(document).ready(function(){
		jQuery("#terms-conditions-link").click(function () {
			jQuery("#term_conditions_text").toggle();
		});
	});
</script>