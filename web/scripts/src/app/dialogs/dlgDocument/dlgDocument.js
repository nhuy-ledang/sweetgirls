angular.module('app.dlgDocument', [])

.controller('DlgDocumentCtrl', function($scope, $uibModalInstance, $data, dlgInfoDownload, storageService, apiService) {
  var vm = this;
  vm.category_info = _.extend([], window.data.info);
  vm.category_id = $data.category_id;
  vm.categories = _.extend([], window.data.categories);
  vm.info = _.find(vm.categories, {id: vm.category_id});

  $scope.langText =  window.langtext;

  $scope.titleDocument = $scope.langText.titlebrochure + ' ' + vm.category_info.name;

  $scope.close = function() {
    console.log('close');
    $uibModalInstance.dismiss('cancel');
  };

  $scope.submit = function(form) {
    console.log(form);
    $uibModalInstance.close($scope.data);
  };

  $scope.saveInfo = function (item) {
    console.log(item);
    var info = storageService.getItem('info');
    if (!info) {
      dlgInfoDownload.show(function (res) {
        console.log(res);
        storageService.setItem('info', res);
        // Save info to database
        var params = {email: res.email, phone: res.phone_number, type:'download', message: vm.category_info.name};
        apiService.postViaApi('system_register_download', params).then(function(res) {
          console.log(res);
        }, function(errors) {
          console.log(errors);
        });

        window.open(item.download_url);
      });
    } else {
      window.open(item.download_url);
    }
  };

  console.log(vm.category_id);
})

.factory('dlgDocument', function($uibModal, helper) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'dialogs/dlgDocument/dlgDocument.tpl.html',
        controller: 'DlgDocumentCtrl',
        controllerAs: 'vm',
        size: 'lg custom-modal-document modal-dialog-centered',
        resolve: {
          $data: function() {
            return {info: data.info ,category_id: data.category_id};
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
