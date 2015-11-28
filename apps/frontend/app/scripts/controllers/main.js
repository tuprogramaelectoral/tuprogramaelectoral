'use strict';

/**
 * @ngdoc function
 * @name TPEApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the TPEApp
 */
angular.module('TPEApp')
  .controller('MainCtrl', function ($scope, $filter, Ambito, MiPrograma) {
    $scope.misAmbitos = {};
    $scope.mostrarResumen = false;

    Ambito.findAll().then(function (ambitos) {
      $scope.ambitos = ambitos;
    });

    $scope.seleccionarIntereses = function () {
      MiPrograma.crear($filter('filter')($scope.ambitos, {"elegido": true})).then(function (miPrograma) {
        $scope.miPrograma = miPrograma;
        $('#collapse-ambitos').collapse();
      });
    };

    $scope.elegirPolitica = function (miProgramaId, ambitoId, politicaId) {
      MiPrograma.elegirPolitica(miProgramaId, ambitoId, politicaId).then(function () {
        MiPrograma.cargar(miProgramaId).then(function (miNuevoPrograma) {
          $scope.miPrograma = miNuevoPrograma;
        });
      });
    };

    $scope.$watch("miPrograma", function (newValue, oldValue) {
      if (typeof newValue != 'undefined') {
        if (typeof newValue.proximo_interes != 'undefined') {
          Ambito.findPoliticas(newValue.proximo_interes).then(function (ambito) {
            $scope.misAmbitos[ambito.id] = ambito;
          });
        } else {
          $scope.mostrarResumen = true;
        }
      }
    });
  });
