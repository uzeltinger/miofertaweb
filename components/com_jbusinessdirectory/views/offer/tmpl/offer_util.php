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
      <span title="Cancel" class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
    </div>

    <div class="dialogContent">
      <h3 class="title"><?php echo JText::_('LNG_CONTACT_OFFER_OWNER') ?></h3>
      <div class="dialogContentBody" id="dialogContentBody">
        <form id="contactCompanyFrm" name="contactCompanyFrm"
              action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
          <p>
            <?php echo JText::_('LNG_OFFER_CONTACT_TEXT') ?>
          </p>
          <div class="review-repsonse">
            <fieldset>

              <div class="form-item">
                <label><?php echo JText::_('LNG_FIRST_NAME') ?></label>
                <div class="outer_input">
                  <input type="text" name="firstName" id="firstName" class="input_txt  validate[required]">
                  <span class="error_msg" id="frmFirstNameC_error_msg"
                        style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                </div>
              </div>

              <div class="form-item">
                <label><?php echo JText::_('LNG_LAST_NAME') ?></label>
                <div class="outer_input">
                  <input type="text" name="lastName" id="lastName" class="input_txt  validate[required]">
                  <span class="error_msg" id="frmLastNameC_error_msg"
                        style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                </div>
              </div>

              <div class="form-item">
                <label><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
                <div class="outer_input">
                  <input type="text" name="email" id="email" class="input_txt  validate[required,custom[email]]">
                  <span class="error_msg" id="frmEmailC_error_msg"
                        style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                </div>
              </div>

              <div class="form-item">
                <label><?php echo JText::_('LNG_CONTACT_TEXT') ?>:</label>
                <div class="outer_input">
                  <textarea rows="5" name="description" id="description"
                            class="input_txt  validate[required]"></textarea>
                  <span class="error_msg" id="frmDescriptionC_error_msg"
                        style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                </div>
              </div>

              <div class="form-item">
                <input type="checkbox" name="copy-me" id="copy-me" value="1"> <?php echo JText::_('LNG_COPY_ME') ?>
              </div>

              <div class="form-item">
				<input type="checkbox"  name="terms-conditions" id="terms-conditions" value="1" class="validate[required]"> <a href="javascript:void(0)" id="terms-conditions-link"><?php echo JText::_('LNG_TERMS_AGREAMENT')?></a>
			  </div>

				<div id="term_conditions_text" style="display: none;">
					<?php echo $this->appSettings->contact_terms_conditions ?>
				</div>
              
              <?php if ($this->appSettings->captcha) { ?>
                <div class="form-item">
                  <?php
                  $namespace = "jbusinessdirectory.contact";
                  $class = " required";

                  $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

                  if (!empty($captcha)) {
                    echo $captcha->display("captcha", "captcha-div-contact", $class);
                  }
                  ?>
                </div>
              <?php } ?>

              <div class="clearfix clear-left">
                <div class="button-row ">
                  <button type="button" class="ui-dir-button" onclick="saveForm('contactCompanyFrm')">
                    <span class="ui-button-text"><?php echo JText::_("LNG_SEND_EMAIL") ?></span>
                  </button>
                  <button type="button" class="ui-dir-button ui-dir-button-grey" onclick="jQuery.unblockUI()">
                    <span class="ui-button-text"><?php echo JText::_("LNG_CANCEL") ?></span>
                  </button>
                </div>
              </div>
            </fieldset>
          </div>

          <?php echo JHTML::_('form.token'); ?>
          <input type='hidden' name='task' id="task" value='offer.contactCompany'/>
          <input type="hidden" name="contact_id_offer" value="<?php echo $this->offer->company->email ?>"/>
          <input type='hidden' name='userId' value='<?php echo $user->id ?>'/>
          <input type="hidden" name="companyId" value="<?php echo $this->offer->company->id ?>"/>
          <input type="hidden" name="offer_Id" value="<?php echo $this->offer->id ?>"/>
        </form>
      </div>
    </div>
  </div>
</div>

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
                    <input type='hidden' name='task' value='offer.addBookmark'/>
                    <input type='hidden' name='user_id' value='<?php echo $user->id?>'/>
                    <input type='hidden' name='item_type' value='<?php echo BOOKMARK_TYPE_OFFER ?>'/>
                    <input type="hidden" name='item_id' value="<?php echo $this->offer->id?>" />
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
                                    <a href="javascript:removeBookmark('offer')" class="red"> <?php echo JText::_("LNG_REMOVE_BOOKMARK")?></a>
                                </div>
                                <div class="form-item">
                                    <label><?php echo JText::_('LNG_NOTE')?>:</label>
                                    <div class="outer_input">
                                        <textarea rows="5" name="note" id="note" cols="50" ><?php echo isset($this->offer->bookmark)?$this->offer->bookmark->note:"" ?></textarea>
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
                        <input type='hidden' id="task" name='task' value='offer.updateBookmark'/>
                        <input type='hidden' name='id' value='<?php echo $this->offer->bookmark->id ?>'/>
                        <input type='hidden' name='user_id' value='<?php echo $user->id?>'/>
                        <input type='hidden' name='item_type' value='<?php echo BOOKMARK_TYPE_OFFER ?>'/>
                        <input type="hidden" name="item_id" value="<?php echo $this->offer->id?>" />
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

<script>
jQuery(document).ready(function(){
    var url = jbdUtils.siteRoot + 'index.php?option=com_jbusinessdirectory';
	jQuery("#terms-conditions-link").click(function () {
		jQuery("#term_conditions_text").toggle();
	});
});
</script>
