angular.module('app.dlgMap', [])

.controller('DlgMapCtrl', function($scope, $window, $uibModalInstance, $data) {
  console.log($data);

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

.factory('dlgMap', function($uibModal, helper) {
  return {
    show: function(fn, data) {
      return $uibModal.open({
        animation: true,
        //backdrop: 'static',
        templateUrl: 'dialogs/dlgMap/dlgMap.tpl.html',
        controller: 'DlgMapCtrl',
        size: 'lg modal-dialog-centered modal-map',
        resolve: {
          $data: function() {
            return {lat: data.lat, long: data.lng};
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

.directive('googleMap', function() {
  return {
    restrict: 'EA',
    scope: {
      opt: '=googleMap'
    },
    link: function(scope, element, attrs) {
      if(scope.opt.lat&&scope.opt.long) {
        var latlng = new google.maps.LatLng( scope.opt.lat, scope.opt.long );
        var myOptions = {
          zoom: 15,
          center: latlng,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          draggable: true,
          zoomControl: true,
          mapTypeControl: false,
          streetViewControl: false,
          scrollwheel: false
        };
        var map = new google.maps.Map(element[0], myOptions);
        new google.maps.Marker({
          position: latlng,
          icon: "/assets/demo/images/icons/marker.png",
          map: map
        });

        if($(window).width() <= 576){
          element.css("height","300px");
        }
      }
    }
  };
});
