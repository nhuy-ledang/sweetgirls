angular.module('resources.vouchers', [])

.factory('vouchers', function($rootScope, $q, storageService, apiService) {
  // Set base url for resource (used as API endpoint)
  var resource = 'vouchers';

  var service = {
    getAll: function(loading) {
      var deferred = $q.defer();
      var data = false; //storageService.getItem('tf_vouchers');
      if (data) {
        deferred.resolve(data);
      } else {
        apiService.get(resource + (loading === true ? '?loadingSpinner' : '')).then(function(response) {
          // storageService.setItem('tf_vouchers', response.data.data, {expires: apiService.getExpires(1)});
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