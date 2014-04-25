<?php
namespace OAuth2Integration\Security\Provider;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Класс реализует загрузку пользователя
 */
class DemoUserProvider implements UserProviderInterface
{
    private $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Загрузка пользователя по имени из базы данных
     *
     * @param string $username
     * @return User|UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        /**
         * Загрузка пользователя из базы оператора с примерным видом запроса
         */
        $user = $this->conn->fetchAssoc('SELECT * FROM users WHERE login = ?', array($username));
        if ($user) {
            return new User(
                $user['login'],
                $user['password']
            );
        }

        throw new UsernameNotFoundException();
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