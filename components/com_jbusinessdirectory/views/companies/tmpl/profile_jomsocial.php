<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

if(JFile::exists(JPATH_ADMINISTRATOR . '/components/com_community/libraries/core.php')){
    include_once JPATH_ROOT.'/components/com_community/libraries/core.php';
    include_once JPATH_ROOT.'/components/com_community/libraries/messaging.php';
    // Add a onclick action to any link to send a message
    // Here, we assume $usrid contain the id of the user we want to send message to
    
	$userId = $this->company->userId;
	$cuser = CFactory::getUser($userId);
	$avatarUrl = $cuser->getThumbAvatar();
	//$avatarUrl = $user->getAvatar();
	$coverUrl = $cuser->getCover();
	$name = $cuser->getDisplayName();
	$onclick = CMessaging::getPopup($userId);
        $addbuddy = "joms.api.friendAdd($userId)";
	$link = CRoute::_('index.php?option=com_community&view=profile&userid='.$userId);
?>
	<div class="jbd-user-profile jomsocial">
		<div class="joms-hcard__cover" style="height: 150px;">
			<img src="<?php echo $coverUrl; ?>" alt="<?php echo $this->escape($name); ?>">
	             <div class="joms-hcard__info">
	                    <div class="joms-avatar">
	                     <a style="color: #fff;" title="<?php echo $this->escape($name); ?>" target="_blank" href="<?php echo $link?>"><img src="<?php echo $this->escape($avatarUrl); ?>" alt="<?php echo $this->escape($name); ?>"></a></h3>
	                    </div>
	                    <div class="joms-hcard__info-content">
	                        <h3 class="reset-gap"><a style="color: #fff;" title="<?php echo $this->escape($name); ?>" target="_blank" href="<?php echo $link?>"><?php echo $this->escape($name); ?></a></h3>
	                        
	                  	  <div class="jbd-user-info">		
				          	  <a class="hidden-sm hidden-xs ui-dir-button" href="#" onclick="<?php echo $addbuddy?>"><?php echo JText::_("COM_COMMUNITY_FRIENDS_ADD_BUTTON")?></a>				
			                    <a style="color: #fff;float: right;" title="<?php echo $this->escape($name); ?>" target="_blank" href="<?php echo $link?>"><?php echo JText::_("LNG_USER_PROFILE")?></a>
				            </div>
	                    </div>
	             </div>
		</div>
		<div class="jbd-user-info" style="margin-top:5px;">
			<a class="ui-dir-button" style="width: 100%;"  href="#" onclick="<?php echo $onclick?>"><?php echo JText::_("LNG_SEND_MESSAGE")?></a>
		</div>
	</div>
<?php }else{ ?>
  <div>Jomsocial not installed!!</div>
<?php } ?>