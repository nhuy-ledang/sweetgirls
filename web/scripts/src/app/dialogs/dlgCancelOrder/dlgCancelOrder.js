angular.module('app.dlgCancelOrder', [])

.controller('DlgCancelOrderCtrl', function($rootScope, $scope, $uibModalInstance, $interval, utils, orders, $data) {
  var vm = this;

  $scope.id =  $data;
  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false};
  $scope.params = {
    reason: '',
  };

  $scope.listReason = [
    'Cần thay đổi phương thức thanh toán',
    'Chiết khấu không như mong đợi',
    'Đơn đặt hàng được tạo do có sự nhầm lẫn',
    'Phí giao hàng cao',
    'Có giá tốt hơn',
    'Thông tin giao hàng không chính xác',
    'Sản phẩm không được vận chuyển đúng thời hạn',
  ];

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    if (form.$valid) {
      $scope.envData.submitted = true;
      var params = angular.copy($scope.params);
      orders.cancelOrder($scope.id, params, true).then(function() {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.openAlert({summary: 'Đơn hàng của bạn đã được hủy!', timeout: 3000});
        utils.resetForm(form);
        $uibModalInstance.close('close');
        location.reload();
      }, function(errors) {
        console.log(errors);
        $rootScope.openError(errors[0].errorMessage);
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
      });
    } else {
      $rootScope.openAlert({summary: 'Vui lòng chọn lý do hủy đơn!', timeout: 2000});
    }
  };

  $scope.close = function() {
    console.log('close');
    $uibModalInstance.close('cancel');
  };
})

.factory('dlgCancelOrder', function($uibModal, helper) {
  return {
    show: function(fn, id) {
      return $uibModal.open({
        animation: true,
        backdrop: 'static',
        templateUrl: 'dialogs/dlgCancelOrder/dlgCancelOrder.tpl.html',
        controller: 'DlgCancelOrderCtrl',
        size: 'md custom-modal modal-dialog-centered',
        resolve: {
          $data: function() {
            return id;
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
