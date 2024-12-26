angular.module('app.dlgAddress', [])

.controller('DlgAddressCtrl', function($scope, $rootScope, $uibModalInstance, utils, locations, users, addresses, $data) {
  var vm = this;
  vm.labels = angular.extend({}, window.langtext);
  $scope.labels = vm.labels;
  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false};
  vm.params = {
    first_name: '',//$rootScope.currentUser.display,
    phone_number: '',
    type: 'home',
    country_id: '',
    province_id: '',
    district_id: '',
    ward_id: '',
    address_1: '',
    address_2: '',
    is_default: 0,
  };
  $scope.params = angular.copy(vm.params);
  $scope.provinceList = [];
  $scope.districtList = [];
  $scope.wardList = [];
  $scope.streetList = [];

  $scope.close = function() {
    $uibModalInstance.dismiss('cancel');
  };

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    console.log($scope.params);
    if (form.$valid) {
      $scope.envData.submitted = true;
      if(!$scope.info) {
        addresses.createAddress($rootScope.currentUser.id, $scope.params).then(function(res) {
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
          utils.resetForm(form);
          $uibModalInstance.close(res);
        }, function(errors) {
          $rootScope.openError(errors[0].errorMessage);
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
        });
      } else {
        addresses.updateAddress($scope.info.id, $scope.params).then(function(res) {
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
          utils.resetForm(form);
          $uibModalInstance.close(res);
        }, function(errors) {
          $rootScope.openError(errors[0].errorMessage);
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
        });
      }
    }
  };

  //<editor-fold desc="Location">
  vm.getProvinces = function() {
    locations.getProvinces().then(function(res) {
      console.log(res);
      $scope.provinceList = res;
      var province = _.find($scope.provinceList, {id: 2});
      if (province) {
        $scope.params.province_id = province.id;
        $scope.changeProvince(province);
      } else if ($scope.provinceList.length > 0) {
        $scope.params.province_id = $scope.provinceList[0].id;
        $scope.changeProvince($scope.provinceList[0]);
      }
    }, function() {
    });
  };

  vm.getDistricts = function(province_id) {
    locations.getDistrictsByProvinceId(province_id).then(function(districtList) {
      $scope.districtList = districtList;
      if ($scope.districtList.length > 0) {
        var district = $scope.params.district_id ? _.find($scope.districtList, {id: parseInt($scope.params.district_id)}): false;
        if (!district) {
          $scope.params.district_id = $scope.districtList[0].id;
          $scope.changeDistrict($scope.districtList[0]);
        } else {
          $scope.changeDistrict(district);
        }
      } else {
        $scope.params.district_id = '';
      }
    }, function() {
    });
  };

  vm.getWards = function(district_id) {
    locations.getWardsByDistrictId(district_id).then(function(res) {
      $scope.wardList = res;
      /*if ($scope.wardList.length > 0) {
        $scope.params.ward_id = $scope.wardList[0].id;
      } else {
        $scope.params.ward_id = '';
      }*/
    }, function() {
    });
  };

  $scope.changeProvince = function(province) {
    var provinceId;
    if (province) {
      provinceId = province.id;
    } else {
      provinceId = $scope.params.province_id;
    }
    vm.getDistricts(provinceId);
  };

  $scope.changeDistrict = function(district) {
    var districtId;
    if (district) {
      districtId = district.id;
    } else {
      districtId = $scope.params.district_id;
      // Reset data
      $scope.wardList = [];
      $scope.params.ward_id = '';
    }
    if (districtId) {
      vm.getWards(districtId);
    }
  };
  vm.getProvinces();
  //</editor-fold>

  // Edit Fn
  vm.editInfo = function(item) {
    for (var key in $scope.params) {
      if ($scope.params.hasOwnProperty(key)) {
        $scope.params[key] = !_.isNull(item[key]) && item[key] !== undefined ? item[key] : '';
      }
    }
    // Set Info
    $scope.info = angular.copy(item);
    $scope.changeProvince();
  };
  $scope.info = null;
  if ($data && $data.info && $data.info.id) {
    vm.editInfo($data.info);
  }
})

.factory('dlgAddress', function($uibModal, helper) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'dialogs/dlgAddress/dlgAddress.tpl.html',
        controller: 'DlgAddressCtrl',
        controllerAs: 'vm',
        size: 'md modal-edit-address modal-dialog-centered',
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
