<?php
use App\api\Models\Project;
use App\api\ActiveRecordServiceProvider;

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
$app->get('/project/private/{id}', function ($id) use ($app)
{
    Project::$connection = 'private';
    $project = Project::find($id);
    $asArray = $project->to_array();
    $canEncode = json_encode($asArray);
    if ($canEncode === false) {
        throw new \Exception("Cannot encode to json");
    }
    //     var_dump($asArray);
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
            	'description',
            	'name',
            )
        ));
        $asArray['name'] = utf8_encode($asArray['name']);
        $asArray['type'] = $project->type();
        $asArray['description'] = utf8_encode($asArray['description']);
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
$app->get('/project/{id}', function ($id) use ($app)
{
    $project = Project::find($id);
    $asArray = $project->to_array();
    $canEncode = json_encode($asArray);
    if ($canEncode === false) {
        throw new \Exception("Cannot encode to json");
    }
    //     var_dump($asArray);
    return json_encode($asArray);
});

$app->run();