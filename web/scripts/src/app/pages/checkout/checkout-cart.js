angular.module('app.pages.checkout-cart', [])

.controller('CheckoutCartCtr', function($scope, $rootScope, $window, carts, products, dlgAddress, dlgCoupon) {
  var vm = this;
  var user = $rootScope.currentUser;
  $scope.inited = false;
  vm.redeemProducts = angular.extend([], $window['redeemProducts']);
  // Get cart info
  $scope.data = {
    loading: false, submitted: false, products: [], coins: 0, totals: {
      sub_total: 0,
      discount_code: '',
      discount_total: 0,
      voucher_code: '',
      voucher_total: 0,
      shipping_code: '',
      shipping_fee: 0,
      shipping_discount: 0,
      total: 0,
      included_total: 0,
      coins: 0,
    }, coupon: null,
    gift_orders : {
      next_gifts: null,
      current_gifts: null
    }
  };

  vm.checkIsValid = function (data) {
    vm.pd_id_carts = [];
    $scope.hasProduct = false;
    _.each(data.products, function(item) {
      if(item.type === 'G') {
        vm.pd_id_carts.push(item.product_id);
      }
      if(item.type === 'T') {
        $scope.hasProduct = true;
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
      if (user) {
        _.each($scope.data.products, function(item) {
          $rootScope.currentUser.coins = $rootScope.currentUser.coins - item.coins;
        });
      }
      if ($scope.data.products.length > 1 && _.some($scope.data.products, { stock_status: 'pre_order' })) {
        $rootScope.openAlert({summary: '<div class="mb-5">Đơn hàng của bạn có sản phẩm <b class="text-danger">Đặt trước</b> <br> Vui lòng không đặt các sản phẩm khác chung với sản phẩm này!</div>', style: 'ok'});
      }
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    });
  };

  vm.getTotals = function(loading) {
    if (loading !== false) {
      $scope.data.loading = true;
    }
    products.getCartTotals($scope.data.loading).then(function(res) {
      $scope.data.totals = _.extend($scope.data.totals, res);
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    });
  };

  $scope.updateCart = function(item, type) {
    var quantity = item.quantity;
    if (type === 'plus') {
      quantity += 1;
    } else if (type === 'minus') {
      quantity -= 1;
    }
    if (quantity < 0) {
      quantity = 0;
    }
    console.log(item);
    var promise = false;
    if (item.cart_id) {
      if (quantity) {
        promise = carts.update(item.cart_id, {quantity: quantity});
      } else {
        promise = carts.remove(item.cart_id);
      }
    }/* else if (quantity) {
      promise = carts.create({product_id: $scope.info.id, quantity: quantity});
    }*/
    if (promise === false) {
      return;
    }
    $scope.data.submitted = true;
    promise.then(function(res) {
      item.quantity = quantity;
      console.log(res);
      $scope.data = _.extend($scope.data, res);
      $scope.data.submitted = false;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $rootScope.openAlert({summary: errors[0].errorMessage, timeout: 1500});
      $scope.data.submitted = false;
      return $scope.$$phase || $scope.$apply();
    });
  };

  $scope.removeCart = function(item) {
    console.log(item);
    carts.remove(item.cart_id).then(function(res) {
      console.log(res);
      $scope.data = _.extend($scope.data, res);
      $scope.data.submitted = false;
      vm.checkIsValid($scope.data);
      if (item.type === 'G') {
        $rootScope.currentUser.coins = $rootScope.currentUser.coins + item.coins;
      }
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $scope.data.submitted = false;
      return $scope.$$phase || $scope.$apply();
    });
  };

  $scope.like = function(item) {
    console.log(item);
    var liked = !item.liked;
    item.liked = liked;
    vm.loading = true;
    products.addLikeDislike(item.product_id, {liked: liked}).then(function(res) {
      vm.loading = false;
    }, function(errors) {
      console.log(errors);
      vm.loading = false;
      item.liked = !liked;
      return $scope.$$phase || $scope.$apply();
    });
    return $scope.$$phase || $scope.$apply();
  };

  $scope.addressInfo = _.extend({id: 0, address_1: user ? user.address : '', first_name: user ? user.display : '', phone_number: user ? user.phone_number : ''}, $window['addressInfo']);

  $scope.showEditAddress = function() {
    dlgAddress.show(function(addressInfo) {
      $scope.addressInfo = addressInfo;
      $.ajax({
        url: '/checkout/shipping/save',
        type: 'post',
        data: $.param({address_id: addressInfo.id}),
        dataType: 'json',
        success: function(json) {
          console.log(json);
        }
      });
      return $scope.$$phase || $scope.$apply();
    });
  };

  vm.addCoupon = function(coupon) {
    carts.addCoupon(coupon, true).then(function(res) {
      console.log(res);
      $scope.data.totals = res.totals;
      $scope.data.coupon = res.coupon;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $rootScope.openError(errors[0].errorMessage);
    });
  };

  $scope.selectCoupon = function() {
    $rootScope.checkLogin();
    dlgCoupon.show(function(res) {
      vm.addCoupon(res.code);
    });
  };

  $scope.clearCoupon = function() {
    carts.clearCoupon(true).then(function(res) {
      console.log(res);
      $scope.data.totals = res.totals;
      $scope.data.coupon = res.coupon;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $rootScope.openError(errors[0].errorMessage);
    });
  };

  $scope.addVoucher = function() {
    console.log('addVoucher', $scope.data.totals.voucher_code);
    carts.addVoucher($scope.data.totals.voucher_code, true).then(function(res) {
      console.log(res);
      $scope.data.totals = res.totals;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $rootScope.openError(errors[0].errorMessage);
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
