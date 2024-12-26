angular.module('app.pages.affiliate-ticket-detail', [])

.controller('AffiliateTicketDetailCtrl', function($scope, $rootScope, $window, $interval, PATTERNS, utils, affiliates) {
  var vm = this;
  vm.extensions = ['doc', 'docx', 'jpg', 'pdf', 'png'];

  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false};
  $scope.info = window.info;
  $scope.params = {
    message:'',
    // files
  };
  $scope.files = [];

  $scope.submit = function(form) {
    console.log($scope.params);
    $scope.envData.showValid = true;
    if (form.$valid) {
      var newParams = angular.copy($scope.params);
      var files = [];
      _.forEach($scope.files, function(f) {
        files.push(f.file);
      });
      newParams.files = files;
      $scope.envData.submitted = true;
      affiliates.repTicket($scope.info.id, utils.toFormData(newParams)).then(function() {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        $scope.files = [];
        $scope.params.message = '';
        // $rootScope.openAlert({summary: 'Đã gửi thành công!', timeout: 1500});
        location.reload();
        utils.resetForm(form);

        return $scope.$$phase || $scope.$apply();
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

  vm.upload = function(file) {
    if ($scope.files.length >= 3) {
      return $rootScope.openAlert({summary: 'Số lượng file không quá 3'});
    }
    console.log(file);
    if (file.name.match(/.(jpg|jpeg|png)$/i)) {
      utils.resizeImage(file).then(function(file) {
        utils.fileToDataURL(file).then(function(imgURL) {
          $scope.files.push({file: file, thumb_url: imgURL});
        });
      });
    } else {
      var ext = '';
      var exts = file.name.split('.');
      if (exts.length > 1) {
        ext = exts[1];
        if (ext) {
          if (vm.extensions.indexOf(ext) === -1) {
            ext = 'other';
          }
        }
      }
      if (!ext) {
        ext = 'file_upload';
      }
      $scope.files.push({file: file, thumb_url: '/assets/icons/files/' + ext + '.svg'});
    }
  };

  var timer;
  $scope.upload = function() {
    $('#form-upload').remove();
    $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input name="file" type="file" accept="' + $scope.settings.accept + '"></form>');
    $('#form-upload input[name=\'file\']').trigger('click');
    if (typeof timer != 'undefined') {
      $interval.cancel(timer);
    }
    timer = $interval(function() {
      if ($('#form-upload input[name=\'file\']').val() != '') {
        $interval.cancel(timer);
        var formData = new FormData($('#form-upload')[0]);
        var file = formData.get('file');
        vm.upload(file);
      }
    }, 500);
  };

  $scope.removeFile = function(pos) {
    $scope.files.splice(pos, 1);

    return $scope.$$phase || $scope.$apply();
  };

  $scope.init = function () {
    $scope.inited = true;
  };
});

