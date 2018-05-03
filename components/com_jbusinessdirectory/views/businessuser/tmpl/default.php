<?php 
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
JHtml::_('behavior.keepalive');
$user = JFactory::getUser();

$language = JFactory::getLanguage();
$language->load('com_users');

$showOnlyLogin = JFactory::getApplication()->input->get('showOnlyLogin');

?>


<div id="process-container" class="process-container">
	<ol class="process-steps">
		<li class="is-complete dir-icon-inbox" data-step="1">
			<p><?php echo JText::_("LNG_CHOOSE_PACKAGE")?></p>
		</li>
		<li class="is-active dir-icon-user" data-step="2">
			<p><?php echo JText::_("LNG_BASIC_INFO")?></p>
		</li>
		<li class="progress__last dir-icon-file-text-o" data-step="3">
			<p><?php echo JText::_("LNG_LISTING_INFO")?></p>
		</li>
	</ol>
	<div class="clear"></div>
</div>
	

<div id="user-details" class="row-fluid user-details">
	<?php if(!empty($this->package)){?>
	<div class="span3">
		<div class="featured-product-col" >
			<?php
				$package = $this->package;
				require  JPATH_COMPONENT_SITE."/views/packages/tmpl/default_package.php"
			?>
		</div>
	</div>
	<?php }?>


	<?php
		$usersConfig = JComponentHelper::getParams('com_users');
		if ($usersConfig->get('allowUserRegistration') && empty($showOnlyLogin)){ ?>
		<div class="span5">
			<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.addUser'); ?>" method="post" name="registration-form" id="registration-form" >
				<fieldset>
					<h3><?php echo JText::_("LNG_USER_REGISTRATION_DETAILS")?></h3>
					<p>
						<?php echo JText::_("LNG_USER_REGISTRATION_DETAILS_TXT")?>
					</p>
					<div class="form-item">
						<label for="username"><?php echo JText::_('LNG_NAME') ?></label>
						<div class="outer_input">
							<input type="text" name="name" id="name" size="50" class="validate[required]"><br>
						</div>
					</div>
					<div class="form-item">
						<label for="username"><?php echo JText::_('LNG_USERNAME') ?></label>
						<div class="outer_input">
							<input type="text" name="username" id="username" size="50" class="validate[required]"><br>
						</div>
					</div>
					<div class="form-item">
						<label for="email"><?php echo JText::_('LNG_EMAIL') ?></label>
						<div class="outer_input">
							<input type="text" name="email" id="email" size="50" class="validate[required,custom[email]]"><br>
						</div>
					</div>
					<div class="form-item">
						<label for="password"><?php echo JText::_('LNG_PASSWORD') ?></label>
						<div class="outer_input">
							<input type="password" name="password" id="password" size="50" class="validate[required]"><br>
						</div>
					</div>
					<div class="form-item">
						<label for="passwordc"><?php echo JText::_('LNG_CONFIRM_PASSWORD') ?></label>
						<div class="outer_input">
							<input type="password" name="passwordc" id="passwordc" size="50" class="validate[required]"><br>
						</div>
					</div>
					<?php if($this->appSettings->captcha){?>
						<div class="form-item">
							<?php 
							$namespace="jbusinessdirectory.contact";
							$class=" required";
							
							$captcha = JCaptcha::getInstance("recaptcha");
																
							if(!empty($captcha)){	
								echo $captcha->display("captcha", "captcha-div-registration", $class);
							}
							?>
						</div>
					<?php } ?>
								
					<div class="control-group">
						<div class="controls">
							<button type="submit"><?php echo JText::_('LNG_CREATE_ACCOUNT') ?></button>
						</div>
					</div>		
					
					<input type="hidden" name="filter_package" id="filter_package" value="<?php echo $this->filter_package ?>" />
				</fieldset>
			</form>
		</div>
	<?php } ?>
	<div class="span4 inner-box">
		<div class="">
			<h3><?php echo JText::_("LNG_ALREADY_HAVE_ACCOUNT")?></h3>
			<p>
				<?php echo JText::_("LNG_ALREADY_HAVE_ACCOUNT_TXT")?>
			</p>
			
			<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.loginUser'); ?>" method="post" id="login-form" name="login-form">
				<fieldset>
					<div class="form-item">
						<label for="username"><?php echo JText::_('LNG_USERNAME') ?></label>
						<div class="outer_input">
							<input type="text" name="username" id="username" size="50" class="validate[required]"><br>
						</div>
					</div>
					<div class="form-item">
						<label for="password"><?php echo JText::_('LNG_PASSWORD') ?></label>
						<div class="outer_input">
							<input type="password" name="password" id="password" size="50" class="validate[required]"><br>
						</div>
					</div>
											
					<div class="control-group">
						<div class="controls">
							<button type="submit"><?php echo JText::_('LNG_LOG_IN') ?></button>
						</div>
					</div>		
					<ul class="nav">
						<li>
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
							<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
						</li>
						<li>
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
							<?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?></a>
						</li>
  					</ul>
				</fieldset>
				<input type="hidden" name="filter_package" id="filter_package" value="<?php echo $this->filter_package ?>" />
			</form>
		</div>
	</div>
</div>
	
<script>

jQuery(document).ready(function(){
	jQuery("#registration-form").validationEngine('attach');
	jQuery("#login-form").validationEngine('attach');
});
</script>