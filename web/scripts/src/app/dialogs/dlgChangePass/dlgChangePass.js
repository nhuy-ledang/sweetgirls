angular.module('app.dlgChangePass', [])

.controller('DlgChangePassCtrl', function($scope, $rootScope, $window, $uibModalInstance, users) {
  var vm = this;
  // vm.labels = angular.extend({}, $window['labels']);
  vm.labels = angular.extend({}, window.langtext);
  vm.settings = angular.extend({}, $window['settings']);
  $scope.labels = vm.labels;
  $scope.envData = {showValid: false, submitted: false};

  // Init Model
  $scope.params = {
    current_password: '',
    password: '',
    confirm_password: ''
  };

  $scope.close = function() {
    console.log('close');
    $uibModalInstance.dismiss('cancel');
  };

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    console.log($scope.params);
    if(form.$valid) {
      $scope.envData.submitted = true;
      users.changePassword($scope.params).then(function(res) {
        console.log(res);
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.openAlert({summary: vm.labels.success_change_pass ? vm.labels.success_change_pass : 'Đổi mật mật khẩu thành công!', timeout: 5000}).result.then(function() {
          // location = vm.settings.loginUrl;
        }, function() {
          // location = vm.settings.loginUrl;
        });
        $uibModalInstance.close(res);
      }, function(errors) {
        $rootScope.openError(errors[0].errorMessage);
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
      });
    }
  };
})

.factory('dlgChangePass', function($uibModal, helper) {
  return {
    show: function(fn) {
      return $uibModal.open({
        animation: true,
        backdrop: 'static',
        templateUrl: 'dialogs/dlgChangePass/dlgChangePass.tpl.html',
        controller: 'DlgChangePassCtrl',
        controllerAs: 'vm',
        size: 'sm modal-dialog-centered',
      }).result.then(function(res) {
        helper.runFnc(fn, res);
      }, function(error) {
        console.log(error);
      });
    }
  };
});
