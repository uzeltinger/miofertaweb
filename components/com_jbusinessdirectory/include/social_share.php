<?php 
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

$itemId = '';
$itemType = '';
$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view');

?>

    <div id="fb-root"></div>
    <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>

<div id="top-right-container">
    <?php if($appSettings->show_view_count) { ?>
        <span class="view-counter">
            <?php
            switch ($view){
                case "companies":
                    echo JText::_("LNG_BUSINESS_SEEN") . $this->company->viewCount . JText::_("LNG_BUSINESS_SEEN_TIMES");
                    break;
                case "event":
                    echo JText::_("LNG_EVENT_SEEN") . $this->event->view_count . JText::_("LNG_BUSINESS_SEEN_TIMES");
                    break;
                case "offer":
                    echo JText::_("LNG_OFFER_SEEN") . $this->offer->viewCount . JText::_("LNG_BUSINESS_SEEN_TIMES");
                    break;
            }
            ?>
        </span>
    <?php } ?>

    <?php if($appSettings->enable_socials) { ?>
    <!-- Button trigger modal -->
    <a href="#" class="btn btn-primary btn-xs" id="open_socials">
        <i class="dir-icon-share-square-o"></i> <?php echo JText::_('LNG_SHARE') ?>
    </a>
    <?php }?>
</div>

<?php if($appSettings->enable_socials) { ?>
	<!-- Modal -->
	<div id="socials" style="display:none;">
		<div id="dialog-container">
			<div class="titleBar">
				<span class="dialogTitle" id="dialogTitle"></span>
				<span title="Cancel" class="dialogCloseButton" onclick="jQuery.unblockUI();">
					<span title="Cancel" class="closeText">x</span>
				</span>
			</div>
			<div class="dialogContent">
				<div class="row-fluid">
					<div class="span3">
						<div class="item-image text-center">
							<?php if($view == 'company' || $view == 'companies') {
							    $itemId = $company->id; $itemType = STATISTIC_ITEM_BUSINESS; ?>
								<?php if (!empty($company->logoLocation)) { ?>
									<img src="<?php echo JURI::root().PICTURES_PATH.$company->logoLocation ?>" alt="<?php echo $this->escape($this->company->name)?>" class="img-responsive"/>
									
								<?php } else { ?>
									<img src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $this->escape($this->company->name)?>" class="img-responsive" />
								<?php } ?>
							<?php } ?>
							<?php if($view == 'offer') {
                                $itemId = $this->offer->id; $itemType = STATISTIC_ITEM_OFFER; ?>
								<?php if (!empty($this->offer->pictures[0]->picture_path)) { ?>
									<img src="<?php echo JURI::root().PICTURES_PATH.$this->offer->pictures[0]->picture_path ?>" alt="<?php echo $this->escape($this->offer->subject)?>" class="img-responsive"/>
									
								<?php } else { ?>
									<img src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $this->escape($this->offer->subject)?>" class="img-responsive" />
								<?php } ?>
							<?php } ?>
							<?php if($view == 'event') {
                                $itemId = $this->event->id; $itemType = STATISTIC_ITEM_EVENT; ?>
								<?php if (!empty($this->event->pictures[0]->picture_path)) { ?>
									<img src="<?php echo JURI::root().PICTURES_PATH.$this->event->pictures[0]->picture_path ?>" alt="<?php echo $this->escape($this->event->name)?>" class="img-responsive"/>

								<?php } else { ?>
									<img src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $this->escape($this->event->name)?>" class="img-responsive" />
								<?php } ?>
							<?php } ?>
						</div>
					</div>
					<div class="span9">
						<div class="row-fluid share">
							<div class="span12">
								<?php if($view == 'company' || $view == 'companies') { ?>
									<h4><?php echo isset($this->company->name)?$this->company->name:"" ; ?></h4>
									<?php if(!empty($company->slogan)) { ?>
										<p><?php echo $company->slogan; ?></p>
									<?php } else { ?>
										<p><?php echo $company->typeName; ?></p>
									<?php } ?>
								<?php } ?>
								<?php if($view == 'offer') { ?>
									<h4><?php echo isset($this->offer->subject)?$this->offer->subject:"" ; ?></h4>
									<?php if(!empty($this->offer->short_description)) { ?>
										<p><?php echo $this->offer->short_description; ?></p>
									<?php } else { ?>
										<p><i class="dir-icon-map-marker"></i> <?php echo JBusinessUtil::getAddressText($this->offer); ?></p>
									<?php } ?>
								<?php } ?>
								<?php if($view == 'event') { ?>
									<h4><?php echo isset($this->event->name)?$this->event->name:"" ; ?></h4>
									<?php if(!empty($this->event->short_description)) { ?>
										<p><?php echo $this->event->short_description; ?></p>
									<?php } else { ?>
										<p><i class="dir-icon-map-marker"></i> <?php echo JBusinessUtil::getAddressText($this->event); ?></p>
									<?php } ?>
								<?php } ?>
							</div>
							<div class="span12">
								<ul>
									<li>
										<div class="fb-like" data-href="<?php echo htmlspecialchars($url, ENT_QUOTES)?>" data-layout="button" data-action="like" data-show-faces="true" data-share="true"></div>
									</li>
									<li>
										<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
										<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
									</li>
									<li>
										<script src="https://platform.linkedin.com/in.js" type="text/javascript"> lang: <?php echo JFactory::getLanguage()->getTag()?></script>
										<script type="IN/Share" data-counter="right"></script>
									</li>
									<li>
										<!-- Place this tag in your head or just before your close body tag. -->
										<script src="https://apis.google.com/js/platform.js" async defer></script>
										
										<!-- Place this tag where you want the share button to render. -->
										<div class="g-plus" data-href="<?php echo htmlspecialchars($url, ENT_QUOTES);?>" data-action="share" data-annotation="none" data-width="65"></div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#open_socials').click(function() {
					<?php if($view == 'company' || $view == 'companies' || $view == 'offer' || $view == 'event' ){ ?>
			    increaseShareClicks(<?php echo $itemId ?>, <?php echo $itemType ?>);
				<?php } ?>
				jQuery.blockUI({ message: jQuery('#socials'), css: {width: 'auto', top: '5%', left:"0", position:"absolute", cursor:'default'} });
				jQuery('.blockUI.blockMsg').center();
				jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI);
			});
		});
	</script>

<?php } ?>