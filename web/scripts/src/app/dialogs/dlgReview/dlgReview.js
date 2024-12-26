angular.module('app.dlgReview', [])

.controller('DlgReviewCtrl', function($scope, $uibModalInstance, $interval, utils, reviews, $data) {
  var vm = this;
  vm.extensions = ['jpg', 'png', 'jpeg'];

  // $scope.info =  window.info;
  $scope.info =  $data;
  $scope.PATTERNS = PATTERNS;
  $scope.envData = {showValid: false, submitted: false, mode: 'review'};
  $scope.params = {
    rating: 1,
    review: '',
  };
  $scope.files = [];

  $scope.submit = function(form) {
    $scope.envData.showValid = true;
    console.log(form);
    if (form.$valid) {
      $scope.envData.submitted = true;
      var newParams = angular.copy($scope.params);
      var files = [];
      _.forEach($scope.files, function(f) {
        files.push(f.file);
      });
      newParams.files = files;
      reviews.create($scope.info.id, utils.toFormData(newParams)).then(function(res) {
        $scope.envData.showValid = false;
        $scope.envData.submitted = false;
        utils.resetForm(form);
        // $scope.params.rating = '';
        // $scope.params.review = '';
        $scope.files = [];
        $scope.envData.mode = 'share';
      }, function(errors) {
        alert('Không thành công!');
      });
    } else {
      alert('Xin nhập đầy đủ thông tin!');
    }
  };

  // Add 100 coins when Share FB
  $scope.createShare = function(id) {
    reviews.createShare(id, true).then(function(res) {
      $uibModalInstance.close(res);
    }, function(errors) {
      alert('Chia sẻ không thành công');
    });
  };

  $scope.close = function() {
    console.log('close');
    $uibModalInstance.close('cancel');
  };

  $scope.onClickStart = function(rating) {
    $scope.params.rating = rating;
    console.log($scope.params);
  };

  vm.upload = function(file) {
    if ($scope.files.length >= 10) {
      return $rootScope.openAlert({summary: 'Số lượng file không quá 10'});
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
})

.factory('dlgReview', function($uibModal, helper, reviews) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        backdrop: 'static',
        templateUrl: 'dialogs/dlgReview/dlgReview.tpl.html',
        controller: 'DlgReviewCtrl',
        size: 'md custom-modal',
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
