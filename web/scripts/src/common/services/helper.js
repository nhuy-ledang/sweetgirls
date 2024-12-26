angular.module('services.helper', [])

.factory('helper', function() {
  var service = {
    runFnc: function(a, b) {
      if (typeof a === 'function') {
        a(b);
      }
    },
    getParamRnd: function(a) {
      var b = (new Date()).getTime(), c = (a ? a.indexOf('?') > -1 ? '&' : '?' : '') + 't=' + b;
      return c;
    },
    log: function(a, b) {
      console.log(a);
    },
    getUrlParams: function(a) {
      var b = '';
      for (var c in a) {
        if (c) {
          if (a[c] !== undefined && a[c] !== null) {
            b += (b !== '' ? '&' : '') + c + '=' + a[c];
          }
        }
      }
      return b;
    },
    fromNow: function(date) {
      var seconds = Math.floor((new Date() - new Date(date)) / 1000);
      var interval = Math.floor(seconds / 31536000);
      if (interval > 1) {
        return interval + " năm";
      }
      interval = Math.floor(seconds / 2592000);
      if (interval > 1) {
        return interval + " tháng";
      }
      interval = Math.floor(seconds / 86400);
      if (interval > 1) {
        return interval + " ngày";
      }
      interval = Math.floor(seconds / 3600);
      if (interval > 1) {
        return interval + " giờ";
      }
      interval = Math.floor(seconds / 60);
      if (interval > 1) {
        return interval + " phút";
      }
      return Math.floor(seconds) + " giây";
    }
  };

  return service;
});
