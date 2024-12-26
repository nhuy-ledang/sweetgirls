angular.module('security', [])

.factory('security', function($rootScope, $cookies, $q, $location, $window, apiService) {
  // Redirect to the given url (defaults to '/')
  function redirect(url) {
    url = url || '/';
    $location.path(url);
  }

  // The public API of the service
  var service = {
    tokenName: 'Authorization',

    // Holds the object of the current logged in user
    currentUser: undefined,

    setAuthToken: function(user) {
      // document.cookie = 'Authorization=' + user.access_token + ';path=/;';
      // document.cookie = 'Authorization=' + user.access_token + ';path=/;domain=.vietnammanufacturers.vn';
      // Set the authentication token in a cookie
      $cookies.put(service.tokenName, user.access_token);
      // Set the Request Header 'Authorization'
      apiService.setAuthTokenHeader(user.access_token);
    },

    // If successful, set current user
    setUserResponse: function(response) {
      service.currentUser = response.data.data.data;
      if(service.currentUser.access_token) {
        service.setAuthToken(service.currentUser);
      }
      return service.currentUser;
    },

    // Attempt to authenticate a user by the given username and password
    login: function(params) {
      var deferred = $q.defer();
      // Make request to API
      apiService.post('auth/login', params).then(function(response) {
        service.setUserResponse(response);
        deferred.resolve(service.currentUser);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    // Logout the current user and redirect
    logout: function(redirectTo) {
      if (arguments.length === 0) {
        redirectTo = '/login';
      }

      // Remove authToken and currentUser cookie
      $cookies.remove(service.tokenName);

      // To log out a user
      apiService.get('auth/logout');

      // Reset current user object
      service.currentUser = null;

      // Redirect to supplied route
      redirect(redirectTo);

      // Notif logout
      $rootScope.$broadcast('security:logout');
    },

    forgot: function(email) {
      var deferred = $q.defer();
      apiService.post('user/password', {email: email}).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    // Update current user
    setCurrent: function(user) {
      if (service.currentUser !== user) {
        service.currentUser = user;
        $rootScope.$broadcast('user:current:updated', service.currentUser);
      }
    },

    // Ask the backend to see if a user is already authenticated - this may be from a previous session.
    requestCurrentUser: function() {
      // Otherwise check if a session exists on the server
      var deferred = $q.defer(), authToken = $cookies.get(service.tokenName);
      if (authToken) {
        apiService.setAuthTokenHeader(authToken);
        if (!service.isAuthenticated()) {
          apiService.get('auth').then(function(response) {
            service.setUserResponse(response);
            deferred.resolve(service.currentUser);
          }, function(error) {
            // Remove authToken and currentUser cookie
            $cookies.remove(service.tokenName);

            deferred.resolve(false);
          });
        } else {
          deferred.resolve(service.currentUser);
        }
      } else {
        deferred.resolve(false);
      }
      return deferred.promise;
    },

    // Is the current user authenticated?
    isAuthenticated: function() {
      return !!service.currentUser;
    },

    // Update user info
    update: function(params, loading) {
      var deferred = $q.defer();
      apiService.post('auth/profile-change' + (loading === true ? '?loadingSpinner' : ''), params).then(function(response) {
        service.setUserResponse(response);
        deferred.resolve(service.currentUser);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },

    // Create Share Code
    createShareCode: function(loading) {
      var deferred = $q.defer();
      apiService.post('auth/create_share_code' + (loading === true ? '?loadingSpinner' : ''), {}).then(function(response) {
        deferred.resolve(response.data.data.data);
      }, function(error) {
        deferred.reject(error.data.errors);
      });

      return deferred.promise;
    },
  };

  return service;
});
