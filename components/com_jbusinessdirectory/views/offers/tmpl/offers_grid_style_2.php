<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
$user = JFactory::getUser();

$showData = !($user->id==0 && $appSettings->show_details_user == 1);

?>

<!-- layout -->
<div id="layout" class="pagewidth clearfix grid4 grid-view-2" <?php echo !$this->appSettings->offers_view_mode?'style="display: none"':'' ?>>

<div id="grid-content" class="grid-content row-fluid grid4" itemscope itemtype="http://schema.org/OfferCatalog">

	<?php 
	if(!empty($this->offers)){
	    $index=0;
		foreach($this->offers as $index=>$offer){
            $index++;
		?>

		<article id="post-<?php echo $offer->id ?>" class="post clearfix span3" itemscope itemprop="itemListElement" itemtype="http://schema.org/Offer">
			<div class="post-inner">
				<h2 class="post-title"><a href="<?php echo $offer->link ?>"><span itemprop="name"><?php echo $this->escape($offer->subject)?></span></a></h2>
				<figure class="post-image" itemprop="url">
						<a href="<?php echo $offer->link ?>" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
							<?php if(!empty($offer->picture_path) ){?>
								<img title="<?php echo $this->escape($offer->subject)?>" alt="<?php echo $this->escape($offer->subject)?>" src="<?php echo JURI::root().PICTURES_PATH.$offer->picture_path ?>" itemprop="contentUrl">
							<?php }else{ ?>
								<img title="<?php echo $this->escape($offer->subject)?>" alt="<?php echo $this->escape($offer->subject)?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl">
							<?php } ?>
						</a>
				</figure>
				
				<!-- /.post-content -->
			</div>
		<!-- /.post-inner -->
		</article>
    <?php if ($index % 4 == 0){ ?>
    </div>
    <div id="grid-content" class="grid-content row-fluid grid4" itemscope itemtype="http://schema.org/OfferCatalog">
        <?php
        }
        }
        }
        ?>
	 <div class="clear"></div>
	</div>
</div>	
