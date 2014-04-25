<?php
namespace OAuth2Integration\Server\Controllers;

use Silex\Application;

/**
 * Контроллер обрабатывает подтверждение пользователя о предоставлении доступа приложению
 *
 * Class AuthorizationController
 */
class AuthorizationController
{
    static public function addRoutes($routing)
    {
        $routing
            ->get('/authorization', array(new self(), 'authorizationAction'))
            ->bind('authorization');

        $routing
            ->post("/authorization", array(new self(), 'authorizeFormSubmit'))
            ->bind('authorize_post');
    }

    /**
     * Показывает пользователю форму с подтверждением авторизации приложения
     *
     * @param Application $app
     * @return mixed
     */
    public function authorizationAction(Application $app)
    {
        $server = $app['oauth_server'];
        $response = $app['oauth_response'];

        if (!$server->validateAuthorizeRequest($app['request'], $response)) {
            return $server->getResponse();
        }

        /**
         * Рендер формы с подтверждением о предоставлении доступа
         */
        return $app['twig']->render('authorization.twig', array(
                'client_id' => $app['request']->query->get('client_id'),
                'response_type' => $app['request']->query->get('response_type')
            ));
    }

    /**
     * Авторизует приложение
     *
     * @param Application $app
     * @return mixed
     */
    public function authorizeFormSubmit(Application $app)
    {
        $server = $app['oauth_server'];
        $response = $app['oauth_response'];

        $authorized = (bool) $app['request']->request->get('authorize');

        $user = $app['security']->getToken()->getUser();

        return $server->handleAuthorizeRequest($app['request'], $response, $authorized, $user->getUsername());
    }
}