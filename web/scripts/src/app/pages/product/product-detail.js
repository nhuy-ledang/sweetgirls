angular.module('app.pages.product-detail', [])

.controller('ProductDetailCtr', function($scope, $rootScope, $window, apiService, products, carts) {
  var vm = this;
  vm.settings = $window['settings'];
  vm.info = angular.extend({liked: false, totalLikes: 0}, $window['info']);
  console.log(vm.info);
  vm.loading = false;
  $scope.liked = vm.info.liked;
  $scope.totalLikes = vm.info.totalLikes;
  vm.updateLike = function() {
    if($scope.liked) {
      $scope.totalLikes++;
    } else if($scope.totalLikes > 0) {
      $scope.totalLikes--;
    }
  };
  $scope.like = function() {
    console.log('like');
    if (!$rootScope.isLogged() || vm.loading) {
      return;
    }
    var liked = !$scope.liked;
    $scope.liked = liked;
    vm.updateLike();
    vm.loading = true;
    products.addLikeDislike(vm.info.id, {liked: liked}).then(function(res) {
      vm.loading = false;
    }, function(errors) {
      console.log(errors);
      vm.loading = false;
      $scope.liked = !liked;
      vm.updateLike();
    });
  };

  $scope.envData = {submitted: false};
  $scope.params = {
    quantity: 1,
  };

  $scope.updateCart = function(type) {
    var quantity = parseInt($scope.params.quantity);
    if (!quantity) {
      quantity = 1;
    }
    if (type === 'plus') {
      quantity += 1;
    } else if (type === 'minus') {
      quantity -= 1;
    }
    if (quantity < 1) {
      quantity = 1;
    }
    $scope.params.quantity = quantity;
  };

  $scope.addToCart = function() {
    vm.checkLogin();
    // check option
    if ($scope.lengthChecked >= $scope.lengthDataArray) {
      carts.create({product_id: vm.info.id, quantity: $scope.params.quantity}, true).then(function(res) {
        var cartData = _.extend({products: [], coins: 0, totals: {sub_total: 0, total: 0, coins: 0}}, res);
        $rootScope.$broadcast('sidecart:updated', cartData);
        $scope.envData.submitted = false;
      }, function(errors) {
        console.log(errors);
        $scope.envData.submitted = false;
      });
    } else {
      $rootScope.openAlert({summary: 'Vui lòng chọn Phân loại hàng', timeout: 1500});
    }
  };

  $scope.buyNow = function() {
    vm.checkLogin();
    if ($scope.lengthChecked >= $scope.lengthDataArray) {
      carts.create({product_id: vm.info.id, quantity: $scope.params.quantity}, true).then(function(res) {
        location = '/checkout/cart';
      }, function(errors) {
        console.log(errors);
        $scope.envData.submitted = false;
      });
    } else {
      $rootScope.openAlert({summary: 'Vui lòng chọn Phân loại hàng', timeout: 1500});
    }
  };

  $scope.init = function() {
    console.log('ProductDetailCtr');
    apiService.getVia('/', 'product/product/viewed', {product_id: vm.info.master_id?vm.info.master_id:vm.info.id});
  };

  $scope.info = vm.info;
  var dataObject = angular.extend({}, $window['product_options']);
  var dataArray = Object.values(dataObject);
  $scope.lengthChecked = 0;
  $scope.lengthDataArray = dataArray.length;
  vm.options = [];

  vm.filter = function(optionId, valueId) {
    var newArray = [];
    if (valueId) {
      $.each(dataArray, function(index, option) {
        if (option.option_id == optionId) {
          $.each(option.products, function(index, product) {
            if (product.option_value_id == valueId) {
              newArray.push(parseInt(product.id));
            }
          });
        }
      });
    }
    var filteredArr = _.filter(dataArray, function(item) {
      return item.option_id !== optionId;
    });
    $.each(filteredArr, function(index, item) {
      if (valueId) {
        $('#input-option' + item.option_id + ' [id^="input-p"]').prop('disabled', true);
        $.each(item.products, function(index, product) {
          if (newArray.includes(product.id)) {
            $('#input-option' + item.option_id + ' #input-p' + product.option_value_id).prop('disabled', false);
            $('#input-option' + item.option_id + ' #input-p' + product.option_value_id).parent().attr('data-product', product.id);
          }
        });
      } else {
        $('#input-option' + item.option_id + ' [id^="input-p"]').prop('disabled', false);
      }
    });
    console.log('valueId', valueId);
    console.log('filteredArr', filteredArr);
  };

  vm.checkLogin = function() {
    $rootScope.checkLogin();
  };

  $scope.chooseProduct = function($event, optionId, valueId) {
    vm.checkLogin();
    var isDisabled = $($event.currentTarget).attr('disabled');
    var productId = $($event.currentTarget).parent().attr('data-product');
    var selectedOptionId = parseInt(optionId);
    var selectedValueId = parseInt(valueId);

    $scope.lengthChecked = Object.keys($scope.params.options).length;
    if ($scope.lengthChecked >= $scope.lengthDataArray) {
      var info = _.find(dataArray[0].products, { id: parseInt(productId) });
      $scope.info.reduce = null;
      $.each(info, function(index, item) {
        $scope.info[index] = item;
      });

      var currentUrl = window.location.href;
      var newUrl = $scope.info.href;
      window.history.replaceState(null, null, newUrl);
      if (window.location.href !== currentUrl) {
        console.log("Change");
      }
      /*$(window).on("popstate", function() {
        console.log("Back");
      });*/
    }
    if (!isDisabled) {
      if (vm.options[optionId] == $scope.params.options[optionId]) {
        delete $scope.params.options[optionId];
        delete vm.options[optionId];
        $scope.lengthChecked--;
        $($event.currentTarget).prop("checked", false);
        vm.filter(selectedOptionId, 0);
      } else {
        vm.filter(selectedOptionId, selectedValueId);
        vm.options[optionId] = $scope.params.options[optionId];
      }
    }
  };
});
