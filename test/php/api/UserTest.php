<?php

use Api\Models\User;
//use PHPUnit_Framework_TestCase;

TestEnvironment::ensureDatabaseConfigured();

class UserTest extends PHPUnit_Framework_TestCase
{
    public function testUserProjects_UserUnknown_404()
    {
        $client = ApiTestEnvironment::client();

        $response = $client->post(ApiTestEnvironment::url().'/api/user/bogus_user/projects', array(
            'headers' => ApiTestEnvironment::headers(),
            'exceptions' => false
        ));
        $this->assertEquals('404', $response->getStatusCode());
        $header = $response->getHeader('Content-Type');
        $this->assertEquals('application/json', $header);
        $result = $response->getBody();
        $result = json_decode($result);
        $this->assertEquals('Unknown user', $result->error);
    }

    public function testUserProjects_UserBadPassword_403()
    {
        $client = ApiTestEnvironment::client();

        $response = $client->post(ApiTestEnvironment::url().'/api/user/test/projects', array(
            'headers' => ApiTestEnvironment::headers(),
            'exceptions' => false,
            'body' => array(
                'password' => 'bogus_password'
            )
        ));
        $this->assertEquals('403', $response->getStatusCode());
        $header = $response->getHeader('Content-Type');
        $this->assertEquals('application/json', $header);
        $result = $response->getBody();
        $result = json_decode($result);
        $this->assertEquals('Bad password', $result->error);
    }

    public function testUserProjects_All_Ok()
    {
        $client = ApiTestEnvironment::client();

        $response = $client->post(ApiTestEnvironment::url().'/api/user/test/projects', array(
            'headers' => ApiTestEnvironment::headers(),
            'exceptions' => false,
            'body' => array(
                'password' => 'tset23'
            )
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $header = $response->getHeader('Content-Type');
        $this->assertEquals('application/json', $header);
        $result = $response->getBody();
        $result = json_decode($result);

        $expected0 = new \stdclass;
        $expected0->identifier = 'test-ld-dictionary';
        $expected0->name = 'LD Test Dictionary';
        $expected0->repository = 'http://public.languagedepot.org';
        $expected0->role = 'contributor';
        $expected1 = new \stdclass;
        $expected1->identifier = 'test-ld-flex';
        $expected1->name = 'LD API Test Flex';
        $expected1->repository = 'http://public.languagedepot.org';
        $expected1->role = 'manager';
        $expected2 = new \stdclass;
        $expected2->identifier = 'test-ld-demo';
        $expected2->name = 'LD API Test Demo';
        $expected2->repository = 'http://public.languagedepot.org';
        $expected2->role = 'unknown'; // languagedepotprogrammer
        $expected = array($expected0, $expected1, $expected2);
        $this->assertEquals($expected, $result);
    }

    public function testUserProjects_RoleIsManager_Ok()
    {
        $client = ApiTestEnvironment::client();

        $response = $client->post(ApiTestEnvironment::url().'/api/user/test/projects', array(
            'headers' => ApiTestEnvironment::headers(),
            'exceptions' => false,
            'body' => array(
                'password' => 'tset23',
                'role' => 'manager'
            )
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $header = $response->getHeader('Content-Type');
        $this->assertEquals('application/json', $header);
        $result = $response->getBody();
        $result = json_decode($result);

        $expected0 = new \stdclass;
        $expected0->identifier = 'test-ld-flex';
        $expected0->name = 'LD API Test Flex';
        $expected0->repository = 'http://public.languagedepot.org';
        $expected0->role = 'manager';
        $expected = array($expected0);
        $this->assertEquals($expected, $result);
    }

    public function testFindByLogin_UsernameDoesntExist_Null()
    {
        $nonexistentUsername = 'ran4domuser6543';
        $user = User::findByLogin($nonexistentUsername);
        $this->assertNull($user);
    }

    public function testFindByLogin_UsernameExist_NotNull()
    {
        $existentUsername = 'test';
        $user = User::findByLogin($existentUsername);
        $this->assertNotNull($user);

        // Check lowercase login
        $existentUsername = 'tEst';
        $user = User::findByLogin($existentUsername);
        $this->assertNotNull($user);
    }

    public function testFindByLogin_UsernameNull_Null()
    {
        $username = null;
        $user = User::findByLogin($username);

        $this->assertNull($user);
    }

    public function testFindByMail_MailDoesntExist_Null()
    {
        $nonexistentMail = 'nonexistent@example.com';
        $user = User::findByMail($nonexistentMail);
        $this->assertNull($user);
    }

    public function testFindByMail_MailExist_NotNull()
    {
        $existentMail = 'test@example.net';
        $user = User::findByMail($existentMail);
        $this->assertNotNull($user);

        // Check lowercase mail
        $existentMail = 'tEst@example.net';
        $user = User::findByMail($existentMail);
        $this->assertNotNull($user);
    }

    public function testFindByMail_MailNull_Null()
    {
        $mail = null;
        $user = User::findByMail($mail);

        $this->assertNull($user);
    }
}
