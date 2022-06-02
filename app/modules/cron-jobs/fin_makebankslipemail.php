<?php

$debug = true ;

if ($debug)
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
else
    error_reporting(0);


session_start();

if (isCli()) {
    $CLI = true ;
    ini_set('html_errors', false);
}

$cron_path = getRootPath('cron-jobs') ;
define ('HELPDEZK_PATH', $cron_path) ;

$lineBreak = $CLI ? PHP_EOL : '<br>';

define('SMARTY', HELPDEZK_PATH.'includes/Smarty/');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronSystem.php');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronController.php');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronModel.php');


require_once(HELPDEZK_PATH . 'includes/classes/pipegrep/syslog.php');

require_once (HELPDEZK_PATH.'system/common.php');
require_once(HELPDEZK_PATH . 'app/modules/admin/models/index_model.php');

$cronSystem = new cronSystem();

$cronSystem->setCronSession() ;
$cronSystem->SetEnvironment();
$cronSystem->_tokenOperatorLink = false;

$moduleID =  $cronSystem->getIdModule("Financeiro");

setLogVariables($cronSystem);
$lineBreak = $cronSystem->getLineBreak();

/*
 *  Models
 */
if(!setRequire(HELPDEZK_PATH.'app/modules/fin/models/bankslipemail_model.php',$cronSystem)){exit;}
if(!setRequire(HELPDEZK_PATH.'app/modules/acd/models/acdclass_model.php',$cronSystem)){exit;}
if(!setRequire(HELPDEZK_PATH.'app/modules/admin/models/tracker_model.php',$cronSystem)){exit;}

$dbBankSlip = new bankslipemail_model();
$dbClasses = new acdclass_model();

$competence = date("m/Y");
$where = "AND competencia = '{$competence}' AND a.dtprocess IS NULL";
$order = "ORDER BY a.idcompany";
$limit = "LIMIT 200";
$ret = $dbBankSlip->getBankSlip($where,null,$order,$limit);
if (!$ret['success']) {
    $cronSystem->logIt($ret['message'] . '  - Program: '. __FILE__ ,3,'general',__LINE__);
    exit;
}

$rs = $ret['data'];
if($rs->RecordCount() <= 0){
    $cronSystem->logIt( "No schedule to process. Finish at ".date("d/m/Y H:i:s")." - Competence {$competence} - Program: ". __FILE__ ,6,'general');
    exit;
}

$cronSystem->logIt("Start at ".date("d/m/Y H:i:s")." - Competence {$competence} - Program: ". __FILE__ ,6,'general');

$template = $dbBankSlip->getTemplate('SEND_BATCH_BANKSLIP_EMAIL');
if (!$template['success']) {
    $cronSystem->logIt($template['message'] . " - Program: ". __LINE__ ,3,'general',__LINE__);
    exit;
}
$rsTemplate = $template['data'];

