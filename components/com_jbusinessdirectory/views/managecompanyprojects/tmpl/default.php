
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
    $return = base64_encode(('index.php?option=com_jbusinessdirectory&view=managecompanyprojects'));
    $app->redirect(JRoute::_('index.php?option=com_users&return='.$return,false));
}

if(!$this->actions->get('directory.access.listings') && $this->appSettings->front_end_acl){
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

<div class="button-row right">
        <button type="submit" class="ui-dir-button ui-dir-button-green" onclick="addProject()">
            <span class="ui-button-text"><i class="dir-icon-plus-sign"></i> <?php echo JText::_("LNG_ADD_NEW_PROJECT")?></span>
        </button>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyprojects');?>" method="post" name="adminForm" id="adminForm">
    <div id="editcell">
        <table class="dir-table dir-panel-table">
            <thead>
            <tr>
                <th class="hidden-xs hidden-phone" width='10%' align='center'><?php echo JText::_('LNG_NAME'); ?></th>
                <th class="hidden-xs hidden-phone" width='10%' align='center'><?php echo JText::_('LNG_COMPANY'); ?></th>
                <th class="hidden-xs hidden-phone" width='10%' align='center'><?php echo JText::_('LNG_DESCRIPTION'); ?></th>
                <th class="hidden-xs hidden-phone" width='5%'  ><?php echo JText::_("LNG_STATUS")?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $nrcrt = 1;
            if(!empty($this->items)){
                foreach($this->items as $project) { ?>
                    <tr class="row<?php echo $nrcrt%2 ?>">
                        <td align="left">
                            <div class="row-fluid">
                                <div class="item-image text-center">
                                    <?php if (!empty($project->picture_path)) { ?>
                                        <a href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managecompanyproject.edit&'.JSession::getFormToken().'=1&id='. $project->id )?>'>
                                            <img
                                                    src="<?php echo JURI::root().PICTURES_PATH.$project->picture_path ?>"
                                                    class="img-circle"
                                            />
                                        </a>
                                    <?php } else { ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompanyproject.edit&'.JSession::getFormToken().'=1&id='. $project->id ) ?>">
                                            <img
                                                    src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>"
                                                    class="img-circle"
                                            />
                                        </a>
                                    <?php } ?>
                                </div>
                                <div class="item-name text-left">
                                    <div class="row-fluid">
                                        <a href='<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompanyproject.edit&'.JSession::getFormToken().'=1&id='.$project->id )?>'
                                           title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>">
                                            <B><?php echo $project->name?></B>
                                        </a>
                                    </div>

                                    <div class="row-fluid">
                                        <a href="javascript:void(0);" onclick="editProject(<?php echo $project->id ?>)"
                                           title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>" class="btn btn-xs btn-success btn-panel">
                                            <?php echo JText::_("LNG_EDIT") ?>
                                        </a>
                                        <a href="javascript:void(0);" onclick="deleteProject(<?php echo $project->id ?>)"
                                           title="<?php echo JText::_('LNG_CLICK_TO_DELETE'); ?>" class="btn btn-xs btn-danger btn-panel">
                                            <?php echo JText::_("LNG_DELETE")?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="hidden-xs hidden-phone">
                            <?php echo $project->companyName?>
                        </td>
                        <td class="hidden-xs hidden-phone">
                            <?php echo (strlen($project->description)>80)?substr($project->description,0,80)."...":$project->description; ?>
                        </td>
                        <td class="hidden-xs hidden-phone" align="center">
                            <?php
                            switch($project->status) {
                                case 1:
                                    echo '<span class="status-btn status-btn-primary">'.JText::_("LNG_PUBLISHED").'</span>';
                                    break;
                                case 0:
                                    echo '<span class="status-btn status-btn-dangerS">'.JText::_("LNG_UNPUBLISHED").'</span>';
                                    break;
                            }
                            ?>
                        </td>
                    </tr>
                <?php }
            }
            ?>
            </tbody>
        </table>
        <div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
            <?php echo $this->pagination->getListFooter(); ?>
            <div class="clear"></div>
        </div>
    </div>
    <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
    <input type="hidden" name="task" id="task" value="" />
    <input type="hidden" name="id" id="id" value="" />
    <input type="hidden" name="Itemid" id="Itemid" />
    <?php echo JHTML::_('form.token'); ?>
</form>
<div class="clear"></div>
