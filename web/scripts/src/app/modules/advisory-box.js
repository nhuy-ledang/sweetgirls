angular.module('app.modules.advisory-box', [])

.controller('AdvisoryBoxCtrl', function($scope, utils, apiService) {
  var vm = this;

  $scope.PATTERNS = PATTERNS;
  $scope.langText = window.langtext;
  $scope.envData = {showValid: false, submitted: false};
  $scope.params = {
    name: '',
    company: '',
    categories: '',
    email: '',
    phone: '',
    message: '',
    file: '',
    filename: '',
    type: '',
    website: '',
    table_content: {},
  };

  $scope.change = function($event) {
    console.log($event);
    if ($event.files.length) {
      $scope.params.file = $event.files[0];
      $scope.params.filename = $scope.params.file.name;
    } else {
      $scope.params.filename = '';
    }
    return $scope.$apply();
  };

  vm.resetForm = function(form) {
    $scope.envData.showValid = false;
    for (var att in $scope.params) {
      if ($scope.params.hasOwnProperty(att)) {
        $scope.params[att] = '';
      }
    }
    utils.resetForm(form);
    return $scope.$$phase || $scope.$apply();
  };

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    if (form.$valid) {
      var table_content = JSON.stringify($scope.params.table_content);
      console.log($scope.params.table_content);
      $scope.envData.submitted = true;
      var formData = new FormData();
      formData.append('name', $scope.params.name);
      formData.append('email', $scope.params.email);
      formData.append('phone', $scope.params.phone);
      formData.append('company', $scope.params.company);
      if ($scope.params.message) {
        formData.append('message', $scope.params.message);
      }
      if ($scope.params.categories) {
        formData.append('categories', $scope.params.categories);
      }
      if ($scope.params.type) {
        formData.append('type', $scope.params.type);
      }
      if ($scope.params.website) {
        formData.append('website', $scope.params.website);
      }
      if ($scope.params.table_content) {
        formData.append('table_content', table_content);
      }
      if ($scope.params.file) {
        formData.append('file', $scope.params.file, $scope.params.filename);
      }
      apiService.postFormData('system_contacts', formData).then(function() {
        console.log($scope.params.table_content);
        $scope.envData.submitted = false;
        vm.resetForm(form);
        alert($scope.langText.text_send_success ? $scope.langText.text_send_success : 'text_send_success');
      }, function(errors) {
        alert($scope.langText.text_send_unsuccess ? $scope.langText.text_send_unsuccess : 'text_send_unsuccess');
      });
    } else {
      alert($scope.langText.text_enter_all_information ? $scope.langText.text_enter_all_information : 'text_enter_all_information');
    }
  };
});
