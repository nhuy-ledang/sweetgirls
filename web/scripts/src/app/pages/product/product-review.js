angular.module('app.pages.product-review', [])

.controller('ProductReviewCtr', function($scope, $rootScope, $window, apiService, reviews, dlgComment) {
  var vm = this;
  vm.settings = $window['settings'];
  vm.type = $window['type'];
  vm.filter = $window['filter'];
  vm.user_id = $window['user_id'];
  vm.info = angular.extend({liked: false, totalLikes: 0}, $window['info']);
  $scope.data = {loading: true, submitted: false, reviews: []};
  console.log(vm.info);
  console.log($rootScope.currentUser);
  vm.loading = false;
  $scope.params = {
    'type': vm.type,
    'filter': vm.filter,
    'user_id': vm.user_id,
  };

  vm.getReviews = function(loading) {
    if (loading !== false) {
      $scope.data.loading = true;
    }
    reviews.getReviews($scope.params, $scope.data.loading).then(function(res) {
      console.log(res);
      $scope.data.reviews = res;
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    });
  };

  vm.updateReview = function(info) {
    var reviewToUpdate = _.find($scope.data.reviews, { id: info.id });
    if (reviewToUpdate) {
      _.extend(reviewToUpdate, info);
    }
  };

  vm.updateLike = function(info) {
    if(info.liked) {
      info.like_total++;
    } else if(info.like_total > 0) {
      info.like_total--;
    }
  };
  $scope.like = function(info) {
    if (!$rootScope.isLogged() || vm.loading) {
      location = vm.settings.loginUrl;
    }
    if (info) {
      var liked = !info.liked;
      info.liked = liked;
      vm.updateLike(info);
      vm.loading = true;
      reviews.createLike(info.id, {like: liked}).then(function(res) {
        vm.updateReview(info);
        vm.loading = false;
      }, function(errors) {
        console.log(errors);
        vm.loading = false;
        info.liked = !liked;
        vm.updateLike(info);
      });
    }
  };

  $scope.open = function(data, link) {
    console.log(data);
    if (link) {
      $window.open(link, "_blank");
    } else {
      dlgComment.show(function(res) {
        console.log(res);
      }, data);
    }
  };

  $scope.init = function() {
    console.log('ProductReviewCtr');
    vm.getReviews();
  };
});
