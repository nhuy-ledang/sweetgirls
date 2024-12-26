angular.module('app.dlgLogin', [])

.controller('DlgLoginCtrl', function($scope, $rootScope, $uibModalInstance, $window, $socialConfig, security, MTLPopup, apiService, users, utils) {
  var vm = this;
  vm.labels = angular.extend({}, window.langtext);
  $scope.labels = vm.labels;
  $scope.listMode = {
    login : 'login',
    forgot: 'forgot',
    register: 'register',
  };
  $scope.envData = {showValid: false, submitted: false, mode: $scope.listMode.login};
  
  $scope.loginParams = {email: '', password: '', remember: true};
  $scope.forgotParams = {email: ''};
  $scope.registerParams = {first_name: '', email: '', password: '', confirm_password: ''};
  $scope.passType = 'password';

  $scope.login = function(isValid) {
    $scope.envData.showValid = true;
    if(isValid) {
      $scope.envData.submitted = true;
      security.login($scope.loginParams).then(function(res) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $scope.close();
        $window.location.reload();
      }, function(errors) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.openError(errors[0].errorMessage);
      });
    }
  };

  $scope.submitForgot = function(form) {
    $scope.envData.showValid = true;
    if(form.$valid) {
      $scope.envData.submitted = true;
      users.forgot($scope.forgotParams.email).then(function() {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.openAlert({summary: 'Lấy lại mật khẩu thành công. Xin kiểm tra email để đổi mật khẩu!'});
        $scope.envData.mode = $scope.listMode.login;
      }, function(errors) {
        $rootScope.openError(errors[0].errorMessage);
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
      });
    }
  };

  $scope.submitRegister = function(form) {
    console.log($scope.registerParams);
    console.log('form.$valid', form.$valid);
    
    $scope.envData.showValid = true;
    if (form.$valid) {
      var params = angular.copy($scope.registerParams);
      $scope.envData.submitted = true;
      users.register(utils.toFormData(params), true).then(function(res) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        security.setAuthToken(res);
        console.log(res);
        $scope.close();
        $window.location.reload();
      }, function(errors) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.openError(errors[0].errorMessage);
      });
    }
  };

  $scope.authenticate = function(provider) {
    var config = $socialConfig[provider];
    if (config) {
      $scope.submitted = true;
      MTLPopup.open(config.authorizationEndpoint + '?' + $.param(config.urlParams), config.name, config.popupOptions, config.urlParams.redirect_uri).then(function(params) {
        console.log(params);
        apiService.post(config.url + '?loadingSpinner', _.extend(params, {clientId: config.urlParams.client_id, redirectUri: config.urlParams.redirect_uri})).then(function(res) {
          var currentUser = security.setUserResponse(res);
          console.log(currentUser);
          $scope.submitted = false;
          $window.location.reload();
        }, function(error) {
          console.log(error.data);
        });
      }).catch(function(error) {
        console.log(error);
      });
    }
  };

  $scope.changeMode = function(mode) {
    $scope.envData.mode = mode;
  };

  $scope.changePassType = function() {
    $scope.passType = $scope.passType === 'password' ? 'text' : 'password';
  };

  $scope.close = function() {
    $uibModalInstance.dismiss('cancel');
  };

})

.factory('dlgLogin', function($uibModal, helper) {
  return {
    show: function(fn) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'dialogs/dlgLogin/dlgLogin.tpl.html',
        controller: 'DlgLoginCtrl',
        size: 'md custom-modal modal-login',
      }).result.then(function(res) {
        helper.runFnc(fn, res);
      }, function(error) {
        console.log(error);
      });
    }
  };
});
