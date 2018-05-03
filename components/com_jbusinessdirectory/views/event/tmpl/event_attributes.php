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

<?php
if(!empty($this->eventAttributes)) {
    $attributes = JBusinessUtil::arrangeAttributesByGroup($this->eventAttributes);
    $ungrouped = array();

    if(count($attributes) > 1) { ?>
        <div class='attribute-groups-container'>
            <?php foreach($attributes as $group => $values) { ?>
                <?php if($group != 'ungrouped') { ?>
                    <div class='attribute-group'>
                        <h4><?php echo $group ?></h4>
                        <hr/>
                        <?php
                        $renderedContent = AttributeService::renderAttributesFront($values, false, array());
                        echo $renderedContent;
                        ?>
                    </div>
                <?php } else {
                    $ungrouped = $values;
                } ?>
            <?php } ?>
        </div>
        <?php
        $renderedContent = AttributeService::renderAttributesFront($ungrouped, false, array());
        echo $renderedContent;
    }
    else {
        $renderedContent = AttributeService::renderAttributesFront($attributes['ungrouped'], false, array());
        echo $renderedContent;
    }
}
?>