'use strict';

/**
 * @ngdoc overview
 * @name TPEApp
 * @description
 * # TPEApp
 *
 * Main module of the application.
 */
angular
  .module('TPEApp', [
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngRoute',
    'ngSanitize',
    'ngTouch',
    'restangular',
    'services'
  ])
  .config(function ($routeProvider) {
    $routeProvider
      .when('/:miProgramaId?', {
        templateUrl: 'views/main.html',
        controller: 'MainCtrl',
        controllerAs: 'main'
      })
      .otherwise({
        redirectTo: '/'
      });
  });

