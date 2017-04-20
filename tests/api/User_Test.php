<?php
use GuzzleHttp\Client;
//use PHPUnit_Framework_TestCase;

class UserTest extends PHPUnit_Framework_TestCase
{

    public function testUserProjects_UserUnknown_404()
    {
        $client = ApiTestEnvironment::client();

        $response = $client->post('/api/user/bogus_user/projects', array(
            'headers' => ApiTestEnvironment::headers(),
            'exceptions' => false
        ));
        $this->assertEquals('404', $response->getStatusCode());
        $header = $response->getHeader('Content-Type');
        $this->assertEquals('application/json', $header[0]);
        $result = $response->getBody();
        $result = json_decode($result);
        $this->assertEquals('Unknown user', $result->error);
    }

    public function testUserProjects_UserBadPassword_403()
    {
        $client = ApiTestEnvironment::client();

        $response = $client->post('/api/user/test/projects', array(
            'headers' => ApiTestEnvironment::headers(),
            'exceptions' => false,
            'form-data' => array(
                'password' => 'bogus_password'
            )
        ));
        $this->assertEquals('403', $response->getStatusCode());
        $header = $response->getHeader('Content-Type');
        $this->assertEquals('application/json', $header[0]);
        $result = $response->getBody();
        $result = json_decode($result);
        $this->assertEquals('Bad password', $result->error);
    }

    public function testUserProjects_All_Ok()
    {
        $client = ApiTestEnvironment::client();

        $response = $client->post('/api/user/test/projects', array(
            'headers' => ApiTestEnvironment::headers(),
            'exceptions' => false,
            'form_params' => array(
                'password' => 'tset23'
            )
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $header = $response->getHeader('Content-Type');
        $this->assertEquals('application/json', $header[0]);
        $result = $response->getBody();
        $result = json_decode($result);

        $expected0 = new \stdclass;
        $expected0->identifier = 'testpal-dictionary';
        $expected0->name = 'Test Palaso';
        $expected0->repository = 'http://public.languagedepot.org';
        $expected0->role = 'contributor';
        $expected1 = new \stdclass;
        $expected1->identifier = 'lwl2';
        $expected1->name = 'Eastern Lawa';
        $expected1->repository = 'http://public.languagedepot.org';
        $expected1->role = 'manager';
        $expected = array($expected0, $expected1);
        $this->assertEquals($expected, $result);
    }

    public function testUserProjects_RoleIsManager_Ok()
    {
        $client = ApiTestEnvironment::client();

        $response = $client->post('/api/user/test/projects', array(
            'headers' => ApiTestEnvironment::headers(),
            'exceptions' => false,
            'form_params' => array(
                'password' => 'tset23',
                'role' => 'manager'
            )
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $header = $response->getHeader('Content-Type');
        $this->assertEquals('application/json', $header[0]);
        $result = $response->getBody();
        $result = json_decode($result);

        $expected0 = new \stdclass;
        $expected0->identifier = 'lwl2';
        $expected0->name = 'Eastern Lawa';
        $expected0->repository = 'http://public.languagedepot.org';
        $expected0->role = 'manager';
        $expected = array($expected0);
        $this->assertEquals($expected, $result);
    }

    public function usernameIsAvailable_usernameDoesntExist_true() {
        $client = ApiTestEnvironment::client();

        $nonexistentUsername = 'ran4domuser6543';

        $response = $client->get('/api/user/exists/' . $nonexistentUsername, array(
            'headers' => ApiTestEnvironment::headers()
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $result = $response->getBody();
        $result = json_decode($result);

        $this->assertTrue($result);

    }

    public function usernameIsAvailable_usernameExists_false() {
        $client = ApiTestEnvironment::client();

        $existingUsername = 'test';

        $response = $client->get('/api/user/exists/' . $existingUsername, array(
            'headers' => ApiTestEnvironment::headers()
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $result = $response->getBody();
        $result = json_decode($result);

        $this->assertFalse($result);
    }
}
