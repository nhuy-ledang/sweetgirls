angular.module('app.pages.account-invite', [])

.controller('AccountInviteCtrl', function($scope, $rootScope, security, users) {
  var vm = this;
  console.log('AccountInviteCtrl');
  $scope.envData = {showValid: false, submitted: false};
  $scope.share_code = '';
  $scope.data = {
    loading: false,
    items: [],
    page: 1,
    pageSize: 1000,
    sort: 'id',
    order: 'desc',
  };

  $scope.createShareCode = function() {
    $scope.envData.submitted = true;
    security.createShareCode(true).then(function(res) {
      console.log(res);
      $scope.share_code = res;
      $scope.envData.submitted = false;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      $rootScope.openError(errors[0].errorMessage);
      $scope.envData.submitted = false;
    });
  };

  vm.getInviteHistory = function() {
    $scope.data.loading = true;
    users.getInviteHistory($scope.data).then(function(res) {
      console.log(res);
      $scope.data.loading = false;
      $scope.data.items = res.data;
    }, function(errors) {
      $scope.data.loading = false;
      console.log(errors);
    });
  };

  $scope.init = function() {
    $scope.inited = true;
    vm.getInviteHistory();
  };
});
