angular.module('app.pages.password-change', [])

.controller('PasswordChangeCtr', function($scope, $rootScope, $window, users) {
  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false};

  // Init Model
  $scope.params = {
    email: $rootScope.currentUser.email,
    current_password: '',
    password: '',
    confirm_password: '',
  };

  $scope.submit = function(form) {
    console.log($scope.params);
    $scope.envData.showValid = true;
    if(form.$valid) {
      $scope.envData.submitted = true;
      users.changePassword($scope.params).then(function() {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $scope.params.current_password = '';
        $scope.params.password = '';
        $scope.params.confirm_password = '';
        $rootScope.openAlert({summary: 'Đổi mật khẩu thành công!', timeout: 3000});
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
