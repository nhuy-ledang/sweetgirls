angular.module('app.pages.wishlist', [])

.controller('WishlistCtrl', function($scope, $rootScope, $window) {
  var vm = this;
  vm.labels = $window['labels'];

  $scope.init = function() {
    $scope.inited = true;
  };
})

.controller('WishlistProductCtrl', function($scope, $rootScope, $window, products) {
  var vm = this;
  vm.loading = false;
  $scope.totalLikes = 0;
  $scope.dislike = function(id) {
    if(!$rootScope.isLogged() || vm.loading) {
      return;
    }
    vm.loading = true;
    products.addLikeDislike(id, {liked: false}).then(function(res) {
      vm.loading = false;
      location.reload();
    }, function(errors) {
      console.log(errors);
      vm.loading = false;
    });
  };
  $scope.init = function() {
  };
});
