<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
$showLocation = isset($showLocation)?$showLocation:1;
?>

<div id="dir-items" class="dir-items<?php echo $moduleclass_sfx; ?>">
    <?php $index = 0;?>
    <div class="row-fluid ">
        <?php if(!empty($items)){?>
        <?php foreach ($items as $item) { ?>
        <?php $index ++; ?>
        <div class="item-box <?php echo $span ?>">
            <div class="full-width-logo" style="<?php echo $backgroundCss?> <?php echo $borderCss?>">
                <div class="item-overlay">
                    <div class="item-vertical-middle">
                        <div>
                            <a href="<?php echo $item->link ?>" class="btn-view"><?php echo JText::_("LNG_VIEW")?></a>
                        </div>
                    </div>
                </div>
                <a href="<?php echo $item->link ?>">
                   <?php if(isset($item->logoLocation) && $item->logoLocation!='') { ?>
						<div class="dir-bg-image" style="background-image: url('<?php echo JURI::root().PICTURES_PATH.$item->logoLocation ?>')"></div>
					<?php } else { ?>
						<div class="dir-bg-image" style="background-image: url('<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>')"></div>
					<?php } ?>
                </a>
            </div>
            <div class="item-info">
                <a class="item-name" href="<?php echo $item->link ?>">
                    <?php echo $item->name; ?>
                </a>
                <?php if ( isset($showListingName) && $showListingName == 1 ){ ?>
	                <span class="company-info" style="padding: 10px !important;">
	                   <i class="dir-icon-building-o"></i> <?php echo " ".$item->companyName; ?>
	                </span>
                <?php } ?>
                <p style="padding-top: 6px !important;">
                    <?php
                    if(!empty($item->slogan)) {
                        echo $item->slogan;
                    } else if(!empty($item->short_description)) {
                        echo JBusinessUtil::truncate($item->short_description, 200);
                    } else if(!empty($item->description)) {
                        echo JBusinessUtil::truncate($item->description, 200);
                    }
                    ?>
                </p>
            </div>
            <div class="item-options">
                <?php if(isset($item->mainCategoryLink)) { ?>
                <div class="dir-category">
                    <a href="<?php echo $item->mainCategoryLink ?>"><i class="dir-icon-<?php echo $item->mainCategoryIcon ?>"></i> <?php echo $item->mainCategory ?></a>
                </div>
                <?php }
                $address = JBusinessUtil::composeAddress($item->city, $item->county);
                if($showLocation && !empty($address)) {?>
						<span class="item-address ">
							 <i class="dir-icon-map-marker"></i> <?php echo $address; ?>
						</span>
                <?php } ?>
                <a class="ui-dir-button" href="<?php echo $item->link ?>">
                    <span class="ui-button-text"><?php echo JText::_("LNG_VIEW_DETAILS")?></span>
                </a>
            </div>
        </div>
        <?php if($index%4 == 0 && count($items)>$index){ ?>
    </div>
    <div class="row-fluid">
        <?php }?>
        <?php } ?>
        <?php } ?>
    </div>

    <?php if(!empty($params) && $params->get('showviewall')){?>
        <div class="view-all-items">
            <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
        </div>
    <?php }?>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery(".full-width-logo").each(function(){
        });

        <?php
        $load = JRequest::getVar("latitude");
        if($params->get('geo_location') && empty($load)){ ?>
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(addCoordinatesToUrl);
        }
        <?php } ?>
    });

    function addCoordinatesToUrl(position){

        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        var newURLString = window.location.href;
        newURLString += ((newURLString.indexOf('?') == -1) ? '?' : '&');
        newURLString += "latitude="+latitude;
        newURLString += ((newURLString.indexOf('?') == -1) ? '?' : '&');
        newURLString += "longitude="+longitude;

        window.location.href = newURLString;    // The page will redirect instantly

    }
</script>