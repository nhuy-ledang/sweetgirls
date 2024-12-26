angular.module('app.dlgBankName', [])

.controller('DlgBankNameCtrl', function($scope, $rootScope, $uibModalInstance, affiliates, systems) {
  var vm = this;
  vm.labels = angular.extend({}, window.langtext);
  $scope.labels = vm.labels;
  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false};

  vm.getAllBanks = function() {
    systems.getBanks().then(function(bankList) {
      console.log(bankList);
      $scope.bankList = bankList;
    }, function(errors) {
      console.log(errors);
    });
  };
  vm.getAllBanks();
  vm.params = {
    bank_id: $rootScope.affInfo.bank_id?parseInt($rootScope.affInfo.bank_id):'',
    bank_name: $rootScope.affInfo.bank_name,
  };
  $scope.params = angular.copy(vm.params);

  $scope.close = function() {
    $uibModalInstance.dismiss('cancel');
  };

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    $bank_selected = _.find($scope.bankList, {id: parseInt($scope.params.bank_id)});
    $scope.params.bank_name = $bank_selected.name;
    console.log($scope.params);
    if(form.$valid ) {
      var params = angular.copy($scope.params);
      $scope.envData.submitted = true;
      affiliates.updateAffiliate(params, true).then(function(res) {
        console.log(res);
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.$emit('dlgBankNameParams', params);
        $rootScope.affInfo.bank_id = params.bank_id;
        $rootScope.affInfo.bank_name = params.bank_name;
        // $rootScope.openAlert({summary: 'Cập nhật thành công!', timeout: 2000});
        $uibModalInstance.close();
      }, function(errors) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.openError(errors[0].errorMessage);
      });
    }
  };

  $scope.init = function() {
    $(document).ready(function() {
      $('#bank_id').select2();
      $('#bank_id').next('.select2-container').addClass('form-control border-top-0 border-left-0 border-right-0 rounded-0 px-0 w-100');
    });
  };
})

.factory('dlgBankName', function($uibModal, helper) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'pages/affiliate/dlgBankName/dlgBankName.tpl.html',
        controller: 'DlgBankNameCtrl',
        controllerAs: 'vm',
        size: 'sm modal-edit-bank-name modal-dialog-centered',
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
