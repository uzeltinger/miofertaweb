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

if(!empty( $this->event->company->categoryMarker)) {
    $marker = JURI::root().PICTURES_PATH. $this->event->company->categoryMarker;
}

$db = JFactory::getDBO();
if(!empty($this->event->contact_phone)){
    $contentPhone = '<div class="info-phone"><i class="dir-icon-phone"></i> '.htmlspecialchars($this->event->contact_phone, ENT_QUOTES).'</div>';
} else {
    $contentPhone = '';
}

$contentString =
    '<div class="info-box">'.
    '<div class="title">'.htmlspecialchars($this->event->name).'</div>'.
    '<div class="info-box-content">'.
    '<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">'.
    htmlspecialchars(JBusinessUtil::getAddressText($this->event), ENT_QUOTES).'</div>'.$contentPhone.
    '</div>'.
    '<div class="info-box-image">'.
    (!empty($this->event->pictures[0]->picture_path)?'<img src="'. JURI::root().PICTURES_PATH.(htmlspecialchars($this->event->pictures[0]->picture_path, ENT_QUOTES)).'" alt="'.htmlspecialchars($this->event->name).'">':"").
    '</div>'.
    '</div>';

$itemLocations = array();
$tmp = array();
if(!empty($this->event->latitude) && !empty($this->event->longitude)){
    $tmp['latitude'] = $this->event->latitude;
    $tmp['longitude'] = $this->event->longitude;
    $tmp['marker'] = $marker;
    $tmp['content'] = $contentString;
}

$params = array();
$params['map_div'] = 'event-map-2';
$params['map_longitude'] = $this->event->longitude;
$params['map_latitude'] = $this->event->latitude;

$itemLocations[] = $tmp;
?>

<?php if(isset($this->event->latitude) && isset($this->event->longitude)) {?>
    <a target="_blank" href="<?php echo JBusinessUtil::getDirectionURL($this->location, $this->event) ?>"><?php echo JText::_("LNG_GET_MAP_DIRECTIONS")?></a>
<?php }?>
<div id="event-map-2" style="position:relative;">
</div>

<script>
    setMapParameters(<?php echo json_encode($itemLocations) ?>, <?php echo json_encode($params)?>);
    window.onload = loadMapScript;
</script>