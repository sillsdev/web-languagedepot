'use strict';

angular.module('sa.projects', [
    'sa.services'
  ])
  .controller('ProjectsCtrl', [ '$scope', 'ProjectService', function($scope, ProjectService) {
    var projects = ProjectService.query(function() {
      console.log(projects);
    });
  }])
  ;
