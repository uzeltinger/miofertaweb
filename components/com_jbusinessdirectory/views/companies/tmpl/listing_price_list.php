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

<?php if ($this->appSettings->price_list_view == 1) { ?>
    <div class="service-statistics">
        <?php $header = "";
        if (!empty($this->services_list)) { ?>
            <?php foreach ($this->services_list as $key => $item) { ?>
                <div>
                    <?php if ($header != $item->service_section) { ?>
                        <div class="service-section expand_heading">
                            <h3><?php echo $item->service_section ?></h3>
                        </div>
                        <?php $header = $item->service_section;
                    } ?>
                    <div class="service-statistics-body toggle_container">
                        <ul>
                            <?php foreach ($this->services_list as $key2 => $service) {
                                if ($service->service_section == $header) {
                                    ?>
                                    <li class="service-item">
                                        <div class="row-fluid" style="height: 100%;">
                                            <div class="span2">
                                                <img alt="<?php echo $service->service_name ?>" class="img-rounded"
                                                     src="<?php echo !empty($service->service_image) ? JURI::root() . PICTURES_PATH . $service->service_image : JURI::root() . PICTURES_PATH . '/no_image.jpg' ?>"
                                                     style="">
                                            </div>
                                            <div class="span10">
                                                <h4 class="service-item-heading"><?php echo $service->service_name ?>
                                                    <span style="float: right" class="price red">
                                                <?php echo JBusinessUtil::getPriceFormat($service->service_price, $this->appSettings->currency_id); ?>
                                                </h4>
                                                </span>
                                                <p><?php echo $service->service_description ?></p>
                                            </div>
                                        </div>
                                    </li>
                                    <?php
                                    unset($this->services_list[$key2]);
                                } else {
                                    break;
                                } ?>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <h3><?php echo JText::_('LNG_NO_SERVICES'); ?></h3>
        <?php } ?>
        <br/>
    </div>
    <?php
}else { ?>
    <div class="pagewidth clearfix grid4">
        <div id="grid-content" class="grid-content row-fluid grid4">
		 <?php
       	 	if(isset($this->services_list) && !empty($this->services_list)){
                $index = 0;
            	foreach($this->services_list as $index=>$service){
                    $index++;
           ?>
				<article id="post-<?php echo  $service->id ?>" class="post clearfix span4">
					<div class="post-inner">
						<figure class="post-image "   >
							 <?php if(isset($service->service_image) && $service->service_image!=''){?>
	                         	<img title="<?php echo $service->service_name?>" alt="<?php echo $service->service_name?>" src="<?php echo JURI::root().PICTURES_PATH.$service->service_image ?>" >
	                         <?php }else{ ?>
	                             <img title="<?php echo $service->service_name?>" alt="<?php echo $service->service_name?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" >
	                         <?php } ?>
						</figure>
						
						 <div class="post-content">
		                     <h2 class="post-title"><span ><?php echo $service->service_name?></span></h2>
	                         <span class="price red"><?php echo JBusinessUtil::getPriceFormat($service->service_price, $this->appSettings->currency_id); ?></span>
	                         <p>
	                            <?php echo $service->service_description?>
	                          </p>
	
	                     </div>
					</div>
				</article>
            <?php if ($index % 3 == 0){ ?>
        </div>
        <div id="grid-content" class="grid-content row-fluid grid4">
            <?php } ?>
            <?php
            }
            }?>
       <div class="clear"></div>
    </div>
</div>
<?php } ?>


<script>
    jQuery(document).ready(function() {
        //Expand/Collapse Individual Boxes
        jQuery(".expand_heading").toggle(function(){
            jQuery(this).addClass("active");
        }, function () {
            jQuery(this).removeClass("active");
        });
        jQuery(".expand_heading").click(function(){
            jQuery(this).nextAll(".toggle_container:first").slideToggle("slow");
        });

        //Show hide 'expand all' and 'collapse all' text
        jQuery(".expand_all").toggle(function(){
            jQuery(this).addClass("expanded");
        }, function () {
            jQuery(this).removeClass("expanded");
        });

        jQuery(".expand_all").click(function () {
            if (jQuery(this).hasClass("expanded")) {
                jQuery(".toggle_container").slideDown("slow");
            }
            else {
                jQuery(".toggle_container").slideUp("slow");
            }
        });
    });
</script>
