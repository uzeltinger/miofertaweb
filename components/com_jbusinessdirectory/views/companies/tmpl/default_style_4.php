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



<div id="one-page-container" class="one-page-container style4" itemscope itemtype="http://schema.org/LocalBusiness">
<?php require_once 'breadcrumbs.php';?>
     <div class="row-fluid">
    	<div class="span8">
    		<h1 itemprop="name">
    			<?php  echo isset($this->company->name)?$this->company->name:"" ; ?>
    		</h1>
    		<?php if(isset($this->company->slogan) && strlen($this->company->slogan)>2){?>
    			<div class="business-slogan"><?php echo  $this->company->slogan ?></div>
    		<?php }?>
    	</div>
    	<div class="span4">
    		<div class="dir-print">
                <a href="javascript:printCompany(<?php echo $this->company->id ?>, '<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=companies&tmpl=component"); ?>')"><i class="dir-icon-print"></i> <?php echo JText::_("LNG_PRINT")?></a>
            </div>
    		<?php require_once JPATH_COMPONENT_SITE."/include/social_share.php"?>
    	</div>
    </div>	
    <div class="row-fluid" style="margin-bottom: 0">
    <?php 
    		if((isset($this->package->features) && in_array(IMAGE_UPLOAD,$this->package->features) || !$appSettings->enable_packages)
    			&& !empty($this->pictures)){ 
    ?>
    	<?php $showImage = true;?>
    	<div id="company-info" class="company-info span7">
    		<?php require_once JPATH_COMPONENT_SITE.'/include/image_gallery.php';
    		if(false && $this->company->enableWorkingStatus && (!$appSettings->enable_packages || isset($this->package->features) && in_array(OPENING_HOURS,$this->package->features) && $this->company->enableWorkingStatus)) {
    			if ($this->company->workingStatus){?>
    				<div class="ribbon-open"><span><?php echo JText::_("LNG_OPEN")?></span></div>
    			<?php } else{ ?>
    				<div class="ribbon-close"><span><?php echo JText::_("LNG_CLOSED")?></span></div>
    			<?php } ?>
    		<?php } ?>
    	</div>
    <?php }?>
    
    <div class="contact-information <?php echo !empty($showImage)?"span5":"span12" ?>" >
    	<div class="company-info-container">
    	    <strong class="title"><?php echo JText::_("LNG_FIND_OUT_MORE")?></strong>
    		<?php if($showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)) { ?>
    			<?php if(!empty($this->companyContacts) && (!empty($this->companyContacts[0]->contact_name) || !empty($this->companyContacts[0]->contact_phone) )) { ?>
    	    	   <strong><?php echo count($this->companyContacts)>1?JText::_('LNG_CONTACT_PERSONS'):JText::_('LNG_CONTACT_PERSON'); ?></strong>
    			   <div style="line-height:18px;">
    				   <?php require_once 'contact_details.php'; ?>
    			   </div>
    			<?php } ?>
    	   <?php } ?>
    
            <?php $address = JBusinessUtil::getAddressText($this->company);?>
            <?php if(!empty($address)) { ?>
    		<strong><?php echo JText::_('LNG_ADDRESS') ?>:</strong>
    		<span class="company-address" itemprop="address">
    				<?php echo JBusinessUtil::getAddressText($this->company) ?>
    		</span>
            <?php } ?>
    		
    		<?php if($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
    			<div class="comany-contact-details">
    				<?php if(!empty($company->phone)) { ?>
    					<span>
    						<strong><?php echo JText::_('LNG_PHONE') ?>: </strong> <a href="tel:<?php echo $this->escape($company->phone); ?>"><span itemprop="telephone"><?php echo $this->escape($company->phone); ?></span></a>
    					</span><br/>
    				<?php } ?>
    				<?php if(!empty($company->fax)) { ?>
    					<span>
    						<strong><?php echo JText::_('LNG_FAX') ?>: </strong><span itemprop="faxNumber"><?php echo $this->escape($company->fax); ?></span>
    					</span><br/>
    				<?php } ?>
    				<?php if(!empty($company->mobile)) { ?>
    					<span>
    						<strong><?php echo JText::_('LNG_MOBILE') ?>: </strong><a href="tel:<?php echo $this->escape($company->mobile); ?>"><span itemprop="telephone"> <?php echo $this->escape($company->mobile); ?></span></a>
    					</span><br/>
    				<?php } ?>
    			</div>
    		<?php } ?>
    		
    		<?php
            if($showData && (isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
            && ((!empty($this->company->linkedin) || !empty($this->company->youtube) ||!empty($this->company->facebook) || !empty($this->company->twitter)
                || !empty($this->company->googlep) || !empty($this->company->linkedin) || !empty($this->company->skype)|| !empty($this->company->instagram) || !empty($this->company->pinterest) || !empty($this->company->whatsapp)))) {
                require_once 'listing_social_networks.php';
            }
            ?>
    		
    		<div class="clear"></div>
    		<div class="company-links">
    			<ul class="features-links">
    				<?php if($showData && (isset($this->package->features) && in_array(WEBSITE_ADDRESS,$this->package->features) || !$appSettings->enable_packages) && !empty($company->website)){
    					if ($appSettings->enable_link_following){
    					$followLink = (isset($this->package->features) && in_array(LINK_FOLLOW,$this->package->features) && $appSettings->enable_packages)?'rel="follow"' : 'rel="nofollow"';
    					}else{
    						$followLink ="";
    					}?>
    					<li>
    						<a <?php echo $followLink ?> itemprop="url" class="website" title="<?php echo $this->escape($this->company->name) ?> Website" target="_blank" onclick="increaseWebsiteClicks(<?php echo $company->id ?>)" href="<?php echo $this->escape($company->website) ?>"><?php echo JText::_('LNG_WEBSITE') ?></a>
    					</li>
    				<?php } else { ?>
                        <span style="display:none;" itemprop="url">
                            <?php echo JBusinessUtil::getCompanyLink($this->company); ?>
                        </span>
                    <?php } ?>
    				<?php if((isset($this->package->features) && in_array(CONTACT_FORM,$this->package->features) || !$appSettings->enable_packages) && !empty($company->email) && $appSettings->show_contact_form){ ?>
    					<li>
    						<a class="email" href="javascript:contactCompany(<?php echo $showData?"1":"0"?>)" ><?php echo JText::_('LNG_CONTACT_COMPANY'); ?></a>
    					</li>
    				<?php } ?>
    				<?php if($appSettings->enable_reporting){?>
    					<li>
    						<a href="javascript:showReportAbuse()" style="padding:0px;"><?php echo JText::_('LNG_REPORT_LISTING'); ?></a>
    					</li>
    				<?php } ?>
    			</ul>
    		</div>
    		<div class="clear"></div>
    		
    		<div class="rating-info">
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
    
                    <?php if($appSettings->enable_ratings) { ?>
                        <div class="company-info-average-rating">
                            <div class="rating">
                                <div id="rating-average" title="<?php echo $this->escape($this->company->review_score) ?>"></div>
                            </div>
                        </div>
                    <?php } ?>
    			</div>
    			
    			<div class="company-info-review" >
    				<div class="review-count">
    					<?php if($appSettings->enable_reviews){?>
    						<?php if(count($this->reviews)){ ?> 
    	   					    <a href="#reviews"><?php echo count($this->reviews)?> <?php echo JText::_('LNG_REVIEWS') ?></a>
    							&nbsp;|&nbsp;
    							<a href="javascript:void(0)" onclick="addNewReview(<?php echo ($appSettings->enable_reviews_users && $user->id ==0) ?"1":"0"?>)"> <?php echo JText::_('LNG_WRITE_REVIEW') ?></a>
    						<?php } else{ ?>
    							<a href="javascript:void(0)" onclick="addNewReview(<?php echo ($appSettings->enable_reviews_users && $user->id ==0) ?"1":"0"?>)"><?php echo JText::_('LNG_BE_THE_FIRST_TO_REVIEW') ?></a>
    						<?php }?>
    					<?php }?>
    					
    					<?php if($this->appSettings->enable_bookmarks) { ?>
    						<?php if(!empty($company->bookmark)){?>
    							<a href="javascript:showUpdateBookmarkDialog(<?php echo $user->id==0?"1":"0"?>)"  title="<?php echo JText::_("LNG_UPDATE_BOOKMARK")?>" class="bookmark "><i class="dir-icon-heart"></i></a>
    						<?php }else{?>
    							<a href="javascript:addBookmark(<?php echo $user->id==0?"1":"0"?>)" title="<?php echo JText::_("LNG_ADD_BOOKMARK")?>" class="bookmark "><i class="dir-icon-heart-o"></i></a>
    						<?php } ?>
    						
    					<?php } ?>
    				</div>
    			</div>
    		</div>
    		
    		<?php if($showData){?>
    			<div class="company-map">
    				<?php
    					if((isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages ) 
    											&& !empty($this->company->latitude) && !empty($this->company->longitude)){ 
    				?>		
    					<a href="javascript:showCompanyMap()" title="Show Map">
    						<?php 
    							$key="";
    							if(!empty($appSettings->google_map_key))
    								$key="&key=".$appSettings->google_map_key;
    
    							echo '<img src="https://maps.googleapis.com/maps/api/staticmap?center='.$this->company->latitude.','.$this->company->longitude.'&zoom=13&size=360x120&markers=color:blue|'.$this->company->latitude.','.$this->company->longitude.$key.'&sensor=false">';
    						?>
    					</a> 	
    					<div class="clear"></div>
    				<?php } ?>
		
					<?php if((!isset($this->company->userId) || $this->company->userId == 0) && $appSettings->claim_business){ ?>
						<div class="claim-container" id="claim-container">
							
							<a href="javascript:void(0)" onclick="claimCompany(<?php echo $user->id==0?"1":"0"?>)">
								<div class="claim-btn">
									<?php echo JText::_('LNG_CLAIM_COMPANY')?>
								</div>
							</a>
						</div>
						<?php  } ?>
				</div>
			<?php } ?>
		</div>
		<div class="clear"></div>
		
	</div>
</div>
	
	
	<div class="clear"></div>

	<div id="company-map-holder" style="display:none" class="company-cell">
		<div class="search-toggles">
			<span class="button-toggle">
				<a title="" class="" href="javascript:hideMap()"><?php echo JText::_("LNG_CLOSE_MAP")?></a>
			</span>
			<div class="clear"></div>
		</div>
		<h2><?php echo JText::_("LNG_BUSINESS_MAP_LOCATION")?></h2>
		
		<?php 
			if(isset($this->company->latitude) && isset($this->company->longitude) && $this->company->latitude!='' && $this->company->longitude!='')
				require_once 'map.php';
		?>
	</div>

	<div class="company-menu">
		<nav>
			<a id="business-link" href="javascript:showDetails('company-business');" class="active"><?php echo JText::_("LNG_BUSINESS_DETAILS")?></a>
		
			<?php 
				if((isset($this->package->features) && in_array(VIDEOS,$this->package->features) || !$appSettings->enable_packages)
					&& isset($this->videos) && count($this->videos)>0){	
			?>
					<a id="videos-link" href="javascript:showDetails('company-videos');" class=""><?php echo JText::_("LNG_VIDEOS")?></a>
			<?php } ?>
			
			<?php if((isset($this->package->features) && in_array(SOUNDS_FEATURE,$this->package->features) || !$appSettings->enable_packages)
							&& !empty($this->sounds)) { ?>
				<a id="sounds-link" href="javascript:showDetails('company-sounds');" class=""><?php echo JText::_("LNG_SOUNDS")?></a>
			<?php } ?>
			
			<?php 
				if((isset($this->package->features) && in_array(COMPANY_OFFERS,$this->package->features) || !$appSettings->enable_packages)
					&& isset($this->offers) && count($this->offers) && $appSettings->enable_offers){
			?>
					<a id="offers-link" href="javascript:showDetails('company-offers');" class=""><?php echo JText::_("LNG_OFFERS")?></a>
			<?php } ?>

			<?php
			if((isset($this->package->features) && in_array(RELATED_COMPANIES,$this->package->features) || !$appSettings->enable_packages)
				&& isset($this->realtedCompanies) && count($this->realtedCompanies)){
				?>
				<a id="related-link" href="javascript:showDetails('company-related');" class=""><?php echo JText::_("LNG_RELATED_COMPANIES")?></a>
			<?php } ?>
			
			<?php 
				if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
					&& isset($this->events) && count($this->events) && $appSettings->enable_events){
			?>
					<a id="events-link" href="javascript:showDetails('company-events');" class=""><?php echo JText::_("LNG_EVENTS")?></a>
			<?php } ?>

			<?php
			if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
				&& isset($this->associatedEvents) && count($this->associatedEvents) && $appSettings->enable_events){
				?>
				<a id="associated-link" href="javascript:showDetails('events-associated');" class=""><?php echo JText::_("LNG_ASSOCIATED_EVENTS")?></a>
			<?php } ?>

            <?php
            if((isset($this->package->features) && in_array(TESTIMONIALS,$this->package->features) || !$appSettings->enable_packages)
                && !empty($this->companyTestimonials)){
                ?>
                <a id="testimonials-link" href="javascript:showDetails('company-testimonials');" class=""><?php echo JText::_("LNG_TESTIMONIALS")?></a>
            <?php } ?>

			<?php if((isset($this->package->features) && in_array(COMPANY_SERVICES,$this->package->features) || !$appSettings->enable_packages)
				&& isset($this->services) && count($this->services) && $appSettings->enable_services) { ?>
				<a id="events-link" href="javascript:showDetails('company-services');" class=""><?php echo JText::_("LNG_COMPANY_SERVICES")?></a>
			<?php } ?>
			
			<?php if($appSettings->enable_reviews){ ?>
				<a id="reviews-link" href="javascript:showDetails('company-reviews');" class=""><?php echo JText::_("LNG_REVIEWS")?></a>
			<?php }?>

            <?php if((isset($this->package->features) && in_array(SERVICES_LIST,$this->package->features) || !$appSettings->enable_packages)
                    && !empty($this->services_list) && count($this->services_list)) { ?>
                <a id="price-list-link" href="javascript:showDetails('company-price-list');" class=""><?php echo JText::_("LNG_PRICE_LIST")?></a>
            <?php }?>

            <?php if(!empty($this->companyProjects)){ ?>
                <a id="projects-link" href="javascript:showDetails('company-projects');" class=""><?php echo JText::_("LNG_PROJECTS")?></a>
            <?php }?>
			
		</nav>
	</div>

	<div id="company-details" class="company-cell">
		<?php if(isset($this->company->slogan) && strlen($this->company->slogan)>2){?>
			<p class="business-slogan"><?php echo  $this->company->slogan ?> </p>
		<?php }?>
			
		<dl>
			<?php if(!empty($this->company->typeName)){?>
				<dt><?php echo JText::_('LNG_TYPE')?>:</dt>
				<dd><?php echo $this->escape($this->company->typeName) ?></dd>
			<?php } ?>

			<?php if(!empty($this->company->establishment_year)){?>
				<dt><?php echo JText::_('LNG_ESTABLISHMENT_YEAR')?>:</dt>
				<dd><?php echo $this->company->establishment_year?></dd>
			<?php } ?>

			<?php if(!empty($this->company->employees)){?>
				<dt><?php echo JText::_('LNG_EMPLOYEES')?>:</dt>
				<dd><?php echo $this->company->employees?></dd>
			<?php } ?>
			
			<?php if(!empty($this->company->categories)){?>
				<dt><?php echo JText::_('LNG_CATEGORIES')?>:</dt>
				<dd>
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

			<?php if(!empty($this->company->keywords)){?>
				<dt><?php echo JText::_('LNG_KEYWORDS')?>:</dt>
				<dd>
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
			
			<?php if(!empty($this->company->description) && (isset($this->package->features) && in_array(DESCRIPTION,$this->package->features) || !$appSettings->enable_packages)){?>
				<dt><?php echo JText::_("LNG_GENERAL_INFO")?></dt>
				<dd><div id="dir-listing-description" class="dir-listing-description"><?php echo JHTML::_("content.prepare", $this->company->description);?></div></dd>
			<?php }?>
			
			<?php if((isset($this->package->features) && in_array(CUSTOM_TAB,$this->package->features) || !$appSettings->enable_packages)
					   && !empty($this->company->custom_tab_name)){ ?>
				<dt><?php echo $this->company->custom_tab_name ?></dt>
				<dd><?php echo JHTML::_("content.prepare", $this->company->custom_tab_content);?>&nbsp;</dd>
			<?php } ?>
			
			<?php if(!empty($this->company->locations) && $appSettings->show_secondary_locations == 1
					&& (isset($this->package->features) && in_array(SECONDARY_LOCATIONS, $this->package->features) || !$appSettings->enable_packages)){ ?>
				<dt><?php echo JText::_("LNG_COMPANY_LOCATIONS")?></dt>
				<dd><?php require_once 'locations.php';?></dd>
			<?php } ?>
			
			<?php if((isset($this->package->features) && in_array(OPENING_HOURS,$this->package->features) || !$appSettings->enable_packages)
				&& !empty($this->company->business_hours) && $this->company->enableWorkingStatus){ ?>
				<dt><?php echo JText::_("LNG_OPENING_HOURS"); ?></dt>
				<dd><?php require_once 'listing_hours.php'; ?>	</dd>
			<?php } ?>
			
			<?php if($showData && $appSettings->enable_attachments && (isset($this->package->features) && in_array(ATTACHMENTS, $this->package->features) || !$appSettings->enable_packages)) { ?>
				<?php if(!empty($this->company->attachments)) { ?>
					<dt><?php echo JText::_("LNG_ATTACHMENTS")?></dt>
					<dd>
						<?php require "listing_attachments.php" ?>
					</dd>
				<?php } ?>
			<?php } ?>
		</dl>

        <div class="classification">
            <?php require_once 'listing_attributes.php'; ?>
        </div>
			
	</div>
	<div class="clear"></div>
					
	
	<?php 
		if((isset($this->package->features) && in_array(VIDEOS,$this->package->features) || !$appSettings->enable_packages)
							&& isset($this->videos) && count($this->videos)>0){
	?>			
		<div id="company-videos" class="company-cell">
			<h2><?php echo JText::_("LNG_VIDEOS")?></h2>
			<?php  require_once 'listing_videos.php';?>
		</div>
	<?php }	?>
	
	<?php if((isset($this->package->features) && in_array(SOUNDS_FEATURE,$this->package->features) || !$appSettings->enable_packages)
							&& !empty($this->sounds)) { ?>
		<div id="company-sounds" class="company-cell">
			<h2><?php echo JText::_("LNG_SOUNDS")?></h2>
			<?php  require_once 'listing_sounds.php';?>
		</div>
	<?php }	?>
	
	<?php 
	if((isset($this->package->features) && in_array(COMPANY_OFFERS,$this->package->features) || !$appSettings->enable_packages)
			&& isset($this->offers) && count($this->offers) && $appSettings->enable_offers){
	?>
		<div id="company-offers" class="company-cell" itemprop="hasOfferCatalog" itemscope itemtype="http://schema.org/OfferCatalog">
			<h2><?php echo JText::_("LNG_COMPANY_OFFERS")?></h2>
			<?php require_once 'listing_offers.php';?>
		</div>
		<div class="clear"></div>
	<?php } ?>
	
	<?php
	if((isset($this->package->features) && in_array(RELATED_COMPANIES,$this->package->features) || !$appSettings->enable_packages)
		&& isset($this->realtedCompanies) && count($this->realtedCompanies)){
		?>
		<div id="company-related" class="company-cell" >
			<h2><?php echo JText::_("LNG_RELATED_COMPANIES")?></h2>
			<?php require_once 'related_business.php';?>
		</div>
		<div class="clear"></div>
	<?php } ?>


	<?php 
	if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
			&& isset($this->events) && count($this->events) && $appSettings->enable_events){
	?>
		<div id="company-events" class="company-cell">
			<h2><?php echo JText::_("LNG_COMPANY_EVENTS")?></h2>
			<?php require_once 'listing_events.php';?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php
	if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
		&& isset($this->associatedEvents) && count($this->associatedEvents) && $appSettings->enable_events){
		?>
		<div id="events-associated" class="company-cell">
			<h2><?php echo JText::_("LNG_ASSOCIATED_EVENTS")?></h2>
			<?php require_once 'listing_associated_events.php';?>
		</div>
		<div class="clear"></div>
	<?php } ?>

    <?php
    if((isset($this->package->features) && in_array(TESTIMONIALS,$this->package->features) || !$appSettings->enable_packages)
        && !empty($this->companyTestimonials)){
        ?>
        <div id="company-testimonials" class="company-cell">
            <h2><?php echo JText::_("LNG_TESTIMONIALS")?></h2>
            <?php require_once 'listing_testimonials.php';?>
        </div>
        <div class="clear"></div>
    <?php } ?>

	<?php if((isset($this->package->features) && in_array(COMPANY_SERVICES,$this->package->features) || !$appSettings->enable_packages)
		&& isset($this->services) && count($this->services) && $appSettings->enable_services) { ?>
		<div id="company-services" class="company-cell">
			<h2><?php echo JText::_("LNG_COMPANY_SERVICES")?></h2>
			<?php require_once 'listing_services.php';?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php if($appSettings->enable_reviews){ ?>
		<div id="company-reviews" class="company-cell">
			<h2><?php echo JText::_("LNG_BUSINESS_REVIEWS")?></h2>
			<?php require_once 'listing_reviews.php';?>
		</div>
		<div class="clear"></div>
	<?php } ?>

    <?php if((isset($this->package->features) && in_array(SERVICES_LIST,$this->package->features) || !$appSettings->enable_packages)
            && !empty($this->services_list)) { ?>
        <div id="company-price-list" class="company-cell">
            <h2><?php echo JText::_("LNG_PRICE_LIST")?></h2>
            <?php require_once 'listing_price_list.php';?>
        </div>
        <div class="clear"></div>
    <?php } ?>

    <?php if(!empty($this->companyProjects)) { ?>
        <div id="company-projects" class="company-cell">
            <h2 onclick="returnToProjects();" onmouseover =	"this.style.cursor='hand';this.style.cursor='pointer'" onmouseout = "this.style.cursor='default'" >
                <?php echo JText::_("LNG_PROJECTS")?></h2>
            <?php require_once 'listing_projects.php';?>
        </div>
        <div class="clear"></div>
    <?php } ?>
	
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

<?php require_once 'listing_util.php'; ?>     		
