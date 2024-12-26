angular.module('directive', [])

.directive('imgDef', function() {
  return {
    restrict: 'A',
    link: function(scope, el, attr) {
      var def = attr.imgDef ? attr.imgDef : 'image/l640x400.png';
      var src = el.attr('src') || attr.ngSrc;

      function setError(el, src, def) {
        var img = document.createElement('img');
        img.src = src;
        img.onload = function() {
        };
        img.onerror = function() {
          if (src && src.indexOf('/thumb/') > -1) { // Try with raw url
            setError(el, src.replace('/thumb/', '/'), def);
          } else {
            el.attr('src', def);
          }
        };
      }

      setError(el, src, def);
    }
  };
})

.directive('goToElement', function($window) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {
      element.click(function() {
        if (attrs.goToElement && $(attrs.goToElement)) {
          var top = $(attrs.goToElement).offset().top;
          $($window).scrollTop(top);
        }
      });
    }
  };
})

.directive('keyEnter', function() {
  return function(scope, element, attrs) {
    element.bind("keydown keypress", function(event) {
      if (event.which === 13) {
        scope.$apply(function() {
          scope.$eval(attrs.keyEnter);
        });

        event.preventDefault();
      }
    });
  };
})

.directive('numbersOnly', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, ngModelCtrl) {
      function fromUser(text) {
        if (text) {
          var transformedInput = text.replace(/[^0-9-]/g, '');
          if (transformedInput !== text) {
            ngModelCtrl.$setViewValue(transformedInput);
            ngModelCtrl.$render();
          }
          return transformedInput;
        }
        return undefined;
      }

      ngModelCtrl.$parsers.push(fromUser);
    }
  };
})

.directive('mainNav', function($window) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {
      var mobileBreakpoint = 1012;
      $window['isDesktop'] = $window.innerWidth >= mobileBreakpoint;
      element.find('li.has-dropdown').not('.nav__item-search').each(function() {
        var ele = $(this);
        var $dropdown = $('> .nav__link', ele);
        $dropdown.hover(function(e) {
          e.preventDefault();
          if ($window['isDesktop']) {
            ele.addClass('is-open');
          }
        }, function(e) {
          e.preventDefault();
          /*if ($window['isDesktop']) {
            ele.removeClass('is-open');
          }*/
        });
        ele.hover(function(e) {

        }, function(e) {
          e.preventDefault();
          if ($window['isDesktop']) {
            ele.removeClass('is-open');
          }
        });
        $dropdown.click(function(e) {
          // if (e.target.tagName == 'I') {
          e.preventDefault();
          element.find('li.has-dropdown.is-open').each(function() {
            if ($(this).attr('id') !== ele.attr('id')) {
              $(this).removeClass('is-open');
            }
          });
          if (!$window['isDesktop']) {
            if (ele.hasClass('is-open')) {
              ele.removeClass('is-open');
            } else {
              ele.addClass('is-open');
            }
          }
          // }
        });
      });

      function init() {
        $window['isDesktop'] = $window.innerWidth >= mobileBreakpoint;
      }

      init();
      $($window).on('resize', function() {
        init();
      });
    }
  };
})

.directive('openNav', function($window) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {
      var mobileBreakpoint = 1012;
      var openNav = $(attrs.openNav);

      function init() {
        /*var innerWidth = $window.innerWidth;
        if (innerWidth >= mobileBreakpoint) {
          element.removeClass('is-open');
          openNav.removeClass('is-open');
        }*/
        $('[open-nav]').removeClass('is-open');
        $('.navbar__menu').removeClass('is-open');
      }

      init();
      element.click(function() {
        var isOpen = element.hasClass('is-open');

        $('[open-nav]').removeClass('is-open');
        $('.navbar__menu').removeClass('is-open');

        if (isOpen) {
          element.removeClass('is-open');
          openNav.removeClass('is-open');
        } else {
          element.addClass('is-open');
          openNav.addClass('is-open');
        }
      });
      $($window).on('resize', function() {
        init();
      });
    }
  };
})

.filter('formatTime', function($filter) {
  return function(timeString, format) {
    var parts = timeString.split(':');
    if (parts.length === 3) {
      var date = new Date(0, 0, 0, parts[0], parts[1], parts[2]);
      return $filter('date')(date, format || 'hh:mm');
    } else {
      return timeString;
    }
  };
})

.filter('formatTimeShort', function() {
  return function(timeString) {
    var parts = timeString.split(':');
    if (parts.length >= 2) {
      return parts[0] + 'h' + (parseInt(parts[1]) === 0 ? '' : parts[1]);
    } else {
      return timeString;
    }
  };
})

.filter('formatDate', function($rootScope) {
  return function(date, format) {
    if (date && date !== '0000-00-00') {
      return new Date(date).format(format ? format : 'shortDate', undefined, $rootScope.settings.locale);
    } else {
      return date;
    }
  };
})

.filter('formatCurrency', function($filter) {
  return function(value) {
    if (value) {
      return $filter('currency')(value, '', 0);
    } else {
      return value;
    }
  };
});
