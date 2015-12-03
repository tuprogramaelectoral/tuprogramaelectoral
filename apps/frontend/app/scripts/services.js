angular.module('services', [])
  .factory('Ambito', ['Restangular', 'config', function (Restangular, config) {
    var restAngular =
      Restangular.withConfig(function (Configurer) {
        Configurer.setBaseUrl(config.restAPI);
      });

    var _ambitos = restAngular.all('ambitos');

    return {
      findAll: function () {
        return _ambitos.getList();
      },
      findPoliticas: function (id) {
        return _ambitos.one(id).get();
      }
    }
  }])
  .factory('Politica', ['Restangular', 'config', function (Restangular, config) {
    var restAngular =
      Restangular.withConfig(function (Configurer) {
        Configurer.setBaseUrl(config.restAPI);
      });

    var _politicas = restAngular.all('politicas');

    return {
      findOneById: function (id) {
        return _politicas.one(id).get();
      }
    }
  }])
  .factory('MiPrograma', ['Restangular', 'config', function (Restangular, config) {
    var restAngular =
      Restangular.withConfig(function (Configurer) {
        Configurer.setBaseUrl(config.restAPI);
      });

    var _misProgramas = restAngular.all('misprogramas');

    return {
      cargar: function (miProgramaId) {
        return _misProgramas.one(miProgramaId).get();
      },
      crear: function (ambitosElegidos) {
        var intereses = {"politicas": {}};
        ambitosElegidos.forEach(function (ambito) {
          intereses["politicas"][ambito] = null;
        });

        return _misProgramas.post(intereses);
      },
      elegirPolitica: function (miProgramaId, ambitoId, politicaId) {
        var seleccion = {"politicas": {}};
        seleccion["politicas"][ambitoId] = politicaId;

        return _misProgramas.one(miProgramaId).post('', seleccion);
      }
    }
  }])
;
