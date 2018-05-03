<?php 
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 

defined('_JEXEC' ) or die('Restricted access' );

require_once 'header.php';
require_once JPATH_COMPONENT_SITE.'/classes/attributes/attributeservice.php';
?>
<?php
    require_once 'breadcrumbs.php';
?>

<div id="listing-style-1" class="company-details-tabs" itemscope itemtype="http://schema.org/LocalBusiness">
<div class="row-fluid">
	<div class="span8">
    	<h1 itemprop="name">
    		<?php  echo isset($this->company->name)?$this->company->name:"" ; ?>
    	</h1>
    	<?php if(!empty($this->company->slogan)){?>
    		<div class="business-slogan"><?php echo  $this->company->slogan ?> </div>
    	<?php }?>
    
    	<div class="company-info-rating">
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
            <div class="company-info-average-rating">
            <?php if($appSettings->enable_ratings) { ?>
                    <div class="rating">
                        <div id="rating-average" title="<?php echo $this->company->review_score?>"></div>
                    </div>
            <?php } ?>
            <?php if($appSettings->enable_reviews) { ?>
                    <div class="review-count">
                        <?php if(count($this->reviews)){ ?>
                            <a href="javascript:void(0)" onclick="jQuery('#dir-tab-3').click()"><?php echo count($this->reviews)?> <?php echo JText::_('LNG_REVIEWS') ?></a>
                            &nbsp;|&nbsp;
                            <a href="javascript:void(0)" onclick="addNewReviewOnTabs(<?php echo ($appSettings->enable_reviews_users && $user->id ==0) ?"1":"0"?>)"> <?php echo JText::_('LNG_WRITE_REVIEW') ?></a>
                        <?php } else{ ?>
                            <a href="javascript:void(0)" onclick="addNewReviewOnTabs(<?php echo ($appSettings->enable_reviews_users && $user->id ==0) ?"1":"0"?>)" ><?php echo JText::_('LNG_BE_THE_FIRST_TO_REVIEW') ?></a>
                        <?php }?>
                    </div>
            <?php } ?>
            </div>
    	</div>
	</div>
	<div class="span4">	
        <div class="dir-print right">
            <a href="javascript:printCompany(<?php echo $this->company->id ?>, '<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=companies&tmpl=component"); ?>')"><i class="dir-icon-print"></i> <?php echo JText::_("LNG_PRINT")?></a>
        </div>

		<?php require_once JPATH_COMPONENT_SITE."/include/social_share.php"?>
		<div class="clear"></div>
    	<div class="right">
    		<?php if((!isset($this->company->userId) || $this->company->userId == 0) && $appSettings->claim_business){ ?>
        		<div class="claim-container" id="claim-container">
        			<div class="claim-btn">
        				<a href="javascript:claimCompany(<?php echo $user->id==0?"1":"0"?>)"><?php echo JText::_('LNG_CLAIM_COMPANY')?></a>
        			</div>
        		</div>
    		<?php  } ?>
    	</div>	
	</div>
