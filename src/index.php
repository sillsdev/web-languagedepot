<?php
use Silex\Provider\TwigServiceProvider;
use Site\AssetService;
use Api\ApiControllerProvider;
use Api\ActiveRecordServiceProvider;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Config.php';

$app = new Silex\Application();
$app['debug'] = true; // TODO Set to false for production

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/site/views'
));
$app->register(new ActiveRecordServiceProvider(), array(
    'ActiveRecord.modelPath' => __DIR__ . '/api/Models',
    'ActiveRecord.connections' => array(
        'public' => 'mysql://' . DB_USER . ':' . DB_PASS . '@localhost/languagedepot',
        'private' => 'mysql://' . DB_USER . ':' . DB_PASS . '@localhost/languagedepotpvt'
    ),
    'ActiveRecord.defaultConnection' => 'public'
));

$app['assets.service'] = $app->share(function() {
    return new AssetService();
});
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->error(function (\Exception $e, $code) use ($app) {
    //     $app['monolog']->addError($e->getMessage());
    //     $app['monolog']->addError($e->getTraceAsString());

    return new JsonResponse(array('statusCode' => $code, 'message' => $e->getMessage(), 'stacktrace' => $e->getTraceAsString()));
});

$app->mount('/api', new ApiControllerProvider());

$app->get('/', function (Silex\Application $app)
{
    $assetService = $app['assets.service'];
    $scripts = $assetService->scriptFiles('app-ng');
    return $app['twig']->render('app.twig.html', array(
        'app' => 'ngStatisticsApp',
        'appScripts' => $scripts
    ));
});

$app->run();