<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
$session = JFactory::getSession();
$searchPerformed = $session->get("listing-search");
$searchType = $session->get("searchType");
$menuId = $session->get("menuItemId");
?>

<div id="search-path">
	<ul>
	<?php if(!isset($searchPerformed) || !isset($searchType)){?>
			<li><?php echo JText::_("LNG_YOU_ARE_HERE")?>:</li>
			<?php if(isset($this->category)){ ?>
			<li>
				<a class="search-filter-elem" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&controller=search&view=search') ?>"><?php echo JText::_('LNG_ALL_CATEGORIES') ?></a>
			</li>
			<?php } ?>
			<?php 
				if(!empty($this->company->path)){
				foreach($this->company->path as $path) {
					if(empty($path))
						continue;
				?>
				<li>
					<a  class="search-filter-elem" href="<?php echo JBusinessUtil::getCategoryLink($path->id, $path->alias) ?>"><?php echo $path->name?></a>
				</li>
			<?php }
				} 
			?>
			<li>
				<?php echo $this->company->name ?>
			</li>
		<?php }else{ ?>
			<li>
				<?php if($searchType == 2){?>
					<a href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=catalog&Itemid=".$menuId)?>"><?php echo JText::_("LNG_BACK_TO_SEARCH_RESULTS")?></a>
				<?php }else{?>
					<a href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=search&Itemid=".$menuId)?>"><?php echo JText::_("LNG_BACK_TO_SEARCH_RESULTS")?></a>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
</div>