$sendertitle = "Escola Mario Quintana";
$idserver = getFeature('SEND_BANKSLIP_EMAIL_SRV',$dbBankSlip,$cronSystem);
$i = 1;
foreach($ret['data'] as $key=>$value){
    $aCompetence = explode('/',$value['competencia']);
    $value["month"] = $aCompetence[0];
    $INVOICE_DATE = $value["vencimento"];
    $INVOICE = $value['idboleto'];
    $INVOICE_AMT = number_format($value["valor"], 2, ',', '.');

    $discountValue = 0;
    $wDiscount = "WHERE idcompany = {$value["idcompany"]} AND discount_year = {$aCompetence[1]}";
    $retDiscount = $dbBankSlip->getDiscount($wDiscount);
    if (!$retDiscount['success']) {
        $cronSystem->logIt($retDiscount['message'] . ' - Program: '. __FILE__ ,3,'general',__LINE__);
    }

    if($retDiscount['data']->fields('discount_value') && $retDiscount['data']->fields('discount_value') != ''){
        $discountValue = $retDiscount['data']->fields('discount_value');
    }

    if($discountValue > 0){
        $valor_desconto =  round((($value['valor'] * 10)/100),2);
        $valor_cobrado_inv =  $value['valor'] - $valor_desconto;
        $value["valor_desconto"] = number_format($valor_desconto,2,',','.');
        $value["valor_cobrado"] = number_format($valor_cobrado_inv,2,',','.');
    }

    $body = str_replace('"', "'", addslashes($rsTemplate->fields['description'])) . "<br/>";
    eval("\$body = \"$body\";");

    $subject = $rsTemplate->fields['name'];
    eval("\$subject = \"$subject\";");
    $attachment = makeBankslip($value,$cronSystem);

    $push = str_replace('"', "'", addslashes($rsTemplate->fields['template_push']));
    eval("\$push = \"$push\";");

    if($attachment){
        $retSpool = $dbBankSlip->insertSpool($value['idsender'],$sendertitle,$subject,$body,$push,$value['competencia'],$value['idcompany']);
        if (!$retSpool['success']) {
            $cronSystem->logIt($retSpool['message'] . ' - Program: '. __FILE__ ,3,'general',__LINE__);
            continue;
        }

        $retAtt = $dbBankSlip->saveAttachment($retSpool['id'],$attachment['filename'],$attachment['filedir']);
        if (!$retAtt['success']) {
            $cronSystem->logIt($retAtt['message'] . ' - Program: '. __FILE__ ,3,'general',__LINE__);
            continue;
        }

        $where = "AND a.idstudent = {$value['idstudent']} AND e.email != '' AND a.bank_ticket = 'Y'";
        $retRecip = $dbClasses->getParentIDByBind($where);
        if(!$retRecip){
            $cronSystem->logIt("Get e-mail recipient - Program: ". __FILE__ , 3, 'general', __LINE__);
            continue;
        }

        if($retRecip->RecordCount() > 0){
            $cut = 0;
            while(!$retRecip->EOF){

                $recipname = addslashes($retRecip->fields['name']);
                $recipemail = $retRecip->fields['email'];
                $recipPuschID = addslashes($retRecip->fields['cpf']);
                $recipSendType = $retRecip->fields['send_bank_ticket'];

                $insRecipient = $dbBankSlip->insertSpoolRecipient($retSpool['id'],$recipname,$recipemail,$idserver,$recipPuschID,$recipSendType);
                if (!$insRecipient['success']) {
                    $cronSystem->logIt($insRecipient['message'] . ' - Program: '. __FILE__ ,3,'general',__LINE__);
                    continue;
                }

                $makeEmail = $cronSystem->saveTracker($moduleID,$value['sender_email'],$recipemail,$subject,$body);
                if(!$makeEmail){
                    $cronSystem->logIt('Can\'t make E-mail tracker - Program: '. __FILE__ ,3,'general',__LINE__);
                    continue;
                }

                $bindEmail = $dbBankSlip->insertBindEmail($makeEmail,$insRecipient['id'],1);
                if (!$bindEmail['success']) {
                    $cronSystem->logIt($bindEmail['message'] . ' - Program: '. __FILE__ ,3,'general',__LINE__);
                    continue;
                }

                $insRecipientStudent = $dbBankSlip->insertRecipientStudentBind($insRecipient['id'],$retRecip->fields['idstudent']);
                if (!$insRecipientStudent['success']) {
                    $cronSystem->logIt($insRecipientStudent['message'] . ' - program: '. __FILE__ ,3,'general',__LINE__);
                    continue;
                }
                if($cut > 30) die('die here');
                $cut++;
                $retRecip->MoveNext();
            }
        }else{
            $cronSystem->logIt("No recipient to bank slip # {$value['idboleto']} - program: ". __FILE__ ,3,'general',__LINE__);
            continue;
        }

        $updBSlipProc = $dbBankSlip->updateBankSlipProcess($value['idbankslip']);
        if (!$updBSlipProc['success']) {
            $cronSystem->logIt($updBSlipProc['message'] . ' - Program: '. __FILE__ ,3,'general',__LINE__);
            continue;
        }
        $i++;
    }

}

$cronSystem->logIt("Finish at ".date("d/m/Y H:i:s").". Total Records: {$i}. - Program: ". __FILE__ ,6,'general');

die('Ran OK ') ;

function setLogVariables($cronSystem)
{
    $objSyslog = new Syslog();
    $log  = $objSyslog->setLogStatus() ;
    if ($log) {
        $objSyslog->SetFacility(18);
        $cronSystem->_logLevel = $objSyslog->setLogLevel();
        $cronSystem->_logHost = $objSyslog->setLogHost();
        if($cronSystem->_logHost == 'remote')
            $cronSystem->_logRemoteServer = $objSyslog->setLogRemoteServer();
    }
}

