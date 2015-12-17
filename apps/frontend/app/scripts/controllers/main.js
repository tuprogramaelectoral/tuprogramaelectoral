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
    MiPrograma,
    Grafico
  ) {

    $scope.crearMiPrograma = function () {
      $cookies.remove('miProgramaId');
      $location.path('/');
      $scope.reset();
      $scope.cargarAmbitos();
    };

    $scope.borrarMiPrograma = function () {
      var programaId = $scope.getProgramaId();
      MiPrograma.borrar(programaId).then(function () {
        $cookies.remove('miProgramaId');
        $location.path('/');
      });
    };

    $scope.cargarAmbitos = function (miPrograma) {
      Ambito.findAll().then(function (ambitos) {
        var nuevosAmbitos = {};
        ambitos.forEach(function (ambito) {
          nuevosAmbitos[ambito.id] = ambito;
        });
        $scope.ambitos = nuevosAmbitos;
        $('#panel-ambitos').removeClass('hidden');
        if (typeof miPrograma != 'undefined') {
          $scope.establecerPrograma(miPrograma);
        } else {
          $('#collapse-ambitos').collapse('show');
        }
      });
    };

    $scope.marcarTodosLosAmbitos = function (accion) {
      for (var ambito in $scope.ambitos) {
        if ($scope.ambitos.hasOwnProperty(ambito)) {
          $scope.ambitos[ambito].elegido = accion;
        }
      }
    };

    $scope.cargarPrograma = function (miProgramaId) {
        MiPrograma.cargar(miProgramaId).then(function (miNuevoPrograma) {
        if (miNuevoPrograma.terminado) {
          if ($location.path() == '/') {
            $location.path('/' + miProgramaId);
          }
          $('#panel-ambitos').addClass('hidden');
          $scope.establecerPrograma(miNuevoPrograma);
          if (!$('#grafico').html()) {
            Grafico.mostrar(miNuevoPrograma.afinidad);
          }
          $('#panel-resultados').removeClass('hidden');
        } else {
          $scope.cargarAmbitos(miNuevoPrograma);
        }
      }, function() {
          $scope.crearMiPrograma();
      });
    };

    $scope.establecerPrograma = function (miPrograma) {
      $('#collapse-ambitos').collapse('hide');
      $scope.miPrograma = miPrograma;
      $scope.miProgramaId = miPrograma.id;
      if (!miPrograma.terminado) {
        $cookies.put('miProgramaId', miPrograma.id, {'expires': new Date(Date.now() + 1.728e+8)});
        miPrograma.intereses.forEach(function (interes) {
          $scope.ambitos[interes].elegido = true;
        });
      }
      $scope.cargarMisPoliticas();
    };

    $scope.cargarMisPoliticas = function () {
      var politicas = $scope.miPrograma.politicas;
      if (typeof politicas != 'undefined') {
        for (var ambito in politicas) {
          var existe = $.grep($scope.misPoliticas, function(e){
              return e.id == politicas[ambito];
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
        $cookies.put('miProgramaId', miPrograma.id, {'expires': new Date(Date.now() + 1.728e+8)});
        $('#collapse-ambitos').collapse('hide');
      });
    };

    $scope.elegirPolitica = function (miProgramaId, ambitoId, politicaId) {
      MiPrograma.elegirPolitica(miProgramaId, ambitoId, politicaId).then(function () {
        $scope.cargarPrograma(miProgramaId);
      });
    };

    $scope.completarPrograma = function (miProgramaId, publico) {
      MiPrograma.completarPrograma(miProgramaId, publico).then(function () {
        $location.path('/' + miProgramaId);
      });
    };

    $scope.reset = function () {
      $scope.misAmbitos = {};
      $scope.misPoliticas = [];
      $scope.miPrograma = undefined;
      $scope.miProgramaId = undefined;
      $scope.miProgramaUrl = $location.absUrl();
      $scope.compartir = 'Esta es mi afinidad con las propuestas electorales';
      $('#panel-resultados').addClass('hidden');
      $('#panel-resumen').addClass('hidden');
    };

    $scope.getProgramaId = function () {
      var miProgramaId = $routeParams.miProgramaId;
      if (typeof $scope.miProgramaId == 'undefined') {
        miProgramaId = $cookies.get('miProgramaId');
      }

      return miProgramaId;
    };

    $scope.cookies = function () {
      if ($cookies.get('cookies') == 'aceptado') {
        $('.cookies').hide();
      } else {
        $cookies.put('cookies', 'aceptado');
      }
    };

    $scope.run = function () {
      var miProgramaId = $scope.getProgramaId();
      if (typeof miProgramaId != 'undefined') {
        $scope.cargarPrograma(miProgramaId)
      } else {
        $scope.cargarAmbitos();
      }
    };

    $scope.$watch("miPrograma", function (newValue, oldValue) {
      if (typeof newValue != 'undefined') {
        $('#panel-resumen').addClass('hidden');
        if (typeof newValue.proximo_interes != 'undefined') {
          Ambito.findPoliticas(newValue.proximo_interes).then(function (ambito) {
            window.knuthShuffle(ambito.politicas);
            $scope.misAmbitos[ambito.id] = ambito;
          });
        } else {
          if (!newValue.terminado) {
            $('#panel-resumen').removeClass('hidden');
          }
        }
      }
    });

    $scope.reset();
    $scope.run();
    $scope.cookies();
  });
