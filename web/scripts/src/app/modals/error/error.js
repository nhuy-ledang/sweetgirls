angular.module('app.modals.error', [])

.controller('ErrorCtrl', function ($scope, $uibModalInstance, message) {
  $scope.error = message;

  $scope.ok = function () {
    $uibModalInstance.dismiss('cancel');
  };

  $scope.cancel = function () {
    $uibModalInstance.dismiss('cancel');
  };
});
