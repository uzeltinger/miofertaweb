<?php // no direct access
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.module.helper');

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
if(isset($activeMenu)){
	$menuitem   = $app->getMenu()->getItem($currentMenuId); // or get item by ID `enter code here`
	$params = $menuitem->params; // get the params `enter code here`
}else{
	$params = null;
}

//set page title
if(!empty($params) && $params->get('page_title') != ''){
	$title = $params->get('page_title', '');
}

if(empty($title)) {
	$title = JText::_("LNG_OFFERS");

	if(!empty($this->category->name) || !empty($this->citySearch) || !empty($this->regionSearch)){
		$title .= " in ";
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
	
	if(!empty($items)){
		$title .= implode("|",$items);
	}
}

$document->setTitle($title);

//set page meta description and keywords
$description = $this->appSettings->meta_description;
$document->setDescription($description);
$document->setMetaData('keywords', $this->appSettings->meta_keywords);

if(!empty($params) && $params->get('menu-meta_description') != ''){
	$document->setMetaData( 'description', $params->get('menu-meta_description') );
	$document->setMetaData( 'keywords', $params->get('menu-meta_keywords') );
}

if (isset($this->category)){
	if (!empty($this->category->meta_title))
		$document->setTitle($this->escape($this->category->meta_title));
	if (!empty($this->category->meta_description))
		$document->setMetaData( 'description', $this->category->meta_description );
	if (!empty($this->category->meta_keywords))
		$document->setMetaData( 'keywords', $this->category->meta_keywords );
}

$fullWidth = true;
$enableSearchFilter = $this->appSettings->enable_search_filter_offers;
$mposition = "dir-search-offers-top";
$topModules = JModuleHelper::getModules($mposition);
$mposition = "dir-search-offers";
if (!empty($this->category)) {
    $mposition = "dir-search-offers-" . $this->category->alias;
}
$bottomModules = JModuleHelper::getModules($mposition);

if($enableSearchFilter || !empty($topModules) || !empty($bottomModules)){
    $fullWidth = false;
}

//add the possibility to chage the view and layout from http params
$grid_layout = JRequest::getVar('grid_layout');
if(!empty($grid_layout)) {
    $this->appSettings->offer_search_results_grid_view = $grid_layout;
}

$list_layout = JRequest::getVar('list_layout');
if(!empty($list_layout)) {
	$this->appSettings->offer_search_results_list_view = $list_layout;
}

$view_mode = JRequest::getVar('view_mode');
if(!empty($view_mode)) {
	$this->appSettings->offers_view_mode = $view_mode;
}

$setCategory = isset($this->category)?1:0;
$categId = isset($this->categoryId)?$this->categoryId:0;
$showClear = 0;
?>

<?php if (!empty($this->params) && $this->params->get('show_page_heading', 1)) { ?>
    <div class="page-header">
        <h1 class="title"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
<?php } ?>
 
<div id="offers" class="row-fluid">
	<div class="row-fluid" id="filterCategoryItems">
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
        				<div class="search-category-box">
        					<?php if(!empty($this->location["latitude"])){ ?>
        						<h4><?php echo JText::_("LNG_DISTANCE")?></h4>
        						<ul>
        							<li>
        								<?php if($this->radius != 50){ ?>
        									<a href="javascript:changeRadius(50)" >50 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></a>
        								<?php }else{ ?>
        									<strong>50 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></strong>
        								<?php } ?>
        							</li>
        							<li>
        								<?php if($this->radius != 25){ ?>
        									<a href="javascript:changeRadius(25)">25 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></a>
        								<?php }else{ ?>
        									<strong>25 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></strong>
        								<?php } ?>
        							</li>
        							<li>
        								<?php if($this->radius != 10){ ?>
        									<a href="javascript:changeRadius(10)">10 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></a>
        								<?php }else{ ?>
        									<strong>10 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></strong>
        								<?php } ?>
        							</li>
        							<li>
        								<?php if($this->radius != 0){ ?>
        									<a href="javascript:changeRadius(0)"><?php echo JText::_("LNG_ALL")?></a>
        								<?php }else{ ?>
        									<strong><?php echo JText::_("LNG_ALL")?></strong>
        								<?php } ?>
        							</li>
        						</ul>
        					<?php } ?>
        					
                            <div id="filterCategoryItems"  class="">

                                <?php if(!empty($this->searchFilter["categories"])) { ?>
                                <div class="filter-criteria">
                                    <div class="filter-header"><?php echo JText::_("LNG_CATEGORIES") ?></div>
                                    <?php if ($this->appSettings->search_type == 0) {
                                        $counterCategories = 0; ?>
                                        <ul>
                                            <?php
                                            foreach ($this->searchFilter["categories"] as $filterCriteria) {
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
                                                        <?php  $counterCategories++; ?>
                                                    <?php }?>
                                                <?php } else { ?>
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
                                                    <?php }
                                                    $counterCategories++;
                                                } else { ?>
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
                                                               onclick="<?php echo $selected ? "removeFilterRule('area', '" . $filterCriteria->areaName . "', ".$setCategory.", ".$categId."  )" : "addFilterRule('area', '" . $filterCriteria->areaName . "', ".$setCategory.", ".$categId."  )"; ?>"><?php echo $filterCriteria->areaName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
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
                                                                   onclick="<?php echo $selected ? "removeFilterRule('area', '" . $filterCriteria->areaName . "', ".$setCategory.", ".$categId."  )" : "addFilterRule('area', '" . $filterCriteria->areaName . "', ".$setCategory.", ".$categId."  )"; ?>"><?php echo $filterCriteria->areaName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
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
                                                               onclick="<?php echo $selected ? "removeFilterRule('city', '" . $filterCriteria->cityName . "', ".$setCategory.", ".$categId."  )" : "addFilterRule('city', '" . $filterCriteria->cityName . "', ".$setCategory.", ".$categId."  )"; ?>"><?php echo $filterCriteria->cityName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
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
                                                                   onclick="<?php echo $selected ? "removeFilterRule('city', '" . $filterCriteria->cityName . "', ".$setCategory.", ".$categId."  )" : "addFilterRule('city', '" . $filterCriteria->cityName . "', ".$setCategory.", ".$categId."  )"; ?>"><?php echo $filterCriteria->cityName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
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
                                                               onclick="<?php echo $selected ? "removeFilterRule('region', '" . $filterCriteria->regionName . "', ".$setCategory.", ".$categId."  )" : "addFilterRule('region', '" . $filterCriteria->regionName . "', ".$setCategory.", ".$categId." )"; ?>"><?php echo $filterCriteria->regionName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
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
                                                                   onclick="<?php echo $selected ? "removeFilterRule('region', '" . $filterCriteria->regionName . "', ".$setCategory.", ".$categId."  )" : "addFilterRule('region', '" . $filterCriteria->regionName . "', ".$setCategory.", ".$categId."  )"; ?>"><?php echo $filterCriteria->regionName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
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
                            </div>
        				    <div class="clear"></div>
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
        							<a class="search-filter-elem" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=offers&resetSearch=1') ?>"><?php echo JText::_('LNG_ALL_CATEGORIES') ?></a>
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
								<div class="categoy-image"><img alt="<?php echo $this->escape($this->category->name)?>" src="<?php echo JURI::root().PICTURES_PATH.$this->category->imageLocation ?>"></div>
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
					<?php } ?>
					
					<div id="search-details" >
					<div class="search-toggles">
						<span class="sortby"><?php echo JText::_('LNG_SORT_BY');?>: </span>
						<select name="orderBy" class="orderBy inputbox input-medium" onchange="changeOrder(this.value)">
							<?php echo JHtml::_('select.options', $this->sortByOptions, 'value', 'text',  $this->orderBy);?>
						</select>
						<p class="view-mode">
							<label><?php echo JText::_('LNG_VIEW')?></label>
							<a id="grid-view-link" class="grid" title="Grid" href="javascript:showGrid()"><?php echo JText::_("LNG_GRID")?></a>
							<a id="list-view-link" class="list active" title="List" href="javascript:showList()"><?php echo JText::_("LNG_LIST")?></a>
						</p>
						
						<?php if($this->appSettings->show_search_map && !empty($this->offers)){?>
							<p class="view-mode">
								<a id="map-link" class="map" title="Grid" href="javascript:showMap(true)"><?php echo JText::_("LNG_SHOW_MAP")?></a>
							</p>
						<?php } ?>
						
						<?php if($this->appSettings->enable_rss == 1) { ?>
							<p class="view-mode">
									<a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=directoryrss.getOffersRss') ?>" target="_blank">
										<img alt="<?php echo JTEXT::_("LNG_RSS") ?>" src="<?php echo JURI::base() ."components/".JBusinessUtil::getComponentName().'/assets/images/rss-icon.png' ?>" />
									</a>
							</p>
						<?php } ?>
						<div class="clear"></div>
					</div>
					
					<div class="search-keyword" >
						<div class="result-counter"><?php echo $this->pagination->getResultsCounter() ?></div>
						<?php if( !empty($this->customAtrributesValues) || !empty($this->categoryId) || !empty($this->searchkeyword) || !empty($this->citySearch) || !empty($this->countrySearch) || !empty($this->regionSearch) || !empty($this->zipCode)) {
							$searchText="";
							if(!empty($this->searchkeyword) || !empty($this->customAtrributesValues)){
								echo "<strong>".JText::_('LNG_FOR')."</strong> ";


								$searchText.= !empty($this->searchkeyword)? $this->searchkeyword:"";

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

							if(!empty($this->citySearch) || !empty($this->countrySearch) || !empty($this->regionSearch) || !empty($this->zipCode)) {
								$searchText.= "<strong>".JText::_('LNG_INTO')."</strong>".' ';
								$searchText.= !empty($this->zipCode)?$this->zipCode.", ":"";
								$searchText.= !empty($this->citySearch)?$this->citySearch.", ":"";
								$searchText.= !empty($this->regionSearch)?$this->regionSearch.", ":"";
								$searchText.= !empty($this->countrySearch)?$this->country->country_name.", ":"";
								$searchText = trim(trim($searchText), ",");
								$searchText.=" ";
							}

							$searchText.= !empty($this->category->name)?"<strong>".JText::_('LNG_IN')."</strong>".' '.$this->category->name." ":"";
							$searchText = trim(trim($searchText), ",");

							echo $searchText;
							echo '';
						} ?>
					</div>
					<div class="clear"></div>
				</div>
				
				</div>
				
				<div id="companies-map-container" style="display:none">
					<?php require 'map.php' ?>
				</div>
				<?php 
					 if($this->appSettings->offer_search_results_grid_view==1){
					 	require_once 'offers_grid_style_2.php';
					 }else{
					 	require_once 'offers_grid_style_1.php';
					 }
				?>
				<?php
					if($this->appSettings->offer_search_results_list_view==2){
						require_once 'offers_list_style_2.php';
					}else{
						require_once 'offers_list_style_1.php';
					}
				?>
				<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
					<?php echo $this->pagination->getListFooter(); ?>
					<div class="clear"></div>
				</div>
			
				<input type='hidden' name='option' value='com_jbusinessdirectory'/>
				<input type='hidden' name='view' value='offers' />
				<input type='hidden' id="categories-filter" name='categories' value='<?php echo isset($this->categories)?$this->categories:"" ?>' />
				<input type='hidden' id="categoryId" name='categoryId'  value='<?php echo isset($this->categoryId)?$this->categoryId:"0" ?>' />
				<input type='hidden' name='searchkeyword' id="searchkeyword" value='<?php echo isset($this->searchkeyword)?$this->searchkeyword:'' ?>' />
                <input type='hidden' name='countrySearch' id='country-search' value='<?php echo !empty($this->countrySearch)?$this->countrySearch: '' ?>' />
                <input type='hidden' name='provinceSearch' id='province-search' value='<?php echo !empty($this->provinceSearch)?$this->provinceSearch: '' ?>' />
                <input type='hidden' id='city-search' name='citySearch' value='<?php echo isset($this->citySearch)?$this->citySearch: '' ?>' />
				<input type='hidden' id='region-search' name='regionSearch' value='<?php echo isset($this->regionSearch)?$this->regionSearch: '' ?>' />
				<input type='hidden' name='zipcode' id="zipcode" value='<?php echo isset($this->zipCode)?$this->zipCode: '' ?>' />
				<input type='hidden' name='radius' id="radius" value='<?php echo isset($this->radius)?$this->radius: '' ?>' />
				<input type='hidden' name='selectedParams' id='selectedParams' value='<?php echo !empty($this->selectedParams["selectedParams"])?$this->selectedParams["selectedParams"]:"" ?>' />
				<input type='hidden' name='resetSearch' id="resetSearch" value="" />
			</form>	
			<div class="clear"></div>	
		</div>	
	</div>
 </div>
 
<script>
jQuery(document).ready(function(){
	<?php if ($this->appSettings->offers_view_mode == 1) {?>
		showGrid();
	<?php }else{ ?>
		showList();
	<?php }?>

	<?php if ($this->appSettings->map_auto_show == 1) { ?>
		showMap(true);
	<?php } ?>
});
</script>
