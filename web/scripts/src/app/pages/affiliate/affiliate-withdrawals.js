angular.module('app.pages.affiliate-withdrawals', [])

.controller('AffiliateWithdrawalsCtrl', function($scope, $rootScope, $window, affiliates) {
  var vm = this;
  $scope.inited = false;

  // Init Model
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
      agent_id: $rootScope.affInfo.id,
    },
  };

  vm.getAffOrders = function () {
    $scope.data.loading = true;
    affiliates.getWithdrawals($scope.data).then(function(res) {
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
    vm.getAffOrders();
  };
});

