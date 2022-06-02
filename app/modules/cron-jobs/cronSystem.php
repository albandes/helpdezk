<?php
class cronSystem {

    private $_url, $_explode;
    public $_controller, $_action, $_params, $_module, $_config;

    public $_logLevel ;
    public $_logHost ;
    public $_logRemoteServer ;
    public $_logFacility;

    public static $_logStatus;

    /**
     * Use token on the operator link to view the request
     * @var bool
     */
    public $_tokenOperatorLink;

    public function __construct() {

        $this->setConfig();

        $this->database     = $this->getConfig('db_connect');
        $this->pathDefault  = $this->getConfig('path_default');
        $this->dateFormat 	= $this->getConfig('date_format');
        $this->hourFormat 	= $this->getConfig('hour_format');

        $this->logFile = $this->getLogFile('general');
        $this->logFileEmail  = $this->getLogFile('email');
        $this->logDateHour  = $this->getlogDateHour();

        $this->helpdezkUrl = $this->getHelpdezkUrl();

        // Use of tokens in the request´s view url by the operator
        $this->_tokenOperatorLink = $this->getEnabledTokenOperatorLink();


    }

    /**
     * Return if use of tokens in the request´s view url by the operator is enabled
     *
     * @return bool  true|false
     *
     * @since 1.1.7.1 First time this was introduced.
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function getEnabledTokenOperatorLink()
    {
        return true;
    }

    /*
     *  Adapted
     */
    public function setConfig($type = null, $value = null)
    {

        if ((include HELPDEZK_PATH . 'includes/config/config.php') == false) {
            die('The config file does not exist: ' . 'includes/config/config.php, line ' . __LINE__ . '!!!');
        }

        if($type && $value){
            $this->_config[$type] = $value;
        } else{
            $this->_config = $config;
        }

    }

    public function getHelpdezkUrl()
    {

        $hdkUrl = $this->getConfig('hdk_url');
        if (substr($hdkUrl, 0, 1) == '/')
            $hdkUrl = substr($hdkUrl, 0, -1);
        return $hdkUrl;

    }

    public function getConfig($param)
    {

        return $this->_config[$param];
    }

    function getLogFile($logType)
    {
        $dirLog = HELPDEZK_PATH.'/logs/';
        if(!is_dir($dirLog)) {
            mkdir ($dirLog, 0777 ); // create dir
        }else{
            if(!is_writable($dirLog)) {//validation
                chmod($dirLog, 0777);
            }
        }

        if ($logType == 'general') {
            $file = $dirLog.'helpdezk.log';
        } elseif($logType == 'email') {
            $file = $dirLog.'email.log';
        }

        if (!file_exists($file)) {
            if($fp = fopen($file, 'a')) { //create log file
                @fclose($fp);
            } else {
                return false;
            }
        }

        return $file;
    }

    function getlogDateHour()
    {
        $dateHour = $this->getConfig('log_date_format');
        if (empty($dateHour)){
            return "d/m/Y H:i:s";
        } else {
            return str_replace('%','',$dateHour );
        }

    }

    /*
     *  Nova
     */
    public function  _getRootPath()
    {
        $path =  str_replace(DIRECTORY_SEPARATOR ."app".DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR . $module ,"",__DIR__ ) ;
        return $path . DIRECTORY_SEPARATOR;
    }

    public function _getDocumentRoot()
    {
        $document_root=$_SERVER['DOCUMENT_ROOT'];
        if(substr($document_root, -1)!='/'){
            $document_root=$document_root.'/';
        }
        return $document_root ;
    }

    public function _getPathDefault()
    {
        $path_default = $this->getConfig("path_default");

        if(substr($path_default, 0,1)!='/'){
            $path_default='/'.$path_default;
        }
        if ($path_default == "/..") {
            $path_default = '';
        }
        return $path_default;
    }


