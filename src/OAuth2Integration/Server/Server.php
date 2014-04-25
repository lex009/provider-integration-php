<?php
namespace OAuth2Integration\Server;

use OAuth2\GrantType\AuthorizationCode;
use OAuth2\Storage\Pdo;
use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\Server as OAuth2Server;
use OAuth2Integration\Server\Controllers\LoginController;
use OAuth2Integration\Server\Controllers\AuthorizationController;
use OAuth2Integration\Server\Controllers\ResourceController;
use OAuth2Integration\Server\Controllers\TokenController;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\Provider\FormServiceProvider;

/**
 * Класс реализует настройку сервера OAuth и подключение контроллеров
 */
class Server implements ControllerProviderInterface
{
    public function setup(Application $app)
    {
        /**
         * Подключение к базе данных, где будут хранится данные oauth (токены, идентификаторы клиента и т.д.)
         */
        $storage = new Pdo(array('dsn' => 'mysql:dbname=oauth;host=localhost'));

        /**
         * Настройка OAuth сервера
         */
        $server = new  OAuth2Server($storage, array(
            'allow_implicit' => true,
        ));
        $server->addGrantType(new AuthorizationCode($storage));

        $app['oauth_server'] = $server;
        $app['oauth_response'] = new BridgeResponse($storage);

        $app->register(new FormServiceProvider());
    }

    /**
     * Подключение контроллеров
     *
     * @param Application $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->setup($app);

        $routing = $app['controllers_factory'];

        LoginController::addRoutes($routing);
        AuthorizationController::addRoutes($routing);
        TokenController::addRoutes($routing);
        ResourceController::addRoutes($routing);

        return $routing;
    }
}