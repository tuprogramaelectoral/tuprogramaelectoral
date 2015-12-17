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
    'services',
    '720kb.socialshare'
  ])
  .config(['$routeProvider', function ($routeProvider) {
    $routeProvider
      .when('/politica-de-cookies', {
        templateUrl: 'views/cookies.html'
      })
      .when('/:miProgramaId?', {
        templateUrl: 'views/main.html',
        controller: 'MainCtrl',
        controllerAs: 'main'
      })
      .otherwise({
        redirectTo: '/'
      });
  }]);

