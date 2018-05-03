<?php // no direct access
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$config = new JConfig();

//retrieving current menu item parameters
$currentMenuId = null;
$activeMenu = JFactory::getApplication()->getMenu()->getActive();
if(isset($activeMenu))
	$currentMenuId = $activeMenu->id ; // `enter code here`
$document = JFactory::getDocument(); // `enter code here`
$app = JFactory::getApplication(); // `enter code here`
if(isset($activeMenu)){
	$menuitem   = $app->getMenu()->getItem($currentMenuId); // or get item by ID `enter code here`
	$params = $menuitem->params; // get the params `enter code here`
}else{
	$params = null;
}

//set page title
if(!empty($params) && $params->get('page_title') != ''){
	$title = $params->get('page_title', '');
}
if(empty($title)){
	$title = JText::_("LNG_CONTROL_PANEL").' | '.$config->sitename;
}
$document->setTitle($title);

//set page meta description and keywords
$description = $this->appSettings->meta_description;
$document->setDescription($description);
$document->setMetaData('keywords', $this->appSettings->meta_keywords);

if(!empty($params) && $params->get('menu-meta_description') != ''){
	$document->setMetaData( 'description', $params->get('menu-meta_description') );
	$document->setMetaData( 'keywords', $params->get('menu-meta_keywords') );
}

$uri     = JURI::getInstance();
$url = $uri->toString( array('scheme', 'host', 'port', 'path'));

$user = JFactory::getUser();
if($user->id == 0){
	$app = JFactory::getApplication();
	$return = base64_encode('index.php?option=com_jbusinessdirectory&view=useroptions');
	$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return,false));
}

$appSettings =  JBusinessUtil::getInstance()->getApplicationSettings();
$enablePackages = $appSettings->enable_packages;
$enableOffers = $appSettings->enable_offers;
$hasBusiness = isset($this->companies) && count($this->companies)>0;
?>

<style>
#content-wrapper{
	margin: 20px;
	padding: 0px;
}

.tooltip {
	border-style:none !important;
}
</style>


