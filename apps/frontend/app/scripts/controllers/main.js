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
    Scope,
    Policy,
    MyProgramme,
    Graphic
  ) {

    $scope.createMyProgramme = function () {
      $cookies.remove('myProgrammeId');
      $location.path('/');
      $scope.reset();
      $scope.loadScopes();
    };

    $scope.deleteMyProgramme = function () {
      MyProgramme.delete($scope.getMyProgrammeId()).then(function () {
        $cookies.remove('myProgrammeId');
        $location.path('/');
      });
    };

    $scope.loadScopes = function (myProgramme) {
      Scope.findAll().then(function (scopes) {
        var newScopes = {};
        scopes.forEach(function (scope) {
          newScopes[scope.id] = scope;
        });
        $scope.scopes = newScopes;
        $('#panel-scopes').removeClass('hidden');
        if (typeof myProgramme !== 'undefined') {
          $scope.setMyProgramme(myProgramme);
        } else {
          $('#collapse-scopes').collapse('show');
        }
      });
    };

    $scope.checkAllScopes = function (action) {
      for (var scope in $scope.scopes) {
        if ($scope.scopes.hasOwnProperty(scope)) {
          $scope.scopes[scope].marked = action;
        }
      }
    };

    $scope.loadMyProgramme = function (myProgrammeId) {
        MyProgramme.findOneById(myProgrammeId).then(function (myNewProgramme) {
        if (myNewProgramme.completed) {
          if ($location.path() === '/') {
            $location.path('/' + myProgrammeId);
          }
          $('#panel-scopes').addClass('hidden');
          $scope.setMyProgramme(myNewProgramme);
          if ($('#graphic').html() === "") {
            $scope.graphic = Graphic.show(myNewProgramme.party_affinity);
          }
          $('#panel-results').removeClass('hidden');
        } else {
          $scope.loadScopes(myNewProgramme);
        }
      }, function() {
          $scope.createMyProgramme();
      });
    };

    $scope.setMyProgramme = function (myProgramme) {
      $('#collapse-scopes').collapse('hide');
      $scope.myProgramme = myProgramme;
      $scope.myProgrammeId = myProgramme.id;
      if (!myProgramme.completed) {
        $cookies.put('myProgrammeId', myProgramme.id, {'expires': new Date(Date.now() + 1.728e+8)});
        myProgramme.interests.forEach(function (interest) {
          $scope.scopes[interest].marked = true;
        });
      }
      $scope.loadMyLinkedPolicies();
    };

    $scope.loadMyLinkedPolicies = function () {
      var policies = $scope.myProgramme.policies;
      if (typeof policies !== 'undefined') {
        for (var scope in policies) {
          var exists = $.grep($scope.myLinkedPolicies, function(e){
              return e.id === policies[scope];
            }).length > 0;
          if (policies.hasOwnProperty(scope) && !exists && typeof $scope.myProgrammeScopes[scope] === 'undefined') {
            Policy.findOneById(policies[scope]).then(function (policy) {
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
        $('#collapse-scopes').collapse('hide');
      });
    };

    $scope.selectLinkedPolicy = function (myProgrammeId, scopeId, policyId) {
      MyProgramme.selectLinkedPolicy(myProgrammeId, scopeId, policyId).then(function () {
        $scope.loadMyProgramme(myProgrammeId);
      });
    };

    $scope.completeMyProgramme = function (myProgrammeId, isPublic) {
      MyProgramme.completeMyProgramme(myProgrammeId, isPublic).then(function () {
        $location.path('/' + myProgrammeId);
      });
    };

    $scope.reset = function () {
      $scope.myProgrammeScopes = {};
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
        $scope.loadScopes();
      }
    };

    $scope.$watch("myProgramme", function (newValue, oldValue) {
      if (typeof newValue != 'undefined') {
        $('#panel-summary').addClass('hidden');
        if (typeof newValue.next_interest != 'undefined') {
          Scope.findOneById(newValue.next_interest).then(function (scope) {
            window.knuthShuffle(scope.policies);
            $scope.myProgrammeScopes[scope.id] = scope;
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
