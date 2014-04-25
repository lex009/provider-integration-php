<?php
namespace OAuth2Integration\Server\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Контроллер отдает данные о пользователе (подписки)
 *
 * Class ResourceController
 */
class ResourceController
{
    static public function addRoutes($routing)
    {
        $routing->get('/profile', array(new self(), 'profileAction'))->bind('profile');
    }

    public function profileAction(Application $app)
    {
        $server = $app['oauth_server'];
        $response = $app['oauth_response'];

        /**
         * Верификация токена
         */
        if (!$server->verifyResourceRequest($app['request'], $response)) {
            return $server->getResponse();
        }

        $token = $server->getAccessTokenData($app['request']);

        /**
         * Идентификатор пользователя в системе оператора
         */
        $username = $token['user_id'];

        $data = array();

        $db = $app['db'];

        /**
         * Здесь может быть запрос к базе данных, выбирающий подписки пользователя
         *
         * $sql = "SELECT * FROM ...";
         * $data = $db->fetchAll($sql, ...)
         */

        return new JsonResponse($data);
    }
}