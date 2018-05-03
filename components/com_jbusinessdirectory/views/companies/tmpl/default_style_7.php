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

<div id="company-style-7-container" itemscope itemtype="http://schema.org/LocalBusiness">
    <div class="dir-print">
        <a href="javascript:printCompany(<?php echo $this->company->id ?>, '<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=companies&tmpl=component"); ?>')"><i class="dir-icon-print"></i> <?php echo JText::_("LNG_PRINT")?></a>
    </div>
    <?php require_once 'breadcrumbs.php';?>
	<div id="company-style-7-header">
		<div class="row-fluid">
			<!-- Business Categories -->
			<div class="company-style-7-header-image span12">
				<div class="company-style-7-header-info row-fluid">
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
				<div class="company-style-7-header-menu">
    				<ul class="menu">
    					<li>
            				<a id="business-link" href="#company-style-7-body" class="active"><?php echo JText::_("LNG_PROFILE")?></a>
            			</li>
            			<?php 
            				if((isset($this->package->features) && in_array(IMAGE_UPLOAD,$this->package->features) || !$appSettings->enable_packages)
            					&& isset($this->pictures) && count($this->pictures)>0){ 
            			?>	<li>
            					<a id="gallery-link" href="#image-gallery" class=""><?php echo JText::_("LNG_GALLERY")?></a>
            				</li>
            			<?php } ?>
            			<?php 
        				    if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
        				    	&& isset($this->events) && count($this->events) && $appSettings->enable_events){
        		      	?>	
        		      			<li>
            						<a id="events-link" href="#company-events" class=""><?php echo JText::_("LNG_EVENTS")?></a>
            					</li>
            			<?php } ?>
        			</ul>
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

	<div id="company-style-7-body">
		<div class="row-fluid">
			<div class="span6">
				<div class="company-style-box">
    				<div class="row-fluid">
    					<div class="span6">
            				<div class="row-fluid">
            					<h3><?php echo JText::_("LNG_SERVICES")?></h3>
            					<?php if(!empty($this->company->typeName)){?>
            						<?php $types = explode(",",$this->company->typeName); ?>
            						<ul class="company-services">
            						<?php foreach($types as $type){?>
            							<li>
            								<?php echo $type ?>
            							</li>
            						<?php } ?>
            						</ul>
            					<?php } ?>
            				</div>
            			</div>
            			<div class="span6">
            				<div class="row-fluid">
            					<h3><?php echo JText::_("LNG_LOCATIONS")?></h3>
            				</div>
            			</div>
    				</div>
    			</div>
			</div>
			<div class="span6">
				<div class="company-style-box">
    				<div class="row-fluid">
    					<h3><?php echo JText::_("LNG_BUSINESS_DETAILS")?></h3>
    						<div class="dir-address">
								<span itemprop="address">
									<!-- Business Address -->
									<?php echo JBusinessUtil::getAddressText($this->company); ?>
								</span>
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
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="company-style-box">
    				<div class="row-fluid">
    					<div class="span12">
            				<?php echo JText::_("LNG_OWNER_COACH")?>
            			</div>
            			<?php require_once 'listing_attributes.php'; ?>
    				</div>
    				<div class="row-fluid">
    					<div class="span6">
    						<?php if(!empty($this->company->establishment_year)) { ?>
            					<?php echo JText::_("LNG_ESTABLISHMENT_YEAR")?> <?php echo $this->company->establishment_year; ?>
            				<?php } ?>
            			</div>
            			<div class="span6">
                			<?php if(!empty($this->company->employees)) { ?>
                				<?php echo JText::_("LNG_EMPLOYEES")?> <?php echo $this->company->employees; ?>
            				<?php } ?>
            			</div>
    				</div>

    			</div>
    			
			</div>
			<div class="span6">
				<div class="company-style-box">
					<div class="row-fluid">
						<div class="span12">
							<h3><i class="fa dir-icon-share-alt"></i> <?php echo JText::_("LNG_SOCIAL_NETWORK"); ?></h3>
							<?php require_once 'listing_social_networks.php'; ?>
						</div>
					</div>
				</div>
				<div class="company-style-box">
					<div class="row-fluid">
						<div class="span12">
							<h3><i class="fa dir-icon-share-alt"></i> <?php echo JText::_("LNG_BUSINESS_DESCRIPTION"); ?></h3>
							<?php if(!empty($this->company->description) && (isset($this->package->features) && in_array(DESCRIPTION,$this->package->features) || !$appSettings->enable_packages)) { ?>
								<?php echo JHTML::_("content.prepare", $this->company->description); ?>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="company-style-box">
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
				</div>
			</div>
		</div>
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
        
        <!-- Business Gallery -->
		<?php if((isset($this->package->features) && in_array(IMAGE_UPLOAD,$this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->pictures)){?>
			<div id="image-gallery" class="company-style-box">
				<div class="row-fluid">
					<div class="span12">
						<h3><i class="fa dir-icon-camera-retro"></i> <?php echo JText::_("LNG_GALLERY"); ?></h3>
						<?php require_once JPATH_COMPONENT_SITE."/include/image_gallery.php";  ?>
					</div>
				</div>
			</div>
		<?php } ?>

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
			<div id="company-events" class="company-style-box">
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
			jQuery(".company-style-7-header-image").css({
				"background-image": "url('<?php echo JURI::root().PICTURES_PATH.$this->company->business_cover_image ?>')",
				"background-repeat": "no-repeat",
				"background-size": "cover",
				"background-position": "center"
				});
		<?php } ?>
	});
</script>

<?php require_once 'listing_util.php'; ?>