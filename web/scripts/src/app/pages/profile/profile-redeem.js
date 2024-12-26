angular.module('app.pages.profile-redeem', [])

.controller('ProfileRedeemCtrl', function($scope, $rootScope, $window, carts) {
  console.log('ProfileRedeemCtrl');
  var vm = this;
  vm.redeemProducts = angular.extend([], $window['redeemProducts']);

  $scope.data = {
    loading: false,
  };

  vm.checkIsValid = function (data) {
    vm.pd_id_carts = [];
    _.each(data.products, function(item) {
      if(item.type === 'G') {
        vm.pd_id_carts.push(item.product_id);
      }
    });
    _.each(vm.redeemProducts, function(item) {
      item.is_redeem = false;
      if (vm.pd_id_carts.includes(item.id)) {
        item.is_redeem = true;
      }
    });
    
    $scope.redeemProducts = vm.redeemProducts;
  };

  vm.getCarts = function(loading) {
    if (loading !== false) {
      $scope.data.loading = true;
    }
    carts.get(null, true).then(function(res) {
      console.log(res);
      $scope.data = _.extend($scope.data, res);
      $scope.data.loading = false;
      vm.checkIsValid($scope.data);
      _.each($scope.data.products, function(item) {
        $rootScope.currentUser.coins = $rootScope.currentUser.coins - item.coins;
      });
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    });
  };

  $scope.addProductByCoins = function(product) {
    console.log(product);
    carts.addProductByCoins({product_id: parseInt(product.id)}, true).then(function(res) {
      console.log(res);
      $scope.data = _.extend($scope.data, res);
      vm.checkIsValid($scope.data);
      $rootScope.currentUser.coins = $rootScope.currentUser.coins - product.coins;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $rootScope.openAlert({summary: errors[0].errorMessage, timeout: 1500});
    });
  };

  $scope.init = function() {
    $scope.inited = true;
    vm.getCarts();
  };
});

