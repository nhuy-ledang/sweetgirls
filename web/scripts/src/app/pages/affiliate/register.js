angular.module('app.pages.affiliate.register', [])

.controller('AffiliateRegisterCtr', function($scope, $rootScope, $window, $interval, utils, helper, affiliates, systems) {
  var vm = this;
  vm.labels = angular.extend({}, $window['labels']);
  vm.settings = angular.extend({}, $window['settings']);
  $scope.info = $rootScope.currentUser;
  $scope.labels = vm.labels;
  $scope.PATTERNS = PATTERNS;
  $scope.inited = false;
  $scope.envData = {showValid: false, submitted: false, disable: false};

  // Init Model
  $scope.params = {
    fullname: $rootScope.currentUser.display,
    email: $rootScope.currentUser.email,
    phone_number: $rootScope.currentUser.phone_number,
    website: $rootScope.currentUser.website,
    id_no: $rootScope.currentUser.id_no,
    tax: $rootScope.currentUser.tax,
    bank_number: $rootScope.currentUser.bank_number,
    card_holder: $rootScope.currentUser.card_holder,
    bank_id: $rootScope.currentUser.bank_id,
    bank_name: $rootScope.currentUser.bank_name,
    id_front: '',
    id_behind: '',
    id_front_url: '/assets/images/cccd_front.png',
    id_behind_url: '/assets/images/cccd_behind.png',
    agree: false,
  };

  vm.getAllBanks = function() {
    systems.getBanks().then(function(bankList) {
      console.log(bankList);
      $scope.bankList = bankList;
    }, function(errors) {
      console.log(errors);
    });
  };
  vm.getAllBanks();

  vm.upload = function(file, position) {
    if (file.name.match(/.(jpg|jpeg|png)$/i)) {
      utils.resizeImage(file).then(function(file) {
        utils.fileToDataURL(file).then(function(dataURL) {
          if (position === 'front') {
            $scope.params.id_front = file;
            $scope.params.id_front_url = dataURL;
          } else {
            $scope.params.id_behind = file;
            $scope.params.id_behind_url = dataURL;
          }
          return $scope.$$phase || $scope.$apply();
        });
      });
    } else {
      $rootScope.openAlert({summary: 'File không đúng định dạng!', timeout: 2000});
    }
  };

  var timer;
  $scope.upload = function(position) {
    $('#form-upload').remove();
    $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input name="' + position + '" type="file" accept="image/png,image/jpeg"></form>');
    $('#form-upload input[name=' + position + ']').trigger('click');
    if (typeof timer != 'undefined') {
      $interval.cancel(timer);
    }
    timer = $interval(function() {
      if ($('#form-upload input[name=' + position + ']').val() != '') {
        $interval.cancel(timer);
        var formData = new FormData($('#form-upload')[0]);
        var file = formData.get(position);
        vm.upload(file, position);
      }
    }, 500);
  };

  $scope.onSubmit = function(form) {
    console.log($scope.params);
    $scope.envData.showValid = true;
    if (form.$valid) {
      if (!$scope.params.id_front || !$scope.params.id_behind) {
        return $rootScope.openAlert({summary: 'Hãy cập nhật đầy đủ thông tin CMND/CCCD', timeout: 3500});
      }
      if (!$scope.params.agree) {
        $rootScope.openAlert({summary: 'Bạn chưa đồng ý các điều khoản của chúng tôi', timeout: 3500});
        $scope.error_agree = true;
        return;
      }
      delete $scope.params.id_front_url;
      delete $scope.params.id_behind_url;
      vm.bank_selected = _.find($scope.bankList, {id: parseInt($scope.params.bank_id)});
      $scope.params.bank_name = vm.bank_selected.name;
      var params = angular.copy($scope.params);
      $scope.envData.submitted = true;
      affiliates.create(utils.toFormData(params), true).then(function(res) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $scope.envData.disable = false;
        console.log(res);
        $rootScope.openAlert({message: 'Bạn đã đăng ký tham gia chương trình Affiliate thành công và đang chờ xét duyệt. Chúng tôi sẽ thông báo đến bạn qua email sau khi quá trình xét duyệt hoàn tất.', timeout: 4500});
        setTimeout(function() {
          $window.location = '/';
        },3500);
      }, function(errors) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $rootScope.openError(errors[0].errorMessage);
      });
    }
  };

  $scope.init = function() {
    $scope.inited = true;
  };
});
