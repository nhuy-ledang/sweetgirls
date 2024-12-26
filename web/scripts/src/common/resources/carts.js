angular.module('resources.carts', [])

.factory('carts', function($rootScope, $q, apiService) {
  // Set base url for resource (used as API endpoint)
  var resource = 'carts';

  var service = {
    /**
     * Get Carts
     * @param data
     * @param loading
     * @returns {*}
     */
    get: function(data, loading) {
      var deferred = $q.defer();
      apiService.get(resource + (loading === true ? '?loadingSpinner' : ''), {data: data}).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    // product_id', quantity
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

    remove: function(id, product_id, loading) {
      var deferred = $q.defer();
      apiService.remove(resource + '/' + id + (loading === true ? '?loadingSpinner' : '')).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    addProductByCoins: function(params, loading) {
      var deferred = $q.defer();
      apiService.post(resource + '_coins' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    addIncludeProduct: function(params, loading) {
      var deferred = $q.defer();
      apiService.post(resource + '_includes' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    removeProduct: function(product_id, loading) {
      var deferred = $q.defer();
      apiService.remove(resource + '_products/' + product_id + (loading === true ? '?loadingSpinner' : '')).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * Get Cart Totals
     * @param loading
     * @returns {*}
     */
    getCartTotals: function(loading) {
      var deferred = $q.defer();
      apiService.get(resource + '_totals' + (loading === true ? '?loadingSpinner' : '')).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * Add coupon
     * @param coupon
     * @param loading
     * @returns {*}
     */
    addCoupon: function(coupon, loading) {
      var deferred = $q.defer();
      apiService.post(resource + '_coupon' + (loading === true ? '?loadingSpinner' : ''), {coupon: coupon}).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * Clear coupon
     * @param loading
     * @returns {*}
     */
    clearCoupon: function(loading) {
      var deferred = $q.defer();
      apiService.remove(resource + '_coupon' + (loading === true ? '?loadingSpinner' : '')).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * Add voucher
     * @param voucher
     * @param loading
     * @returns {*}
     */
    addVoucher: function(voucher, loading) {
      var deferred = $q.defer();
      apiService.post(resource + '_voucher' + (loading === true ? '?loadingSpinner' : ''), {voucher: voucher}).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * Clear voucher
     * @param loading
     * @returns {*}
     */
    clearVoucher: function(loading) {
      var deferred = $q.defer();
      apiService.remove(resource + '_voucher' + (loading === true ? '?loadingSpinner' : '')).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    getShippingServices: function(params, loading) {
      var deferred = $q.defer();
      apiService.post('shipping_services' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    setShippingFee: function(params, loading) {
      var deferred = $q.defer();
      apiService.post('shipping_fee' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
  };

  return service;
});
