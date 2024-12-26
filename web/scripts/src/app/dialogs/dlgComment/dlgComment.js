angular.module('app.dlgComment', [])

.controller('DlgCommentCtrl', function($scope, $window, $rootScope, $data, $uibModalInstance, $interval, utils, reviews) {
  var vm = this;

  vm.settings = $window['settings'];
  $scope.logo = $window['logo'];
  $scope.data = {loading: true, submitted: false, comments: []};
  $scope.info = $data;
  $scope.like_total = $scope.info.like_total;
  $scope.comment_total = $scope.info.comment_total;
  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false};
  $scope.params = {
    comment: '',
  };

  vm.getComments = function(loading) {
    reviews.getComments($scope.info.id, $scope.data.loading).then(function(res) {
      console.log(res);
      $scope.data.comments = res;
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    }, function(errors) {
      console.log(errors);
      $scope.data.loading = false;
      return $scope.$$phase || $scope.$apply();
    });
  };
  vm.getComments();

  $scope.liked = $scope.info.liked;
  vm.updateLike = function() {
    if($scope.liked) {
      $scope.like_total++;
    } else if($scope.like_total > 0) {
      $scope.like_total--;
    }
  };
  $scope.like = function() {
    if (!$rootScope.isLogged() || vm.loading) {
      location = vm.settings.loginUrl;
    }
    var liked = !$scope.liked;
    $scope.liked = liked;
    console.log(liked);
    vm.updateLike();
    console.log('updateLike', liked);

    vm.loading = true;
    reviews.createLike($scope.info.id, {like: liked}).then(function(res) {
      vm.loading = false;
    }, function(errors) {
      console.log(errors);
      vm.loading = false;
      $scope.liked = !liked;
      vm.updateLike();
    });
  };

  $scope.submit = function(form) {
    if (!$rootScope.isLogged() || vm.loading) {
      location = vm.settings.loginUrl;
    }
    $scope.envData.showValid = true;
    console.log(form);
    if (form.$valid) {
      $scope.envData.submitted = true;
      var newParams = angular.copy($scope.params);
      reviews.createComment($scope.info.id , utils.toFormData(newParams)).then(function(res) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        utils.resetForm(form);
        $scope.params.comment = '';
        $scope.comment_total++;
        vm.getComments();
      }, function(errors) {
        alert('Không thành công!');
      });
    } else {
      alert('Xin nhập đầy đủ thông tin!');
    }
  };

  $scope.close = function() {
    console.log('close');
    $uibModalInstance.close({'like_total': $scope.like_total, 'comment_total': $scope.comment_total});
  };
})

.factory('dlgComment', function($uibModal, helper) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'dialogs/dlgComment/dlgComment.tpl.html',
        controller: 'DlgCommentCtrl',
        size: 'lg custom-modal',
        resolve: {
          $data: function() {
            return data;
          }
        }
      }).result.then(function(res) {
        helper.runFnc(fn, res);
      }, function(error) {
        console.log(error);
      });
    }
  };
});
