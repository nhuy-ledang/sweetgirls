angular.module('app.dlgPostFeed', [])

  .controller('DlgPostFeedCtrl', function($scope, $uibModalInstance, utils, reviews) {
    var vm = this;
    $scope.info = window.info;
    vm.labels = angular.extend({}, window.langtext);
    $scope.labels = vm.labels;
    $scope.PATTERNS = PATTERNS;
    $scope.envData = {showValid: false, submitted: false, changeFile: false};
    $scope.params = {
      link: '',
      status: 0,
    };

    $scope.submit = function(form) {
      $scope.envData.showValid = true;
      console.log(form);
      if (form.$valid) {
        $scope.envData.submitted = true;
        var newParams = angular.copy($scope.params);
        reviews.createLink(newParams).then(function(res) {
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
          utils.resetForm(form);
          $scope.params.link = '';
          alert('Thành công!');
          $uibModalInstance.close(res);
        }, function(errors) {
          alert('Không thành công!');
        });
      } else {
        alert('Xin nhập đầy đủ thông tin!');
      }
    };

    $scope.close = function() {
      console.log('close');
      $uibModalInstance.dismiss('cancel');
    };
  })

  .factory('dlgPostFeed', function($uibModal, helper) {
    return {
      show: function(fn, id) {
        return $uibModal.open({
          animation: true,
          //backdrop: 'static',
          templateUrl: 'dialogs/dlgPostFeed/dlgPostFeed.tpl.html',
          controller: 'DlgPostFeedCtrl',
          size: 'md custom-modal',
          resolve: {
            $data: function() {
              return {id: id};
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
