<?php
namespace Api\Models;

class Member extends \ActiveRecord\Model
{
    static $connection = 'public';

    static $belongs_to = array(
        array('project'),
        array('user')
    );
    
}