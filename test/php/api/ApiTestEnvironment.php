<?php

use GuzzleHttp\Client;

class ApiTestEnvironment
{
    /**
     * @param string $time
     * @return string
     */
    public static function StripTimeZone($time)
    {
        return strstr($time, '+', true) ?: $time;
    }

    public static function url(){
        return 'http://api.languagedepot.local';
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public static function client()
    {
        return new Client(array(
            'base_uri' => self::url()
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
     * @return array
     */
    public static function headers()
    {
        return array();
    }

    public static function cleanTable($table)
    {
        $sql = 'DELETE FROM ' . $table;
        db_query($sql, "Could not clean table '$table'");
    }
}
