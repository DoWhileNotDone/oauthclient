<?php declare(strict_types=1);

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NullHandler;
use Monolog\Formatter\LineFormatter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager;

use Rakit\Validation\Validator;

$container = $app->getContainer();

$dbsettings = $container->get('settings')['database'];

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($dbsettings);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Database ORM
$container['db'] = function ($c) use ($capsule) {
    return $capsule;
};

// $container['oauthserver'] = function ($container) {
//     // Use the existing database connection, in PDO form, for initialising the
//     // OAuth server
//     $storage= $container->get('db')->getConnection()->getPdo();
//
//     $server = new OAuth2\Server($storage);
//
//     //FIXME: Sort out Oauth Server Configuration
//     $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage)); // or any grant type you like!
//     //$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
//
//     return $server;
// };

$container["logger"] = function ($container) {
    $logname = getenv("LOG_NAME");
    $logger = new Logger($logname);

    $formatter = new LineFormatter(
        "[%datetime%] [%level_name%]: %message% %context%\n",
        null,
        true,
        true
    );

    $rotating = new RotatingFileHandler(__DIR__ . "/../var/logs/{$logname}.log", 0, Logger::DEBUG);
    $rotating->setFormatter($formatter);
    $logger->pushHandler($rotating);

    return $logger;
};

# Views and Templating
# Using Twig View
$container['view'] = function ($c) {

    $settings = $c->get('settings')['renderer'];

    $view = new \Slim\Views\Twig($settings['template_path'], [
        'cache' => $settings['cache_path']
    ]);

    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};

//Add Validation
$container['validator'] = function ($container) {
    return new Validator;
};
