angular.module('app.modals.warning', [])

.controller('WarningCtrl', function ($scope, $uibModalInstance, message) {
  $scope.title = '';
  $scope.message = '';

  if (typeof message === 'string') {
    $scope.title = 'Cảnh báo!';
    $scope.message = message;
  } else if (typeof message === 'object') {
    $scope.title = message.title;
    $scope.message = message.content;
  }

  $scope.ok = function () {
    $uibModalInstance.close(true);
  };

  $scope.cancel = function () {
    $uibModalInstance.close(false);
  };
});
