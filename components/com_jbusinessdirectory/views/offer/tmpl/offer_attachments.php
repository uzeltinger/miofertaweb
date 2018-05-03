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

<div class="attachments">
  <ul>
    <?php foreach($this->offer->attachments as $attachment) {?>
      <li>
        <?php if (!empty($attachment)){?>
          <div class="attachment-info">
           <a href="<?php echo JURI::root() . "/" . ATTACHMENT_PATH . $attachment->path ?>" target="_blank">
              <img class="icon" src="<?php echo $attachment->properties->icon; ?>"/>
              <div class="truncate-text"><?php echo !empty($attachment->name) ? $this->escape($attachment->name) : $this->escape(basename($attachment->path)) ?></div>
           </a>
           <span><?php echo "[" . strtolower($attachment->properties->fileProperties['extension']) . ", ".(!empty($attachment->properties->nrPages)?$attachment->properties->nrPages." ".JText::_("LNG_PAGES").", ":"").$attachment->properties->size; ?>] </span>
          </div>
        <?php } ?>
      </li>
    <?php }?>
  </ul>
</div>