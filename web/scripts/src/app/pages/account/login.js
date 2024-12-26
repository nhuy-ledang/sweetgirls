angular.module('app.pages.login', [])

.controller('LoginCtr', function($scope, $rootScope, $window, $socialConfig, security, MTLPopup, apiService) {
  var vm = this;
  vm.labels = angular.extend({}, $window['labels']);
  vm.settings = angular.extend({}, $window['settings']);

  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false};

  // Init Model
  $scope.params = {email: '', password: '', remember: true};
  $scope.passType = 'password';

  $scope.login = function(isValid) {
    $scope.envData.showValid = true;
    if(isValid) {
      $scope.envData.submitted = true;
      security.login($scope.params).then(function(res) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $window.location = vm.settings.returnUrl ? vm.settings.returnUrl : vm.settings.APP_URL;
      }, function(errors) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.openError(errors[0].errorMessage);
      });
    }
  };

  $scope.changePassType = function() {
    console.log('changePassType');
    $scope.passType = $scope.passType === 'password' ? 'text' : 'password';
  };

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
