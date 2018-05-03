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

<div>
	<?php if(!empty($this->companyContacts)){ ?>
	  <?php foreach($this->companyDepartments as $department) { ?>
	    <strong><?php echo $department->contact_department ?></strong>
	    <?php foreach($this->companyContacts as $contact) {?>
	      <?php if ($department->contact_department == $contact->contact_department && !empty($contact->contact_name)){ ?>
	        <div id="contact-person-details<?php echo $contact->id?>" class="contact-person-details">
	          <div  onclick="jQuery('#contact-person-details<?php echo $contact->id ?>').toggleClass('open')"><?php echo $this->escape($contact->contact_name); ?> (+)</div>
	          	<div class="contact-item">
		              <?php if(!empty($contact->contact_email)) { ?>
		                <div><i class="dir-icon-envelope"></i> <?php echo $this->escape($contact->contact_email); ?></div>
		              <?php }?>
		
		              <?php if(!empty($contact->contact_fax)) {?>
		              	<div><i class="dir-icon-fax"></i> <?php echo $this->escape($contact->contact_fax); ?></div>
		              <?php }?>
		
		              <?php if(!empty($contact->contact_phone)) { ?>
		              	<div><i class="dir-icon-mobile-phone"></i> <a href="tel:<?php echo $this->escape($contact->contact_phone); ?>"><?php echo $this->escape($contact->contact_phone); ?></a></div>
		           	 <?php } ?>
	           	 </div>
	        </div>
	      <?php } ?>
	    <?php }?>
	  <?php  } ?>
	<?php }?>
</div>
