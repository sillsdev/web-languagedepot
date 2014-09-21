<?php
use Silex\Provider\TwigServiceProvider;
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Config.php';

function getScripts()
{
    $it = new RecursiveDirectoryIterator('app-ng');
    $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::SELF_FIRST);

    $scripts = array();
    foreach ($it as $file) {
        if ($file->isFile()) {
            $ext = $file->getExtension();
            $isMin = (strpos($file->getPathname(), '-min') !== false);
            if (! $isMin && $ext == 'js') {
                $scripts[] = $file->getPathname();
            }
        }
    }
    return $scripts;
}

$app = new Silex\Application();
$app['debug'] = true; // TODO Set to false for production

$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views'
));

$app->get('/', function () use($app)
{
    $scripts = getScripts();
    return $app['twig']->render('app.twig.html', array(
        'app' => 'ngStatisticsApp',
        'appScripts' => $scripts
    ));
});

$app->run();