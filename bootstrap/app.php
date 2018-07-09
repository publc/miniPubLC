<?php

require_once 'autoloader.php';
require_once '../vendor/autoload.php';

session_start();

use App\Core\App;
use Bootstrap\Autoloader;

try {
    Autoloader::register();
    $app = new App;
} catch (Exception $e) {
    throw new Exception("Error Processing Request. \n Error: " . $e);
};

require_once '../config/containers.php';

require_once '../routes/web.php';
require_once '../routes/api.php';

$app->run();
