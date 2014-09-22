'use strict';

// Services
// StudioLive common services
angular.module('sa.services', [ 'ngResource' ])
  .service('PublicProjectService', [ '$resource', function($resource) {
    return $resource('api/project/:id');
  }])
  .service('PrivateProjectService', [ '$resource', function($resource) {
    return $resource('api/project/private/:id');
  }])
  ;
