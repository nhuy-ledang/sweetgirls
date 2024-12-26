angular.module('app.pages.profile-coupon', [])

.controller('ProfileCouponCtrl', function($scope, $rootScope, $window, coupons) {
  console.log('ProfileCouponCtrl');
  var vm = this;

  $scope.data = {loading: false, items: []};
  vm.getCoupons = function() {
    $scope.data.loading = true;
    coupons.getAll(true).then(function(res) {
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
    vm.getCoupons();
  };
});

