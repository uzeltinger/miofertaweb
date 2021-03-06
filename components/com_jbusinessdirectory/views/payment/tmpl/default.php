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
$orderId = $this->state->get("payment.orderId");
$ssl = 0;
if($this->appSettings->enable_https_payment)
	$ssl = 1;

?>
<div id="payment-details">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=payment', false, $ssl); ?>" method="post" name="discount-form" id="discount-form" >
		<div>
			<div class="payment-items">
				<table>
					<thead>
						<tr  bgcolor="#D9E5EE" class="heading">
							<td><?php echo JText::_('LNG_PRODUCT_SERVICE'); ?></td>
							
							<td align="right"><?php echo JText::_('LNG_UNIT_PRICE'); ?></td>
							<td align="right"><?php echo JText::_('LNG_TOTAL'); ?></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="left">
								<div class="left">
									<strong><?php echo $this->order->service ?></strong> <br/>
									<?php echo $this->order->description ?>
								</div>
							</td>
							<td align="right" nowrap="nowrap"><?php echo JBusinessUtil::getPriceFormat($this->order->initial_amount) ?> </td>
							<td align="right" nowrap="nowrap"><?php echo JBusinessUtil::getPriceFormat($this->order->initial_amount) ?> </td>
						</tr>
						<tr>
							<td colspan="5">&nbsp;</td>
						</tr>
						<?php if($this->order->discount_amount>0){?>
							<tr>
								<td align="right" colspan="2"><b><?php echo JText::_("LNG_DISCOUNT")?>:</b></td>
								<td align="right" nowrap="nowrap"><?php echo JBusinessUtil::getPriceFormat($this->order->discount_amount) ?> </td>
							</tr>
						<?php } ?>
						<tr>
							<td align="right" colspan="2"><b><?php echo JText::_('LNG_SUB_TOTAL'); ?>:</b></td>
							<td align="right" nowrap="nowrap"><?php echo JBusinessUtil::getPriceFormat($this->order->initial_amount- $this->order->discount_amount) ?> </td>
						</tr>
						<?php if($this->appSettings->vat>0){?>
							<tr>
								<td align="right" colspan="2"><b><?php echo JText::_('LNG_VAT'); ?> (<?php echo $this->appSettings->vat?>%):</b></td>
								<td align="right" nowrap="nowrap"><?php echo JBusinessUtil::getPriceFormat($this->order->vat_amount) ?> </td>
							</tr>
						<?php } ?>
						<?php if(!empty($this->order->taxes)) { ?>
							<?php foreach ($this->order->taxes as $tax) { ?>
								<tr>
									<td colspan="2">
										<div class="right">
											<?php echo $tax->tax_name ?>  <?php echo $tax->tax_type==2?"(".$tax->percentage."%)":""?>
										</div>
									</td>
									<td align="right"  nowrap="nowrap"><?php echo JBusinessUtil::getPriceFormat($tax->tax_amount); ?> </td>
								</tr>
							<?php } ?>
							<?php
						}
						?>
						<tr>
							<td align="right" colspan="2"><b><?php echo JText::_('LNG_TOTAL'); ?>:</b></td>
							<td align="right" nowrap="nowrap"><?php echo JBusinessUtil::getPriceFormat($this->order->amount) ?> </td>
						</tr>
					</tbody>
				</table>	
			</div>
			<div class="clear"></div>
			<div>
				<label for="coupon_code"><?php echo JText::_('LNG_DISCOUNT_TXT');?></label>
				<input type="text" size="40" value="<?php echo !empty($this->order->discount)?$this->order->discount->code:"" ?>" name="discount_code" id="discount_code" class="input-text noSubmit">				          	
				<button type="submit" class="ui-dir-button ui-dir-button-green">
						<span class="ui-button-text"><?php echo JText::_("LNG_APPLY")?></span>
				</button>
			</div>
			<div class="clear"></div>
		</div>
		
		<input type="hidden" name="orderId" value="<?php echo $this->state->get("payment.orderId")?>" /> 
		<input type="hidden" name="companyId" value="<?php echo $this->companyId?>" />  
		<input type="hidden" name="reset_discount" value="1" />  
		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" /> 
	</form>

	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=payment&layout=redirect', false, $ssl); ?>" method="post" name="payment-form" id="payment-form" >
		<?php if(!empty($orderId)){?>
			<fieldset>
				<h4><?php echo JText::_("LNG_PAYMENT_METHODS")?></h4>
				
				<?php if($this->order->amount>0){?>
					<dl class="sp-methods" id="checkout-payment-method-load">
						<?php
						    foreach ($this->paymentMethods as $method){
						?>
						    <dt>
						        <span class="no-display"><input id="p_method_<?php echo $method->type ?>" value="<?php echo $method->type ?>" type="radio" name="payment_method" onclick="switchMethod('<?php echo $method->type ?>')" class="radio validate[required]" /></span>
						    	<img class="payment-icon" src="<?php echo JURI::base() ."components/".JBusinessUtil::getComponentName().'/assets/images/payment/'.strtolower($method->type).'.gif' ?>"  />
						        <label for="p_method_<?php echo $method->type ?>"><?php echo $method->name ?> </label>
						    </dt>
						   	 <?php if ($html = $method->getPaymentProcessorHtml($this->order)){ ?>
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
		<?php } ?>
		
		<input type="hidden" name="orderId" value="<?php echo $this->state->get("payment.orderId")?>" /> 
		<input type="hidden" name="companyId" value="<?php echo $this->companyId?>" /> 
		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" /> 
		<input type="hidden" name="task" value="payment.processTransaction" />
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
	//console.debug(method);
	jQuery("#payment_form_"+method).show();
}

</script>