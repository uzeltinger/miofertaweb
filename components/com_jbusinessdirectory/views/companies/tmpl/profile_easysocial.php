<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

if(JFile::exists(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php')){
	require_once( JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php' );
   
	$userId = $this->company->userId;
	$suser = Foundry::user( $userId );
	$sconfig  = Foundry::config();
	
	$avatarUrl = $suser->getAvatar('medium');

	$name =$suser->name;
	
	$options = array('id' => $name);
	$link = FRoute::profile($options);
	
	$link =  JURI::base().substr($link, 1);

	
	
?>
<div class="jbd-user-profile easysocial">
	<div class="jbd-user-image">
		<img src="<?php echo $avatarUrl?> "/>
	</div>
	<div class="jbd-user-info">
		<div class="user-name"><?php echo $name ;?></div>
		<a target="_blank" href="<?php echo $link?>"><?php echo JText::_("LNG_USER_PROFILE")?></a>
	</div>
</div>

<?php }else{ ?>
  <div>EasySocial not installed!!</div>
<?php } ?>