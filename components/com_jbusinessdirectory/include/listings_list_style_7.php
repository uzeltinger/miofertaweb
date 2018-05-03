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
<div id="results-container" itemscope itemtype="http://schema.org/ItemList" <?php echo $this->appSettings->search_view_mode?'style="display: none"':'' ?> class="results-style-7">
	<?php 
	if(!empty($this->companies)){
	    $itemCount = 1;
		foreach($this->companies as $index=>$company){
		?>
			<div class="result <?php echo isset($company->featured) && $company->featured==1?"featured":"" ?>">
				<div class="business-container" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <span itemscope itemprop="item" itemtype="http://schema.org/Organization">
					<div class="business-details row-fluid">
						<?php $showPictures = false;?>
						<?php if(isset($company->packageFeatures) && in_array(IMAGE_UPLOAD, $company->packageFeatures) || !$enablePackages ){ ?>
							<?php if(!empty($company->pictures)){?>
								<?php $showPictures = true;?>
								<div class="span5">
									<div class="company-gallery">
										<a href="<?php echo JBusinessUtil::getCompanyLink($company)?>">
											<?php foreach($company->pictures as $picture){?>
												<img src="<?php echo JURI::root().PICTURES_PATH.$picture?>" style="display:none">
											<?php } ?>
										</a>
									</div>
								</div>
							<?php }?>
						<?php }?>
	 					<div class="company-info <?php echo !$showPictures ? "span12":"span7" ?>">
							<div class="company-header row-fluid">
								<?php if(isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO,$company->packageFeatures) || !$enablePackages){ ?>
									<div class="company-logo" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
										<a href="<?php echo JBusinessUtil::getCompanyLink($company)?>">
											<?php if(isset($company->logoLocation) && $company->logoLocation!=''){?>
												<img title="<?php echo $company->name?>" alt="<?php echo $company->name?>" src="<?php echo JURI::root().PICTURES_PATH.$company->logoLocation ?>" itemprop="contentUrl" />
											<?php }else{ ?>
												<img title="<?php echo $company->name?>" alt="<?php echo $company->name?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl" />
											<?php } ?>
										</a>
									</div>
								<?php } ?>
								<?php if(isset($company->featured) && $company->featured==1){ ?>
									<div class="featured-text">
										<?php echo JText::_("LNG_FEATURED")?>
									</div>
								<?php } ?>
	
								<div class="busienss-name-info">									
									<h3 class="business-name">
										<a href="<?php echo JBusinessUtil::getCompanyLink($company)?>" ><?php echo $enableNumbering? "<span>".($index + $limitStart + 1).". </span>":""?><span itemprop="name"> <?php echo $company->name?> </span></a>
									</h3>
									<div class="company-rating" <?php echo !$enableRatings? 'style="display:none"':'' ?>>
										<?php if($this->appSettings->enable_ratings) { ?>
			                                    <div class="rating">
			                                        <p class="rating-average" title="<?php echo $company->review_score?>" id="<?php echo $company->id?>" style="display: block;"></p>
			                                    </div>
			                                    <div class="review-count" <?php echo $company->review_score == 0 ? 'style="display:none"':'' ?>>
			                                       <span> <?php echo $company->nr_reviews." ".JText::_("LNG_REVIEWS");?></span>
			                                    </div>
			                                <?php } ?>
									</div>
								</div>
								<div class="clear"></div>
							</div>
							<?php if(!empty($company->short_description)){?>
								<div class="company-intro">
									<?php echo JBusinessUtil::truncate($company->short_description, 200) ?>
									<a class="" href="<?php echo JBusinessUtil::getCompanyLink($company)?>"><?php echo JText::_('LNG_MORE_INFO') ?></a>
								</div>
							<?php } ?>
	
						
							<div class="content-box">
								<ul class="company-links" style="display: none">
									<?php if(!empty($company->categories)){?>
										<li>
											<ul class="business-categories">
												<?php 
													$categories = explode('#|',$company->categories);
													foreach($categories as $i=>$category){
														$category = explode("|", $category);
														?>
															<li> <a href="<?php echo JBusinessUtil::getCategoryLink($category[0], $category[2]) ?>"><?php echo $category[1]?></a><?php echo $i<(count($categories)-1)? ' | ':'' ?></li>
														<?php 
													}
												?>
											</ul>
										</li>
									<?php }?>
								</ul>
								<ul class="company-links">
									<?php if(!empty($company->typeName)){ ?>
										<li><?php echo $company->typeName?></li>
									<?php } ?>	
								
								
									<?php if($showData && !empty($company->website) && (isset($company->packageFeatures) && in_array(WEBSITE_ADDRESS,$company->packageFeatures) || !$enablePackages)){
										if ($appSettings->enable_link_following) {
											$followLink = (isset($company->packageFeatures) && in_array(LINK_FOLLOW, $company->packageFeatures) && $enablePackages) ? 'rel="follow"' : 'rel="nofollow"';
										}else{
											$followLink ="";
										}?>
										<li><a <?php echo $followLink ?> class="nowrap" target="_blank" title="<?php echo $company->name?> Website" target="_blank"  onclick="increaseWebsiteClicks(<?php echo $company->id ?>)"  href="<?php echo $company->website ?>"><i class="dir-icon-globe"></i> <?php echo JText::_('LNG_WEBSITE') ?></a></li>
									<?php } ?>
									<?php if($showData && !empty($company->latitude) && !empty($company->longitude) && (isset($company->packageFeatures) && in_array(GOOGLE_MAP,$company->packageFeatures) || !$enablePackages)){?>
											<li><a target="_blank" class="nowrap" href="<?php echo JBusinessUtil::getDirectionURL($this->location, $company) ?>"><i class="dir-icon-map-marker"></i> <?php echo JText::_("LNG_GET_MAP_DIRECTIONS")?></a></li>
									<?php } ?>
								</ul>
                                <span style="display:none;" itemprop="url"><?php echo JBusinessUtil::getCompanyLink($company) ?></span>

                                <?php if(!empty($company->distance)){?>
	                                <div>
	                                    <?php echo JText::_("LNG_DISTANCE").": ".round($company->distance,1)." ". ($this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM")) ?>
	                                </div>
	                            <?php } ?>
				
							</div>
							
							<div class="bottom-info row-fluid clear">
								<?php if( $showData && (isset($company->packageFeatures) && in_array(PHONE, $company->packageFeatures) || !$enablePackages )){ ?> 
									<?php if(!empty($company->phone)) { ?>
									<div class="span4">
										<span class="phone" itemprop="telephone">
											<i class="dir-icon-phone"></i> <a href="tel:<?php  echo $company->phone; ?>"><?php echo $company->phone; ?></a>
										</span>
									</div>
									<?php } ?>
								<?php } ?>
								
								<?php $address = JBusinessUtil::getAddressText($company); ?>
								<?php if(!empty($address)){?>
									<div class="span8">
										<span class="company-address">
											<span itemprop="address"><i class="dir-icon-map-marker"></i>&nbsp;<?php echo $address?></span>
										</span>
									</div>
								<?php }?>
							</div>
						</div>
					</div>
                    </span>
                    <span style="display:none;" itemprop="position"><?php echo $itemCount ?></span>
                </div>
			
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
	<script>
	jQuery(document).ready(function() {
	
		jQuery(".company-gallery").each(function(){
			jQuery(this).children().children().first().show();
		});
	
		var visibleChild = 1;
		
		jQuery(".company-gallery").mousemove(function( event ) {
			var nrChildren = jQuery(this).children().children().size();
			var offsetX = parseInt(jQuery(this).offset().left);
			var width = parseInt(jQuery(this).width());
			var currentChild = parseInt((event.pageX - offsetX) / (width/nrChildren));
			if((currentChild+1)>=nrChildren){
				currentChild = nrChildren-1;
			}
			if(currentChild!=visibleChild){
				jQuery(this).children().children().hide();
				jQuery(this).children().children().eq(currentChild).show();
				visibleChild = currentChild;
			}
		});
	});
	
	</script>
</div>