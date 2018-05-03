<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/imagesloaded.pkgd.min.js');
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/jquery.isotope.min.js');
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/isotope.init.js');
?>

<!-- SIMPLE(grid) VIEW -->
<div id="grid-content" class="grid-content row-fluid grid4 categories-grid">

			<?php $k = 0;
            $index = 0;
            ?>
			<?php foreach($categories as $category) {
    $index++;
    if (!is_array($category)) {
        $category = array($category);
        $category["subCategories"] = array();
    }
    if (isset($category[0]->name)) {
        $k = $k + 1;
        ?>
        <article id="post-<?php echo $category[0]->id ?>" class="post clearfix span3">
            <div class="post-inner">
                <figure class="post-image ">
                    <a href="<?php echo $category[0]->link ?>">
                        <?php if (!empty($category[0]->imageLocation)) { ?>
                            <img title="<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>"
                                 alt="<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>"
                                 src="<?php echo JURI::root() . PICTURES_PATH . $category[0]->imageLocation ?>">
                        <?php } else { ?>
                            <img title="<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>"
                                 alt="<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>"
                                 src="<?php echo JURI::root() . PICTURES_PATH . '/no_image.jpg' ?>">
                        <?php } ?>
                    </a>
                </figure>

                <div class="post-content">
                    <h1 class="post-title">
                        <a href="<?php echo htmlspecialchars($category[0]->link, ENT_QUOTES) ?>"><?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>
                            <?php if ($appSettings->show_total_business_count) { ?>
                                <span class="numberCircle"> <?php echo $category[0]->nr_listings ?></span>
                            <?php } ?>
                        </a>
                    </h1>
                </div>
                <!-- /.post-content -->
            </div>
            <!-- /.post-inner -->
        </article>
        <?php if ($index % 4 == 0) { ?>
            </div>
            <div id="grid-content" class="grid-content row-fluid grid4 categories-grid">
        <?php } ?>
        <?php
    }
    }
    ?>
<div class="clear"></div>
</div>
<?php if(!empty($params) && $params->get('showviewall')){?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
    </div>
<?php }?>