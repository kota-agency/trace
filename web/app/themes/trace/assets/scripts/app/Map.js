import InfoBox from "google-maps-infobox";

function Map() {

    let activeInfowindow = null;

    let map;
    let markers = [];
    let geocoder;
    let infoBoxes = [];

    /**
     * initMap
     *
     * Renders a Google Map onto the selected jQuery element
     *
     * @date    22/10/19
     * @since   5.8.6
     *
     * @param   jQuery $el The jQuery element.
     * @return  object The map instance.
     */
    function initMap($el) {

        // Find marker elements within map.
        var $markers = $el.find('.marker');

        // Create gerenic map.
        var mapArgs = {
            zoom: $el.data('zoom') || 16,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            disableDefaultUI: true
        };
        var map = new google.maps.Map($el[0], mapArgs);

        // Add markers.
        $markers.each(function () {
            initMarker($(this), map);
        });

        // Center map based on markers.
        centerMap(map);

        google.maps.event.addListener(map, 'click', function () {
            if (activeInfowindow !== null) {
                activeInfowindow.close();
            }

            for (let i = 0; i < markers.length; i++) {
                markers[i].setIcon({
                    url: theme_params.stylesheet_dir + '/dist/images/marker.png', // url
                    scaledSize: new google.maps.Size(38, 50)
                });
            }
        });

        // Return map instance.
        return map;
    }

    function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
        }
    }

    function clearMarkers() {
        setMapOnAll(null);
    }


    function deleteMarkers() {
        clearMarkers();
        markers = [];
        infoBoxes = [];
    }


    /**
     * initMarker
     *
     * Creates a marker for the given jQuery element and map.
     *
     * @date    22/10/19
     * @since   5.8.6
     *
     * @param   jQuery $el The jQuery element.
     * @param   object The map instance.
     * @return  object The marker instance.
     */
    function initMarker($marker, map) {

        // Get position from marker.
        var lat = $marker.data('lat');
        var lng = $marker.data('lng');
        var latLng = {
            lat: parseFloat(lat),
            lng: parseFloat(lng)
        };

        let icon = {
            url: theme_params.stylesheet_dir + '/dist/images/marker.png', // url
            scaledSize: new google.maps.Size(38, 50)
        };

        // Create marker instance.
        var marker = new google.maps.Marker({
            position: latLng,
            map: map,
            icon: icon
        });

        // Append to reference for later use.
        markers.push(marker);


        // If marker contains HTML, add it to an infoWindow.
        if ($marker.html()) {

            let infoBox = new InfoBox();

            var myOptions = {
                content: $marker.html(),
                disableAutoPan: false,
                maxWidth: 0,
                pixelOffset: new google.maps.Size(-40, 10),
                alignBottom: false,
                zIndex: null,
                boxStyle: {
                    opacity: 1,
                    width: "390px"
                },
                // closeBoxMargin: "0 0 0 0",
                closeBoxURL: theme_params.stylesheet_dir + '/dist/images/close.png',
                infoBoxClearance: new google.maps.Size(1, 1),
                isHidden: false,
                pane: "floatPane",
                enableEventPropagation: false
            };


            // Create info window.
            // var infowindow = new google.maps.InfoWindow({
            //     content: $marker.html()
            // });

            infoBox.setOptions(myOptions);

            infoBoxes.push(infoBox);

            // Show info window when marker is clicked.
            google.maps.event.addListener(infoBox, 'closeclick', function () {
                if (activeInfowindow !== null) {
                    activeInfowindow.close();
                }

                for (let i = 0; i < markers.length; i++) {
                    if (markers[i].type === 'Distributor') {
                        markers[i].setIcon({
                            url: theme_params.stylesheet_dir + '/dist/images/marker-primary.png', // url
                            scaledSize: new google.maps.Size(38, 50)
                        });
                    } else {
                        markers[i].setIcon({
                            url: theme_params.stylesheet_dir + '/dist/images/marker.png', // url
                            scaledSize: new google.maps.Size(38, 50)
                        });
                    }
                }
            });

            google.maps.event.addListener(marker, 'click', function () {

                if (activeInfowindow !== null) {
                    activeInfowindow.close();
                }

                for (let i = 0; i < markers.length; i++) {
                    markers[i].setIcon({
                        url: theme_params.stylesheet_dir + '/dist/images/marker.png', // url
                        scaledSize: new google.maps.Size(38, 50)
                    });
                }


                marker.setIcon({
                    url: theme_params.stylesheet_dir + '/dist/images/marker-solid.png', // url
                    scaledSize: new google.maps.Size(38, 50)
                });


                infoBox.open(map, marker);
                map.setCenter(marker.getPosition());
                activeInfowindow = infoBox;

            });
        }
    }

    /**
     * centerMap
     *
     * Centers the map showing all markers in view.
     *
     * @date    22/10/19
     * @since   5.8.6
     *
     * @param   object The map instance.
     * @return  void
     */
    function centerMap(map) {

        // Create map boundaries from all map markers.
        var bounds = new google.maps.LatLngBounds();
        map.markers.forEach(function (marker) {
            bounds.extend({
                lat: marker.position.lat(),
                lng: marker.position.lng()
            });
        });

        // Case: Single marker.
        if (map.markers.length == 1) {
            map.setCenter(bounds.getCenter());

            // Case: Multiple markers.
        } else {
            map.fitBounds(bounds);
        }
    }

    function codeAddress() {

        var address = document.getElementById('filter-postcode').value;
        if (address) {
            return new Promise(function (resolve, reject) {
                geocoder.geocode({'address': address}, (results, status) => {
                    if (status == 'OK') {

                        resolve({lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()});

                    } else {
                        console.log('Geocode was not successful for the following reason: ' + status);
                    }
                });
            });

        }
    }

// Render maps on page load.
    $(document).ready(function () {
        $('.acf-map').each(function () {
            map = initMap($(this));
        });

        geocoder = new google.maps.Geocoder();
    });

}

export default Map;
