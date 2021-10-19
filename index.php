<?php
require __DIR__ . '/vendor/autoload.php';
error_reporting(E_ERROR | E_WARNING | E_PARSE);

use App\core\App;
use App\core\Controller;
use App\core\Database;

//Load environment settings
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$app = new App();