function getRootPath ($module)
{

    $path =  str_replace(DIRECTORY_SEPARATOR ."app".DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR . $module ,"",__DIR__ ) ;
    return $path . DIRECTORY_SEPARATOR;
}

function cronSets($cronSystem)
{
    //environment
    global $CLI;
    $cron_path = getRootPath('cron-jobs');

    define('path', $cronSystem->_getPathDefault());

    if ($CLI) {
        define('DOCUMENT_ROOT', substr( $cron_path,0, strpos($cron_path,$cronSystem->pathDefault)).'/');
    } else {
        define('DOCUMENT_ROOT', $cronSystem->_getDocumentRoot());
    }
    define('LANGUAGE',$cronSystem->getConfig("lang"));
    define('HELPDEZK_PATH', realpath(DOCUMENT_ROOT.path)) ;
}

function isCli()
{
    if ( defined('STDIN') )
    {
        return true;
    }

    if ( php_sapi_name() === 'cli' )
    {
        return true;
    }

    if ( array_key_exists('SHELL', $_ENV) ) {
        return true;
    }

    if ( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0)
    {
        return true;
    }

    if ( !array_key_exists('REQUEST_METHOD', $_SERVER) )
    {
        return true;
    }

    return false;
}

function request ($type, $request, $args = false, $cronSystem) {
    if (!$args) {
        $args = array();
    } elseif (!is_array($args)) {
        $args = array($args);
    }

    $url = $cronSystem->getConfig('server_api') . $request;
    
    $c = curl_init();
    curl_setopt($c, CURLOPT_HEADER, 0);
    curl_setopt($c, CURLOPT_VERBOSE, 0);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $url);

    if (count($args)) curl_setopt($c, CURLOPT_POSTFIELDS , http_build_query($args));

    switch ($type) {
        case 'POST':
            curl_setopt($c, CURLOPT_POST, 1);
            break;
        case 'GET':
            curl_setopt($c, CURLOPT_HTTPGET, 1);
            break;
        default:
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, $type);
    }

    $data = curl_exec($c);

    if(!curl_errno($c)) {
        $info = curl_getinfo($c); 
        if ($info['http_code'] == 401) {
            $message = 'Got error, http code: ' . $info['http_code'] . ' - ' . getHttpErrorCode($info['http_code']) ;
            $arrayRet = array('success' => false, 'message' => $message, 'return' => '');
        } else {
            $res = json_decode($data,true);
            $arrayRet = array('success' => true, 'message' => '', 'return' => $res['result']);
        }

    } else {
        $message = 'Error making API request, curl error: ' . getCurlErrorCode(curl_error($c)); echo"{$message}\n";
        $arrayRet = array('success' => false, 'message' => $message, 'return' => '');
    }

    curl_close($c);

    return $arrayRet;

}

function getHttpErrorCode($code)
{
    $http_codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Checkpoint',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended'
    );
    return $http_codes[$code];
}

