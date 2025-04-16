<?php
require __DIR__ . '/vendor/autoload.php';
error_reporting(E_ERROR | E_WARNING | E_PARSE);

use App\core\App;
use App\core\Controller;
use App\core\Database;

//Load environment settings
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    echo '<div style="color: white; background: red; padding: 10px; border-radius: 5px;">
            File <strong>.env</strong> not found.
          </div>';
    exit;
} elseif (!is_readable($envPath)) {
    echo '<div style="color: white; background: orange; padding: 10px; border-radius: 5px;">
            The <strong>.env</strong> file exists, but does not have read permission.
          </div>';
    exit;
} else {
    // Load .env normally
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

date_default_timezone_set($_ENV['TIME_ZONE']);

$app = new App();