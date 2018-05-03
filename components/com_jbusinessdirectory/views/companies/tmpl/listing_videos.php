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

<div class='video-container row-fluid'>
	<?php 
	if(!empty($this->videos)){
	    $index = 0;
		foreach( $this->videos as $video ){
			if(!empty($video->url))	{ 
			    $index++;
			    ?>
    			<div class="span6">
    				<a class="popup-video" href="<?php echo $this->escape($video->url) ?>">
    					<div class="videoSitesLoader-holder">
    						<div class="play_btn"></div>
    						<div class="videoSitesLoader" style="background-image:url('<?php echo $video->videoThumbnail ?>')"></div>
    					</div>
    				</a>
    			</div>
			   <?php if ($index % 2 == 0){ ?>
                	</div>
              	    <div class='video-container row-fluid'>
                <?php
                    }
			}
		}
	} else {
		echo JText::_("LNG_NO_COMPANY_VIDEO");
	} ?>
	<div class="clear"></div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.popup-video').magnificPopup({
			disableOn: 200,
			type: 'iframe',
			mainClass: 'mfp-fade',
			removalDelay: 160,
			preloader: false,
			fixedContentPos: false,
			mainClass: 'mfp-fade'
		});
	});
</script>