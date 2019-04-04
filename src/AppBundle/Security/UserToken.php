<?php
/**
 *  UserToken Class for creating and saving UserToken for User related Operations.
 */

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class UserToken extends AbstractToken
{
    /**
     * @param string|object  $user   The username (like a nickname, email address, etc.),
     * or a UserInterface instance or an object implementing a __toString method
     * @param array $roles  An array of roles
     *
     */
    public function __construct($user, $roles = array())
    {
        parent::__construct($roles);
        parent::setAuthenticated(count($roles) > 0);
        parent::setUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthenticated($isAuthenticated)
    {
        if ($isAuthenticated) {
            throw new \LogicException('Cannot set this token to trusted after instantiation.');
        }

        parent::setAuthenticated(false);
    }

    public function getCredentials()
    {
    }
}