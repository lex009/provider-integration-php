<?php
namespace OAuth2Integration\Server\Controllers;

use Silex\Application;

/**
 * Контроллер отдает токен
 *
 * Class TokenController
 */
class TokenController
{
    static public function addRoutes($routing)
    {
        $routing->post('/token', array(new self(), 'tokenAction'))->bind('grant');
    }

    public function tokenAction(Application $app)
    {
        $server = $app['oauth_server'];
        $response = $app['oauth_response'];

        return $server->handleTokenRequest($app['request'], $response);
    }
}