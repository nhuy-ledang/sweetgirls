angular.module('app.pages.profile-voucher', [])

.controller('ProfileVoucherCtrl', function($scope, $rootScope, $window, vouchers) {
  console.log('ProfileVoucherCtrl');
  var vm = this;

  $scope.data = {loading: false, items: []};
  vm.getVouchers = function() {
    $scope.data.loading = true;
    vouchers.getAll(true).then(function(res) {
      //console.log(res);
      $scope.data.items = res.data;
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    });
  };

  $scope.init = function() {
    $scope.inited = true;
    vm.getVouchers();
  };
});

