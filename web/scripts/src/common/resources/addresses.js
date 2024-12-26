angular.module('resources.addresses', [])

.factory('addresses', function ($rootScope, $q, storageService, apiService) {
  var service = {
    getAddressAll: function (loading) {
      var deferred = $q.defer();
      apiService.get('addresses_all' + (loading === true ? '?loadingSpinner' : '')).then(function (response) {
        deferred.resolve(response.data.data.data);
      }, function (error) {
        deferred.reject(error.data.errors);
      });
      return deferred.promise;
    },

    createAddress: function (id, params, loading) {
      var deferred = $q.defer();
      apiService.post('addresses' + (loading === true ? '?loadingSpinner' : ''), params).then(function (response) {
        deferred.resolve(response.data.data.data);
      }, function (error) {
        deferred.reject(error.data.errors);
      });
      return deferred.promise;
    },

    updateAddress: function (id, params, loading) {
      var deferred = $q.defer();
      apiService.put('addresses/' + id + (loading === true ? '?loadingSpinner' : ''), params).then(function (response) {
        deferred.resolve(response.data.data.data);
      }, function (error) {
        deferred.reject(error.data.errors);
      });
      return deferred.promise;
    },
  };

  return service;
});
