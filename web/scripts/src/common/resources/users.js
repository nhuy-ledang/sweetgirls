angular.module('resources.users', [])

.factory('users', function($rootScope, $q, storageService, apiService) {
  var service = {
    /**
     * Check Email
     * @param email
     * @param loading
     * @returns {*}
     */
    /*checkEmail: function(email, loading) {
      var deferred = $q.defer();
      apiService.post('auth/email-check' + (loading === true ? '?loadingSpinner' : ''), {email: email}).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },*/

    /**
     * Register Check Phone OTP
     * @param params
     * @param loading
     * @returns {*}
     */
    /*registerCheckPhoneOTP: function(params, loading) {
      var deferred = $q.defer();
      apiService.post('auth/register-phoneotp' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },*/

    /**
     * Register
     * @param params
     * @param loading
     * @returns {*}
     */
    register: function(params, loading) {
      var deferred = $q.defer();
      apiService.post('auth/register' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * Forgot
     * @param email
     * @param loading
     * @returns {*}
     */
    forgot: function(email, loading) {
      var deferred = $q.defer();
      apiService.post('auth/forgot' + (loading === true ? '?loadingSpinner' : ''), {email: email}).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * ForgotNewPw {email,password,code}
     * @param params
     * @param loading
     * @returns {*}
     */
    forgotNewPw: function(params, loading) {
      var deferred = $q.defer();
      apiService.post('auth/forgot-newpw' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * Change password {current_password,password}
     * @param params
     * @param loading
     * @returns {*}
     */
    changePassword: function(params, loading) {
      var deferred = $q.defer();
      apiService.post('auth/pw-change' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * Get orders
     * @param data: {paging: paging, page: page, pageSize: pageSize, sort: sort, order: order, data: data}
     * @param loading
     * @returns {*}
     */
    getOrders: function(data, loading) {
      var deferred = $q.defer();
      apiService.get('user_orders' + (loading === true ? '?loadingSpinner' : ''), data).then(function(response) {
        deferred.resolve(response.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * Get coins history
     * @param data: {paging: paging, page: page, pageSize: pageSize, sort: sort, order: order, data: data}
     * @param loading
     * @returns {*}
     */
    getCoinsHistory: function(data, loading) {
      var deferred = $q.defer();
      apiService.get('user_coins' + (loading === true ? '?loadingSpinner' : ''), data).then(function(response) {
        deferred.resolve(response.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * Get invite history
     * @param data: {paging: paging, page: page, pageSize: pageSize, sort: sort, order: order, data: data}
     * @param loading
     * @returns {*}
     */
    getInviteHistory: function(data, loading) {
      var deferred = $q.defer();
      apiService.get('auth/get_invite_history' + (loading === true ? '?loadingSpinner' : ''), data).then(function(response) {
        deferred.resolve(response.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    /**
     * Get Calendars
     * @param data: {paging: paging, page: page, pageSize: pageSize, sort: sort, order: order, data: data}
     * @param loading
     * @returns {*}
     */
    /*getCalendars: function(data, loading) {
      var deferred = $q.defer();
      apiService.get('user_calendars' + (loading === true ? '?loadingSpinner' : ''), data).then(function(response) {
        deferred.resolve(response.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },*/

    /**
     * Create wheel
     * @param params
     * @param loading
     * @returns {*}
     */
    createWheel: function(params, loading) {
      var deferred = $q.defer();
      apiService.post('wheel_users' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
  };

  return service;
});
