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

<div class='sounds-container'>
	<?php 
	if(!empty($this->sounds)){
		foreach( $this->sounds as $sound ){
			if(!empty($sound->url))	{ ?>
				<?php echo ($sound->url) ?>
			<?php
			}
		}
	}?>
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