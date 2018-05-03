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

<?php if(($showData && isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
						&& ((!empty($this->company->linkedin) || !empty($this->company->youtube) ||!empty($this->company->facebook) || !empty($this->company->twitter) 
						    || !empty($this->company->googlep) || !empty($this->company->linkedin) || !empty($this->company->skype) || !empty($this->company->instagram) || !empty($this->company->pinterest || !empty($this->company->whatsapp))))){ ?> 
	<div id="social-networks-container">
		<ul class="socials-network">
			<li>
				<span class="social-networks-follow"><?php echo JText::_("LNG_FOLLOW_US")?>: &nbsp;</span>
			</li>
			<?php if(!empty($this->company->facebook)){ ?>
			<li >
				<a title="Follow us on Facebook" target="_blank" class="share-social  dir-icon-facebook" href="<?php echo $this->escape($this->company->facebook) ?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->twitter)){ ?>
			<li >
				<a title="Follow us on Twitter" target="_blank" class="share-social  dir-icon-twitter" href="<?php echo $this->escape($this->company->twitter) ?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->googlep)){ ?>
			<li >
				<a title="Follow us on Google" target="_blank" class="share-social  dir-icon-google" href="<?php echo $this->escape($this->company->googlep) ?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->linkedin)){ ?>
			<li >
				<a title="Follow us on LinkedIn" target="_blank" class="share-social  dir-icon-linkedin" href="<?php echo $this->escape($this->company->linkedin)?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->skype)){ ?>
			<li >
				<a title="Skype" target="_blank" class="share-social  dir-icon-skype" href="skype:<?php echo $this->escape($this->company->skype)?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->youtube)){ ?>
			<li >
				<a title="Follow us on YouTube" target="_blank" class="share-social  dir-icon-youtube" href="<?php echo $this->escape($this->company->youtube)?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->instagram)){ ?>
			<li >
				<a title="Follow us on Instagram" target="_blank" class="share-social  dir-icon-instagram" href="<?php echo $this->escape($this->company->instagram)?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->pinterest)){ ?>
			<li >
				<a title="Follow us on Pinterest" target="_blank" class="share-social  dir-icon-pinterest" href="<?php echo $this->escape($this->company->pinterest)?>"></a>
			</li>
			<?php } ?>
            <?php if(!empty($this->company->whatsapp)){ ?>
                <li >
                    <a title="Ping us on WhatsApp" target="_blank" class="share-social dir-icon-whatsapp" href="whatsapp://send?text=<?php echo JText::_("LNG_HELLO")?>!&phone=<?php echo $this->company->whatsapp?>"></a>
                </li>
            <?php } ?>
		</ul>
		<div class="clear"></div>
	</div>
<?php } ?>