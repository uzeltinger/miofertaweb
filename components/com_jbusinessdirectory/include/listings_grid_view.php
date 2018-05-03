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
$enableNumbering = $appSettings->enable_numbering;
$user = JFactory::getUser();

$showData = !($user->id==0 && $appSettings->show_details_user == 1);

$grid_layout = JRequest::getVar('grid_layout');
if(!empty($grid_layout)) {
	$this->appSettings->search_result_grid_view = $grid_layout;
} ?>

<div id="jbd-grid-view" <?php echo !$this->appSettings->search_view_mode?'style="display: none"':'' ?>>
	<?php 
	if($this->appSettings->search_result_grid_view == 2) {
		require_once JPATH_COMPONENT_SITE.'/include/listings_grid_style_2.php';
	} else { 
		require_once JPATH_COMPONENT_SITE.'/include/listings_grid_style_1.php';
	} ?>
</div>