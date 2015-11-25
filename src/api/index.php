<?php
use App\api\Models\Project;
use App\api\Models\User;
use App\api\ActiveRecordServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Config.php';

$app = new Silex\Application();
$app['debug'] = true; // TODO Set to false for production

$app->register(new ActiveRecordServiceProvider(), array(
    'ActiveRecord.modelPath' => __DIR__ . '/Models',
    'ActiveRecord.connections' => array(
        'public' => 'mysql://' . DB_USER . ':' . DB_PASS . '@localhost/languagedepot',
        'private' => 'mysql://' . DB_USER . ':' . DB_PASS . '@localhost/languagedepotpvt'
    ),
    'ActiveRecord.defaultConnection' => 'public'
));

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->error(function (\Exception $e, $code) use ($app) {
//     $app['monolog']->addError($e->getMessage());
//     $app['monolog']->addError($e->getTraceAsString());

    return new JsonResponse(array("statusCode" => $code, "message" => $e->getMessage(), "stacktrace" => $e->getTraceAsString()));
});
    
$app->get('/project/private', function ()
{
    Project::$connection = 'private';
    $projects = Project::all();
    $results = array();
    $fail = array();
    foreach ($projects as $project) {
        $asArray = $project->to_array(array(
            'only' => array(
                'id',
                'identifier',
                'created_on',
                'name'
            )
        ));
        $asArray['name'] = utf8_encode($asArray['name']);
        $asArray['type'] = $project->type();
        $canEncode = json_encode($asArray);
        if ($canEncode === false) {
            // $fail[] = $asArray;
            throw new \Exception("Cannot encode to json");
        } else {
            $results[] = $asArray;
        }
    }
    // var_dump($fail);
    return json_encode($results);
});
$app->get('/project/private/{id}', function ($id) use($app)
{
    Project::$connection = 'private';
    $project = Project::find($id);
    $asArray = $project->to_array();
    $canEncode = json_encode($asArray);
    if ($canEncode === false) {
        throw new \Exception("Cannot encode to json");
    }
    // var_dump($asArray);
    return json_encode($asArray);
});
$app->get('/project', function ()
{
    $projects = Project::all();
    $results = array();
    $fail = array();
    foreach ($projects as $project) {
        $asArray = $project->to_array(array(
            'only' => array(
                'id',
                'identifier',
                'created_on',
                'name'
            )
        ));
        $asArray['name'] = utf8_encode($asArray['name']);
        $asArray['type'] = $project->type();
        $canEncode = json_encode($asArray);
        if ($canEncode === false) {
            // $fail[] = $asArray;
            throw new \Exception("Cannot encode to json");
        } else {
            $results[] = $asArray;
        }
    }
    // var_dump($fail);
    return json_encode($results);
});
$app->get('/project/{id}', function ($id) use($app)
{
    $project = Project::find($id);
    $asArray = $project->to_array();
    $canEncode = json_encode($asArray);
    if ($canEncode === false) {
        throw new \Exception("Cannot encode to json");
    }
    // var_dump($asArray);
    return json_encode($asArray);
});
$app->post('/user/{login}/projects', function ($login, Request $request) use ($app)
{
    $user = User::findByLogin($login);
    if ($user == null) {
        return new JsonResponse(array('error' => 'Unknown user'), 404);
    }
    $password = $request->request->get('password');
    if (!$user->passwordCheck($password)) {
        return new JsonResponse(array('error' => 'Bad password'), 403);
    }
    $role = $request->request->get('role');
    if ($role && $role != 'any') {
        switch ($role) {
            case 'manager':
                $role_id = 3;
                break;
            case 'contributor':
                $role_id = 4;
                break;
            default:
                $role_id = -1;
        }
        $conditions = array('user_id = ? AND role_id = ?', $user->id, $role_id);
    } else {
        $conditions = array('user_id = ?', $user->id);
    }
    
    $projects = Project::find('all', array(
        'joins' => array('members'),
        'select' => 'projects.identifier,projects.name,members.user_id,members.role_id',
        'conditions' => $conditions
    ));
    $result = array();
    foreach($projects as $project) {
        $o = new \stdclass;
        $o->identifier = $project->identifier;
        $o->name = $project->name;
        $o->repository = 'http://public.languagedepot.org';
        switch ($project->role_id) {
            case 3:
                $o->role = 'manager';
                break;
            case 4:
                $o->role = 'contributor';
                break;
            default:
                $o->role = 'unknown';
        }
        
        $result[] = $o;
        
    }
    return new JsonResponse($result, 200);
});

$app->run();