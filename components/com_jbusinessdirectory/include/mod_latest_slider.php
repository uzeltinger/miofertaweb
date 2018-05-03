<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/font-awesome.css');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/slick.css');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/slick.js');
$lang = JFactory::getLanguage();
$dir = $lang->get('rtl');
$showLocation = isset($showLocation)?$showLocation:1;
$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
$enablePackages = $appSettings->enable_packages;
$idnt = rand(500, 1500);
$sliderId = rand(1000,10000);

$sliderParams = array();
$sliderParams['sliderId'] = $sliderId;
$sliderParams['autoplay'] = $params->get('autoplay') ? true : false;
$sliderParams['autoplaySpeed'] = $params->get('autoplaySpeed');
$sliderParams['nrVisibleItems'] = $params->get('nrVisibleItems');
$sliderParams['nrItemsToScrool'] = $params->get('nrItemsToScrool');
$sliderParams['rtl'] = $dir ? true : false;

$user = JFactory::getUser();
$showData = !($user->id==0 && $appSettings->show_details_user == 1);
$db = JFactory::getDBO();
require_once JPATH_SITE.'/components/com_jbusinessdirectory/classes/attributes/attributeservice.php';
?>

<div id="dir-items"  class="dir-items<?php echo $moduleclass_sfx; ?>" >
    <?php $index = 0; ?>
    <div class="bussiness-slider responsive slider" id="slider-<?php echo $sliderId ?>">
        <?php if(!empty($items)) ?>
        <?php foreach ($items as $item) {?>
            <?php $index ++; ?>
            <div>
                <div class="slider-item">
                    <div class="slider-content" id="slider-content-<?php echo $sliderId ?>" style="<?php echo $backgroundCss?> <?php echo $borderCss?>">
                        <a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>">
                             <?php if(isset($item->logoLocation) && $item->logoLocation!='') { ?>
								<div class="dir-bg-image" style="background-image: url('<?php echo JURI::root().PICTURES_PATH.$item->logoLocation ?>')"></div>
							<?php } else { ?>
								<div class="dir-bg-image" style="background-image: url('<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>')"></div>
							<?php } ?>
                        </a>
                        <div class="info" onclick="goToLink('<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>')">
                            <div class="hover_info">
                                <h3><?php echo $item->name ?></h3>

                                <div class="" >
                                    <?php $address = JBusinessUtil::getAddressText($item);
                                    if($showLocation && !empty($address)) { ?>
                                        <i class="dir-icon-map-marker"></i> <?php echo $address; ?>
                                    <?php }?>
                                </div>

                                  <?php if(!empty($item->phone) && $showData && (isset($item->packageFeatures) && in_array(PHONE,$item->packageFeatures) || !$enablePackages)) { ?>
                                    <div>
                                        <i class="dir-icon-phone"></i> <?php echo htmlspecialchars($item->phone, ENT_QUOTES) ?>
                                    </div>
                                <?php } ?>
                                <?php if($showData && !empty($item->website) && (isset($item->packageFeatures) && in_array(WEBSITE_ADDRESS,$item->packageFeatures) || !$enablePackages)){
                                    if ($appSettings->enable_link_following){
                                    $followLink = (isset($item->packageFeatures) && in_array(LINK_FOLLOW,$item->packageFeatures) && $enablePackages)?'rel="follow"' : 'rel="nofollow"';
                                    }else{
                                        $followLink ="";
                                    }?>
                                    <div onclick="this.event.stopPropagation()">
                                        <a <?php echo $followLink ?> target="_blank" itemprop="url" title="<?php echo $db->escape($item->name);?> Website" onclick="increaseWebsiteClicks(<?php echo $item->id ?>);event.stopPropagation();" href="<?php echo $db->escape($item->website) ?>"><i class="dir-icon-globe"></i> <?php echo $db->escape($item->website) ?></a>
                                    </div>
                                <?php } ?>
                                <?php if (isset($item->customAttributes)) { ?>
                                    <div class="attribute-icon-container-slider">
                                        <?php foreach($item->customAttributes as $attribute) {
                                            $icons = AttributeService::getAttributeIcons($attribute, $appSettings->enable_packages, $item->packageFeatures);
                                            $color = !empty($attribute->color)?$attribute->color:'';
                                            if(!empty($icons)) {
                                                foreach($icons as $icon)
                                                    echo '<i class="'.$icon.' attribute-icon" style="color:'.$color.';"></i>';
                                            }
                                        }?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="slider-item-name">
                        <?php if(!empty($item->mainCategoryIcon) && isset($item->mainCategoryIcon)){ ?>
                            <a href="<?php echo $item->mainCategoryLink ?>">
                                <i class="pull-right dir-icon-custom rounded-x dir-icon-bg-grey dir-icon-<?php echo $item->mainCategoryIcon ?>"></i>
                            </a>
                        <?php } ?>
                        <h3 style="line-height: 20px"><?php echo $item->name ?></h3>
                        <?php if(isset($item->review_score) && $appSettings->enable_ratings){ ?>
                            <span title="<?php echo $item->review_score ?>" class="rating-review-<?php echo $idnt ?>"></span>
                        <?php } ?>
                        <?php if ( isset($showListingName) && $showListingName == 1 ){ ?>
                        <h5 class="company-info dir-icon-building-o">
                            <?php echo " ".htmlspecialchars($item->companyName, ENT_QUOTES); ?>
                        </h5>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php if(!empty($params) && $params->get('showviewall')){?>
        <div class="view-all-items">
            <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
        </div>
    <?php }?>
</div>

<script>
    jQuery(document).ready(function() {
        initSlider(<?php echo json_encode($sliderParams) ?>);

        <?php if($appSettings->enable_ratings) { ?> 
	        jQuery('.rating-review-<?php echo $idnt ?>').raty({
	            half:		true,
	            size:		24,
	            starHalf:	'star-half.png',
	            starOff:	'star-off.png',
	            starOn: 	'star-on.png',
	            hintList:	["<?php echo JText::_('LNG_BAD') ?>","<?php echo JText::_('LNG_POOR') ?>","<?php echo JText::_('LNG_REGULAR') ?>","<?php echo JText::_('LNG_GOOD') ?>","<?php echo JText::_('LNG_GORGEOUS') ?>"],
	            noRatedMsg: "<?php echo JText::_('LNG_NOT_RATED_YET') ?>",
	            start:		function() { return jQuery(this).attr('title')},
	            path:		'<?php echo JURI::root().'components/com_jbusinessdirectory/assets/images/' ?>',
	            readOnly:	true
	        });
        <?php } ?>

        <?php
        $load = JRequest::getVar("latitude");
        if($params->get('geo_location') && empty($load)){ ?>
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(addCoordinatesToUrl);
        }
        <?php } ?>
    });

    function goToLink(link){
        document.location.href=link;
    }

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
