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

<div class='grid-content offers-container grid4 row-fluid'>
    <?php
    if(isset($this->realtedCompanies) && count($this->realtedCompanies)){
        $index = 0;
        foreach ($this->realtedCompanies as $rCompany){
            $index++;
            ?>
            <article id="post-<?php echo  $rCompany->id ?>" class="post clearfix span4">
                <div class="post-inner">
                    <figure class="post-image ">
                        <a href="<?php echo JBusinessUtil::getCompanyLink($rCompany) ?>">
                            <?php if(!empty($rCompany->logoLocation) ){?>
                                <img title="<?php echo $rCompany->name?>" alt="<?php echo $rCompany->name?>" src="<?php echo JURI::root().PICTURES_PATH.$rCompany->logoLocation ?>" >
                            <?php }else{ ?>
                                <img title="<?php echo $rCompany->name?>" alt="<?php echo $rCompany->name?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" >
                            <?php } ?>
                        </a>
                    </figure>

                    <div class="post-content">
                        <h2 class="post-title"><a  href="<?php echo JBusinessUtil::getCompanyLink($rCompany) ?>"><span ><?php echo $rCompany->name?></span></a></h2>
                        <span class="post-date" ><i class="fa dir-icon-map-marker"></i> <?php echo $rCompany->address?>, <?php echo $rCompany->city	?>, <?php echo $rCompany->county?></span>


                    </div>
                    <!-- /.post-content -->
                </div>
                <!-- /.post-inner -->
            </article>
      <?php if($index%3==0){?>
    		</div>
    		<div class="grid-content offers-container row-fluid grid4">
  		<?php } ?>
    <?php } ?>
    
	<?php 
    }else{
        echo JText::_("LNG_NO_COMPANY_RELATED");
    }
    ?>

</div>
<div class="clear"></div>


