angular.module('app.dlgVideo', [])

.controller('DlgVideoCtrl', function($scope, $window, $uibModalInstance, $data) {
  $scope.data = {};

  $scope.close = function() {
    console.log('close');
    $uibModalInstance.dismiss('cancel');
  };

  $scope.submit = function(form) {
    console.log(form);
    $uibModalInstance.close($scope.data);
  };

  $scope.info = $data;
})

.factory('dlgVideo', function($uibModal, helper) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'dialogs/dlgVideo/dlgVideo.tpl.html',
        controller: 'DlgVideoCtrl',
        size: 'md modal-dialog-centered',
        resolve: {
          $data: function() {
            return {name: data.name, youtubeId: data.youtubeId};
          }
        }
      }).result.then(function(res) {
        helper.runFnc(fn, res);
      }, function(error) {
        console.log(error);
      });
    }
  };
})

.directive('youtubeIframe', function() {
  return {
    restrict: 'EA',
    scope: {
      yid: '='
    },
    template: '<div class="embed-responsive embed-responsive-16by9"><iframe id="iframe" width="100%" height="100%" src="" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>',
    link: function(scope, element, attrs) {
      console.log(scope);
      element.find('iframe')[0].src = 'https://www.youtube.com/embed/' + scope.yid;
    }
  };
});
