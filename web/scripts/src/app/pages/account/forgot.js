angular.module('app.pages.forgot', [])

.controller('ForgotCtr', function($scope, $rootScope, $window, users) {
  var vm = this;
  vm.user = null;
  $scope.labels = $window['labels'];
  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false};

  // Init Model
  $scope.params = {email: ''};

  $scope.submit = function(form) {
    console.log($scope.params);
    $scope.envData.showValid = true;
    if(form.$valid) {
      $scope.envData.submitted = true;
      users.forgot($scope.params.email).then(function() {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.openAlert({summary: 'Lấy lại mật khẩu thành công. Xin kiểm tra email để đổi mật khẩu!'});
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
