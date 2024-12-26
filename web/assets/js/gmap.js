function GMap() {
  var self = this;
  self.elemMap = null;
  self.map = null;
  self.service = null;
  self.geocoder = null;
  self.infoWindow = null;
  self.marker = null;
  self.autocomplete = null;
  self.directionsRenderer = null;
  self.directionsService = null;
  self.latLng = null;
  self.options = {
    apiKey: '',
    elemMapId: 'gmap',
    latitude: 10.771918,
    longitude: 106.698347,
    zoom: 13,
    isLockDrag: true,
    elemLatitude: null,
    elemLongitude: null,
    elemLocation: null,
    elemAddress: null,
    elemPlaceId: null,
    elemPlaceType: null,
    content: '',
    icon: '/assets/images/icons/ico-marker.png',
    markerDragEnd: function (event) {

    }
  };
  self.supported_types = [
    'hospital',
    'restaurant',
    'grocery_or_supermarket',
    'gas_station',
    'city_hall',
    'local_government_office',
    'atm',
  ];
  self.nearRadius = 200;

  self.initialize = function (options) {
    if (options) {
      $.each(options, function (key, value) {
        if (self.options.hasOwnProperty(key) && value) {
          self.options[key] = value;
        }
      });
    }

    self.elemMap = document.getElementById(self.options.elemMapId);
    if (!self.elemMap) {
      console.error('Element name does not exist!');
    }
    self.map = new google.maps.Map(self.elemMap, {
      center: {lat: self.options.latitude, lng: self.options.longitude},
      zoom: self.options.zoom
    });
    self.service = new google.maps.places.PlacesService(self.map);

    self.infoWindow = new google.maps.InfoWindow;

    self.marker = self.addMarker(self.options.latitude, self.options.longitude, self.options.content);

    // Pan to
    var latLng = new google.maps.LatLng(self.options.latitude, self.options.longitude);
    self.map.panTo(latLng);

    //--- Events
    self.map.addListener('click', function (e) {
      if (!e.placeId) {
        return;
      }

      self.getDetails(e.placeId);
    });

    // self.map.addListener('rightclick', function(e) {
    //   self.latLng = e.latLng;
    //   self.marker.setPosition(self.latLng);
    // });
  };

  self.resize = function () {
    google.maps.event.trigger(self.map, 'resize');
  };

  self.openInfoWindow = function (place, marker) {
    self.infoWindow.setContent(place.name);
    self.infoWindow.open(self.map, marker);
  };

  self.closeInfoWindow = function () {
    self.infoWindow.close();
  };

  self.markerDragStart = function (event) {

  };

  self.markerDragEnd = function (event) {
    self.latLng = event.latLng;
    self.options.latitude = event.latLng.lat();
    self.options.longitude = event.latLng.lng();

    self.updateElementByLatLng(self.options.latitude, self.options.longitude);

    self.options.markerDragEnd(event);
  };

  self.addMarker = function (latitude, longitude, content) {
    var marker = new google.maps.Marker({
      map: self.map,
      position: {lat: latitude, lng: longitude},
      draggable: !self.options.isLockDrag,
      animation: google.maps.Animation.DROP,
      icon: self.options.icon,
    });
    if (!self.options.isLockDrag) {
      self.marker.addListener('dragstart', self.markerDragStart);
      self.marker.addListener('dragend', self.markerDragEnd);
    }

    //--- Events
    marker.addListener('click', function (e) {
      var latLng = marker.getPosition();
      console.log(latLng);

      if(content) {
        self.infoWindow.setContent(content);
        self.infoWindow.open(self.map, marker);
      }
    });

    return marker;
  };

  self.updateMarkerPosition = function (lat, lng) {
    self.latLng = new google.maps.LatLng(lat, lng);
    if (self.map && self.marker) {
      self.marker.setPosition(self.latLng);
      self.map.panTo(self.latLng);
    }
  };

  self.panTo = function (lat, lng) {
    if (self.map) {
      var latLng = new google.maps.LatLng(lat, lng);
      self.map.panTo(latLng);
    }
  };

  self.updateElementByPlace = function (place, isChangeLocation) {
    var lat = place.geometry.location.lat();
    var lng = place.geometry.location.lng();

    $(self.options.elemLatitude).val(lat);
    $(self.options.elemLongitude).val(lng);
    $(self.options.elemPlaceId).val(place.place_id);
    $(self.options.elemPlaceType).val(place.types.join(','));
    if (isChangeLocation) {
      $(self.options.elemLocation).val(place.formatted_address);
    }
    $(self.options.elemAddress).val(place.vicinity);

    this.nearbySearchPrint(lat, lng);
  };

  self.updateElementByLatLng = function (lat, lng) {
    $(self.options.elemLatitude).val(lat);
    $(self.options.elemLongitude).val(lng);

    self.getGeocodeByLatLng(lat, lng, function (place) {
      if (place) {
        $(self.options.elemPlaceId).val(place.place_id);
        $(self.options.elemLocation).val(place.formatted_address);
        $(self.options.elemPlaceType).val(place.types.join(','));
        $(self.options.elemAddress).val(place.vicinity);
      }
    });

    this.nearbySearchPrint(lat, lng);
  };

  self.getDetails = function (placeId) {
    self.service.getDetails({
      placeId: placeId
    }, function (place, status) {
      if (status === google.maps.places.PlacesServiceStatus.OK) {
        self.latLng = place.geometry.location;
        self.marker.setPosition(self.latLng);
        self.place = place;
        self.updateElementByPlace(place, true);

        /*google.maps.event.addListener(marker, 'click', function () {
         infoWindow.setContent('<div><strong>' + place.name + '</strong><br>' + place.formatted_address + '<br>' + '<a class="btn btn-primary" onclick="applyPlace()">Chọn vị trí này</a></div>');
         infoWindow.open(map, this);
         });*/
      }
    });
  };

  self.getGeocodeByLatLng = function (lat, lng, cb) {
    self.initGeocoder();
    self.geocoder.geocode({'location': {lat: parseFloat(lat), lng: parseFloat(lng)}}, function (results, status) {
      if (status === google.maps.GeocoderStatus.OK) {
        if (cb instanceof Function) {
          cb(results[0]);
        }
      } else {
        console.error(status);
      }
    });
  };

  // This function is called when the user clicks the UI button requesting
  // a reverse geocode.
  self.getGeocodeByPlaceId = function (placeId, cb) {
    self.initGeocoder();
    self.geocoder.geocode({'placeId': placeId}, function (results, status) {
      if (status === google.maps.GeocoderStatus.OK) {
        if (cb instanceof Function) {
          cb(results[0]);
        }
      } else {
        console.error(status);
      }
    });
  };

  self.getGeocodeByByAddress = function (address, cb) {
    self.initGeocoder();
    self.geocoder.geocode({'address': address}, function (results, status) {
      if (status === google.maps.GeocoderStatus.OK) {
        if (cb instanceof Function) {
          cb(results[0]);
        }
      } else {
        console.error(status);
      }
    });
  };

  //--- Get Zoom
  self.getBoundsZoomLevel = function (bounds) {
    var WORLD_DIM = {height: 400, width: 400};
    var ZOOM_MAX = 21;

    function latRad(lat) {
      var sin = Math.sin(lat * Math.PI / 180);
      var radX2 = Math.log((1 + sin) / (1 - sin)) / 2;
      return Math.max(Math.min(radX2, Math.PI), -Math.PI) / 2;
    }

    function zoom(mapPx, worldPx, fraction) {
      return Math.floor(Math.log(mapPx / worldPx / fraction) / Math.LN2);
    }

    var ne = bounds.getNorthEast();
    var sw = bounds.getSouthWest();

    var latFraction = (latRad(ne.lat()) - latRad(sw.lat())) / Math.PI;

    var lngDiff = ne.lng() - sw.lng();
    var lngFraction = ((lngDiff < 0) ? (lngDiff + 360) : lngDiff) / 360;

    var latZoom = zoom(self.elemMap.offsetHeight, WORLD_DIM.height, latFraction);
    var lngZoom = zoom(self.elemMap.offsetWidth, WORLD_DIM.width, lngFraction);

    return Math.min(latZoom, lngZoom, ZOOM_MAX);
  };

  //--- Draw street
  self.calcRoute = function (start, end) {
    var request = {
      origin: start,
      destination: end,
      travelMode: google.maps.TravelMode.DRIVING
    };
    self.directionsService.route(request, function (response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        self.directionsRenderer.setDirections(response);
        self.directionsRenderer.setMap(self.map);
      } else {
        self.directionsRenderer.setMap(null);
      }
    });
  };

  self.updateMap = function (start, end) {
    if (start && end) {
      var start = new google.maps.LatLng(start.latitude, start.longitude);
      var end = new google.maps.LatLng(end.latitude, end.longitude);
      var bounds = new google.maps.LatLngBounds();
      bounds.extend(start);
      bounds.extend(end);
      var lat = bounds.getCenter().lat();
      var lng = bounds.getCenter().lng();
      self.map.setZoom(self.getBoundsZoomLevel(bounds));
      self.map.setCenter({lat: lat, lng: lng});
      self.calcRoute(start, end);
    } else if (start) {
      self.map.setCenter({lat: start.latitude, lng: start.longitude});
    } else if (end) {
      self.map.setCenter({lat: end.latitude, lng: end.longitude});
    }
  };

  self.createMarker = function (place) {
    var marker = new google.maps.Marker({
      map: self.map,
      position: place.geometry.location,
      icon: {
        url: 'https://developers.google.com/maps/documentation/javascript/images/circle.png',
        anchor: new google.maps.Point(10, 10),
        scaledSize: new google.maps.Size(10, 17)
      }
    });

    google.maps.event.addListener(marker, 'click', function() {
      self.service.getDetails(place, function(result, status) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
          self.openInfoWindow(result, marker);
        } else {
          console.error(status);
        }
      });
    });
  };

  self.nearbySearch = function (lat, lng) {
    var latLng = new google.maps.LatLng(lat, lng);
    var request = {
      location: latLng,
      radius: self.nearRadius,
    };
    if (self.supported_types.length) {
      request['types'] = self.supported_types;
    }
    this.service.nearbySearch(request, function (results, status) {
      if (status == google.maps.places.PlacesServiceStatus.OK) {
        for (var i = 0; i < results.length; i++) {
          self.createMarker(results[i]);
        }
      }
    });
  };

  self.nearbySearchPrint = function (lat, lng) {
    var latLng = new google.maps.LatLng(lat, lng);
    var types = [
      'hospital',
      'restaurant',
      'grocery_or_supermarket',
      'gas_station',
      'city_hall',
      'local_government_office',
      'atm',
    ];
    for (var i = 0; i < types.length; i++) {
      var request = {
        location: latLng,
        radius: 500,
        types: [types[i]]
      };
      this.service.nearbySearch(request, function (results, status) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
          var log = '';
          for (var i = 0; i < results.length; i++) {
            log += 'STT:       ' + (i+1) + "\n";
            log += 'Tên:       ' + results[i].name + "\n";
            log += 'Địa chỉ: ' + results[i].vicinity + "\n";
            log += "________________________\n";
            console.log(results[i].name);
            console.log(results[i].vicinity);
            console.log(results[i].types);
            console.log('________________________');
          }
          var elemLog = $('textarea[name="log"]');
          if (elemLog.length) {
            elemLog.val(log);
          }
        }
      });
    }
  };

  // Bias the autocomplete object to the user's geographical location,
  // as supplied by the browser's 'navigator.geolocation' object.
  self.geolocate = function () {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (position) {
        var geolocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        var circle = new google.maps.Circle({
          center: geolocation,
          radius: position.coords.accuracy
        });
        //autocomplete.setBounds(circle.getBounds());
      });
    }
  };

  self.initGeocoder = function () {
    if (!self.geocoder) {
      self.geocoder = new google.maps.Geocoder();
    }
  };

  self.initAutocomplete = function () {
    if (!self.options.elemLocation) {
      console.error('Element name does not exist!');
    }
    self.autocomplete = new google.maps.places.Autocomplete($(self.options.elemLocation)[0], {componentRestrictions: {country: 'vn'}});

    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.
    google.maps.event.addListener(self.autocomplete, 'place_changed', function () {
      var place = self.autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }

      // If the place has a geometry, then present it on a map.
      if (place.geometry.viewport) {
        self.map.fitBounds(place.geometry.viewport);
      } else {
        self.map.setCenter(place.geometry.location);
      }

      self.latLng = place.geometry.location;
      self.marker.setPosition(self.latLng);
      self.map.panTo(self.latLng);

      self.updateElementByPlace(place);
    });
  };

  self.initDirection = function () {
    self.directionsRenderer = new google.maps.DirectionsRenderer({
      map: self.map,
      polylineOptions: {
        strokeWeight: 5,
        strokeColor: '#ec5f41',
      },
      suppressMarkers: true,
    });
    self.directionsService = new google.maps.DirectionsService();
  };

  self.initPlace = function () {
    var timer = null;
    function getPlace() {
      var lat = $(self.options.elemLatitude).val();
      var lng = $(self.options.elemLongitude).val();

      if(!timer) {
        timer = setTimeout(function () {
          self.updateMarkerPosition(lat, lng);
          self.nearbySearchPrint(lat, lng);
          clearTimeout(timer);
          timer = null;
        }, 500);
      }
    }

    function setSupportedTypes(types) {
      self.supported_types = types;
      getPlace();
    }

    function setRadius(radius) {
      self.nearRadius = parseInt(radius);
      getPlace();
    }

    if(self.options.elemLatitude && self.options.elemLongitude) {
      $(self.options.elemLatitude).keyup(function () {
        getPlace();
      });

      $(self.options.elemLongitude).keyup(function () {
        getPlace();
      });

      var elemSupportedTypes = $('input[name="supported_types"][type="checkbox"]');
      if (elemSupportedTypes.length) {
        elemSupportedTypes.change(function () {
          var types = [];
          for (var i = 0; i < elemSupportedTypes.length; i++) {
            if (elemSupportedTypes[i].checked) {
              types.push(elemSupportedTypes[i].value);
            }
          }
          setSupportedTypes(types);
        });
      }

      var elemRadius = $('select[name="radius"]');
      if (elemRadius.length) {
        elemRadius.change(function () {
          setRadius(this.value);
        });
      }

      $(self.options.elemLatitude).trigger('keyup');
    }
  };
}
