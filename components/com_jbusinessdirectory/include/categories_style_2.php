<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
?>

<!-- BOXES VIEW -->
<div id="categories-container" class="categories-container">
	<div class="clear"></div>
	<div class="row-fluid">
	<?php $k = 0;?>
	<?php foreach($categories as $category){
	if(!is_array($category)){
		$category = array($category);
		$category["subCategories"] = array();
	}
		if(isset($category[0]->name)){	
			$k= $k+1;
			
	?>
		<div class="category-content span4">
			<?php if(!empty($category[0]->imageLocation)){ ?>
				<div class="category-img-container">
					<img alt="" src="<?php echo JURI::root().PICTURES_PATH.$category[0]->imageLocation ?>">
				</div>
			<?php } ?>
			<h2>
				<a href="<?php echo $category[0]->link ?>"> <?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>
					<?php if($appSettings->show_total_business_count) { ?>
						<span class="numberCircle"> <?php echo $category[0]->nr_listings ?></span>
					<?php } ?>
				</a>
			</h2>
			<?php
				$i=1; 
				foreach($category["subCategories"] as $cat){ 
					if($i>20)
						break;
					echo $i++==1?'':'|';
			?>
				
				<a class="categoryLink" title="<?php echo htmlspecialchars($cat[0]->name, ENT_QUOTES)?>" alt="<?php echo htmlspecialchars($cat[0]->name, ENT_QUOTES)?>"
					href="<?php echo $cat[0]->link ?>"
				>
					<?php echo htmlspecialchars($cat[0]->name, ENT_QUOTES)?>
				</a>
			<?php } ?>
			<div class="clear"></div>
		</div>
		
		<?php if($k%3==0){?>
			</div>
			<div class="row-fluid">
		<?php }?>
		
	<?php 
		}
	} 
	?>
	</div>
</div>
<div class="clear"></div>
<?php if(!empty($params) && $params->get('showviewall')){?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
    </div>
<?php }?>