<div id="user-options">
	<?php if($this->actions->get('directory.access.controlpanel') || !$appSettings->front_end_acl){ ?>

		<div class="row-fluid">
			<div class="ibox">
				<div class="ibox-content">
					<div class="row-fluid">
						<div class="span9">
							<?php $days_ago = 70; ?>
							<?php $time = strftime('%Y-%m-%d',(strtotime($days_ago.' days ago'))); ?>
							<div id="dir-dashboard-calendar-form">
								<div class="remove-margin item-calendar">
									<div class="remove-margin">
										<?php echo JHTML::_('calendar', $time, 'start_date', 'start_date', $appSettings->calendarFormat, array('id'=>'start_date', 'class'=>'inputbox calendar-date front-calendar', 'size'=>'10',  'maxlength'=>'10', 'onChange'=>'calendarChange()')); ?>
										<span><?php echo JText::_("LNG_TO")?></span>
										<?php echo JHTML::_('calendar', date("Y-m-d"), 'end_date', 'end_date', $appSettings->calendarFormat, array('id'=>'end_date', 'class'=>'inputbox calendar-date front-calendar', 'size'=>'10',  'maxlength'=>'10', 'onChange'=>'calendarChange()')); ?>
										<div class="clear"></div>
									</div>
								</div>
								<div class="row-fluid">
									<div class="span12">
										<div id="tabs">
											<div id="dir-dashboard-tabs" class="row-fluid">
												<div class="span12" id="dir-dashboard-tabs-col">
													<ul>
														<li><a href="#newCompanies"><?php echo JText::_("LNG_BUSINESS_LISTINGS");?></a></li>
														<?php if($enableOffers){?><li><a href="#newOffers"><?php echo JText::_("LNG_OFFERS");?></a></li> <?php }?>
                                                        <?php if($appSettings->enable_events){?><li><a href="#newEvents"><?php echo JText::_("LNG_EVENTS");?></a></li> <?php }?>
													</ul>
													<div class="clear"></div>
												</div>
											</div>
											<div id="newCompanies">
												<div id="graph"></div>
											</div>
											<div id="newOffers">
											</div>
											<div id="newEvents">
											</div>
										</div>
										<div class="clear"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="span3">
							<div>
								<h3><?php echo JText::_("LNG_TOTAL_VIEWS")?></h3> <h3><?php echo $this->statistics->totalViews?></h3>
							</div>
							<br/><br/><br/>
							<ul class="stat-list">
								<li>
									<h2 class="no-margins"><?php echo $this->statistics->listingsTotalViews ?></h2>
                                    <div class="row-fluid">
                                        <?php echo JText::_("LNG_BUSINESS_LISTING_VIEWS")?>
                                        <span class="stat-percent">
                                            <?php echo $this->statistics->totalViews> 0 ? round($this->statistics->listingsTotalViews * 100/$this->statistics->totalViews): 0 ?>%
                                        </span>
                                    </div>
									<div class="dir-progress progress-mini">
										<div class="dir-progress-bar" style="width: <?php echo round($this->statistics->listingsTotalViews * 100/$this->statistics->totalViews)?>%;"></div>
									</div>
								</li>
                                <?php if($enableOffers){?>
                                    <li>
                                        <h2 class="no-margins "><?php echo $this->statistics->offersTotalViews ?></h2>
                                        <div class="row-fluid">
                                            <?php echo JText::_("LNG_OFFER_VIEWS")?>
                                            <span class="stat-percent">
                                                <?php echo $this->statistics->totalViews>0 ? round($this->statistics->offersTotalViews * 100/$this->statistics->totalViews): 0?>%
                                            </span>
                                        </div>
                                        <div class="dir-progress progress-mini">
                                            <div class="dir-progress-bar" style="width: <?php echo round($this->statistics->offersTotalViews * 100/$this->statistics->totalViews)?>%;"></div>
                                        </div>
                                    </li>
                                <?php } ?>
                                <?php if($appSettings->enable_events){?>
                                    <li>
                                        <h2 class="no-margins "><?php echo $this->statistics->eventsTotalViews ?></h2>
                                        <div class="row-fluid">
                                            <?php echo JText::_("LNG_EVENT_VIEWS")?>
                                            <span class="stat-percent">
                                                <?php echo $this->statistics->totalViews > 0 ?round($this->statistics->eventsTotalViews * 100/$this->statistics->totalViews): 0?>%
                                            </span>
                                        </div>
                                        <div class="dir-progress progress-mini">
                                            <div class="dir-progress-bar" style="width: <?php echo $this->statistics->totalViews > 0 ? round($this->statistics->eventsTotalViews * 100/$this->statistics->totalViews): 0?>%;"></div>
                                        </div>
                                    </li>
                                <?php } ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row-fluid">
			<?php if($this->actions->get('directory.access.listings')|| !$appSettings->front_end_acl){?>
				<div class="span4 user-option-box">
					<div class="ibox" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies') ?>')">
						<div class="ibox-title">
							<div class="stats-icon pull-right">
								<img alt="<?php echo JTEXT::_("LNG_ADD_MODIFY_COMPANY_DATA") ?>" src="<?php echo JURI::base() ."components/".JBusinessUtil::getComponentName().'/assets/images/business-listings.png' ?>" />
							</div>
							<h5 class="clear"><?php echo JTEXT::_("LNG_ADD_MODIFY_COMPANY_DATA") ?></h5>
							<p class="small"> <?php echo JTEXT::_("LNG_ADD_MODIFY_COMPANY_DATA_INFO") ?></p>
						</div>
						<div class="ibox-content">
							<div class="row-fluid">
                                <div style="" class="span6">
                                    <small class="stats-label"><?php echo JText::_("LNG_BUSINESS_LISTINGS")?></small>
                                    <h4 class="text-success"><?php echo $this->statistics->totalListings ?></h4>
                                </div>

                                <div style="" class="span6">
                                    <small class="stats-label"><?php echo JText::_("LNG_VIEW_NUMBER")?></small>
                                    <h4><?php echo $this->statistics->listingsTotalViews?></h4>
                                </div>
                            </div>
						</div>
					</div>
				</div>
			<?php }?>
			
			<?php if($enableOffers && ($this->actions->get('directory.access.offers')|| !$appSettings->front_end_acl)){?>
				<div class="span4 user-option-box">
					<div class="ibox" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffers') ?>')">
						<div class="ibox-title">
							<div class="stats-icon pull-right">
								<img alt="<?php echo JTEXT::_("LNG_ADD_MODIFY_OFFERS") ?>" src="<?php echo JURI::base() ."components/".JBusinessUtil::getComponentName().'/assets/images/special-offer.png' ?>" />
							</div>
							<h5 class="clear"><?php echo JTEXT::_("LNG_ADD_MODIFY_OFFERS") ?></h5>
							<p class="small"> <?php echo JTEXT::_("LNG_ADD_MODIFY_OFFERS_INFO") ?></p>
						</div>
						<div class="ibox-content">
							<div class="row-fluid">
                                <div style="" class="span6">
                                    <small class="stats-label"><?php echo JText::_("LNG_OFFERS")?></small>
                                    <h4 class="text-success"><?php echo $this->statistics->totalOffers ?></h4>
                                </div>

                                <div style="" class="span6">
                                    <small class="stats-label"><?php echo JText::_("LNG_VIEW_NUMBER")?></small>
                                    <h4><?php echo $this->statistics->offersTotalViews?></h4>
                                </div>
                            </div>
						</div>
					</div>
				</div>

				<!-- <div class="span4">
					<div class="ibox" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffercoupons') ?>')">
						<div class="ibox-title">
							<div class="stats-icon pull-right">
								<img alt="<?php echo JTEXT::_("LNG_ADD_MODIFY_COUPONS") ?>" src="<?php echo JURI::base() ."components/".JBusinessUtil::getComponentName().'/assets/images/coupons.png' ?>" />
							</div>
							<h5 class="clear"><?php echo JTEXT::_("LNG_ADD_MODIFY_COUPONS") ?></h5>
							<p class="small"> <?php echo JTEXT::_("LNG_ADD_MODIFY_COUPONS_INFO") ?></p>
						</div>
						<div class="ibox-content">
							<div class="row-fluid">
                                <div style="" class="span6">
                                    <small class="stats-label"><?php echo JText::_("LNG_COUPONS")?></small>
                                   <h4 class="text-success">1</h4>
                                </div>

                                <div style="" class="span6">
                                    <small class="stats-label"><?php echo JText::_("LNG_VIEW_NUMBER")?></small>
                                    <h4>0</h4>
                                </div>
                            </div>
						</div>
					</div>
				</div> -->
			<?php } ?>
			
			<?php if($appSettings->enable_events && ($this->actions->get('directory.access.events')|| !$appSettings->front_end_acl)){?>		
				<div class="span4 user-option-box">
					<div class="ibox" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyevents') ?>')">
						<div class="ibox-title">
							<div class="stats-icon pull-right">
								<img alt="<?php echo JTEXT::_("LNG_MANAGE_YOUR_EVENTS") ?>" src="<?php echo JURI::base() ."components/".JBusinessUtil::getComponentName().'/assets/images/events.png' ?>" />	
							</div>
							<h5 class="clear"><?php echo JTEXT::_("LNG_MANAGE_YOUR_EVENTS") ?></h5>
							<p class="small"> <?php echo JTEXT::_("LNG_EVENTS_INFO") ?></p>
						</div>
						<div class="ibox-content">
							<div class="row-fluid">
                                <div style="" class="span6">
                                    <small class="stats-label"><?php echo JText::_("LNG_EVENTS")?></small>
                                    <h4 class="text-success"><?php echo $this->statistics->totalEvents; ?></h4>
                                </div>
                                <div style="" class="span6">
                                    <small class="stats-label"><?php echo JText::_("LNG_VIEW_NUMBER")?></small>
                                    <h4><?php echo $this->statistics->eventsTotalViews; ?></h4>
                                </div>
                            </div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>

		<div class="row-fluid">
			<?php if($appSettings->enable_packages) { ?>
				<div class="span4 user-option-box">
					<div class="ibox" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=orders') ?>')">
						<div class="ibox-title">
							<div class="stats-icon pull-right">
								<img alt="<?php echo JTEXT::_("LNG_MANAGE_YOUR_ORDERS") ?>" src="<?php echo JURI::base() ."components/".JBusinessUtil::getComponentName().'/assets/images/orders.png' ?>" />
							</div>
							<h5 class="clear"><?php echo JTEXT::_("LNG_MANAGE_YOUR_ORDERS") ?></h5>
							<p class="small"> <?php echo JTEXT::_("LNG_ORDERS_INFO") ?></p>
						</div>
						<div class="ibox-content">
						
						</div>
					</div>
				</div>
			<?php } ?>
			
			<?php if($appSettings->enable_bookmarks && ($this->actions->get('directory.access.bookmarks') || !$appSettings->front_end_acl)) { ?>
				<div class="span4 user-option-box">
					<div class="ibox" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks') ?>')">
						<div class="ibox-title">
							<div class="stats-icon pull-right">
								<img alt="<?php echo JTEXT::_("LNG_MANAGE_YOUR_BOOKMARKS") ?>" src="<?php echo JURI::base() ."components/".JBusinessUtil::getComponentName().'/assets/images/bookmark.png' ?>" />
							</div>
							<h5 class="clear"><?php echo JTEXT::_("LNG_MANAGE_YOUR_BOOKMARKS") ?></h5>
							<p class="small"> <?php echo JTEXT::_("LNG_BOOKMARKS_INFO") ?></p>
						</div>
						<div class="ibox-content">
							
						</div>
					</div>
				</div>
			<?php } ?>
			
			<?php if($appSettings->enable_packages){?>
				<div class="span4 user-option-box">
					<div class="ibox" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=billingdetails&layout=edit') ?>')">
						<div class="ibox-title">
							<div class="stats-icon pull-right">
								<img alt="<?php echo JTEXT::_("LNG_BILLING_DETAILS") ?>" src="<?php echo JURI::base() ."components/".JBusinessUtil::getComponentName().'/assets/images/user.png' ?>" />	
							</div>
							<h5 class="clear"><?php echo JTEXT::_("LNG_BILLING_DETAILS") ?></h5>
							<p class="small"> <?php echo JTEXT::_("LNG_BILLING_DETAILS_INFO") ?></p>
						</div>
						<div class="ibox-content">
						
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php } else {
			echo JText::_("LNG_NOT_AUTHORIZED");
		}
	?>
