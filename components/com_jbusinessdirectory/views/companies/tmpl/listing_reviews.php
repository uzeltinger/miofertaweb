<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
?>

<a class="add-review-link" href="javascript:void(0)" onclick="addNewReview(<?php echo ($appSettings->enable_reviews_users && $user->id==0)?"1":"0"?>)"><?php echo JText::_("LNG_ADD_REVIEW") ?></a>
<div class="clear"></div>
<?php if(!$appSettings->enable_reviews_users || !$user->id ==0){?>
<?php require_once 'default_addreview.php'; ?>
<?php } ?>
<br/>	
<?php if(count($this->reviews)==0){ ?>
	<p><?php echo JText::_('LNG_NO_REVIEWS') ?></p>
<?php }else{ ?>
<ul id="reviews" itemprop="review" itemscope itemtype="https://schema.org/Review">
	<?php foreach($this->reviews as $review){?>
		<li class="review">
			<div class="review-content">
			
				<h4 itemprop="name"><?php echo $this->escape($review->subject) ?></h4>
				
				<div class="review-author">
					<p class="review-by-content">
					<span class="reviewer-name" itemprop="author"> <?php echo $this->escape($review->name) ?> </span>,
						<span class="review-date" itemprop="datePublished"><?php echo JBusinessUtil::getDateGeneralFormat($review->creationDate) ?></span>
					</p>
				</div>
			
				<div class="rating-block">
					<?php if(!empty($review->scores) && !empty($this->reviewCriterias)){ ?>
						<div class="review-rating" itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="reviewRating">
							<?php echo number_format($review->rating,1) ?>
							<span style="display:none;">
									<span itemprop="ratingValue"><?php echo number_format($review->rating,1) ?></span>
									<span itemprop="worstRating">0</span>
									<span itemprop="bestRating">5</span>
									<span itemprop="ratingCount"><?php echo count($this->reviewCriterias)?></span>
							</span>
							</div>
						<?php
                        if(isset($review->criteriaIds)) {
                            foreach ($review->criteriaIds as $key => $value) {
                                if (empty($this->reviewCriterias[$value]))
                                    continue;
                                ?>
                                <div class="review-criteria">
                                    <span class="review-criteria-name"><?php echo $this->reviewCriterias[$value]->name ?></span>
                                    <span title="<?php echo $review->scores[$key] ?>" class="rating-review"></span>
                                </div>
                            <?php }
                        }?>
					
					<?php }else{?>
						<div>
							<span title="<?php echo $review->rating ?>" class="rating-review"></span>
						</div>	
					<?php } ?>
					<div class="clear"></div>
				</div>
				<div class="review-questions" id="<?php echo $review->id; ?>">
					<?php if(!empty($review->answerIds) && !empty($this->reviewQuestions) && !empty($this->reviewAnswers)) { ?>
						<a style="display:none" href="javascript:void(0)" id="show-questions<?php echo $review->id; ?>" onclick="showReviewQuestions('<?php echo $review->id; ?>')"><?php echo JText::_('LNG_SHOW_REVIEW_QUESTIONS'); ?>	</a>
						<div id="review-questions<?php echo $review->id; ?>">
                            <?php if(!empty($review->questionIds)) { ?>
                            <?php foreach($review->questionIds as $key=>$value){
                                if(!isset($this->reviewQuestions[$value]))
                                    continue;
                                $question = $this->reviewQuestions[$value];
                                $answer = $this->reviewAnswers[$review->answerIds[$value]];
                            ?>
                            <?php if(isset($answer->answer)) { ?>
                                <div class="review-question"><strong><?php echo $question->name?></strong><?php echo (isset($answer->user_id) && $user->id==$answer->user_id && $user->id!=0) ? ' <i class="dir-icon-pencil" style="cursor:pointer;" onClick="editAnswer('.$answer->id.','.$question->type.')"></i>' : ''; ?></div>
                                <?php
                                if($question->type == 1) {
                                    if ($answer->answer == 0)
                                        $answer->answer = JText::_('LNG_NO');
                                    else if ($answer->answer == 1)
                                        $answer->answer = JText::_('LNG_YES');
                                }
                                $enableEditing = (isset($answer->user_id) && $user->id==$answer->user_id && $user->id!=0) ? 'ondblclick="editAnswer('.$answer->id.','.$question->type.')"' : '';
                                $editClass = (isset($answer->user_id) && $user->id==$answer->user_id && $user->id!=0) ? 'question-answer' : '';
                                ?>
                                <?php if($question->type != 2) { ?>
                                    <div <?php echo $enableEditing ?> class="review-question-answer <?php echo $editClass ?>" id="question-answer<?php echo $answer->id ?>"><?php echo $answer->answer ?></div>
                                <?php }
                                else { ?>
                                    <div id="question-answer<?php echo $answer->id ?>" class="review-question-answer star-rating <?php echo $editClass ?>"></div>
                                    <input type="hidden" id="star-rating-score<?php echo $answer->id ?>" value="<?php echo $answer->answer ?>" />
                                <?php } ?>
                                <?php } ?>
                                <?php } ?>
                            <?php } ?>
						</div>
					<?php } ?>
				</div>
				
				<div class="review-description" itemprop="description">
					<?php echo $this->escape($review->description) ?>
				</div>
				<?php if(isset($review->responses) && count($review->responses)>0) {
					foreach ($review->responses as $response) {
						?>
						<div class="review-response">
							<strong><?php echo JText::_('LNG_REVIEW_RESPONSE') ?></strong><br/>
							<span class="bold"><?php echo $this->escape($response->firstName) ?> </span>
							<p><?php echo $this->escape($response->response) ?></p>
						</div>
						<?php
					}
				}
				require 'review_gallery.php';
				?>
				
				<div class="review-actions">
					<ul>
						<li class="first">
							<a href="javascript:reportReviewAbuse(<?php echo $review->id?>)"><?php echo JText::_('LNG_REPORT_ABUSE') ?></a>
						</li>
						<li>
							<a href="javascript:respondToReview(<?php echo $review->id?>)"><?php echo JText::_('LNG_RESPOND_TO_REVIEW') ?></a>
						</li>
					</ul>
				</div>
				
				<div class="rate-review">
					<span class="rate"><?php echo JText::_("LNG_RATE_REVIEW")?>:</span>
					<ul>
						<li class="thumbup">
                            <a
							    id="increaseLike<?php echo $review->id ?>"
							    href="javascript:void(0)" onclick="increaseReviewLikeCount(<?php echo $review->id ?>)"><?php echo JText::_("LNG_THUMB_UP")?>
                            </a> <span class="count"> (<span id="like<?php echo $review->id ?>"><?php echo $review->likeCount ?></span>) </span>
						</li>
						<li class="thumbdown">
							<a  
							    id="decreaseLike<?php echo $review->id ?>"
							    href="javascript:void(0)" onclick="increaseReviewDislikeCount(<?php echo $review->id ?>)"><?php echo JText::_("LNG_THUMB_DOWN")?>
                            </a>
							<span class="count"> (<span id="dislike<?php echo $review->id ?>"><?php echo $review->dislikeCount ?></span>) </span>
						</li>
					</ul>
				</div>
				<div class="clear"></div>
			</div>
		</li>
	<?php } ?>
</ul>
<?php } ?>
<div class="clear"></div>

