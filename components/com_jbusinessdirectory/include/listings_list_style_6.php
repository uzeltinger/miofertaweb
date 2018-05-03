<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
require_once JPATH_COMPONENT_SITE.'/classes/attributes/attributeservice.php';

JHtml::_('stylesheet', 'https://fonts.googleapis.com/css?family=Raleway:700');

$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
$enableSEO = $appSettings->enable_seo;
$enablePackages = $appSettings->enable_packages;
$enableRatings = $appSettings->enable_ratings;
$enableNumbering = $appSettings->enable_numbering;
$user = JFactory::getUser();

$total_page_string = $this->pagination->getPagesCounter();
$current_page = substr($total_page_string,5,1);
$limitStart = JFactory::getApplication()->input->get('limitstart');
if(empty($limitStart) || ($current_page == 1) || $total_page_string==null ) {
    $limitStart = 0;
}
$showData = !($user->id==0 && $appSettings->show_details_user == 1);
?>

<div id="results-container" itemscope itemtype="http://schema.org/ItemList" <?php echo $this->appSettings->search_view_mode?'style="display: none"':'' ?> class="results-style-6">
    <?php
    if(!empty($this->companies)){
        $itemCount = 1;
        foreach($this->companies as $index=>$company){
            ?>
            <div class="result <?php echo isset($company->featured) && $company->featured==1?"featured":"" ?>">

                <div class="business-container row-fluid" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <span itemscope itemprop="item" itemtype="http://schema.org/Organization">
                            <?php if( $company->logoLocation && (isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO,$company->packageFeatures) || !$enablePackages)){ ?>
		                        <div class="span3">
                                    <div class="item-image" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
                                        <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>">
                                            <?php if(isset($company->logoLocation) && $company->logoLocation!=''){?>
                                                <div class="item-thumbnail-wrap" style="background-image: url('<?php echo JURI::root().PICTURES_PATH.$company->logoLocation ?>')"></div>
                                                <img style="display:none;" title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo JURI::root().PICTURES_PATH.$company->logoLocation ?>" itemprop="contentUrl" >
                                            <?php }else{ ?>
                                                <div class="item-thumbnail-wrap" style="background-image: url('<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>')"></div>
                                                <img style="display:none;" title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl" >
                                            <?php } ?>
                                        </a>
                                         <?php if(isset($company->featured) && $company->featured==1){ ?>
                    						<div class="featured-text">
                    	                        <?php echo JText::_("LNG_FEATURED")?>
                                            </div>
                    		  	  		  <?php } ?>
                                  	</div>
                                  </div>
                            <?php } ?>

                            <div class="no-margin <?php echo isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO,$company->packageFeatures) || !$enablePackages && isset($company->logoLocation) && $company->logoLocation!=''?'span9':'span12'?>">
                                <div class="row-fluid">
                                	<div class="span8 company-details">
                                        <h3 class="business-name">
                                            <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>"><?php echo $enableNumbering? "<span>".($index + $limitStart + 1).". </span>":""?><span itemprop="name"><?php echo $company->name ?></span></a>
                                        </h3>
                                        <div class="content-box">
                                        	<div class="dir-intro-text">
                                            	<?php echo JBusinessUtil::truncate($company->short_description, 250); ?>
                                            </div>
                                            
                                            <?php $address = JBusinessUtil::getAddressText($company);?>
                                            <?php if(!empty($address)){ ?>
                                                <div class="company-address">
                                                    <i class="dir-icon-map-marker light-blue-marker"></i> <?php echo $address ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <div class="span4 item-info-container">
                                        <div class="company-rating" <?php echo !$enableRatings? 'style="display:none"':'' ?>>

                                            <?php if($appSettings->enable_ratings) { ?>
                                                <div class="rating">
                                                    <p class="rating-average" title="<?php echo $company->review_score?>" id="<?php echo $company->id?>" style="display: block;"></p>
                                                </div>
                                                <div class="review-count">
                                                    <a <?php echo $company->review_score == 0 ? 'style="display:none"':'' ?>></a>
                                                </div>
                                            <?php } ?>

                                        </div>
                                        <div class="clear"></div>

                                        <div class="item-info-wrap">
                                            <?php if($showData && !empty($company->website) && (isset($company->packageFeatures) && in_array(WEBSITE_ADDRESS,$company->packageFeatures) || !$enablePackages)){
                                                if ($appSettings->enable_link_following) {
                                                    $followLink = (isset($company->packageFeatures) && in_array(LINK_FOLLOW, $company->packageFeatures) && $enablePackages) ? 'rel="follow"' : 'rel="nofollow"';
                                                }else{
                                                    $followLink ="";
                                                }?>
                                                <div class="item-info truncate-text">
                                                    <i class="dir-icon-home"></i><a  rel="<?php echo $followLink ?>" href="<?php echo $this->escape($company->website) ?>"><?php echo $this->escape($company->website) ?></a>
                                                </div>
                                            <?php } ?>
                                            
                                            <span style="display:none;" itemprop="url"><?php echo JBusinessUtil::getCompanyLink($company) ?></span>
                                            <?php if($showData && !empty($company->phone)) { ?>
                                            	<div class="item-info truncate-text">
                                                     <i class="dir-icon-phone"></i> <?php echo $company->phone ?>
                                                </div>
                                            <?php } ?>
                                            
                                             <?php if(!empty($company->distance)){?>
                                                <div class="item-info">
                                                    <?php echo JText::_("LNG_DISTANCE").": ".round($company->distance,1)." ". ($this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM")) ?>
                                                </div>
                                            <?php } ?>

                                            <?php if(!empty($company->latitude) && !empty($company->longitude) && $showData && (isset($company->packageFeatures) && in_array(GOOGLE_MAP,$company->packageFeatures) || !$enablePackages)){?>
                                            	<div class="item-info">
                                               		<i class="dir-icon-map-marker"></i><a target="_blank" class="nowrap" href="<?php echo $this->escape(JBusinessUtil::getDirectionURL($this->location, $company)) ?>"><?php echo JText::_("LNG_GET_MAP_DIRECTIONS")?></a>
                                               	</div>
                                            <?php }?>
                                        </div>
                                        <?php if(!empty($company->categories)){?>
                                                <div class="item-info">
                                                    <?php
                                                    $categories = explode('#|',$company->categories);
                                                    foreach($categories as $i=>$category) {
                                                        $category = explode("|", $category);
                                                        //dump($category);
                                                        ?>
                                                        <?php if(isset($category[3]) && !empty($category[3])&& $category[3]!="None"){ ?>
                                                                <a title="<?php echo $category[1] ?>" class="icon-box-link" href="<?php echo JBusinessUtil::getCategoryLink($category[0], $category[2]) ?>">
                                                                    <div class="cat-icon-box" style="border-color:<?php echo $category[4]; ?>;">
                                                                         <i class="dir-icon-<?php echo $category[3]; ?>" style="color:<?php echo $category[4]; ?>;"></i>
                                                                    </div>
                                                                </a>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <span style="display:none;" itemprop="position"><?php echo $itemCount ?></span>
                
                <div class="result-actions">
                    <ul>
                        <li> </li>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>
            <?php
            $itemCount++;
        }
    }
    ?>
</div>