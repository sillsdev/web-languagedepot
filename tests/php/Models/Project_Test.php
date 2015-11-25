<?php
use App\api\Models\Project;
use App\api\Models\User;

require_once (__DIR__ . '/../TestConfig.php');
require_once (__DIR__ . '/../TestEnvironment.php');

TestEnvironment::ensureDatabaseConfigured();

class ProjectModelTest extends PHPUnit_Framework_TestCase
{

    public function testProject_Ok()
    {
        $user = User::findByLogin('test');
        $result = Project::find('all', array(
            'joins' => array('members'),
            'select' => 'projects.identifier,members.user_id,members.role_id',
            'conditions' => array('user_id = ?', $user->id)
        ));
        $this->assertEquals(4, $result[0]->role_id);
        $this->assertEquals(3, $result[1]->role_id);
    }
    
}
