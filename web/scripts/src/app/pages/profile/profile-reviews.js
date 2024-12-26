angular.module('app.pages.profile-reviews', [])

  .controller('ProfileReviewsCtrl', function($scope, $rootScope, reviews, dlgReview) {
    var vm = this;
    console.log('ProfileReviewsCtrl');
    $scope.data = {
      loading: false,
      items: [],
      page: 1,
      pageSize: 1000,
      sort: 'id',
      order: 'desc',
    };

    vm.getProductReviews = function() {
      $scope.data.loading = true;
      reviews.get($scope.data.page, $scope.data.pageSize, $scope.data.sort, $scope.data.order, $scope.data.data).then(function(res) {
        console.log(res);
        $scope.data.loading = false;
        $scope.data.items = res;
      }, function(errors) {
        $scope.data.loading = false;
        console.log(errors);
      });
    };

    $scope.openReview = function(data) {
      console.log(data);
      dlgReview.show(function(res) {
        console.log(res);
        vm.getProductReviews();
      }, data);
    };

    $scope.init = function() {
      $scope.inited = true;
      vm.getProductReviews();
    };
  });
