<?php
namespace Api;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ActiveRecordServiceProvider implements ServiceProviderInterface
{

    function register(Application $app)
    {
        $this->app = $app;

        $app['ActiveRecord.init'] = $app->share(function (Application $app)
        {
            \ActiveRecord\Config::initialize(function ($cfg) use($app)
            {
                $cfg->set_model_directory($app['ActiveRecord.modelPath']);
                $cfg->set_connections($app['ActiveRecord.connections']);
                $cfg->set_default_connection($app['ActiveRecord.defaultConnection']);
            });
        });
    }

    function boot(Application $app)
    {
        $this->app['ActiveRecord.init'];
    }
}
