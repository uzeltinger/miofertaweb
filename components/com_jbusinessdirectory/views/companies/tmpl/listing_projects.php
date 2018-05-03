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
<?php if(!empty($this->companyProjects)){?>
<div id="company-projects-container" class="grid-style2 projects-container pagewidth clearfix grid4">
    <div class="grid-content row-fluid grid4">
        <?php $index = 0; foreach($this->companyProjects as $index=>$project){
        $index++; ?>
        <div class="grid-item span4">
            <div class="grid-content">
                <a onclick="showProjectDetail(<?php echo $project->id ?>);applyLighSlider();" href="javascript:void(0)">
                    <?php if (!empty($project->picture_path)) { ?>
                        <img title="<?php echo $project->name ?>" alt="<?php echo $project->name ?>"
                             src="<?php echo JURI::root() . PICTURES_PATH . $project->picture_path ?>">
                    <?php } else { ?>
                        <img title="<?php echo $project->name ?>" alt="<?php echo $project->name ?>"
                             src="<?php echo JURI::root() . PICTURES_PATH . '/no_image.jpg' ?>">
                    <?php } ?>
                </a>

                <div class="info"
                     onclick="document.location.href='javascript:showProjectDetail(<?php echo $project->id ?>)'">
                    <div class="hover_info">
                        <?php if (!empty($project->description)) { ?>
                            <div class="">
                                <?php echo JBusinessUtil::truncate($project->description, 150); ?>
                            </div>
                        <?php } ?>

                        <div class="item-vertical-middle">
                            <a onclick="showProjectDetail(<?php echo $project->id ?>);applyLighSlider();"
                               href="javascript:void(0)" class="btn-view"><?php echo JText::_("LNG_VIEW") ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-item-name">
                <h3 style="padding-bottom: 2px"><?php echo $project->name ?></h3>
                <span><?php echo $project->nrPhotos . " " . JText::_("LNG_PHOTOS"); ?> </span>
            </div>
        </div>
        <?php if ($index % 3 == 0){ ?>
    </div>
    <div class="grid-content row-fluid grid4">
        <?php }
        }
        ?>
    </div>
</div>

<div class="row-fluid projects-container" id="project-details" style="display: none">
    <div id="search-path">
        <ul>
            <li><?php echo JText::_("LNG_YOU_ARE_HERE")?>:</li>
            <li>
                <a href="javascript:returnToProjects();"><?php echo JText::_("LNG_PROJECTS"); ?></a>
            </li>
            &raquo;
            <li>
                <span id="project-name-link"></span>
            </li>
        </ul>
        <div class="clear"></div>
    </div>
    
	<div class="row-fluid project-content">
	    <div class="span4">
		    <h4 id="project-name"></h4>
	    	<div id="project-description"></div>
	    </div>
	
	    <div class="span8" id="project-image-container">
	        <div class="">
	            <div class="row-fluid">
	                <div class="span12">
	                    <div class='picture-container' id="project-gallery">
	                        <div style="clear:both;"></div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
</div>

<?php } ?>