function getCurlErrorCode($code)
{
    $curl_error_codes = array (
        0 => 'CURLE_OK',
        1 => 'CURLE_UNSUPPORTED_PROTOCOL',
        2 => 'CURLE_FAILED_INIT',
        3 => 'CURLE_URL_MALFORMAT',
        4 => 'CURLE_NOT_BUILT_IN',
        5 => 'CURLE_COULDNT_RESOLVE_PROXY',
        6 => 'CURLE_COULDNT_RESOLVE_HOST',
        7 => 'CURLE_COULDNT_CONNECT',
        8 => 'CURLE_FTP_WEIRD_SERVER_REPLY',
        9 => 'CURLE_REMOTE_ACCESS_DENIED',
        10 => 'CURLE_FTP_ACCEPT_FAILED',
        11 => 'CURLE_FTP_WEIRD_PASS_REPLY',
        12 => 'CURLE_FTP_ACCEPT_TIMEOUT',
        13 => 'CURLE_FTP_WEIRD_PASV_REPLY',
        14 => 'CURLE_FTP_WEIRD_227_FORMAT',
        15 => 'CURLE_FTP_CANT_GET_HOST',
        17 => 'CURLE_FTP_COULDNT_SET_TYPE',
        18 => 'CURLE_PARTIAL_FILE',
        19 => 'CURLE_FTP_COULDNT_RETR_FILE',
        21 => 'CURLE_QUOTE_ERROR',
        22 => 'CURLE_HTTP_RETURNED_ERROR',
        23 => 'CURLE_WRITE_ERROR',
        25 => 'CURLE_UPLOAD_FAILED',
        26 => 'CURLE_READ_ERROR',
        27 => 'CURLE_OUT_OF_MEMORY',
        28 => 'CURLE_OPERATION_TIMEDOUT',
        30 => 'CURLE_FTP_PORT_FAILED',
        31 => 'CURLE_FTP_COULDNT_USE_REST',
        33 => 'CURLE_RANGE_ERROR',
        34 => 'CURLE_HTTP_POST_ERROR',
        35 => 'CURLE_SSL_CONNECT_ERROR',
        36 => 'CURLE_BAD_DOWNLOAD_RESUME',
        37 => 'CURLE_FILE_COULDNT_READ_FILE',
        38 => 'CURLE_LDAP_CANNOT_BIND',
        39 => 'CURLE_LDAP_SEARCH_FAILED',
        41 => 'CURLE_FUNCTION_NOT_FOUND',
        42 => 'CURLE_ABORTED_BY_CALLBACK',
        43 => 'CURLE_BAD_FUNCTION_ARGUMENT',
        45 => 'CURLE_INTERFACE_FAILED',
        47 => 'CURLE_TOO_MANY_REDIRECTS',
        48 => 'CURLE_UNKNOWN_OPTION',
        49 => 'CURLE_TELNET_OPTION_SYNTAX',
        51 => 'CURLE_PEER_FAILED_VERIFICATION',
        52 => 'CURLE_GOT_NOTHING',
        53 => 'CURLE_SSL_ENGINE_NOTFOUND',
        54 => 'CURLE_SSL_ENGINE_SETFAILED',
        55 => 'CURLE_SEND_ERROR',
        56 => 'CURLE_RECV_ERROR',
        58 => 'CURLE_SSL_CERTPROBLEM',
        59 => 'CURLE_SSL_CIPHER',
        60 => 'CURLE_SSL_CACERT',
        61 => 'CURLE_BAD_CONTENT_ENCODING',
        62 => 'CURLE_LDAP_INVALID_URL',
        63 => 'CURLE_FILESIZE_EXCEEDED',
        64 => 'CURLE_USE_SSL_FAILED',
        65 => 'CURLE_SEND_FAIL_REWIND',
        66 => 'CURLE_SSL_ENGINE_INITFAILED',
        67 => 'CURLE_LOGIN_DENIED',
        68 => 'CURLE_TFTP_NOTFOUND',
        69 => 'CURLE_TFTP_PERM',
        70 => 'CURLE_REMOTE_DISK_FULL',
        71 => 'CURLE_TFTP_ILLEGAL',
        72 => 'CURLE_TFTP_UNKNOWNID',
        73 => 'CURLE_REMOTE_FILE_EXISTS',
        74 => 'CURLE_TFTP_NOSUCHUSER',
        75 => 'CURLE_CONV_FAILED',
        76 => 'CURLE_CONV_REQD',
        77 => 'CURLE_SSL_CACERT_BADFILE',
        78 => 'CURLE_REMOTE_FILE_NOT_FOUND',
        79 => 'CURLE_SSH',
        80 => 'CURLE_SSL_SHUTDOWN_FAILED',
        81 => 'CURLE_AGAIN',
        82 => 'CURLE_SSL_CRL_BADFILE',
        83 => 'CURLE_SSL_ISSUER_ERROR',
        84 => 'CURLE_FTP_PRET_FAILED',
        85 => 'CURLE_RTSP_CSEQ_ERROR',
        86 => 'CURLE_RTSP_SESSION_ERROR',
        87 => 'CURLE_FTP_BAD_FILE_LIST',
        88 => 'CURLE_CHUNK_FAILED',
        89 => 'CURLE_NO_CONNECTION_AVAILABLE'
    );
    return $curl_error_codes[$code];
}

function encodeString($string)
{
    return urlencode(urlencode($string));

}

function makeBankSlip($data,$cronSystem)
{

    switch ($data['banco']){
        case "033":
            require_once(HELPDEZK_PATH.  "/app/modules/fin/lib/classes/makeBoleto/Santander.php");
            $bankSlip = new Santander($data);
            break;
        default:
            require_once(HELPDEZK_PATH. "/app/modules/fin/lib/classes/makeBoleto/Sicredi.php");
            $bankSlip = new Sicredi($data);
            break;

    }

    $ret = $bankSlip->setParams();

    $file = makefile($ret,$data['month'],$cronSystem);
    if(!$file){
        $cronSystem->logIt('Could not return the bank slip file - Program: '. __FILE__ ,3,'general',__LINE__);
        return false;
    }
    return $file;

}

