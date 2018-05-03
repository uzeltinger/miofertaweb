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

<div class="row-fluid" id="product-details">
    <!-- Product Details -->
    <div class="span8">
        <span id="product-name">
            <h2><?php echo $this->product->subject ?></h2>
        </span>
        <hr/>
        <p><?php echo $this->product->description ?></p>
        <div class="clear"></div>
        <div id="offer-image-container" class="offer-image-container">
            <?php if(!empty($this->product->pictures) ){?>
                <img title="<?php echo $this->product->subject ?>" alt="<?php echo $this->product->subject ?>" src="<?php echo JURI::root().PICTURES_PATH.$this->product->pictures[0]->picture_path ?>">
            <?php }else{ ?>
                <img title="<?php echo $this->product->subject ?>" alt="<?php echo $this->product->subject ?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>">
            <?php } ?>
        </div>
        <div class="clear"></div>
        <?php $address = JBusinessUtil::getAddressText($this->product); ?>
        <?php if(!empty($address)) { ?><br/>
            <div class="offer-location">
                <span><i class="dir-icon-map-marker dir-icon-large"></i> <?php echo $address ?></span>
            </div>
        <?php } ?>
    </div>

    <!-- Module Panel -->
    <div class="span3">
        <?php
        jimport('joomla.application.module.helper');
        // this is where you want to load your module position
        $modules = JModuleHelper::getModules('dir-product');

        if(isset($modules) && count($modules)>0){
            $fullWidth = false; ?>
            <div class="dir-product">
                <?php foreach($modules as $module) {
                    echo JModuleHelper::renderModule($module);
                } ?>
            </div>
        <?php }
        ?>
    </div>
</div>
