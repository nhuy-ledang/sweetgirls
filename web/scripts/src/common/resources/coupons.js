angular.module('resources.coupons', [])

.factory('coupons', function($rootScope, $q, storageService, apiService) {
  // Set base url for resource (used as API endpoint)
  var resource = 'coupons';

  var service = {
    getAll: function(loading) {
      var deferred = $q.defer();
      var data = false; //storageService.getItem('tf_coupons');
      if (data) {
        deferred.resolve(data);
      } else {
        apiService.get(resource + (loading === true ? '?loadingSpinner' : '')).then(function(response) {
          storageService.setItem('tf_coupons', response.data.data, {expires: apiService.getExpires(1)});
          deferred.resolve(response.data.data);
        }, function(error) {
          deferred.reject(error.data.errors);
        });
      }

      return deferred.promise;
    },
  };

  return service;
});
