<?php
use Phalcon\Loader;

$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
      
       
    ]
);

$loader->registerNamespaces(
    [
        'App\Component' => APP_PATH . '/component/'
        
    ]
);

$loader->register();