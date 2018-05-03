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

if(!empty( $this->offer->company->categoryMarker)) {
    $marker = JURI::root().PICTURES_PATH. $this->offer->company->categoryMarker;
}

$db = JFactory::getDBO();
if(!empty($this->event->company->phone)){
    $contentPhone = '<div class="info-phone"><i class="dir-icon-phone"></i> '.htmlspecialchars($this->event->company->phone, ENT_QUOTES).'</div>';
} else {
    $contentPhone = '';
}

$contentString =
    '<div class="info-box">'.
    '<div class="title">'.htmlspecialchars($this->offer->subject).'</div>'.
    '<div class="info-box-content">'.
    '<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">'.
    htmlspecialchars(JBusinessUtil::getAddressText($this->offer), ENT_QUOTES).'</div>'.$contentPhone.
    '</div>'.
    '<div class="info-box-image">'.
    (!empty($this->offer->pictures[0]->picture_path)?'<img src="'. JURI::root().PICTURES_PATH.(htmlspecialchars($this->offer->pictures[0]->picture_path, ENT_QUOTES)).'" alt="'.$db->escape($this->offer->subject, ENT_QUOTES).'">':"").
    '</div>'.
    '</div>';

$itemLocations = array();
$tmp = array();
if(!empty($this->offer->latitude) && !empty($this->offer->longitude)){
    $tmp['latitude'] = $this->offer->latitude;
    $tmp['longitude'] = $this->offer->longitude;
    $tmp['marker'] = $marker;
    $tmp['content'] = $contentString;
}

$params = array();
$params['map_div'] = 'offer-map-2';
$params['map_longitude'] = $this->offer->longitude;
$params['map_latitude'] = $this->offer->latitude;

$itemLocations[] = $tmp;
?>

<?php if(isset($this->offer->latitude) && isset($this->offer->longitude)) {
    ?>
    <a target="_blank" href="https://maps.google.com/?saddr=<?php echo $this->offer->latitude.",".$this->offer->longitude ?>&daddr=<?php echo $this->offer->latitude.",".$this->offer->longitude ?>"><?php echo JText::_("LNG_GET_MAP_DIRECTIONS")?></a>
<?php }?>
<div id="offer-map-2" style="position:relative;">
</div>

<script>
    setMapParameters(<?php echo json_encode($itemLocations) ?>, <?php echo json_encode($params)?>);
    window.onload = loadMapScript;
</script>
