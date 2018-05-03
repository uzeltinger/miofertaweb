<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (! defined ( 'COMPONENT_IMAGE_PATH' ))
	define ( "COMPONENT_IMAGE_PATH", JURI::base () . "components/com_jbusinessdirectory/assets/images/" );

$appSettings = JBusinessUtil::getInstance ()->getApplicationSettings ();
$lang = JFactory::getLanguage ()->getTag ();
$key = "";
if (! empty ( $appSettings->google_map_key ))
	$key = "&key=" . $appSettings->google_map_key;

	JHtml::_('script', "https://maps.googleapis.com/maps/api/js?language=".$lang.$key."&libraries=geometry");

$map_latitude = ( float ) $appSettings->map_latitude;
$map_longitude = ( float ) $appSettings->map_longitude;
$map_zoom = ( float ) $appSettings->map_zoom;
$map_enable_auto_locate = $appSettings->map_enable_auto_locate;
$map_apply_search = $appSettings->map_apply_search;

$map_latitude = ( float ) $appSettings->map_latitude;
$map_longitude = ( float ) $appSettings->map_longitude;
$map_zoom = ( float ) $appSettings->map_zoom;

if ((empty ( $map_latitude )) || (! is_numeric ( $map_latitude )))
	$map_latitude = 43.749156;

if ((empty ( $map_longitude )) || (! is_numeric ( $map_longitude )))
	$map_longitude = - 79.411048;

if ((empty ( $map_zoom )) || (! is_numeric ( $map_zoom )))
	$map_zoom = 6;

if ($map_apply_search == '0') {
	$map_latitude = 43.749156;
	$map_longitude = - 79.411048;
	$map_zoom = 6;
}

$map_enable_auto_locate = "";
if ($appSettings->map_enable_auto_locate) {
	$map_enable_auto_locate = "map.fitBounds(bounds);";
}

if ($appSettings->enable_google_map_clustering) {
	JHtml::_ ( 'script', 'components/com_jbusinessdirectory/assets/js/markercluster.js' );
}

$mapId = rand ( 1000, 10000 );

$event_locations = array ();

$db = JFactory::getDBO ();

if (! isset ( $events ))
	$events = $this->events;

$index = 1;
foreach ( $events as $event ) {
	$tmp = array ();
	$marker = 0;
	if (! empty ( $event->categoryMaker )) {
		$marker = JURI::root () . PICTURES_PATH . $event->categoryMaker;
	}
	
	$contentString = '<div class="info-box">' . '<div class="title">' . htmlspecialchars($event->name) . '</div>' . '<div class="info-box-content">' . '<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">' . htmlspecialchars(JBusinessUtil::getAddressText ( $event ), ENT_QUOTES) . '</div>' . '<div class="info-phone"><i class="dir-icon-phone"></i> ' . htmlspecialchars($event->phone, ENT_QUOTES). '</div>' . '<a href="' . htmlspecialchars(JBusinessUtil::geteventLink ( $event->id, $event->alias )) . '"><i class="dir-icon-external-link"></i> ' . $db->escape ( JText::_ ( "LNG_MORE_INFO", true ) ) . '</a>' . '</div>' . '<div class="info-box-image">' . (! empty ( $event->picture_path ) ? '<img src="' . JURI::root () . PICTURES_PATH . $event->picture_path . '" alt="' . htmlspecialchars($event->name) . '">' : "") . '</div>' . '</div>';
	
	if (! empty ( $event->latitude ) && ! empty ( $event->longitude )) {
		$tmp ['title'] = htmlspecialchars($event->name);
		$tmp ['latitude'] = $event->latitude;
		$tmp ['longitude'] = $event->longitude;
		$tmp ['zIndex'] = 4;
		$tmp ['content'] = $contentString;
		$tmp [] = $index;
		$tmp ['marker'] = $marker;
		
		$event_locations [] = $tmp;
	}
	
	$index ++;
}

// the params array that will be used on map.js
$initparams = array ();
$initparams ["tmapId"] = $mapId;
$initparams ["map_div"] = 'events-map-';
$initparams ["map_latitude"] = ! empty ( $map_latitude ) ? $map_latitude : 0;
$initparams ["map_longitude"] = ! empty ( $map_longitude ) ? $map_longitude : 0;
$initparams ["map_zoom"] = $map_zoom;
$initparams ["has_location"] = (isset ( $this ) && ! empty ( $this->location ["latitude"] )) ? 1 : 0;
$initparams ["radius"] = ! empty ( $radius ) ? $radius : 0;
$initparams ["autolocate"] = $appSettings->map_enable_auto_locate;
$initparams ["map_clustering"] = $appSettings->enable_google_map_clustering;
$initparams ["imagePath"] = COMPONENT_IMAGE_PATH;
$initparams ["longitude"] = '';
$initparams ["latitude"] = '';
if (isset ( $this ) && ! empty ( $this->location ["latitude"] )) {
	$initparams ["longitude"] = $this->location ["longitude"];
	$initparams ["latitude"] = $this->location ["latitude"];
}
?>
<div id="events-map-<?php echo $mapId ?>" style="position: relative;"></div>

<script>
    setMapParameters(<?php echo json_encode($event_locations) ?>, <?php echo json_encode($initparams) ?>);
</script>