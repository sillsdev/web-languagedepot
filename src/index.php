<?php
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Api\ActiveRecordServiceProvider;
use Api\ApiControllerProvider;
use Site\AssetService;
use Site\UserProvider;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

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

$app->register(new SessionServiceProvider());

$app['user.provider'] = $app->share(function() {
    return new UserProvider();
});
$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/account/admin',
            'security' => true,
            'form' => array(
                'login_path' => '/user#/sign_in',
                'check_path' => '/account/sign_in_check',
                'default_target_path' => '/account/admin',
                'always_use_default_target_path' => false
            ),
            'logout' => array('logout_path' => '/account/logout', 'invalidate_session' => true),
            'users' => $app['user.provider']
            // TODO Add ROLE_ADMIN
        ),
        'account' => array(
            'pattern' => '^/account',
            'security' => true,
            'form' => array(
                'login_path' => '/user#/sign_in',
                'check_path' => '/account/sign_in_check',
                'default_target_path' => '/account',
                'always_use_default_target_path' => false
            ),
            'logout' => array('logout_path' => '/account/logout', 'invalidate_session' => true),
            'users' => $app['user.provider']
            // TODO Add ROLE_USER
        )
    ),
));

$app['security.encoder.digest'] = $app->share(function (Silex\Application $app) {
    // use the sha1 algorithm
    // don't base64 encode the password
    // use only 1 iteration
    return new MessageDigestPasswordEncoder('sha1', false, 1);
});
$app['assets.service'] = $app->share(function ()
{
    return new AssetService();
});

$app->before(function (Request $request)
{
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->mount('/api', new ApiControllerProvider())
    ->before(function (Request $request, Silex\Application $app)
    {
        $root = '/api';
        if (strncmp($request->getRequestUri(), $root, strlen($root) === 0)) {
            $app->error(function (\Exception $e, $code) use($app)
            {
                // $app['monolog']->addError($e->getMessage());
                // $app['monolog']->addError($e->getTraceAsString());
                return new JsonResponse(array(
                    'statusCode' => $code,
                    'message' => $e->getMessage(),
                    'stacktrace' => $e->getTraceAsString()
                ));
            });
        }
    });

$app->get('/stats', function (Silex\Application $app)
{
    $assetService = $app['assets.service'];
    $scripts = $assetService->scriptFiles('app-ng');
    return $app['twig']->render('app.twig.html', array(
        'app' => 'ngStatisticsApp',
        'appScripts' => $scripts
    ));
});
$app->get('/account/admin', function() {
    return 'admin page';
});
$app->get('/account', function() {
    return 'account page';
});
$app->get('/user', function (Silex\Application $app, Request $request) {
    var_dump(time());
    $error = $app['security.last_error']($request);
    $lastUserName = $app['session']->get('_security.last_username');
    
    var_dump($error, $lastUserName);
    
    $assetService = $app['assets.service'];
    $scripts = $assetService->scriptFiles('app-ng');
    // TODO add the grab bag into the window and have an angular service pick it up.
    return $app['twig']->render('app.twig.html', array(
        'app' => 'ngSignInApp',
        'appScripts' => $scripts
    ));
});
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