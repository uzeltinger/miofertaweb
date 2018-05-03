<?php /*------------------------------------------------------------------------
# JBusinessDirectory
# author CMSJunkie
# copyright Copyright (C) 2012 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/j-businessdirectory/?p=1
-------------------------------------------------------------------------*/

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
    }  ?>
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