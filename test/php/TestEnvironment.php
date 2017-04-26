<?php

class TestEnvironment
{
    static public function ensureDatabaseConfigured()
    {
        static $isConnected = false;
        if (!$isConnected) {
            \ActiveRecord\Config::initialize(function ($cfg)
            {
                $cfg->set_model_directory(SRC_PATH . '/api/Models');
                $cfg->set_connections(array(
                    'public' => 'mysql://' . DB_USER . ':' . DB_PASS . '@localhost/languagedepot',
                    'private' => 'mysql://' . DB_USER . ':' . DB_PASS . '@localhost/languagedepotpvt'
                ));
                $cfg->set_default_connection('public');
            });
        }
    }
}