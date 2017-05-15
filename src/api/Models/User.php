<?php
namespace Api\Models;

class User extends \ActiveRecord\Model
{
    static $connection = 'public';

    static $has_many = array(
        array('members'),
        array('projects', 'through' => 'members')
    );

    static $table_name = 'users';

    public function passwordCheck($plainPassword) {
        $hashedPassword = sha1($plainPassword);
        return $hashedPassword == $this->hashed_password;
    }

    static public function findByLogin($login)
    {
        return self::first(array('conditions' => array('login = ?', $login)));
    }

    /**
     * Query for user by lowercased email address
     * @param $mail
     * @return \ActiveRecord\Model
     */
    static public function findByMail($mail)
    {
        return self::first(array('conditions' => array('mail = ?', $mail)));
    }
}
