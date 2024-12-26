angular.module('app.pages.profile-address', [])

.controller('ProfileAddressCtrl', function($scope, $rootScope, $window, users, addresses, dlgAddress) {
  console.log('ProfileAddressCtrl');
  var vm = this;
  $scope.addressList = $window['addressList'] ? $window['addressList'] : [];
  $scope.inited = false;

  vm.getAddresses = function() {
    addresses.getAddressAll($rootScope.currentUser.id, false).then(function(addressList) {
      console.log(addressList);
      $scope.addressList = addressList;
    }, function(errors) {
      console.log(errors);
    });
  };

  $scope.init = function() {
    $scope.inited = true;
    vm.getAddresses();
  };

  $scope.create = function() {
    dlgAddress.show(function(addressInfo) {
      console.log(addressInfo);
      vm.getAddresses();
      return $scope.$$phase || $scope.$apply();
    });
  };

  $scope.edit = function(item) {
    dlgAddress.show(function(addressInfo) {
      console.log(addressInfo);
      vm.getAddresses();
      return $scope.$$phase || $scope.$apply();
    }, {info: item});
  };
});
