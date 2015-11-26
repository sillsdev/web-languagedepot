<?php
namespace Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Api\Models\Project;
use Api\Models\User;

class UserController
{
    public function getProjectsAccess($login, Request $request)
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
    }
}