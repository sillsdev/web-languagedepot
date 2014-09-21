'use strict';

// Services
// StudioLive common services
angular.module('sa.services', [ 'ngResource' ])
  .service('ProjectService', [ '$resource', function($resource) {
    return $resource('api/project/:id');
  }])
  ;
