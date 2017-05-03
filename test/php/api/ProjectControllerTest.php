<?php
use Api\ProjectController;

//use PHPUnit_Framework_TestCase;

TestEnvironment::ensureDatabaseConfigured();

class ProjectControllerTest extends PHPUnit_Framework_TestCase
{
    public function testGetAllPrivate_CountMatches() {
        $controller = new ProjectController();

        $result = $controller->getAllPrivate();
        $result = json_decode($result);
        foreach($result as $project) {
            $project->created_on = ApiTestEnvironment::StripTimeZone($project->created_on);
        }

        $expected = new \stdclass();
        $expected->id = 1;
        $expected->name = 'LD Test';
        $expected->created_on = '2009-07-23T09:56:52';
        $expected->identifier = 'ld-test';
        $expected->type = 'test';
        $expectedNumPrivateProjects = 7;

        $this->assertEquals($expected, $result[0]);
        $this->assertEquals($expectedNumPrivateProjects, count($result));
    }

    public function testGetAll_CountMatches() {
        $controller = new ProjectController();

        $result = $controller->getAll();
        $result = json_decode($result);
        foreach($result as $project) {
            $project->created_on = ApiTestEnvironment::StripTimeZone($project->created_on);
        }

        $expected = new \stdclass();
        $expected->id = 1;
        $expected->name = 'LD Test';
        $expected->created_on = '2009-07-23T09:56:52';
        $expected->identifier = 'ld-test';
        $expected->type = 'test';
        $expectedNumPublicProjects = 7;

        $this->assertEquals($expected, $result[0]);
        $this->assertEquals($expectedNumPublicProjects, count($result));
    }

    public function testGetPrivate_1stProject() {
        $controller = new ProjectController();

        // Get by id
        $id = 1;
        $result = $controller->getPrivate($id);
        $result = json_decode($result);
        $result->created_on = ApiTestEnvironment::StripTimeZone($result->created_on);
        $result->updated_on = ApiTestEnvironment::StripTimeZone($result->updated_on);

        $expected = new \stdclass();
        $expected->id = $id;
        $expected->id = 1;
        $expected->name = 'LD Test';
        $expected->description = 'LD API Test project';
        $expected->homepage = '';
        $expected->is_public = 0;
        $expected->parent_id = null;
        $expected->projects_count = 0;
        $expected->created_on = '2009-07-23T09:56:52';
        $expected->updated_on = '2017-02-24T09:56:52';
        $expected->identifier = 'ld-test';
        $expected->status = 1;

        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Couldn't find Api\Models\Project with ID=
     */
    public function testGetPrivate_EmptyId_Exception() {
        $controller = new ProjectController();

        // Get by id
        $id = '';
        $result = $controller->getPrivate($id);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Couldn't find Api\Models\Project with ID=2000
     */
    public function testGetPrivate_InvalidId_Exception() {
        $controller = new ProjectController();

        // Get by id
        $id = '2000';
        $result = $controller->getPrivate($id);
        $result =json_decode($result);
    }

    public function testGet_3rdProject() {
        $controller = new ProjectController();

        // Get by id
        $id = 3;
        $result = $controller->get($id);
        $result = json_decode($result);
        $result->created_on = ApiTestEnvironment::StripTimeZone($result->created_on);
        $result->updated_on = ApiTestEnvironment::StripTimeZone($result->updated_on);

        $expected = new \stdclass();
        $expected->id = $id;
        $expected->name = 'LD API Test Flex';
        $expected->description = 'LD API Test FLEx project';
        $expected->homepage = '';
        $expected->is_public = 1;
        $expected->parent_id = null;
        $expected->projects_count = 0;
        $expected->created_on = '2012-09-21T02:44:47';
        $expected->updated_on = '2017-02-24T02:44:47';
        $expected->identifier = 'test-ld-flex';
        $expected->status = 1;

        $this->assertEquals($expected, $result);
    }

    public function testGetProjectAccess_FieldsUTF8Encoded_OK() {
        $controller = new ProjectController();

        // Get by id
        $id = '7';
        $result = $controller->get($id);
        $result = json_decode($result);
        $result->created_on = ApiTestEnvironment::StripTimeZone($result->created_on);
        $result->updated_on = ApiTestEnvironment::StripTimeZone($result->updated_on);

        $expected = new \stdclass();
        $expected->id = $id;
        $expected->name = 'LD API UTF8 Eñcoding';
        $expected->description = 'LD API Test UTF8 Eñcoding project';
        $expected->homepage = '';
        $expected->is_public = 1;
        $expected->parent_id = null;
        $expected->projects_count = 0;
        $expected->created_on = '2016-08-10T07:30:45';
        $expected->updated_on = '2017-03-01T08:10:20';
        $expected->identifier = 'test-ld-ütf8';
        $expected->status = 1;

        $this->assertEquals($expected, $result);
    }

    public function testProjectCodeIsAvailable_CodeExists_False() {
        $controller = new ProjectController();

        $existingProjectCode = 'test-ld-dictionary';
        $result = $controller->projectCodeIsAvailable($existingProjectCode);
        $result = json_decode($result);

        $this->assertFalse($result);
    }

    public function testProjectCodeIsAvailable_CodeNoExists_True() {
        $controller = new ProjectController();

        $nonexistentProjectCode = 'ran4domproj6543';
        $result = $controller->projectCodeIsAvailable($nonexistentProjectCode);
        $result = json_decode($result);

        $this->assertTrue($result);
    }
}
