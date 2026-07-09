<?php
session_start();

// Apenas para testes
//$_SESSION['SES_LOGIN_PERSON'] = 'admin';

// Carrega o autoload do Composer
require_once __DIR__ . '/vendor/autoload.php';

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

// Importa a classe
use App\src\appServices;   // Ajuste para o namespace correto da sua classe

$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Instancia o serviço
    $appSrc = new appServices();

    // Pasta onde será salvo
    $saveMode = $_ENV['S3BUCKET_STORAGE'] ? "aws-s3" : 'disk';
    if ($saveMode == "aws-s3") {
        $bucket = $_ENV['S3BUCKET_NAME'];
        $storagePath = "exp/materials/";
    } else {
        if ($_ENV['EXTERNAL_STORAGE']) {
            $moduleDir = $appSrc->_setFolder($_ENV['EXTERNAL_STORAGE_PATH'] . '/exp/');
            $storagePath = $appSrc->_setFolder($moduleDir . 'exp/');
        } else {
            $storageDir = $appSrc->_setFolder($appSrc->_getHelpdezkPath() . '/storage/');
            $upDir = $appSrc->_setFolder($storageDir . 'uploads/');
            $moduleDir = $appSrc->_setFolder($upDir . '/exp/');
            $storagePath = $appSrc->_setFolder($moduleDir . 'materials/');
        }
    }

    // Chama o método existente
    $resultado = $appSrc->_uploadFile($_FILES, $storagePath);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Teste Upload</title>
</head>
<body>

<h2>Teste de Upload</h2>

<form method="post" enctype="multipart/form-data">

    <input type="file" name="file" required>

    <br><br>

    <button type="submit">
        Enviar
    </button>

</form>

<?php if ($resultado !== null): ?>

    <hr>

    <pre><?php print_r($resultado); ?></pre>

<?php endif; ?>

</body>
</html>