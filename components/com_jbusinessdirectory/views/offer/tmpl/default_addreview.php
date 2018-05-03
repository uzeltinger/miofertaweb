<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

$user = JFactory::getUser();

JBusinessUtil::includeValidation();
$app = JFactory::getApplication();
$data = $app->getUserState("com_jbusinessdirectory.add.review.data");

$menuItemId="";
if(!empty($this->appSettings->menu_item_id)){
	$menuItemId = "&Itemid=".$this->appSettings->menu_item_id;
}

$allowedNr = isset($this->appSettings->max_review_images)?$this->appSettings->max_review_images:6;
$allowedNr=($allowedNr<0)?0:$allowedNr;
?>
<div id="add-review" style="display:none">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=offers'.$menuItemId); ?>" method="post" name="addReview" id="addReview">
		<h2>
			<span class="heading-green">
				<?php echo JText::_('LNG_WRITE_A_REVIEW') ?>
			</span>
		</h2>

		<div class="add-review">
		<fieldset>
			<div class="style2-rating-label">
				<?php if($this->appSettings->offer_view==2){echo '<label for="rating" style="width: 30%">'; echo JText::_('LNG_OFFER_REVIEW_RATING_TEXT'); echo '</label>';} ?>
				<div class="form-item user-rating clearfix">
					<label for="rating"><?php echo JText::_('LNG_OFFER_REVIEW_RATING_TEXT') ?></label><div class="rating-criteria"></div>
					<input type="hidden" name="rating" id="rating" value="<?php echo isset($this->rating->rating)?$this->rating->rating:'0' ?>">
				</div>
			</div>

			<div class="form-item">
				<label for="name"><?php echo JText::_('LNG_NAME') ?></label>
				<div class="outer_input">
					<input placeholder="<?php if($this->appSettings->offer_view==2)echo JText::_('LNG_NAME') ?>"  type="text" name="name" id="name" class="validate[required]" value="<?php if ($this->appSettings->offer_view==1){echo $user->id>0?$user->name:"";} ?>" ><br>
				</div>
			</div>

			<div class="form-item">
				<label for="email"><?php echo JText::_('LNG_EMAIL') ?></label>
				<div class="outer_input">
					<input placeholder="<?php if($this->appSettings->offer_view==2)echo JText::_('LNG_EMAIL') ?>" type="text" name="email" id="email" class="validate[required,custom[email]]" value="<?php if ($this->appSettings->offer_view==1){echo $user->id>0?$user->email:"";} ?>"><br>
				</div>
			</div>

			<div class="form-item">
				<label for="subject"><?php echo JText::_('LNG_NAME_YOUR_REVIEW') ?></label>
				<div class="outer_input">
					<input placeholder="<?php if($this->appSettings->offer_view==2)echo JText::_('LNG_NAME_YOUR_REVIEW') ?>" type="text" name="subject" id="subject" class="validate[required]"  value="<?php if ($this->appSettings->offer_view==1)echo $data["subject"]; ?>"><br>
				</div>
			</div>


			<div class="form-item " >
				<label><?php echo JText::_('LNG_OFFER_REVIEW_DESCRIPTION_TXT')?></label>
				<div class="outer_input">
					<textarea placeholder="<?php if($this->appSettings->offer_view==2)echo JText::_('LNG_OFFER_REVIEW_DESCRIPTION_TXT') ?>" rows="10" name="description" id="description" class="validate[required]" ><?php if ($this->appSettings->offer_view==1 && isset($data["description"]))echo $data["description"]?></textarea><br>
				</div>
			</div>

			<?php if($allowedNr!=0) { ?>
				<div class="form-item">
					<label><?php echo JText::_('LNG_ADD_REVIEW_IMAGE_TEXT')?>:</label>
					<input type='button' name='btn_removefile' id='btn_removefile' value='x' style='display:none'>
					<input type='hidden' name='crt_pos' id='crt_pos' value=''>
					<input type='hidden' name='crt_path' id='crt_path' value=''>

					<ul id="sortable" class="images-list" >

					</ul>

					<div class="clear"></div>
					<br/> <br/>
					<div class="dropzone dropzone-previews" id="file-upload">
						<div id="actions" style="margin-left:-15px;margin-top:-15px;" class="row">
							<div class="col-lg-12">
								<!-- The fileinput-button span is used to style the file input field as button -->
								<span class="btn btn-success fileinput-button dz-clickable">
    								<span><?php echo JText::_('LNG_ADD_FILES'); ?></span>
    							</span>
								<button  class="btn btn-primary start" id="submitAll">
									<span><?php echo JText::_('LNG_UPLOAD_ALL'); ?></span>
								</button>
							</div>
						</div>
					</div>

				</div>
			<?php } ?>


			<div class="form-item">
				<input type="checkbox" class="validate[required] term-offer" name="claim-review-conditions" id="claim-review-conditions" value="1"> <a href="javascript:void(0)" id="agreementLinkReview"><?php echo JText::_('LNG_TERMS_AGREAMENT')?></a>
			</div>

			<div id="termAgreementReview" style="display: none;">
				<?php echo $this->appSettings->reviews_terms_conditions ?>
			</div>


			<?php if($this->appSettings->captcha){?>
				<div class="form-item">
					<?php
					$namespace="jbusinessdirectory.contact";
					$class=" required";

					$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

					if(!empty($captcha)){
						echo $captcha->display("captcha", "captcha-div-review", $class);
					}

					?>
				</div>
			<?php } ?>

			<input type="hidden" name="task" id="task" value="offer.saveReview" />
			<input type="hidden" name="review_type" id="review_type" value="<?php echo REVIEW_TYPE_OFFER ?>">
			<input type="hidden" name="userId" value="<?php $user = JFactory::getUser(); echo $user->id;?> " />
			<input type="hidden" name="itemId" value="<?php echo $this->offer->id?>" />
			<input type="hidden" name="ratingId" value="<?php echo isset($this->rating->id)?$this->rating->id:0 ?>" />
			<input type="hidden" name="CompanyOfOffer" id="CompanyOfOffer" value="<?php echo $this->offer->companyId ?>" />

			<div class="clearfix clear-left">

				<div class="button-row ">
					<button type="button" class="ui-dir-button" onclick="saveReview('addReview')">
							<span class="ui-button-text"><?php echo JText::_("LNG_SAVE_REVIEW")?></span>
					</button>
					<button type="button" class="ui-dir-button ui-dir-button-grey" onclick="cancelSubmitReview()">
							<span class="ui-button-text"><?php echo JText::_("LNG_CANCEL_REVIEW")?></span>
					</button>
				</div>
			</div>
		</fieldset>
		</div>


	</form>