<div id="report-abuse" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			<h3 class="title"><?php echo JText::_('LNG_REPORT_ABUSE') ?></h3>
			<div class="dialogContentBody" id="dialogContentBody">
				<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" id="reportAbuse" name="reportAbuse"  method="post">
					<p>
						<?php echo JText::_("LNG_ABUSE_INFO");?>
					</p>
					<div class="report-abuse">
						<fieldset>
							<div class="form-item">
								<label><?php echo JText::_('LNG_EMAIL') ?></label>
								<div class="outer_input">
									<input type="text" name="email" id="email-abuse" class="input_txt  validate[required,custom[email]]"><br>
								</div>
							</div>
					
							<div class="form-item">
								<label><?php echo JText::_('LNG_REPORT_ABUSE_BECAUSE')?>:</label>
								<div class="outer_input">
									<textarea rows="5" name="description" id="description-abuse" class="input_txt  validate[required]"></textarea><br>
								</div>
							</div>

							<div class="form-item">
								<input type="checkbox" class="validate[required]" name="review-report-conditions" id="review-report-conditions" value="1"> <a href="javascript:void(0)" id="agreementLinkReviewReport"><?php echo JText::_('LNG_TERMS_AGREAMENT')?></a>
							</div>

							<div id="termAgreementReviewReport" style="display: none;">
								<?php echo $this->appSettings->reviews_terms_conditions ?>
							</div>


							<?php if($this->appSettings->captcha){?>
								<div class="form-item">
									<?php 
									$namespace="jbusinessdirectory.contact";
									$class=" required";
									
									$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
																		
									if(!empty($captcha)){	
										echo $captcha->display("captcha", "captcha-div-review-abuse", $class);
									}
									
									?>
								</div>
							<?php } ?>
									
							<div class="clearfix clear-left">
								<div class="button-row ">
									<button type="button" class="ui-dir-button" onclick="saveForm('reportAbuse')">
											<span class="ui-button-text"><?php echo JText::_("LNG_SUBMIT")?></span>
									</button>
									<button type="button" class="ui-dir-button ui-dir-button-grey" onclick="jQuery.unblockUI()">
											<span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
									</button>
								</div>
							</div>
						</fieldset>
					</div>
					
					<input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
					<input type='hidden' name='task' id="task" value='companies.reportAbuse'/>
					<input type='hidden' name='view' value='companies' />
					<input type="hidden" id="reviewId" name="reviewId" value="" />
					<input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
				</form>
			</div>
		</div>
	</div>
