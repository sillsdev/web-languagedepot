'use strict';

angular.module('ngSignInApp', [
     'ngRoute',
     'ui.bootstrap',
     'sign_in.forgot',
     'sign_in.sign_in',
     'sign_in.join'
   ])
  .config([ '$routeProvider', function($routeProvider) {
    $routeProvider.when('/sign_in', {
      templateUrl : 'app-ng/sign_in/views/sign_in.html',
      controller : 'SignInCtrl'
    });
    $routeProvider.when('/join', {
      templateUrl : 'app-ng/sign_in/views/join.html',
      controller : 'JoinCtrl'
    });
    $routeProvider.when('/forgot', {
      templateUrl : 'app-ng/sign_in/views/forgot.html',
      controller : 'ForgotCtrl'
    });
    $routeProvider.otherwise({
      redirectTo : '/sign_in'
    });
  }])
  .controller('MainCtrl', [ '$scope', function($scope) {
  }])
  ;
