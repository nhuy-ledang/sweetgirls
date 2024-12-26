angular.module('app.pages.profile-feedback', [])

  .controller('ProfileFeedbackCtrl', function($scope, $rootScope, utils, apiService) {
    $scope.envData = {showValid: false, submitted: false};

    $scope.params = {
      phone_number: '',
      order_id : '',
      type : '',
      message : '',
      file : '',
      filename : '',
    };

    $scope.listType = [
       'Hủy đơn hàng',
        'Đổi/trả sản phẩm lỗi',
        'Xuất hóa đơn',
        'Hỗ trợ Bảo hành',
        'Khác',
    ];

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

    $scope.submit = function(form) {
      $scope.envData.showValid = true;
      var phone = $rootScope.currentUser.phone_number ? $rootScope.currentUser.phone_number : $scope.params.phone_number;
      var containsNonNumeric = /\D/.test(phone);
      if (containsNonNumeric) {
        $rootScope.openAlert({summary: 'Số điện thoại không đúng định dạng!', timeout: 2000});
        return;
      }
      if (form.$valid) {
        console.log(form);
        $scope.envData.submitted = true;
        var formData = new FormData();
        formData.append('phone_number', phone);
        formData.append('order_id', $scope.params.order_id);
        formData.append('type',  $scope.params.type);
        formData.append('message', $scope.params.message);
        if ($scope.params.file !== '') {
          formData.append('file', $scope.params.file, $scope.params.filename);
        }
        apiService.postFormData('sys_feedbacks', formData).then(function() {
            $scope.envData.showValid = false;
            $scope.envData.submitted = false;
            $scope.params.order_id = '';
            $scope.params.type = '';
            $scope.params.message = '';
            $scope.params.file = '';
            $scope.params.filename = '';
            $rootScope.openAlert({summary: 'Yêu cầu hỗ trợ của bạn đã được gửi!', timeout: 3000});
            utils.resetForm(form);
        }, function(errors) {
          $rootScope.openError(errors[0].errorMessage);
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
        });
      }
    };

    $scope.init = function() {
      $scope.inited = true;
    };
  });
