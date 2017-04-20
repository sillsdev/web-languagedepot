<?php
use GuzzleHttp\Client;

class ApiTestEnvironment
{

	/**
	 *
	 * @return \GuzzleHttp\Client
	 */
	public static function client()
	{
		return new Client(array(
			'base_uri' => 'http://localhost:8000'
		), array(
			'request.options' => array(
				'exceptions' => false
			)
		));
	}

	public static function createId()
	{
		return date('YmdHis');
	}

	/**
	 * @return multitype:string
	 */
	public static function headers()
	{
		return array(
		);
	}

	public static function cleanTable($table)
	{
		$sql = 'DELETE FROM ' . $table;
		db_query($sql, "Could not clean table '$table'");
	}

}