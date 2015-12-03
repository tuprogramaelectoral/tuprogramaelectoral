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
    Ambito,
    Politica,
    MiPrograma
  ) {

    $scope.cargarAmbitos = function () {
      Ambito.findAll().then(function (ambitos) {
        var nuevosAmbitos = {};
        ambitos.forEach(function (ambito) {
          nuevosAmbitos[ambito.id] = ambito;
        });
        $scope.ambitos = nuevosAmbitos;
      });
    };

    $scope.cargarPrograma = function (miProgramaId) {
      MiPrograma.cargar(miProgramaId).then(function (miNuevoPrograma) {
        $scope.miPrograma = miNuevoPrograma;
        $scope.miProgramaId = miNuevoPrograma.id;
        $cookies.put('miProgramaId', miNuevoPrograma.id);
        miNuevoPrograma.intereses.forEach(function (interes) {
          $scope.ambitos[interes].elegido = true;
        });
        $scope.cargarMisPoliticas();
      }, function() {
        $cookies.remove('miProgramaId');
        $location.path('/');
        $('#collapse-ambitos').collapse('show');
      });
    };

    $scope.cargarMisPoliticas = function () {
      var politicas = $scope.miPrograma.politicas;
      if (typeof politicas != 'undefined') {
        for (var ambito in politicas) {
          var existe = $.grep($scope.misPoliticas, function(e){
              return e.id == id;
            }).length > 0;
          if (politicas.hasOwnProperty(ambito) && !existe && typeof $scope.misAmbitos[ambito] == 'undefined') {
            Politica.findOneById(politicas[ambito]).then(function (politica) {
              $scope.misPoliticas.push(politica);
            });
          }
        }
      }
    };

    $scope.seleccionarIntereses = function () {
      var misIntereses = [];
      $('.interes:checked').each(function() {
        misIntereses.push($(this).data('interes-id'));
      });

      MiPrograma.crear(misIntereses).then(function (miPrograma) {
        $scope.reset();
        $scope.miPrograma = miPrograma;
        $scope.miProgramaId = miPrograma.id;
        $cookies.put('miProgramaId', miPrograma.id);
        $('#collapse-ambitos').collapse();
      });
    };

    $scope.elegirPolitica = function (miProgramaId, ambitoId, politicaId) {
      MiPrograma.elegirPolitica(miProgramaId, ambitoId, politicaId).then(function () {
        $scope.cargarPrograma(miProgramaId);
      });
    };

    $scope.reset = function () {
      $scope.misAmbitos = {};
      $scope.misPoliticas = [];
      $scope.mostrarResumen = false;
    };

    $scope.cargarProgramaId = function () {
      $scope.miProgramaId = $routeParams.miProgramaId;
      if (typeof $scope.miProgramaId == 'undefined') {
        $scope.miProgramaId = $cookies.get('miProgramaId');
      }
    };

    $scope.cookies = function () {
      if (typeof $cookies.get('cookies') == 'undefined') {
        $cookies.put('cookies', true);
      } else {
        $('#cookies').hide();
      }
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

    $scope.$watch('ambitos', function (newValue) {
      if (typeof newValue != 'undefined') {
        if (typeof $scope.miProgramaId == 'undefined') {
          $('#collapse-ambitos').collapse('show');
        } else {
          $scope.cargarPrograma($scope.miProgramaId);
        }
      }
    });

    $scope.cookies();
    $scope.reset();
    $scope.cargarProgramaId();
    $scope.cargarAmbitos();
  });
