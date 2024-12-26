angular.module('app.pages.profile', [])

.controller('ProfileCtrl', function($scope, $window) {
  var vm = this;
  $scope.labels = $window['labels'];
  $scope.PATTERNS = PATTERNS;
  $scope.countryList = [];
  $scope.provinceList = [];

  /*vm.getProvinces = function(country_id) {
    locations.getProvinces(country_id).then(function(res) {
      $scope.provinceList = res;
    }, function(errors) {
      console.log(errors);
    });
  };

  vm.getCountries = function() {
    locations.getCountries().then(function(res) {
      $scope.countryList = res;
      vm.getProvinces(1);
    }, function(errors) {
      console.log(errors);
    });
  };*/

  //vm.getCountries();

  $scope.init = function() {
    console.log('ProfileCtrl');
    /*messages.getUnread(false).then(function(totalCount) {
      totalCount = totalCount ? parseInt(totalCount) : 0;
      $('#sms-badge').html(totalCount);
    }, function(errors) {
      console.log(errors);
    });*/
  };
})

.controller('ProfileInfoCtrl', function($scope) {
  $scope.inited = false;

  $scope.init = function() {
    $scope.inited = true;
  };
})

// .controller('ProfileMenuCtrl', function($scope, user_notifies) {
.controller('ProfileMenuCtrl', function($scope) {
  var vm = this;
  $scope.inited = false;
  /*vm.getUnreadTotal = function() {
    user_notifies.getUnreadTotal().then(function(res) {
      $("#notify-badge").html(res ? parseInt(res) : 0);
    }, function(errors) {
      console.log(errors);
    });
  };*/

  $scope.init = function() {
    $scope.inited = true;
    setTimeout(function() {
      // vm.getUnreadTotal();
    }, 100);
  };
})

.controller('ProfileFormCtrl', function($scope, $rootScope, $window, utils, security, dlgChangePass, dlgFullName, dlgGender, dlgBirthday, dlgPhoneNumber, dlgEmail) {
  var vm = this;
  vm.now = new Date();
  $scope.PATTERNS = PATTERNS;
  $scope.inited = false;
  $scope.envData = {showValid: false, submitted: false};
  $scope.affInfo = $rootScope.affInfo;

  // Init Model
  $scope.params = {
    first_name: '',
    phone_number: '',
    email: '',
    gender: 1,
    birthday: '',
    avatar_url: '',
  };

  $rootScope.$on('dlgFullNameParams', function(event, params) {
    $scope.params.first_name = params.first_name;
  });

  $rootScope.$on('dlgGenderParams', function(event, params) {
    $scope.params.gender = params.gender;
  });

  $rootScope.$on('dlgBirthdayParams', function(event, params) {
    $scope.params.birthday = params.birthday;
  });

  $rootScope.$on('dlgPhoneNumberParams', function(event, params) {
    $scope.params.phone_number = params.phone_number;
  });

  $rootScope.$on('dlgEmailParams', function(event, params) {
    $scope.params.email = params.email;
  });
  /*$scope.submit = function(form) {
    $scope.envData.showValid = true;
    console.log($scope.params);
    if(form.$valid) {
      var params = angular.copy($scope.params);
      if (typeof params.birthday === 'object') {
        params.birthday = $scope.params.birthday.format('yyyy-mm-dd');
      }
      delete params.email;
      // $scope.envData.submitted = true;
      security.update(utils.toFormData(params), true).then(function(res) {
        console.log(res);
        $scope.envData.showValid = false;
        // $scope.envData.submitted = false;
        $rootScope.openAlert({summary: 'Cập nhật thành công!', timeout: 5000});
      }, function(errors) {
        $scope.envData.showValid = false;
        // $scope.envData.submitted = false;
        $rootScope.openError(errors[0].errorMessage);
      });
    }
  };*/

  //<editor-fold desc="Date Picker">
  $scope.dateOpts = {
    type: 'birthday'
  };
  //</editor-fold>

  $scope.changePass = function() {
    dlgChangePass.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeFullName = function() {
    dlgFullName.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeGender = function() {
    dlgGender.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeBirthday = function() {
    dlgBirthday.show(function(res) {
      console.log(res);
    });
  };

  $scope.changePhoneNumber = function() {
    dlgPhoneNumber.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeEmail = function() {
    dlgEmail.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeAvatar = function($event) {
    if ($event.files.length) {
      $scope.params.avatar = $event.files[0];
      security.update(utils.toFormData($scope.params), true).then(function(res) {
        $scope.params.avatar_url = res.avatar_url;
        return $scope.$$phase || $scope.$apply();
      }, function(errors) {
        $rootScope.openError(errors[0].errorMessage);
      });
    }
  };

  // Edit Fn
  vm.editInfo = function(item) {
    for (var key in $scope.params) {
      // if(['birthday'].indexOf(key) === -1 && $scope.params.hasOwnProperty(key)) {
      if($scope.params.hasOwnProperty(key)) {
        $scope.params[key] = !_.isNull(item[key]) ? item[key] : '';
      }
    }
    // Upload opts
    // $scope.uploadOpts.imgURL = item.avatar_url;
    // Set Info
    $scope.userInfo = angular.copy(item);
    console.log(item, $scope.params);
  };

  $scope.init = function(page) {
    $scope.inited = true;
    console.log($scope.affInfo.status);
    
    if (parseInt($scope.affInfo.status) === 2 && page === 'affiliate') {
      return $rootScope.openAlert({title: 'Tính năng này đã bị khóa đối với tài khoản của bạn', style: 'ok'});
    }
  };

  vm.editInfo($rootScope.currentUser);
});

