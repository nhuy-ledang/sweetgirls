angular.module('app.pages.orderShippingHistories', [])

.directive('orderShippingHistories', function() {
  return {
    restrict: 'E',
    scope: {id: '@'},
    templateUrl: 'pages/profile/component/orderShippingHistories.tpl.html',
    controller: function($scope, orderShippingHistories) {
      var vm = this;
      vm.inited = false;
      $scope.data = {loading: false, submitted: false, items: {}};
      vm.getCarts = function(loading) {
        if (loading !== false) {
          $scope.data.loading = true;
        }
        orderShippingHistories.get({order_id: $scope.id}).then(function(res) {
          console.log(res);
          $scope.data.items = res;
          $scope.data.loading = false;
        }, function(errors) {
          console.log(errors);
          $scope.data.loading = false;
        });
      };
      vm.getCarts();
    },
    link: function($scope, $element, $attr, $ctrl) {
      // console.log($element);
    }
  };
});
