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
	$return = base64_encode(('index.php?option=com_jbusinessdirectory&view=managebookmarks'));
	$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return,false));
}

if(!$this->actions->get('directory.access.bookmarks') && $this->appSettings->front_end_acl){
	$app = JFactory::getApplication();
	$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions',false), JText::_("LNG_ACCESS_RESTRICTED"), "warning");
}

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.multiselect');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<a href="javascript:exportBookmarks()"
   title="<?php echo JText::_('LNG_CLICK_TO_EXPORT'); ?>" class="export_csv btn btn-xs btn-primary btn-panel right" style="margin:5px;">
	<?php echo JText::_("LNG_EXPORT")?>
</a>

<a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks&task=managebookmarks.generatePDF') ?>" target="_blank" class="export_pdf btn btn-xs btn-success btn-panel right"
   title="<?php echo JText::_('LNG_CLICK_TO_GENERATE_PDF'); ?>" style="margin:5px;">
	<?php echo JText::_("LNG_SAVE_AS_PDF")?>
</a>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks');?>" method="post" name="adminForm" id="adminForm">
	<table class="dir-table dir-panel-table" id="itemList">
		<thead>
			<tr>
                <th align="left">#</th>
                <th align="left" width="25%"><?php echo JText::_("LNG_BOOKMARK")?></th>
				<th align="left" width="10%"><?php echo JText::_("LNG_TYPE")?></th>
				<th align="left" width="65%"><?php echo JText::_("LNG_NOTE")?></th>
			</tr>
		</thead>
		<tbody class="sorted_table" id="tableBody">
			<?php foreach( $this->items as $bookmark){?>
                <?php
                if($bookmark->item_type == BOOKMARK_TYPE_BUSINESS) {
                    $bookmark->itemName = !empty($bookmark->name) ? $bookmark->name : JText::_('LNG_ITEM_REMOVED');
                    $bookmark->link = !empty($bookmark->name) ? JBusinessUtil::getCompanyLink($bookmark) : '';
                }
                else if($bookmark->item_type == BOOKMARK_TYPE_OFFER) {
                    $bookmark->itemName = !empty($bookmark->subject) ? $bookmark->subject : JText::_('LNG_ITEM_REMOVED');
                    $bookmark->link = !empty($bookmark->subject) ? JBusinessUtil::getOfferLink($bookmark->offerId, $bookmark->offerAlias) : '';
                }
                ?>
				<tr id="<?php echo $bookmark->bookmarkId ?>" class="orderedRow">
                    <td style="cursor: move;">
                        <span class="sortable-handler" title="<?php echo JText::_('LNG_REORDER_BOOKMARKS'); ?>">
							<i class="icon-menu"></i>
                        </span>
                    </td>
					<td align="left">
						<div class="row-fluid">
							<div class="span12">
								<a target="_blank" href="<?php echo $bookmark->link?>">
									<?php echo $bookmark->itemName ?>
								</a>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span12">
								<a href="javascript:deleteBookmark(<?php echo $bookmark->bookmarkId ?>)"
									title="<?php echo JText::_('LNG_CLICK_TO_DELETE'); ?>" class="btn btn-xs btn-danger btn-panel">
									<?php echo JText::_("LNG_DELETE")?>
								</a>
							</div>
						</div>
					</td>
                    <td>
                        <?php
                        if($bookmark->item_type == BOOKMARK_TYPE_BUSINESS)
                            echo JText::_('LNG_COMPANY');
                        else if($bookmark->item_type == BOOKMARK_TYPE_OFFER)
                            echo JText::_('LNG_OFFER');
                        ?>
                    </td>
					<td>
						<span><?php echo $bookmark->note ?></span>
					</td>
				</tr>
			<?php }	?>
		</tbody>
	</table>
	<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
		<?php echo $this->pagination->getListFooter(); ?>
		<div class="clear"></div>
	</div>

	<input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
	<input type="hidden" name="task" id="task" value="" /> 
	<input type="hidden" name="bookmarkId" value="" />
	<input type="hidden" id="cid" name="cid" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHTML::_('form.token'); ?> 
</form>

<script>
	function deleteBookmark(id) {
		if(confirm("<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true);?>")){
			jQuery("#cid").val(id);
			jQuery("#task").val("managebookmarks.delete");
			jQuery("#adminForm").submit();
		}
	}

    function exportBookmarks(id) {
        jQuery("#cid").val(id);
        jQuery("#task").val("managebookmarks.exportBookmarks");
        jQuery("#adminForm").submit();
    }

    jQuery(document).ready(function () {
        jQuery('.sorted_table').sortable({
            items: "tr",
            cursor: 'move',
            opacity: 0.6,
            //handle: '.sortable-handler',
            update: function() {
                sendOrderToServer();
            }
        });
    });

    function sendOrderToServer() {
        var newOrder = [];
        jQuery('#itemList .orderedRow').each(function(){
            newOrder.push(this.id);
        });

        var url = jbdUtils.siteRoot+'/index.php?option='+jbdUtils.componentName+'&task=managebookmarks.reOrderList';
        jQuery.ajax({
            type:"POST",
            url: url,
            data: {
                newOrder: newOrder
            },
            dataType: 'json'
        });
    }
</script>