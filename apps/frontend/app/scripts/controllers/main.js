'use strict';

/**
 * @ngdoc function
 * @name TPEApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the TPEApp
 */
angular.module('TPEApp')
  .controller('MainCtrl', function (
    $scope,
    $routeParams,
    $location,
    $cookies,
    $filter,
    Field,
    Policy,
    MyProgramme,
    Graphic
  ) {

    $scope.createMyProgramme = function () {
      $cookies.remove('myProgrammeId');
      $location.path('/');
      $scope.reset();
      $scope.loadFields();
    };

    $scope.deleteMyProgramme = function () {
      MyProgramme.delete($scope.getMyProgrammeId()).then(function () {
        $cookies.remove('myProgrammeId');
        $location.path('/');
      });
    };

    $scope.loadFields = function (myProgramme) {
      Field.findAll().then(function (fields) {
        var newFields = {};
        fields.forEach(function (field) {
          newFields[field.id] = field;
        });
        $scope.fields = newFields;
        $('#panel-fields').removeClass('hidden');
        if (typeof myProgramme !== 'undefined') {
          $scope.setMyProgramme(myProgramme);
        } else {
          $('#collapse-fields').collapse('show');
        }
      });
    };

    $scope.checkAllFields = function (action) {
      for (var field in $scope.fields) {
        if ($scope.fields.hasOwnProperty(field)) {
          $scope.fields[field].marked = action;
        }
      }
    };

    $scope.loadMyProgramme = function (myProgrammeId) {
        MyProgramme.findOneById(myProgrammeId).then(function (myNewProgramme) {
        if (myNewProgramme.completed) {
          if ($location.path() === '/') {
            $location.path('/' + myProgrammeId);
          }
          $('#panel-fields').addClass('hidden');
          $scope.setMyProgramme(myNewProgramme);
          if ($('#graphic').html() === "") {
            $scope.graphic = Graphic.show(myNewProgramme.party_affinity);
          }
          $('#panel-results').removeClass('hidden');
        } else {
          $scope.loadFields(myNewProgramme);
        }
      }, function() {
          $scope.createMyProgramme();
      });
    };

    $scope.setMyProgramme = function (myProgramme) {
      $('#collapse-fields').collapse('hide');
      $scope.myProgramme = myProgramme;
      $scope.myProgrammeId = myProgramme.id;
      if (!myProgramme.completed) {
        $cookies.put('myProgrammeId', myProgramme.id, {'expires': new Date(Date.now() + 1.728e+8)});
        myProgramme.interests.forEach(function (interest) {
          $scope.fields[interest].marked = true;
        });
      }
      $scope.loadMyLinkedPolicies();
    };

    $scope.loadMyLinkedPolicies = function () {
      var policies = $scope.myProgramme.policies;
      if (typeof policies !== 'undefined') {
        for (var field in policies) {
          var exists = $.grep($scope.myLinkedPolicies, function(e){
              return e.id === policies[field];
            }).length > 0;
          if (policies.hasOwnProperty(field) && !exists && typeof $scope.myProgrammeFields[field] === 'undefined') {
            Policy.findOneById(policies[field]).then(function (policy) {
              $scope.myLinkedPolicies.push(policy);
            });
          }
        }
      }
    };

    $scope.selectInterests = function () {
      var myInterests = [];
      $('.interest:checked').each(function() {
        myInterests.push($(this).data('interest-id'));
      });

      MyProgramme.create(myInterests).then(function (myProgramme) {
        $scope.reset();
        $scope.myProgramme = myProgramme;
        $scope.myProgrammeId = myProgramme.id;
        $cookies.put('myProgrammeId', myProgramme.id, {'expires': new Date(Date.now() + 1.728e+8)});
        $('#collapse-fields').collapse('hide');
      });
    };

    $scope.selectLinkedPolicy = function (myProgrammeId, fieldId, policyId) {
      MyProgramme.selectLinkedPolicy(myProgrammeId, fieldId, policyId).then(function () {
        $scope.loadMyProgramme(myProgrammeId);
      });
    };

    $scope.completeMyProgramme = function (myProgrammeId, isPublic) {
      MyProgramme.completeMyProgramme(myProgrammeId, isPublic).then(function () {
        $location.path('/' + myProgrammeId);
      });
    };

    $scope.reset = function () {
      $scope.myProgrammeFields = {};
      $scope.myLinkedPolicies = [];
      $scope.myProgramme = undefined;
      $scope.myProgrammeId = undefined;
      $scope.graphic = undefined;
      $scope.myProgrammeUrl = $location.absUrl();
      $scope.sharingText = 'Esta es mi afinidad con las propuestas electorales';
      $('#panel-results').addClass('hidden');
      $('#panel-summary').addClass('hidden');
    };

    $scope.getMyProgrammeId = function () {
      var myProgrammeId = $routeParams.myProgrammeId;
      if (typeof myProgrammeId === 'undefined') {
        myProgrammeId = $cookies.get('myProgrammeId');
      }

      return myProgrammeId;
    };

    $scope.cookies = function () {
      if ($cookies.get('cookies') === 'accepted') {
        $('.cookies').hide();
      } else {
        $cookies.put('cookies', 'accepted');
      }
    };

    $scope.run = function () {
      var myProgrammeId = $scope.getMyProgrammeId();
      if (typeof myProgrammeId != 'undefined') {
        $scope.loadMyProgramme(myProgrammeId)
      } else {
        $scope.loadFields();
      }
    };

    $scope.$watch("myProgramme", function (newValue, oldValue) {
      if (typeof newValue != 'undefined') {
        $('#panel-summary').addClass('hidden');
        if (typeof newValue.next_interest != 'undefined') {
          Field.findOneById(newValue.next_interest).then(function (field) {
            window.knuthShuffle(field.policies);
            $scope.myProgrammeFields[field.id] = field;
          });
        } else {
          if (!newValue.completed) {
            $('#panel-summary').removeClass('hidden');
          }
        }
      }
    });

    $scope.reset();
    $scope.run();
    $scope.cookies();
  });
