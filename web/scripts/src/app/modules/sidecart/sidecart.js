angular.module('app.modules.sidecart', [])

.directive('sidecart', function() {
  return {
    restrict: 'EA',
    scope: {},
    templateUrl: 'modules/sidecart/sidecart.tpl.html',
    controller: function($scope, carts) {
      var vm = this;
      vm.inited = false;
      $scope.data = {loading: false, submitted: false, products: [], coins: 0, totals: {sub_total: 0, total: 0, coins: 0}, gift_orders : {next_gifts: null, current_gifts: null}};
      vm.getCarts = function(loading) {
        if (loading !== false) {
          $scope.data.loading = true;
        }
        carts.get(null, false).then(function(res) {
          console.log(res);
          $scope.data = _.extend($scope.data, res);
          $scope.data.loading = false;
          vm.resizePopup();
        }, function(errors) {
          console.log(errors);
          $scope.data.loading = false;
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
        item.quantity = quantity;
        $scope.data.submitted = true;
        promise.then(function(res) {
          console.log(res);
          $scope.data = _.extend($scope.data, res);
          $scope.data.submitted = false;
        }, function(errors) {
          console.log(errors);
          $scope.data.submitted = false;
        });
      };

      $scope.removeCart = function(item) {
        console.log(item);
        carts.remove(item.cart_id).then(function(res) {
          console.log(res);
          $scope.data = _.extend($scope.data, res);
          $scope.data.submitted = false;
        }, function(errors) {
          console.log(errors);
          $scope.data.submitted = false;
        });
      };

      // On/off
      $scope.isOpenCart = false;

      $scope.openCart = function() {
        $scope.isOpenCart = true;
        $("body").css({"overflow": "hidden", "padding-right": "5px"});
        if (!vm.inited) {
          vm.inited = true;
          vm.getCarts();
        }
        vm.resizePopup();
      };

      $scope.outsideCart = function() {
        $scope.isOpenCart = false;
        $("body").css({"overflow": "unset", "padding-right": "0"});
      };

      $scope.$on('sidecart:on', function() {
        console.log('sidecart:on');
        $scope.openCart();
      });

      $scope.$on('sidecart:updated', function(event, cartData) {
        console.log('sidecart:updated');
        $scope.data = _.extend($scope.data, cartData);
        vm.inited = true;
        $scope.openCart();
      });
    },
    link: function($scope, $element, $attr, $ctrl) {
      // console.log($element);
      // Set height cart popup
      $ctrl.resizePopup = function() {
        setTimeout(function() {
          var total_height = $('.cart-popup .section_title_cart').outerHeight(true) + $('.cart-popup .section_button_cart').outerHeight(true);
          $('.cart-popup .total_cart').css('height', 'calc(100vh - ' + total_height + 'px)');
          $('.cart-popup .empty_product').css('height', 'calc(100vh - ' + ($('.cart-popup .section_title_cart').outerHeight(true) * 2) + 'px)');
        });
      };
      /*$scope.$watch('isOpenCart', function(newVal, oldVal) {
        console.log(newVal, oldVal);
      });*/
    }
  };
});
