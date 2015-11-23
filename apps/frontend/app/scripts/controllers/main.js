'use strict';

/**
 * @ngdoc function
 * @name VSPApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the VSPApp
 */
angular.module('VSPApp')
  .controller('MainCtrl', function ($scope, AmbitoRepositorio) {
    AmbitoRepositorio.findAll().then(function(ambitos) {
      $scope.ambitos = ambitos;
    });
  });
