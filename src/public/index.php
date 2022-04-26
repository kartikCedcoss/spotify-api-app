<?php
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;
use Phalcon\Loader;
use Phalcon\Events\Manager as EventsManager;
use GuzzleHttp\Client;
use Phalcon\Http\Response\Cookies;
use Phalcon\Session\Adapter\Stream as SessionAdapter;
use Phalcon\Session\Manager as SessionManager;


define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

$container = new FactoryDefault();

$container->setShared('session', function () {
    $session = new SessionManager();
    $files = new SessionAdapter([
        'savePath' => sys_get_temp_dir(),
    ]);
    $session->setAdapter($files);
    $session->start();

    return $session;
});
$container->set(
    'cookies',
    function () {
        $cookies = new Cookies();

        $cookies->useEncryption(false);

        return $cookies;
    }
);


include APP_PATH . '/config/services.php';
include APP_PATH . '/config/loader.php';

$loader = new Loader();
$loader->registerNamespaces(
    [
        'App\Component' => APP_PATH . '/component/'
        
    ]
);

$loader->register();



$container->set('api', new \App\Component\Helper());

$application=new Application($container);
$eventsManager=new EventsManager();

require  BASE_PATH . '/vendor/autoload.php';

   $container->set('client',new Client([
    // Base URI is used with relative requests
    'base_uri' => 'http://httpbin.org',
    // You can set any number of default request options.
    'timeout'  => 2.0,
    ])) ;


$container->set(
    'EventsManager',
    $eventsManager
);

$application->setEventsManager($eventsManager);
try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}