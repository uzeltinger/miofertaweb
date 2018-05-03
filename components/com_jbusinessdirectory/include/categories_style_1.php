<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery-ui.js');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/jquery-ui.css');
?>

<!-- ACCORDION VIEW -->
<div id="categories-container" class="categories-accordion">
	<ul id="categories-accordion" class="categories-accordion1">
	<?php foreach($categories as $category){
		if(!is_array($category)){
			$category = array($category);
			$category["subCategories"] = array();
		}
		if(isset($category[0]->name)){		
	?>
		<li class="accordion-element">
			<div>
				<!-- div class="category-img-container">
					<img alt="" src="<?php echo JURI::root().PICTURES_PATH.$category[0]->imageLocation ?>">
				</div--> 
				<h3>
					<a href="<?php echo $category[0]->link ?>"><?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>
						<?php if($appSettings->show_total_business_count) { ?>
							<span class="numberCircle"> <?php echo $category[0]->nr_listings ?></span>
						<?php } ?>
					</a>
				</h3>
			</div>
			<div>
				<ul class="category-list">
					<?php
						$i=1; 
						foreach($category["subCategories"] as $cat){ 
					?>
					<li>
						<a class="categoryLink" title="<?php echo htmlspecialchars($cat[0]->name, ENT_QUOTES) ?>" alt="<?php echo htmlspecialchars($cat[0]->name, ENT_QUOTES) ?>"
							href="<?php echo $cat[0]->link ?>"
						>
							<?php echo htmlspecialchars($cat[0]->name, ENT_QUOTES)?>
						</a>
					</li> 
				<?php } ?>
				</ul>
			</div>
		</li>
	<?php 
		}
	} 
	?>
	</ul>
</div>
<div class="clear"></div>
<?php if(!empty($params) && $params->get('showviewall')){?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
    </div>
<?php }?>

<script>
	jQuery(document).ready(function(){
		jQuery(".categories-accordion1" ).each( function () {
			jQuery(this).accordion({
				heightStyle: "content",
				active: "false",
				event: "click hoverintent"
			});
		});
	});

</script>