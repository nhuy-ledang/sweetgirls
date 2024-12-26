angular.module('app.dlgInfoDownload', [])

.controller('DlgInfoDownloadCtrl', function($scope, $uibModalInstance) {
  var vm = this;

  $scope.langText =  window.langtext;

  $scope.params = {
    email: '',
    phone_number: ''
  };

  $scope.close = function() {
    console.log('close');
    $uibModalInstance.dismiss('cancel');
  };

  $scope.submit = function(form) {
    console.log($scope.params);
    if(form.$valid) {
      $uibModalInstance.close($scope.params);
    }
  };
})

.factory('dlgInfoDownload', function($uibModal, helper) {
  return {
    show: function(fn) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'dialogs/dlgDocument/dlgInfoDownload.tpl.html',
        controller: 'DlgInfoDownloadCtrl',
        controllerAs: 'vm',
        size: 'lg modal-info-doc modal-dialog-centered',
      }).result.then(function(res) {
        helper.runFnc(fn, res);
      }, function(error) {
        console.log(error);
      });
    }
  };
});