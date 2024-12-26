angular.module('resources.locations', [])

.factory('locations', function($rootScope, $q, storageService, apiService) {
  var service = {
    COUNTRY_LIST: [],
    PROVINCE_LIST: {},
    PARK_LIST: {},
    DISTRICT_DATA: {},
    WARD_DATA: {},
    STREET_DATA: {},

    /**
     * Get Location
     */
    getLocation: function() {
      var deferred = $q.defer();
      if (navigator && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          deferred.resolve(position);
        }, function(error) {
          deferred.reject(error);
        });
      } else {
        deferred.reject(null);
      }

      return deferred.promise;
    },

    /**
     * Get Address
     */
    getAddress: function(lat, lng) {
      var deferred = $q.defer();
      var geocoder = new google.maps.Geocoder();
      var latLng = new google.maps.LatLng(lat, lng);
      geocoder.geocode({latLng: latLng}, function(a, b) {
        if (b === google.maps.GeocoderStatus.OK) {
          deferred.resolve(a);
        } else {
          console.error("Geocoder failed due to: " + b);
          deferred.reject(b);
        }
      });

      return deferred.promise;
    },

    getCountries: function() {
      var deferred = $q.defer();
      if (service.COUNTRY_LIST.length > 0) {
        deferred.resolve(service.COUNTRY_LIST);
      } else {
        var data = storageService.getItem('vn_allCountries');
        if (data) {
          service.COUNTRY_LIST = data;
          deferred.resolve(service.COUNTRY_LIST);
        } else {
          apiService.get('loc_countries_all').then(function(response) {
            storageService.setItem('vn_allCountries', response.data.data.data, {expires: apiService.getExpires(7)});
            service.COUNTRY_LIST = response.data.data.data;
            deferred.resolve(service.COUNTRY_LIST);
          }, function(error) {
            deferred.reject(error.data.errors);
          });
        }
      }

      return deferred.promise;
    },

    /**
     * @returns array
     */
    getProvinces: function(country_id) {
      var key = 'vn_PWCId_' + country_id;
      var deferred = $q.defer();
      if(service.PROVINCE_LIST[country_id] && service.PROVINCE_LIST[country_id].length > 0) {
        deferred.resolve(service.PROVINCE_LIST[country_id]);
      } else {
        var data = storageService.getItem(key);
        if(data) {
          service.PROVINCE_LIST[country_id] = data;
          deferred.resolve(service.PROVINCE_LIST[country_id]);
        } else {
          apiService.get('loc_provinces_all').then(function(response) {
            storageService.setItem(key, response.data.data.data, {expires: apiService.getExpires(7)});
            service.PROVINCE_LIST[country_id] = response.data.data.data;
            deferred.resolve(service.PROVINCE_LIST[country_id]);
          }, function(error) {
            deferred.reject(error.data.errors);
          });
        }
      }

      return deferred.promise;
    },

    /**
     * GetDistrictsByProvinceId
     * @param province_id
     * @param data
     * @returns {*}
     */
    getDistrictsByProvinceId: function(province_id, data) {
      var deferred = $q.defer();
      if (data === undefined) {
        if (service.DISTRICT_DATA[province_id] && service.DISTRICT_DATA[province_id].length > 0) {
          deferred.resolve(service.DISTRICT_DATA[province_id]);
        } else {
          var districts = storageService.getItem('vn_DWPId_' + province_id);
          if (districts) {
            service.DISTRICT_DATA[province_id] = districts;
            deferred.resolve(service.DISTRICT_DATA[province_id]);
          } else {
            apiService.get('loc_districts_all?province_id=' + province_id).then(function(response) {
              storageService.setItem('vn_DWPId_' + province_id, response.data.data.data, {expires: apiService.getExpires(7)});
              service.DISTRICT_DATA[province_id] = response.data.data.data;
              deferred.resolve(service.DISTRICT_DATA[province_id]);
            }, function(error) {
              deferred.reject(error.data.errors);
            });
          }
        }
      } else {
        apiService.get('loc_districts_all?province_id=' + province_id, {data: data}).then(function(response) {
          deferred.resolve(response.data.data.data);
        }, function(error) {
          deferred.reject(error.data.errors);
        });
      }

      return deferred.promise;
    },

    /**
     * GetWardsByDistrictId
     * @param district_id
     * @returns {*}
     */
    getWardsByDistrictId: function(district_id) {
      var deferred = $q.defer();
      if (service.WARD_DATA[district_id] && service.WARD_DATA[district_id].length > 0) {
        deferred.resolve(service.WARD_DATA[district_id]);
      } else {
        var data = storageService.getItem('vn_WWDId_' + district_id);
        if (data) {
          service.WARD_DATA[district_id] = data;
          deferred.resolve(service.WARD_DATA[district_id]);
        } else {
          apiService.get('loc_wards_all?district_id=' + district_id).then(function(response) {
            storageService.setItem('vn_WWDId_' + district_id, response.data.data.data, {expires: apiService.getExpires(7)});
            service.WARD_DATA[district_id] = response.data.data.data;
            deferred.resolve(service.WARD_DATA[district_id]);
          }, function(error) {
            deferred.reject(error);
          });
        }
      }

      return deferred.promise;
    },
  };

  return service;
});
