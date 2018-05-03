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

<?php if(!empty($this->companyContacts)) { ?>
	<?php foreach($this->companyContacts as $contact) { ?>
		<?php if(!empty($contact->contact_name)){ ?>
			<div id="contact-person-details<?php echo $contact->id?>" class="contact-person-details">
	    		<div class="contact-person-name" onclick="jQuery('#contact-person-details<?php echo $contact->id ?>').toggleClass('open')"><?php echo $contact->contact_name?> (+)</div>
	    		<ul>
				    <?php if(!empty($contact->contact_email)) { ?>
				        <i class="dir-icon-envelope"></i> <?php echo $this->escape($contact->contact_email); ?>
				        <br/>
				    <?php }?>
		   			 <?php if(!empty($contact->contact_phone)) { ?>
					     <i class="dir-icon-mobile-phone"></i> <a href="tel:<?php echo $this->escape($contact->contact_phone); ?>"><?php echo $this->escape($contact->contact_phone); ?></a>
					     <br/>
			    	<?php } ?>
				    <?php if(!empty($contact->contact_fax)) {?>
				        <i class="dir-icon-fax"></i> <?php echo $this->escape($contact->contact_fax) ?>
				        <br/>
				    <?php }?>
		        </ul>
	     	</div>
	     <?php } ?>
	<?php } ?>
<?php } ?>