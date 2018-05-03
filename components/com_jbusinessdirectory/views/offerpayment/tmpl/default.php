<?php // no direct accesss
/**
 * @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();

$ssl = 0;
if($this->appSettings->enable_https_payment)
    $ssl = 1;

$menuItemId="";
if(!empty($this->appSettings->menu_item_id)){
    $menuItemId = "&Itemid=".$this->appSettings->menu_item_id;
}
?>
<div id="process-container" class="process-container">
    <ol class="process-steps">
        <li class="is-complete dir-icon-user" data-step="1">
            <p><?php echo JText::_("LNG_BILLING_INFO")?></p>
        </li>
        <li class="is-active dir-icon-credit-card" data-step="2">
            <p><?php echo JText::_("LNG_PAYMENT_METHOD")?></p>
        </li>
        <li class="progress__last dir-icon-file-text-o" data-step="3">
            <p><?php echo JText::_("LNG_ORDER_REVIEW")?></p>
        </li>
    </ol>
    <div class="clear"></div>
</div>
<div class="clear"></div>

<div id="payment-details" class="event-booking-payment">
    <div class="event-booking-details row-fluid">
        <div class="event-ticket-details">
            <div class="event-guest-details ">
                <?php echo $this->orderDetails->buyerDetailsSummary?>
            </div>
            <div class="event-guest-details ">
                <?php echo $this->orderDetails->itemsSummary?>
            </div>
        </div>
    </div>

    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=offerpayment&layout=redirect'.$menuItemId, false, $ssl); ?>" method="post" name="payment-form" id="payment-form" >
        <fieldset>
            <h4><?php echo JText::_("LNG_PAYMENT_METHODS")?></h4>

            <?php if(!empty($this->orderDetails->totalPrice)){?>
                <dl class="sp-methods" id="checkout-payment-method-load">
                    <?php
                    $oneMethod = count($this->paymentMethods) <= 1;
                    foreach ($this->paymentMethods as $method){
                        ?>
                        <dt>
                            <?php if(!$oneMethod){ ?>
                                <input id="p_method_<?php echo $method->type ?>" value="<?php echo $method->type ?>" type="radio" name="payment_method" title="<?php echo $method->name ?>" onclick="switchMethod('<?php echo $method->type ?>')" class="radio validate[required]" />
                            <?php }else{ ?>
                                <span class="no-display"><input id="p_method_<?php echo $method->type ?>" value="<?php echo $method->type ?>" type="radio" name="payment_method" checked="checked" class="radio validate[required]" /></span>
                                <?php $oneMethod = $method->type; ?>
                            <?php } ?>
                            <img class="payment-icon" src="<?php echo JURI::base() ."components/".JBusinessUtil::getComponentName().'/assets/images/payment/'.strtolower($method->type).'.gif' ?>"  />
                            <label for="p_method_<?php echo $method->type ?>"><?php echo $method->name ?> </label>
                        </dt>
                        <?php if ($html = $method->getPaymentProcessorHtml()){ ?>
                            <dd>
                                <?php echo $html; ?>
                            </dd>
                        <?php } ?>
                    <?php } ?>
                </dl>
            <?php }else{?>
                <div><?php echo JText::_("LNG_NO_PAYMENT_INFO_REQUIRED")?></div><br/><br/>
            <?php } ?>
        </fieldset>

        <input type="hidden" name="orderId" value="<?php echo $this->state->get("payment.orderId")?>" />
        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" value="offerpayment.processTransaction" />
        <input type="hidden" name="discount_code" value="<?php echo !empty($this->order->discount)?$this->order->discount->code:"" ?>" />

        <button type="submit" class="ui-dir-button ui-dir-button-green">
            <span class="ui-button-text"><?php echo JText::_("LNG_CONTINUE")?></span>
        </button>
    </form>
</div>
<script>
    jQuery(document).ready(function(){
        jQuery("#payment-form").validationEngine('attach');

    });

    function switchMethod(method){
        jQuery("#checkout-payment-method-load ul").each(function(){
            jQuery(this).hide();
        });

        jQuery("#payment_form_"+method).show();
    }
</script>