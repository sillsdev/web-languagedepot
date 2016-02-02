<?php
namespace Api;

use Api\Models\Project;

class ProjectController
{
    public function __construct()
    {
        
    }
    
    public function getAllPrivate()
    {
        Project::$connection = 'private';
        return $this->getAll();
    }
    
    public function getAll()
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
    }
    
    public function getPrivate($id)
    {
        Project::$connection = 'private';
        return $this->get($id);
    }
    
    public function get($id)
    {
        $project = Project::find($id);
        $asArray = $project->to_array();
        $canEncode = json_encode($asArray);
        if ($canEncode === false) {
            throw new \Exception("Cannot encode to json");
        }
        // var_dump($asArray);
        return json_encode($asArray);
    }

    public function projectCodeIsAvailable($projectCode) {
        // only includes 'public' projects for now
        $project = Project::findByIdentifier($projectCode);
        return json_encode($project == null);
    }
    
}