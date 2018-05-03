<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/libraries/unitegallery/css/unite-gallery.css');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/libraries/unitegallery/themes/default/ug-theme-default.css');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/libraries/unitegallery/js/unitegallery.js');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/libraries/unitegallery/themes/default/ug-theme-default.js');
?>


<div id="gallery" style="display:none;">
    <?php if (!empty($this->pictures)) {
        $hasDescription = false; ?>
        <?php foreach($this->pictures as $picture) {
            if(!empty($picture->picture_info))
                $hasDescription = true; ?>

            <img alt="<?php echo $picture->picture_info ?>" src="<?php echo JURI::root().PICTURES_PATH.$picture->picture_path ?>"
                 data-image="<?php echo JURI::root().PICTURES_PATH.$picture->picture_path ?>"
                 data-description="<?php echo $picture->picture_info ?>">

        <?php } ?>
    <?php } else { ?>
        <?php echo JText::_("LNG_NO_IMAGES"); ?>
    <?php } ?>
</div>
<div style="clear:both;"></div>

			
<?php if (!empty($this->pictures)){?>	
	<script type="text/javascript">
		var unitegallery = null;
        jQuery(document).ready(function() {
    		var galleryHeight = "550";
    		containerHeight = jQuery(".style4 .company-info-container").height();
    		if(containerHeight){
    			galleryHeight = containerHeight + 315; 
    		}
        	unitegallery = jQuery("#gallery").unitegallery({
                gallery_theme: "default",
                gallery_height: galleryHeight,
            	<?php if (count($this->pictures)<=1){ ?>
					theme_hide_panel_under_width: 4000,
					slider_enable_arrows: false,
				<?php } ?>
                theme_enable_text_panel: <?php if ($hasDescription) echo 'true'; else echo 'false'; ?>,
                slider_control_zoom: false,
                slider_enable_zoom_panel: false,		
                thumb_fixed_size: false,
                gallery_autoplay: <?php if ($this->appSettings->autoplay_gallery) echo 'true'; else echo 'false'; ?>
            });
        });

       
    	     
	</script>
<?php } ?>
	
	
