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
if(!empty(($this->companyAttributes))) {
    $attributes = JBusinessUtil::arrangeAttributesByGroup($this->companyAttributes);
    $packageFeatured = isset($this->package->features)?$this->package->features:null;
    $ungrouped = array();

    if(count($attributes) > 1) { ?>
        <div class='attribute-groups-container'>
            <?php foreach($attributes as $group => $values) { ?>
                <?php if($group != 'ungrouped') {
                    $renderedContent = AttributeService::renderAttributesFront($values,$appSettings->enable_packages, $packageFeatured);
                    if(!empty($renderedContent)) { ?>
                        <div class='attribute-group'>
                            <h4><?php echo $group ?></h4>
                            <hr/>
                            <?php
                            echo $renderedContent;
                            ?>
                        </div>
                    <?php } ?>
                <?php } else {
                    $ungrouped = $values;
                } ?>
            <?php } ?>
        </div>
        <?php
        $renderedContent = AttributeService::renderAttributesFront($ungrouped,$appSettings->enable_packages, $packageFeatured);
        echo $renderedContent;
    }
    else {?>
        <div class="custom-attributes">
           <?php 
            $renderedContent = AttributeService::renderAttributesFront($attributes['ungrouped'],$appSettings->enable_packages, $packageFeatured);
            echo $renderedContent;
            ?>
        </div>
    <?php }
}
?>