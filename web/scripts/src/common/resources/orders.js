angular.module('resources.orders', [])

.factory('orders', function($rootScope, $q, apiService) {
  // Set base url for resource (used as API endpoint)
  var resource = 'orders';

  var service = {
    get: function(page, pageSize, sort, order, data) {
      var deferred = $q.defer();

      apiService.get(resource, {page: page, pageSize: pageSize, sort: sort, order: order, data: data}).then(function(response) {
        deferred.resolve(response.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    create: function(params, loading) {
      var deferred = $q.defer();
      apiService.post(resource + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    update: function(id, params, loading) {
      var deferred = $q.defer();
      apiService.put(resource + '/' + id + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    remove: function(id, loading) {
      var deferred = $q.defer();
      apiService.remove(resource + '/' + id + (loading === true ? '?loadingSpinner' : '')).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    createGuest: function(params, loading) {
      var deferred = $q.defer();
      apiService.post(resource + '/guest' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    cancelOrder: function(id, params, loading) {
      var deferred = $q.defer();
      apiService.put(resource + '/' + id + '/cancel' +(loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    }
  };

  return service;
});
