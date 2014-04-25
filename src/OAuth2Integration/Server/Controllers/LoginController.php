<?php
namespace OAuth2Integration\Server\Controllers;

use OAuth2\HttpFoundationBridge\Request;
use Silex\Application;
use Symfony\Component\Routing\Router;

/**
 * Контроллер, реализующий аутентификацию пользователя
 *
 * Class LoginController
 */
class LoginController
{
    static public function addRoutes($routing)
    {
        $routing
            ->match('/login', array(new self(), 'loginAction'))
            ->bind('login');
    }

    public function loginAction(Application $app, Request $request)
    {
        /**
         * Рендер формы логина
         */
        return $app['twig']->render('login.html.twig', array(
                'error'         => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
            ));
    }
}