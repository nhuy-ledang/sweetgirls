angular.module('app.modules.header', [])

.controller('HeaderCtrl', function($scope, $window, $timeout) {
  $scope.isOpen = false;

  $scope.open = function() {
    $scope.isOpen = !$scope.isOpen;
    if ($scope.isOpen) {
      $timeout(function() {
        var inputElement = document.getElementById('input_search_form');
        if (inputElement) {
          inputElement.focus();
        }
      });
    }
  };
  $scope.outside = function() {
    $scope.isOpen = false;
  };

  // Search
  $scope.filterLang = angular.extend({lang: []}, $window['filterLang']);
  $scope.params = {
    q: '',
  };

  $scope.search = function() {
    console.log('search fn');
    if ($scope.params.q) {
      location.href = $scope.filterLang.lang + '/product/search?q=' + $scope.params.q;
    }
  };
});