</div>
<div class="row-fluid">
	<div id="company-info" class="dir-company-info span4">
		<?php if(isset($this->package->features) && in_array(SHOW_COMPANY_LOGO,$this->package->features) || !$appSettings->enable_packages){ ?>
			<div class="company-image" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
				<?php if(isset($this->company->logoLocation) && $this->company->logoLocation!=''){?>
					<img title="<?php echo $this->escape($this->company->name) ?>" alt="<?php echo $this->escape($this->company->name) ?>" src="<?php echo JURI::root().PICTURES_PATH.$this->company->logoLocation ?>" itemprop="contentUrl">
				<?php }else{ ?>
					<img title="<?php echo $this->escape($this->company->name) ?>" alt="<?php echo $this->escape($this->company->name) ?>" src="<?php echo JURI::base() ."components/".JBusinessUtil::getComponentName().'/assets/no_image.jpg' ?>" itemprop="contentUrl">
				<?php } ?>
				<?php if($this->appSettings->enable_bookmarks) { ?>
					<div id="bookmark-container">
					<?php if(!empty($company->bookmark)){?>
						<a href="javascript:showUpdateBookmarkDialog(<?php echo $user->id==0?"1":"0"?>)"  title="<?php echo JText::_("LNG_UPDATE_BOOKMARK")?>" class="bookmark "><i class="dir-icon-heart"></i></a>
					<?php }else{?>
						<a href="javascript:addBookmark(<?php echo $user->id==0?"1":"0"?>)"  title="<?php echo JText::_("LNG_ADD_BOOKMARK")?>" class="bookmark "><i class="dir-icon-heart-o"></i></a>
					<?php } ?>
					</div>
				<?php } ?>
			
				<?php
				if(false && $this->company->enableWorkingStatus && (!$appSettings->enable_packages || isset($this->package->features) && in_array(OPENING_HOURS,$this->package->features) && $this->company->enableWorkingStatus)) {
					if ($this->company->workingStatus){?>
						<div class="ribbon-open"><span><?php echo JText::_("LNG_OPEN")?></span></div>
					<?php } else{ ?>
						<div class="ribbon-close"><span><?php echo JText::_("LNG_CLOSED")?></span></div>
					<?php } ?>
				<?php } ?>
			</div>
		<?php } ?>
		<div class="company-info-container" >
			
			
			<?php if($appSettings->enable_reporting){?>
				<div>
					<a href="javascript:showReportAbuse()" style="float:left;padding:1px;"><?php echo JText::_('LNG_REPORT_LISTING'); ?></a>
				</div>
				
			<?php } ?>

			<div class="clear"></div>
			<div>		
				<div id="company-info-details" class="company-info-details">
					<?php $address = JBusinessUtil::getAddressText($this->company);?>
					<ul class="company-contact">
						<li>
                            <?php if(!empty($address)) { ?>
							<span itemprop="address">
								<i class="dir-icon-map-marker"></i><?php echo $this->escape($address) ?>
							</span>
                            <?php } ?>
						</li>
						<li>
							<?php if($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages )) { ?>
								<?php if(!empty($this->company->phone)) { ?>
									<span itemprop="telephone">
										<i class="dir-icon-phone"></i><a href="tel:<?php echo $this->escape($this->company->phone); ?>"><?php echo $this->escape($this->company->phone); ?></a>
									</span>
								<?php } ?>
									
								<?php if(!empty($this->company->mobile)) { ?>
									<span itemprop="telephone">
										<i class="dir-icon-mobile-phone"></i><a href="tel:<?php echo $this->escape($this->company->mobile); ?>"><?php echo $this->escape($this->company->mobile); ?></a>
									</span>
								<?php } ?>
									
								<?php if(!empty($this->company->fax)) {?>
									<span itemprop="faxNumber">
										<i class="dir-icon-fax"></i> <?php echo $this->escape($this->company->fax) ?>
									</span>
								<?php } ?>
							<?php } ?>
						</li>
						<li>
							<?php if(!empty($this->company->email) && $showData && $appSettings->show_email){?>
								<span itemprop="email">
									 <i class="dir-icon-envelope"></i> <?php echo $this->escape($this->company->email) ?>
								</span>
							<?php } ?>
						</li>
						<li>
							<?php if($showData && (isset($this->package->features) && in_array(WEBSITE_ADDRESS,$this->package->features) || !$appSettings->enable_packages) && !empty($this->company->website)){
								if ($appSettings->enable_link_following){
								$followLink = (isset($this->package->features) && in_array(LINK_FOLLOW,$this->package->features) && $appSettings->enable_packages)?'rel="follow"' : 'rel="nofollow"';
								}else{
									$followLink ="";
								}?>
								<span>
									<i class="dir-icon-globe"></i>	<a <?php echo $followLink ?> itemprop="url" title="<?php echo $this->escape($this->company->name) ?> Website" target="_blank" onclick="increaseWebsiteClicks(<?php echo $company->id ?>)" href="<?php echo $this->escape($company->website) ?>"> <?php echo $company->website ?></a>
								</span>
							<?php } else { ?>
                                <span style="display:none;" itemprop="url">
                                    <?php echo JBusinessUtil::getCompanyLink($this->company); ?>
                                </span>
                            <?php } ?>
						</li>
					</ul>
					<div class="clear" ></div>
					<?php if($showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)) { ?>
							<?php if(!empty($this->companyContacts) && (!empty($this->companyContacts[0]->contact_name) || !empty($this->companyContacts[0]->contact_phone) )) { ?>
								<h4 class="contact"><?php echo count($this->companyContacts)>1?JText::_('LNG_CONTACT_PERSONS'):JText::_('LNG_CONTACT_PERSON'); ?></h4>
								<ul>
									<li>
										
										<?php require_once 'contact_details.php'; ?>
									</li>
								</ul>
							<?php } ?>
						<?php } ?>
						
					<div class="clear" ></div>
					<div class="listing-details">
    					<div class="classification">
    						<div class="categories">
    							<?php if(!empty($this->company->typeName)){ ?>
    							<?php echo JText::_('LNG_TYPE')?>: <span><?php echo $this->company->typeName?></span>
    							<?php } ?>	
    						</div>
    					</div>
    					
    					<?php if(!empty($this->company->categories)){?>
    						<div class="classification">
    							<div>
    								<ul class="business-categories">
    									<li><?php echo JText::_('LNG_CATEGORIES')?>:&nbsp;</li>
    									<?php
    										foreach($this->company->categories as $i=>$category){
    											?>
    												<li> <a href="<?php echo JBusinessUtil::getCategoryLink($category[0], $category[2]) ?>"><?php echo $category[1]?></a><?php echo $i<(count($this->company->categories)-1)? ',&nbsp;':'' ?></li>
    											<?php 
    										}
    									?>
    								</ul>
    							</div>
    						</div>
    					<?php } ?>
    					
    					<?php if(!empty($this->company->establishment_year)){?>
    						<div class="classification">
    								<span><?php echo " ".JText::_('LNG_ESTABLISHMENT_YEAR') ?>: <?php echo " ".$this->company->establishment_year;?></span>
    						</div>				
    					<?php }?>
    
    					<?php if(!empty($this->company->employees)){?>
    						<div class="classification">
    							<span>
    								<?php echo " ".JText::_('LNG_EMPLOYEES') ?>: <?php echo " ".$this->company->employees;?>
    							</span>
    						</div>
    					<?php }?>
    
    					<?php if(!empty($this->company->keywords)){?>
    						<div class="classification">
    							<div>
    								<ul class="business-categories">
    									<li><?php echo JText::_('LNG_KEYWORDS')?>:&nbsp;</li>
    									<?php 
    									$keywords =  explode(',', $this->company->keywords);
    									for($i=0; $i<count($keywords); $i++) { ?>
    										<li>
    											<a  href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=search&searchkeyword='.$keywords[$i].$menuItemId) ?>"><?php echo $keywords[$i]?><?php echo $i<(count($keywords)-1)? ',&nbsp;':'' ?></a>
    										</li>
    									<?php 
    									} ?>
    								</ul>
    							</div>
    						</div>
    					<?php } ?>
    					
    					<div class="clear"></div>
    
    					<?php if($showData && $appSettings->enable_attachments && (isset($this->package->features) && in_array(ATTACHMENTS, $this->package->features) || !$appSettings->enable_packages)) { ?>
    						<?php if(!empty($this->company->attachments)) { ?>
    							<h4 class="contact"><?php echo JText::_('LNG_ATTACHMENTS') ?></h4>
    							<?php require "listing_attachments.php" ?>
    							<div class="clear"></div>
    						<?php } ?>
    					<?php } ?>
    
                        <div class="classification">
                            <?php require_once 'listing_attributes.php'; ?>
                        </div>
                        <?php
                        if($showData && (isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
                            && ((!empty($this->company->linkedin) || !empty($this->company->youtube) ||!empty($this->company->facebook) || !empty($this->company->twitter)
                                || !empty($this->company->googlep) || !empty($this->company->linkedin) || !empty($this->company->skype)|| !empty($this->company->instagram) || !empty($this->company->pinterest) || !empty($this->company->whatsapp)))) {
                            require_once 'listing_social_networks.php';
                        }
                        ?>
    					<div class="clear"></div>
    					<?php if((isset($this->package->features) && in_array(CONTACT_FORM,$this->package->features) || !$appSettings->enable_packages) && !empty($this->company->email) && $appSettings->show_contact_form){ ?>
    							<button type="button" class="ui-dir-button" onclick="contactCompany(<?php echo $showData?"1":"0"?>)">
    								<span class="ui-button-text"><i class="dir-icon-edit"></i><?php echo JText::_("LNG_CONTACT_COMPANY")?></span>
    							</button>
    					<?php } ?>
    					<div class="clear"></div>
    				</div>
				</div>
			</div>
		</div>
		
	</div>
	<div id="tab-panel" class="dir-tab-panel span8">
		<div id="tabs" class="clearfix">
			<ul class="tab-list">
				<?php
					$tabs = array();
				    if (isset($this->package->features) && in_array(DESCRIPTION,$this->package->features) || !$appSettings->enable_packages) {
					    $tabs[1] = JText::_('LNG_BUSINESS_DETAILS');
				    }
					if($showData && (isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages )
							&& !empty($this->company->latitude) && !empty($this->company->longitude)){ 
						$tabs[2]=JText::_('LNG_MAP');
					}
					if($appSettings->enable_reviews){
						$tabs[3]=JText::_('LNG_REVIEWS');
					}
					if((isset($this->package->features) && in_array(IMAGE_UPLOAD,$this->package->features) || !$appSettings->enable_packages)
							&& !empty($this->pictures)){
						$tabs[4]=JText::_('LNG_GALLERY');
					}
					if((isset($this->package->features) && in_array(VIDEOS,$this->package->features) || !$appSettings->enable_packages)
							&& isset($this->videos) && count($this->videos)>0){
						$tabs[5]=JText::_('LNG_VIDEOS');
					}
					if((isset($this->package->features) && in_array(COMPANY_OFFERS,$this->package->features) || !$appSettings->enable_packages)
							&& isset($this->offers) && count($this->offers) && $appSettings->enable_offers){
						$tabs[6]=JText::_('LNG_OFFERS');
					}
					
					if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
							&& isset($this->events) && count($this->events) && $appSettings->enable_events){
						$tabs[7]=JText::_('LNG_EVENTS');
					}
					
					if(!empty($this->company->locations) && $appSettings->show_secondary_locations == 1 
							&& (isset($this->package->features) && in_array(SECONDARY_LOCATIONS, $this->package->features) || !$appSettings->enable_packages)){ 
						$tabs[8]=JText::_('LNG_COMPANY_LOCATIONS');
					}
					
					if((isset($this->package->features) && in_array(OPENING_HOURS,$this->package->features) || !$appSettings->enable_packages)
							&& !empty($this->company->business_hours) && $this->company->enableWorkingStatus){
						$tabs[9]=JText::_('LNG_OPENING_HOURS');
					}
					
					if((isset($this->package->features) && in_array(CUSTOM_TAB,$this->package->features) || !$appSettings->enable_packages)
					   && !empty($this->company->custom_tab_name)){
						$tabs[10]=$this->company->custom_tab_name;
					}
					
					if((isset($this->package->features) && in_array(RELATED_COMPANIES,$this->package->features) || !$appSettings->enable_packages)
						&& isset($this->realtedCompanies) && count($this->realtedCompanies)){
						$tabs[11]=JText::_('LNG_RELATED_COMPANIES');
					}

					if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
						&& isset($this->associatedEvents) && count($this->associatedEvents) && $appSettings->enable_events){
						$tabs[12]=JText::_('LNG_ASSOCIATED_EVENTS');
					}

					if((isset($this->package->features) && in_array(COMPANY_SERVICES,$this->package->features) || !$appSettings->enable_packages)
						&& isset($this->services) && count($this->services) && $appSettings->enable_services){
						$tabs[13]=JText::_('LNG_COMPANY_SERVICES');
					}

					if((isset($this->package->features) && in_array(SOUNDS_FEATURE,$this->package->features) || !$appSettings->enable_packages)
							&& !empty($this->sounds)) { 
						$tabs[14]=JText::_('LNG_SOUNDS');
					}

                    if((isset($this->package->features) && in_array(SERVICES_LIST,$this->package->features) || !$appSettings->enable_packages)
                            && !empty($this->services_list) && count($this->services_list)){
                        $tabs[15]=JText::_('LNG_PRICE_LIST');
                    }

                    if((isset($this->package->features) && in_array(TESTIMONIALS,$this->package->features) || !$appSettings->enable_packages)
                            && count($this->companyTestimonials)){
                        $tabs[16]=JText::_('LNG_TESTIMONIALS');
                    }

                    if(!empty($this->companyProjects)){
                        $tabs[17]=JText::_('LNG_PROJECTS');
                    }

					foreach($tabs as $key=>$tab){
					?>
						<li class="dir-dir-tabs-options"><span id="dir-tab-<?php echo $key?>"  onclick="showDirTab('#tabs-<?php echo $key?>')" class="track-business-details"><?php echo $tab?></span></li>
					<?php } ?>
			</ul>

			<?php if (isset($this->package->features) && in_array(DESCRIPTION,$this->package->features) || !$appSettings->enable_packages){ ?>
				<div id="tabs-1" class="dir-tab ui-tabs-panel">
					<?php require_once 'details.php'; ?>
				</div>
			<?php } ?>

			<?php if((isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages )
							&& isset($this->company->latitude) && isset($this->company->longitude)){
			?>
			<div id="tabs-2" class="dir-tab ui-tabs-panel">
				<?php
					if(isset($this->company->latitude) && isset($this->company->longitude) && $this->company->latitude!='' && $this->company->longitude!='')
						require_once 'map.php';
					else
						echo JText::_("LNG_NO_MAP_COORDINATES_DEFINED");
				?>
			</div>
			<?php } ?>

			<?php if($appSettings->enable_reviews){ ?>
				<div id="tabs-3" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_reviews.php'; ?>
				</div>
			<?php }?>
			<?php
				if((isset($this->package->features) && in_array(IMAGE_UPLOAD,$this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->pictures)){
			?>
			<div id="tabs-4" class="dir-tab ui-tabs-panel">
				<?php require_once JPATH_COMPONENT_SITE.'/include/image_gallery.php'; ?>

			</div>
			<?php } ?>

			<?php
				if((isset($this->package->features) && in_array(VIDEOS,$this->package->features) || !$appSettings->enable_packages)
					&& isset($this->videos) && count($this->videos)>0){
			?>
			<div id="tabs-5" class="dir-tab ui-tabs-panel">
				<?php require_once 'listing_videos.php'; ?>
			</div>
			<?php } ?>

			<?php
				if((isset($this->package->features) && in_array(COMPANY_OFFERS,$this->package->features) || !$appSettings->enable_packages)
					&& isset($this->offers) && count($this->offers) && $appSettings->enable_offers){
			?>
			<div id="tabs-6" class="dir-tab ui-tabs-panel" itemprop="hasOfferCatalog" itemscope itemtype="http://schema.org/OfferCatalog">
				<?php require_once 'listing_offers.php'; ?>
			</div>
			<?php } ?>

			<?php
				if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
					&& isset($this->events) && count($this->events) && $appSettings->enable_events){
			?>
			<div id="tabs-7" class="dir-tab ui-tabs-panel">
				<?php require_once 'listing_events.php'; ?>
			</div>
			<?php } ?>

			<?php if(!empty($this->company->locations) && $appSettings->show_secondary_locations == 1 
					&& (isset($this->package->features) && in_array(SECONDARY_LOCATIONS, $this->package->features) || !$appSettings->enable_packages)){ ?>
				<div id="tabs-8" class="dir-tab ui-tabs-panel">
					<?php require_once 'locations.php'; ?>
				</div>
			<?php } ?>

			<?php if((isset($this->package->features) && in_array(OPENING_HOURS,$this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->company->business_hours) && $this->company->enableWorkingStatus){ ?>
				<div id="tabs-9" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_hours.php'; ?>
				</div>
			<?php } ?>

			<?php if((isset($this->package->features) && in_array(CUSTOM_TAB,$this->package->features) || !$appSettings->enable_packages)
					   && !empty($this->company->custom_tab_name)){ ?>
				<div id="tabs-10" class="dir-tab ui-tabs-panel">
					<?php echo JHTML::_("content.prepare",$this->company->custom_tab_content); ?>
				</div>
			<?php } ?>
			<?php
			if((isset($this->package->features) && in_array(RELATED_COMPANIES,$this->package->features) || !$appSettings->enable_packages)
				&& isset($this->realtedCompanies) && count($this->realtedCompanies)){
				?>
				<div id="tabs-11" class="dir-tab ui-tabs-panel" >
					<h2><?php echo JText::_("LNG_RELATED_COMPANIES")?></h2>
					<?php require_once 'related_business.php';?>
				</div>
			<?php } ?>
			<?php
				if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
				&& isset($this->associatedEvents) && count($this->associatedEvents) && $appSettings->enable_events){
			?>
				<div id="tabs-12" class="dir-tab ui-tabs-panel" >
					<?php require_once 'listing_associated_events.php';?>
				</div>
	    	<?php } ?>
			<?php
			if((isset($this->package->features) && in_array(COMPANY_SERVICES,$this->package->features) || !$appSettings->enable_packages)
				&& isset($this->services) && count($this->services) && $appSettings->enable_services){
				?>
				<div id="tabs-13" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_services.php';?>
				</div>
			<?php } ?>
			
			<?php if((isset($this->package->features) && in_array(SOUNDS_FEATURE,$this->package->features) || !$appSettings->enable_packages)
							&& !empty($this->sounds)) { ?>
				<div id="tabs-14" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_sounds.php'; ?>
				</div>
			<?php } ?>

            <?php
            if((isset($this->package->features) && in_array(SERVICES_LIST,$this->package->features) || !$appSettings->enable_packages)
                    && !empty($this->services_list)){
                ?>
                <div id="tabs-15" class="dir-tab ui-tabs-panel">
                    <?php require_once 'listing_price_list.php';?>
                </div>
            <?php } ?>
            <?php
            if((isset($this->package->features) && in_array(TESTIMONIALS,$this->package->features) || !$appSettings->enable_packages)
                    && !empty($this->companyTestimonials)){
                ?>
                <div id="tabs-16" class="dir-tab ui-tabs-panel" >
                    <?php require_once 'listing_testimonials.php';?>
                </div>
            <?php } ?>

            <?php
            if(!empty($this->companyProjects)){?>
                <div id="tabs-17" class="dir-tab ui-tabs-panel" >
                    <?php require_once 'listing_projects.php';?>
                </div>
            <?php } ?>
	</div>
	</div>
</div >
<div class="clear"></div>

<form name="tabsForm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" id="tabsForm" method="post">
 	 <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
	 <input type="hidden" name="task" value="companies.displayCompany" /> 
	 <input type="hidden" name="tabId" id="tabId" value="<?php echo $this->tabId?>" /> 
	 <input type="hidden" name="view" value="companies" /> 
	 <input type="hidden" name="layout2" id="layout2" value="" /> 
	 <input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
	 <input type="hidden" name="controller"	value="<?php echo JRequest::getCmd('controller', 'J-BusinessDirectory')?>" />
</form>
</div>

<?php
jimport('joomla.application.module.helper');
// this is where you want to load your module position
$modules = JModuleHelper::getModules('dir-listing');

if(isset($modules) && count($modules)>0){
    $fullWidth = false; ?>
    <div class="dir-company-module">
        <?php foreach($modules as $module) {
            echo JModuleHelper::renderModule($module);
        } ?>
    </div>
<?php }
?>

<script>
jQuery(document).ready(function(){
    initTabs(<?php echo $this->tabId ?>);
});
</script>

<?php require_once 'listing_util.php'; ?>