</div>


<script>

var chart = Morris.Area({
	element: 'graph',
	data: [{date: '<?php echo date("d-m-Y"); ?>', value: 0}],
	fillOpacity: 0.6,
	hideHover: 'auto',
	behaveLikeLine: true,
	resize: true,
	lineColors: ['#54cdb4'],
	xkey: 'date',
	ykeys: ['value'],
	labels: ['Total'],
    xLabelFormat: function(d) {
	    return jbdUtils.getDateWithFormat(d);
    },
    dateFormat: function(unixTime) {
        var d = new Date(unixTime);
        return jbdUtils.getDateWithFormat(d);
    }
});

var start_date = jQuery("#start_date").val();
var end_date = jQuery("#end_date").val();
var urlReport = jbdUtils.siteRoot+'index.php?option='+jbdUtils.componentName+'&task=useroptions.newCompanies';

requestData(urlReport, start_date, end_date, chart);


jQuery(document).ready(function() {
	jQuery("#tabs").tabs();
	var curTab = jQuery("#tabs").tabs('option', 'active');

	jQuery("#start_date, #end_date").bind("paste keyup change", function(e) {
		calendarChange();
	});

	jQuery("#tabs").click(function(e) {
		e.preventDefault();
		calendarChange();
	});
		
});

function openLink(link){
	document.location.href=link;
}

function requestData(urlReport, start_date, end_date, chart) {
	jQuery.ajax({
		url: urlReport,
		dataType: 'json',
		type: 'GET',
		data: { start_date: start_date, end_date: end_date }
	})
	.done(function(data) {
		console.log(JSON.stringify(data));
		chart.setData(data);
	})
	.fail(function(data) {
		console.log("Error");
		console.log(JSON.stringify(data));
	});
}

function calendarChange() {
	console.debug("calendar change");
	var curTab = jQuery("#tabs .ui-tabs-panel:visible").attr("id");
	var start_date = jQuery("#start_date").val();
	var end_date = jQuery("#end_date").val();
	var urlReport = jbdUtils.siteRoot+'index.php?option='+jbdUtils.componentName+'&task=useroptions.'+curTab;
	jQuery("#graph").appendTo("#"+curTab);
	requestData(urlReport, start_date, end_date, chart);
}

</script>
