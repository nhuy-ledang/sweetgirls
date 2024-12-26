angular.module('app.pages.verify', [])

.controller('VerifyCtr', function($scope) {
  var vm = this;
  vm.now = new Date();

  $scope.PATTERNS = PATTERNS;

  $scope.envData = {
    showValid: false,
    submitted: false,
    step: 1
  };

  // Init Model
  $scope.params = {
    access_token: '',
    code: '',
  };

  $scope.submit = function(form) {
    console.log($scope.params);
    $scope.envData.showValid = true;
    if (form.$valid) {
      $scope.envData.submitted = true;
      /*users.registerVerify($scope.params).then(function(currentUser) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        console.log(currentUser);
        security.setUserResponse(currentUser);
        $state.go('loggedIn.dashboard');
      }, function(errors) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        vm.openError(errors[0].errorMessage);
      });*/
    }
  };
});
