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
      borrar: function (miProgramaId) {
        return _misProgramas.one(miProgramaId).remove();
      },
      elegirPolitica: function (miProgramaId, ambitoId, politicaId) {
        var seleccion = {"politicas": {}};
        seleccion["politicas"][ambitoId] = politicaId;

        return _misProgramas.one(miProgramaId).post('', seleccion);
      },
      completarPrograma: function (miProgramaId, publico) {
        var seleccion = {
          "terminado": 1,
          "publico": (publico) ? 1 : 0
        };

        return _misProgramas.one(miProgramaId).post('', seleccion);
      }
    }
  }])
  .factory('Grafico', [function () {
    function _configuracion(contenido) {
      this.size = {
        "canvasHeight": 400,
        "canvasWidth": 590,
        "pieInnerRadius": "50%",
        "pieOuterRadius": "100%"
      };
      this.data = {"sortOrder": "value-asc", "content": contenido};
      this.labels = {
        "outer": { "pieDistance": 32},
        "mainLabel": { "font": "verdana"},
        "percentage": {"color": "#e1e1e1", "font": "verdana", "decimalPlaces": 0},
        "value": {"color": "#e1e1e1", "font": "verdana"},
        "lines": {"enabled": true, "color": "#cccccc"},
        "truncation": {"enabled": true}
      };
    }

    var _contenido = {
      'partido-popular': {"label": "PP", "color": "#279FD4"},
      'partido-socialista': {"label": "PSOE", "color": "#E0001A"},
      'ciudadanos': {"label": "Ciudadanos", "color": "#EE8738"},
      'podemos': {"label": "Podemos", "color": "#6C295C"}
    };

    return {
      mostrar: function (afinidad) {
        var contenido = [];
        for (var partido in afinidad) {
          if (afinidad.hasOwnProperty(partido)) {
            var dato = _contenido[partido];
            if (afinidad[partido] > 0) {
              dato.value = afinidad[partido];
              contenido.push(dato);
            }
          }
        }

        _grafico = new d3pie("grafico", new _configuracion(contenido));
      }
    }
  }])
;
