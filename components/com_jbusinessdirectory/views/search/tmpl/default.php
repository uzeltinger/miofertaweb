<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
$config = new JConfig();

//retrieving current menu item parameters
$currentMenuId = null;
$activeMenu = JFactory::getApplication()->getMenu()->getActive();
$menuItemId="";
if(isset($activeMenu)){
	$currentMenuId = $activeMenu->id ; // `enter code here`
	$menuItemId="&Itemid=".$currentMenuId;
}
else if(!empty($this->appSettings->menu_item_id)){
	$menuItemId = "&Itemid=".$this->appSettings->menu_item_id;
}
$document = JFactory::getDocument(); // `enter code here`
$app = JFactory::getApplication(); // `enter code here`
if(isset($activeMenu)) {
	$menuitem   = $app->getMenu()->getItem($currentMenuId); // or get item by ID `enter code here`
	$params = $menuitem->params; // get the params `enter code here`
} else {
	$params = null;
}

//set page title
if(!empty($params) && $params->get('page_title') != '') {
	$title = $params->get('page_title', '');
}
if(empty($title)) {
	$title = JText::_("LNG_BUSINESS_LISTINGS");
	
	if(!empty($this->category->name) || !empty($this->citySearch) || !empty($this->regionSearch) || !empty($this->countrySearch)){
		$title .= " ".JText::_("LNG_IN")." ";
	}
	
	$items = array();
	if(!empty($this->category->name))
		$items[] = $this->category->name;
	if(!empty($this->citySearch))
		$items[] = $this->citySearch;
	if(!empty($this->regionSearch))
		$items[] = $this->regionSearch;
	if(!empty($this->countrySearch))
		$items[]= $this->country->country_name;
    if(!empty($this->provinceSearch))
        $items[]= $this->provinceSearch;

	if(!empty($items)){
		$title .= implode("|",$items);
	}
}
$document->setTitle($title);

//set page meta description and keywords
$description = $this->appSettings->meta_description;
$document->setDescription($description);
$document->setMetaData('keywords', $this->appSettings->meta_keywords);

if(!empty($params) && $params->get('menu-meta_description') != '') {
	$document->setMetaData( 'description', $params->get('menu-meta_description') );
	$document->setMetaData( 'keywords', $params->get('menu-meta_keywords') );
}

if (isset($this->category)){
	if (!empty($this->category->meta_title))
		$document->setTitle($this->category->meta_title);
	if (!empty($this->category->meta_description))
		$document->setMetaData( 'description', $this->category->meta_description );
	if (!empty($this->category->meta_keywords))
		$document->setMetaData( 'keywords', $this->category->meta_keywords );
}

$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
$user = JFactory::getUser();
$enableSearchFilter = $this->appSettings->enable_search_filter;
$fullWidth = true;
$mposition = "dir-search-listing-top";
$topModules = JModuleHelper::getModules($mposition);
$mposition = "dir-search-listing";
if(!empty($this->category)){
    $mposition = "dir-search-".$this->category->alias;
}
$bottomModules = JModuleHelper::getModules($mposition);

if($enableSearchFilter || !empty($topModules) || !empty($bottomModules)){
    $fullWidth = false;
}

//add the possibility to chage the view and layout from http params
$list_layout = JRequest::getVar('list_layout');
if(!empty($list_layout)) {
	$this->appSettings->search_result_view = $list_layout;
}
$view_mode = JRequest::getVar('view_mode');
if(!empty($view_mode)) {
	$this->appSettings->search_view_mode = $view_mode;
}

$setCategory = isset($this->category)?1:0;
$categId = isset($this->categoryId)?$this->categoryId:0;


$showClear = 0;
$url = "index.php?option=com_jbusinessdirectory&view=search";

?>

<?php if (!empty($this->params) && $this->params->get('show_page_heading', 1)) { ?>
    <div class="page-header">
        <h1 class="title"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
<?php } ?>


