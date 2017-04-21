<?php
use Api\Models\User;
//use PHPUnit_Framework_TestCase;

TestEnvironment::ensureDatabaseConfigured();

class UserModelTest extends PHPUnit_Framework_TestCase
{

    public function testPassword_Ok()
    {
        
        $user = User::find(170);
        $this->assertNotNull($user);

        $plain_password = 'tset23';
        $isAuth = $user->passwordCheck($plain_password);
        $this->assertTrue($isAuth);
        
    }
    
    public function testFindByLogin_Ok()
    {
        $user = User::findByLogin('bogus_login');
        $this->assertNull($user);
        $user = User::findByLogin('test');
        $this->assertNotNull($user);
        $this->assertEquals('test', $user->login);
    }
    
    public function testProjectsJoin_Ok()
    {
        $user = User::findByLogin('test');
        $this->assertNotNull($user);
        
        $projects = $user->projects;
        $c = count($projects);
        $this->assertEquals(2, $c);
        $this->assertEquals('testpal-dictionary', $projects[0]->identifier);
        $this->assertEquals('lwl2', $projects[1]->identifier);
    }
    
}
