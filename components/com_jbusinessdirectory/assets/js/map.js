var locations;
var params;
var panorama;

function setMapParameters(items, map_params) {
    params = map_params;
    locations = items;
}

function initialize() {
    var center = 0;
    if (typeof params['map_longitude'] !== 'undefined' && typeof params['map_latitude'] !== 'undefined') {
        center = new google.maps.LatLng(params['map_latitude'], params['map_longitude']);
    }

    var search_styles = [{
        "featureType": "administrative",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#808080"}]
    }, {
        "featureType": "administrative.locality",
        "elementType": "all",
        "stylers": [{"visibility": "on"}]
    }, {
        "featureType": "administrative.neighborhood",
        "elementType": "all",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "administrative.neighborhood",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#de2929"}]
    }, {
        "featureType": "administrative.land_parcel",
        "elementType": "all",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "administrative.land_parcel",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#de1616"}]
    }, {
        "featureType": "landscape",
        "elementType": "geometry.fill",
        "stylers": [{"lightness": "61"}, {"saturation": "-62"}]
    }, {
        "featureType": "landscape.man_made",
        "elementType": "all",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "landscape.man_made",
        "elementType": "labels",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "landscape.natural.landcover",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#ff0000"}, {"visibility": "off"}]
    }, {
        "featureType": "landscape.natural.terrain",
        "elementType": "geometry.stroke",
        "stylers": [{"visibility": "on"}]
    }, {
        "featureType": "landscape.natural.terrain",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#b2b2b2"}, {"visibility": "on"}]
    }, {
        "featureType": "poi",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#C5E3BF"}]
    }, {
        "featureType": "poi",
        "elementType": "labels.text.fill",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "poi.attraction",
        "elementType": "all",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "poi.attraction",
        "elementType": "labels.text",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "poi.business",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#e8e8e8"}]
    }, {
        "featureType": "poi.government",
        "elementType": "all",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "poi.medical",
        "elementType": "all",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "poi.park",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#b8e695"}]
    }, {
        "featureType": "poi.park",
        "elementType": "labels",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "poi.place_of_worship",
        "elementType": "all",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "poi.school",
        "elementType": "all",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "poi.sports_complex",
        "elementType": "all",
        "stylers": [{"visibility": "off"}]
    }, {
        "featureType": "road",
        "elementType": "geometry",
        "stylers": [{"lightness": 100}, {"visibility": "simplified"}]
    }, {
        "featureType": "road",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#D1D1B8"}]
    }, {
        "featureType": "road.highway",
        "elementType": "geometry.fill",
        "stylers": [{"visibility": "on"}, {"color": "#ffffff"}]
    }, {
        "featureType": "road.highway",
        "elementType": "geometry.stroke",
        "stylers": [{"color": "#e4e4e4"}, {"visibility": "simplified"}]
    }, {
        "featureType": "road.arterial",
        "elementType": "geometry.fill",
        "stylers": [{"visibility": "on"}, {"color": "#ffffff"}]
    }, {
        "featureType": "road.arterial",
        "elementType": "geometry.stroke",
        "stylers": [{"color": "#e4e4e4"}, {"visibility": "on"}]
    }, {
        "featureType": "road.arterial",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#b2b2b2"}]
    }, {
        "featureType": "road.local",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#ffffff"}, {"visibility": "on"}]
    }, {
        "featureType": "road.local",
        "elementType": "geometry.stroke",
        "stylers": [{"color": "#e4e4e4"}, {"visibility": "on"}]
    }, {
        "featureType": "road.local",
        "elementType": "labels.text.fill",
        "stylers": [{"visibility": "on"}, {"color": "#b2b2b2"}]
    }, {
        "featureType": "transit",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#e1e1e1"}]
    }, {
        "featureType": "transit",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#b2b2b2"}]
    }, {"featureType": "water", "elementType": "geometry", "stylers": [{"visibility": "on"}, {"color": "#accff7"}]}];

    // default map zoom
    var map_zoom = jbdUtils.mapDefaultZoom;
    if (typeof params['map_zoom'] !== 'undefined') {
        map_zoom = params['map_zoom'];
    }

    var mapOptions = {
        zoom: map_zoom,
        scrollwheel: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: center
    };

    if (params['map_style'] == 'search')
        mapOptions.styles = search_styles;

    // default to company
    var mapDivId = "company-map";
    if (typeof params['map_div'] !== 'undefined')
        mapDivId = params['map_div'];

    var mapId = '';
    if (typeof params['tmapId'] !== 'undefined') {
        mapId = params['tmapId'];
    }
    var mapdiv = document.getElementById(mapDivId + '' + mapId);

    var map_width = '100%';
    var map_height = '450px';
    if (typeof params["map_width"] !== 'undefined')
        map_width = params["map_width"];
    if (typeof params["map_height"] !== 'undefined')
        map_height = params["map_height"];

    mapdiv.style.width = map_width;
    mapdiv.style.height = map_height;

    var map = new google.maps.Map(mapdiv, mapOptions);

    setMarkers(map);

    if (params['panorama'] == 1) {
        var company = locations[0];
        var myLatLng = new google.maps.LatLng(company.latitude, company.longitude);

        panorama = map.getStreetView();
        panorama.setPosition(myLatLng);
        panorama.setPov(/** @type {google.maps.StreetViewPov} */({
            heading: 265,
            pitch: 0
        }));
    }
}

