angular.module('resources', [
  'resources.users',
  'resources.systems',
  'resources.addresses',
  'resources.locations',
  'resources.affiliates',
  'resources.reviews',
  'resources.products',
  'resources.coupons',
  'resources.vouchers',
  'resources.discounts',
  'resources.carts',
  'resources.orders',
  'resources.orderShippingHistories',
])

.factory('resources', function($rootScope, $q, apiService) {
  var service = {
    addSubscription: function(email) {
      var deferred = $q.defer();

      apiService.post('api/subscription', {email: email}).then(function(response) {
        deferred.resolve(response.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    addContact: function(params) {
      var deferred = $q.defer();

      apiService.post('api/contact', params).then(function(response) {
        deferred.resolve(response.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    }
  };

  return service;
});
