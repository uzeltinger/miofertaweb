<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
if($user->id == 0){
	$app = JFactory::getApplication();
	$return = base64_encode(('index.php?option=com_jbusinessdirectory&view=managecompanies'));
	$app->redirect('index.php?option=com_users&view=login&return='.$return);
}

if(!$this->actions->get('directory.access.listings') && $this->appSettings->front_end_acl){
	$app = JFactory::getApplication();
	$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions',false), JText::_("LNG_ACCESS_RESTRICTED"), "warning");
}


JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.multiselect');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<style>
    .tooltip {
        border-style:none !important;
    }

    .tooltip-inner {
        background-color: rgba(0,0,0,0.35);
        max-width:600px;
        padding:3px 8px;
        text-align:center;
        border-radius:4px;
    }
</style>

<div class="button-row right">
	<?php
	if($this->appSettings->max_business > $this->total || empty($this->appSettings->max_business)) { ?>
		<button type="submit" class="ui-dir-button ui-dir-button-green" onclick="Joomla.submitbutton('managecompany.add')">
			<span class="ui-button-text"><i class="dir-icon-plus-sign"></i> <?php echo JText::_("LNG_ADD_NEW_LISTING")?></span>
		</button>
	<?php } else {
		JError::raiseNotice(100, JText::_('LNG_MAX_BUSINESS_LISTINGS_REACHED'));
	} ?>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies');?>" method="post" name="adminForm" id="adminForm">
	
	<table class="dir-table dir-panel-table" id="itemList">
		<thead>
			<tr>
				<th>
					<?php echo JText::_("LNG_NAME")?>
				</th>
                <th>
                    <?php echo JText::_("LNG_PROGRESS"); ?>
                </th>
				<?php if($this->appSettings->enable_packages){?>
					<th class="hidden-phone hidden-xs" width="10%"><?php echo JHtml::_('grid.sort', 'LNG_PACKAGE', 'ct.name', $listDirn, $listOrder); ?></th>
					<th class="hidden-phone hidden-xs" width="10%"><?php echo JHtml::_('grid.sort', 'LNG_PACKAGE_STATUS', 'ct.name', $listDirn, $listOrder); ?></th>
				<?php } ?>	
				<th class="hidden-phone hidden-xs" width="5%"><?php echo JText::_("LNG_WEBSITE_CLICKS")?></th>
				<th class="hidden-phone hidden-xs" width="5%"><?php echo JText::_("LNG_VIEW_NUMBER")?></th>
				<th class="hidden-phone hidden-xs" width="5%"><?php echo JText::_("LNG_CONTACT_NUMBER")?></th>
				<th width="5%"><?php echo JText::_("LNG_STATE")?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<a href="#" id="open_legend">
						<h5 class="right"><?php echo JText::_('LNG_STATUS_MESSAGES_LEGEND'); ?></h5>
					</a>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$nrcrt = 1;
			$i=0;
			foreach( $this->items as $company) { ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td align="left">
						<div class="row-fluid">
							<div class="item-image text-center">
								<?php 
									if (!empty($company->logoLocation)) { ?>
										<a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompany.edit&'.JSession::getFormToken().'=1&id='. $company->id ) ?>">
											<img src="<?php echo JURI::root().PICTURES_PATH.$company->logoLocation ?>" 
												class="img-circle"/>
										</a>
								<?php } else { ?>
									<a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompany.edit&'.JSession::getFormToken().'=1&id='. $company->id ) ?>">
										<img src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" 
											class="img-circle"/>
									</a>
								<?php } ?>
							</div>

							<div class="item-name text-left">
								<div class="row-fluid">
									<?php if($company->approved != COMPANY_STATUS_CLAIMED) { ?>
										<a href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managecompany.edit&'.JSession::getFormToken().'=1&id='. $company->id )?>'
											title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>"> 
											<strong><?php echo $company->name ?></strong>
										</a>
									<?php } else { ?>
										<strong><?php echo $company->name ?></strong>
									<?php } ?>
								</div>
								<div class="row-fluid">
									<?php if($company->approved != COMPANY_STATUS_CLAIMED) { ?>
										<a target="_blank" href="<?php echo JURI::base().('index.php?option=com_jbusinessdirectory&view=companies&companyId='.$company->id) ?>" 
											title="<?php echo JText::_('LNG_CLICK_TO_VIEW'); ?>" class="btn btn-xs btn-primary btn-panel"> 
											<?php echo JText::_('LNG_VIEW'); ?>
										</a>
										<a href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managecompany.edit&'.JSession::getFormToken().'=1&id='. $company->id )?>"
											title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>" class="btn btn-xs btn-success btn-panel">
											<?php echo JText::_('LNG_EDIT'); ?>
										</a>
										<a href="javascript:deleteDirListing(<?php echo $company->id ?>)" 
											title="<?php echo JText::_('LNG_CLICK_TO_DELETE'); ?>" class="btn btn-xs btn-danger btn-panel">
											<?php echo JText::_('LNG_DELETE'); ?>
										</a>
									<?php } ?>
									<?php if($company->approved == COMPANY_STATUS_APPROVED) { ?>
										<a onclick="document.location.href = '<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managecompany.changeState&id='. $company->id )?> '"
											title="<?php echo JText::_('LNG_CLICK_TO_CHANGE_STATE'); ?>"
											<?php 
											if($company->state==0) 
												echo 'class="btn btn-xs btn-info"'; 
											else 
												echo 'class="btn btn-xs btn-warning"'; 
											?>
										>
											<?php 
											if($company->state==0) 
												echo JText::_('LNG_ACTIVATE'); 
											else 
												echo JText::_('LNG_DEACTIVATE');  
											?>
										</a>
									<?php } ?>
								</div>
							</div>
						</div>
					</td>

                    <td>
                    <?php if(count($company->checklist) > 0 ) { ?>
                        <div id="<?php echo $company->id ?>"
                             class="c100 p<?php echo (int)($company->progress*100) ?> small green"
                             rel="tooltip" data-toggle="tooltip"
                             data-trigger="hover" data-placement="right" data-html="true" data-title=
                             "
                                <div>
                                    <table class='checklist'>
                                        <tbody>
                                        <?php foreach($company->checklist as $key=>$val) { ?>
                                        <tr>
                                            <td >
                                                <?php echo $val->name ?>
                                            </td>
                                            <td class='status <?php echo $val->status?'status_done':''; ?>'>
                                                <i class='dir-icon-<?php echo $val->status?'check':'exclamation'; ?>'></i>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                             ">
                            <span><?php echo $company->progress*100 ?>%</span>
                            <div class="slice">
                                <div class="bar"></div>
                                <div class="fill"></div>
                            </div>
                        </div>
                    <?php } ?>
                </td>
			
					<?php if($this->appSettings->enable_packages) { ?>
						<td class="hidden-phone hidden-xs">
							<?php echo $company->packageName ?>
						</td>
						<td class="hidden-phone hidden-xs">
							<?php echo $company->active==1?JText::_("LNG_ACTIVE")." - ":"" ?>
								<?php if($company->active==0){
        							    if($company->futureDate!=1){
        							        echo JText::_("LNG_EXPIRED")." - "; 
                                        }else {
                                            echo JText::_("LNG_NOT_STARTED")." - ";
                                        }
        							}
                            ?>
							<?php echo $company->paid==1?JText::_("LNG_PAID"):"" ?>
							<?php echo $company->paid==='0'?JText::_("LNG_NOT_PAID"):"" ?>
							<?php if($company->paid==='1' && $company->packageType!=1){ ?>
								<br/><?php echo JText::_("LNG_EXPIRATION_DATE").": ". JBusinessUtil::getDateGeneralShortFormat($company->expirationDate) ?>
							<?php }?>
						</td>
					<?php } ?>
					
					<td class="center hidden-phone hidden-xs">
						<?php echo $company->websiteCount ?>
					</td>
					<td class="center hidden-phone hidden-xs">
						<?php echo $company->viewCount ?>
					</td>
					<td class="center hidden-phone hidden-xs">
						<?php echo $company->contactCount ?>
					</td>
					<td valign="top" align="center">
						<?php
						if(($company->state == 1) && ($company->approved == COMPANY_STATUS_APPROVED)) {
							if($company->expired)
								echo '<span class="status-btn status-btn-warning warn2">'.JText::_("LNG_EXPIRED").'</span>';
							else
								echo '<span class="status-btn status-btn-success">'.JText::_("LNG_PUBLISHED").'</span>';
						} else {
							switch($company->approved) {
								case COMPANY_STATUS_DISAPPROVED:
									echo '<span class="status-btn status-btn-danger">'.JText::_("LNG_DISAPPROVED").'</span>';
									break;
								case COMPANY_STATUS_CLAIMED:
									echo '<span class="status-btn status-btn-warning">'.JText::_("LNG_CLAIM_PENDING").'</span>';
									break;
								case COMPANY_STATUS_CREATED:
									echo '<span class="status-btn status-btn-info">'.JText::_("LNG_PENDING").'</span>';
									break;
								case COMPANY_STATUS_APPROVED:
									echo '<span class="status-btn status-btn-primary">'.JText::_("LNG_DEACTIVATED").'</span>';
									break;
							}
						} ?>
					</td>
				</tr>
			<?php
			$i++;
			} ?>
		</tbody>
	</table>
	<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
		<?php echo $this->pagination->getListFooter(); ?>
		<div class="clear"></div>
	</div>
	<input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
	<input type="hidden" name="task" id="task" value="" /> 
	<input type="hidden" name="companyId" value="" />
	<input type="hidden" id="cid" name="cid" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHTML::_('form.token'); ?> 
</form>

<!-- Modal -->
<div id="legend" style="display:none;">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span title="Cancel" class="dialogCloseButton" onclick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		<div class="dialogContent">
			<div class="row-fluid">
				<div class="row-fluid">
					<div class="span10 offset1">
						<dl class="dl-horizontal">
							<dt><span class="status-btn status-btn-success"><?php echo JText::_('LNG_PUBLISHED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_PUBLISHED_LEGEND'); ?></dd>
							<dt><span class="status-btn status-btn-primary"><?php echo JText::_('LNG_DEACTIVATED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_DEACTIVATED_LEGEND'); ?></dd>
							<dt><span class="status-btn status-btn-info"><?php echo JText::_('LNG_PENDING'); ?></span></dt>
							<dd><?php echo JText::_('LNG_PENDING_LEGEND'); ?></dd>
							<?php if($this->appSettings->enable_packages){?>
								<dt><span class="status-btn status-btn-warning"><?php echo JText::_('LNG_EXPIRED'); ?></span></dt>
								<dd><?php echo JText::_('LNG_EXPIRED_LEGEND'); ?></dd>
							<?php } ?>
							<dt><span class="status-btn status-btn-warning warn"><?php echo JText::_('LNG_CLAIM_PENDING'); ?></span></dt>
							<dd><?php echo JText::_('LNG_CLAIM_PENDING_LEGEND'); ?></dd>
							<dt><span class="status-btn status-btn-danger"><?php echo JText::_('LNG_DISAPPROVED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_DISAPPROVED_LEGEND'); ?></dd>
						</dl>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function() {
		jQuery('#open_legend').click(function() {
			jQuery.blockUI({ message: jQuery('#legend'), css: {width: 'auto', top: '25%', left:"0", position:"absolute", cursor:'default'} });
			jQuery('.blockUI.blockMsg').center();
			jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI);
			jQuery(document).scrollTop( jQuery("#legend").offset().top );
			jQuery("html, body").animate({ scrollTop: 0}, "slow");

			!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
		});

        jQuery('[rel="tooltip"]').tooltip();
	});
</script>