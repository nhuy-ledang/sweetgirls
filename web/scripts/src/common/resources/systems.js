angular.module('resources.systems', [])

.factory('systems', function($rootScope, $q, apiService) {
  var service = {
    /**
     * Get Banks
     * @param loading
     * @returns {*}
     */
    getBanks: function(loading) {
      var deferred = $q.defer();
      apiService.get('core_banks' + (loading === true ? '?loadingSpinner' : '')).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
  };

  return service;
});