    public function retornaSmarty() {
        require_once(SMARTY . 'Smarty.class.php');
        $smarty = new Smarty;
        $smarty->debugging = false;
        //$smarty->template_dir = VIEWS;
        $smarty->compile_dir = HELPDEZK_PATH."/system/templates_c/";
        $lang_default = $this->getConfig("lang");
        $license =  $this->getConfig("license");
        if (path == "/..") {
            $smarty->config_load(DOCUMENT_ROOT . '/app/lang/' . $lang_default . '.txt', $license);
        } else {
            $smarty->config_load(DOCUMENT_ROOT . path . '/app/lang/' . $lang_default . '.txt', $license);
        }

        $smarty->assign('lang', $lang_default);
        $smarty->assign('date_format', $this->getConfig("date_format"));
        $smarty->assign('hour_format', $this->getConfig("hour_format"));
        $smarty->assign('demo', $this->getConfig("demo"));
        $smarty->assign('theme', $this->getConfig("theme"));
        $smarty->assign('path', path);
        $smarty->assign('pagetitle', $this->getConfig("page_title"));
        return $smarty;
    }

    public function setCronSession()
    {

        session_start();

        $bd = new index_model();

        $data = $bd->getConfigData();

        while (!$data->EOF) {
            $ses = $data->fields['session_name'];
            $val = $data->fields['value'];
            $_SESSION[$ses] = $val;
            $data->MoveNext();
        }

        // Global Config Data
        $rsConfig = $bd->getConfigGlobalData();
        while (!$rsConfig->EOF) {
            $ses = $rsConfig->fields['session_name'];
            $val = $rsConfig->fields['value'];
            $_SESSION[$ses] = $val;
            $rsConfig->MoveNext();
        }


    }

    public function formatDate($date) {
        $dbCommon = new common();
        $dateafter = $dbCommon->getDate($date, $this->getConfig("date_format"));
        return $dateafter;
    }

    public function formatHour($date) {
        $bd = new operatorview_model();
        $dateafter = $bd->getDate($date, $this->getConfig("hour_format"));
        return $dateafter;
    }

    public function formatDateHour($date) {
        $bd = new operatorview_model();
        $dateafter = $bd->getDateTime($date, $this->getConfig("date_format")." ".$this->getConfig("hour_format"));
        return $dateafter;
    }

