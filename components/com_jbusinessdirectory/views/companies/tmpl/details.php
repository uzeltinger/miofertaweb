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
<div id="dir-listing-description" class="dir-listing-description">
<?php
	if(isset($this->company->description) && $this->company->description!=''){ 
		echo JHTML::_("content.prepare", $this->company->description);
	} else {
		echo JText::_("LNG_NO_COMPANY_DETAILS");
	}
?>
</div>