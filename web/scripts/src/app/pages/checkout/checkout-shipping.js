angular.module('app.pages.checkout-shipping', [])

.controller('CheckoutShippingCtr', function($scope, $rootScope, $window, $timeout, carts, products, users, addresses, dlgAddress, dlgBuyerInfo, dlgAddressList, dlgCoupon) {
  var vm = this;
  var user = angular.extend({email: null, phone_number: null, id: null}, $rootScope.currentUser);
  vm.orderInfo = $window['orderInfo'];
  $scope.addressInfo = $window['addressInfo'];
  $scope.addressList = [];
  $scope.inited = false;
  console.log(vm.orderInfo, $scope.addressInfo);
  $scope.shippingMethodList = [];
  $scope.includedProducts = [];
  // Get cart info
  vm.data = {
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
    }
  };
  $scope.data = angular.copy(vm.data);
  // Model
  $scope.params = {
    address_id: $scope.addressInfo ? $scope.addressInfo.id : '',
    email: vm.orderInfo ? vm.orderInfo.email : user.email,
    phone_number: vm.orderInfo ? vm.orderInfo.phone_number : user.phone_number,
    shipping_code: '',
    shipping_method: '',
    shipping_time: '',
    shipping_fee: 0,
    shipping_discount: 0,
    is_invoice: vm.orderInfo ? !!vm.orderInfo.is_invoice : false,
    company: vm.orderInfo ? vm.orderInfo.company : '',
    company_tax: vm.orderInfo ? vm.orderInfo.company_tax : '',
    company_email: vm.orderInfo ? vm.orderInfo.company_email : '',
    company_address: vm.orderInfo ? vm.orderInfo.company_address : '',
    // is_note: vm.orderInfo ? !!vm.orderInfo.note : false,
    is_note: false,
    note: vm.orderInfo ? vm.orderInfo.note : '',
    message: vm.orderInfo ? vm.orderInfo.message : '',
  };

  vm.getCarts = function(loading) {
    if (loading !== false) {
      $scope.data.loading = true;
    }
    carts.get({extend_fields: 'included_products'}, true).then(function(res) {
      var includedProducts = res.included_products ? res.included_products : [];
      delete res.included_products;
      $scope.data = _.extend(angular.copy(vm.data), res);
      console.log($scope.data);
      for (var i = 0; i < includedProducts.length; i++) {
        includedProducts[i].selected = !!_.find($scope.data.products, {product_id: includedProducts[i].id});
      }
      $scope.includedProducts = includedProducts;
      $scope.params.shipping_code = $scope.data.totals.shipping_code;
      $scope.params.shipping_fee = $scope.data.totals.shipping_fee;
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    });
  };

  vm.getShippingServices = function() {
    var province_id = $scope.addressInfo.vt_province_id;
    var district_id = $scope.addressInfo.vt_district_id;
    var ward_id = $scope.addressInfo.vt_ward_id;
    if (province_id && district_id && ward_id) {
      $scope.shippingMethodList = [];
      carts.getShippingServices({province_id: province_id, district_id: district_id, ward_id: ward_id}).then(function(res) {
        console.log(res);
        // Sắp xếp phương thức giao hàng theo giá nhỏ => lớn
        if (res) {
          res.sort(function(a, b) {
            return a.GIA_CUOC - b.GIA_CUOC;
          });
        }
        for (var i = 0; i < res.length; i++) {
          var name = res[i].TEN_DICHVU;
          if (res[i].MA_DV_CHINH === 'LCOD') {
            name = 'Giao hàng tiết kiệm';
          } else if (res[i].MA_DV_CHINH === 'NCOD') {
            name = 'Giao hàng nhanh';
          } else if (res[i].MA_DV_CHINH === 'VHT') {
            name = 'Giao hàng hỏa tốc';
            // Skip VHT
            continue;
          }
          // VCN mã lỗi
          if (res[i].MA_DV_CHINH !== 'VCN') {
            // Ẩn giao hàng nhanh => Lấy 1 phương thức đầu tiên
            if (i === 0) {
              $scope.shippingMethodList.push({name: name, code: res[i].MA_DV_CHINH, price: res[i].GIA_CUOC, time: res[i].THOI_GIAN});
            }
          }
        }
        // Update shipping fee
        if ($scope.params.shipping_code !== '') {
          setTimeout(function() {
              var selectedInput = $("#" + $scope.params.shipping_code);
              selectedInput.parent().trigger('click');
          }, 100);
        }
        return $scope.$$phase || $scope.$apply();
      }, function(errors) {
        console.log(errors);
        return $scope.$$phase || $scope.$apply();
      });
    }
    /*else {
      $rootScope.openAlert({summary: 'Vui lòng nhập đầy đủ thông tin', timeout: 1500});
    }*/
  };

  $scope.shippingMethodSelected = null;
  $scope.selectShippingMethod = function(shippingMethod) {
    $scope.data.loading = true;
    $scope.params.shipping_code = shippingMethod.code;
    $scope.params.shipping_method = shippingMethod.name;
    $scope.params.shipping_time = shippingMethod.time;
    $scope.shippingMethodSelected = shippingMethod; //_.find($scope.shippingMethodList, {code: code});
    //if ($scope.shippingMethodSelected) {
      $scope.params.shipping_fee = $scope.shippingMethodSelected.price;
      if (!$scope.data.totals.shipping_fee) {
        $scope.data.totals.shipping_fee = 0;
      }
      $scope.data.totals.total -= $scope.data.totals.shipping_fee;
      $scope.data.totals.shipping_fee = $scope.shippingMethodSelected.price;
      $scope.data.totals.total += $scope.data.totals.shipping_fee;
    //}
    // Save database
    var province_id = $scope.addressInfo.vt_province_id;
    var district_id = $scope.addressInfo.vt_district_id;
    var ward_id = $scope.addressInfo.vt_ward_id;
    if (province_id && district_id && ward_id) {
      carts.setShippingFee({province_id: province_id, district_id: district_id, ward_id: ward_id, shipping_code: $scope.params.shipping_code}, true).then(function(res) {
        console.log(res);
        $scope.data.totals = res.totals;
        $scope.data.coupon = res.coupon;
        $scope.data.loading = false;
        return $scope.$$phase || $scope.$apply();
      }, function(errors) {
        console.log(errors);
        $scope.data.loading = false;
        return $scope.$$phase || $scope.$apply();
      });
    }
  };

  vm.getAddresses = function() {
    if (user.id) {
      addresses.getAddressAll(user.id, false).then(function(addressList) {
        // console.log(addressList);
        $scope.addressList = addressList;
      }, function(errors) {
        console.log(errors);
      });
    }
  };

  $scope.changeAddress = function() {
    dlgAddressList.show(function(addressInfo) {
      $scope.addressInfo = addressInfo;
      $scope.params.address_id = addressInfo.id;
      console.log($scope.addressInfo);
      // vm.getAddresses();
      vm.getShippingServices();
      return $scope.$$phase || $scope.$apply();
    }, {
      addressList: $scope.addressList, address_id: $scope.addressInfo ? $scope.addressInfo.id : 0,
    });
  };

  $scope.editBuyerInfo = function() {
    dlgBuyerInfo.show(function(params) {
      console.log(params);
      $scope.params.phone_number = params.phone_number;
      $scope.params.email = params.email;
      return $scope.$$phase || $scope.$apply();
    }, {});
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

  vm.onChangeIncludedProduct = function(item) {
    if (item.selected) {
      carts.addIncludeProduct({product_id: item.id}, true).then(function(res) {
        console.log(res);
        $scope.data = _.extend(angular.copy(vm.data), res);
        return $scope.$$phase || $scope.$apply();
      }, function(errors) {
        console.log(errors);
        // $rootScope.openError(errors[0].errorMessage);
      });
    } else {
      carts.removeProduct(item.id, true).then(function(res) {
        console.log(res);
        $scope.data = _.extend(angular.copy(vm.data), res);
        return $scope.$$phase || $scope.$apply();
      }, function(errors) {
        console.log(errors);
        item.selected = true;
        return $scope.$$phase || $scope.$apply();
      });
    }
  };

  var timer = null;
  $scope.onChangeIncludedProduct = function(item) {
    console.log(item);
    if (!timer) {
      timer = $timeout(function() {
        vm.onChangeIncludedProduct(item);
        timer = null;
      }, 500);
    } else {
      $timeout.cancel(timer);
      timer = $timeout(function() {
        vm.onChangeIncludedProduct(item);
        timer = null;
      }, 500);
    }
  };

  $scope.confirm = function(form, $event) {
    var params = angular.copy($scope.params);
    params.is_invoice = $scope.params.is_invoice ? 1 : 0;
    /*if (!$scope.addressInfo) {
      $rootScope.openAlert({summary: 'Chưa thêm người nhận', timeout: 3500});
      return;
    }*/
    if (!$scope.params.email && !$scope.params.phone_number) {
      $rootScope.openAlert({summary: 'Chưa thêm người mua', timeout: 3500});
      return;
    }
    // if (!$scope.params.shipping_code) {
    //   $rootScope.openAlert({summary: 'Chưa chọn phương thức vận chuyển', timeout: 3500});
    // }
    console.log(params);
    if (form.$valid) {
      $.ajax({
        url: '/checkout/shipping/order_info',
        type: 'post',
        data: $.param(params),
        dataType: 'json',
        beforeSend: function() {
          $($event.currentTarget).button('loading');
        },
        complete: function() {
          $($event.currentTarget).button('reset');
        },
        success: function(json) {
          location = '/checkout/payment';
        }
      });
    }
  };

  $scope.init = function() {
    $scope.inited = true;
    vm.getCarts();
    vm.getAddresses();
    vm.getShippingServices();
  };
});
