<?php
use GuzzleHttp\Client;
//use PHPUnit_Framework_TestCase;

class ProjectTest extends PHPUnit_Framework_TestCase
{

    public function testPublicRead_Ok()
    {
        $client = ApiTestEnvironment::client();

        // List
        $response = $client->get('/api/project', array(
            'headers' => ApiTestEnvironment::headers()
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $result = $response->getBody();
        $result = json_decode($result);

        $count0 = count($result);
        $this->assertGreaterThan(0, $count0);

        // Get by id
        $id = 3;
        $response = $client->get('/api/project/' . $id, array(
            'headers' => ApiTestEnvironment::headers()
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $result = $response->getBody();
        $result = json_decode($result);

        $expected = new \stdclass();
        $expected->id = $id;
        $expected->name = 'LD API Test Flex';
        $expected->description = 'LD API Test project of random Flex';
        $expected->homepage = '';
        $expected->is_public = 1;
        $expected->parent_id = null;
        $expected->projects_count = 0;
        $expected->created_on = '2009-09-21T02:44:47+0700';
        $expected->updated_on = '2009-09-21T02:44:47+0700';
        $expected->identifier = 'test-th-flex';
        $expected->status = 1;

        $this->assertEquals($expected, $result);
    }

    public function testPrivateRead_Ok()
    {
        $client = ApiTestEnvironment::client();

        // List
        $response = $client->get('/api/project/private', array(
            'headers' => ApiTestEnvironment::headers()
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $result = $response->getBody();
        $result = json_decode($result);

        $count0 = count($result);
        $this->assertGreaterThan(0, $count0);

        // Get by id
        $id = 1;
        $response = $client->get('/api/project/private/' . $id, array(
            'headers' => ApiTestEnvironment::headers()
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $result = $response->getBody();
        $result = json_decode($result);

        $expected = new \stdclass();
        $expected->id = 1;
        $expected->name = 'test';
        $expected->description = '';
        $expected->homepage = '';
        $expected->is_public = 0;
        $expected->parent_id = null;
        $expected->projects_count = 0;
        $expected->created_on = '2009-07-23T09:56:52+0700';
        $expected->updated_on = '2009-07-23T09:56:52+0700';
        $expected->identifier = 'test';
        $expected->status = 1;

        $this->assertEquals($expected, $result);
    }

    public function testProjectCodeExists_CodeExists_False() {
        $client = ApiTestEnvironment::client();

        $existingProjectCode = 'tpi-dictionary';

        $response = $client->get('/api/project/exists/' . $existingProjectCode, array(
            'headers' => ApiTestEnvironment::headers()
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $result = $response->getBody();
        $result = json_decode($result);

        $this->assertFalse($result);
    }

    public function testProjectCodeExist_CodeNoExists_True() {
        $client = ApiTestEnvironment::client();

        $nonexistentProjectCode = 'ran4domproj6543';

        $response = $client->get('/api/project/exists/' . $nonexistentProjectCode, array(
            'headers' => ApiTestEnvironment::headers()
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $result = $response->getBody();
        $result = json_decode($result);

        $this->assertTrue($result);
    }
}
