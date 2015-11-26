<?php
namespace Api\Models;

class User extends \ActiveRecord\Model
{
    static $connection = 'public';

    static $has_many = array(
        array('members'),
        array('projects', 'through' => 'members')
    );
    
    public function passwordCheck($plainPassword) {
        $hashedPassword = sha1($plainPassword);
        return $hashedPassword == $this->hashed_password;
    }
    
    static public function findByLogin($login)
    {
        return self::first(array('conditions' => array('login = ?', $login)));
    }
    
}