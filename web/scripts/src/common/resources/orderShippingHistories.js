angular.module('resources.orderShippingHistories', [])

.factory('orderShippingHistories', function($rootScope, $q, apiService) {
  // Set base url for resource (used as API endpoint)
  var resource = 'ord_order_shipping_histories';

  var service = {
    get: function(data, loading) {
      var deferred = $q.defer();
      apiService.get(resource + (loading === true ? '?loadingSpinner' : ''), {data: data}).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
  };

  return service;
});