<div class="row-fluid">
<?php if(!$fullWidth){?>
	<div class="span3">
    	<?php if(!empty($topModules)) { ?>
    		<div class="search-modules">
    			<?php 
    			foreach($topModules as $module) {
    				echo JModuleHelper::renderModule($module);
    			} ?>
    		</div>
    	<?php } ?>	
        <?php if($enableSearchFilter){?>
        	<div id="search-filter" class="search-filter">
            	<div class="filter-fav clear" style="display:none"> 
            		<a href="javascript:filterByFavorites(<?php echo $user->id==0?'false':'true' ?>)" style="float:right;padding:5px;"><?php echo JText::_('LNG_FILTER_BY_FAVORITES'); ?></a>
            	</div>
            	<div class="search-category-box">
            		 <?php if(!empty($this->location["latitude"])) { ?>
            		 	<div class="filter-criteria">
            				<div class="filter-header"><?php echo JText::_("LNG_DISTANCE"); ?></div>
            				<ul>
            					<li>
            						<?php if($this->radius != 50) { ?>
            							<a href="javascript:setRadius(50)">50 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></a>
            						<?php } else { ?>
            							<strong>50 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></strong>
            						<?php } ?>
            					</li>
            					<li>
            						<?php if($this->radius != 25) { ?>
            							<a href="javascript:setRadius(25)">25 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></a>
            						<?php } else { ?>
            							<strong>25 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></strong>
            						<?php } ?>
            					</li>
            					<li>
            						<?php if($this->radius != 10) { ?>
            							<a href="javascript:setRadius(10)">10 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></a>
            						<?php } else { ?>
            							<strong>10 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></strong>
            						<?php } ?>
            					</li>
            					<li>
            						<?php if($this->radius != 0) { ?>
            							<a href="javascript:setRadius(0)"><?php echo JText::_("LNG_ALL")?></a>
            						<?php } else { ?>
            							<strong><?php echo JText::_("LNG_ALL")?></strong>
            						<?php } ?>
            					</li>
            				</ul>
            			</div>
            		<?php } ?>
            
            		<div id="filterCategoryItems"  class="">
            		<?php if(!empty($this->searchFilter["categories"])) { ?>
            			<div class="filter-criteria">
            		        <div class="filter-header"><?php echo JText::_("LNG_CATEGORIES") ?></div>
            		        <?php if ($this->appSettings->search_type == 0) {
            		            $counterCategories = 0; ?>
            		            <ul>
            		                <?php foreach ($this->searchFilter["categories"] as $filterCriteria) {
            		                    if ($counterCategories < $this->appSettings->search_filter_items) {
            		                        if ($filterCriteria[1] > 0) { ?>
            		                            <li>
            		                                <?php if (isset($this->category) && $filterCriteria[0][0]->id == $this->category->id) { ?>
            		                                    <strong><?php echo $filterCriteria[0][0]->name; ?>
            		                                        &nbsp;</strong>
            		                                <?php } else { ?>
            		                                    <a href="javascript:chooseCategory(<?php echo $filterCriteria[0][0]->id ?>)"><?php echo $filterCriteria[0][0]->name; ?></a>
            		                                <?php } ?>
            		                            </li>
            		                        <?php }
            		                        $counterCategories++;
            		                    } else { ?>
            		                        <a id="showMoreCategories" class="filterExpand" href="javascript:void(0)"
            		                           onclick="showMoreParams('extra_categories_params','showMoreCategories')"><?php echo JText::_("LNG_MORE") . " (+)" ?></a>
            		                        <?php break;
            		                    }
            		                } ?>

            		                <div style="display: none" id="extra_categories_params">
            		                    <?php
            		                    foreach ($this->searchFilter["categories"] as $filterCriteria) {
            		                        $counterCategories--; ?>
            		                        <?php if ($counterCategories >= 0) {
            		                            continue;
            		                        } else {
            		                            if ($filterCriteria[1] > 0) { ?>
            		                                <li>
            		                                    <?php if (isset($this->category) && $filterCriteria[0][0]->id == $this->category->id) { ?>
            		                                        <strong><?php echo $filterCriteria[0][0]->name; ?>
            		                                            &nbsp;</strong>
            		                                    <?php } else { ?>
            		                                        <a href="javascript:chooseCategory(<?php echo $filterCriteria[0][0]->id ?>)"><?php echo $filterCriteria[0][0]->name; ?></a>
            		                                        <?php //echo '('.$filterCriteria[1].')' ?>
            		                                    <?php } ?>
            		                                </li>
            		                            <?php }
            		                        }
            		                    } ?>
            		                    <a id="showLessCategories" class="filterExpand" href="javascript:void(0)"
            		                       onclick="showLessParams('extra_categories_params','showMoreCategories')"><?php echo JText::_("LNG_LESS") . " (-)" ?></a>
            		                </div>
            		            </ul>
            		        <?php } else { ?>
            		            <ul class="filter-categories">
            		                <?php $counterCategories = 0;
            		                foreach ($this->searchFilter["categories"] as $filterCriteria) {
            		                    if ($counterCategories < $this->appSettings->search_filter_items) {
            		                        if ($filterCriteria[1] > 0) { ?>
            		                            <li <?php if (in_array($filterCriteria[0][0]->id, $this->selectedCategories)) echo 'class="selectedlink"'; ?>>
            		                                <div <?php if (in_array($filterCriteria[0][0]->id, $this->selectedCategories)) echo 'class="selected"'; ?>>
            		                                    <a href="javascript:void(0)" class="filter-main-cat"
            		                                       onclick="<?php echo in_array($filterCriteria[0][0]->id, $this->selectedCategories) ? "removeFilterRuleCategory(" . $filterCriteria[0][0]->id . ")" : "addFilterRuleCategory(" . $filterCriteria[0][0]->id . ")"; ?>"> <?php echo $filterCriteria[0][0]->name ?><?php echo in_array($filterCriteria[0][0]->id, $this->selectedCategories) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            		                                    <?php //echo '('.$filterCriteria[1].')' ?>
            		                                </div>
            		                                <?php if (isset($filterCriteria[0]["subCategories"])) { ?>
            		                                    <ul>
            		                                        <?php foreach ($filterCriteria[0]["subCategories"] as $subcategory) { ?>
            		                                            <li <?php if (in_array($subcategory[0]->id, $this->selectedCategories)) echo 'class="selectedlink"'; ?>>
            		                                                <div <?php if (in_array($subcategory[0]->id, $this->selectedCategories)) echo 'class="selected"'; ?>>
            		                                                    <a href="javascript:void(0)"
            		                                                       onclick="<?php echo in_array($subcategory[0]->id, $this->selectedCategories) ? "removeFilterRuleCategory(" . $subcategory[0]->id . ")" : "addFilterRuleCategory(" . $subcategory[0]->id . ")"; ?>"> <?php echo $subcategory[0]->name ?><?php echo in_array($subcategory[0]->id, $this->selectedCategories) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            		                                                </div>
            		                                            </li>
            		                                        <?php } ?>
            		                                    </ul>
            		                                <?php } ?>
            		                            </li>
        		                                <?php  $counterCategories++; ?>
               		                        <?php }?>
            		                    <?php } else { ?>
            		                        <a id="showMoreCategories1" class="filterExpand" href="javascript:void(0)"
            		                           onclick="showMoreParams('extra_categories_params1','showMoreCategories1')"><?php echo JText::_("LNG_MORE") . " (+)" ?></a>
            		                        <?php break;
            		                    }
            		                } ?>
            		                <div style="display: none" id="extra_categories_params1">
            		                    <?php
            		                    foreach ($this->searchFilter["categories"] as $filterCriteria) {
            		                        $counterCategories--; ?>
            		                        <?php if ($counterCategories >= 0) {
            		                            continue;
            		                        } else {
            		                            if ($filterCriteria[1] > 0) { ?>
            		                                <li <?php if (in_array($filterCriteria[0][0]->id, $this->selectedCategories)) echo 'class="selectedlink"'; ?>>
            		                                    <div <?php if (in_array($filterCriteria[0][0]->id, $this->selectedCategories)) echo 'class="selected"'; ?>>
            		                                        <a href="javascript:void(0)" class="filter-main-cat"
            		                                           onclick="<?php echo in_array($filterCriteria[0][0]->id, $this->selectedCategories) ? "removeFilterRuleCategory(" . $filterCriteria[0][0]->id . ")" : "addFilterRuleCategory(" . $filterCriteria[0][0]->id . ")"; ?>"> <?php echo $filterCriteria[0][0]->name ?><?php echo in_array($filterCriteria[0][0]->id, $this->selectedCategories) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            		                                        <?php //echo '('.$filterCriteria[1].')' ?>
            		                                    </div>
            		                                    <?php if (isset($filterCriteria[0]["subCategories"])) {
            		                                        $counterCategories = 0; ?>
            		                                        <ul>
            		                                            <?php foreach ($filterCriteria[0]["subCategories"] as $subcategory) { ?>
            		                                                <li <?php if (in_array($subcategory[0]->id, $this->selectedCategories)) echo 'class="selectedlink"'; ?>>
            		                                                    <div <?php if (in_array($subcategory[0]->id, $this->selectedCategories)) echo 'class="selected"'; ?>>
            		                                                        <a href="javascript:void(0)"
            		                                                           onclick="<?php echo in_array($subcategory[0]->id, $this->selectedCategories) ? "removeFilterRuleCategory(" . $subcategory[0]->id . ")" : "addFilterRuleCategory(" . $subcategory[0]->id . ")"; ?>"> <?php echo $subcategory[0]->name ?><?php echo in_array($subcategory[0]->id, $this->selectedCategories) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            		                                                    </div>
            		                                                </li>
            		                                            <?php } ?>
            		                                        </ul>
            		                                    <?php } ?>
            		                                </li>
            		                            <?php }
            		                        }
            		                    } ?>
            		                    <a id="showLessCategories1" class="filterExpand" href="javascript:void(0)"
            		                       onclick="showLessParams('extra_categories_params1','showMoreCategories1')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
            		                </div>
            		            </ul>
            		        
            	        		<?php } ?>
            	        		<div class="clear"></div>
            	        	</div>
            	    	<?php } ?>
            		
            			<?php $searchType = 1;?>
            
                        <?php if(!empty($this->searchFilter["starRating"])) { ?>
                            <div class="filter-criteria">
                                <div class="filter-header"><?php echo JText::_("LNG_STAR_RATING") ?></div>
                                <ul>
                                    <?php
                                    foreach($this->searchFilter["starRating"] as $filterCriteria) { ?>
                                        <?php if(empty($filterCriteria->reviewScore)) continue; ?>
                                        <?php $selected = isset($this->selectedParams["starRating"]) && in_array($filterCriteria->reviewScore, $this->selectedParams["starRating"]); ?>
                                        <li <?php if($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
                                            <div <?php if($selected) echo 'class="selected"'; ?>>
                                                <a href="javascript:void(0)" onclick="<?php echo $selected?"removeFilterRule('starRating', ".$filterCriteria->reviewScore.")":"addFilterRule('starRating', ".$filterCriteria->reviewScore.")";?>"><?php echo $filterCriteria->reviewScore." ".JText::_("LNG_STARS"); ?><?php echo ($selected)?'<span class="cross">(remove)</span>':"";  ?></a>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>

            			<?php if(!empty($this->searchFilter["types"])) { ?>
            				<div class="filter-criteria">
            		            <div class="filter-header"><?php echo JText::_("LNG_TYPES") ?></div>
            		            <ul>
            		                <?php $counterTypes = 0;
            		                foreach ($this->searchFilter["types"] as $filterCriteria) { ?>
            		                    <?php if (empty($filterCriteria->typeName)){continue;} ?>
            		                    <?php if ($counterTypes < $this->appSettings->search_filter_items) { ?>
            		                        <?php $selected = isset($this->selectedParams["type"]) && in_array($filterCriteria->typeId, $this->selectedParams["type"]); ?>
            		                        <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
            		                            <div <?php if ($selected) echo 'class="selected"'; ?>>
            		                                <a href="javascript:void(0)"
            		                                   onclick="<?php echo ($selected) ? "removeFilterRule('type', " . $filterCriteria->typeId . ")" : "addFilterRule('type', " . $filterCriteria->typeId . ")"; ?>"><?php echo $filterCriteria->typeName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            		                            </div>
            		                        </li>
            		                        <?php $counterTypes++;
            		                    } else { ?>
            		                        <a id="showMoreTypes" class="filterExpand" href="javascript:void(0)"
            		                           onclick="showMoreParams('extra_types_params','showMoreTypes')"><?php echo JText::_("LNG_MORE") . " (+)"  ?></a>
            		                        <?php break;
            		                    }
            		                }
            		                ?>
            		                <div style="display: none" id="extra_types_params">
            		                    <?php
            		                    foreach ($this->searchFilter["types"] as $filterCriteria) {
            		                      	if (empty($filterCriteria->typeName)) {
            		                            continue;
            		                      	}else if ($counterTypes > 0) {
            		                           $counterTypes--; 
            		                           continue;
            		                        } else {
            		                            $selected = isset($this->selectedParams["type"]) && in_array($filterCriteria->typeId, $this->selectedParams["type"]); ?>
            		                            <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
            		                                <div <?php if ($selected) echo 'class="selected"'; ?>>
            		                                    <a href="javascript:void(0)"
            		                                       onclick="<?php echo ($selected) ? "removeFilterRule('type', " . $filterCriteria->typeId . ")" : "addFilterRule('type', " . $filterCriteria->typeId . ")"; ?>"><?php echo $filterCriteria->typeName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            		                                </div>
            		                            </li>
            		                            <?php
            		                        }
            		                    } ?>
            		                    <a id="showLessTypes" class="filterExpand" href="javascript:void(0)"
            		                       onclick="showLessParams('extra_types_params','showMoreTypes')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
            		                </div>
            		            </ul>
            		            <div class="clear"></div>
            		          </div>
            	        <?php } ?>
            	     
            			<?php if(!empty($this->searchFilter["countries"])) { ?>
            				<div class="filter-criteria">
            	            	<div class="filter-header"><?php echo JText::_("LNG_COUNTRIES") ?></div>
            		            <ul>
            		                <?php $counterCountries = 0;
            		                foreach ($this->searchFilter["countries"] as $filterCriteria) { ?>
            		                    <?php if (empty($filterCriteria->countryName)){continue;}?>
            		                    <?php $selected = isset($this->selectedParams["country"]) && in_array($filterCriteria->countryId, $this->selectedParams["country"]); ?>
            		                    <?php if ($counterCountries < $this->appSettings->search_filter_items) { ?>
            		                        <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
            		                            <div <?php if ($selected) echo 'class="selected"'; ?>>
            		                                <a href="javascript:void(0)"
            		                                   onclick="<?php echo $selected ? "removeFilterRule('country', " . $filterCriteria->countryId . ")" : "addFilterRule('country', " . $filterCriteria->countryId . ")"; ?>"><?php echo $filterCriteria->countryName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            		                            </div>
            		                        </li>
            		                        <?php $counterCountries++;
            		                    } else { ?>
            		                        <a id="showMoreCountries" class="filterExpand" href="javascript:void(0)"
            		                           onclick="showMoreParams('extra_countries_params','showMoreCountries')"><?php echo JText::_("LNG_MORE") . " (+)"  ?></a>
            		                        <?php break;
            		                    } ?>
            		                <?php } ?>
            		                <div style="display: none" id="extra_countries_params">
            		                    <?php
            		                    foreach ($this->searchFilter["countries"] as $filterCriteria) {
            		                        if(empty($filterCriteria->countryName)) {
            		                            continue;
            		                        }else if ($counterCountries > 0) {
            		                        	$counterCountries--;
            		                           	continue;
            		                        } else
            		                            $selected = isset($this->selectedParams["country"]) && in_array($filterCriteria->countryId, $this->selectedParams["country"]); ?>
            		                        <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
            		                            <div <?php if ($selected) echo 'class="selected"'; ?>>
            		                                <a href="javascript:void(0)"
            		                                   onclick="<?php echo $selected ? "removeFilterRule('country', " . $filterCriteria->countryId . ")" : "addFilterRule('country', " . $filterCriteria->countryId . ")"; ?>"><?php echo $filterCriteria->countryName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            		                            </div>
            		                        </li>
            		                        <?php
            		                    } ?>
            		                    <a id="showLessCountries" class="filterExpand" href="javascript:void(0)"
            		                       onclick="showLessParams('extra_countries_params','showMoreCountries')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
            		                </div>
            		            </ul>
            		            <div class="clear"></div>
            	            </div>
            	        <?php } ?>
            			
            			<?php if(!empty($this->searchFilter["regions"])) { ?>
            				<div class="filter-criteria">
            		            <div class="filter-header"><?php echo JText::_("LNG_REGIONS") ?></div>
            		            <ul>
            		                <?php $counterRegions = 0;
            		                foreach ($this->searchFilter["regions"] as $filterCriteria) { ?>
            		                    <?php if (empty($filterCriteria->regionName)){continue;} ?>
            		                    <?php if ($counterRegions < $this->appSettings->search_filter_items) { ?>
            		                        <?php $selected = isset($this->selectedParams["region"]) && in_array($filterCriteria->regionName, $this->selectedParams["region"]); ?>
            		                        <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
            		                            <div <?php if ($selected) echo 'class="selected"'; ?>>
            		                                <a href="javascript:void(0)"
            		                                   onclick="<?php echo $selected ? "removeFilterRule('region', '" . $this->escape($filterCriteria->regionName) . "')" : "addFilterRule('region', '" . $this->escape($filterCriteria->regionName) . "')"; ?>"><?php echo $this->escape($filterCriteria->regionName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            		                            </div>
            		                        </li>
            		                        <?php $counterRegions++;
            		                    } else { ?>
            		                        <a id="showMoreRegions" class="filterExpand" href="javascript:void(0)"
            		                           onclick="showMoreParams('extra_regions_params','showMoreRegions')"><?php echo JText::_("LNG_MORE") . " (+)"  ?></a>
            		                        <?php break;
            		                    } ?>
            		                <?php } ?>
            		                <div style="display: none" id="extra_regions_params">
            		                    <?php
            		                    foreach ($this->searchFilter["regions"] as $filterCriteria) {
            		                        if (empty($filterCriteria->regionName)) {
            		                            continue;
            		                        }else if ($counterRegions > 0) {
            		                        	$counterRegions--;
            		                            continue;
            		                        } else {
            		                            $selected = isset($this->selectedParams["regions"]) && in_array($filterCriteria->regionName, $this->selectedParams["city"]); ?>
            		                            <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
            		                                <div <?php if ($selected) echo 'class="selected"'; ?>>
            		                                    <a href="javascript:void(0)"
            		                                       onclick="<?php echo $selected ? "removeFilterRule('region', '" . $this->escape($filterCriteria->regionName) . "')" : "addFilterRule('region', '" . $this->escape($filterCriteria->regionName) . "')"; ?>"><?php echo $this->escape($filterCriteria->regionName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            		                                </div>
            		                            </li>
            		                            <?php
            		                        }
            		                    } ?>
            		                    <a id="showLessRegions" class="filterExpand" href="javascript:void(0)"
            		                       onclick="showLessParams('extra_regions_params','showMoreRegions')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
            		                </div>
            		            </ul>
            		            <div class="clear"></div>
            		        </div>
            	        <?php } ?>
            	
            			<?php if(!empty($this->searchFilter["cities"])) { ?>
            				<div class="filter-criteria">
            		            <div class="filter-header"><?php echo JText::_("LNG_CITIES") ?></div>
            		            <ul>
            		                <?php $counterCities = 0;
            		                foreach ($this->searchFilter["cities"] as $filterCriteria) { ?>
            		                    <?php if (empty($filterCriteria->cityName)){$counterCities++; continue;} ?>
            		                    <?php $selected = isset($this->selectedParams["city"]) && in_array($filterCriteria->cityName, $this->selectedParams["city"]); ?>
            		                    <?php if ($counterCities < $this->appSettings->search_filter_items) { ?>
            		                        <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
            		                            <div <?php if ($selected) echo 'class="selected"'; ?> class="selectedlink">
            		                                <a href="javascript:void(0)"
            		                                   onclick="<?php echo $selected ? "removeFilterRule('city', '" . $this->escape($filterCriteria->cityName) . "')" : "addFilterRule('city', '" . $filterCriteria->cityName . "')"; ?>"><?php echo $this->escape($filterCriteria->cityName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            		                            </div>
            		                        </li>
            		                        <?php $counterCities++;
            		                    } else { ?>
            		                        <a id="showMoreCities" class="filterExpand" href="javascript:void(0)"
            		                           onclick="showMoreParams('extra_cities_params','showMoreCities')"><?php echo JText::_("LNG_MORE") . " (+)"  ?></a>
            		                        <?php break;
            		                    } ?>
            		                <?php } ?>
            		                <div style="display: none" id="extra_cities_params">
            		                    <?php
            		                    foreach ($this->searchFilter["cities"] as $filterCriteria) {
            		                        if (empty($filterCriteria->cityName)) {
            		                            continue;
            		                        }else if ($counterCities > 0) {
            		                        	$counterCities--;
            		                            continue;
            		                    	} else {
            		                            $selected = isset($this->selectedParams["city"]) && in_array($filterCriteria->cityName, $this->selectedParams["city"]); ?>
            		                            <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
            		                                <div <?php if ($selected) echo 'class="selected"'; ?>>
            		                                    <a href="javascript:void(0)"
            		                                       onclick="<?php echo $selected ? "removeFilterRule('city', '" . $this->escape($filterCriteria->cityName) . "')" : "addFilterRule('city', '" . $this->escape($filterCriteria->cityName) . "')"; ?>"><?php echo $this->escape($filterCriteria->cityName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            		                                </div>
            		                            </li>
            		                            <?php
            		                        }
            		                    } ?>
            		                    <a id="showLessCities" class="filterExpand" href="javascript:void(0)"
            		                       onclick="showLessParams('extra_cities_params','showMoreCities','showLessCities')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
            		                </div>
            		            </ul>
            		            <div class="clear"></div>
            		         </div>
            	        <?php } ?>
            	
            			<?php if(!empty($this->searchFilter["areas"])) { ?>
            				<div class="filter-criteria">
            					<div class="filter-header"><?php echo JText::_("LNG_AREA") ?></div>
            					<ul>
            						<?php $counterAreas = 0;
            						foreach ($this->searchFilter["areas"] as $filterCriteria) { ?>
            							<?php if (empty($filterCriteria->areaName)){$counterAreas++;continue;} ?>
            							<?php $selected = isset($this->selectedParams["area"]) && in_array($filterCriteria->areaName, $this->selectedParams["area"]); ?>
            							<?php if ($counterAreas < $this->appSettings->search_filter_items) { ?>
            								<li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
            									<div <?php if ($selected) echo 'class="selected"'; ?>>
            										<a href="javascript:void(0)"
            										   onclick="<?php echo $selected ? "removeFilterRule('area', '" . $filterCriteria->areaName . "')" : "addFilterRule('area', '" . $this->escape($filterCriteria->areaName) . "')"; ?>"><?php echo $this->escape($filterCriteria->areaName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            									</div>
            								</li>
            								<?php $counterAreas++;
            							} else { ?>
            								<a id="showMoreAreas" class="filterExpand" href="javascript:void(0)"
            								   onclick="showMoreParams('extra_areas_params','showMoreAreas')"><?php echo JText::_("LNG_MORE") . " (+)"  ?></a>
            								<?php break;
            							} ?>
            						<?php } ?>
            						<div style="display: none" id="extra_areas_params">
            							<?php
            							foreach ($this->searchFilter["areas"] as $filterCriteria) {
            								if (empty($filterCriteria->areaName)) {
            									continue;
            								}else if ($counterAreas > 0) {
            									$counterAreas--;
            									continue;
            								} else {
            									$selected = isset($this->selectedParams["area"]) && in_array($filterCriteria->areaName, $this->selectedParams["area"]); ?>
            									<li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
            										<div <?php if ($selected) echo 'class="selected"'; ?>>
            											<a href="javascript:void(0)"
            											   onclick="<?php echo $selected ? "removeFilterRule('area', '" . $filterCriteria->areaName . "')" : "addFilterRule('area', '" . $this->escape($filterCriteria->areaName) . "')"; ?>"><?php echo $this->escape($filterCriteria->areaName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
            										</div>
            									</li>
            									<?php
            								}
            							} ?>
            							<a id="showLessAreas" class="filterExpand" href="javascript:void(0)"
            							   onclick="showLessParams('extra_areas_params','showMoreAreas','showLessAreas')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
            						</div>
            					</ul>
            					<div class="clear"></div>
            				</div>
            			<?php } ?>

                        <?php if(!empty($this->searchFilter["provinces"])) { ?>
                            <div class="filter-criteria">
                                <div class="filter-header"><?php echo JText::_("LNG_PROVINCE") ?></div>
                                <ul>
                                    <?php $counterProvinces = 0;
                                    foreach ($this->searchFilter["provinces"] as $filterCriteria) { ?>
                                        <?php if (empty($filterCriteria->provinceName)){$counterProvinces++;continue;} ?>
                                        <?php $selected = isset($this->selectedParams["province"]) && in_array($filterCriteria->provinceName, $this->selectedParams["province"]); ?>
                                        <?php if ($counterProvinces < $this->appSettings->search_filter_items) { ?>
                                            <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
                                                <div <?php if ($selected) echo 'class="selected"'; ?>>
                                                    <a href="javascript:void(0)"
                                                       onclick="<?php echo $selected ? "removeFilterRule('province', '" . $filterCriteria->provinceName . "')" : "addFilterRule('province', '" . $this->escape($filterCriteria->provinceName) . "')"; ?>"><?php echo $this->escape($filterCriteria->provinceName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
                                                </div>
                                            </li>
                                            <?php $counterProvinces++;
                                        } else { ?>
                                            <a id="showMoreProvinces" class="filterExpand" href="javascript:void(0)"
                                               onclick="showMoreParams('extra_provinces_params','showMoreProvinces')"><?php echo JText::_("LNG_MORE") . " (+)"  ?></a>
                                            <?php break;
                                        } ?>
                                    <?php } ?>
                                    <div style="display: none" id="extra_provinces_params">
                                        <?php
                                        foreach ($this->searchFilter["provinces"] as $filterCriteria) {
                                            if (empty($filterCriteria->provinceName)) {
                                                continue;
                                            }else if ($counterProvinces > 0) {
                                                $counterProvinces--;
                                                continue;
                                            } else {
                                                $selected = isset($this->selectedParams["province"]) && in_array($filterCriteria->provinceName, $this->selectedParams["province"]); ?>
                                                <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
                                                    <div <?php if ($selected) echo 'class="selected"'; ?>>
                                                        <a href="javascript:void(0)"
                                                           onclick="<?php echo $selected ? "removeFilterRule('province', '" . $filterCriteria->provinceName . "')" : "addFilterRule('province', '" . $this->escape($filterCriteria->provinceName) . "')"; ?>"><?php echo $this->escape($filterCriteria->provinceName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
                                                    </div>
                                                </li>
                                                <?php
                                            }
                                        } ?>
                                        <a id="showLessProvinces" class="filterExpand" href="javascript:void(0)"
                                           onclick="showLessParams('extra_provinces_params','showMoreProvinces','showLessProvinces')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
                                    </div>
                                </ul>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
            		</div>
            	</div>
            </div>
			<?php } ?>
					
    	 <?php if (!empty($bottomModules)) { ?>
                <div class="search-modules">
                    <?php
                        foreach ($bottomModules as $module) {
                            echo JModuleHelper::renderModule($module);
                        }
                    ?>
				</div>
        <?php } ?>
	</div>
<?php }?>
<div id="search-results" class="search-results <?php echo $fullWidth ?'search-results-full span12':'search-results-normal span9' ?> ">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" method="<?php echo $this->appSettings->submit_method ?>" name="adminForm" id="adminForm">
		<div class="search-header">
			<div id="search-path">
				<?php if(isset($this->category)) { ?>
					<ul class="category-breadcrumbs">
						<li>
							<a class="search-filter-elem" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=search&resetSearch=1') ?>"><?php echo JText::_('LNG_ALL_CATEGORIES') ?></a>
						</li>
						<?php 
						if(isset($this->searchFilter["path"])) {
							foreach($this->searchFilter["path"] as $path) {
								if($path[0]==1)
									continue;
							?>
								<span class="divider">/</span>
								<li>
									<a class="search-filter-elem" href="<?php echo JBusinessUtil::getCategoryLink($path[0], $path[2]) ?>"><?php echo $path[1]?></a>
								</li>
							<?php } ?>
						<?php } ?>
						<span class="divider">/</span>
						<li>
							<?php if(!empty($this->category)) echo $this->category->name ?>
						</li>
					</ul>
				<?php } ?>
				<ul class="selected-criteria">
					<?php if(!empty($this->selectedParams["type"]) && !empty($this->searchFilter["types"])) {?>
						<li>
							<a class="filter-type-elem" onclick="removeFilterRule('type', <?php echo $this->selectedParams["type"][0] ?>)"><?php echo $this->searchFilter["types"][$this->selectedParams["type"][0]]->typeName; ?> x</a>
						</li>
					<?php $showClear++; } ?>
					<?php if(!empty($this->selectedParams["country"]) && !empty( $this->searchFilter["countries"])) {?>
						<li>
							<a  class="filter-type-elem" onclick="removeFilterRule('country', <?php echo $this->selectedParams["country"][0] ?>)"><?php echo $this->searchFilter["countries"][$this->selectedParams["country"][0]]->countryName; ?> x</a>
						</li>
					<?php $showClear++; } ?>
					<?php if(!empty($this->selectedParams["region"]) && !empty( $this->searchFilter["regions"])) {?>
						<li>
							<a class="filter-type-elem" onclick="removeFilterRule('region', '<?php echo $this->selectedParams["region"][0] ?>')"><?php echo $this->searchFilter["regions"][$this->selectedParams["region"][0]]->regionName; ?> x</a>
						</li>
					<?php $showClear++; } ?>
					<?php if(!empty($this->selectedParams["city"]) && !empty($this->searchFilter["cities"]) && isset( $this->searchFilter["cities"][$this->selectedParams["city"][0]])) {?>
						<li>
							<a class="filter-type-elem" onclick="removeFilterRule('city', '<?php echo $this->selectedParams["city"][0] ?>')"><?php echo $this->searchFilter["cities"][$this->selectedParams["city"][0]]->cityName; ?> x</a>
						</li>
					<?php $showClear++; } ?>
					<?php if(!empty($this->selectedParams["area"]) && !empty( $this->searchFilter["areas"])) {?>
						<li>
							<a class="filter-type-elem" class="remove" onclick="removeFilterRule('area', '<?php echo $this->selectedParams["area"][0] ?>')"> <?php echo $this->searchFilter["areas"][$this->selectedParams["area"][0]]->areaName; ?> x</a>
						</li>
					<?php $showClear++; } ?>
                    <?php if(!empty($this->selectedParams["province"]) && !empty($this->searchFilter["provinces"]) && isset( $this->searchFilter["provinces"][$this->selectedParams["province"][0]])) {?>
                        <li>
                            <a class="filter-type-elem" onclick="removeFilterRule('province', '<?php echo $this->selectedParams["province"][0] ?>')"><?php echo $this->searchFilter["provinces"][$this->selectedParams["province"][0]]->provinceName; ?> x</a>
                        </li>
                    <?php $showClear++; } ?>
					<?php if($showClear > 1) { ?>
						<span class="filter-type-elem reset"><a href="javascript:resetFilters(true)" style="text-decoration: none;"><?php echo JText::_('LNG_CLEAR_ALL'); ?></a></span>
					<?php } ?>
				</ul>
				<div class="clear"></div>
			</div>
			
			<div class="clear"></div>
			
			<?php if(isset($this->category) && $this->appSettings->show_cat_description && !empty($this->category->description)) { ?>
				<div class="category-container">
					<?php if(!empty($this->category->imageLocation)) { ?>
						<div class="categoy-image"><img alt="<?php echo $this->category->name?>" src="<?php echo JURI::root().PICTURES_PATH.$this->category->imageLocation ?>"></div>
					<?php } ?>
					<h3><?php echo $this->category->name?></h3>
					<div>
						<div id="category-description" class="dir-cat-description">
							<div class="intro-text">
								<?php echo JBusinessUtil::truncate(JHTML::_("content.prepare", $this->category->description),300) ?>
								<?php if(strlen(strip_tags($this->category->description))>strlen(strip_tags(JBusinessUtil::truncate(JHTML::_("content.prepare", $this->category->description),300)))){?>
									<a class="cat-read-more" href="javascript:void(0)" onclick="jQuery('#category-description').toggleClass('open')">
										<?php echo JText::_("LNG_MORE") ?> </a>
								<?php } ?>
							</div>
							<div class="full-text">
								<?php echo JHTML::_("content.prepare", $this->category->description) ?>
								<a class="cat-read-more" href="javascript:void(0)" onclick="jQuery('#category-description').toggleClass('open')">
										<?php echo JText::_("LNG_LESS") ?> </a>
							</div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			<?php } else if(!empty($this->country) && $this->appSettings->show_cat_description && false) { ?>
				<div class="category-container">
					<?php if(!empty($this->country->logo)) { ?>
						<div class="categoy-image"><img alt="<?php echo $this->country->country_name?>" src="<?php echo JURI::root().PICTURES_PATH.$this->country->logo ?>"></div>
					<?php } ?>
					<h3><?php echo $this->country->country_name?></h3>
					<div>
						<?php echo JHTML::_("content.prepare", $this->country->description);?>
					</div>
					<div class="clear"></div>
				</div>
			<?php } ?>
	
			<?php 
				jimport('joomla.application.module.helper');
				// this is where you want to load your module position
			    $modules = JModuleHelper::getModules("listing-search");
				?>
				<?php if(isset($modules) && count($modules)>0) { ?>
					<div class="search-modules">
						<?php 
						$fullWidth = false;
						foreach($modules as $module) {
							echo JModuleHelper::renderModule($module);
						} ?>
					</div>
			<?php } ?>
	
			<div id="search-details">
				<div class="search-toggles">
					<span class="sortby"><?php echo JText::_('LNG_SORT_BY');?>: </span>
					<select name="orderBy" class="orderBy inputbox input-medium" onchange="changeOrder(this.value)">
						<?php echo JHtml::_('select.options', $this->sortByOptions, 'value', 'text',  $this->orderBy);?>
					</select>
	
					<?php if($this->appSettings->search_result_view != 5) { ?>
						<p class="view-mode">
							<label><?php echo JText::_('LNG_VIEW')?></label>
							<a id="grid-view-link" class="grid" title="Grid" href="javascript:showGrid()"><?php echo JText::_("LNG_GRID") ?></a>
							<a id="list-view-link" class="list active" title="List" href="javascript:showList()"><?php echo JText::_("LNG_LIST") ?></a>
						</p>
						
						<?php if($this->appSettings->show_search_map) { ?>
							<p class="view-mode">
								<a id="map-link" class="map" title="Grid" href="javascript:showMap(true)"><?php echo JText::_("LNG_SHOW_MAP") ?></a>
							</p>
						<?php } ?>
					<?php } ?>
					<div class="clear"></div>
				</div>
				
				<div class="search-keyword">
					<div class="result-counter"><?php echo $this->pagination->getResultsCounter()?></div> 
					<?php if( !empty($this->customAtrributesValues) || !empty($this->categoryId) || !empty($this->typeSearch) || !empty($this->searchkeyword) || !empty($this->citySearch) || !empty($this->countrySearch) || !empty($this->regionSearch) || !empty($this->zipCode)) {
						$searchText="";
						if(!empty($this->searchkeyword) || !empty($this->customAtrributesValues)){
							echo "<strong>".JText::_('LNG_FOR')."</strong> ";
		
							$searchText.= !empty($this->searchkeyword)? $this->searchkeyword :"";
							
							if(!empty($this->searchkeyword) && !empty($this->customAtrributesValues)){
								$searchText .=", ";
							}
							
							if( !empty($this->customAtrributesValues) ) {
								foreach($this->customAtrributesValues as $attribute) {
									$searchText.= !empty($attribute)?$attribute->name.", ":"";
								}
							}
							
							$searchText = trim(trim($searchText), ",");
							
							$searchText .=" ";
						}
	
						if(!empty($this->citySearch) || !empty($this->countrySearch) || !empty($this->regionSearch)|| !empty($this->provinceSearch)|| !empty($this->areaSearch) || !empty($this->zipCode)) {
							$searchText.= "<strong>".JText::_('LNG_INTO')."</strong>".' ';
							$searchText.= !empty($this->zipCode)?$this->zipCode.", ":"";
							$searchText.= !empty($this->citySearch)?$this->citySearch.", ":"";
                            $searchText.= !empty($this->regionSearch)?$this->regionSearch.", ":"";
                            $searchText.= !empty($this->areaSearch)?$this->areaSearch.", ":"";
                            $searchText.= !empty($this->provinceSearch)?$this->provinceSearch.", ":"";
							$searchText.= !empty($this->countrySearch)?$this->country->country_name.", ":"";
							$searchText = trim(trim($searchText), ",");
							$searchText.=" ";
						} 
	
						$searchText.= !empty($this->category->name)?"<strong>".JText::_('LNG_IN')."</strong>".' '.$this->category->name." ":"";
						$searchText.= !empty($this->type->name)?"<strong>".JText::_('LNG_IN')."</strong>".' '.$this->type->name.", ":"";
						$searchText = trim(trim($searchText), ",");
	
						echo $searchText;
						echo '';
					} ?>
				</div>
					<?php if ($this->appSettings->enable_search_letters == 1) {
						//require_once JPATH_COMPONENT_SITE . '/include/letterfilter.php';
					}?>
				<div class="clear"></div>
			</div>
		</div>

		<?php if($this->appSettings->search_result_view != 5 && $appSettings->show_search_map) { ?>
			<div id="companies-map-container" style="display:none">
				<?php require JPATH_COMPONENT_SITE.'/include/search-map.php' ?>
			</div>
		<?php } ?>

		<?php 
		require_once JPATH_COMPONENT_SITE.'/include/listings_grid_view.php';
		
		if($this->appSettings->search_result_view == 1) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_1.php';
		} else if($this->appSettings->search_result_view == 2) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_2.php';
		} else if($this->appSettings->search_result_view == 3) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_3.php';
		} else if($this->appSettings->search_result_view == 4) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_4.php';
		} else if($this->appSettings->search_result_view == 5) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_5.php';
		} else if($this->appSettings->search_result_view == 6) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_6.php';
		} else if($this->appSettings->search_result_view == 7) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_7.php';
		} else {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_1.php';
		} ?>

		<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
			<?php echo $this->pagination->getListFooter(); ?>
			<div class="clear"></div>
		</div>
		<input type='hidden' name='task' value='searchCompaniesByName'/>
		<input type='hidden' name='option' value='com_jbusinessdirectory'/>
		<input type='hidden' name='controller' value='search' />
		<input type='hidden' name='categories' id="categories-filter" value='<?php echo !empty($this->categories)?$this->categories:"" ?>' />
		<input type='hidden' name='view' value='search' />
		<input type='hidden' name='categoryId' id='categoryId' value='<?php echo !empty($this->categoryId)?$this->categoryId:"0" ?>' />
		<input type='hidden' name='searchkeyword' id="searchkeyword" value='<?php echo !empty($this->searchkeyword)?$this->searchkeyword:'' ?>' />
		<input type='hidden' name='letter' id="letter" value='<?php echo !empty($this->letter)?$this->letter:'' ?>' />
		<input type='hidden' name="categorySearch" id="categorySearch" value='<?php echo !empty($this->categorySearch)?$this->categorySearch: '' ?>' />
		<input type='hidden' name='citySearch' id='city-search' value='<?php echo !empty($this->citySearch)?$this->citySearch: '' ?>' />
        <input type='hidden' name='regionSearch' id='region-search' value='<?php echo !empty($this->regionSearch)?$this->regionSearch: '' ?>' />
        <input type='hidden' name='provinceSearch' id='province-search' value='<?php echo !empty($this->provinceSearch)?$this->provinceSearch: '' ?>' />
		<input type='hidden' name='countrySearch' id='country-search' value='<?php echo !empty($this->countrySearch)?$this->countrySearch: '' ?>' />
		<input type='hidden' name='typeSearch' id='type-search' value='<?php echo !empty($this->typeSearch)?$this->typeSearch: '' ?>' />
		<input type='hidden' name='zipcode' id="zipcode" value='<?php echo !empty($this->zipCode)?$this->zipCode: '' ?>' />
		<input type='hidden' name='radius' id="radius" value='<?php echo !empty($this->radius)?$this->radius: '' ?>' />
		<input type='hidden' name='featured' id="featured" value='<?php echo !empty($this->featured)?$this->featured: '' ?>' />
		<input type='hidden' name='filter-by-fav' id="filter-by-fav" value='<?php echo !empty($this->filterByFav)?$this->filterByFav: '' ?>' />
		<input type='hidden' name='filter_active' id="filter_active" value="<?php echo !empty($this->filterActive)?$this->filterActive: '' ?>" />
		<input type='hidden' name='selectedParams' id='selectedParams' value='<?php echo !empty($this->selectedParams["selectedParams"])?$this->selectedParams["selectedParams"]:"" ?>' />
		<input type='hidden' name='form_submited' id="form_submited" value="1" />
		
		<input type='hidden' name='preserve' id='preserve' value='<?php echo !empty($this->preserve)?$this->preserve: '' ?>' />
		
		<?php if(!empty($this->customAtrributes)){ ?>
			<?php foreach($this->customAtrributes as $key=>$val){?>
				<input type='hidden' name='attribute_<?php echo $key?>' value='<?php echo $val ?>' />
			<?php } ?>
		<?php } ?>
		
	</form>
	<div class="clear"></div>
</div>

</div>

<div id="login-notice" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		<div class="dialogContent">
			<h3 class="title"><?php echo JText::_('LNG_INFO') ?></h3>
	  		<div class="dialogContentBody" id="dialogContentBody">				
				<p>
					<?php echo JText::_('LNG_YOU_HAVE_TO_BE_LOGGED_IN') ?>
				</p>
				<p>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($url)); ?>"><?php echo JText::_('LNG_CLICK_LOGIN') ?></a>
				</p>
			</div>
		</div>
	</div>
</div>

<?php 
if($this->appSettings->search_result_view == 3) {
	require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_3_util.php';
}

$showNotice = ($this->appSettings->enable_reviews_users && $user->id ==0);
?>

<script>
jQuery(document).ready(function() {
	<?php if($this->appSettings->enable_ratings){?>
        renderSearchAverageRating();
	<?php } ?>

	<?php 
		$load = JRequest::getVar("geo-latitude");
		if(empty($load)){
			$load = JRequest::getVar("latitude");
		}
		$geolocation = JRequest::getVar("geolocation");
		if($geolocation && empty($load) && empty($this->form_submited)){ ?>
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(addCoordinatesToUrl);
			}
	<?php } ?>

	jQuery('.button-toggle').click(function() {  
		if(!jQuery(this).hasClass("active")) {
			jQuery(this).addClass('active');
		}
		jQuery('.button-toggle').not(this).removeClass('active'); // remove buttonactive from the others
	});

	<?php if ($this->appSettings->map_auto_show == 1) { ?>
		showMap(true);
	<?php } ?>

	<?php if ($this->appSettings->search_view_mode == 1 && $this->appSettings->search_result_view != 5) { ?>
		showGrid();
	<?php } else { ?>
		showList();
	<?php }?>

	//disable all empty fields to have a nice url
    <?php if($this->appSettings->submit_method=="get"){?>
	    jQuery('#adminForm').submit(function() {
	    	jQuery(':input', this).each(function() {
	            this.disabled = !(jQuery(this).val());
	        });
	
	    	jQuery('#adminForm select').each(function() {
		    	if(!(jQuery(this).val()) || jQuery(this).val()==0){
	            	jQuery(this).attr('disabled', 'disabled');
		    	}
	        });
	    });

     <?php }?>

    collapseSearchFilter();
 	if(window.innerWidth<400){
 		jQuery(".search-filter").css("display","none");
 	}
 	applyReadMore();
    setCategoryStatus(<?php echo isset($this->category)?'true':'false' ?>, <?php echo isset($this->categoryId)?$this->categoryId:0; ?>);
});
</script>