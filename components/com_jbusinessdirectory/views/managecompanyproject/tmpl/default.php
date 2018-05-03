
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
    $return = base64_encode(('index.php?option=com_jbusinessdirectory&view=managecompanyproject'));
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
<?php

include(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'project'.DS.'tmpl'.DS.'edit.php');

?>
<div class="clear"></div>
