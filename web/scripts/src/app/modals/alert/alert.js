angular.module('app.modals.alert', [])

.controller('AlertCtrl', function($scope, $timeout, $uibModalInstance, data) {
  $scope.data = angular.extend({}, data);

  $scope.ok = function(url) {
    if (url) {
      location = url;
    }
    $uibModalInstance.close(true);
  };

  $scope.cancel = function() {
    $uibModalInstance.close(false);
  };

  if($scope.data.timeout) {
    $timeout(function() {
      $scope.cancel();
    }, parseInt($scope.data.timeout));
  }
});
