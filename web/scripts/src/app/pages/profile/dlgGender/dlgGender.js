angular.module('app.dlgGender', [])

.controller('DlgGenderCtrl', function($scope, $rootScope, $uibModalInstance, utils, users, security) {
  var vm = this;
  vm.labels = angular.extend({}, window.langtext);
  $scope.labels = vm.labels;
  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false};
  vm.params = {
    gender: $rootScope.currentUser.gender,
  };
  $scope.params = angular.copy(vm.params);

  $scope.close = function() {
    $uibModalInstance.dismiss('cancel');
  };

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    console.log($scope.params);
      if(form.$valid ) {
        var params = angular.copy($scope.params);
        $scope.envData.submitted = true;
        security.update(utils.toFormData(params), true).then(function(res) {
          console.log(res);
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
          $rootScope.$emit('dlgGenderParams', params);
          $rootScope.currentUser.gender = params.gender;
          // $rootScope.openAlert({summary: 'Cập nhật thành công!', timeout: 2000});
          $uibModalInstance.close();
        }, function(errors) {
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
          $rootScope.openError(errors[0].errorMessage);
        });
      }
  };
})


.factory('dlgGender', function($uibModal, helper) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'pages/profile/dlgGender/dlgGender.tpl.html',
        controller: 'DlgGenderCtrl',
        controllerAs: 'vm',
        size: 'sm modal-edit-gender modal-dialog-centered',
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
