angular.module('app.pages.register', [])

.controller('RegisterCtr', function($scope, $rootScope, $window, utils, helper, users, $socialConfig, security, MTLPopup, apiService) {
  var vm = this;
  vm.labels = angular.extend({}, $window['labels']);
  vm.settings = angular.extend({}, $window['settings']);
  $scope.labels = vm.labels;
  $scope.PATTERNS = PATTERNS;
  $scope.inited = false;
  $scope.envData = {showValid: false, submitted: false};

  // Init Model
  $scope.params = {
    first_name: '',
    email: '',
    // phone_number: '',
    // code: '',
    password: '',
    confirm_password: '',
  };

  /*$scope.verifyPhoneNumber = function() {
    users.registerCheckPhoneOTP(angular.copy($scope.params)).then(function(res) {
      console.log(res);
      if (res && res.verify_code) {
        $scope.params.code = res.verify_code;
      }
      $rootScope.openAlert({summary: 'Bạn đã gởi thành công!', timeout: 5000});
    }, function(errors) {
      $rootScope.openError(errors[0].errorMessage);
    });
  };*/

  $scope.onSubmit = function(form) {
    console.log($scope.params);
    $scope.envData.showValid = true;
    if (form.$valid) {
      var params = angular.copy($scope.params);
      $scope.envData.submitted = true;
      users.register(utils.toFormData(params), true).then(function(res) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        security.setAuthToken(res);
        console.log(res);
        $window.location = vm.settings.returnUrl ? vm.settings.returnUrl : vm.settings.APP_URL;
      }, function(errors) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.openError(errors[0].errorMessage);
      });
    }
  };

  // Same as login
  $scope.authenticate = function(provider) {
    /*$auth.authenticate(provider).then(function(res) {
      var currentUser = security.setUserResponse(res);
      console.log(currentUser);
      $scope.submitted = false;
      $window.location = vm.settings.returnUrl ? vm.settings.returnUrl : vm.settings.APP_URL;
    }).catch(function(errors) {
      $rootScope.openError(errors[0].errorMessage);
      $scope.submitted = false;
    });*/
    var config = $socialConfig[provider];
    if (config) {
      $scope.submitted = true;
      MTLPopup.open(config.authorizationEndpoint + '?' + $.param(config.urlParams), config.name, config.popupOptions, config.urlParams.redirect_uri).then(function(params) {
        console.log(params);
        apiService.post(config.url + '?loadingSpinner', _.extend(params, {clientId: config.urlParams.client_id, redirectUri: config.urlParams.redirect_uri})).then(function(res) {
          var currentUser = security.setUserResponse(res);
          console.log(currentUser);
          $scope.submitted = false;
          $window.location = vm.settings.returnUrl ? vm.settings.returnUrl : vm.settings.APP_URL;
        }, function(error) {
          console.log(error.data);
        });
      }).catch(function(error) {
        console.log(error);
      });
    }
  };

  $scope.init = function() {
    $scope.inited = true;
  };
});
