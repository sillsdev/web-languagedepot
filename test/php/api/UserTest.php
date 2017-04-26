<?php

//use PHPUnit_Framework_TestCase;

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
        $expected0->name = 'LD Test Dictioñary';
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

    public function testUsernameIsAvailable_usernameDoesntExist_true() {
        $client = ApiTestEnvironment::client();

        $nonexistentUsername = 'ran4domuser6543';

        $response = $client->get(ApiTestEnvironment::url().'/api/user/exists/' . $nonexistentUsername, array(
            'headers' => ApiTestEnvironment::headers()
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $result = $response->getBody();
        $result = json_decode($result);

        $this->assertTrue($result == true);
    }

    public function testUsernameIsAvailable_usernameExists_false() {
        $client = ApiTestEnvironment::client();

        $existingUsername = 'test';

        $response = $client->get(ApiTestEnvironment::url().'/api/user/exists/' . $existingUsername, array(
            'headers' => ApiTestEnvironment::headers()
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $result = $response->getBody();
        $result = json_decode($result);

        $this->assertTrue($result == false);
    }
}
