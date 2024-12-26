angular.module('app.dlgBuyerInfo', [])

.controller('DlgBuyerInfoCtrl', function($scope, $rootScope, $uibModalInstance) {
  var vm = this;
  vm.labels = angular.extend({}, window.langtext);
  $scope.currentUser = $rootScope.currentUser;
  $scope.labels = vm.labels;
  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false};
  $scope.params = {
    email: '',
    phone_number: '',
  };

  $scope.close = function() {
    $uibModalInstance.dismiss('cancel');
  };

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    if (form.$valid) {
      $uibModalInstance.close($scope.params);
    }
  };
})

.factory('dlgBuyerInfo', function($uibModal, helper) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'dialogs/dlgBuyerInfo/dlgBuyerInfo.tpl.html',
        controller: 'DlgBuyerInfoCtrl',
        controllerAs: 'vm',
        size: 'modal-edit-buyer-info modal-dialog-centered',
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
