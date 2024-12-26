angular.module('app.dlgAddressList', [])

.controller('DlgAddressListCtrl', function($scope, $rootScope, $uibModalInstance, utils, locations, users, addresses, $data) {
  var vm = this;
  vm.labels = angular.extend({}, window.langtext);
  vm.address_id = $data.address_id;
  $scope.labels = vm.labels;
  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false, mode: 'list'};
  $scope.params = {
    first_name: '',
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
  $scope.provinceList = [];
  $scope.districtList = [];
  $scope.wardList = [];
  $scope.streetList = [];
  $scope.addressList = $data.addressList;
  $scope.addressSelected = null;

  $scope.close = function() {
    $uibModalInstance.dismiss('cancel');
  };

  $scope.back = function() {
    $scope.envData.mode = 'list';
  };

  $scope.selectAddress = function(item) {
    $uibModalInstance.close(item);
    $.ajax({
      url: '/checkout/shipping/save',
      type: 'post',
      data: $.param({address_id: item.id, data: item}),
      dataType: 'json',
      beforeSend: function() {
        // $($event.currentTarget).button('loading');
      },
      complete: function() {
        // $($event.currentTarget).button('reset');
      },
      success: function(json) {
        // $uibModalInstance.close(json);
      }
    });
  };

  $scope.create = function() {
    $scope.envData.mode = 'form';
  };

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    if (form.$valid) {
      $scope.envData.submitted = true;
      if (!$scope.addressSelected) {
        if (!$rootScope.currentUser) {
          var params = angular.copy($scope.params);
          if ($scope.params.province_id) {
            var province = _.find($scope.provinceList, {id: parseInt($scope.params.province_id)});
            if (province) {
              params.province = province.name;
              params.vt_province_id = province.vt_id;
            }
          }
          if ($scope.params.district_id) {
            var district = _.find($scope.districtList, {id: parseInt($scope.params.district_id)});
            if (district) {
              params.district = district.name;
              params.vt_district_id = district.vt_id;
            }
          }
          
          if ($scope.params.ward_id) {
            var ward = _.find($scope.wardList, {id: parseInt($scope.params.ward_id)});
            if (ward) {
              params.ward = ward.name;
              params.vt_ward_id = ward.vt_id;
            }
          }

          $scope.selectAddress(params);
        } else {
          addresses.createAddress($rootScope.currentUser.id, $scope.params).then(function(res) {
            $scope.envData.showValid = false;
            $scope.envData.submitted = false;
            utils.resetForm(form);
            // $uibModalInstance.close(res);
            console.log(res);

            $scope.addressList.push(res);
            $scope.selectAddress(res);
          }, function(errors) {
            $rootScope.openError(errors[0].errorMessage);
            $scope.envData.showValid = false;
            $scope.envData.submitted = false;
          });
        }
      } else {
        addresses.updateAddress($scope.addressSelected.id, $scope.params).then(function(res) {
          $scope.envData.showValid = false;
          $scope.envData.submitted = false;
          utils.resetForm(form);
          // $uibModalInstance.close(res);
          var address = _.find($scope.addressList, {id: $scope.addressSelected.id});
          if (address) {
            _.each(res, function(val, key) {
              if (address.hasOwnProperty(key)) {
                address[key] = val;
              }
            });
          }
          $scope.envData.mode = 'list';
          console.log(res);
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
        var district = $scope.params.district_id ? _.find($scope.districtList, {id: parseInt($scope.params.district_id)}) : false;
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
  if (!$rootScope.currentUser) {
    $scope.create();
  }
  //</editor-fold>
})

.factory('dlgAddressList', function($uibModal, helper) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        backdrop: 'static',
        templateUrl: 'dialogs/dlgAddress/dlgAddressList.tpl.html',
        controller: 'DlgAddressListCtrl',
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
