angular.module('app.pages.profile-coins', [])

.controller('ProfileCoinsCtrl', function($scope, $rootScope, $window, users) {
  console.log('ProfileCoinsCtrl');
  var vm = this;

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

  vm.getCoinHistory = function() {
    $scope.data.loading = true;
    users.getCoinsHistory($scope.data).then(function(res) {
      console.log(res);
      $scope.data.loading = false;
      $scope.data.items = res.data;
      $scope.data.totalItems = res.pagination.total;
    }, function(errors) {
      $scope.data.loading = false;
      console.log(errors);
    });
  };

  $scope.init = function() {
    $scope.inited = true;
    vm.getCoinHistory();
  };
});

