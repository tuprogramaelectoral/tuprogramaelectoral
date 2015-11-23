'use strict';

describe('Controller: MainCtrl', function () {

  // load the controller's module
  beforeEach(module('VSPApp'));

  var MainCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    MainCtrl = $controller('MainCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('ejemplo', function () {
    //expect(MainCtrl.awesomeThings.length).toBe(3);
  });
});
