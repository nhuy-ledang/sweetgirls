angular.module('app.pages.checkout-payment', [])

.controller('CheckoutPaymentCtr', function($scope, $rootScope, $window, carts, orders, dlgCoupon) {
  var vm = this;
  var user = $rootScope.currentUser;
  $scope.inited = false;
  $scope.envData = {showValid: false, submitted: false};
  vm.orderInfo = angular.extend({coupon: ''}, $window['orderInfo']);
  console.log(vm.orderInfo);

  // Get cart info
  $scope.data = {
    loading: false, products: [], coins: 0, totals: {
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
    },
    no_cod: false
  };
  // Model
  $scope.params = {
    tracking: vm.orderInfo.tracking ? vm.orderInfo.tracking : '',
    payment_code: 'cod',
    shipping_code: '',
    shipping_method: vm.orderInfo.shipping_method ? vm.orderInfo.shipping_method : '',
    shipping_time: vm.orderInfo.shipping_time ? vm.orderInfo.shipping_time : '',
    shipping_fee: 0,
    shipping_discount: 0,
    coupon: vm.orderInfo.coupon,
    address_id: vm.orderInfo.address_id,
    phone_number: vm.orderInfo.phone_number,
    email: vm.orderInfo.email,
    is_invoice: vm.orderInfo.is_invoice,
    company: vm.orderInfo.company,
    company_tax: vm.orderInfo.company_tax,
    company_email: vm.orderInfo.company_email,
    company_address: vm.orderInfo.company_address,
    note: vm.orderInfo.note,
    message: vm.orderInfo.message,
    referral_code: '',
    agree: false,
  };

  vm.getCarts = function(loading) {
    if (loading !== false) {
      $scope.data.loading = true;
    }
    carts.get(null, true).then(function(res) {
      console.log(res);
      $scope.data = _.extend($scope.data, res);
      $scope.params.shipping_code = $scope.data.totals.shipping_code;
      $scope.params.shipping_fee = $scope.data.totals.shipping_fee;
      $scope.params.shipping_discount = $scope.data.totals.shipping_discount;
      $scope.params.payment_code = $scope.data.no_cod ? 'bank_transfer' : 'cod';
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    });
  };

  $scope.changePaymentMethod = function() {
    console.log($scope.params);
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

  $scope.confirm = function(form, $event) {
    console.log('confirm');
    var isOutStock = _.filter($scope.data.products, function(obj) {
      return obj.product.stock_status == 'out_of_stock';
    });
    
    if (isOutStock.length) {
      $rootScope.openAlert({title: 'Sản phẩm đã hết hàng!', summary: isOutStock[0].product.long_name?isOutStock[0].product.long_name:isOutStock[0].product.name, timeout: 5000});
      return;
    }

    if ($scope.data.products.length > 1 && _.some($scope.data.products, { stock_status: 'pre_order' })) {
      $rootScope.openAlert({summary: '<div class="mb-5">Đơn hàng của bạn có sản phẩm <b class="text-danger">Đặt trước</b> <br> Vui lòng không đặt các sản phẩm khác chung với sản phẩm này!</div>', style: 'ok', url: '/checkout/cart'});
      return;
    }

    // if (!$scope.params.shipping_code) {
    //   $rootScope.openAlert({summary: 'Chưa chọn phương thức vận chuyển', timeout: 3500});
    // }
    console.log($scope.params);
    /*if(!$scope.params.address_id) {
      return $rootScope.openAlert({summary: 'Vui lòng chọn địa chỉ giao hàng!', timeout: 5000});
    }*/
    // $($event.currentTarget).button('loading');
    if (!$scope.params.agree) {
      $rootScope.openAlert({summary: 'Bạn chưa đồng ý với điều khoản thanh toán', timeout: 3500});
      $scope.error_agree = true;
      return;
    }
    if (true) {
      $scope.envData.submitted = true;
      // $($event.currentTarget).button('loading');
      if (!user) {
        var params = angular.copy($scope.params);
        params.address_info = vm.orderInfo.addressInfo;
        orders.createGuest(params, true).then(function(res) {
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
          // $($event.currentTarget).button('reset');
          // console.log(res);
          location = res.payment_url ? res.payment_url : res.success_url;
          // $rootScope.openAlert({summary: 'Đã mua hàng thành công!', timeout: 5000});
        }, function(errors) {
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
          // $($event.currentTarget).button('reset');
          $rootScope.openError(errors[0].errorMessage);
        });
      } else {
        orders.create(angular.copy($scope.params), true).then(function(res) {
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
          // $($event.currentTarget).button('reset');
          // console.log(res);
          location = res.payment_url ? res.payment_url : res.success_url;
          // $rootScope.openAlert({summary: 'Đã mua hàng thành công!', timeout: 5000});
        }, function(errors) {
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
          // $($event.currentTarget).button('reset');
          $rootScope.openError(errors[0].errorMessage);
        });
      }
      /*$.ajax({
        url: '/' + vm.urlPrefix + 'checkout/payment/confirm',
        type: 'post',
        data: $('#payment_methods input[type=\'text\'], #payment_methods input[type=\'hidden\'], #payment_methods input[type=\'radio\']:checked, #payment_methods input[type=\'checkbox\']:checked'),
        dataType: 'json',
        beforeSend: function() {
          $($event.currentTarget).button('loading');
        },
        complete: function() {
          $($event.currentTarget).button('reset');
        },
        success: function(json) {
          console.log(json);
          // location = '/checkout/payment';
          if (json['error']) {
            $rootScope.openError(json['error']);
          }
          if (json['payment'] && json['payment']['payment_url']) {
            location = json['payment']['payment_url'];
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          console.log(xhr, ajaxOptions, thrownError);
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });*/
    }
  };

  $scope.init = function() {
    $scope.inited = true;
    vm.getCarts();
  };
});
