<?php
/*
 * http://www.slimframework.com/docs/v3/tutorial/first-app.html [Using GET Parameters]
 */


//error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ALL);

//error_reporting(0);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require 'apiSystem.php';
require 'apiModel.php';
require 'apiController.php';

$sys = new apiSystem();

define('path', $sys->_getPathDefault());
define('DOCUMENT_ROOT', $sys->_getDocumentRoot());
define('LANGUAGE',$sys->getConfig("lang"));
define ('HELPDEZK_PATH', realpath(DOCUMENT_ROOT.path)) ;
define('SMARTY', $sys->_helpdezkPath. 'includes/smarty/');



// Models
require_once ($sys->_helpdezkPath.'/app/modules/admin/models/index_model.php');
require_once ($sys->getHelpdezkPath().'/system/common.php');
require_once ($sys->getHelpdezkPath().'/app/modules/admin/models/features_model.php');
require_once ($sys->getHelpdezkPath().'/app/modules/admin/models/person_model.php');
require_once ($sys->_helpdezkPath . 'app/modules/helpdezk/models/ticket_model.php');
require_once ($sys->_helpdezkPath . 'app/modules/helpdezk/models/ticketrules_model.php');
require_once ($sys->_helpdezkPath . '/app/modules/helpdezk/models/expiredate_model.php');
require_once ($sys->_helpdezkPath . '/app/modules/helpdezk/models/groups_model.php');

//require_once ($sys->getHelpdezkPath().'/app/modules/helpdezk/models/requestinsert_model.php');
//require_once ($sys->getHelpdezkPath().'/app/modules/helpdezk/models/operatorview_model.php');
//require_once ($sys->getHelpdezkPath().'/app/modules/helpdezk/models/evaluation_model.php');
//require_once ($sys->getHelpdezkPath().'/app/modules/helpdezk/models/requestinsert_model.php');
//require_once ($sys->getHelpdezkPath().'/app/modules/helpdezk/models/requestrules_model.php');
//require_once ($sys->getHelpdezkPath().'/app/modules/helpdezk/models/request_model.php');
//require_once ($sys->getHelpdezkPath().'/app/modules/helpdezk/models/emailconfig_model.php');

require_once ($sys->getHelpdezkPath().'/system/common.php');

// Controllers
require_once ($sys->getHelpdezkPath().'/app/modules/helpdezk/controllers/hdkCommonController.php');

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);


$app->get('[/{controller}/{action}{params:.*}]', function ($request, $response, $args) {

    global $sys;

    //echo $request->getUri() . '<br>';
    //var_dump( $request->getQueryParams());

    $controller = $args['controller'];
    $action     = $args['action'];

    $queryParams = $request->getQueryParams();

    $classFile = $sys->_helpdezkPath . "api/helpdezk/controllers/{$controller}.php";
    if(!file_exists($classFile)) {
       $check['error'] = 'Controller not found.';
       die( '{"result":' . json_encode($check) . '}');
    }

    include_once $classFile;

    $class = new $controller();
    if(!method_exists($class, "get_" . $action)) {
        $check['error'] = 'Action not found.';
        die( '{"result":' . json_encode($check) . '}');
    }

    $ret = call_user_func_array(array($class, "get_" . $action), array($queryParams));

    echo '{"result":' . json_encode($ret, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . '}';

});



$app->post('[/{params:.*}]', function ($request, $response, $args) {

    global $sys ;

    $getParams = $request->getAttribute('params') ;
    $params = explode('/', $getParams);

    $controller = $params[0];
    $action     = $params[1];

    $classFile = $sys->_helpdezkPath . "api/helpdezk/controllers/{$controller}.php";
    if(!file_exists($classFile)) {
        $check['error'] = 'Controller not found.';
        die( '{"result":' . json_encode($check) . '}');
    }

    include_once $classFile;

    $class = new $controller();
    if(!method_exists($class, "post_" . $action)) {
        $check['error'] = 'Action not found.';
        die( '{"result":' . json_encode($check) . '}');
    }

    $parsedBody = $request->getParsedBody();

    $ret = call_user_func_array(array($class, "post_" . $action), array($parsedBody));

    echo '{"result":' . json_encode($ret, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . '}';

});

$app->run();