angular.module('app.dlgRecruit', [])

.controller('DlgRecruitCtrl', function($scope, $uibModalInstance, utils, apiService) {

  $scope.downloadForm =  window.data.form;
  $scope.langText =  window.langtext;

  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false, changeFile: false};
  $scope.params = {
    name: '',
    email: '',
    phone: '',
    birthday: '',
    gender: '1',
    portfolio: '',
    file: '',
    filename: ' Choose file'
  };

  // $scope.info = $data;

  $scope.change = function($event) {
    console.log($event);
    if ($event.files.length) {
      $scope.params.file = $event.files[0];
      $scope.params.filename = $scope.params.file.name;
    } else {
      $scope.params.filename = ' Choose file';
    }
    return $scope.$apply();
  };

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    console.log(form);
    if (form.$valid) {
      $scope.envData.submitted = true;
      var formData = new FormData();
      formData.append('name', $scope.params.name);
      formData.append('email', $scope.params.email);
      formData.append('phone', $scope.params.phone);
      formData.append('company', $scope.params.birthday);
      formData.append('gender', $scope.params.gender);
      formData.append('categories', $scope.info.name);
      formData.append('message', $scope.params.portfolio);
      formData.append('file', $scope.params.file, $scope.params.filename);
      apiService.postFormData('system_recruits', formData).then(function() {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        utils.resetForm(form);
        $scope.params.name = '';
        $scope.params.email = '';
        $scope.params.phone = '';
        $scope.params.birthday = '';
        $scope.params.gender = '1';
        $scope.params.portfolio = '';
        $scope.params.file = '';
        alert('Bạn đã gửi thành công!');
      }, function(errors) {
        alert('Bạn đã gửi không thành công!');
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

.factory('dlgRecruit', function($uibModal, helper) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'dialogs/dlgRecruit/dlgRecruit.tpl.html',
        controller: 'DlgRecruitCtrl',
        // controllerAs: 'vm',
        size: 'md modal-recruit modal-dialog-centered',
        resolve: {
          $data: function() {
            return {id: data.id, name: data.name};
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
