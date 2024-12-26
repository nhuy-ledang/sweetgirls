angular.module('app.pages.wheel', [])

.controller('WheelCtr', function($scope, $rootScope, $window, users) {
  var vm = this;
  vm.order_id = $window['order_id'];
  vm.settings = $window['settings'];
  vm.wheel_info = $window['wheel_info'];
  vm.info = $rootScope.currentUser;
  console.log(vm.info);

  $scope.checkLogin = function() {
    if (vm.order_id) {
      return;
    }
    if (!$rootScope.isLogged() || vm.loading) {
      $rootScope.openAlert({summary: '<b class="font-3">Vui lòng đăng nhập!</b>'});
      setTimeout(function() {
        location = vm.settings.loginUrl;
      }, 2000);
    }
  };

  $scope.create = function(data) {
    $scope.checkLogin();
    users.createWheel({wheel_cat_id: vm.wheel_info.id, wheel_id: data.id, order_id: vm.order_id ? vm.order_id : ''}).then(function(res) {
      console.log(res);
      var message = data.text;
      if (parseInt(data.lost)) {
        message = "<svg xmlns=\"http://www.w3.org/2000/svg\" height=\"80\" viewBox=\"0 -960 960 960\" width=\"80\"><path fill=\"#ffd121\" d=\"M605.726-512.501q21.12 0 35.908-14.783 14.788-14.784 14.788-35.904 0-21.119-14.783-35.908-14.784-14.788-35.904-14.788-21.12 0-35.908 14.784-14.788 14.783-14.788 35.903 0 21.12 14.784 35.908 14.783 14.788 35.903 14.788Zm-251.461 0q21.12 0 35.908-14.783 14.788-14.784 14.788-35.904 0-21.119-14.784-35.908-14.783-14.788-35.903-14.788-21.12 0-35.908 14.784-14.788 14.783-14.788 35.903 0 21.12 14.783 35.908 14.784 14.788 35.904 14.788Zm125.757 79.693q-62.022 0-113.406 34.577-51.384 34.577-71.692 93.884h62.691q19.916-32.492 52.521-51.842 32.605-19.35 69.864-19.35 37.259 0 69.864 19.35 32.605 19.35 52.521 51.842h62.691q-20.308-59.307-71.67-93.884t-113.384-34.577Zm.345 308.73q-73.427 0-138.341-27.825-64.914-27.824-113.652-76.595-48.738-48.77-76.517-113.513-27.779-64.744-27.779-138.356 0-73.693 27.825-138.107 27.824-64.414 76.595-113.152 48.77-48.738 113.513-76.517 64.744-27.779 138.356-27.779 73.693 0 138.107 27.825 64.414 27.824 113.152 76.595 48.738 48.77 76.517 113.28 27.779 64.509 27.779 137.855 0 73.427-27.825 138.341-27.824 64.914-76.595 113.652-48.77 48.738-113.28 76.517-64.509 27.779-137.855 27.779ZM480-480Zm-.013 307.962q127.898 0 217.936-90.026 90.039-90.026 90.039-217.923 0-127.898-90.026-217.936-90.026-90.039-217.923-90.039-127.898 0-217.936 90.026-90.039 90.026-90.039 217.923 0 127.898 90.026 217.936 90.026 90.039 217.923 90.039Z\"/></svg>";
        $rootScope.openAlert({summary: '<b class="font-3">Chúc bạn may mắn lần sau</b>', message: message, style: 'ok'});
      } else {
        if (vm.order_id && data.product_id && (data.type === 'P' && parseInt(data.amount) === 100)) {
          message += '<br><br><i class="small">Phần quà sẽ được gửi cùng đơn hàng của bạn.</i>';
        }
        $rootScope.openAlert({summary: '<b class="font-3">Chúc mừng bạn đã trúng</b>', message: message, style: 'ok'});
      }
    }, function(errors) {
      console.log(errors);
      $rootScope.openAlert({message: '<div class="text-danger">'+ errors[0].errorMessage +'</div>', timeout: 3000});
    });
  };

  $scope.init = function() {
    console.log('WheelCtr');
  };
});
