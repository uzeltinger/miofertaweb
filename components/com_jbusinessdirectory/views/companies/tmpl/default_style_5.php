<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
require_once 'header.php';
require_once JPATH_COMPONENT_SITE.'/classes/attributes/attributeservice.php';
$db = JFactory::getDBO();
?>

<div id="company-style-5-container" itemscope itemtype="http://schema.org/LocalBusiness">
    <div class="dir-print">
        <a href="javascript:printCompany(<?php echo $this->company->id ?>, '<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=companies&tmpl=component"); ?>')"><i class="dir-icon-print"></i> <?php echo JText::_("LNG_PRINT")?></a>
    </div>
    <?php require_once 'breadcrumbs.php';?>
	<div id="company-style-5-header">
		<div class="row-fluid">
			<!-- Business Categories -->
			<div class="company-style-5-header-image span12">
				<div class="company-style-5-header-info row-fluid">
					<div class="span9 first-column">
						<?php if(isset($this->package->features) && in_array(SHOW_COMPANY_LOGO,$this->package->features) || !$appSettings->enable_packages){ ?>
							<div class="span3" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
								<!-- Business Logo -->
								<?php if(isset($this->company->logoLocation) && $this->company->logoLocation!=''){?>
									<img class="business-logo" title="<?php echo $this->escape($this->company->name) ?>" alt="<?php echo $this->escape($this->company->name) ?>" src="<?php echo JURI::root().PICTURES_PATH.$this->company->logoLocation ?>" itemprop="contentUrl">
								<?php }else{ ?>
									<img class="business-logo" title="<?php echo $this->escape($this->company->name) ?>" alt="<?php echo $this->escape($this->company->name) ?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl">
								<?php } ?>
							</div>
						<?php } ?>
						<div class="span9">
							<!-- Business Name -->
							<h2 itemprop="name"><?php echo isset($this->company->name) ? $this->escape($this->company->name) : ""; ?></h2>
							<div class="dir-address">
								<span itemprop="address">
									<!-- Business Address -->
									<?php echo JBusinessUtil::getAddressText($this->company); ?>
								</span>
							</div>
							<div class="dir-categories">
								<!-- Business Categories -->
								<?php if(!empty($this->company->categories)){?>
									<?php
										foreach($this->company->categories as $i=>$category){
											?>
												<a href="<?php echo JBusinessUtil::getCategoryLink($category[0], $category[2]) ?>"><?php echo $this->escape($category[1]) ?></a><?php echo $i<(count($this->company->categories)-1)? ',&nbsp;':'' ?>
											<?php 
										}
									?>
								<?php } ?>
							</div>
							<?php if($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
								<?php if(!empty($this->company->phone)) { ?>
									<span class="phone" itemprop="telephone">
										<i class="dir-icon-phone"></i> <a href="tel:<?php echo $this->escape($this->company->phone); ?>"><?php echo $this->escape($this->company->phone); ?></a>
									</span><br/>
								<?php } ?>
								<?php if(!empty($this->company->mobile)) { ?>
									<span class="phone" itemprop="telephone">
										<i class="dir-icon-mobile-phone"></i> <a href="tel:<?php echo $this->escape($this->company->mobile); ?>"><?php echo $this->escape($this->company->mobile); ?></a>
									</span><br/>
								<?php } ?>
							<?php }?>
							<div>
									<!-- Business Ratings -->
	                             <?php if($appSettings->enable_ratings) { ?>
									<div class="company-info-rating" <?php echo !$appSettings->enable_ratings? 'style="display:none"':'' ?>>
                                        <?php if(!empty($this->reviews) > 0) { ?>
                                            <span style="display:none" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                                                <span>
                                                     <span itemprop="itemReviewed" itemscope itemtype="http://schema.org/LocalBusiness">
                                                         <span itemprop="name"><?php echo $this->company->name?></span>
                                                         <span itemprop="image"><?php echo JURI::root().PICTURES_PATH.$this->company->logoLocation ?></span>
                                                         <span itemprop="address"><?php echo $address ?></span>
                                                         <span itemprop="telephone"><?php echo $this->company->phone ?></span>
                                                     </span>
                                                    <span itemprop="ratingValue"><?php echo $this->company->review_score?></span>  <span itemprop="worstRating">0</span><span itemprop="bestRating">5</span>
                                                </span>
                                                <span itemprop="ratingCount"><?php echo count($this->reviews)?></span>
                                            </span>
                                        <?php } ?>

										<div class="rating">
											<span class="user-rating-avg" id="rating-average" title="<?php echo $company->review_score?>" alt="<?php echo $company->id?>" style="display: block;"></span>
										</div>
									</div>
								<?php } ?>
								<div class="review-info">
									<?php if($appSettings->enable_reviews) { ?> 
										<a href="#go-company-reviews"><span><?php echo count($this->reviews); ?> <?php echo JText::_('LNG_REVIEWS'); ?></span></a>
									<?php } ?>
		
									<?php if($this->appSettings->enable_bookmarks) { ?>
										<?php if($appSettings->enable_reviews) { ?> | <?php } ?>
										<?php if(!empty($company->bookmark)) { ?>
											<!-- Business Bookmarks -->
											<a href="javascript:showUpdateBookmarkDialog(<?php echo $user->id==0?"1":"0"?>)"  title="<?php echo JText::_("LNG_UPDATE_BOOKMARK")?>" class="bookmark"><i class="dir-icon-heart"></i> <span><?php echo JText::_('LNG_UPDATE_BOOKMARK'); ?></span></a>
										<?php } else {?>
											<a href="javascript:addBookmark(<?php echo $user->id==0?"1":"0"?>)" title="<?php echo JText::_("LNG_ADD_BOOKMARK")?>" class="bookmark"><i class="dir-icon-heart-o"></i> <span><?php echo JText::_('LNG_ADD_BOOKMARK'); ?></span></a>
										<?php } ?>
									<?php } ?>
								</div>
							</div>
                            <div class="clear"></div>
                            <div class="attribute-icon-container">
                                <?php foreach($this->companyAttributes as $attribute) {
                                    $packageFeatured = isset($this->package->features)?$this->package->features:null;
                                    $icons = AttributeService::getAttributeIcons($attribute, $appSettings->enable_packages, $packageFeatured);
                                    $color = !empty($attribute->color)?$attribute->color:'';
                                    if(!empty($icons)) {
                                        foreach($icons as $icon)
                                            echo '<i class="'.$icon.' attribute-icon" style="color:'.$color.';"></i>';
                                    }
                                }?>
                            </div>
						</div>
					</div>
					<div class="span3 second-column">
                        <!-- Business Socials -->
                        <div class="span12">
                            <?php require_once JPATH_COMPONENT_SITE."/include/social_share.php"; ?>
                        </div>
						<?php if($appSettings->enable_reviews){ ?>
							<div class="span12 clear">
								<!-- Business Add Review -->
								<a href="#go-company-reviews" onclick="showReviewForm(<?php echo ($appSettings->enable_reviews_users && $user->id ==0)?"1":"0"; ?>)" class="ui-dir-button ui-dir-button-blue">
									<span class="ui-button-text"><?php echo JText::_("LNG_ADD_REVIEW") ?></span>
								</a>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<?php
		if(false && $this->company->enableWorkingStatus && (!$appSettings->enable_packages || isset($this->package->features) && in_array(OPENING_HOURS,$this->package->features))){
			if ($this->company->workingStatus){?>
				<div class="ribbon-open"><span><?php echo JText::_("LNG_OPEN")?></span></div>
			<?php } else{ ?>
				<div class="ribbon-close"><span><?php echo JText::_("LNG_CLOSED")?></span></div>
			<?php } ?>
		<?php } ?>
	</div>

	<div id="company-style-5-body">
		<div class="row-fluid">

			<!-- BODY -->
			<div class="span8">
				<!-- Business Gallery -->
				<?php if((isset($this->package->features) && in_array(IMAGE_UPLOAD,$this->package->features) || !$appSettings->enable_packages)
							&& !empty($this->pictures)){?>
					<div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<h3><i class="fa dir-icon-camera-retro"></i> <?php echo JText::_("LNG_GALLERY"); ?></h3>
								<?php require_once JPATH_COMPONENT_SITE."/include/image_gallery.php";  ?>
							</div>
						</div>
					</div>
				<?php } ?>
				<!-- Business Details -->
				<div class="company-style-box">
					<div class="row-fluid">
						<div class="span12">
							<h3><i class="fa dir-icon-newspaper-o"></i> <?php echo JText::_("LNG_COMPANY_DETAILS"); ?></h3>
							<!-- Business Slogan -->
							<?php if(isset($this->company->slogan) && strlen($this->company->slogan)>2) { ?>
								<p class="business-slogan"><?php echo $this->escape($this->company->slogan); ?> </p>
							<?php } ?>
						</div>
					</div>

					<!-- Business Description -->
					<div class="row-fluid">
						<div class="span12">
							<div id="dir-listing-description" class="dir-listing-description" itemprop="description">
								<?php if(!empty($this->company->description) && (isset($this->package->features) && in_array(DESCRIPTION,$this->package->features) || !$appSettings->enable_packages)) { ?>
									<?php echo JHTML::_("content.prepare", $this->company->description); ?>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<dl>
								<!-- Business Type -->
								<?php if(!empty($this->company->typeName)) { ?>
									<dt><?php echo JText::_('LNG_TYPE'); ?>:</dt>
									<dd><?php echo $this->escape($this->company->typeName); ?></dd>
								<?php } ?>

								<?php if(!empty($this->company->establishment_year)) { ?>
									<dt><?php echo JText::_('LNG_ESTABLISHMENT_YEAR'); ?>:</dt>
									<dd><?php echo $this->company->establishment_year; ?></dd>
								<?php } ?>

								<?php if(!empty($this->company->employees)) { ?>
									<dt><?php echo JText::_('LNG_EMPLOYEES'); ?>:</dt>
									<dd><?php echo $this->company->employees; ?></dd>
								<?php } ?>
							
								<!-- Business Keywords -->
								<?php if(!empty($this->company->keywords)) { ?>
									<dt><?php echo JText::_('LNG_KEYWORDS'); ?>:</dt>
									<dd>
										<ul class="dir-keywords">
											<?php 
											$keywords =  explode(',', $this->company->keywords);
											for($i=0; $i<count($keywords); $i++) { ?>
												<li>
													<a  href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=search&searchkeyword='.$this->escape($keywords[$i]).$menuItemId) ?>"><?php echo $this->escape($keywords[$i])?><?php echo $i<(count($keywords)-1)? ',&nbsp;':'' ?></a>
												</li>
											<?php 
											} ?>
										</ul>
									</dd>
								<?php } ?>
								
								<!-- Business Locations -->
								<?php if(!empty($this->company->locations) && $appSettings->show_secondary_locations == 1 && 
										(isset($this->package->features) && in_array(SECONDARY_LOCATIONS, $this->package->features) || !$appSettings->enable_packages)){ ?>
									<dt><?php echo JText::_("LNG_COMPANY_LOCATIONS"); ?>:</dt>
									<dd><?php require_once 'locations.php'; ?></dd>
								<?php } ?>
								
								<!-- Business Attachments -->
								<?php if($showData && $appSettings->enable_attachments && (isset($this->package->features) && in_array(ATTACHMENTS, $this->package->features) || !$appSettings->enable_packages)) { ?>
									<?php if(!empty($this->company->attachments)) { ?>
										<dt><?php echo JText::_("LNG_ATTACHMENTS"); ?>:</dt>
										<dd>
											<?php require "listing_attachments.php" ?>
										</dd>
									<?php } ?>
								<?php } ?>
							</dl>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<div class="classification">
                                <?php require_once 'listing_attributes.php'; ?>
							</div>
						</div>
					</div>
				</div>

				<?php if((isset($this->package->features) && in_array(CUSTOM_TAB,$this->package->features) || !$appSettings->enable_packages)
	 				  && !empty($this->company->custom_tab_name)){ ?>
				  <div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<h3> <?php echo $this->company->custom_tab_name; ?></h3>
								<div>
									<?php echo JHTML::_("content.prepare", $this->company->custom_tab_content);?>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				
				
				<!-- Business Videos -->
				<?php if((isset($this->package->features) && in_array(VIDEOS,$this->package->features) || !$appSettings->enable_packages)
							&& !empty($this->videos)) { ?>
					<div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<h3><i class="fa dir-icon-video-camera"></i> <?php echo JText::_("LNG_VIDEOS")?></h3>
								<div id="company-videos">
									<?php require_once 'listing_videos.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php }	?>

				<!-- Business Sounds -->
				<?php if((isset($this->package->features) && in_array(SOUNDS_FEATURE,$this->package->features) || !$appSettings->enable_packages)
							&& !empty($this->sounds)) { ?>
					<div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<h3><i class="fa dir-icon-sound"></i> <?php echo JText::_("LNG_SOUNDS")?></h3>
								<div id="company-sounds">
									<?php require_once 'listing_sounds.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php }	?>
				
				<!-- Business Map Location -->
				<?php if($showData && (isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages )
												&& !empty($this->company->latitude) && !empty($this->company->longitude)){ ?>
					<div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<h3><i class="fa dir-icon-map-marker"></i> <?php echo JText::_("LNG_BUSINESS_MAP_LOCATION"); ?></h3>
								<div>
									<?php require_once 'map.php';?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Business Offers -->
				<?php if((isset($this->package->features) && in_array(COMPANY_OFFERS,$this->package->features) || !$appSettings->enable_packages)
							&& !empty($this->offers) && $appSettings->enable_offers) { ?>
					<div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<h3><i class="fa dir-icon-tag"></i> <?php echo JText::_("LNG_COMPANY_OFFERS"); ?></h3>
								<div id="company-offers" itemprop="hasOfferCatalog" itemscope itemtype="http://schema.org/OfferCatalog">
									<?php require_once 'listing_offers.php';?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Related Companies -->
				<?php if((isset($this->package->features) && in_array(RELATED_COMPANIES,$this->package->features) || !$appSettings->enable_packages)
				&& !empty($this->realtedCompanies)){?>
					<div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<h3><i class="fa dir-icon-tag"></i> <?php echo JText::_("LNG_RELATED_COMPANIES"); ?></h3>
								<div id="company-related" class="company-cell" >
									<?php require_once 'related_business.php';?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Company Services -->
				<?php if((isset($this->package->features) && in_array(COMPANY_SERVICES,$this->package->features) || !$appSettings->enable_packages)
					&& isset($this->services) && count($this->services) && $appSettings->enable_services){
					?>
					<div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<h3><i class="fa dir-icon-wrench"></i> <?php echo JText::_("LNG_SERVICES"); ?></h3>
								<div id="company-services">
									<?php require_once 'listing_services.php';?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Business Events -->
				<?php if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
							&& isset($this->events) && count($this->events) && $appSettings->enable_events) { ?>
					<div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<h3><i class="fa dir-icon-calendar"></i> <?php echo JText::_("LNG_COMPANY_EVENTS"); ?></h3>
								<div id="company-events">
									<?php require_once 'listing_events.php';?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Associated Events -->
				<?php if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
					&& isset($this->associatedEvents) && count($this->associatedEvents) && $appSettings->enable_events) { ?>
					<div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<h3><i class="fa dir-icon-calendar"></i> <?php echo JText::_("LNG_ASSOCIATED_EVENTS"); ?></h3>
								<div id="associated-events">
									<?php require_once 'listing_associated_events.php';?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

                <!-- Company Testimonials -->
                <?php if((isset($this->package->features) && in_array(TESTIMONIALS,$this->package->features) || !$appSettings->enable_packages)
                    && !empty($this->companyTestimonials)) { ?>
                    <div class="company-style-box">
                        <div class="row-fluid">
                            <div class="span12">
                                <h3><i class="fa dir-icon-quote-left "></i> <?php echo JText::_("LNG_TESTIMONIALS")?></h3>
                                <div id="company-testimonials">
                                    <?php require_once 'listing_testimonials.php'; ?>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

				<!-- Business Reviews -->
				<?php if($appSettings->enable_reviews) { ?>
					<div id="go-company-reviews" class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<h3><i class="fa dir-icon-check-square-o"></i> <?php echo JText::_("LNG_BUSINESS_REVIEWS"); ?></h3>
								<div id="company-reviews">
									<?php require_once 'listing_reviews.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

                <!-- Business Price List -->
                <?php if((isset($this->package->features) && in_array(SERVICES_LIST,$this->package->features) || !$appSettings->enable_packages)
                        && !empty($this->services_list) && count($this->services_list)) { ?>
                    <div id="company-price-list" class="company-style-box">
                        <div class="row-fluid">
                            <div class="span12">
                                <h3><i class="fa dir-icon-list-alt "></i> <?php echo JText::_("LNG_PRICE_LIST"); ?></h3>
                                <div id="company-reviews">
                                    <?php require_once 'listing_price_list.php'; ?>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Business Projects -->
                <?php if(!empty($this->companyProjects)) { ?>
                    <div id="company-projects" class="company-style-box">
                        <div class="row-fluid">
                            <div class="span12">
                                <h3 onclick="returnToProjects();" onmouseover =	"this.style.cursor='hand';this.style.cursor='pointer'" onmouseout = "this.style.cursor='default'" >
                                    <i class="fa dir-icon-briefcase"></i> <?php echo JText::_("LNG_PROJECTS"); ?>
                                </h3>
                                <div>
                                    <?php require_once 'listing_projects.php'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
			</div>
			<!-- SIDEBAR -->
			<div class="span4">
				<?php if($appSettings->social_profile && $this->company->userId!=0 && isset($this->claimDetails) && ($this->claimDetails->status == 1)){?>
				    <div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
							<h3><i class="fa dir-icon-user"></i> <?php echo JText::_("LNG_COMMUNITY_OWNER_PROFILE"); ?></h3>
								<?php require_once 'listing_profile.php'; ?>
							</div>
						</div>
					</div>
				<?php } ?>	

				<?php if((!isset($this->company->userId) || $this->company->userId == 0) && $appSettings->claim_business) { ?>
					<div class="company-style-box">
						<!-- Business Claim -->
						<div class="row-fluid">
							<div class="span12">
								<h3><i class="fa fa-check-square-o"></i> <?php echo JText::_("LNG_CLAIM_COMPANY"); ?></h3>
								<div><?php echo JText::_('LNG_CLAIM_COMPANY_TEXT')?></div><br/>
								<a href="javascript:claimCompany(<?php echo $user->id==0?"1":"0"?>)" class="ui-dir-button ui-dir-button-blue email">
									<span class="ui-button-text"><?php echo JText::_("LNG_CLAIM_COMPANY") ?></span>
								</a>
							</div>
						</div>
					</div>
				<?php } ?>
			
				<div class="company-style-box">
					<!-- Business Map -->
					<div class="row-fluid">
						<div class="span12">
							<div class="dir-map-image">
								<?php 
									$key="";
									if(!empty($appSettings->google_map_key))
									$key="&key=".$appSettings->google_map_key;
								?>
							
								<?php if((isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages) && !empty($this->company->latitude) && !empty($this->company->longitude)) { 
								    echo '<img alt="company map" src="https://maps.googleapis.com/maps/api/staticmap?center='.$db->escape($this->company->latitude).','.$db->escape($this->company->longitude).'&zoom=13&size=300x300&markers=color:blue|'.$db->escape($this->company->latitude).','.$db->escape($this->company->longitude).$key.'&sensor=false">';
								} ?>
							</div>
						</div>
					</div>

					<!-- Business Address -->
					<div class="row-fluid">
						<?php $address = JBusinessUtil::getAddressText($this->company);?>
                		<?php if(!empty($address)) { ?>
    						<div class="span12 dir-address">
    							<?php echo JBusinessUtil::getAddressText($this->company); ?>
    						</div>
    					<?php }?>
					</div>

					<!-- Business Contact Informations -->
					<div class="row-fluid">
						<div class="span12">
							<div class="company-info-details">
								<?php if(!empty($this->company->email) && $showData && $appSettings->show_email){?>
									<span itemprop="email">
										<i class="dir-icon-envelope"></i> <?php echo $this->escape($this->company->email) ?>
									</span>
									<br/>
								<?php } ?>

								<?php if($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
									<?php if(!empty($this->company->phone)) { ?>
										<span class="phone" itemprop="telephone">
											<i class="dir-icon-phone"></i> <a href="tel:<?php echo $this->escape($this->company->phone); ?>"><?php echo $this->escape($this->company->phone); ?></a>
										</span><br/>
									<?php } ?>
										
									<?php if(!empty($this->company->mobile)) { ?>
										<span class="phone" itemprop="telephone">
											<i class="dir-icon-mobile-phone"></i> <a href="tel:<?php echo $this->escape($this->company->mobile); ?>"><?php echo $this->escape($this->company->mobile); ?></a>
										</span><br/>
									<?php } ?>
										
									<?php if(!empty($this->company->fax)) {?>
										<span class="phone" itemprop="faxNumber">
											<i class="dir-icon-fax"></i> <?php echo $this->escape($this->company->fax) ?>
										</span>
									<?php } ?>
								<?php } ?>
							</div>
						</div>
					</div>

					<!-- Business Website & Business Contact -->
					<div class="row-fluid">
						<div class="span12">
							<div class="company-links">
								<?php if($showData && (isset($this->package->features) && in_array(WEBSITE_ADDRESS, $this->package->features) || !$appSettings->enable_packages) && !empty($company->website)) {
									if ($appSettings->enable_link_following){
									   $followLink = (isset($this->package->features) && in_array(LINK_FOLLOW,$this->package->features) && $appSettings->enable_packages)?'rel="follow"' : 'rel="nofollow"';
									}else{
									    $followLink ='';
									}?>
									<i class="dir-icon-globe"></i> 
									<a <?php echo $followLink ?> itemprop="url" class="website" title="<?php echo $this->escape($this->company->name) ?> Website" target="_blank" onclick="increaseWebsiteClicks(<?php echo $company->id ?>)" href="<?php echo $this->escape($company->website) ?>">
										<?php echo JText::_('LNG_WEBSITE') ?>
									</a>
								<?php } else { ?>
                                    <span style="display:none;" itemprop="url">
                                        <?php echo JBusinessUtil::getCompanyLink($this->company); ?>
                                    </span>
                                <?php } ?>
								<?php if($appSettings->enable_reporting){?>
									<div>
										<a href="javascript:showReportAbuse()" style="padding:0px;"><i class="fa fa-flag"></i> <?php echo JText::_('LNG_REPORT_LISTING'); ?></a>
									</div>
								<?php } ?>
								<div class="clear"></div>

								<?php if((isset($this->package->features) && in_array(CONTACT_FORM,$this->package->features) || !$appSettings->enable_packages) && !empty($company->email) && $appSettings->show_contact_form) { ?>
									<div class="span12">
										<br>
										<a href="javascript:contactCompany(<?php echo $showData?"1":"0"?>)"" class="ui-dir-button ui-dir-button-blue email">
											<span class="ui-button-text"><?php echo JText::_("LNG_CONTACT_COMPANY") ?></span>
										</a>
									</div>
									<div class="clear"></div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

				<!-- Business Contact Persons Informations -->
				<?php if($showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)) { ?>
					<?php if(!empty($this->companyContacts) && (!empty($this->companyContacts[0]->contact_name) || !empty($this->companyContacts[0]->contact_phone) )) { ?>
						<div class="company-style-box">
							<div class="row-fluid">
								<h3><i class="fa dir-icon-user"></i> <?php echo JText::_("LNG_CONTACT_PERSONS"); ?></h3>
								<div class="span12">
									<?php require_once 'contact_details.php'; ?>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php  } ?>

				<?php if($this->appSettings->enable_bookmarks) { ?>
                    <div class="span12 clear" style="display:none">
                        <!-- Business Add/Remove Bookmark -->
                        <?php if(!empty($company->bookmark)) { ?>
                            <a href="javascript:removeBookmark('companies')" title="<?php echo JText::_("LNG_REMOVE_BOOKMARK")?>" class="ui-dir-button ui-dir-button-red">
                                <span class="ui-button-text"><?php echo JText::_('LNG_REMOVE_BOOKMARK'); ?></span>
                            </a>
                        <?php } else { ?>
                            <a href="javascript:addBookmark(<?php echo $user->id==0 ? "1":"0" ?>)" title="<?php echo JText::_("LNG_ADD_BOOKMARK")?>" class="ui-dir-button ui-dir-button-blue">
                                <span class="ui-button-text"><?php echo JText::_('LNG_ADD_BOOKMARK'); ?></span>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>


				<!-- Business Social Networks -->
				<?php if(($showData && (isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
						&& ((!empty($this->company->linkedin) || !empty($this->company->youtube) ||!empty($this->company->facebook) || !empty($this->company->twitter) 
						    || !empty($this->company->googlep) || !empty($this->company->linkedin) || !empty($this->company->skype) || !empty($this->company->instagram) || !empty($this->company->pinterest || !empty($this->company->whatsapp)))))){ ?> 
					<div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<h3><i class="fa dir-icon-share-alt"></i> <?php echo JText::_("LNG_SOCIAL_NETWORK"); ?></h3>
								<?php require_once 'listing_social_networks.php'; ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Business Hours -->
				<?php if((isset($this->package->features) && in_array(OPENING_HOURS,$this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->company->business_hours) && $this->company->enableWorkingStatus) { ?>
					<div class="company-style-box">
						<div class="row-fluid">
							<div class="span12">
								<?php require_once 'listing_hours.php'; ?>
							</div>
						</div>
					</div>
				<?php } ?>
				
				<div class="listing-banners">
					<?php 
						jimport('joomla.application.module.helper');
						// this is where you want to load your module position
						$modules = JModuleHelper::getModules('dir-listing');
						$fullWidth = true;
						?>
						<?php if(isset($modules) && count($modules)>0) { ?>
							<div class="dir-company-module company-style-box">
								<?php 
								$fullWidth = false;
								foreach($modules as $module) {
									echo JModuleHelper::renderModule($module);
								} ?>
							</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>

<form name="tabsForm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" id="tabsForm" method="post">
 	 <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
	 <input type="hidden" name="task" value="companies.displayCompany" /> 
	 <input type="hidden" name="tabId" id="tabId" value="<?php echo $this->tabId?>" /> 
	 <input type="hidden" name="view" value="companies" /> 
	 <input type="hidden" name="layout2" id="layout2" value="" /> 
	 <input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
	 <input type="hidden" name="controller"	value="<?php echo JRequest::getCmd('controller', 'J-BusinessDirectory')?>" />
</form>

<script>
	jQuery(document).ready(function() {
		<?php if($showData && (isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages )
				&& !empty($this->company->latitude) && !empty($this->company->longitude)){ ?>
			loadMapScript();
		<?php }	?>

		<?php if(!empty($this->company->business_cover_image)) { ?>
			jQuery(".company-style-5-header-image").css({
				"background-image": "url('<?php echo JURI::root().PICTURES_PATH.$this->company->business_cover_image ?>')",
				"background-repeat": "no-repeat",
				"background-size": "cover",
				"background-position": "center"
				});
		<?php } ?>
	});
</script>

<?php require_once 'listing_util.php'; ?>