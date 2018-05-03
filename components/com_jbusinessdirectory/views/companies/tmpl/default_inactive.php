<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
require_once 'header.php';
?>

<?php 
require_once 'breadcrumbs.php';
?>

<div class="company-name">
	<span style="display: none">
		<?php echo isset($this->company->comercialName) ? $this->escape($this->company->comercialName) : ""; ?>
	</span>
	<span>
		<?php echo isset($this->company->name) ? $this->escape($this->company->name) : "" ; ?>
	</span>
</div>
<div class="clear"></div>
<div>
	<?php echo JText::_("LNG_COMPANY_INACTIVE")?>
</div>