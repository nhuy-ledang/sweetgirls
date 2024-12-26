angular.module('App', [
  'angular-click-outside',
  'ui.bootstrap',
  'ngCookies',
  'configs',
  'services',
  'resources',
  'directive',
  'security',
  'theme',
  'templates-app',
  'templates-common',
  'app.modals',
  'app.dialogs',
  'app.modules',
  'app.pages',
])

.provider('$socialConfig', function() {
  this.$get = function() {
    return {
      facebook: {
        name: 'facebook',
        url: 'auth/facebook',
        authorizationEndpoint: 'https://www.facebook.com/' + window['settings']['FACEBOOK_APP_VERSION'] + '/dialog/oauth',
        urlParams: {
          response_type: 'code',
          client_id: window['settings']['FACEBOOK_APP_ID'],
          redirect_uri: window['settings']['URL'] + '/oauth/oauthcallback.html',
          scope: 'email',
        },
        popupOptions: {width: 580, height: 400}
      },
      google: {
        name: 'google',
        url: 'auth/google',
        authorizationEndpoint: 'https://accounts.google.com/o/oauth2/auth',
        urlParams: {
          response_type: 'code',
          client_id: window['settings']['GOOGLE_CLIENT_ID'],
          redirect_uri: window['settings']['URL'] + '/oauth/oauth2callback.html',
          scope: 'openid profile email',
        },
        popupOptions: {width: 452, height: 633},
      }
    };
  };
})

.controller('AppCtrl', function($scope, $rootScope, $window, $uibModal, loadingSpinnerService, PATTERNS, resources, apiService, utils, dlgReview, dlgPostFeed, dlgCancelOrder, dlgLogin) {
  var vm = this;
  vm.labels = angular.extend({}, $window['labels']);
  vm.settings = angular.extend({locale: 'vi'}, $window['settings']);
  vm.user = angular.extend({logged: false, info: null, affiliate: null}, $window['user']);
  vm.logged = vm.user.logged;
  $rootScope.currentUser = vm.user.info;
  $rootScope.affInfo = vm.user.affiliate;
  $rootScope.labels = vm.labels;
  $rootScope.settings = vm.settings;
  $scope.PATTERNS = PATTERNS;

  //<editor-fold desc="Global Modal">
  $rootScope.openAlert = function(data, sizeName) {
    $scope.modal = $uibModal.open({
      animation: false,
      //backdrop: 'static',
      controller: 'AlertCtrl',
      templateUrl: 'modals/alert/alert.tpl.html',
      size: 'dialog-centered' + (sizeName ? sizeName : ''),
      resolve: {
        data: function() {
          return data;
        }
      }
    });

    return $scope.modal;
  };

  $rootScope.openWarning = function(message, sizeName) {
    $scope.modal = $uibModal.open({
      animation: false,
      backdrop: 'static',
      controller: 'WarningCtrl',
      templateUrl: 'modals/warning/warning.tpl.html',
      size: 'dialog-centered modal-sm ' + (sizeName ? sizeName : ''),
      resolve: {
        message: function() {
          return message;
        }
      }
    });

    return $scope.modal;
  };

  $rootScope.openError = function(message) {
    $scope.modal = $uibModal.open({
      animation: false,
      backdrop: 'static',
      controller: 'ErrorCtrl',
      templateUrl: 'modals/error/error.tpl.html',
      size: 'dialog-centered modal-sm',
      resolve: {
        message: function() {
          return message;
        }
      }
    });

    return $scope.modal;
  };
  //</editor-fold>

  $rootScope.checkLogin = function(url) {
    if ((!$rootScope.isLogged() && vm.settings.login_first)) {
      if (vm.settings.login_popup) {
        dlgLogin.show(function(res) {
          console.log(res);
        });
      } else {
        location = vm.settings.loginUrl;
      }
      throw new Error('Người dùng chưa đăng nhập!');
    } else {
      if (url) {
        location = url;
      }
    }
  };

  var countVisitor = function() {
    apiService.post('sys_visitors', {user_id: $rootScope.currentUser ? $rootScope.currentUser.id : ''}).then(function(res) {
    // console.log(res.data.data);
    }, function(errors) {
     console.log(errors);
    });
  };
  setTimeout(countVisitor, 5000);

  var notifyAffiliate = function() {
    if (vm.logged && $rootScope.affInfo && $rootScope.affInfo.status) {
      var aff = $rootScope.affInfo;
      var url = '/affiliate/account';
      if (location.pathname != url && aff.balance > 0 && (!aff.id_no || !aff.id_date || !aff.id_provider || !aff.id_behind || !aff.id_front || !aff.bank_number || !aff.card_holder || !aff.bank_name)) {
        $rootScope.openAlert({summary: '<b class="font-3">Vui lòng cập nhật đầy đủ thông tin để nhận thanh toán affiliate!</b><br><br>', url: url, style: 'ok'});
      }
    }
  };
  setTimeout(notifyAffiliate, 2000);

  $rootScope.isLogged = function() {
    if (!vm.logged) {
      console.log('Chưa đăng nhập!');
    }
    return vm.logged;
  };

  $rootScope.openCart = function() {
    $rootScope.$broadcast('sidecart:on');
  };

  // window.loadingSpinnerService = loadingSpinnerService;

  // TODO: Comment back in when request issues are resolved
  $scope.$on('API:loading:started', function(event, args) {
    loadingSpinnerService.spin('main-spinner');
    /*var excluded = ['signout'];
    if(excluded.indexOf(args.what) === -1) {
      loadingSpinnerService.spin('main-spinner');
    }*/
  });

  $scope.$on('API:loading:ended', function(event) {
    loadingSpinnerService.stop('main-spinner');
  });

  $scope.$on('response:authorized:error', function(event) {
    console.log('response:authorized:error');
    loadingSpinnerService.stop('main-spinner');
  });

  // Request timeout - disable spinner
  $scope.$on('response:timeout', function(event, rejection) {
    console.log('response:timeout');
    loadingSpinnerService.stop('main-spinner');
  });

  // Footer
  $scope.params = {
    email: ''
  };

  $scope.addSubscription = function(form) {
    if (form.$valid) {
      resources.addSubscription(form.email.$modelValue).then(function() {
        utils.resetForm(form);
        $scope.params.email = '';
        alert('Bạn đã đăng ký thành công!');
      }, function() {
        alert('Bạn đã đăng ký không thành công!');
      });
    }
  };

  $scope.openPostFeed = function() {
    if (!$rootScope.isLogged()) {
      location = vm.settings.loginUrl;
    }
    dlgPostFeed.show(function(res) {
      console.log(res);
    });
  };
  
  $scope.openCancelOrder = function(id) {
    dlgCancelOrder.show(function(res) {
      console.log(res);
    }, id);
  };

  $scope.openLogin = function() {
    if ($rootScope.isLogged()) {
      return;
    }
    dlgLogin.show(function(res) {
      console.log(res);
    });
  };
});

