<?php

namespace SilexBlog;

// Various Symfony classes we need in order to write our own UserProvider class
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\DBAL\Connection;

// Class that loads user information from a database and instantiates User objects.
class UserProvider implements UserProviderInterface
{
    private $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    // Load a user object given the user's username
    public function loadUserByUsername($username)
    {
        // Users are stored in a database in the users table.
        $stmt = $this->conn->executeQuery('SELECT * FROM users WHERE username = ?', array(strtolower($username)));

        // If the user doesn't exist, throw an exception.
        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        // Grab all information on the user including his/her roles.
        $db_results = $this->conn->fetchAll('SELECT `roles`.`role` FROM `roles`
                                             INNER JOIN `user_roles` ON
                                             `user_roles`.`role_id` = `roles`.`id`
                                             WHERE `user_roles`.`user_id` = ?', array($user['id']));

        // Put the roles in an array like the User constructor expects.
        $user_roles = array();
        foreach($db_results as $row) {
            $user_roles[] = $row['role'];
        }

        // If the user doesn't have any roles, simply use an empty string.
        if (count($user_roles) == 0) {
            $user_roles = array('');
        }

        // Set the proper status for whether a user is enabled or not.
        if ($user['enabled'] == 1) {
            $user_enabled = true;
        } else {
            $user_enabled = false;
        }

        // Create a new Symfony user object.
        return new User($user['username'], $user['password'], $user_roles, $user_enabled, true, true, true);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}