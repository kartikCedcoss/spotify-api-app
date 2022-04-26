<?php
$t=time();
return  [
   
    'app' => [
        'baseUri'  => getenv('APP_BASE_URI'),
        'env'      => getenv('APP_ENV'),
        'name'     => 'APP_NAME',
        'timezone' => date("hr-m",$t),
        'url'      => getenv('APP_URL'),
        'version'  => getenv('VERSION'),
        'time'     => microtime(true),
    ],
    'database' =>[
        'adapter'     => 'Mysql',
        'host'        => 'mysql-server',
        'username'    => 'root',
        'password'    => 'secret',
        'dbname'      => 'spotify',
        'charset'     => 'utf8',
    ],

];