angular.module('theme.services', [])

.service('MTLPopup', (function() {
  function getFullUrlPath(location) {
    var isHttps = location.protocol === 'https:';
    var url = location.protocol + '//' + location.hostname + ':' + (location.port || (isHttps ? '443' : '80')) + (/^\//.test(location.pathname) ? location.pathname : '/' + location.pathname);
    return url;
  }
  function parseQueryString(str) {
    var obj = {};
    var key;
    var value;
    angular.forEach((str || '').split('&'), function (keyValue) {
      if (keyValue) {
        value = keyValue.split('=');
        key = decodeURIComponent(value[0]);
        obj[key] = angular.isDefined(value[1]) ? decodeURIComponent(value[1]) : true;
      }
    });
    return obj;
  }
  function Popup($interval, $window, $q) {
    this.$interval = $interval;
    this.$window = $window;
    this.$q = $q;
    this.popup = null;
  }
  Popup.prototype.stringifyOptions = function(options) {
    var parts = [];
    angular.forEach(options, function(value, key) {
      parts.push(key + '=' + value);
    });
    return parts.join(',');
  };
  Popup.prototype.open = function(url, name, popupOptions, redirectUri) {
    popupOptions = angular.extend({}, popupOptions);
    var width = popupOptions.width || 500;
    var height = popupOptions.height || 500;
    var options = this.stringifyOptions({
      width: width,
      height: height,
      top: this.$window.screenY + ((this.$window.outerHeight - height) / 2.5),
      left: this.$window.screenX + ((this.$window.outerWidth - width) / 2)
    });
    var popupName = this.$window['cordova'] || this.$window.navigator.userAgent.indexOf('CriOS') > -1 ? '_blank' : name;
    this.popup = this.$window.open(url, popupName, options);
    if(this.popup && this.popup.focus) {
      this.popup.focus();
    }
    var _this = this;
    return this.$q(function(resolve, reject) {
      var redirectUriParser = document.createElement('a');
      redirectUriParser.href = redirectUri;
      var redirectUriPath = getFullUrlPath(redirectUriParser);
      var polling = _this.$interval(function() {
        if(!_this.popup || _this.popup.closed || _this.popup.closed === undefined) {
          _this.$interval.cancel(polling);
          reject(new Error('The popup window was closed'));
        }
        try {
          var popupWindowPath = getFullUrlPath(_this.popup.location);
          if(popupWindowPath === redirectUriPath) {
            if(_this.popup.location.search || _this.popup.location.hash) {
              var query = parseQueryString(_this.popup.location.search.substring(1).replace(/\/$/, ''));
              var hash = parseQueryString(_this.popup.location.hash.substring(1).replace(/[\/$]/, ''));
              var params = angular.extend({}, query, hash);
              if(params.error) {
                reject(new Error(params.error));
              } else {
                resolve(params);
              }
            } else {
              reject(new Error('OAuth redirect has occurred but no query or hash parameters were found. ' +
                'They were either not set during the redirect, or were removed—typically by a ' +
                'routing library—before Satellizer could read it.'));
            }
            _this.$interval.cancel(polling);
            _this.popup.close();
          }
        }
        catch (error) {
        }
      }, 500);
    });
  };
  Popup.$inject = ['$interval', '$window', '$q'];
  return Popup;
}()))

.value('fileScripts', [])

.service('lazyLoad', function($q, fileScripts) {
  var deferred = $q.defer();
  this.load = function(files) {
    var promises = [];
    angular.forEach(files, function(file) {
      if (file.indexOf('.js') > -1 && fileScripts.indexOf(file) === -1) { // script
        promises.push(new Promise(function(resolve) {
          (function(d, script) {
            script = d.createElement('script');
            script.type = 'text/javascript';
            script.async = true;
            script.onload = function() {
              resolve('success');
            };
            script.onerror = function() {
              resolve('fail');
            };
            script.src = file;
            d.getElementsByTagName('head')[0].appendChild(script);

            fileScripts.push(file);
          }(document));
        }));
      }
    });
    if (promises.length) {
      Promise.all(promises).then(function(res) {
        deferred.resolve(res);
      });
    } else {
      deferred.resolve([]);
    }

    return deferred.promise;
  };
})

.filter('safe_html', function ($sce) {
  return function (val) {
    return $sce.trustAsHtml(val);
  };
})

.filter('trustedUrl', function($sce) {
  return function (b) {
    return b ? $sce.trustAsResourceUrl(b) : '';
  };
})

/*.filter('dateTimeJson', function () {
  return function (value) {
    if (!value) {
      return value;
    }

    return value;
    /!*var date = eval(('new ' + value).replace(/\//g, ''));
    var dd = date.getDate(), MM = date.getMonth() + 1, yy = date.getFullYear();
    // CountryId == 76 && LanguageId == 1 && (yy += 543);
    var HH = date.getHours(), mm = date.getMinutes();
    return mm < 10 ? HH + ':0' + mm + ' - ' + dd + '/' + MM + '/' + yy : HH + ':' + mm + ' - ' + dd + '/' + MM + '/' + yy;*!/
  };
})*/

.filter('firstLetter', function () {
  return function (input) {
    return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
  };
})

.filter('formatN', function () {
  return function (a, b) {
    return b *= 1, a *= 1, window.isNaN(a) ? '' : (window.isNaN(b) && (b = 0), a.format('n' + b));
  };
})

.filter('numberLg', function ($filter) {
  return function (b) {
    if (window.isNaN(b)) {
      return '';
    }
    var c = 0;
    return $filter('number')(b, c);
  };
})

.filter('numberPrice', function ($filter) {
  return function (b) {
    if (window.isNaN(b)) {
      return '';
    }
    var point = 0;
    if (typeof b === 'string') {
      b = parseFloat(b);
    }

    var unit = '';
    if (b >= 1000000000) {
      b = Math.floor(b / 1000000000);
      unit = 'Tỷ';
    } else if (b >= 100000000) {
      b = Math.floor(b / 100000000);
      unit = 'Tr';
    }
    var output = $filter('number')(b, point);
    return output.toString() + unit;
  };
})

.filter('formatNUnit', function () {
  return function (a, b, c) {
    return b *= 1, a *= 1, window.isNaN(a) ? '' : (window.isNaN(b) && (b = 0), a.format('n' + b) + c);
  };
})

.filter('formatNK', function () {
  return function (a, b) {
    var c, e = ['k', 'M', 'G', 'T', 'P', 'E'];
    return window.isNaN(a) ? '' : a < 1e3 ? a : (c = Math.floor(Math.log(a) / Math.log(1e3)), (a / Math.pow(1e3, c)).toFixed(b) + e[c - 1]);
  };
})

.filter('formatPoint', function () {
  return function (a) {
    return a *= 1, window.isNaN(a) ? '' : a >= 10 ? '10' : a > 0 ? a.format('n1') : '_._';
  };
})

.filter('formatDistance', function() {
  return function (a) {
    if (a === null || !a) {
      return '';
    }
    var c = a * 1;
    return (window.isNaN(c) || !c) ? '' : c.toFixed(1) + 'km';
  };
})

.filter('formatCategories', function () {
  return function (a) {
    if (!a || !_.isArray(a) || (_.isArray(a) && a.length === 0)) {
      return '';
    }
    var b = a[0].name;

    if(a.length > 1) {
      b += ', ' + a[1].name;
    }

    return b;
  };
});
