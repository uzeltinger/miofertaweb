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
if(isset($this->productCategories) && count($this->productCategories)) { ?>
    <div id="search-path">
        <ul>
            <li><?php echo JText::_("LNG_YOU_ARE_HERE")?>:</li>
            <li id="all-categories">
                <a href="javascript:void(0);" onclick="goBack()"><?php echo JText::_("LNG_ALL_CATEGORIES"); ?></a>
                &raquo;
            </li>
            <li id="sub-categories">
            </li>
            <li id="category-products">
            </li>
            <li id="product-details">
            </li>
        </ul>
    </div>
    <div class="clear"></div>
    <div id="grid-content" class='categories-level-1'>
        <?php $i = 0; ?>
        <?php foreach ($this->productCategories[1] as $category) {
            ?>
            <?php if($i%3==0) { ?>
                <div class="row-fluid">
            <?php } ?>
                    <div id="post-<?php echo $category->id ?>" class="span4 product-image">
                        <div>
                            <figure class="post-image">
                                <a href="javascript:void(0)" onclick="showProductCategories(<?php echo $category->id ?>)">
                                    <?php if(!empty($category->imageLocation) ){?>
                                        <img title="<?php echo $category->name?>" alt="<?php echo $category->name?>" src="<?php echo JURI::root().PICTURES_PATH.$category->imageLocation ?>">
                                    <?php }else{ ?>
                                        <img title="<?php echo $category->name?>" alt="<?php echo $category->name?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>">
                                    <?php } ?>
                                </a>
                            </figure>

                            <div class="post-content" style="text-align:center;">
                                <a style="color:black;" href="javascript:void(0)" onclick="showProductCategories(<?php echo $category->id ?>)">
                                    <strong class="post-title">
                                        <span><?php echo $category->name ?></span>
                                    </strong>
                                    <p class="offer-dates">
                                        <?php
                                        echo JBusinessUtil::truncate($category->description, 100);
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

    <?php foreach($this->productCategories[2] as $key=>$categories) { ?>
        <div id="grid-content" class='grid4 categories-level-<?php echo $key ?>' style="display:none;">
            <?php $i = 0; ?>
            <span id="parent-category-<?php echo $key ?>"><h1><?php echo $categories->parent ?></h1></span>
            <?php foreach($categories->categories as $category) { ?>
            <?php if($i%4==0) { ?>
                <div class="row-fluid">
            <?php } ?>
                <div id="subcategory-<?php echo $category->id ?>" class="span3 product-image-small">
                    <div>
                        <figure class="post-image">
                            <a href="javascript:void(0);" onclick="showProducts(<?php echo $category->id.', '.$this->company->id; ?>)">
                                <?php if(!empty($category->imageLocation) ){?>
                                    <img title="<?php echo $category->name?>" alt="<?php echo $category->name?>" src="<?php echo JURI::root().PICTURES_PATH.$category->imageLocation ?>">
                                <?php }else{ ?>
                                    <img title="<?php echo $category->name?>" alt="<?php echo $category->name?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>">
                                <?php } ?>
                            </a>
                        </figure>

                        <div class="post-content" style="text-align:center;">
                            <a style="color:black;" href="javascript:void(0);" onclick="showProducts(<?php echo $category->id.', '.$this->company->id; ?>)">
                                <h2 class="post-title">
                                    <span><?php echo $category->name ?></span>
                                </h2>
                                <p class="offer-dates">
                                    <?php
                                    echo JBusinessUtil::truncate($category->description, 100);
                                    ?>
                                </p>
                            </a>
                        </div>
                    </div>
                </div>
                <?php
                $i++;
                if($i%4==0) { ?>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php if($i%4!=0) { ?>
            </div>
        <?php } ?>
        </div>
    <?php } ?>
<?php } else {
    echo JText::_("LNG_NO_PRODUCT_CATEGORIES");
}
?>
<span id="product-list-content"></span>
<span id="product-details-content"></span>
<div class="clear"></div>