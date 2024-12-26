angular.module('resources.reviews', [])

.factory('reviews', function($rootScope, $q, apiService) {
  var service = {

    /**
     * Get Messages
     * @param page
     * @param pageSize
     * @param sort
     * @param order
     * @param data
     * @param loading
     * @returns {*}
     */
    get: function(page, pageSize, sort, order, data, loading) {
      var deferred = $q.defer();
      apiService.get('user_reviews' + (loading === true ? '?loadingSpinner' : ''), {page: page, pageSize: pageSize, sort: sort, order: order, data: data}).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    create: function(product_id, params, loading) {
      var deferred = $q.defer();
      apiService.post('products/' + product_id + '/reviews' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    getReviews: function(params, loading) {
      var deferred = $q.defer();
      apiService.get('pd_review_all' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    createLike: function(review_id, params, loading) {
      var deferred = $q.defer();
      apiService.post('reviews/' + review_id + '/likes' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    getLikes: function(review_id, loading) {
      var deferred = $q.defer();
      apiService.get('reviews/' + review_id + '/likes').then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    createComment: function(review_id, params, loading) {
      var deferred = $q.defer();
      apiService.post('reviews/' + review_id + '/comments' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    getComments: function(review_id, loading) {
      var deferred = $q.defer();
      apiService.get('reviews/' + review_id + '/comments').then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    createLink: function(params, loading) {
      var deferred = $q.defer();
      apiService.post('pd_review_link' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    createShare: function(product_id) {
      var deferred = $q.defer();
      apiService.put('products/' + product_id + '/share').then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    }
  };

  return service;
});