</div>


<div id="new-review-response" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			<h3 class="title"><?php echo JText::_('LNG_RESPOND_REVIEW') ?></h3>
			<div class="dialogContentBody" id="dialogContentBody">
				<form id="reviewResponseFrm" name ="reviewResponseFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" method="post">
					<p>
						<?php echo JText::_('LNG_RESPOND_REVIEW_INFO') ?>
					</p>
					<div class="review-repsonse">
						<fieldset>
		
							<div class="form-item">
								<label><?php echo JText::_('LNG_FIRST_NAME') ?></label>
								<div class="outer_input">
									<input type="text" name="firstName" id="firstName-respond" class="input_txt  validate[required]"><br>
								</div>
							</div>
		
							<div class="form-item">
								<label><?php echo JText::_('LNG_LAST_NAME') ?></label>
								<div class="outer_input">
									<input type="text" name="lastName" id="lastName-respond" class="input_txt  validate[required]"><br>
								</div>
							</div>
		
		
							<div class="form-item">
								<label><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
								<div class="outer_input">
									<input type="text" name="email" id="email-respond" class="input_txt  validate[required,custom[email]]"><br>
								</div>
							</div>
					
							<div class="form-item">
								<label><?php echo JText::_('LNG_REVIEW_RESPONSE')?>:</label>
								<div class="outer_input">
									<textarea rows="5" name="response" id="response" class="input_txt  validate[required]"></textarea><br>
								</div>
							</div>

							<div class="form-item">
								<input type="checkbox" class="validate[required]" name="review-response-conditions" id="review-response-conditions" value="1"> <a href="javascript:void(0)" id="agreementLinkReviewResponse"><?php echo JText::_('LNG_TERMS_AGREAMENT')?></a>
							</div>

							<div id="termAgreementReviewResponse" style="display: none;">
								<?php echo $this->appSettings->reviews_terms_conditions ?>
							</div>
							
							<?php if($this->appSettings->captcha){?>
								<div class="form-item">
									<?php 
										$namespace="jbusinessdirectory.contact";
										$class=" required";
										
										$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
																			
										if(!empty($captcha)){	
											echo $captcha->display("captcha", "captcha-div-review-response", $class);
										}
									?>
								</div>
							<?php } ?>
		
							<div class="clearfix clear-left">
								<div class="button-row ">
									<button type="button" class="ui-dir-button" onclick="saveForm('reviewResponseFrm')">
											<span class="ui-button-text"><?php echo JText::_("LNG_SUBMIT")?></span>
									</button>
									<button type="button" class="ui-dir-button ui-dir-button-grey" onclick="jQuery.unblockUI()">
											<span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
									</button>
								</div>
							</div>
						</fieldset>
					</div>
					
			 		<input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
					<input type='hidden' name='task' id="task" value='companies.saveReviewResponse'/>
					<input type='hidden' name='view' value='companies' />
					<input type="hidden" id="reviewId" name="reviewId" value="" />
					<input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function () {
		jQuery("#agreementLinkReviewReport").click(function () {
			jQuery("#termAgreementReviewReport").toggle();
		});

		jQuery("#agreementLinkReviewResponse").click(function () {
			jQuery("#termAgreementReviewResponse").toggle();
		});
	});
	<?php if(count($this->reviewAnswers) > 0) { ?>
	jQuery(document).ready(function(){
        jQuery('.review-questions').each(function(){
			showReviewQuestions(jQuery(this).attr('id'));
		});
	});
	<?php } ?>
</script>