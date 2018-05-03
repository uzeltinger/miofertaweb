<div class="attachments">
    <ul>
        <?php foreach($this->event->attachments as $attachment) {?>
            <li>
                <?php if (!empty($attachment)){?>
                    <div class="attachment-info">
                        <a href="<?php echo JURI::root() . "/" . ATTACHMENT_PATH . $attachment->path ?>" target="_blank">
                            <img class="icon" src="<?php echo $attachment->properties->icon; ?>"/>
                            <div class="truncate-text"><?php echo !empty($attachment->name) ? $this->escape($attachment->name) : basename($attachment->path) ?></div>
                        </a>
                        <span><?php echo "[" . strtolower($attachment->properties->fileProperties['extension']) . ", ".(!empty($attachment->properties->nrPages)?$attachment->properties->nrPages." ".JText::_("LNG_PAGES").", ":"").$attachment->properties->size; ?>] </span>
                    </div>
                <?php } ?>
            </li>
        <?php }?>
    </ul>
</div>