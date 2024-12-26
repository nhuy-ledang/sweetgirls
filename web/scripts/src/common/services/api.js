angular.module('services.api', ['restangular'])

.factory('apiService', function(Restangular, $rootScope, $window) {
  var vm = this;
  vm.settings = angular.extend({}, $window['settings']);
  var tz = new Date().getTimezoneOffset();
  var defaultHeaders = {};
  var defaultRequestParams = {ver: Math.random(), tz: tz, locale: vm.settings.locale};

  // Global configuration for Restangular API connection.
  Restangular.setBaseUrl('/api/v1');
  Restangular.setFullResponse(false);
  Restangular.setDefaultHttpFields({withCredentials: false, cache: true, timeout: 20000});
  Restangular.setDefaultHeaders(defaultHeaders);

  Restangular.addResponseInterceptor(function(response, operation) {
    $rootScope.$broadcast('API:loading:ended');

    var responseArr = [];
    responseArr['data'] = response;

    return responseArr;
  });

  Restangular.addRequestInterceptor(function(element, operation, what, url) {
    // Not cache
    defaultRequestParams.ver = operation === 'get' ? Math.random() : undefined;
    Restangular.setDefaultRequestParams(defaultRequestParams);
    var data = {element: element, operation: operation, what: what, url: url};
    if (what.indexOf('loadingSpinner') !== -1) {
      $rootScope.$broadcast('API:loading:started', data);
    }
  });

  Restangular.setErrorInterceptor(function(response, deferred, responseHandler) {
    $rootScope.$broadcast('API:loading:ended');
    console.log('Response received with HTTP error code: ' + response.status);
    return true; // error not handled
  });

  var service = {
    authToken: undefined,

    getExpires: function(day, hour) {
      day = day || 0;
      hour = hour || 0;
      hour = day * 24 + hour;
      hour = hour || 24;

      return new Date((new Date()).getTime() + hour * 3600 * 1000);
    },

    all: function(resource, queryParams) {
      if (queryParams === undefined) {
        return Restangular.all(resource).getList();
      } else {
        return Restangular.all(resource).getList(queryParams);
      }
    },

    find: function(resource, id) {
      return Restangular.one(resource, id).get();
    },

    postFormData: function(resource, formData) {
      return Restangular.all(resource).withHttpConfig({transformRequest: angular.identity}).post(formData, undefined, {'Content-Type': undefined});
    },

    post: function(resource, elementToPost, headers) {
      if (elementToPost instanceof FormData) {
        return service.postFormData(resource, elementToPost);
      } else {
        return Restangular.all(resource).post(elementToPost, headers);
      }
    },

    download: function(resource, subElement, queryParams) {
      if (queryParams === undefined) {
        queryParams = subElement;
        return Restangular.withConfig(function(Config) {
          Config.setFullResponse(true);
        }).one(resource).withHttpConfig({responseType: 'arraybuffer'}).get(queryParams);
      } else {
        return Restangular.withConfig(function(Config) {
          Config.setFullResponse(true);
        }).all(resource).withHttpConfig({responseType: 'arraybuffer'}).customGET(subElement, queryParams);
      }
    },

    get: function(resource, subElement, queryParams) {
      if (queryParams === undefined) {
        queryParams = subElement;
        if (queryParams && !_.isObject(queryParams['data'])) {
          queryParams['data'] = encodeURIComponent(JSON.stringify(queryParams['data']));
        }
        return Restangular.one(resource).get(queryParams);
      } else {
        if (queryParams && !_.isObject(queryParams['data'])) {
          queryParams['data'] = encodeURIComponent(JSON.stringify(queryParams['data']));
        }
        return Restangular.all(resource).customGET(subElement, queryParams);
      }
    },

    put: function(resource, elementToPost, subElement, queryParams, headers) {
      if (elementToPost instanceof FormData) {
        return service.postFormData(resource, elementToPost);
      } else {
        return Restangular.one(resource).customPUT(elementToPost, subElement, queryParams, headers);
      }
    },

    patch: function(resource, object, queryParams, headers) {
      return Restangular.one(resource).patch(object, queryParams, headers);
    },

    remove: function(resource, subElement, queryParams, headers) {
      return Restangular.one(resource, subElement).remove(queryParams, headers);
    },

    postVia: function(baseUrl, resource, elementToPost, headers) {
      if (typeof elementToPost === 'string') {
        return Restangular.withConfig(function(config) {
          config.setBaseUrl(baseUrl);
        }).all(resource).withHttpConfig({transformRequest: angular.identity}).post(elementToPost, undefined, {'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'});
      } else if (elementToPost instanceof FormData) {
        return Restangular.withConfig(function(config) {
          config.setBaseUrl(baseUrl);
        }).all(resource).withHttpConfig({transformRequest: angular.identity}).post(elementToPost, undefined, {'Content-Type': undefined});
      } else {
        return Restangular.withConfig(function(config) {
          config.setBaseUrl(baseUrl);
        }).all(resource).post(elementToPost, headers);
      }
    },

    getVia: function(baseUrl, resource, subElement, queryParams) {
      if (queryParams === undefined) {
        queryParams = subElement;
        if (queryParams && queryParams['data'] && !_.isObject(queryParams['data'])) {
          queryParams['data'] = encodeURIComponent(JSON.stringify(queryParams['data']));
        }
        return Restangular.withConfig(function(config) {
          config.setBaseUrl(baseUrl);
        }).one(resource).get(queryParams);
      } else {
        if (queryParams && queryParams['data'] && !_.isObject(queryParams['data'])) {
          queryParams['data'] = encodeURIComponent(JSON.stringify(queryParams['data']));
        }
        return Restangular.withConfig(function(config) {
          config.setBaseUrl(baseUrl);
        }).all(resource).customGET(subElement, queryParams);
      }
    },

    setAuthTokenHeader: function(authToken) {
      service.authToken = authToken;
      defaultHeaders.Authorization = service.authToken;
      Restangular.setDefaultHeaders(defaultHeaders);
    },
  };

  return service;
});
