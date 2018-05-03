<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$app = JFactory::getApplication();
$user = JFactory::getUser();

if($user->id == 0){
	$return = base64_encode(('index.php?option=com_jbusinessdirectory&view=billingdetails'));
	$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return,false));
}

if(!empty($this->item->id) && $user->id!=$this->item->user_id){
	$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=userdetails'));
}

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{	
		Joomla.submitform(task, document.getElementById('item-form'));
	}
</script>
<div class="clear"></div>	
<div class="category-form-container">	
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
		<div class="clr mandatory oh">
			<p><?php echo JText::_("LNG_REQUIRED_INFO")?></p>
		</div>
		<fieldset class="boxed">

			<h2> <?php echo JText::_('LNG_BILLING_DETAILS');?></h2>
			<p><?php echo JText::_('LNG_BILLING_DETAILS_TXT');?></p>
			<div class="form-box">
				<div class="detail_box">
					<div  class="form-detail req"></div>
					<label for="first_name"><?php echo JText::_('LNG_FIRST_NAME')?> </label> 
					<input type="text" name="first_name" id="first_name" class="input_txt  validate[required]" value="<?php echo !empty($this->item->first_name)?$this->item->first_name:$user->name ?>" maxlength="45">
					<div class="clear"></div>
				</div>

				<div class="detail_box">
					<div  class="form-detail req"></div>
					<label for="last_name"><?php echo JText::_('LNG_LAST_NAME')?> </label> 
					<input type="text" name="last_name" id="last_name" class="input_txt  validate[required]" value="<?php echo $this->item->last_name ?>" maxlength="45">
					<div class="clear"></div>
				</div>

				<div class="detail_box">
					
					<label for="company_name"><?php echo JText::_('LNG_COMPANY_NAME')?> </label> 
					<input type="text" name="company_name" id="company_name" class="input_txt" value="<?php echo !empty($this->company->name)?$this->company->name:$this->item->company_name ?>" maxlength="55">
					<div class="clear"></div>
				</div>
				
				<div class="detail_box">
					<div  class="form-detail req"></div>
					<label for="email"><?php echo JText::_('LNG_EMAIL')?> </label> 
					<input type="text" name="email" id="email" class="input_txt  validate[required,custom[email]]" value="<?php echo !empty($this->item->email)?$this->item->email:$user->email ?>" maxlength="45">
					<div class="clear"></div>
				</div>
				
				<div class="detail_box">
					
					<label for="phone"><?php echo JText::_('LNG_PHONE')?> </label> 
					<input type="text" name="phone" id="phone" class="input_txt" value="<?php echo !empty($this->company->phone)?$this->company->phone:$this->item->phone ?>" maxlength="45">
					<div class="clear"></div>
				</div>
				
				<div class="detail_box">
					
					<label for="address2"><?php echo JText::_('LNG_ADDRESS')?> </label> 
					<input type="text" name="address" id="address2" class="input_txt" value="<?php echo !empty($this->company->address)?$this->company->address:$this->item->address ?>" maxlength="55">
					<div class="clear"></div>
				</div>
				
				<div class="detail_box">
					
					<label for="postal_code"><?php echo JText::_('LNG_POSTAL_CODE')?> </label> 
					<input type="text" name="postal_code" id="postal_code" class="input_txt" value="<?php echo !empty($this->company->postalCode)?$this->company->postalCode:$this->item->postal_code ?>" maxlength="45">
					<div class="clear"></div>
				</div>
				
				<div class="detail_box">
					
					<label for="city"><?php echo JText::_('LNG_CITY')?> </label> 
					<input type="text" name="city" id="city" class="input_txt" value="<?php echo !empty($this->company->city)?$this->company->city:$this->item->city ?>" maxlength="45">
					<div class="clear"></div>
				</div>
				
				<div class="detail_box">
					
					<label for="region"><?php echo JText::_('LNG_REGION')?> </label> 
					<input type="text" name="region" id="region" class="input_txt" value="<?php echo !empty($this->company->county)?$this->company->county:$this->item->region ?>" maxlength="45">
					<div class="clear"></div>
				</div>
				
				<div class="detail_box">
					
					<label for="country"><?php echo JText::_('LNG_COUNTRY')?> </label> 
					<input type="text" name="country" id="country" class="input_txt" value="<?php echo !empty($this->company->country_name)?$this->company->country_name:$this->item->country ?>" maxlength="45">
					<div class="clear"></div>
				</div>
			</div>
		</fieldset>
			
	<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" /> 
	<input type="hidden" name="task" id="task" value="billingdetails.save" /> 
	<input type="hidden" name="id" value="<?php echo $this->item->id ?>" /> 
	<input type="hidden" name="user_id" value="<?php echo $user->id ?>" /> 
	<input type="hidden" name="orderId" id="orderId" value="<?php echo $this->orderId ?>" /> 
	<?php echo JHTML::_( 'form.token' ); ?>
	
	
	<div class="clear"></div>	
	
	<?php if(empty($this->orderId)){?>
		<div class="buttons">
		
			<button type="button" class="ui-dir-button ui-dir-button-green" onClick="saveForm()">
					<span class="ui-button-text"><i class="dir-icon-edit"></i> <?php echo JText::_("LNG_SAVE")?></span>
			</button>
			<button type="button" class="ui-dir-button ui-dir-button-grey" onClick="Joomla.submitbutton('billingdetails.cancel')">
					<span class="ui-button-text"><i class="dir-icon-remove-sign red"></i> <?php echo JText::_("LNG_CANCEL")?></span>
			</button>
		</div>
	<?php }else{?>
		<button type="button" class="ui-dir-button ui-dir-button-green"  onClick="saveForm()">
			<span class="ui-button-text"><?php echo JText::_("LNG_CONTINUE")?></span>
		</button>
	<?php } ?>
</form>
</div>
<div class="clear"></div>	

<script>
	function saveForm() {
	    var isError = true;
	    jQuery('#item-form').validationEngine('attach');
	
	    if(!validateCmpForm())
	        isError = false;
	
	    if(isError)
	        return;
	
	    Joomla.submitbutton('billingdetails.save')
	}
	
    function validateCmpForm() {
        var isError = jQuery("#item-form").validationEngine('validate');
        return !isError;
    }
</script>
