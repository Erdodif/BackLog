<?php

use Illuminate\Database\Capsule\Manager;
use Slim\Factory\AppFactory;

require __DIR__ . "/../vendor/autoload.php";

$db = new Manager();
$db->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'loginapp',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_hungarian_ci',
    'prefix' => '',
]);
// Make this Capsule instance available globally via static methods... (optional)
$db->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$db->bootEloquent();

$app = AppFactory::create();

$routes = require "../src/routes.php";
$routes($app);

$app->run();
