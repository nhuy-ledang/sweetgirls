angular.module('app.pages.affiliate', [
  'app.pages.affiliate.register',
])

.controller('AffiliateFormCtrl', function($scope, $rootScope, $window, $interval, utils, affiliates, dlgTax, dlgFullname, dlgIDNo, dlgIDAddress, dlgIDProvider, dlgIDDate, dlgCardHolder, dlgBankNumber, dlgBankName, dlgWebsite) {
  var vm = this;
  vm.now = new Date();
  $scope.PATTERNS = PATTERNS;
  $scope.inited = false;
  $scope.envData = {showValid: false, submitted: false};

  // Init Model
  $scope.params = {
    tax: '',
    fullname: '',
    id_no: '',
    id_date: '',
    id_provider: '',
    address: '',
    card_holder: '',
    bank_name: '',
    bank_number: '',
    id_front_url: '/assets/images/cccd_front.png',
    id_behind_url: '/assets/images/cccd_behind.png',
    website: '',
  };

  $rootScope.$on('dlgTaxParams', function(event, params) {
    $scope.params.tax = params.tax;
  });

  $rootScope.$on('dlgFullnameParams', function(event, params) {
    $scope.params.fullname = params.fullname;
  });

  $rootScope.$on('dlgIDNoParams', function(event, params) {
    $scope.params.id_no = params.id_no;
  });

  $rootScope.$on('dlgIDAddressParams', function(event, params) {
    $scope.params.address = params.address;
  });

  $rootScope.$on('dlgIDProviderParams', function(event, params) {
    $scope.params.id_provider = params.id_provider;
  });

  $rootScope.$on('dlgIDDateParams', function(event, params) {
    $scope.params.id_date = params.id_date;
  });

  $rootScope.$on('dlgCardHolderParams', function(event, params) {
    $scope.params.card_holder = params.card_holder;
  });

  $rootScope.$on('dlgBankNameParams', function(event, params) {
    $scope.params.bank_name = params.bank_name;
  });

  $rootScope.$on('dlgBankNumberParams', function(event, params) {
    $scope.params.bank_number = params.bank_number;
  });

  $rootScope.$on('dlgWebsiteParams', function(event, params) {
    $scope.params.website = params.website;
  });

  $scope.changeTaxCode = function() {
    dlgTax.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeFullname = function() {
    dlgFullname.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeIDNo = function() {
    dlgIDNo.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeIDAddress = function() {
    dlgIDAddress.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeIDProvider = function() {
    dlgIDProvider.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeIDDate = function() {
    dlgIDDate.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeCardHolder = function() {
    dlgCardHolder.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeBankName = function() {
    dlgBankName.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeBankNumber = function() {
    dlgBankNumber.show(function(res) {
      console.log(res);
    });
  };

  $scope.changeWebsite = function() {
    dlgWebsite.show(function(res) {
      console.log(res);
    });
  };

  vm.upload = function(file, position) {
    if (file.name.match(/.(jpg|jpeg|png)$/i)) {
      utils.resizeImage(file).then(function(file) {
        utils.fileToDataURL(file).then(function() {
          if (position === 'front') {
            $scope.params.id_front = file;
          } else {
            $scope.params.id_behind = file;
          }
          affiliates.updateAffiliate(utils.toFormData($scope.params), true).then(function(res) {
            console.log(res);
            if (position === 'front') {
              $scope.params.id_front_url = res.id_front_url;
            } else {
              $scope.params.id_behind_url = res.id_behind_url;
            }
            return $scope.$$phase || $scope.$apply();
          }, function(errors) {
            $scope.envData.showValid = false;
            $scope.envData.submitted = false;
            $rootScope.openError(errors[0].errorMessage);
          });
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

  // Edit Fn
  vm.editInfo = function(item) {
    for (var key in $scope.params) {
      if($scope.params.hasOwnProperty(key)) {
        $scope.params[key] = !_.isNull(item[key]) && item[key] ? item[key] : $scope.params[key];
      }
    }
    // Set Info
    $scope.affiliateInfo = angular.copy(item);
    console.log(item, $scope.params);
  };

  $scope.init = function() {
    $scope.inited = true;
  };
  vm.editInfo($rootScope.affInfo);
});