</div>
<?php include JPATH_COMPONENT_SITE.'/assets/uploader.php'; ?>
<script>
    var allowedPictures = '<?php echo $allowedNr ?>';
	var reviewFolder = '<?php echo REVIEW_PICTURES_PATH.(0)."/" ?>';
	var removePath = '<?php echo JURI::root()?>/components/<?php echo JBusinessUtil::getComponentName()?>/assets/remove.php?_root_app=<?php echo urlencode(JPATH_COMPONENT_SITE)?>&_filename=';

	jQuery(document).ready(function () {
		jQuery( "#sortable" ).sortable();
		jQuery( "#sortable" ).disableSelection();
        setMaxPictures(allowedPictures);
		checkNumberOfPictures();
		imageUploaderDropzone("#file-upload", '<?php echo JURI::root()?>components/<?php echo JBusinessUtil::getComponentName()?>/assets/upload.php?t=<?php echo strtotime("now")?>&_root_app=<?php echo urlencode(JPATH_ROOT."/".PICTURES_PATH) ?>&picture_type=<?php echo PICTURE_TYPE_GALLERY?>&_target=<?php echo urlencode(REVIEW_PICTURES_PATH.(0)."/")?>',".fileinput-button","<?php echo JText::_('LNG_DRAG_N_DROP',true); ?>", reviewFolder , <?php echo $allowedNr ?> ,"addPicture");
	});
	btn_removefile(removePath);

	jQuery(document).ready(function() {

		jQuery("#agreementLinkReview").click(function () {
			jQuery("#termAgreementReview").toggle();
		});
	});

    renderOfferRatingCriteria('<?php echo COMPONENT_IMAGE_PATH?>');
</script>
