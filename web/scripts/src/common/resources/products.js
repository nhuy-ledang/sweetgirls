angular.module('resources.products', [])

.factory('products', function($rootScope, $q, apiService) {
  // Set base url for resource (used as API endpoint)
  var resource = 'products';

  var service = {
    /**
     * Get Products
     * @param pageSize
     * @param page
     * @param sort
     * @param order
     * @param data
     * @returns {*}
     */
    get: function(page, pageSize, sort, order, data) {
      var deferred = $q.defer();
      apiService.get(resource, {page: page, pageSize: pageSize, sort: sort, order: order, data: data}).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * Add Like Dislike
     * @param product_id
     * @param params
     * @param loading
     * @returns {*}
     */
    addLikeDislike: function(product_id, params, loading) {
      var deferred = $q.defer();
      apiService.post(resource + '/' + product_id + '/like' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
  };

  return service;
});
