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

<div id="company-locations">
    <?php $address = JBusinessUtil::getAddressText($this->company); ?>
    <?php if (!empty($address)) { ?>
        <label><?php echo JText::_('LNG_PRIMARY_LOCATIONS'); ?></label>
        <div class="company-location" id="location">
            <?php echo $address; ?>
        </div>
        <br/>
    <?php } ?>
    <?php if(!empty($this->company->locations)){?>
        <label><?php echo JText::_('LNG_SECONDARY_LOCATIONS'); ?></label>
        <fieldset>
        	<?php foreach ($this->company->locations as $location) {
        		$location->publish_only_city = false;
        		?>
        		<div class="company-location" id="location-<?php echo $location->id ?>">
        			<?php echo (!empty($location->name) ? strtoupper($location->name) . " - " : "") . JBusinessUtil::getAddressText($location); ?>
        			<?php echo !empty($location->phone) ? "&nbsp;&nbsp;&nbsp;<i class='dir-icon-phone'></i> " . $location->phone : ''; ?>
        		</div>
        		<?php
        	} ?>
        </fieldset>
    <?php }?>
</div>
