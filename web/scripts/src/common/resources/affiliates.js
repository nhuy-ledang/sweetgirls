angular.module('resources.affiliates', [])

.factory('affiliates', function ($rootScope, $q, storageService, apiService) {
  var service = {
    create: function( params, loading) {
      var deferred = $q.defer();
      apiService.post('affiliates' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
    updateAffiliate: function( params, loading) {
      var deferred = $q.defer();
      apiService.post('affiliates/update' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
    getOverview: function(data, loading) {
      var deferred = $q.defer();
      apiService.get('affiliate_orders/overview' + (loading === true ? '?loadingSpinner' : ''), data).then(function(response) {
        deferred.resolve(response.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
    getOrders: function(data, loading) {
      var deferred = $q.defer();
      apiService.get('affiliate_orders' + (loading === true ? '?loadingSpinner' : ''), data).then(function(response) {
        deferred.resolve(response.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
    getWithdrawals: function(data, loading) {
      var deferred = $q.defer();
      apiService.get('aff_agent_withdrawals' + (loading === true ? '?loadingSpinner' : ''), data).then(function(response) {
        deferred.resolve(response.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
    createTicket: function(params, loading) {
      var deferred = $q.defer();
      apiService.post('aff_tickets' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
    repTicket: function(ticket_id, params, loading) {
      var deferred = $q.defer();
      apiService.post('aff_tickets/' + ticket_id + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
  };

  return service;
});
