<?php

session_start();

include '../../includes/config/config.php';
include '../../includes/adodb/adodb.inc.php';

if (substr($path_default, 0, 1) != '/') {
    $path_default = '/' . $path_default;
}
define('path', $path_default);
// no caso localhost document root seria D:/xampp/htdocs
$document_root = $_SERVER['DOCUMENT_ROOT'];
if (substr($document_root, -1) != '/') {
    $document_root = $document_root . '/';
}
define('DOCUMENT_ROOT', $document_root);

$OP = isset($_GET['op']);

// Handles all requests.
function execute() {
    // Simulate slow network connections.
    //sleep(rand(10, 50) * 0.05);
    // Find the operation of this request.
    $op = $GLOBALS['_GET']['op'] ? $GLOBALS['_GET']['op'] : ($GLOBALS['_POST']['op'] ? $GLOBALS['_POST']['op'] : false);

    // Call this operation's handler.
    if ($op and $ret = call_function_if_exists("execute_op_$op")) {
        print to_js($ret);
    }
}

// Calls a function if it exists and returns the function's return value.
function call_function_if_exists($func) {
    if (function_exists($func)) {
        return $func();
    } else {
        return "Function '$func' is not defined.";
    }
}

// Taken from http://api.drupal.org/api/function/drupal_to_js/7 (GPL 2)
function to_js($var) {
    // json_encode() does not escape <, > and &, so we do it with str_replace()
    return str_replace(array("<", ">", "&"), array('\x3c', '\x3e', '\x26'), json_encode($var));
}

// Finds the id parameter and includes the respective widget file.
function include_widget_file() {
    $id = $GLOBALS['_GET']['id'] ? $GLOBALS['_GET']['id'] : ($GLOBALS['_POST']['id'] ? $GLOBALS['_POST']['id'] : '');

    // IDs client-side use hyphen separators.  Server-side they use underscores.
    $id = str_replace('-', '_', $id);

    $filename = "widgets/$id.inc";
    if (file_exists($filename)) {
        include $filename;
        return $id;
    } else {
        return "You need to create a widget file called '$filename'.";
    }
}

// Operation handler for get_widgets_by_column operation.
function execute_op_get_widgets_by_column() {
    include '../../includes/config/config.php';
    include '../../includes/adodb/adodb.inc.php';
    // This would normally be coming from either the database (this user's settings) or a default/initial dashboard configuration.
    $conexao = NewADOConnection('mysqlt');
    if (!$conexao->Connect($db_hostname, $db_username, $db_password, $db_name)) {
        print "<br>Error connecting to database: " . $db->ErrorNo() . " - " . $db->ErrorMsg();
        die();
    }
    $widgets = array(
        array('mq-online-motivo' => 0, 'hdk-avisos' => 0, 'mq-total-online' => 0, 'srv-banda' => 0),
        array('hdk-grafico-sla' => 0, 'hdk-grafico-solicitacoes-mensal' => 0, 'cms-online' => 0),
        array('hdk-solicitacoes' => 0, 'cms-grafico-acessos' => 0, 'cms-adsense' => 0),
    );


    $banco = true;
    if ($banco) {


        $sql = "select widgets from dsh_tbwidgetusuario where idusuario = " . $_SESSION['SES_COD_USUARIO'];

        $rs = $conexao->Execute($sql) or die($conexao->ErrorMsg());
        //die("AQUI");
        //die($sql);
        if ($rs->RecordCount() != 0) {
            $widgets = array();
            $widgets = unserialize(trim($rs->Fields("widgets")));
            return $widgets;
        } else {
            $widgets = array(
                array(),
                array(),
                array()
            );
        }
    }

    return $widgets;
}

// Operation handler for get_widget operation.
function execute_op_get_widget() {
    $id = include_widget_file();
    return call_function_if_exists("widget_$id");
}

// Operation handler for save_columns operation.
function execute_op_save_columns() {
    $cols = $GLOBALS['_POST']['columns'];
    include '../../includes/config/config.php';
    include '../../includes/adodb/adodb.inc.php';
    $conexao = NewADOConnection('mysqlt');
    if (!$conexao->Connect($db_hostname, $db_username, $db_password, $db_name)) {
        print "<br>Error connecting to database: " . $db->ErrorNo() . " - " . $db->ErrorMsg();
        die();
    }
    // Parse out strings "1" and "0" as ints/booleans.
    foreach ($cols as $c => $widgets) {
        foreach ($widgets as $wid => $is_minimized) {
            $cols[$c][$wid] = (int) $is_minimized;
        }
    }
    $banco = true;
    if ($banco) {

        $sql = "
				update dsh_tbwidgetusuario
				set widgets = '" . serialize($cols) . "'
				where idusuario = " . $_SESSION['SES_COD_USUARIO'] . "
			 ";

        $rs = $conexao->Execute($sql) or die($conexao->ErrorMsg());
    }
}

// Operation handler for widget_settings operation.
function execute_op_widget_settings() {
    $id = include_widget_file();
    return call_function_if_exists("widget_{$id}_settings");
}

execute();
