angular.module('app.pages.affiliate-order', [])

.controller('AffiliateOrderCtrl', function($scope, $rootScope, $window, affiliates) {
  var vm = this;
  $scope.inited = false;
  $scope.orderList = {loading: false, items: []};
  $scope.commissionList = {loading: false, items: []};
  $scope.overview = {amountTotal: 0, pointTotal: 0, pendingPointTotal: 0};

  // Init Model
  $scope.data = {
    loading: false,
    page: 1,
    pageSize: 25,
    totalItems: 0,
    sort: 'id',
    order: 'desc',
    data: {
      q: '',
      agent_id: $rootScope.affInfo.id,
      embed: 'order',
    },
  };

  vm.getAffOrders = function () {
    $scope.orderList.loading = true;
    $scope.commissionList.loading = true;
    affiliates.getOrders($scope.data).then(function(res) {
      console.log(res);
      $scope.orderList.loading = false;
      $scope.orderList.items = res.data;
      $scope.data.totalItems = res.pagination.total;
      $scope.commissionList = [];
      _.each(res.data, function(item) {
        if (item.status) {
          $scope.commissionList.push(item);
        }
      });
      vm.tempData = res.data;
    }, function(errors) {
      $scope.orderList.loading = false;
      $scope.commissionList.loading = false;
      console.log(errors);
    });
  };

  vm.getAffOverview = function () {
    affiliates.getOverview($scope.data).then(function(res) {
      console.log(res);
      $scope.overview = res.data;
      console.log($scope.overview);
    }, function(errors) {
      console.log(errors);
    });
  };

  vm.removeDiacritics = function(str) {
    return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
  };

  $scope.search = function () {
    var keyword = $scope.filterSearch.toLowerCase();
    vm.filteredItems = [];
    if (keyword === '') {
      $scope.orderList.items = vm.tempData;
      return;
    }
    _.each(vm.tempData, function(item) {
      var orderNo = item.order.no.toLowerCase();
      var firstName = vm.removeDiacritics(item.order.first_name.toLowerCase());
      if (orderNo.includes(vm.removeDiacritics(keyword)) || firstName.includes(vm.removeDiacritics(keyword))) {
        vm.filteredItems.push(item);
      }
    });
    $scope.orderList.items = vm.filteredItems;
  };

  vm.initCalendarPicker = function() {
    $(document).ready(function() {
      $('input[name="daterange"]').daterangepicker({
        opens: 'left', // hoặc 'right' tùy thuộc vào sở thích của bạn
        showDropdowns: true,
        cancelClass: "btn-light",
        buttonClasses: 'btn rounded-0',
        minYear: 2000, // Năm tối thiểu
        maxYear: parseInt(moment().format('YYYY'),10), // Năm tối đa là năm hiện tại
        drops: 'auto',
        locale: {
          format: 'DD/MM/YYYY', // Định dạng ngày tháng
          separator: ' - ',
          applyLabel: 'Xác nhận',
          cancelLabel: 'Hủy',
          fromLabel: 'From',
          toLabel: 'To',
          customRangeLabel: 'Custom',
          daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6','T7'],
          monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
          firstDay: 1
        },

      });

      $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $scope.data.data.start_date = picker.startDate.format('YYYY-MM-DD');
        $scope.data.data.end_date = picker.endDate.format('YYYY-MM-DD');
        console.log($scope.data);
        vm.getAffOrders();
        vm.getAffOverview();
      });
    });
  };

  $scope.$watch('data.page', function() {
    console.log($scope.data.page);
    vm.getAffOrders();
  });

  $scope.init = function() {
    $scope.inited = true;
    vm.getAffOrders();
    vm.getAffOverview();
    vm.initCalendarPicker();
  };
})

.directive('customPagination', function() {
  return {
    restrict: 'E',
    scope: {
      totalItems: '=',
      currentPage: '=',
      maxSize: '=',
      numPages: '=',
      itemsPerPage: '=',
      directionLinks: '=',
    },
    template: '<div><ul uib-pagination total-items="totalItems" ng-model="currentPage" max-size="maxSize" direction-links="directionLinks" class="pagination-sm" boundary-link-numbers="true" first-text="«" last-text="»" previous-text="‹" next-text="›" num-pages="numPages" items-per-page="itemsPerPage"></ul></div>',
    link: function(scope, element, attrs) {

    }
  };
});

