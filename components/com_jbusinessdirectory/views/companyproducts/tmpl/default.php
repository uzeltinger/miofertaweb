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
if(isset($this->products) && count($this->products)) { ?>
    <div id="grid-content" class='grid4 product-list'>
        <?php $i = 0; ?>
        <span id="product-category"><h1><?php echo $this->category->name ?></h1></span>
        <?php foreach ($this->products as $product) { ?>
        <?php if($i%3==0) { ?>
            <div class="row-fluid">
        <?php } ?>
            <div id="post-<?php echo $product->id ?>" class="span4">
                <div>
                    <figure class="post-image">
                        <a href="javascript:void(0)" onclick="showProductDetails(<?php echo $product->id.', '.$this->category->id ?>)">
                            <?php if(!empty($product->picture_path) ){?>
                                <img title="<?php echo $product->subject ?>" alt="<?php echo $product->subject ?>" src="<?php echo JURI::root().PICTURES_PATH.$product->picture_path ?>">
                            <?php }else{ ?>
                                <img title="<?php echo $product->subject ?>" alt="<?php echo $product->subject ?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>">
                            <?php } ?>
                        </a>
                    </figure>

                    <div class="post-content" style="text-align:center;">
                        <a style="color:black;" href="javascript:void(0)" onclick="showProductDetails(<?php echo $product->id.', '.$this->category->id ?>)">
                            <h2 class="post-title">
                                <span><?php echo $product->subject ?></span>
                            </h2>
                            <p class="offer-dates">
                                <?php
                                echo JBusinessUtil::truncate($product->short_description, 100);
                                ?>
                            </p>
                        </a>
                    </div>
                </div>
            </div>
            <?php
            $i++;
            if($i%3==0) { ?>
                </div>
            <?php } ?>
            <?php } ?>
            <?php if($i%3!=0) { ?>
                </div>
            <?php } ?>
        </div>
        <?php
    } else{
    echo JText::_("LNG_NO_PRODUCTS");
    }
?>
<div class="clear"></div>