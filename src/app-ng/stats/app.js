'use strict';

angular.module('ngStatisticsApp', [
     'ngRoute',
     'ui.bootstrap',
     'sa.projects'
   ])
  .config([ '$routeProvider', function($routeProvider) {
    $routeProvider.when('/projects', {
      templateUrl : 'app-ng/stats/partials/projects.html',
      controller : 'ProjectsCtrl'
    });
    $routeProvider.when('/project/:projectId', {
      templateUrl : 'app-ng/stats/partials/project.html',
      controller : 'ProjectCtrl'
    });
    $routeProvider.when('/settings', {
      templateUrl : 'app-ng/stats/partials/settings.html',
      controller : 'SettingsCtrl'
    });
    $routeProvider.otherwise({
      redirectTo : '/projects'
    });
  }])
  .controller('MainCtrl', [ '$scope', function($scope) {
  }])
  ;
