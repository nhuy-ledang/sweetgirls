angular.module('app.dlgCoupon', [])

.controller('DlgCouponCtrl', function($scope, $uibModalInstance, coupons, discounts) {
  var vm = this;
  $scope.envData = {showValid: false, submitted: false};
  $scope.data = {loading: false, coupons: [], vouchers: []};
  vm.getCoupons = function() {
    $scope.data.loading = true;
    coupons.getAll(true).then(function(res) {
      // console.log(res);
      $scope.data.items = res.data;
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    });
  };

  vm.getDiscounts = function() {
    $scope.data.loading = true;
    discounts.getAll(true).then(function(res) {
      console.log(res);
      $scope.data.coupons = res.data.coupons;
      $scope.data.vouchers = res.data.vouchers;
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    });
  };

  $scope.close = function() {
    $uibModalInstance.dismiss('cancel');
  };

  $scope.params = {code: ''};

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    if (form.$valid) {
      $uibModalInstance.close($scope.params);
    }
  };

  $scope.select = function(item) {
    $uibModalInstance.close({code: item.code});
  };

  // vm.getCoupons();
  vm.getDiscounts();
})

.factory('dlgCoupon', function($uibModal, helper) {
  return {
    show: function(fn) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'dialogs/dlgCoupon/dlgCoupon.tpl.html',
        controller: 'DlgCouponCtrl',
        size: 'md custom-modal',
      }).result.then(function(res) {
        helper.runFnc(fn, res);
      }, function(error) {
        console.log(error);
      });
    }
  };
});
