<?php // no direct access
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
?>

<?php require_once JPATH_COMPONENT_SITE.'/classes/attributes/attributeservice.php'; ?>

<?php
	if($this->appSettings->offer_view==1){
        echo $this->loadTemplate('style_1');
	}else{
        echo $this->loadTemplate('style_2');
	}
?>

<div id="cart-dialog" style="display:none">
	<h3 style="color:#000"><i class="dir-icon-check-circle" style="color:green"></i> <?php echo JText::_('LNG_ITEM_ADDED_TO_CART'); ?></h3><br/>
	<a class="btn btn-xs btn-primary btn-panel" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=cart'); ?>">
		<?php echo JText::_('LNG_VIEW_SHOPPING_CART'); ?>
	</a>
	<a class="btn btn-xs btn-danger btn-panel" onclick="jQuery.unblockUI()">
		<?php echo JText::_('LNG_CLOSE'); ?>
	</a>
	<br/><br/>
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
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($this->escape($url))); ?>"><?php echo JText::_('LNG_CLICK_LOGIN') ?></a>
				</p>
			</div>
		</div>
	</div>
</div>

<?php
jimport('joomla.application.module.helper');
// this is where you want to load your module position
$modules = JModuleHelper::getModules('dir-offer');

if(isset($modules) && count($modules)>0){
    $fullWidth = false; ?>
    <div class="dir-offer">
        <?php foreach($modules as $module) {
            echo JModuleHelper::renderModule($module);
        } ?>
    </div>
<?php }
?>

<script>
	// starting the script on page load
	jQuery(document).ready(function() {
		jQuery("img.image-prv").click(function(e) {
			jQuery("#image-preview").attr('src', this.src);	
		});
	});
</script>