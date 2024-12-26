angular.module('app.pages.profile-order', [])

.controller('ProfileOrderCtrl', function($scope, $rootScope, $window, users) {
  console.log('ProfileOrderCtrl');
  var vm = this;
  vm.filter = $window['filter'];

  $scope.data = {
    loading: false,
    items: [],
    page: 1,
    pageSize: 50,
    totalItems: 0,
    sort: 'id',
    order: 'desc',
    data: {
      q: '',
    },
  };

  vm.getUsers = function() {
    $scope.data.loading = true;
    if (vm.filter) {
      for (var key in vm.filter) {
        if (vm.filter.hasOwnProperty(key)) {
          $scope.data.data[key] = vm.filter[key];
        }
      }
    }
    console.log($scope.data.data);
    
    users.getOrders($scope.data).then(function(res) {
      console.log(res);
      $scope.data.loading = false;
      $scope.data.items = res.data;
      $scope.data.totalItems = res.pagination.total;
      setTimeout(function() {
        swiper_orders();
      }, 100);
    }, function(errors) {
      $scope.data.loading = false;
      console.log(errors);
    });
  };

  $scope.init = function() {
    $scope.inited = true;
    vm.getUsers();
  };
});

