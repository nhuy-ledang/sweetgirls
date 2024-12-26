angular.module('services.storage', [])

.factory('storageService', function() {
  var service = {
    getItem: function(key, type) {
      // var value = $cookies.get(key);
      var data = localStorage.getItem(key);
      var value;
      if (data && type !== 'string') {
        try {
          data = JSON.parse(data);
        } catch(e) {
          data = undefined;
        }
      }
      if (data) {
        if (data.o && data.o.expires && (new Date(data.o.expires).getTime() < new Date().getTime())) {
          value = undefined;
        } else {
          value = data.d;
        }
      }

      return value;
    },

    setItem: function(key, value, options) {
      value = JSON.stringify({d: value, o: options});
      // return $cookies.put(key, value, options);
      return localStorage.setItem(key, value);
    },

    removeItem: function(key, options) {
      // return $cookies.remove(key, options);
      return localStorage.removeItem(key);
    },

    clear: function() {
      return localStorage.clear();
    }
  };

  return service;
});
