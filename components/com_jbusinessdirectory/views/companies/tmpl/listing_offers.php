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
<div class="grid-content offers-container row-fluid grid4">
	<?php
		if(isset($this->offers) && !empty($this->offers)){
		    $index = 0;
			foreach ($this->offers as $offer){
			    $index++;
	?>
		<article id="post-<?php echo $offer->id ?>" class="post clearfix span4">
			<div class="post-inner">
				<figure class="post-image">
						<a href="<?php echo $offer->link ?>">
							<?php if(!empty($offer->picture_path) ){?>
								<img title="<?php echo $this->escape($offer->subject) ?>" alt="<?php echo $this->escape($offer->subject) ?>" src="<?php echo JURI::root().PICTURES_PATH.$offer->picture_path ?>" >
							<?php }else{ ?>
								<img title="<?php echo $this->escape($offer->subject) ?>" alt="<?php echo $this->escape($offer->subject) ?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" >
							<?php } ?>
						</a>
				</figure>
				
				<div class="post-content">
					<h2 class="post-title" style="margin-bottom: 0px"><a  href="<?php echo  $offer->link ?>"><span ><?php echo $offer->subject?></span></a></h2>
                    <div class="offer-price" style="margin-bottom: 15px">
                        <?php if(!empty($offer->price) ){ ?>
                            <span class="<?php echo $offer->specialPrice>0 ?"old-price":"" ?>"><?php echo JBusinessUtil::getPriceFormat($offer->price, $offer->currencyId) ?></span>
                        <?php } ?>
                        <?php if(!empty($offer->specialPrice)){?>
                            <span class="price red"><?php echo JBusinessUtil::getPriceFormat($offer->specialPrice, $offer->currencyId); ?></span>
                        <?php }?>
                        <?php if(!empty($offer->specialPrice) && !empty($offer->price) && $offer->specialPrice < $offer->price){ ?>
                            <br />
                            <span class="price"><?php echo JText::_('LNG_DISCOUNT') ?></span>
                            <span class="price red"><?php echo JBusinessUtil::getPriceDiscount($offer->specialPrice, $offer->price) ?>%</span>
                        <?php } ?>
                        <?php if (!empty($offer->price_text)) { ?>
                            <br/>
                            <span  class="price red"><?php echo $offer->price_text ?></span>
                        <?php }elseif (empty($offer->price) && empty($offer->specialPrice) && ($this->appSettings->show_offer_free)){ ?>
                            <span class="price red"><?php echo JText::_('LNG_FREE') ?></span>
                        <?php } ?>
                    </div>
					<span class="post-date" ><?php echo JBusinessUtil::composeAddress($offer->address, $offer->city) ?></span>
					<p class="offer-dates">
						<?php 
							echo JBusinessUtil::getDateGeneralFormat($offer->startDate)." - ".JBusinessUtil::getDateGeneralFormat($offer->endDate);
						?>
					</p>
					
					<?php if(!empty($offer->categories)){ ?>
					<p class="company-clasificaiton">
						<span class="offer-categories">
							<?php 
								foreach($offer->categories as $i=>$category){
									?>
										 <a href="<?php echo JBusinessUtil::getOfferCategoryLink($category[0], $category[2]) ?>"><?php echo $this->escape($category[1]) ?><?php echo $i<(count($offer->categories)-1)? ',&nbsp;':'' ?> </a>
								<?php }	?>
						</span> <br/>
					</p>
					<?php } ?>

                    <?php if($this->appSettings->enable_bookmarks && false) { ?>
                        <?php if(!empty($offer->bookmark)){?>
                            <a href="javascript:showUpdateBookmarkDialog(<?php echo $user->id==0?"1":"0"?>, 'update-bookmark-offer-<?php echo $offer->id ?>')"  title="<?php echo JText::_("LNG_UPDATE_BOOKMARK")?>" class="bookmark right"><i class="dir-icon-heart"></i></a>
                        <?php }else{?>
                            <a href="javascript:addBookmark(<?php echo $user->id==0?"1":"0"?>, 'add-bookmark-offer-<?php echo $offer->id ?>')" title="<?php echo JText::_("LNG_ADD_BOOKMARK")?>" class="bookmark right"><i class="dir-icon-heart-o"></i></a>
                        <?php } ?>
                    <?php } ?>
				</div>
				<span style="display: none;" ><?php echo JBusinessUtil::getDateGeneralFormat($offer->startDate)?></span>
				<span style="display: none;" ><?php echo JBusinessUtil::getDateGeneralFormat($offer->endDate)?></span>
				<!-- /.post-content -->
			</div>
		<!-- /.post-inner -->
		</article>

        <?php if($user->id>0 && false){?>
            <div id="add-bookmark-offer-<?php echo $offer->id ?>" style="display:none">
                <div id="dialog-container">
                    <div class="titleBar">
                        <span class="dialogTitle" id="dialogTitle"></span>
                        <span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
                        <span title="Cancel" class="closeText">x</span>
                        </span>
                    </div>

                    <div class="dialogContent">
                        <h3 class="title"><?php echo JText::_('LNG_ADD_BOOKMARK') ?></h3>
                        <div class="dialogContentBody" id="dialogContentBody">
                            <form id="bookmarkFrm" name="bookmarkFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
                                <div class="review-repsonse">
                                    <fieldset>
                                        <div class="form-item">
                                            <label><?php echo JText::_('LNG_NOTE')?>:</label>
                                            <div class="outer_input">
                                                <textarea rows="5" name="note" id="note" cols="50" ></textarea><br>
                                            </div>
                                        </div>

                                        <div class="clearfix clear-left">
                                            <div class="button-row ">
                                                <button type="submit" class="ui-dir-button">
                                                    <span class="ui-button-text"><?php echo JText::_("LNG_ADD")?></span>
                                                </button>
                                                <button type="button" class="ui-dir-button ui-dir-button-grey" onclick="jQuery.unblockUI()">
                                                    <span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
                                                </button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <?php echo JHTML::_( 'form.token' ); ?>
                                <input type='hidden' name='task' value='offer.addBookmark'/>
                                <input type='hidden' name='user_id' value='<?php echo $user->id?>'/>
                                <input type='hidden' name='item_type' value='<?php echo BOOKMARK_TYPE_OFFER ?>'/>
                                <input type='hidden' name='item_link' value='<?php echo JBusinessUtil::getCompanyLink($this->company) ?>'/>
                                <input type="hidden" name='item_id' value="<?php echo $offer->id?>" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if($user->id>0 && false){?>
            <div id="update-bookmark-offer-<?php echo $offer->id ?>" style="display:none">
                <div id="dialog-container">
                    <div class="titleBar">
                        <span class="dialogTitle" id="dialogTitle"></span>
                        <span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
                        <span title="Cancel" class="closeText">x</span>
                        </span>
                    </div>

                    <div class="dialogContent">
                        <h3 class="title"><?php echo JText::_('LNG_UPDATE_BOOKMARK') ?></h3>
                        <div class="dialogContentBody" id="dialogContentBody">
                            <form id="updateBookmarkFrm" name="bookmarkFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
                                <div class="review-repsonse">
                                    <fieldset>
                                        <div class="form-item">
                                            <a href="javascript:removeBookmark('offer')" class="red"> <?php echo JText::_("LNG_REMOVE_BOOKMARK")?></a>
                                        </div>
                                        <div class="form-item">
                                            <label><?php echo JText::_('LNG_NOTE')?>:</label>
                                            <div class="outer_input">
                                                <textarea rows="5" name="note" id="note" cols="50" ><?php echo isset($offer->bookmark)?$offer->bookmark->note:"" ?></textarea>
                                            </div>
                                        </div>

                                        <div class="clearfix clear-left">
                                            <div class="button-row ">
                                                <button type="submit" class="ui-dir-button">
                                                    <span class="ui-button-text"><?php echo JText::_("LNG_UPDATE")?></span>
                                                </button>
                                                <button type="button" class="ui-dir-button ui-dir-button-grey" onclick="jQuery.unblockUI()">
                                                    <span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
                                                </button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <?php echo JHTML::_( 'form.token' ); ?>
                                <input type='hidden' id="task" name='task' value='offer.updateBookmark'/>
                                <input type='hidden' name='id' value='<?php echo $offer->bookmark->id ?>'/>
                                <input type='hidden' name='user_id' value='<?php echo $user->id?>'/>
                                <input type='hidden' name='item_type' value='<?php echo BOOKMARK_TYPE_OFFER ?>'/>
                                <input type='hidden' name='item_link' value='<?php echo JBusinessUtil::getCompanyLink($this->company) ?>'/>
                                <input type="hidden" name="item_id" value="<?php echo $offer->id?>" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        
        <?php if($index%3==0){?>
    		</div>
    		<div class="grid-content offers-container row-fluid grid4">
  		<?php } ?>
    <?php } ?>
    
	<?php 
	       
		}else{
			echo JText::_("LNG_NO_COMPANY_OFFERS");
		}
	?>
	
</div>
<div class="clear"></div>	
			
		
	