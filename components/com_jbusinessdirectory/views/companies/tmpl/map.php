<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

$lang = JFactory::getLanguage()->getTag();
$key="";
if(!empty($appSettings->google_map_key))
	$key="&key=".$appSettings->google_map_key;

JHtml::_('script', "https://maps.googleapis.com/maps/api/js?language=".$lang.$key."&libraries=geometry");

/**
 * Data for the markers consisting of a name, a LatLng and a zIndex for
 * the order in which these markers should display on top of each
 * other.
 */

$marker = 0;

if ($this->company->featured){
    $marker = JURI::root().PICTURES_PATH."/default_featured_marker.png";
}elseif(!empty($this->company->categoryMaker)) {
    $marker = JURI::root().PICTURES_PATH.$this->company->categoryMaker;
}

$db = JFactory::getDBO();

$contentPhone = (isset($this->package->features) && in_array(PHONE,$this->package->features) || !$appSettings->enable_packages)?'<div class="info-phone"><i class="dir-icon-phone"></i> '.htmlspecialchars($this->company->phone, ENT_QUOTES).'</div>':"";
$contentString =
                '<div class="info-box">'.
                    '<div class="title">'.htmlspecialchars($this->company->name).'</div>'.
                '<div class="info-box-content">'.
                '<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">'.
                htmlspecialchars(JBusinessUtil::getAddressText($company), ENT_QUOTES).'</div>'.$contentPhone.
                '</div>'.
                '<div class="info-box-image">'.
                    (!empty($this->company->logoLocation)?'<img src="'. JURI::root().PICTURES_PATH.(htmlspecialchars($this->company->logoLocation, ENT_QUOTES)).'" alt="'.htmlspecialchars($this->company->name).'">':"").
                '</div>'.
                '</div>';

$itemLocations = array();
$tmp = array();
if(!empty($this->company->latitude) && !empty($this->company->longitude) && (isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages)) {
    $tmp['latitude'] = $this->company->latitude;
    $tmp['longitude'] = $this->company->longitude;
    $tmp['marker'] = $marker;
    $tmp['content'] = $contentString;
}

$itemLocations[] = $tmp;

$index = 1;
foreach($this->company->locations as $location) {
    $tmp = array();
    $contentPhoneLocation = (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages) ? '<div class="info-phone"><i class="dir-icon-phone"></i> ' . htmlspecialchars($location->phone, ENT_QUOTES) . '</div>' : "";
    $address = JBusinessUtil::getAddressText($location);

    $contentStringLocation =
        '<div class="info-box">' .
        '<div class="title">' . htmlspecialchars($this->company->name) . '</div>' .
        '<div class="info-box-content">' .
        '<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">' . htmlspecialchars($address, ENT_QUOTES) . '</div>' .
        $contentPhoneLocation .
        '</div>' .
        '<div class="info-box-image">' .
        (!empty($this->company->logoLocation) ? '<img src="' . JURI::root() . PICTURES_PATH . (htmlspecialchars($this->company->logoLocation, ENT_QUOTES)) . '" alt="' .htmlspecialchars($this->company->name). '">' : "") .
        '</div>' .
        '</div>';

    if (!empty($location->latitude) && !empty($location->longitude) && (isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) && in_array(SECONDARY_LOCATIONS, $this->package->features) || !$appSettings->enable_packages)) {
        $tmp['latitude'] = $location->latitude;
        $tmp['longitude'] = $location->longitude;
        $tmp['marker'] = $marker;
        $tmp['content'] = $contentStringLocation;
    }

    $itemLocations[] = $tmp;
    $index++;
}

$params = array();
$params['map_latitude'] = $itemLocations[0]['latitude'];
$params['map_longitude'] = $itemLocations[0]['longitude'];
$params['map_div'] = 'company-map';
$params['panorama'] = 1;
?>

<?php if((isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages )
		&& isset($this->company->latitude) && isset($this->company->longitude)) {
?>
	<a target="_blank" href="<?php echo JBusinessUtil::getDirectionURL($this->location, $this->company) ?>"><?php echo JText::_("LNG_GET_MAP_DIRECTIONS")?></a>

	<div id="map-street-view-panel">
		<input type="button" value="Toggle Street View" onclick="toggleStreetView();" />
	</div>
	<div id="company-map" style="position:relative;">
	</div>

	
	<script>
	    setMapParameters(<?php echo json_encode($itemLocations) ?>, <?php echo json_encode($params) ?>);
	    <?php
	    if($this->tabId == 2) {
	        echo "window.onload = loadMapScript;";
	    }
	    ?>
	</script>
<?php }?>