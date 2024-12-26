angular.module('app.pages.password-recover', [])

.controller('PasswordRecoverCtr', function($scope, $rootScope, $window, $location, users) {
  var vm = this;
  vm.settings = $window['settings'];
  vm.stateParams = $window['stateParams'];
  $scope.labels = $window['labels'];
  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false};
  $scope.params = {
    email: vm.stateParams.email,
    code: vm.stateParams.code,
    password: '',
    confirm_password: ''
  };

  console.log(vm.stateParams);

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    console.log($scope.params);
    if(form.$valid) {
      $scope.envData.submitted = true;
      users.forgotNewPw($scope.params).then(function(res) {
        console.log(res);
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.openAlert({summary: 'Đặt lại mật khẩu thành công!', timeout: 5000}).result.then(function() {
          location = vm.settings.loginUrl;
        }, function() {
          location = vm.settings.loginUrl;
        });
      }, function(errors) {
        $rootScope.openError(errors[0].errorMessage);
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
      });
    }
  };

  $scope.init = function() {
    $scope.inited = true;
  };
});
