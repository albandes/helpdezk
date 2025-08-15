<?php

//Required to autoload
require __DIR__ . '/vendor/autoload.php';
ini_set('display_errors',0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

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

$pspCer = "{$_ENV['PIX_CERTIFICATE_DIR']}05138510000143.cer";
$appCer = "{$_ENV['PIX_CERTIFICATE_DIR']}api-pix-certificado43.key";
$chainCer = "{$_ENV['PIX_CERTIFICATE_DIR']}05138510000143.cer";
echo "PSPCertificate: {$pspCer}<br>APPCertificate: {$appCer}<br>ChainCertificate: {$chainCer}<br><br>";
$fileDir = substr($_ENV['PIX_CERTIFICATE_DIR'],0,-1);
//echo "{$fileDir}<br>";

if(!is_dir($fileDir)) {
    echo "Não é um diretório<br>";
}else{
    if (is_readable($dir)) {
        echo "O diretório tem permissão de leitura.\n";
    } else {
        echo "Sem permissão de leitura.\n";
    }

    if (is_writable($dir)) {
        echo "O diretório tem permissão de escrita.\n";
    } else {
        echo "Sem permissão de escrita.\n";
    }
}

if (file_exists($pspCer)) {
    echo "O {$pspCer} arquivo existe.\n";
} else {
    echo "O {$pspCer} arquivo não existe.\n";
}

if (file_exists($appCer)) {
    echo "O {$appCer} arquivo existe.\n";
} else {
    echo "O {$appCer} arquivo não existe.\n";
}

if (file_exists($chainCer)) {
    echo "O {$chainCer} arquivo existe.\n";
} else {
    echo "O {$chainCer} arquivo não existe.\n";
}

