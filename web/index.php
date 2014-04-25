<?php
require_once __DIR__.'/../vendor/autoload.php';


ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Настройка приложения
 */
$app = new \Silex\Application();

/**
 * Генерация URL по имения
 */
$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

/**
 * Регистрация шаблонизатора
 */
$app->register(new \Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views',
    ));

/**
 * Регистрация сессии
 */
$app->register(new \Silex\Provider\SessionServiceProvider());
if (!$app['session']->isStarted()) {
    $app['session']->start();
}


$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale' => 'ru_RU',
    'translator.domains' => array(),
));

/**
 * Регистрация сервиса для доступа к базе данных оператора
 */
$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
        'dbs.options' => array(
            'users' => array(
                'driver' => 'pdo_mysql',
                'host' => '127.0.0.1',
                'port' => 6666,
                'dbname' => 'dbname',
                'user' => 'root',
                'password' => '',
            )
        )
    ));

/**
 * Регистрация сервиса безопасности
 */
$app->register(new Silex\Provider\SecurityServiceProvider());
$app['security.firewalls'] = array(
    'login' => array(
        'pattern' => '^/login$',
    ),
    'token' => array(
        'pattern' => '^/token$',
        'security' => false,
    ),
    'secured' => array(
        'pattern' => '^/authorization',
        'form' => array(
            'login_path' => '/login',
            'check_path' => '/authorization/login_check',
            'default_target_path' => '/authorization',
        ),
        'users' => $app->share(function() use ($app) {
                return new \OAuth2Integration\Security\Provider\DemoUserProvider($app['db']);
            })
    ),
);

/**
 * Регистрация сервиса хеширования пароля
 * Для примера считаем, что пароль хранится в базе в открытом виде
 */
$app['security.encoder.digest'] = $app->share(function ($app) {
        // uses the password-compat encryption
        return new \Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder();
    });

$app['debug'] = true;

/**
 * Подключение контроллеров
 */
$app->mount("/", new \OAuth2Integration\Server\Server());

/**
 * Связывание OAuth сервера с Silex
 */
$request = \OAuth2\HttpFoundationBridge\Request::createFromGlobals();

$app->run($request);