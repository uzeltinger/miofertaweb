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
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/isotope.init.js');
JHTML::_('stylesheet',  'components/com_jbusinessdirectory/assets/css/ultimate.min.css');
?>

<!-- ICONS VIEW -->
<div class="pagewidth clearfix grid4 categories-grid" >
	<div class="row-fluid">
	    <?php $k = 0; ?>
	    <?php foreach($categories as $category){
	        if(!is_array($category)){
	            $category = array($category);
	            $category["subCategories"] = array();
	        }
	        if(isset($category[0]->name)){
	            $k++;
	            ?>
				
		            <div id="post-<?php echo  $category[0]->id ?>" class="span3">
		                <div class="wpb_wrapper">
		                    <div class="aio-icon-component style_2">
		                        <a class="aio-icon-box-link" style="text-decoration: none;" href="<?php echo htmlspecialchars($category[0]->link, ENT_QUOTES) ?>">
		                            <div class="aio-icon-box top-icon">
		                                <div class="aio-icon-top">
		                                    <div class="aio-icon none" style="">
		                                        <div class="dir-icon-<?php echo $category[0]->icon ?>"></div>
		                                    </div>
		                                </div>
		                                <div class="aio-icon-header">
		                                    <h3 class="aio-icon-title">
		                                        <?php echo htmlspecialchars($category[0]->name, ENT_QUOTES); ?>
		                                    </h3>
		                                    <?php if($appSettings->show_total_business_count) { ?>
		                                        <h4>
		                                            <span class="numberCircle"> <?php echo $category[0]->nr_listings ?></span>
		                                        </h4>
		                                    <?php } ?>
		                                </div>
		                                <div class="aio-icon-description">
		                                    <?php echo JBusinessUtil::truncate($category[0]->description,100); ?>
		                                </div>
		                            </div>
		                        </a>
		                    </div>
		                </div>
		            </div>
	            <?php
	            if($k % 4 == 0 )
	                echo '</div><div class="row-fluid">';
	
	        } ?>
	    <?php } ?>
    </div>
    <div class="clear"></div>
</div>
<?php if(!empty($params) && $params->get('showviewall')){?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
    </div>
<?php }?>