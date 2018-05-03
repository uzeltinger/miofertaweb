
<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

$user = JFactory::getUser();

if($user->id == 0){
	$app = JFactory::getApplication();
	$return = base64_encode(('index.php?option=com_jbusinessdirectory&view=managecompanyoffercoupons'));
	$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return,false));
}

if(!$this->actions->get('directory.access.offers') && $this->appSettings->front_end_acl){
	$app = JFactory::getApplication();
	$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions',false), JText::_("LNG_ACCESS_RESTRICTED"), "warning");
}

$isProfile = true;

?>
<script>
	var isProfile = true;
</script>
<style>
#header-box, #control-panel-link {
	display: none;
}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffercoupons');?>" method="post" name="adminForm" id="adminForm">
	<div id="editcell">
		<table class="dir-table dir-panel-table">
			<thead>
				<tr>
					<th align='center'><?php echo JText::_('LNG_COUPON'); ?></th>
					<th class="hidden-xs hidden-phone" width='10%' align='center'><?php echo JText::_('LNG_OFFER'); ?></th>
					<th class="hidden-xs hidden-phone" width='10%' align='center'><?php echo JText::_('LNG_COMPANY'); ?></th>
					<th class="hidden-xs hidden-phone" width='10%' align='center'><?php echo JText::_('LNG_GENERATED_TIME'); ?></th>
					<th class="hidden-xs hidden-phone" width='10%' align='center'><?php echo JText::_('LNG_EXPIRATION_TIME'); ?></th>
					<th width='10%'><?php echo JText::_("LNG_PDF"); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$nrcrt = 1;
				if(!empty($this->items)){
					foreach($this->items as $coupon) { ?>
						<tr class="row<?php echo $nrcrt%2 ?>">
							<td align="left">
								<div class="row-fluid">
									<div class="item-image text-left">
										<b><?php echo strtoupper($coupon->code); ?></b>
									</div>
								</div>
								<div class="row-fluid">
									<a href="javascript:void(0);" onclick="deleteCoupon(<?php echo $coupon->id ?>)" 
										title="<?php echo JText::_('LNG_CLICK_TO_DELETE'); ?>" class="btn btn-xs btn-danger btn-panel">
										<?php echo JText::_("LNG_DELETE")?>
									</a>
								</div>
							</td>
							<td class="hidden-xs hidden-phone">
								<div class="item-image text-left">
									<?php echo $coupon->offer; ?>
								</div>
							</td>
							<td class="hidden-xs hidden-phone">
								<div class="item-name text-left">
									<?php echo $coupon->company; ?>
								</div>
							</td>
							<td class="hidden-xs hidden-phone">
								<div class="item-name text-left">
									<?php echo JBusinessUtil::getDateGeneralFormat($coupon->generated_time); ?>
								</div>
							</td>
							<td class="hidden-xs hidden-phone">
								<div class="item-name text-left">
									<?php echo JBusinessUtil::getDateGeneralFormat($coupon->expiration_time); ?>
								</div>
							</td>
							<td>
								<div class="item-name text-left">
									<a href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managecompanyoffercoupon.show&id='. $coupon->id )?>'
										title="<?php echo JText::_('LNG_CLICK_TO_VIEW'); ?>" class="btn btn-xs btn-primary btn-panel" 
										target="_blank">
										<?php echo JText::_("LNG_VIEW")?>
									</a>
								</div>
							</td>
						</tr>
				<?php } 
					}
				?>
			</tbody>
		</table>
        <?php
        if(!empty($this->items)){ ?>
        <div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
            <?php echo $this->pagination->getListFooter(); ?>
            <div class="clear"></div>
        </div>
        <?php } ?>
	</div>
	<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
	<input type="hidden" name="task" id="task" value="" /> 
	<input type="hidden" name="id" id="id" value="" />
	<input type="hidden" name="Itemid" id="Itemid" />
	<?php echo JHTML::_('form.token'); ?> 
</form>
<div class="clear"></div>