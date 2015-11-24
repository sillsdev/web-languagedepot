<?php

use GuzzleHttp\Client;

require_once(__DIR__ . '/TestConfig.php');

require_once(TEST_PATH . '/TestEnvironment.php');

class ProjectTest extends PHPUnit_Framework_TestCase
{

	public function testCRUD_Ok()
	{
		$client = TestEnvironment::client();

		// List
		$response = $client->get('/api/project/private', array(
			'headers' => TestEnvironment::headers()
		));
		$this->assertEquals('200', $response->getStatusCode());
		$result = $response->getBody();
		$result = json_decode($result);

		$count0 = count($result);
		//$this->assertEquals(995, $count0);
		
		foreach ($result as $i) {
			if ($i->type == 'unknown') {
				print "$i->identifier $i->name\n";
			}
		}
// 		var_dump($result[5]);

		// Add
// 		$id = TestEnvironment::createId();
		$response = $client->post('/api/project', array(
			'headers' => TestEnvironment::headers(),
			'form_params' => array(
				'custname' => 'custname'
			)
		));
		$this->assertEquals('201', $response->getStatusCode());
		$result = $response->getBody();
		$result = json_decode($result);
		$id = $result->debtor_no;

		$expected = new stdClass();

		$this->assertEquals($expected, $result);

		// List again
		$response = $client->get('/api/project', array(
			'headers' => TestEnvironment::headers()
		));
		$this->assertEquals('200', $response->getStatusCode());
		$result = $response->getBody();
		$result = json_decode($result);

		$count1 = count($result);
		$this->assertEquals($count0 + 1, $count1);

		// Get by id
		$response = $client->get('/api/project' . $id, array(
			'headers' => TestEnvironment::headers()
		));
		$this->assertEquals('200', $response->getStatusCode());
		$result = $response->getBody();
		$result = json_decode($result);

		$this->assertEquals($expected, $result);

		// Write back
		$response = $client->put('/api/project' . $id, array(
			'headers' => TestEnvironment::headers(),
			'body' => array(
				'custname' => 'new custname'
			)
		));

		$this->assertEquals('200', $response->getStatusCode());
		$result = $response->getBody();
		$result = json_decode($result);

		// Get by id to read back
		$response = $client->get('/api/project' . $id, array(
			'headers' => TestEnvironment::headers()
		));
		$this->assertEquals('200', $response->getStatusCode());
		$result = $response->getBody();
		$result = json_decode($result);

		$expected->name = 'new custname';

		$this->assertEquals($expected, $result);

		// List again
		$response = $client->get('/modules/api/customers/', array(
			'headers' => TestEnvironment::headers()
		));
		$this->assertEquals('200', $response->getStatusCode());
		$result = $response->getBody();
		$result = json_decode($result);

		$count1 = count($result);
		$this->assertEquals($count0 + 1, $count1);

		$this->assertEquals($id, $result[$count1 - 1]->debtor_no);

		// Delete
		$response = $client->delete('/api/project' . $id, array(
			'headers' => TestEnvironment::headers()
		));
		$this->assertEquals('200', $response->getStatusCode());
		$result = $response->getBody();
		$result = json_decode($result);

		// List again
		$response = $client->get('/api/project', array(
			'headers' => TestEnvironment::headers()
		));

		$this->assertEquals('200', $response->getStatusCode());
		$result = $response->getBody();
		$result = json_decode($result);

		$count2 = count($result);
		$this->assertEquals($count0, $count2);

	}

}
