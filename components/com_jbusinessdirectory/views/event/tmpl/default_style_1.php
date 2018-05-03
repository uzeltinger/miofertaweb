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

<?php require_once JPATH_COMPONENT_SITE."/include/social_share.php"; ?>
<div> <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=events'); ?>"><?php echo JText::_("BACK") ?></a></div>
<div id="event-container" class="event-container row-fluid" itemscope itemtype="http://schema.org/Event">
  
    <div class="row-fluid">
        <?php if(!empty($this->event->pictures)){?>
            <div id="event-image-container" class="event-image-container span6">
                <?php
                $this->pictures = $this->event->pictures;
                require_once JPATH_COMPONENT_SITE.'/include/image_gallery.php';

                ?>
            </div>
        <?php } ?>
        <div id="event-content" class="event-content span6">
            <div class="dir-print">
            	<a href="javascript:printEvent(<?php echo $this->event->id ?>, '<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=event&tmpl=component"); ?>')"><i class="dir-icon-print"></i> <?php echo JText::_("LNG_PRINT")?></a>
	    	</div>
            <h1 itemprop="name">
                <?php echo $this->escape($this->event->name)?>
            </h1>
            <div class="event-details">
                <div class="row-fluid">
                    <div class="span6">
                        <?php $address = JBusinessUtil::getAddressText($this->event); ?>
                        <?php if(!empty($address)) { ?>
                            <div class="event-location" itemprop="location" itemscope itemtype="http://schema.org/Place">
                                <i class="dir-icon-map-marker dir-icon-large"></i>&nbsp;<span itemprop="name"><?php echo $this->escape($address) ?></span>
                                <span style="display:none;" itemprop="address"><?php echo JBusinessUtil::getAddressText($this->event)?></span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($this->event->price) && intval($this->event->price)!=0){?>
                            <div class="event-price">
                                <?php echo JText::_("LNG_PRICE")?>: <strong><?php echo JBusinessUtil::getPriceFormat($this->event->price, $this->event->currency_id) ?></strong>
                            </div>
                        <?php } ?>

                        <div class="event-type">
                            <?php echo JText::_("LNG_TYPE")?>: <strong><?php echo $this->escape($this->event->eventType)?></strong>
                        </div>

                        <?php if(!empty($this->event->categories)){?>
                            <div class="event-categories">
                                <div style="float:left"><?php echo JText::_('LNG_CATEGORIES')?>:&nbsp;</div>
                                <ul class="event-categories">
                                    <?php
                                    $categories = explode('#|',$this->event->categories);
                                    foreach($categories as $i=>$category){
                                        $category = explode("|", $category);
                                        ?>
                                        <li> <a rel="nofollow" href="<?php echo JBusinessUtil::getEventCategoryLink($category[0], $category[2]) ?>"><?php echo $this->escape($category[1])?></a><?php echo $i<(count($categories)-1)? ',&nbsp;':'' ?></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        <?php } ?>
                        <span style="display:none;" itemprop="url"><?php echo JBusinessUtil::getEventLink($this->event->id, $this->event->alias); ?></span>

                        <?php if(!empty($this->event->contact_phone) || !empty($this->event->contact_email) || !empty($this->event->company->email)) { ?>
                            <div class="event-contact">
                                <strong><?php echo JText::_('LNG_CONTACT_DETAILS') ?></strong><br/>
                                <?php if(!empty($this->event->contact_phone)) { ?><i class="dir-icon-phone"></i>&nbsp;<a href="tel:<?php echo $this->event->contact_phone ?>"><?php echo $this->event->contact_phone ?></a><br/> <?php } ?>
                                <?php if((!empty($this->event->contact_email) || (!empty($this->event->company) && !empty($this->event->company->email))) && $this->appSettings->show_contact_form) { ?>
                                    <a itemprop="email" href="javascript:contactCompany(<?php echo $showData?1:0 ?>)" ><i class="dir-icon-envelope"></i> <?php echo JText::_('LNG_CONTACT'); ?></a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if($this->appSettings->enable_event_subscription && $this->event->enable_subscription) { ?>
                    <div class="span6">
                        <button class="ui-dir-button ui-dir-button-green right" onclick="joinEvent(<?php echo ($user->id == 0)?0:1 ?>)">
                            <?php if(empty($this->userAssociatedCompanies))
                                echo JText::_('LNG_JOIN');
                            else
                                echo JText::_('LNG_LEAVE_EVENT'); ?>
                        </button>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="event-dates-details">
                <table>
                    <tr>
                        <td>
                            <?php if ($this->event->start_date != '0000-00-00'){ ?>
                            <div class="event-date">
                                <strong><?php echo JText::_('LNG_EVENT_DATES') ?></strong><br/>
                                <i class="dir-icon-calendar"></i> <?php $dates = JBusinessUtil::getDateGeneralFormat($this->event->start_date).(!empty($this->event->start_date) && $this->event->start_date!=$this->event->end_date && $this->event->show_end_date?" - ".JBusinessUtil::getDateGeneralFormat($this->event->end_date):""); echo $dates; ?>
                                <?php echo (empty($dates) || ($this->event->show_start_time==0  && $this->event->show_end_time==0))?"":"," ?>
                                <?php echo ($this->event->show_start_time?JBusinessUtil::convertTimeToFormat($this->event->start_time):"")." ".(!empty($this->event->end_time)&&$this->event->show_end_time?JText::_("LNG_UNTIL"):"")." ".($this->event->show_end_time?JBusinessUtil::convertTimeToFormat($this->event->end_time):""); ?>
                            </div>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php if(!empty($this->event->doors_open_time) && $this->event->show_doors_open_time) { ?>
                        <tr>
                            <td>
                                <strong><?php echo JText::_('LNG_EVENT_DOORS_OPEN') ?></strong><br/>
                                <i class="dir-icon-clock-o"></i>&nbsp;<span itemprop="doorTime"><?php echo JBusinessUtil::convertTimeToFormat($this->event->doors_open_time) ?></span>
                            </td>
                        </tr>
                    <?php } ?>

                    <?php if(!JBusinessUtil::emptyDate($this->event->booking_open_date) || !JBusinessUtil::emptyDate($this->event->booking_close_date)){?>
                        <?php if(!(!JBusinessUtil::emptyDate($this->event->booking_close_date) xor $this->event->booking_open_date <= $this->event->booking_close_date)) {?>
                            <tr>
                                <td>
                                    <div class="event-date">
                                        <strong><?php echo JText::_('LNG_BOOKING_DATES') ?></strong><br/>
                                        <i class="dir-icon-calendar"></i>
                                        <?php echo $this->event->dates ?>
                                        <?php echo (empty($this->event->dates) || (empty($this->event->booking_open_time) && empty($this->event->booking_close_time)))?"":"," ?>
                                        <?php echo JBusinessUtil::convertTimeToFormat($this->event->booking_open_time)." ".(!empty($this->event->booking_close_time)?JText::_("LNG_UNTIL"):"")." ".(JBusinessUtil::convertTimeToFormat($this->event->booking_close_time)); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>
            </div><br/>

            <?php if($this->appSettings->enable_attachments) { ?>
                <?php if(!empty($this->event->attachments)) { ?>
                    <div><strong><?php echo JText::_("LNG_ATTACHMENTS")?></strong></div>
                    <?php require "event_attachments.php"?>
                    <div class="clear"></div><br/>
                <?php } ?>
            <?php } ?>

            <?php if(!empty($this->event->company)){?>
	            <div class="company-details" itemprop="organizer" itemscope itemtype="http://schema.org/Organization">
	                <table>
	                    <tr>
	                        <td><strong><?php echo JText::_('LNG_COMPANY_DETAILS') ?></strong></td>
	                    </tr>
	                    <tr>
	                        <td><a itemprop="url" href="<?php echo JBusinessUtil::getCompanyLink($this->event->company)?>"><span itemprop="name"><?php echo $this->escape($this->event->company->name)?></span></a></td>
	                    </tr>
	
	                    <?php $address = JBusinessUtil::getAddressText($this->event->company); ?>
	                    <?php if(!empty($address)) { ?>
	                        <tr>
	                            <td><i class="dir-icon-map-marker dir-icon-large"></i>&nbsp;<span itemprop="address"><?php echo $this->escape($address) ?></span></td>
	                        </tr>
	                    <?php } ?>
	
	                   <?php if(!empty($this->event->company->phone) || !empty($this->event->company->mobile)){?>
	                        <tr>
	                            <td itemprop="telephone">
	                              <?php if(!empty($this->event->company->phone)){ ?>
	                            	<i class="dir-icon-phone dir-icon-large"></i> <a href="tel:<?php  echo $this->escape($this->event->company->phone); ?>"><?php  echo $this->escape($this->event->company->phone); ?></a> &nbsp;&nbsp;
	                              <?php }?> 
	                              <?php if(!empty($this->event->company->mobile)){ ?>
	                            	<i class="dir-icon-mobile-phone dir-icon-large"></i> <a href="tel:<?php  echo $this->escape($this->event->company->mobile); ?>"><?php  echo $this->escape($this->event->company->mobile); ?></a>
	                              <?php } ?>
	                            </td>
	                        </tr>
	                    <?php } ?>
	                    <?php if(!empty($this->event->company->website)){?>
	                        <tr>
	                        	<td>
	                            	<a target="_blank" itemprop="url" title="<?php echo $this->escape($this->event->company->name)?> Website" onclick="increaseWebsiteClicks(<?php echo $this->event->company->id ?>)" href="<?php echo $this->escape($this->event->company->website) ?>"><i class="dir-icon-link "></i>  <?php echo JText::_('LNG_WEBSITE')?></a></a>
	                            </td>
	                        </tr>
	                    <?php } ?>
	                </table>
	            </div>
            <?php } ?>

        </div>
    </div>

    <!-- Event Booking Section -->
    <?php
    if($this->appSettings->enable_event_reservation)
        require_once 'event_tickets.php';
    ?>

    <div class="classification">
        <?php require_once 'event_attributes.php'; ?>
    </div>
    <div class="event-description" itemprop="description">
        <?php echo $this->event->description?>
    </div>
    <?php if(!empty($this->event->latitude) && !empty($this->event->longitude)){ ?>
        <div id="event-map">
           <?php require_once 'map.php';?>
   		     <div class="clear"></div>
       </div>
    <?php } ?>
    
    <span style="display:none;" itemprop="startDate"><?php echo JBusinessUtil::getDateGeneralFormat($this->event->start_date) ?></span>
    <span style="display:none;" itemprop="endDate"><?php echo JBusinessUtil::getDateGeneralFormat($this->event->end_date) ?></span>
	<span style="display:none;" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
		<?php if(isset($this->event->picture_path) && $this->event->picture_path!=''){?>
            <img  alt="<?php echo $this->escape($this->event->name) ?>" src="<?php echo JURI::root()."/".PICTURES_PATH.$this->event->picture_path?>" itemprop="contentUrl">
            <span style="display:none;" itemprop="url"><?php echo JURI::root().PICTURES_PATH.$this->event->picture_path ?></span>
        <?php }else{?>
            <img title="<?php echo $this->escape($this->event->name)?>" alt="<?php echo $this->event->name?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl">
            <span style="display:none;" itemprop="url"><?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?></span>
        <?php } ?>
	</span>
    <div class="clear"></div><br/>
    <?php if(!empty($this->videos)){ ?>
	    <div>
	        <h4><?php echo JText::_("LNG_EVENT_VIDEOS")?></h3>
	        <?php require_once 'event_videos.php';?>
	    </div>
    <?php } ?>
    <div class="clear"></div><br/>
 	<?php if(!empty($this->associatedCompanies)){ ?>
	    <div>
	        <h4><?php echo JText::_("LNG_ASSOCIATED_COMPANIES")?></h3>
	        <?php require_once 'event_associated_companies.php';?>
	    </div>
    <?php } ?>
</div>

<?php require_once 'event_util.php'; ?>