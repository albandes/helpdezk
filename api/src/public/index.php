<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require_once(str_replace('api/src/public','',dirname(__FILE__)) . '/includes/config/config.php');

$apiConfig['displayErrorDetails'] = true;
$apiConfig['addContentLengthHeader'] = false;

$apiConfig['db']['conn'] = $config["db_connect"]	;
$apiConfig['db']['host'] = $config["db_hostname"];
$apiConfig['db']['port'] = $config["db_port"];
$apiConfig['db']['name'] = $config["db_name"];
$apiConfig['db']['user'] = $config["db_username"];
$apiConfig['db']['pass'] = $config["db_password"];


$app = new \Slim\App(['settings' => $apiConfig]);
$container = $app->getContainer();

/**
 **  Return helpdezk version
 **/
$app->get('/version', function (Request $request, Response $response) use ($app) {
    $return = $response->withJson(['version' => getHelpdezkVersion()], 200);
    return $return;
});

/**
 **  Return helpdezk upload path
 **/
$app->get('/upload_path', function (Request $request, Response $response) use ($app) {
    $pathUpload = str_replace('api/src/public','',dirname(__FILE__)) . "app/uploads" ;
    $return = $response->withJson(['upload_path' => $pathUpload], 200);
    return $return;
});


$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    
});

$app->post('/iot/teste', function (Request $request, Response $response) {
    
	$response->getBody()->write("Hello");

    return $response;			
    
});

$app->post('/iot/temperature/new', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $ticket_data = [];
    $ticket_data['temp'] = filter_var($data['temp'], FILTER_SANITIZE_STRING);
	$temperature = $request->getParam('temp');
	$sensorName = filter_var($request->getParam('sensor-name'), FILTER_SANITIZE_STRING);
	$macAddress = filter_var($request->getParam('mac-address'), FILTER_SANITIZE_STRING);
	$scale = filter_var($request->getParam('scale'), FILTER_SANITIZE_STRING);
	
	$data = [
			//'dttime' => 'NOW()',
			'sensor' => filter_var($request->getParam('sensor-name'), FILTER_SANITIZE_STRING),
			'temperature' => $request->getParam('temp'),
			'mac' => $macAddress,
			'scale' => filter_var($request->getParam('scale'), FILTER_SANITIZE_STRING)
			];
    
	$sql = " 
			INSERT INTO `iot_tbtemperature` (
			  `dttime`,
			  `sensor`,
			  `temperature`,
			  `mac`,
			  `scale`
			) 
			VALUES
			  (
				NOW(),
				:sensor,
				:temperature,
				:mac,
				:scale
			  ) ;
			";
	
	$pdo = $this->db;
	
	try{
		$stmt= $pdo->prepare($sql);
		$stmt->execute($data);
	    return $response->withJson(array('status' => 'true', 'message'=> 'Data insert sucessful'),200);
	}
    catch(\Exception $ex){
       return $response->withJson(array('status' => 'false', 'message' => $ex->getMessage()),422);
    }			
    
});
    
/*
$app->get('/iot/sensor/{sensor-name}/temperature/{param}/scale/{scale}', function (Request $request, Response $response, array $args) {
    $temp = $args['param'];
	$sensor = $args['sensor-name'];
	$scale = $args['scale'];
    $response->getBody()->write("temp:, $temp, sensor: $sensor");

    return $response;
});
*/

$container['db'] = function ($c) {

    $settings = $c->get('settings')['db'];
	
	try{
		$pdo = new PDO('mysql:host=' . $settings['host'] . ';dbname=' . $settings['name'], $settings['user'], $settings['pass']);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		
		return $pdo;
	}
	catch(PDOException $ex){
		die(json_encode(array('outcome' => false, 'message' => $ex->getMessage())));
	}
	
};


function getHelpdezkVersion()
{
    $versionFile = str_replace('api/src/public','',dirname(__FILE__)) . "/version.txt" ;
    if ($arquivo = fopen($versionFile, "r")) {
        while (!feof($arquivo)) {
            return fgets($arquivo, 4096);
        }
    } else {
        return '';
    }

}

$app->run();
