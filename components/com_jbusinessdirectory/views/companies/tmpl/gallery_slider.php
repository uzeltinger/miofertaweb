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

<?php if(!empty($this->pictures)){?>	
<div class="slidergallery" id="slidergallery" style="width:auto">
	<div id="pageContainer">
		<div id="slideshow">
	   		<div id="slidesContainer">
	      		<div class="slide-dir">
	      			<ul class="gallery">
						<?php 
							$index = 1;
							$totalItems = count($this->pictures); 
						?>
						<?php foreach( $this->pictures as $picture ){ ?>
							<li>
								<a href="<?php echo JURI::root().PICTURES_PATH.$picture->picture_path ?>" rel="prettyPhoto[pp_gal]" title="<?php echo $this->escape($picture->picture_info) ?>">
									<img itemprop="image" src="<?php echo JURI::root().PICTURES_PATH.$picture->picture_path ?>" alt="<?php echo $this->escape($picture->picture_info) ?>" />
								</a>
							</li>
							
						<?php } ?>
					</ul>
			
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<script>
    jQuery(document).ready(function() {
        magnifyImages('gallery');
    });
</script>