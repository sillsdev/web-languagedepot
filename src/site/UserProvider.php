<?php
namespace Site;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserProvider implements UserProviderInterface
{

    public function loadUserByUsername($username)
    {
        $user = \Api\Models\User::findByLogin($username);
        if ($user == null) {
            throw new UsernameNotFoundException(sprintf('Username "%s" not found.', $username));
        }
        $roles = array(
            'ROLE_USER'
        );
        if ($user->admin) {
            $roles[] = 'ROLE_ADMIN';
        }
        
        return new User($user->login, $user->hashed_password, $roles,
            true, // enabled
            true, // user not expired
            true, // password not expired
            true  // user not locked
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}