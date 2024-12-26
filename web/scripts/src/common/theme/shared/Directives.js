angular.module('theme.directives', [])

.directive("passwordVerify", function () {
  return {
    require: "ngModel",
    scope: {
      passwordVerify: '='
    },
    link: function (scope, element, attrs, ctrl) {
      scope.$watch(function () {
        var combined;

        if (scope.passwordVerify || ctrl.$viewValue) {
          combined = scope.passwordVerify + '_' + ctrl.$viewValue;
        }
        return combined;
      }, function (value) {
        //if(value) {
        //    ctrl.$parsers.unshift(function(viewValue) {
        //        var origin = scope.passwordVerify;
        //        if(origin !== viewValue) {
        //            ctrl.$setValidity("passwordVerify", false);
        //            return undefined;
        //        } else {
        //            ctrl.$setValidity("passwordVerify", true);
        //            return viewValue;
        //        }
        //    });
        //}
        if (value) {
          if (scope.passwordVerify !== ctrl.$viewValue) {
            ctrl.$setValidity("passwordVerify", false);
            return undefined;
          } else {
            ctrl.$setValidity("passwordVerify", true);
            return ctrl.$viewValue;
          }
        } else {
          ctrl.$setValidity("passwordVerify", true);
          return undefined;
        }
      });
    }
  };
})

// Compare password (fixed from passwordVerify to better )
.directive('ngCompare', function () {
  return {
    require: 'ngModel',
    link: function (scope, currentEl, attrs, ctrl) {
      var comparefield = document.getElementsByName(attrs.ngCompare)[0]; //getting first element
      compareEl = angular.element(comparefield);

      //current field key up
      currentEl.on('keyup', function () {
        if (compareEl.val() !== "") {
          var isMatch = currentEl.val() === compareEl.val();
          ctrl.$setValidity('compare', isMatch);
          scope.$digest();
        }
      });

      //Element to compare field key up
      compareEl.on('keyup', function () {
        if (currentEl.val() !== "") {
          var isMatch = currentEl.val() === compareEl.val();
          ctrl.$setValidity('compare', isMatch);
          scope.$digest();
        }
      });
    }
  };
})

.directive('passwordEqual', function() {
  return {
    require: 'ngModel',
    scope: {
      passwordEqual: '='
    },
    link: function($scope, $element, $attr, $ctrl) {
      $ctrl.$equals = $scope.passwordEqual;

      var validate = function(viewValue) {
        var isValid = $scope.passwordEqual === viewValue;

        if (isValid === false) {
          $ctrl.$setValidity('passwordEqual', false);
          return undefined;
        } else {
          $ctrl.$setValidity('passwordEqual', true);
          return viewValue;
        }
      };

      // Watch own value and re-validate on change
      $scope.$watch(function() {
        return $ctrl.$viewValue;
      }, function(viewValue) {
        if (viewValue) {
          return validate(viewValue);
        }
      });
    }
  };
})

.directive('backToTop', function () {
  return {
    restrict: 'AE',
    link: function (scope, element, attr) {
      element.click(function (e) {
        $('body').scrollTop(0);
      });
    }
  };
})

.directive('icheck', function($timeout) {
  return {
    require: '?ngModel',
    link: function($scope, element, $attrs, ngModel) {
      return $timeout(function() {
        var parentLabel = element.parent('label');
        if (parentLabel.length) {
          parentLabel.addClass('icheck-label');
        }
        var value;
        value = $attrs['value'];

        $scope.$watch($attrs['ngModel'], function(newValue) {
          $(element).iCheck('update');
        });

        return $(element).iCheck({
          checkboxClass: 'icheckbox_minimal',
          radioClass: 'iradio_minimal'
        }).on('ifChanged', function(event) {
          if ($(element).attr('type') === 'checkbox' && $attrs['ngModel']) {
            $scope.$apply(function() {
              return ngModel.$setViewValue(event.target.checked);
            });
          }
          if ($(element).attr('type') === 'radio' && $attrs['ngModel']) {
            return $scope.$apply(function() {
              return ngModel.$setViewValue(value);
            });
          }
        });
      });
    }
  };
});
