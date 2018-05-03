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


<div class="row-fluid">
    <?php $k = 0; ?>
    <?php foreach ($categories as $category){
        if (!is_array($category)){
            $category = array($category);
            $category["subCategories"] = array();
        }
        if (isset($category[0]->name)){
            $k++;
        ?>
            <div class="span3">
                <b>
                    <a href="<?php echo $category[0]->link ?>"><?php echo $category[0]->name ?>
                        <?php if($appSettings->show_total_business_count) { ?>
                            <span class="numberCircle"> <?php echo $category[0]->nr_listings ?></span>
                        <?php } ?>
                    </a>
                </b>
                <br/>

        <?php } ?>
            <?php
            $i=1;
            foreach ($category["subCategories"] as $cat){
                ?>
                    <a title="<?php echo $cat[0]->name?>" alt="<?php echo $cat[0]->name?>"
                       href="<?php echo $cat[0]->link ?>">
                        <?php echo $cat[0]->name?>
                    </a>
                    <br/>
            <?php } ?>
        </div>
    <?php
    if ($k % 4 == 0 )
        echo '</div><br/><div class="row-fluid">';

    } ?>
</div>