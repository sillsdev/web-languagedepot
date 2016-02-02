<?php
namespace Api;

use Silex\ControllerProviderInterface;
use Silex\Application;

class ApiControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $app['project.controller'] = $app->share(function() {
            return new ProjectController();
        });
        $controllers->get('/project/private', 'project.controller:getAllPrivate');
        $controllers->get('/project/private/{id}', 'project.controller:getPrivate');
        $controllers->get('/project', 'project.controller:getAll');
        $controllers->get('/project/{id}', 'project.controller:get');
        $controllers->get('/project/exists/{projectCode}', 'project.controller:projectCodeIsAvailable');

        $app['user.controller'] = $app->share(function() {
            return new UserController();
        });
        $controllers->post('/user/{login}/projects', 'user.controller:getProjectsAccess');
        $controllers->get('/user/exists/{username}', 'user.controller:usernameIsAvailable');

        return $controllers;
    }
}