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

<div id="grid-content" class='offers-container grid-content grid4  row-fluid'>
    <?php
    if(!empty($this->associatedCompanies)){
        $index = 0;
        foreach ($this->associatedCompanies as $company){
            $index++;
            ?>
            <article id="post-<?php echo  $company->id ?>" class="post clearfix span3">
                <div class="post-inner">
                    <figure class="post-image">
                        <a href="<?php echo JBusinessUtil::getCompanyLink($company) ?>" target="_blank">
                            <?php if(!empty($company->logoLocation) ){?>
                                <img title="<?php echo $company->name?>" alt="<?php echo $company->name?>" src="<?php echo JURI::root().PICTURES_PATH.$company->logoLocation ?>" >
                            <?php }else{ ?>
                                <img title="<?php echo $company->name?>" alt="<?php echo $company->name?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" >
                            <?php } ?>
                        </a>
                    </figure>

                    <div class="post-content">
                        <h2 class="post-title"><a  href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"><span ><?php echo $company->name?></span></a></h2>
                        <span class="post-date" ><?php echo $company->address?>, <?php echo $company->city	?>, <?php echo $company->county?></span>


                    </div>
                    <!-- /.post-content -->
                </div>
                <!-- /.post-inner -->
            </article>
          <?php if($index%4==0){?>
        		</div>
        		<div class="grid-content offers-container row-fluid grid4">
      		<?php } ?>
  	  <?php } 
    }else{
        echo JText::_("LNG_NO_ASSOCIATED_COMPANIES");
    }
    ?>

</div>
<div class="clear"></div>


