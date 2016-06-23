angular.module('services', [])
  .factory('Scope', ['Restangular', 'config', function (Restangular, config) {
    var restAngular =
      Restangular.withConfig(function (Configurer) {
        Configurer.setBaseUrl(config.restAPI);
      });

    var _scopes = restAngular.all('/elections/' + config.edition + '/scopes');

    return {
      findAll: function () {
        return _scopes.getList();
      },
      findOneById: function (id) {
        return _scopes.one(id).get();
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
      findOneBy: function (scope, party) {
        return _policies.one('election/' + config.edition + '/scope/' + scope + '/party/' + party).get();
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
        var data = {"edition": config.edition, "policies": {}};
        interests.forEach(function (scope) {
          data["policies"][scope] = null;
        });

        return _myProgrammes.post(data);
      },
      delete: function (myProgrammeId) {
        return _myProgrammes.one(myProgrammeId).remove();
      },
      selectLinkedPolicy: function (myProgrammeId, scopeId, policyId) {
        var data = {"policies": {}};
        data["policies"][scopeId] = policyId;

        return _myProgrammes.one(myProgrammeId).post('', data);
      },
      completeMyProgramme: function (myProgrammeId, isPublic) {
        var data = {
          "completed": 'Yes',
          "public": (isPublic) ? 'Yes' : 'No',
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
      'unidos-podemos': {"label": "Unidos Podemos", "color": "#6C295C"}
    };

    function _getContent(party) {
      if (_content.hasOwnProperty(party)) {
        return _content[party];
      }

      return {"label": party, "color": "#" + ('00000'+(Math.random()*(1<<24)|0).toString(16)).slice(-6)}
    }

    return {
      show: function (affinity) {
        var content = [];
        for (var party in affinity) {
          if (affinity.hasOwnProperty(party)) {
            var data = _getContent(party);
            if (affinity[party] > 0) {
              data.value = affinity[party];
              content.push(data);
            }
          }
        }

        return new d3pie("graphic", new _configuration(content));
      }
    }
  }])
;
