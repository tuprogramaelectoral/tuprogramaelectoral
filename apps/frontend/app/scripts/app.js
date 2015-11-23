'use strict';

/**
 * @ngdoc overview
 * @name VSPApp
 * @description
 * # VSPApp
 *
 * Main module of the application.
 */
angular
  .module('VSPApp', [
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngRoute',
    'ngSanitize',
    'ngTouch',
    'restangular'
  ])
  .config(function ($routeProvider) {
    $routeProvider
      .when('/', {
        templateUrl: 'views/main.html',
        controller: 'MainCtrl',
        controllerAs: 'main'
      })
      .otherwise({
        redirectTo: '/'
      });
  })
  .factory('AmbitoRepositorio', ['Restangular', function(Restangular) {
    var restAngular =
      Restangular.withConfig(function(Configurer) {
        Configurer.setBaseUrl('http://192.168.99.100/');
      });

    var _ambitos = restAngular.all('ambitos');

    return {
      findAll: function() {
        return _ambitos.getList();
      }
    }
  }])
;