function makefile($data,$competence,$cronSystem)
{

    require_once (HELPDEZK_PATH . "/includes/classes/fpdf/fpdf.php");
    require_once (HELPDEZK_PATH . "/includes/classes/fpdf/i25.php");

    $pdf=new PDF_i25('P', 'mm', 'A4');
    $y= 30;

    $pdf->AddPage();

    /**
     **
        ** Recibo do Sacado
        **
        **/

    // Pontilhado
    $pdf->SetDrawColor(200);
    $pdf->SetFont('Arial','B',6);
    $pdf->DashLine(20,$y,204,0.2,50);
    $pdf->SetXY(185,$y+1.5);
    $pdf->Image($data["company_logo"],20,$y+2,13,12);
    $pdf->Cell(20,0,html_entity_decode(utf8_decode("Recibo do Sacado"),ENT_QUOTES, "ISO8859-1"),0,0,'R');
    $y=$y+20;
    $pdf->Image($data["bank_logo"],20,$y,41.66,11.11);
    $pdf->Line(62,$y+5, 62, $y+11) ;
    $pdf->SetFont('Arial','B',20);
    $pdf->Text(63,$y+10, $data["codigo_banco_com_dv"]) ;
    $pdf->Line(82,$y+5, 82, $y+11) ;
    $pdf->SetFont('Arial','B',11);
    $pdf->SetXY(185,$y+9);
    $pdf->Cell(20,0,$data["linha_digitavel"],0,0,'R');
    $y=$y+11;

    // Primeira Linha
    $pdf->SetFont('Arial','',7);
    $pdf->Line(20,$y, 204, $y) ;

    // -- Cedente --
    $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Cedente'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(22,$y+6, html_entity_decode($data["cedente"],ENT_QUOTES, "ISO8859-1")) ;

    // --- Agência/Código do Cedente ---
    $pdf->Line(108,$y, 108, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(110,$y+2.5, html_entity_decode(utf8_decode('Agência/Código do Cedente'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $tmp2 = makeAgencyData($data,$cronSystem);
    $pdf->Text(110,$y+6,$tmp2) ;

    // -- Espécie --
    $pdf->Line(142,$y, 142, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(144,$y+2.5, html_entity_decode(utf8_decode('Espécie'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(144,$y+6, 'R$') ;

    // -- Quantidade --
    $pdf->Line(155,$y, 155, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(157,$y+2.5, html_entity_decode(utf8_decode('Quantidade'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(157,$y+6, $data["quantidade"]) ;

    // -- Nosso número
    $pdf->Line(172,$y, 172, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(174,$y+2.5, html_entity_decode(utf8_decode('Nosso número'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY(185,$y+5);
    $tmp = $data["nosso_numero"];
    $pdf->Cell(20,0,$tmp,0,0,'R');

    // Segunda Linha
    $y = $y+7;
    $pdf->SetFont('Arial','',7);
    $pdf->Line(20,$y, 204, $y) ;
    $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Número do documento'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(22,$y+6, $data["numero_documento"]) ;

    // --------------------------------------
    $pdf->Line(57,$y, 57, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(59,$y+2.5, html_entity_decode(utf8_decode('CPF/CNPJ'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(59,$y+6, $data["cpf_cnpj"]) ;

    // --------------------------------------
    $pdf->Line(98,$y, 98, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(100,$y+2.5, html_entity_decode(utf8_decode('Vencimento'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(100,$y+6, $data["data_vencimento"]) ;

    // --------------------------------------
    $pdf->Line(153,$y, 153, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('Valor documento'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY(185,$y+5);
    $pdf->Cell(20,0,$data["valor_boleto"],0,0,'R');

    // Terceira Linha
    $y = $y+7;
    $pdf->SetFont('Arial','',7);
    $pdf->Line(20,$y, 204, $y) ;
    $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Sacado'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(22,$y+6, $data["sacado"]) ;

    // Quarta Linha
    $y = $y+7;
    $pdf->SetFont('Arial','',7);
    $pdf->Line(20,$y, 204, $y) ;
    $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Demonstrativo'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetXY(185,$y+1.8);
    $pdf->Cell(20,0,html_entity_decode(utf8_decode('Autenticação mecânica'),ENT_QUOTES, "ISO8859-1"),0,0,'R');

    // Pontilhado
    $y=$y+15.5;
    $pdf->SetDrawColor(200);
    $pdf->SetFont('Arial','',6);
    $pdf->SetXY(185,$y);
    $pdf->Cell(20,0,html_entity_decode(utf8_decode("Corte na linha pontilhada"),ENT_QUOTES, "ISO8859-1"),0,0,'R');
    $pdf->DashLine(20,$y+1.5,204,0.2,50);

    /**
     **
        ** Boleto
        **
        **/

    $y=$y+5;
    // 1 px = 5mm
    $pdf->Image($data["bank_logo"],20,$y,41.66,11.11);
    $pdf->Line(62,$y+5, 62, $y+11) ;
    $pdf->SetFont('Arial','B',20);
    $pdf->Text(63,$y+10, $data["codigo_banco_com_dv"]) ;
    $pdf->Line(82,$y+5, 82, $y+11) ;
    $pdf->SetFont('Arial','B',11);
    $pdf->SetXY(185,$y+9);
    $pdf->Cell(20,0,$data["linha_digitavel"],0,0,'R');

    // Primeira Linha
    $y=$y+11;
    $pdf->SetFont('Arial','',7);
    $pdf->Line(20,$y, 204, $y) ;
    $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Local de Pagamento'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(22,$y+6, html_entity_decode(utf8_decode($data['payment_local']),ENT_QUOTES, "ISO8859-1"));
    $pdf->Line(153,$y, 153, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('Vencimento'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY(185,$y+5);
    $pdf->Cell(20,0,$data["data_vencimento"],0,0,'R');

    // Segunda Linha
    $y = $y+7;
    $pdf->SetFont('Arial','',7);
    $pdf->Line(20,$y, 204, $y) ;
    $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Cedente'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(22,$y+6, html_entity_decode($data["cedente"].' - '.$data["cpf_cnpj"],ENT_QUOTES, "ISO8859-1")) ;
    $pdf->Line(153,$y, 153, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('Agência/Código Cedente'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY(185,$y+5);
    $tmp2 = makeAgencyData($data,$cronSystem);
    $pdf->Cell(20,0,$tmp2,0,0,'R');

    // Terceira Linha
    $y = $y+7;
    $pdf->SetFont('Arial','',7);
    $pdf->Line(20,$y, 204, $y) ;
    $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Data do documento'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(22,$y+6, $data["data_documento"]) ;

    // --------------------------------------
    $pdf->Line(57,$y, 57, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(59,$y+2.5, html_entity_decode(utf8_decode('N. documento'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(59,$y+6, $data["numero_documento"]) ;

    // --------------------------------------
    $pdf->Line(98,$y, 98, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(100,$y+2.5, html_entity_decode(utf8_decode('Espécie doc.'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(100,$y+6, 'DMI') ;

    // --------------------------------------
    $pdf->Line(118,$y, 118, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(120,$y+2.5, html_entity_decode(utf8_decode('Aceite'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(120,$y+6, $data["aceite"]) ;

    // --------------------------------------
    $pdf->Line(128,$y, 128, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(130,$y+2.5, html_entity_decode(utf8_decode('Data processamento'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(130,$y+6, $data["data_processamento"]) ;

    // --------------------------------------
    $pdf->Line(153,$y, 153, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('Nosso número'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY(185,$y+5);
    $tmp = $data["nosso_numero"];
    $pdf->Cell(20,0,$tmp,0,0,'R');

    // Quarta Linha
    $y = $y+7; //31
    $pdf->SetFont('Arial','',7);
    $pdf->Line(20,$y, 204, $y) ;
    $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Uso do Banco'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(22,$y+6, '') ;

    // --------------------------------------
    $pdf->Line(57,$y, 57, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(59,$y+2.5, html_entity_decode(utf8_decode('Carteira'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(59,$y+6, $data["carteira"]) ;

    // --------------------------------------
    $pdf->Line(80,$y, 80, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(82,$y+2.5, html_entity_decode(utf8_decode('Moeda'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(82,$y+6, 'REAL') ;

    // --------------------------------------
    $pdf->Line(98,$y, 98, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(100,$y+2.5, html_entity_decode(utf8_decode('Quantidade'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(100,$y+6, $data["quantidade"]) ;

    // --------------------------------------
    $pdf->Line(128,$y, 128, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(130,$y+2.5, html_entity_decode(utf8_decode('(x)Valor'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(130,$y+6, $data["valor_unitario"]) ;

    // --------------------------------------
    $pdf->Line(153,$y, 153, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('(=)Valor do Documento'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY(185,$y+5);
    $pdf->Cell(20,0,$data["valor_boleto"],0,0,'R');

    // Quinta Linha
    $y = $y+7; //38
    $pdf->SetFont('Arial','',7);
    $pdf->Line(20,$y, 204, $y) ;
    $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Instruções'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(22,$y+6, '') ;
    $pdf->Text(22,$y+10, html_entity_decode(utf8_decode($data["instrucoes1"]),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->Text(22,$y+13, html_entity_decode(utf8_decode($data["instrucoes2"]),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->Text(22,$y+16, html_entity_decode(utf8_decode($data["instrucoes3"]),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->Text(22,$y+19, html_entity_decode(utf8_decode($data["instrucoes4"]),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->Text(22,$y+22, html_entity_decode(utf8_decode($data["instrucoes5"]),ENT_QUOTES, "ISO8859-1")) ;

    // --------------------------------------
    $pdf->Line(153,$y, 153, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('(-) Desconto'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY(185,$y+5);
    if(isset($data["valor_desconto"])){
        $pdf->Cell(20,0,$data["valor_desconto"],0,0,'R');
    }else{
        $pdf->Cell(20,0,"",0,0,'R');
    }
    //$pdf->Cell(20,0,"",0,0,'R');

    // Sexta Linha
    $y = $y+7; //45
    $pdf->SetFont('Arial','',7);
    $pdf->Line(153,$y, 204, $y) ;
    $pdf->Line(153,$y, 153, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('(-) Outras Deduções'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY(185,$y+5);
    $pdf->Cell(20,0,"",0,0,'R');

    // Setima Linha
    $y = $y+7; //52
    $pdf->SetFont('Arial','',7);
    $pdf->Line(153,$y, 204, $y) ;
    $pdf->Line(153,$y, 153, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('(+) Mora/Multa'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY(185,$y+5);
    $pdf->Cell(20,0,"",0,0,'R');

    // Oitava Linha
    $y = $y+7; //59
    $pdf->SetFont('Arial','',7);
    $pdf->Line(153,$y, 204, $y) ;
    $pdf->Line(153,$y, 153, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('(+) Outros Acréscimos'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY(185,$y+5);
    $pdf->Cell(20,0,"",0,0,'R');

    // Nona Linha
    $y = $y+7; //66
    $pdf->SetFont('Arial','',7);
    $pdf->Line(153,$y, 204, $y) ;
    $pdf->Line(153,$y, 153, $y+7) ;
    $pdf->SetFont('Arial','',7);
    $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('(=) Valor Cobrado'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY(185,$y+5);
    if(isset($data["valor_cobrado"])){
        $pdf->Cell(20,0,$data["valor_cobrado"],0,0,'R');
    }else{
        $pdf->Cell(20,0,"",0,0,'R');
    }
    //$pdf->Cell(20,0,"",0,0,'R');

    // Decima Linha
    $y = $y+7; //73
    $pdf->SetFont('Arial','',7);
    $pdf->Line(20,$y, 204, $y) ;
    $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Sacado'),ENT_QUOTES, "ISO8859-1")) ;

    // Decima Primeira Linha
    $pdf->SetFont('Arial','',7);
    $pdf->SetFont('Arial','B',7);
    $pdf->Text(22,$y+5.5, $data["sacado"]) ;
    $pdf->Text(22,$y+8, $data["endereco1"]) ;
    $pdf->Text(22,$y+11, $data["endereco2"]) ;

    // Decima Segunda Linha
    $y=$y+11; // 83
    $pdf->Line(153,$y, 153, $y+4) ;
    $pdf->SetFont('Arial','',7);
    //$pdf->Text(22,$y+3, 'Sacador/Avalista') ;
    $pdf->Text(155,$y+3, html_entity_decode(utf8_decode('Cód baixa'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->Line(20,$y+4, 204, $y+4) ;

    // Decima Terceira Linha
    $y=$y+6; //89

    $pdf->Text(22,$y+0.5, html_entity_decode(utf8_decode('Sacador/Avalista'),ENT_QUOTES, "ISO8859-1")) ;
    $pdf->SetXY(185,$y);
    $pdf->Cell(20,0,html_entity_decode(utf8_decode("Autenticação mecânica - Ficha de Compensação"),ENT_QUOTES, "ISO8859-1"),0,0,'R');

    // Código de Barras
    $pdf->i25(20,$y+1,$data["codigo_barras"],0.84,12.95);

    // Pontilhado
    $y=$y+15.5; //104.5
    $pdf->SetDrawColor(200);
    $pdf->SetFont('Arial','',6);
    $pdf->SetXY(185,$y);
    $pdf->Cell(20,0,html_entity_decode(utf8_decode("Corte na linha pontilhada"),ENT_QUOTES, "ISO8859-1"),0,0,'R');
    $pdf->DashLine(20,$y+1.5,204,0.2,50);

    $dirModule = setFolder(HELPDEZK_PATH . "/app/downloads/fin/",$cronSystem);
    $dirType = setFolder($dirModule . "bankslip/",$cronSystem);
    $dirYear = setFolder($dirType . date("Y") . "/",$cronSystem);
    $dirPath = setFolder($dirYear . $competence . "/",$cronSystem);

    $dirUrl = $cronSystem->helpdezkUrl . "/app/downloads/fin/bankslip/" . date("Y") ."/".$competence;
    $fileDir = "/app/downloads/fin/bankslip/" . date("Y") ."/".$competence;

    $fileName = $data["numero_documento"].'.pdf';
    $filePath = $dirPath.$fileName;
    $fileUrl = $dirUrl.$fileName;

    $pdf->Output($filePath ,"F");

    if(!file_exists($filePath )){
        $cronSystem->logIt('Could not create the file: '.$fileName.' - Program: '. __FILE__ ,3,'general',__LINE__);
        return false;
    }

    $aFileRet = array(
        "filename" => $fileName,
        "fileurl" => $fileUrl,
        "filedir" => $fileDir
    );

    return $aFileRet;
}

function makeAgencyData($data,$cronSystem)
{
    switch ($data['cod_febraban']){
        case "033": //Santander
            $tmp2 = $data["codigo_cliente"];
            $tmp2 = substr($tmp2,0,strlen($tmp2)-1).'-'.substr($tmp2,strlen($tmp2)-1,1);
            break;
        default: //Sicredi
            $tmp2 = $data["agencia_codigo"];
            break;
    }

    return $tmp2;
}

function setFolder($path,$cronSystem)
{
    if(!is_dir($path)) {
        $cronSystem->logIt('Directory: '. $path.' does not exists, I will try to create it. - Program: '. __FILE__ ,6,'general',__LINE__);
        if (!mkdir ($path, 0777 )) {
            $cronSystem->logIt('I could not create the directory: '.$path.' - Program: '. __FILE__ ,3,'general',__LINE__);
            return false;
        }else{
            $cronSystem->logIt("Directory: {$path} was created successfully. - Program: '". __FILE__ ,6,'general',__LINE__);
        }
    }

    if (!is_writable($path)) {
        $cronSystem->logIt('Directory: '. $path.' Is not writable, I will try to make it writable - Program: '. __FILE__ ,6,'general',__LINE__);
        if (!chmod($path,0777)){
            $cronSystem->logIt('Directory: '.$path.'Is not writable !! - Program: '. __FILE__ ,3,'general',__LINE__);
            return false;
        }else{
            $cronSystem->logIt("Write permissions were granted to Directory: {$path}. - Program: '". __FILE__ ,6,'general',__LINE__);
        }
    }

    return $path;
}

function getFeature($featName,$dbBankSlip,$cronSystem)
{
    $ret = $dbBankSlip->getFINFeatures("WHERE session_name = '{$featName}'");
    if (!$ret['success']) {
        $cronSystem->logIt($ret['message'] . ' - Program: '. __FILE__ ,3,'general',__LINE__);
        return false;
    }

    return $ret['data']->fields['value'];

}

function setRequire($requireFile,$cronSystem){
    if (!file_exists($requireFile)) {
        $cronSystem->logIt("{$requireFile} does not exist - Program: ". __FILE__ ,3,'email',__LINE__);
        return false;
    }else{
        return require_once($requireFile);
    }
}