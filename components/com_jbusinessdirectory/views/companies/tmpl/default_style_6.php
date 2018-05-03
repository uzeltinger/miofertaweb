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
?>
<div class="company-container-style-6" itemscope itemtype="http://schema.org/LocalBusiness">

    <div class="dir-print">
        <a href="javascript:printCompany(<?php echo $this->company->id ?>, '<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=companies&tmpl=component"); ?>')"><i class="dir-icon-print"></i> <?php echo JText::_("LNG_PRINT")?></a>
    </div>
    <?php require_once 'breadcrumbs.php'; ?>

    <div class="company-header">
    	<div class="company-overlay"></div>
			<div class="socials-network">
                <?php if($showData && (isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
                && ((!empty($this->company->linkedin) || !empty($this->company->youtube) ||!empty($this->company->facebook) || !empty($this->company->twitter)
                    || !empty($this->company->googlep) || !empty($this->company->linkedin) || !empty($this->company->skype)|| !empty($this->company->instagram) || !empty($this->company->pinterest) || !empty($this->company->whatsapp)))) { ?>
                        <?php if(!empty($this->company->facebook)){ ?>
                            <a target="_blank" title="Follow us on Facebook" class="dir-icon-facebook" href="<?php echo $this->company->facebook ?>"></a>
                        <?php } ?>
                        <?php if(!empty($this->company->twitter)){ ?>
                            <a target="_blank" title="Follow us on Twitter" class="dir-icon-twitter" href="<?php echo $this->company->twitter ?>"></a>
                        <?php } ?>
                        <?php if(!empty($this->company->googlep)){ ?>
                            <a target="_blank" title="Follow us on Google" class="dir-icon-google" href="<?php echo $this->company->googlep ?>"></a>
                        <?php } ?>
                        <?php if(!empty($this->company->linkedin)){ ?>
                            <a target="_blank" title="Follow us on LinkedIn" class="dir-icon-linkedin" href="<?php echo $this->company->linkedin ?>"></a>
                        <?php } ?>
                        <?php if(!empty($this->company->skype)){ ?>
                            <a target="_blank" title="Skype" class="dir-icon-skype" href="skyupe:<?php echo $this->company->skype ?>"></a>
                        <?php } ?>
                        <?php if(!empty($this->company->youtube)){ ?>
                            <a target="_blank" title="Follow us on Youtube" class="dir-icon-youtube" href="<?php echo $this->company->youtube ?>"></a>
                        <?php } ?>
                        <?php if(!empty($this->company->instagram)){ ?>
                            <a target="_blank" title="Follow us on Instagram" class="dir-icon-instagram" href="<?php echo $this->company->instagram ?>"></a>
                        <?php } ?>
                        <?php if(!empty($this->company->pinterest)){ ?>
                            <a target="_blank" title="Follow us on Pinterest" class="dir-icon-pinterest" href="<?php echo $this->company->pinterest ?>"></a>
                        <?php } ?>
                        <?php if(!empty($this->company->whatsapp)){ ?>
                            <a target="_blank" title="Ping us on WhatsApp" class="dir-icon-whatsapp" href="whatsapp://send?text=<?php echo JText::_("LNG_HELLO")?>!&phone=<?php echo $this->company->whatsapp?>"></a>
                        <?php } ?>
                <?php } ?>
            </div>
            <div class="header-bottom row-fluid">
                <div class="span12">
                	<div class="company-header-details">
                		<div class="listing-name-container">
                            <h1 itemprop="name" class="company-name"><span><?php echo $this->company->name ?></span></h1>
                        </div>
						<div class="rating-container">
							<!-- Business Ratings -->
                             <?php if($appSettings->enable_ratings) { ?>
								<div class="company-info-rating" <?php echo !$appSettings->enable_ratings? 'style="display:none"':'' ?>>
                                    <?php if(count($this->reviews) > 0) { ?>
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
							</div>
							<div class="clear"></div>
						</div>
						
						<div class="dir-company-meta row-fluid">
							<div class="span7">
    							<?php $address = JBusinessUtil::getAddressText($this->company);?>
                    			<?php if(!empty($address)) { ?>
        							<div class="listing-address" itemprop="address">
        								<i class="dir-icon-map-marker"></i>
        								<?php echo $address; ?>
        							</div>
        						<?php }?>
    						</div>
    						
    						<div class="span5 links-container">
    							<?php require_once JPATH_COMPONENT_SITE."/include/social_share.php"; ?>
    							
    							<?php if($this->appSettings->enable_bookmarks) { ?>
    									<?php if(!empty($company->bookmark)) { ?>
    										<!-- Business Bookmarks -->
    										<a href="javascript:showUpdateBookmarkDialog(<?php echo $user->id==0?"1":"0"?>)"  title="<?php echo JText::_("LNG_UPDATE")?>" class=""><i class="dir-icon-heart"></i> <span><?php echo JText::_('LNG_UPDATE'); ?></span></a>
    									<?php } else {?>
    										<a href="javascript:addBookmark(<?php echo $user->id==0?"1":"0"?>)" title="<?php echo JText::_("LNG_SAVE")?>" class=""><i class="dir-icon-heart-o"></i> <span><?php echo JText::_('LNG_SAVE'); ?></span></a>
    									<?php } ?>
								<?php } ?>
								<span>
		                        	 <?php require_once JPATH_COMPONENT_SITE."/include/social_share.php"; ?>
		                        </span>
		                        
		                        <a class="round-border-buton" href="javascript:void(0)" onclick="addNewReviewOnTabs(<?php echo ($appSettings->enable_reviews_users && $user->id ==0) ?"1":"0"?>)"> <?php echo JText::_('LNG_WRITE_REVIEW') ?> <i class="dir-icon-angle-right"></i></a>
		                        
		                    </div>
						</div>
					</div>
				</div>
           </div>
    </div>

    <div class="company-details-container">
    	<div class="row-fluid">
            <div class="span8">
            	<div class="company-menu">
                    <nav>
                        <a id="details-link" href="javascript:showTabContent('company-details');" class="active"><?php echo JText::_("LNG_BUSINESS_DETAILS")?></a>
                        <?php
                        if($showData &&  (isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages )
                            && !empty($this->company->latitude) && !empty($this->company->longitude)) {
                            ?>
                            <a id="gmap-link" href="javascript:showTabContent('company-gmap');" class=""><?php echo JText::_("LNG_MAP")?></a>
                        <?php } ?>
        
                        <?php
                        if((isset($this->package->features) && in_array(COMPANY_SERVICES,$this->package->features) || !$appSettings->enable_packages)
                            && isset($this->services) && count($this->services) && $appSettings->enable_services){
                            ?>
                            <a id="services-link" href="javascript:showTabContent('company-services');" class=""><?php echo JText::_("LNG_SERVICES")?></a>
                        <?php } ?>
                
                 		<?php if($appSettings->enable_reviews){ ?>
                            <a id="reviews-link" href="javascript:showTabContent('company-reviews');" class=""><?php echo JText::_("LNG_REVIEWS")?></a>
                        <?php }?>
        
                        <?php
                        if((isset($this->package->features) && in_array(TESTIMONIALS,$this->package->features) || !$appSettings->enable_packages)
                            && !empty($this->companyTestimonials)){
                            ?>
                            <a id="testimonials-link" href="javascript:showTabContent('company-testimonials');" class=""><?php echo JText::_("LNG_TESTIMONIALS")?></a>
                        <?php } ?>
        
                        <?php if(!empty($this->companyProjects)){ ?>
                            <a id="projects-link" href="javascript:showTabContent('company-projects');" class=""><?php echo JText::_("LNG_PROJECTS")?></a>
                        <?php }?>
        
                        <?php
                        if((isset($this->package->features) && in_array(SERVICES_LIST,$this->package->features) || !$appSettings->enable_packages)
                            && !empty($this->services_list)){
                            ?>
                            <a id="price-list-link" href="javascript:showTabContent('company-price-list');"><?php echo JText::_("LNG_PRICE_LIST")?></a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
            <div class="span4">
            	<div class="dir-quick-links">
                	<div class="business-contact">
    				<?php if($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
    					<?php if(!empty($this->company->phone)) { ?>
    						<span class="phone" itemprop="telephone">
    							<i class="dir-icon-phone"></i> <a href="tel:<?php echo $this->escape($this->company->phone); ?>"><?php echo $this->escape($this->company->phone); ?></a>
    						</span>
    					<?php } ?>
    					<?php if(empty($this->company->phone) && !empty($this->company->mobile)) { ?>
    						<span class="phone" itemprop="telephone">
    							<i class="dir-icon-mobile-phone"></i> <a href="tel:<?php echo $this->escape($this->company->mobile); ?>"><?php echo $this->escape($this->company->mobile); ?></a>
    						</span><br/>
    					<?php } ?>
    				<?php }?>
    				
    				 <?php if($showData && (isset($this->package->features) && in_array(WEBSITE_ADDRESS, $this->package->features) || !$appSettings->enable_packages) && !empty($company->website)) {
                            if ($appSettings->enable_link_following){
                                $followLink = (isset($this->package->features) && in_array(LINK_FOLLOW,$this->package->features) && $appSettings->enable_packages)?'rel="follow"' : 'rel="nofollow"';
                            }else{
                                $followLink ="";
                            }?>
                            <span class="nowrap">
                                <i class="dir-icon-globe"></i>
                                <a <?php echo $followLink ?> itemprop="url" class="website" title="<?php echo $this->company->name?> Website" target="_blank" onclick="increaseWebsiteClicks(<?php echo $company->id ?>)" href="<?php echo $company->website ?>">
                                    <?php echo JText::_('LNG_WEBSITE') ?>
                                </a>
                            </span>
                        <?php } else { ?>
                            <span style="display:none;" itemprop="url">
                                <?php echo JBusinessUtil::getCompanyLink($this->company); ?>
                            </span>
                        <?php } ?>
                      </div>
            	</div>
            </div>
		</div>
        <!-- Company Details -->
        <div class="company-details-content row-fluid" id="company-details">
        	 <!-- Business Details -->
            <div class="span8">
            	<div class="company-style-box">
                    <div class="row-fluid">
                        <div class="span12">
                            <h3><i class="fa dir-icon-newspaper-o"></i> <?php echo JText::_("LNG_COMPANY_DETAILS"); ?></h3>
                            <!-- Business Slogan -->
                            <?php if(isset($this->company->slogan) && strlen($this->company->slogan)>2) { ?>
                                <p class="business-slogan"><?php echo  $this->company->slogan; ?> </p>
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
                                <!-- Listing Categories -->
                                <?php if(!empty($this->company->categories)){?>
                                    <dt><?php echo JText::_('LNG_CATEGORIES')?>:</dt>
                                    <dd class="dir-keywords">
                                        <ul>
                                            <?php
                                            foreach($this->company->categories as $i=>$category){
                                                ?>
                                                <li> <a href="<?php echo JBusinessUtil::getCategoryLink($category[0], $category[2]) ?>"><?php echo $category[1]?></a><?php echo $i<(count($this->company->categories)-1)? ',&nbsp;':'' ?></li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                    </dd>
                                <?php } ?>
    
                                <!-- Listing Type -->
                                <?php if(!empty($this->company->typeName)) { ?>
                                    <dt><?php echo JText::_('LNG_TYPE'); ?>:</dt>
                                    <dd><?php echo $this->company->typeName; ?></dd>
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
                                    <dd class="dir-keywords">
                                        <ul>
                                            <?php
                                            $keywords =  explode(',', $this->company->keywords);
                                            for($i=0; $i<count($keywords); $i++) { ?>
                                                <li>
                                                    <a  href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=search&searchkeyword='.$keywords[$i].$menuItemId) ?>"><?php echo $keywords[$i]?><?php echo $i<(count($keywords)-1)? ',&nbsp;':'' ?></a>
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
    
                                <?php if((isset($this->package->features) && in_array(CUSTOM_TAB,$this->package->features) || !$appSettings->enable_packages)
                                    && !empty($this->company->custom_tab_name)){ ?>
                                    <dt><?php echo $this->company->custom_tab_name ?></dt>
                                    <dd><?php echo JHTML::_("content.prepare", $this->company->custom_tab_content);	?>&nbsp;</dd>
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
            
                <!-- Listing Gallery -->
                <?php if((isset($this->package->features) && in_array(IMAGE_UPLOAD,$this->package->features) || !$appSettings->enable_packages)
                    && !empty($this->pictures)){?>
                    <div class="company-style-box">
                        <div class="row-fluid">
                            <div class="span12">
                                <?php require_once JPATH_COMPONENT_SITE."/include/image_gallery.php";  ?>
                            </div>
                        </div>
                     </div>
                <?php } ?>
                
                 <!-- Listing Videos -->
                <?php if((isset($this->package->features) && in_array(VIDEOS,$this->package->features) || !$appSettings->enable_packages)
                    && isset($this->videos) && count($this->videos)>0) { ?>
                    <div class="company-style-box">
                        <div class="row-fluid">
                            <div class="span12">
                                <h3><i class="fa dir-icon-video-camera"></i> <?php echo JText::_("LNG_VIDEOS")?></h3>
                                <div>
                                    <?php require_once 'listing_videos.php'; ?>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }	?>
            
             	<!-- Listing Offers -->
                <?php if((isset($this->package->features) && in_array(COMPANY_OFFERS,$this->package->features) || !$appSettings->enable_packages)
                    && isset($this->offers) && count($this->offers) && $appSettings->enable_offers) { ?>
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
                
                  <!-- Listing Events -->
                <?php if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
                    && isset($this->events) && count($this->events) && $appSettings->enable_events) { ?>
                     <div class="company-style-box">
                        <div class="row-fluid">
                            <div class="span12">
                                <h3><i class="fa dir-icon-calendar"></i> <?php echo JText::_("LNG_COMPANY_EVENTS"); ?></h3>
                                <div>
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
                                <div>
                                    <?php require_once 'listing_associated_events.php';?>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
        
                <!-- Listing Sounds -->
                <?php if((isset($this->package->features) && in_array(SOUNDS_FEATURE,$this->package->features) || !$appSettings->enable_packages)
                    && !empty($this->sounds)) { ?>
                    <div class="company-style-box">
                        <div class="row-fluid">
                            <div class="span12">
                                <h3><i class="fa dir-icon-sound"></i> <?php echo JText::_("LNG_SOUNDS")?></h3>
                                <div>
                                    <?php require_once 'listing_sounds.php'; ?>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }	?>
        
                 <!-- Related Listings -->
                <?php if((isset($this->package->features) && in_array(RELATED_COMPANIES,$this->package->features) || !$appSettings->enable_packages)
                    && isset($this->realtedCompanies) && count($this->realtedCompanies)){?>
                    <div class="company-style-box">
                        <div class="row-fluid">
                            <div class="span12">
                                <h3><i class="fa dir-icon-tag"></i> <?php echo JText::_("LNG_RELATED_COMPANIES"); ?></h3>
                                <div class="company-cell" >
                                    <?php require_once 'related_business.php';?>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            
            <div class="span4">
            	 <?php if((!isset($this->company->userId) || $this->company->userId == 0) && $appSettings->claim_business) { ?>
                	<div class="company-style-box">
                		<div class="claim-listing-wrapper">
                            <div class="claim-listing-content">
                                <h4 class="claim-listing-title">Is this your business?</h4>
                                <p class="claim-listing-description">Claim listing is the best way to manage and protect your business</p>
                            </div>
                           <a class="round-border-buton" href="javascript:claimCompany(<?php echo $user->id==0?"1":"0"?>)"><?php echo JText::_('LNG_CLAIM_COMPANY')?></a>
        				</div>
        			</div>            	
           		<?php  } ?>
           		
                <div class="company-style-box">
                     <!-- Business Hours -->
                    <?php if((isset($this->package->features) && in_array(OPENING_HOURS,$this->package->features) || !$appSettings->enable_packages)
                        && !empty($this->company->business_hours) && $this->company->enableWorkingStatus) { ?>
                        <div class="row-fluid">
                            <div class="span12">
                                <?php require_once 'listing_hours.php'; ?>
                            </div>
                        </div>
                    <?php } ?>
				</div>
				
                <!-- Business Contact Informations -->
                <div class="company-style-box ">
                    <div class="row-fluid">
                        <div class="span12">
                            <!-- Business Map -->
                            <?php if(!empty($this->company->latitude) && !empty($this->company->longitude)) { ?>
                                <div class="dir-map-image">
                                    <?php
                                    $key="";
                                    if(!empty($appSettings->google_map_key))
                                        $key="&key=".$appSettings->google_map_key;
                                    ?>
            
                                    <?php if((isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages) && !empty($this->company->latitude) && !empty($this->company->longitude)) {
                                        echo '<img alt="company map" src="https://maps.googleapis.com/maps/api/staticmap?center='.$this->company->latitude.','.$this->company->longitude.'&zoom=13&size=300x300&markers=color:blue|'.$this->company->latitude.','.$this->company->longitude.$key.'&sensor=false">';
                                    } ?>
                                </div>
                            <?php } ?>
                            
                            <!-- Business Address -->
                            <?php $address = JBusinessUtil::getAddressText($this->company);?>
                            <?php if(!empty($address)){?>
                            <div class="row-fluid">
        						<div class="">
        							<i class="dir-icon-map-marker dir-icon-sized"></i><address><?php echo $address; ?></address>
        						</div>
        					</div>
                            <?php } ?>

                            <?php if($showData &&  !empty($this->company->email) && $appSettings->show_email){?>
                                <div class="" itemprop="email">
	                                <i class="dir-icon-envelope dir-icon-sized"></i><?php echo $this->company->email; ?>
                                </div>
                            <?php } ?>

                            <?php if($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                <?php if(!empty($this->company->phone)) { ?>
                                    <div class="phone" itemprop="telephone">
                                    	<i class="dir-icon-phone dir-icon-sized"></i><a href="tel:<?php  echo $this->company->phone; ?>" style="color:black;"><?php  echo $this->company->phone; ?></a>
                                    </div>
                                <?php } ?>

                                <?php if(!empty($this->company->mobile)) { ?>
                                    <div class="phone" itemprop="telephone">
	                                    <i class="dir-icon-mobile-phone dir-icon-large dir-icon-sized"></i><a href="tel:<?php  echo $this->company->mobile; ?>" style="color:black;"><?php  echo $this->company->mobile; ?></a>
                                    </div>
                                <?php } ?>

                                <?php if(!empty($this->company->fax)) {?>
                                    <span class="phone" itemprop="faxNumber">
											<i class="dir-icon-fax dir-icon-sized"></i> <?php echo $this->company->fax?>
										</span>
                                <?php } ?>
                            <?php } ?>
                            
                            <!-- Business Contact Persons Informations -->
                            <?php if($showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                <?php if(!empty($this->companyContacts) && (!empty($this->companyContacts[0]->contact_name) || !empty($this->companyContacts[0]->contact_phone) )) { ?>
                                    <div class="row-fluid side-details">
                                        <i class="dir-icon-user"></i>
                                        <div class="contact-details-wrap">
                                        	<?php require_once 'contact_details.php'; ?>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                <?php } ?>
                            <?php  } ?>
                        </div>
                    </div>
                </div>
                
                <div class="company-style-box">
                    <?php if((isset($this->package->features) && in_array(CONTACT_FORM,$this->package->features) || !$appSettings->enable_packages) && !empty($company->email) && $appSettings->show_contact_form) { ?>
                       <div class="row-fluid">
                            <a href="javascript:contactCompany(<?php echo $showData?"1":"0"?>)" class="ui-dir-button ui-dir-button-blue email">
                                <span class="ui-button-text"><?php echo JText::_("LNG_CONTACT_COMPANY") ?></span>
                            </a>
                        </div>
                        <div class="clear"></div>
                    <?php } ?>
				</div>
				
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

        

        <!-- Listing Map -->
        <?php if($showData && (isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages )
            && !empty($this->company->latitude) && !empty($this->company->longitude)){ ?>
            <div id="company-gmap" class="company-style-6-content">
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
            </div>
        <?php } ?>

       

        <!-- Listing Services -->
        <?php if((isset($this->package->features) && in_array(COMPANY_SERVICES,$this->package->features) || !$appSettings->enable_packages)
            && isset($this->services) && count($this->services) && $appSettings->enable_services){
            ?>
            <div id="company-services" class="company-style-6-content">
            	<div class="company-style-box">
                    <div class="row-fluid">
                        <div class="span12">
                            <h3><i class="fa dir-icon-wrench"></i> <?php echo JText::_("LNG_SERVICES"); ?></h3>
                            <div>
                                <?php require_once 'listing_services.php';?>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
        <?php } ?>


        <!-- Listing Reviews -->
        <?php if($appSettings->enable_reviews) { ?>
         	<div id="company-reviews" >
            	<div class="company-style-box">
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
            </div>
        <?php } ?>
       

        <!-- Listing Testimonials -->
        <?php if((isset($this->package->features) && in_array(TESTIMONIALS,$this->package->features) || !$appSettings->enable_packages)
            && !empty($this->companyTestimonials)) { ?>
            <div id="company-testimonials" class="company-style-6-content">
            	<div class="company-style-box">
                    <div class="row-fluid">
                        <div class="span12">
                            <h3><i class="fa dir-icon-quote-left "></i> <?php echo JText::_("LNG_TESTIMONIALS"); ?></h3>
                            <div>
                                <?php require_once 'listing_testimonials.php';?>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
        <?php } ?>
        
       
        <!-- Listing Price List -->
        <?php if((isset($this->package->features) && in_array(SERVICES_LIST,$this->package->features) || !$appSettings->enable_packages)
            && !empty($this->services_list)) { ?>
            <div id="company-price-list" class="company-style-6-content">
            	<div class="company-style-box">
                    <div class="row-fluid">
                        <div class="span12">
                            <h3><i class="fa dir-icon-list-alt "></i> <?php echo JText::_("LNG_PRICE_LIST"); ?></h3>
                            <div>
                                <?php require_once 'listing_price_list.php'; ?>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Listing Projects -->
        <?php if(!empty($this->companyProjects)) { ?>
            <div id="company-projects" class="company-style-6-content">
            	<div class="company-style-box">
	                <div class="row-fluid">
                        <div class="span12">
                            <div>
                                <?php require_once 'listing_projects.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
        <?php } ?>
    </div>
</div>

<script>
    <?php if(!empty($this->company->business_cover_image)) { ?>
    jQuery(".company-header").css({
        "background-image": "url('<?php echo JURI::root().PICTURES_PATH.$this->company->business_cover_image ?>')",
        "background-repeat": "no-repeat",
        "background-size": "cover",
        "background-position": "center"
    });
    <?php } ?>

    jQuery(document).ready(function() {
        <?php if($showData && (isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages )
            && !empty($this->company->latitude) && !empty($this->company->longitude)){ ?>
        loadMapScript();
        <?php }	?>

        var length = jQuery(".company-menu a").length;
        jQuery(".company-menu a")[0].addClass("first-tab");
        jQuery(".company-menu a")[length - 1].addClass("last-tab");

        jQuery(".company-menu a").each(function () {
            var name = jQuery(this).attr('id');
            name = name.substring(0, name.lastIndexOf("-"));

            if (name !== "details")
                jQuery('#company-' + name).hide();
        });
    });
</script>

<?php require_once 'listing_util.php'; ?>