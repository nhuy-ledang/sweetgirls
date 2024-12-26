angular.module('app.dlgInfo', [])

.controller('DlgInfoCtrl', function($scope, $uibModalInstance, $data) {
  $scope.close = function() {
    console.log('close');
    $uibModalInstance.dismiss('cancel');
  };

  $scope.submit = function(form) {
    console.log(form);
    $uibModalInstance.close($scope.data);
  };

  console.log($data);
  $scope.info = $data;
})

.factory('dlgInfo', function($uibModal, helper) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'dialogs/dlgInfo/dlgInfo.tpl.html',
        controller: 'DlgInfoCtrl',
        size: 'lg custom-modal modal-dialog-centered',
        resolve: {
          $data: function() {
            return {name: data.name, youtubeId: data.youtubeId, description: data.description};
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
