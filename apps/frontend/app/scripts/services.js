angular.module('services', [])
  .factory('Field', ['Restangular', 'config', function (Restangular, config) {
    var restAngular =
      Restangular.withConfig(function (Configurer) {
        Configurer.setBaseUrl(config.restAPI);
      });

    var _fields = restAngular.all('fields');

    return {
      findAll: function () {
        return _fields.getList();
      },
      findOneById: function (id) {
        return _fields.one(id).get();
      }
    }
  }])
  .factory('Policy', ['Restangular', 'config', function (Restangular, config) {
    var restAngular =
      Restangular.withConfig(function (Configurer) {
        Configurer.setBaseUrl(config.restAPI);
      });

    var _policies = restAngular.all('policies');

    return {
      findOneById: function (id) {
        return _policies.one(id).get();
      }
    }
  }])
  .factory('MyProgramme', ['Restangular', 'config', function (Restangular, config) {
    var restAngular =
      Restangular.withConfig(function (Configurer) {
        Configurer.setBaseUrl(config.restAPI);
      });

    var _myProgrammes = restAngular.all('myprogrammes');

    return {
      findOneById: function (myProgrammeId) {
        return _myProgrammes.one(myProgrammeId).get();
      },
      create: function (interests) {
        var data = {"policies": {}};
        interests.forEach(function (field) {
          data["policies"][field] = null;
        });

        return _myProgrammes.post(data);
      },
      delete: function (myProgrammeId) {
        return _myProgrammes.one(myProgrammeId).remove();
      },
      selectLinkedPolicy: function (myProgrammeId, fieldId, policyId) {
        var data = {"policies": {}};
        data["policies"][fieldId] = policyId;

        return _myProgrammes.one(myProgrammeId).post('', data);
      },
      completeMyProgramme: function (myProgrammeId, isPublic) {
        var data = {
          "completed": 1,
          "public": (isPublic) ? 1 : 0,
          "policies": {}
        };

        return _myProgrammes.one(myProgrammeId).post('', data);
      }
    }
  }])
  .factory('Graphic', [function () {
    function _configuration(content) {
      this.size = {
        "canvasHeight": 400,
        "canvasWidth": 590,
        "pieInnerRadius": "50%",
        "pieOuterRadius": "100%"
      };
      this.data = {"sortOrder": "value-asc", "content": content};
      this.labels = {
        "outer": { "pieDistance": 32},
        "mainLabel": { "font": "verdana"},
        "percentage": {"color": "#e1e1e1", "font": "verdana", "decimalPlaces": 0},
        "value": {"color": "#e1e1e1", "font": "verdana"},
        "lines": {"enabled": true, "color": "#cccccc"},
        "truncation": {"enabled": true}
      };
    }

    var _content = {
      'partido-popular': {"label": "PP", "color": "#279FD4"},
      'partido-socialista': {"label": "PSOE", "color": "#E0001A"},
      'ciudadanos': {"label": "Ciudadanos", "color": "#EE8738"},
      'podemos': {"label": "Podemos", "color": "#6C295C"}
    };

    return {
      show: function (affinity) {
        var content = [];
        for (var party in affinity) {
          if (affinity.hasOwnProperty(party)) {
            var data = _content[party];
            if (affinity[party] > 0) {
              data.value = affinity[party];
              content.push(data);
            }
          }
        }

        new d3pie("graphic", new _configuration(content));
      }
    }
  }])
;
