<?php
/**
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2007 - 2015 CMS Junkie. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.keepalive');

$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
$menuItemId="";
if(!empty($appSettings->menu_item_id)){
    $menuItemId = "&Itemid=".$appSettings->menu_item_id;
}
?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        Joomla.submitform(task, document.getElementById('item-form'));
    }
</script>

<div id="process-container" class="process-container">
    <ol class="process-steps">
        <li class="is-active dir-icon-user" data-step="1">
            <p><?php echo JText::_("LNG_BILLING_INFO")?></p>
        </li>
        <li class="dir-icon-credit-card" data-step="2">
            <p><?php echo JText::_("LNG_PAYMENT_METHOD")?></p>
        </li>
        <li class="progress__last dir-icon-file-text-o" data-step="3">
            <p><?php echo JText::_("LNG_ORDER_REVIEW")?></p>
        </li>
    </ol>
    <div class="clear"></div>
</div>
<div class="clear"></div>

<div class="category-form-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit'.$menuItemId); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
        <div class="clr mandatory oh">
            <p><?php echo JText::_("LNG_REQUIRED_INFO")?></p>
        </div>
        <fieldset class="boxed">

            <h2> <?php echo JText::_('LNG_OFFER_BUYER_DETAILS');?></h2>
            <p><?php echo JText::_('LNG_OFFER_BUYER_DETAILS_TXT');?></p>
            <div class="form-box">
                <div class="detail_box">
                    <div  class="form-detail req"></div>
                    <label for="first_name"><?php echo JText::_('LNG_FIRST_NAME')?> </label>
                    <input type="text" name="first_name" id="first_name" class="input_txt validate[required]" value="<?php echo $this->guestDetails->first_name ?>" maxlength="45">
                    <div class="clear"></div>
                </div>

                <div class="detail_box">
                    <div  class="form-detail req"></div>
                    <label for="last_name"><?php echo JText::_('LNG_LAST_NAME')?> </label>
                    <input type="text" name="last_name" id="last_name" class="input_txt validate[required]" value="<?php echo $this->guestDetails->last_name ?>" maxlength="45">
                    <div class="clear"></div>
                </div>

                <div class="detail_box">
                    <div  class="form-detail req"></div>
                    <label for="email"><?php echo JText::_('LNG_EMAIL')?> </label>
                    <input type="text" name="email" id="email" class="input_txt validate[required,custom[email]]" value="<?php echo $this->guestDetails->email ?>" maxlength="45">
                    <div class="clear"></div>
                </div>

                <div class="detail_box">
                    <div  class="form-detail req"></div>
                    <label for="phone"><?php echo JText::_('LNG_PHONE')?> </label>
                    <input type="text" name="phone" id="phone" class="input_txt validate[required]" value="<?php echo $this->guestDetails->phone ?>" maxlength="45">
                    <div class="clear"></div>
                </div>

                <div class="detail_box">
                    <div  class="form-detail req"></div>
                    <label for="address2"><?php echo JText::_('LNG_ADDRESS')?> </label>
                    <input type="text" name="address" id="address2" class="input_txt validate[required]" value="<?php echo $this->guestDetails->address ?>" maxlength="55">
                    <div class="clear"></div>
                </div>

                <div class="detail_box">
                    <div  class="form-detail req"></div>
                    <label for="postal_code"><?php echo JText::_('LNG_POSTAL_CODE')?> </label>
                    <input type="text" name="postal_code" id="postal_code" class="input_txt validate[required]" value="<?php echo $this->guestDetails->postalCode ?>" maxlength="45">
                    <div class="clear"></div>
                </div>

                <div class="detail_box">
                    <div  class="form-detail req"></div>
                    <label for="city"><?php echo JText::_('LNG_CITY')?> </label>
                    <input type="text" name="city" id="city" class="input_txt validate[required]" value="<?php echo $this->guestDetails->city ?>" maxlength="45">
                    <div class="clear"></div>
                </div>

                <div class="detail_box">
                    <div  class="form-detail req"></div>
                    <label for="region"><?php echo JText::_('LNG_REGION')?> </label>
                    <input type="text" name="region" id="region" class="input_txt validate[required]" value="<?php echo $this->guestDetails->county ?>" maxlength="45">
                    <div class="clear"></div>
                </div>

                <div class="detail_box">
                    <div  class="form-detail req"></div>
                    <label for="country"><?php echo JText::_('LNG_COUNTRY')?> </label>
                    <input type="text" name="country" id="country" class="input_txt validate[required]" value="<?php echo $this->guestDetails->country_name ?>" maxlength="45">
                    <div class="clear"></div>
                </div>
            </div>
        </fieldset>

        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" id="task" value="offerbuyerdetails.addBuyerDetails" />
        <?php echo JHTML::_( 'form.token' ); ?>
        <div class="clear"></div>

        <button type="button" class="ui-dir-button ui-dir-button-green" onclick="saveForm()">
            <span class="ui-button-text"><?php echo JText::_("LNG_CONTINUE")?></span>
        </button>
    </form>
</div>
<div class="clear"></div>

<script>
    function saveForm() {
        var isError = true;
        jQuery('#item-form').validationEngine('detach');

        if(!validateCmpForm())
            isError = false;

        jQuery("#item-form").validationEngine('attach');

        if(isError)
            return;

        var form = document.adminForm;
        form.submit();
    }

    function validateCmpForm() {
        var isError = jQuery("#item-form").validationEngine('validate');
        return !isError;
    }
</script>