function setMarkers(map) {
    // Add markers to the map

    // Marker sizes are expressed as a Size of X,Y
    // where the origin of the image (0,0) is located
    // in the top left of the image.

    // Origins, anchor positions and coordinates of the marker
    // increase in the X direction to the right and in
    // the Y direction down.

    var bounds = new google.maps.LatLngBounds();
    var markers = [];

    for (var i = 0; i < locations.length; i++) {
        var item = locations[i];

        //skip iteration if not defined
        if(item.length == 0 || item === 'undefined')
            continue;

        var pinImage = null;

        if (item['marker'] != '0') {
            pinImage = new google.maps.MarkerImage(item['marker'],
                // This marker is 20 pixels wide by 32 pixels tall.
                new google.maps.Size(32, 32),
                // The origin for this image is 0,0.
                new google.maps.Point(0, 0),
                // The anchor for this image is the base of the flagpole at 0,32.
                new google.maps.Point(0, 32));
        }
        else if (jbdUtils.mapMarker.length) {
            pinImage = new google.maps.MarkerImage(jbdUtils.imageBaseUrl + jbdUtils.mapMarker,
                // This marker is 20 pixels wide by 32 pixels tall.
                new google.maps.Size(32, 32),
                // The origin for this image is 0,0.
                new google.maps.Point(0, 0),
                // The anchor for this image is the base of the flagpole at 0,32.
                new google.maps.Point(0, 32));
        }


        var myLatLng = new google.maps.LatLng(item['latitude'], item['longitude']);

        //Check Markers array for duplicate position and offset a little
        if (markers.length != 0 && false) {
            for (i = 0; i < markers.length; i++) {
                var existingMarker = markers[i];
                var pos = existingMarker.getPosition();
                var distance = google.maps.geometry.spherical.computeDistanceBetween(myLatLng, pos);
                if (distance < 50 && false) {
                    var a = 360.0 / markers.length;
                    var newLat = pos.lat() + -.00004 * Math.cos((+a * i) / 180 * Math.PI);  //x
                    var newLng = pos.lng() + -.00004 * Math.sin((+a * i) / 180 * Math.PI);  //Y
                    myLatLng = new google.maps.LatLng(newLat, newLng);
                }
            }
        }

        var zIndex = 0;
        if (typeof item['zIndex'] !== 'undefined') {
            zIndex = item['zIndex'];
        }

        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            icon: pinImage,
            animation: google.maps.Animation.DROP,
            title: item['title'],
            zIndex: zIndex

        });

        markers.push(marker);

        if (params["isLayout"] == 1) {
            (function (Marker) {
                google.maps.event.addListener(marker, 'click', function () {
                    var target = "#company" + this.getZIndex();
                    window.location = target;

                    jQuery(target).fadeOut(1, function () {
                        jQuery(target).css("background-color", "#469021").fadeIn(500);
                    });

                    setTimeout(function () {
                        jQuery(target).removeClass('selected-company');
                        jQuery(target).fadeOut(1, function () {
                            jQuery(target).css("background-color", "transparent").fadeIn(700);
                        });
                    }, 1200);
                });
            }(marker));
        }
        else {
            var contentBody = item['content'];
            var infowindow = new google.maps.InfoWindow({
                content: contentBody,
                maxWidth: 210
            });

            google.maps.event.addListener(marker, 'click', function (contentBody) {
                return function () {
                    infowindow.setContent(contentBody);
                    infowindow.open(map, this);
                }
            }(contentBody));
        }

        bounds.extend(myLatLng);
    }

    if (params["isLayout"] == 1) {
        jQuery(".btn-show-marker").click(function () {
            var companyID = jQuery(this).closest('.grid-item-holder').attr('id');
            var id = companyID.match(/\d/g);
            id = id.join('');

            for (i = 0; i < markers.length; i++) {
                if (markers[i].getZIndex() == id) {
                    map.setZoom(16);
                    map.setCenter(markers[i].getPosition());
                }
            }
        });
    }

    if (params["map_clustering"] == 1) {
        mcOptions = {
            imagePath: params['imagePath'] + "mapcluster/m"
        };
        var markerCluster = new MarkerClusterer(map, markers, mcOptions);
    }

    if (params["has_location"] == 1) {
        var pinImage = new google.maps.MarkerImage("https://maps.google.com/mapfiles/kml/shapes/library_maps.png",
            new google.maps.Size(31, 34),
            new google.maps.Point(0, 0),
            new google.maps.Point(10, 34)
        );

        myLatLng = new google.maps.LatLng(params["latitude"], params["longitude"]);
        marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            icon: pinImage
        });
    }

    if (params["radius"] > 0) {
        // Add circle overlay and bind to marker

        if (typeof params['map_longitude'] !== 'undefined' && typeof params['map_latitude'] !== 'undefined') {
            params['longitude'] = params['map_longitude'];
            params['latitude'] = params['map_latitude'];
        }

    	if (typeof params['longitude'] !== 'undefined' && typeof params['latitude'] !== 'undefined' && params['longitude'] !== '') {
    		  map.setCenter(new google.maps.LatLng(params['latitude'], params['longitude']));
    		  var circle = new google.maps.Circle({
    	            map: map,
    	            radius: params['radius'] * 1600,
    	            strokeColor: "#006CD9",
    	            strokeOpacity: 0.7,
    	            strokeWeight: 2,
    	            fillColor: "#006CD9",
    	            fillOpacity: 0.15
    	        });
    	        circle.bindTo('center', marker, 'position');
    	}
      
    }

    bounds.extend(myLatLng);

    if (params['autolocate'] == 1) {
        map.fitBounds(bounds);
    }

    var listener = google.maps.event.addListener(map, "idle", function () {
        if (map.getZoom() > 16) map.setZoom(16);
        google.maps.event.removeListener(listener);
    });
}

function toggleBounce(marker) {
    if (marker.getAnimation() !== null) {
        marker.setAnimation(null);
    } else {
        marker.setAnimation(google.maps.Animation.BOUNCE);
    }
}

function loadMapScript() {
    initialize();
}

function toggleStreetView() {
    var toggle = panorama.getVisible();
    if (toggle == false) {
        panorama.setVisible(true);
    } else {
        panorama.setVisible(false);
    }
}