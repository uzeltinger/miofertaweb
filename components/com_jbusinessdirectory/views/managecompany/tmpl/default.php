<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

$isProfile = true;
$showSteps =  JRequest::getVar("showSteps",false);

$user = JFactory::getUser();
if(($user->id == 0 && $this->appSettings->allow_user_creation==0)){
	$app = JFactory::getApplication();
	$return = base64_encode('index.php?option=com_jbusinessdirectory&view=managecompany');
	$app->redirect('index.php?option=com_users&view=login&return='.$return);
}

if(!$this->actions->get('directory.access.listings') && $this->appSettings->front_end_acl){
	$app = JFactory::getApplication();
	$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions',false), JText::_("LNG_ACCESS_RESTRICTED"), "warning");
}

if($this->item->approved == COMPANY_STATUS_CLAIMED){
	$app = JFactory::getApplication();
	$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies'));
}

if(isset($this->item) && $this->item->approved == COMPANY_STATUS_CLAIMED){
	echo "<h3>".JText::_("LNG_COMPANY_NEED_CLAIM_APPROVAL")."</h3>";
}
?>


<?php 
if($showSteps) { ?>
	<div id="process-container" class="process-container">
		<ol class="process-steps">
			<li class="is-complete dir-icon-inbox" data-step="1">
				<p><?php echo JText::_("LNG_CHOOSE_PACKAGE")?></p>
			</li>
			<li class="is-complete dir-icon-user" data-step="2">
				<p><?php echo JText::_("LNG_BASIC_INFO")?></p>
			</li>
			<li class="progress__last is-active dir-icon-file-text-o" data-step="3">
				<p><?php echo JText::_("LNG_LISTING_INFO")?></p>
			</li>
		</ol>
		<div class="clear"></div>
	</div>
	
	<div class="row-fluid">
	<div class="span9"> 
		<?php
			include(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'company'.DS.'tmpl'.DS.'edit.php');
		?>
	</div>
	<div class="span3">
		<?php if(!empty($this->package)){?>
			<div class="featured-product-col" >
				<?php
					$package = $this->package;
					require  JPATH_COMPONENT_SITE."/views/packages/tmpl/default_package.php"
				?>
			</div>
		<?php }?>
	</div>
</div>
	
<?php }else{ ?>
	<?php include(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'company'.DS.'tmpl'.DS.'edit.php'); ?>
<?php } ?>


<script>
	var isProfile = true;
</script>

<style>
#header-box, #control-panel-link{
	display: none;
}
</style>