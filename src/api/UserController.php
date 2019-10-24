<?php
namespace Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Api\Models\Project;
use Api\Models\User;
use ActiveRecord\Connection;
use ActiveRecord\DateTime;

class UserController
{
    public function usernameIsAvailable($username) {
        $user = User::findByLogin($username);
        return ($user == null);
    }

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
            'joins' => ['members', 'LEFT JOIN member_roles ON member_roles.member_id = members.id'],
            'select' => 'projects.identifier,projects.name,members.user_id,member_roles.role_id',
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

    /**
     * Create new user.  login and mail attributes assigned from unique mail.
     * plain-text password is encoded into hashed_password attribute.
     * @param Request $request containing mail and plainPassword
     * @return JsonResponse On success, returns login and mail attributes
     * @throws \Exception
     */
    public function create(Request $request)
    {
        // Check for unique login and mail
        $mail = strtolower($request->get('mail'));
        $user = User::findByMail($mail);
        if ($user != null) {
            return new JsonResponse(array('error' => 'Email has already been taken'), 400);
        }

        $login = $mail;
        $user = User::findByLogin($login);
        if ($user != null) {
            return new JsonResponse(array('error' => 'Login has already been taken'), 400);
        }

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(array('error' => 'Invalid email address'), 400);
        }

        Connection::$datetime_format = 'Y-m-d H:i:s';
        $attributes = array('login' => $login,
                            'hashed_password' => sha1($request->get('plainPassword')),
                            'mail' => $mail,
                            'created_on' => new DateTime());

        $user = User::create($attributes, true);

        $asArray = $user->to_array(array(
            'only' => array(
                'login',
                'mail')));
        $canEncode = json_encode($asArray);
        if ($canEncode === false) {
            // $fail[] = $asArray;
            throw new \Exception("Cannot encode to json");
        } else {
            $results[] = $asArray;
        }
        return new JsonResponse($results, 200);
    }

    /**
     * Update existing user based on current login.
     * Attributes user can update: firstname, lastname, mail, language
     * @param Request $request
     * @return JsonResponse On success, returns login and attributes
     * @throws \Exception
     */
    public function update(Request $request)
    {
        $login = $request->get('login');
        $mail = $request->get('mail');
        $user = User::findByLogin($login);
        if ($user == null) {
            return new JsonResponse(array('error' => 'Unknown user'), 400);
        }

        // If mail is different, check that it is unique
        if (($user->mail != $mail) && (User::findByMail($mail) != null)) {
            return new JsonResponse(array('error' => 'Email has already been taken'), 400);
        }

        // Define what attributes are allowed to be modified
        $allowedAttributes = [
            'firstname' => 1,
            'lastname'  => 2,
            'language'  => 3,
            'mail'      => 4];
        $attributes = array_intersect_key(
            $request->request->all(),
            $allowedAttributes);
        $user->update_attributes($attributes);

        $asArray = $user->to_array(array(
            'only' => [
                'login',
                'firstname',
                'lastname',
                'mail',
                'language']));
        $canEncode = json_encode($asArray);
        if ($canEncode === false) {
            // $fail[] = $asArray;
            throw new \Exception("Cannot encode to json");
        }

        $results[] = $asArray;
        return new JsonResponse($results, 200);
    }
}
