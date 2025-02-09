angular.module('app.dlgIDNo', [])

.controller('DlgIDNoCtrl', function($scope, $rootScope, $uibModalInstance, affiliates) {
  var vm = this;
  vm.labels = angular.extend({}, window.langtext);
  $scope.labels = vm.labels;
  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false};
  vm.params = {
    id_no: $rootScope.affInfo.id_no,
  };
  $scope.params = angular.copy(vm.params);

  $scope.close = function() {
    $uibModalInstance.dismiss('cancel');
  };

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    console.log($scope.params);
    if(form.$valid) {
      var params = angular.copy($scope.params);
      $scope.envData.submitted = true;
      affiliates.updateAffiliate(params, true).then(function(res) {
        console.log(res);
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.$emit('dlgIDNoParams', params);
        $rootScope.affInfo.id_no = params.id_no;
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

.factory('dlgIDNo', function($uibModal, helper) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'pages/affiliate/dlgIDNo/dlgIDNo.tpl.html',
        controller: 'DlgIDNoCtrl',
        controllerAs: 'vm',
        size: 'sm modal-edit-id-no modal-dialog-centered',
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