    public function formatSaveDate($date) {
        //$bd = new operatorview_model();
        //$dateafter = $bd->getSaveDate($date, $this->getConfig("date_format"));
        $dbCommon = new common();
        $dateafter = $dbCommon->getSaveDate($date, $this->getConfig("date_format"));
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return "'".$dateafter."'";
        } elseif ($database == 'oci8po') {
            return $dateafter;
        }
    }

    public function formatSaveHour($hour) {
        $bd = new operatorview_model();
        $dateafter = $bd->getSaveHour($hour, $this->getConfig("hour_format"));
        return $dateafter;
    }

    public function formatSaveDateHour($date) {
        $bd = new operatorview_model();
        $dateafter = $bd->getSaveDate($date, $this->getConfig("date_format")." ".$this->getConfig("hour_format"));
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return "'".$dateafter."'";
        } elseif ($database == 'oci8po') {
            return $dateafter;
        }
    }

    /**
     * Format a value to write in database .
     * @access public
     * @param String $valor Value
     * @return String Formated Value
     **/
    function formatSaValue($value)  {
        $value = str_replace(",",".",str_replace(".","",$value)) ;
        return $value;
    }

    /**
     * Method to write in log file
     *
     * @author Rogerio Albandes <rogerio.albandeshelpdezk.cc>
     *
     * @param string  $str String to write
     * @param string  $file  Log filename
     *
     * @since December 06, 2017
     *
     * @return string true|false
     */
    function logIt($msg,$logLevel,$logType,$line = null)
    {


        //if ($logLevel > $this->_logLevel)
        //    return false ;

        $levelStr = '';
        switch ( $logLevel ) {
            case '0':
                $levelStr = 'EMERG';
                break;
            case '1':
                $levelStr = 'ALERT';
                break;
            case '2':
                $levelStr = 'CRIT';
                break;
            case '3':
                $levelStr = 'ERR';
                break;
            case '4':
                $levelStr = 'WARNING';
                break;
            case '5':
                $levelStr = 'NOTICE';
                break;
            case '6':
                $levelStr = 'INFO';
                break;
            case '7':
                $levelStr = 'DEBUG';
                break;
        }

        $date = date($this->logDateHour);

        if($line)
            $msg .= ' line '. $line;

        if ($this->_logHost == 'local'){
            $msg = sprintf( "[%s] [%s]: %s%s", $date, $levelStr, $msg, PHP_EOL );
            if ($logType == 'general'){
                $file = $this->logFile;
            } else {
                $file = $this->logFileEmail;
            }
            file_put_contents( $file, $msg, FILE_APPEND );

        } elseif ($this->_logHost == 'remote'){

            // pipetodo Testar com servidor remoto as adaptações no hdk_sendmail.php
            $rmt = $_SERVER["REMOTE_ADDR"];
            if  ($rmt == '::1' )
                $rmt = '127.0.0.1';

            $msg = sprintf( "[%s]: %s", $levelStr, $msg);
            $remoteSyslog = new Syslog();
            $remoteSyslog->SetFacility(8);
            $remoteSyslog->SetSeverity(3);
            $remoteSyslog->SetHostname(utf8_encode(gethostname()));
            //$remoteSyslog->SetFqdn('hdk.marioquintana.com.br');
            $remoteSyslog->SetIpFrom($rmt);
            $remoteSyslog->SetProcess($logType);
            $remoteSyslog->SetContent($msg);
            $remoteSyslog->SetServer($this->_logRemoteServer);
            $remoteSyslog->SetPort(514);
            $remoteSyslog->SetTimeout(10);
            $remoteSyslog->Send();

        }




    }

    // Since April 28, 2017
    function getPrintDate()
    {
        return str_replace("%","",$this->dateFormat) . " " . str_replace("%","",$this->hourFormat);

    }

    // Since April 28, 2017
    public function getHelpdezkPath()
    {
        $path_default = $this->pathDefault;
        if(substr($path_default, 0,1)!='/'){
            $path_default='/'.$path_default;
        }
        if ($path_default == "/..") {
            $path = "";
        } else {
            $path =$path_default;
        }
        if(substr(php_sapi_name(), 0, 3) == 'cli') {
            $path_parts = pathinfo(dirname(__FILE__));
            $cron_path  = str_replace("\\","/",$path_parts['dirname']).'/' ;
            $document_root=substr( $cron_path,0, strpos($cron_path,$path_default)).'/' ;
        } else {
            $document_root=$_SERVER['DOCUMENT_ROOT'];
        }
        if(substr($document_root, -1)!='/'){
            $document_root=$document_root.'/';
        }
        return  realpath($document_root.$path) ;
    }

    // Since May 01, 2017
    public function getLineBreak()
    {
        if (substr(php_sapi_name(), 0, 3) != 'cli') {
            return '<br>' ;
        } else {
            return PHP_EOL ;
        }
    }

    public function getAdoDbVersion()
    {
        $adodb = $this->getConfig('adodb');
        if (empty($adodb))
            $adodb = 'adodb-5.20.9';
        return $adodb;
    }

    // Since January 24, 2020
    public function loadModel($modelName)
    {
        $ds = DIRECTORY_SEPARATOR ;
        $modelPath = 'app'.$ds.'modules'.$ds;

        $dbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);

        //echo '<pre>'; print_r($dbt); echo '</pre>';


        if (strpos($modelName, '/') === false) {
            /*
            *  Quando vem só o nome do model usar o path do método que chamou para descobrir o módulo
            */
            $class = $modelName;
            $arrayParts = explode(DIRECTORY_SEPARATOR,$dbt[0]['file']);

            //echo '<pre>'; print_r($arrayParts); echo '</pre>';

            $appPos =  array_search('app',$arrayParts);
            $modPos =  array_search('modules',$arrayParts);

            if ($modPos - $appPos == 1) {
                $modelModule = $arrayParts[$modPos+1];
            }

            $modelFile = HELPDEZK_PATH . $modelPath . $modelModule . $ds . 'models'. $ds . $class . '.php';

        } else {
            /*
             * Quando vem o módulo + o model, ex: admin/tracker_model
             */
            $arrParts = explode("/", $modelName);
            $class = $arrParts[1];
            $modelFile = HELPDEZK_PATH . $modelPath . $arrParts[0] . '/models/' . $class . '.php';

        }

        spl_autoload_register(function ($class) use( &$modelFile) {
            if (file_exists($modelFile)) {
                require_once($modelFile);
            } else {
                die ('The model file does not exist: ' . $modelFile);
            }
        });

    }

    public function getIdModule($modulename)
    {
        $dbCommon = new common();
        $id = $dbCommon->_getIdModule($modulename) ;
        if(!$id) {
            die('Module don\'t exists in tbmodule !!!') ;
        } else {
            return $id ;
        }
    }

    // Since January 24, 2020
    function SetEnvironment()
    {

        if (substr(php_sapi_name(), 0, 3) != 'cli') {
            $CLI = true ;
        } else {
            $CLI = false ;
        }

        $cron_path = $this->_getRootPath();

        define('path', $this->_getPathDefault());

        if ($CLI) {
            define('DOCUMENT_ROOT', substr( $cron_path,0, strpos($cron_path,$this->pathDefault)).'/');
        } else {
            define('DOCUMENT_ROOT', $this->_getDocumentRoot());
        }
        define('LANGUAGE',$this->getConfig("lang"));
        define('HELPDEZK_PATH', realpath(DOCUMENT_ROOT.path)) ;
    }

    // Since January 24, 2020
    function getRootPath ($module)
    {
        $temp = join(DIRECTORY_SEPARATOR, array('app', 'modules', $module));
        return str_replace($temp, '', getcwd());
    }


    // Since October 25, 2017
    public function returnPhpMailer()
    {

        // $phpMailerDir = $this->getHelpdezkPath() . '/includes/classes/phpMailer/class.phpmailer.php';
        $phpMailerDir = HELPDEZK_PATH . '/includes/classes/phpMailer/class.phpmailer.php';

        if (!file_exists($phpMailerDir)) {
            die ('ERROR: ' .$phpMailerDir . ' , does not exist  !!!!') ;
        }

        require_once($phpMailerDir);

        $mail = new phpmailer();

        return $mail;
    }


    /**
     * Method to send e-mails
     *
     * @author Rogerio Albandes <rogerio.albandes@pipegrep.com.br>
     *
     * @param string  $subject E-mail subject
     * @param string  $body  E-mail body
     * @param array   $address Addreaesse
     * @param boolean $log If it will log
     * @param string $log_text Log text
     *
     * @return string true|false
     */
    public function sendEmailDefault($params)
    {

        $dbCommon = new common();
        $emconfigs = $dbCommon->getEmailConfigs();
        $tempconfs = $dbCommon->getTempEmail();

        $mail_title     = '=?UTF-8?B?'.base64_encode($emconfigs['EM_TITLE']).'?=';
        $mail_method    = 'smtp';
        $mail_host      = $emconfigs['EM_HOSTNAME'];
        $mail_domain    = $emconfigs['EM_DOMAIN'];
        $mail_auth      = $emconfigs['EM_AUTH'];
        $mail_username  = $emconfigs['EM_USER'];
        $mail_password  = $emconfigs['EM_PASSWORD'];
        $mail_sender    = $emconfigs['EM_SENDER'];
        $mail_header    = $tempconfs['EM_HEADER'];
        $mail_footer    = $tempconfs['EM_FOOTER'];
        $mail_port      = $emconfigs['EM_PORT'];

        $mail = $this->returnPhpMailer();

        $mail->CharSet = 'utf-8';

        if($params['customHeader'] && $params['customHeader'] != ''){
            $mail->addCustomHeader($params['customHeader']);
        }

        if ($this->getConfig('demo') == true) {
            $mail->addCustomHeader('X-hdkLicence:' . 'demo');
        } else {
            $mail->addCustomHeader('X-hdkLicence:' . $this->getConfig('license'));
        }

        if($params['sender'] && $params['sender'] != ''){
            $mail_sender = $params['sender'];
            $mail_title = $params['sender_name'];
        }

        if($params['sender_name'] && $params['sender_name'] != ''){
            $mail_title = '=?UTF-8?B?'.base64_encode($params['sender_name']).'?=';
        }

        $mail->setFrom($mail_sender, $mail_title);

        if ($mail_host)
            $mail->Host = $mail_host;
        if (isset($mail_port) AND !empty($mail_port)) {
            $mail->Port = $mail_port;
        }

        $mail->Mailer = $mail_method;
        $mail->SMTPAuth = $mail_auth;

        if ($emconfigs['EM_TLS'])
            $mail->SMTPSecure = 'tls';

        $mail->Username = $mail_username;
        $mail->Password = $mail_password;

        $mail->AltBody 	= "HTML";
        $mail->Subject 	= '=?UTF-8?B?'.base64_encode($params['subject']).'?=';

        $mail->SetLanguage('br', $this->helpdezkPath . "/includes/classes/phpMailer/language/");

        $paramsDone = array("msg" => $params['msg'],
                            "msg2" => $params['msg2'],
                            "mail_host" => $mail_host,
                            "mail_domain" => $mail_domain,
                            "mail_auth" => $mail_auth,
                            "mail_port" => $mail_port,
                            "mail_username" => $mail_username,
                            "mail_password" => $mail_password,
                            "mail_sender" => $mail_sender
                            );

        if(sizeof($params['attachment']) > 0){
            foreach($params['attachment'] as $key=>$value){
                $mail->AddAttachment($value['filepath'], $value['filename']);  // optional name
            }
        }

        $normalProcedure = true;

        if ($params['tracker'] or $this->_tokenOperatorLink) {

            $aEmail = $this->makeArrayTracker($params['address']);
            $body = $mail_header . $params['contents'] . $mail_footer;

            foreach ($aEmail as $key => $sendEmailTo) {

                $mail->AddAddress($sendEmailTo);

                if ($this->_tokenOperatorLink) {
                    $linkOperatorToken = $this->makeLinkOperatorToken($sendEmailTo, $params['code_request']);
                    if ($linkOperatorToken == false) {
                        $this->logIt("Error make link operator with token, request #" . $params['code_request'] . ' - program: ' . $this->program, 3, 'email', __LINE__);
                    } else {
                        $newContent = $this->replaceBetweenTags($params['contents'], $linkOperatorToken, 'pipegrep');
                        $body = $mail_header . $newContent . $mail_footer;
                    }
                }

                if($params['tracker']) {
                    $idEmail = $this->saveTracker($params['idmodule'],$mail_sender,$sendEmailTo,addslashes($params['subject']),addslashes($params['contents']));
                    if(!$idEmail) {
                        $this->logIt("Error insert in tbtracker, " . $params['msg'] .' - program: ' . $this->program, 3, 'email', __LINE__);
                    } else {
                        $trackerID = '<img src="'.$this->helpdezkUrl.'/tracker/'.$this->modulename.'/'.$idEmail.'.png" height="1" width="1" />' ;
                        $body = $body . $trackerID;
                    }
                }

                $mail->Body = $body;

                // sent email
                $error_send = $this->isEmailDone($mail, $paramsDone);

                $mail->ClearAddresses();

            }

            $normalProcedure = false;

        }

        if ($normalProcedure){
            //Checks for more than 1 email address at recipient
            $this->makeSentTo($mail,$params['address']);
            $mail->Body = $mail_header . $params['contents'] . $mail_footer;
            // sent email
            $error_send = $this->isEmailDone($mail,$paramsDone);
        }

        $mail->ClearAttachments();
        if ($error_send)
            return false;
        else
            return true;


    }

    public function makeLinkOperatorToken($email,$codeRequest)
    {

        $this->loadModel('helpdezk/ticket_model');
        $dbTicket = new ticket_model();

        $token = $dbTicket->getUrlTokenByEmail($email,$codeRequest);
        if ($token)
            return "<a href='".$this->helpdezkUrl."/helpdezk/hdkTicket/viewrequest/id/{$codeRequest}/token/{$token}' target='_blank'>{$codeRequest}</a>";
        else
            return false ;
    }

    /**
     * Method to replace text between tags and delete the tags
     *
     * @author Rogerio Albandes <rogerio.albandes@pipegrep.com.br>
     *
     * @param string  $text     Original text
     * @param string  $replace  New text
     * @param string   $tag      Tag's string
     *
     * @return string           New text without tags
     */
    public function replaceBetweenTags($text, $newText, $tag)
    {
        return  preg_replace("#(<{$tag}.*?>).*?(</{$tag}>)#", $newText , $text);
    }

    /**
     * Method to get text between tags
     *
     * @author Rogerio Albandes <rogerio.albandes@pipegrep.com.br>
     *
     * @param string  $string   String with tags
     * @param string   $tag      Tag's string
     *
     * @return string           Text between tags
     */
    function getBetweenTags($string, $tag)
    {
        $pattern = "#<\s*?$tag\b[^>]*>(.*?)</$tag\b[^>]*>#s";
        preg_match($pattern, $string, $matches);

        return isset($matches[1]) ? $matches[1] : false;
    }

    public function makeArrayTracker($sentTo)
    {
        $jaExiste = array();
        $aRet = array();
        if (preg_match("/;/", $sentTo)) {
            $email_destino = explode(";", $sentTo);
            if (is_array($email_destino)) {
                for ($i = 0; $i < count($email_destino); $i++) {
                    if (empty($email_destino[$i]))
                        continue;
                    if (!in_array($email_destino[$i], $jaExiste)) {
                        $jaExiste[] = $email_destino[$i];
                        array_push($aRet,$email_destino[$i]);
                    }
                }
            } else {
                array_push($aRet,$email_destino);
            }
        } else {
            array_push($aRet,$sentTo);
        }
        return $aRet;
    }

    function saveTracker($idmodule,$mail_sender,$sentTo,$subject,$body)
    {
        $this->loadModel('admin/tracker_model');
        $dbTracker = new tracker_model();

        $ret = $dbTracker->insertEmail($idmodule,$mail_sender,$sentTo,$subject,$body);
        if(!$ret) {
            return false;
        } else {
            return $ret;
        }

    }

    public function isEmailDone($objmail,$params){
        $done = $objmail->Send();
        if (!$done) {
            if($this->log AND $_SESSION['EM_FAILURE_LOG'] == '1') {
                $objmail->Send();
                $this->logIt("Error send email, " . $params['msg'] . ' - program: ' . $this->program, 3, 'email', __LINE__);
                $this->logIt("Error send email, " . $params['msg2'] . ' - Error Info:: ' . $objmail->ErrorInfo . ' - program: ' . $this->program, 3, 'email', __LINE__);
                $this->logIt("Error send email, request # " . $params['request'] . ' - Variables: HOST: '.$params['mail_host'].'  DOMAIN: '.$params['mail_domain'].'  AUTH: '.$params['mail_auth'].' PORT: '.$params['mail_port'].' USER: '.$params['mail_username'].' PASS: '.$params['mail_password'].'  SENDER: '.$params['mail_sender'].' - program: ' . $this->program, 7, 'email', __LINE__);
            }
            $error_send = true ;
        } else {
            if($this->log AND $_SESSION['EM_SUCCESS_LOG'] == '1') {
                $this->logIt("Email Succesfully Sent, ". $params['msg']  ,6,'email');
            }
            $error_send = false ;
        }

        return $error_send;

    }

    public function makeSentTo($mail,$sentTo)
    {
        //$this->logIt('sentTo: ' . $sentTo,7,'email');
        $jaExiste = array();
        if (preg_match("/;/", $sentTo)) {
            //$this->logIt('Entrou',7,'email');
            $email_destino = explode(";", $sentTo);
            if (is_array($email_destino)) {
                for ($i = 0; $i < count($email_destino); $i++) {
                    // If the e-mail address is NOT in the array, it sends e-mail and puts it in the array
                    // If the email already has the array, do not send again, avoiding duplicate emails
                    if (!in_array($email_destino[$i], $jaExiste)) {
                        $mail->AddAddress($email_destino[$i]);
                        $jaExiste[] = $email_destino[$i];
                    }
                }
            } else {
                //$this->logIt('Entrou ' . $email_destino,7,'email');
                $mail->AddAddress($email_destino);
            }
        } else {
            //$this->logIt('Nao Entrou ' . $sentTo,7,'email');
            $mail->AddAddress($sentTo);
        }
    }

    public function sendMandrill($message)
    {
        $dbCommon = new common();
        $emconfigs = $dbCommon->getEmailConfigs();

        $endPoint = $emconfigs['MANDRILL_ENDPOINT'];
        $token = $emconfigs['MANDRILL_TOKEN'];
        $params = array(
            "key" => $token,
            "message" => $message
        );
        
        $headers = [
            "Content-Type: application/json"
        ];
        $ch = curl_init();
        $ch_options = [
            CURLOPT_URL => $endPoint,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST    => 1,
            CURLOPT_HEADER  => 0,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($params)
        ];
        curl_setopt_array($ch,$ch_options);
        $callback = curl_exec($ch);
        $result   = (($callback) ? json_decode($callback,true) : curl_error($ch));
        
        return $result;
            
    }

    public function getPersonName($idperson)
    {
        $dbCommon = new common();
        return $dbCommon->getPersonName($idperson);
    }

}

