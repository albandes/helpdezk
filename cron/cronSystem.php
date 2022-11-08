<?php

class cronSystem{
    private $_url, $_explode;
    public $_controller, $_action, $_params, $_module, $_config, $_version;
    public $_logLevel ;
    public $_logHost ;
    public $_logRemoteServer ;
    public $_logFacility;

    public function __construct()
    {
        $this->setConfig();
        $this->setUrl();
        $this->setExplode();
        //$this->setModule();
        $this->setController();
        $this->setAction();
        $this->setParams();

        $this->database     = $this->getConfig('db_connect');
        $this->pathDefault  = $this->getConfig('path_default');
        $this->dateFormat 	= $this->getConfig('date_format');
        $this->hourFormat 	= $this->getConfig('hour_format');
        $this->langDefault  = $this->getConfig('lang');

        $path_parts = pathinfo(dirname(__FILE__));
        $this->_path = $path_parts['dirname'];

        $this->logFile = $this->getLogFile('general');
        $this->logFileEmail  = $this->getLogFile('email');

        $this->printDate    = $this->getPrintDate();
        $this->logDateHour  = $this->getlogDateHour();
        $this->helpdezkUrl  = $this->getHelpdezkUrl();
        //$this->helpdezkPath = $this->getHelpdezkPath();
        $this->smartyVersion = $this->_getSmartyVersion();

    }

    // Since November 03, 2017
    public function getAdoDbVersion()
    {
        $adodb = $this->getConfig('adodb');
        if (empty($adodb))
            $adodb = 'adodb-5.20.9';
        return $adodb;
    }

    public function getConfig($param){
        return $this->_config[$param];
    }

    public function setConfig($type = null, $value = null) {
        $path_parts = pathinfo(dirname(__FILE__));

        $pathConfig = $path_parts['dirname'] . '/includes/config/config.php';



        if ((include $pathConfig) == FALSE) {
            die('The config file does not exist: ' . $pathConfig . ' , line '.__LINE__ . '!!!');
        }

        //if ((include './includes/config/config.php') == false) {
        //if ((include $pathConfig) == false) {
        //    die('The config file does not exist: ' . $pathConfig . ' , line '.__LINE__ . '!!!');
        //}

        if($type && $value){
            $this->_config[$type] = $value;
        }else{
            $this->_config = $config;
        }

    }

    private function setUrl() {
        $this->_url = $_SERVER['REQUEST_URI'];
        //die(print_r($this->_url)) ;
    }

    private function setExplode() {
        $this->_explode = explode('/', $this->_url);
        //die(print_r($this->_explode));
    }

    private function setModule() {
        $this->_module = $this->_explode[0];
    }

    private function setController() {
        $this->_controller = $this->_explode[0];
    }

    private function setAction() {
        $ac = (!isset($this->_explode[1]) || $this->_explode[1] == NULL || $this->_explode[1] == "index" ? "index" : $this->_explode[1]);
        $this->_action = $ac;
    }

    private function setParams() {
        //unset($this->_explode[0], $this->_explode[1], $this->_explode[2]);
        unset($this->_explode[0], $this->_explode[1]);

        if (end($this->_explode) == NULL) {
            array_pop($this->_explode);
        }

        $i = 0;
        if (!empty($this->_explode)) {
            foreach ($this->_explode as $val) {
                if ($i % 2 == 0) {
                    $ind[] = $val;
                } else {
                    $value[] = $val;
                }
                $i++;
            }
        } else {
            $ind = array();
            $value = array();
        }
        if (count($ind) == count($value) && !empty($ind) && !empty($value)) {
            $this->_params = array_combine($ind, $value);
        } else {
            $this->_params = array();
        }
        //die(print_r($this->_params));
    }

    public function getParam($name = NULL) {
        if ($name != NULL) {
            return $this->_params[$name];
        } else {
            return $this->_params;
        }
    }

    // Since October 28, 2017
    public function getHelpdezkUrl()
    {

        $hdkUrl = $this->getConfig('hdk_url');
        if(substr($hdkUrl, 0,1) == '/')
            $hdkUrl = substr($hdkUrl,0,-1);
        return $hdkUrl;
    }

    public function run() {
        $path_parts = pathinfo(dirname(__FILE__));
        $controller_path = $path_parts['dirname'] .'/'. CONTROLLERS . $this->_controller . 'Controller.php';

        if (!file_exists($controller_path)) {
            die("The controller does not exist: " . $controller_path );
        }
        require_once($controller_path);

        $app = new $this->_controller();

        if (!method_exists($app, $this->_action)) {
            die("A action não existe: " . $this->_action);
        }
        $action = $this->_action;
        $app->$action();
    }

    public function loadModel($modelName)
    {
        $modelPath = $this->_path.'/app/modules/';

        if (strpos($modelName, '/') === false) {
            $class = $modelName;
            $curr_url = $_GET['url'];
            $curr_url = explode("/", $curr_url);
            $file = $modelPath . $curr_url[0]. '/models/' . $class . '.php';
        } else {
            $arrParts = explode("/", $modelName);
            $class = $arrParts[1];
            $file = $modelPath . $arrParts[0] . '/models/' . $class . '.php';

        }
        spl_autoload_register(function ($class) use( &$file) {
            if (file_exists($file)) {
                require_once($file);
            } else {
                die ('The model file does not exist: ' . $file);
            }
        });



    }

    function getLogFile($logType)
    {
        if ($logType == 'general') {
            return $this->_path.'/logs/helpdezk.log';
        } elseif($logType == 'email') {
            return $this->_path.'/logs/email.log';
        }
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


        if ($logLevel > $this->_logLevel)
            return false ;

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

    function getlogDateHour()
    {
        $dateHour = $this->getConfig('log_date_format');
        if (!empty($dateHour)){
            return "d/m/Y H:i:s";
        } else {
            return str_replace('%','',$dateHour );
        }

    }

    // Since April 28, 2017
    function getPrintDate()
    {
        return str_replace("%","",$this->dateFormat) . " " . str_replace("%","",$this->hourFormat);

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

    public function formatSaveDate($date) {
        $dbCommon = new common();
        $dateafter = $dbCommon->getSaveDate($date, $this->getConfig("date_format"));
        return "'".$dateafter."'";
    }

    public function formatDate($date) {
        $dbCommon = new common();
        $dateafter = $dbCommon->getDate($date, $this->getConfig("date_format"));
        return $dateafter;
    }

    /**
     * Method to send e-mails
     *
     * @param array $params E-mail params
     *
     * @return string true|false
     */
    public function sendEmailDefaultNew($params,$typesender=null,$token=null)
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

        if(!$typesender){
            $typesender = strpos($mail_host,'mandrill') !== false ? 'mandrill' : 'SMTP';
        }

        if(!$token){
            if(strpos($typesender,'mandrill') !== false){
                if(!$this->getConfig('mandrill_token')){
                    $this->logIt("Not found this param: 'mandrill_token', in config file" .' - program: ' . $this->program, 3, 'general', __LINE__);
                    return false;
                }

                $token =  $this->getConfig('mandrill_token');
            }
        }


        $mail = $this->returnMailer($typesender,$token);

        if($params['customHeader'] && $params['customHeader'] != ''){
            $arrCustomHead = explode(': ',$params['customHeader']);
            $customHead[$arrCustomHead[0]] = $arrCustomHead[1];
        }

        if ($this->getConfig('demo') == true) {
            $customHead['X-hdkLicence'] = 'demo';
        } else {
            $customHead['X-hdkLicence'] = $this->getConfig('license');
        }

        if($params['sender'] && $params['sender'] != ''){
            $mail_sender = $params['sender'];
        }

        if($params['sender_name'] && $params['sender_name'] != ''){
            $mail_title = '=?UTF-8?B?'.base64_encode($params['sender_name']).'?=';
        }

        $server = array(
            "host" => $mail_host,
            "port" => $mail_port,
            "method" => $mail_method,
            "domain" => $mail_domain,
            "auth" => $mail_auth,
            "username" => $mail_username,
            "password" => $mail_password

        );

        $paramsDone = array("msg" => $params['msg'],
            "msg2" => $params['msg2'],
            "mail_host" => $mail_host,
            "mail_domain" => $mail_domain,
            "mail_auth" => $mail_auth,
            "mail_port" => $mail_port,
            "mail_username" => $mail_username,
            "mail_password" => $mail_password,
            "mail_sender" => $mail_sender,
            "type_sender" => $typesender
        );

        if($params['idspool_recipient'] && $params['idspool_recipient'] != ''){
            $paramsDone['idspool_recip'] = $params['idspool_recipient'];
        }

        $arrMessage = array(
            "subject" => $params['subject'],
            "senderName" => $mail_title,
            "sender" => $mail_sender,
            "extra_header" => $customHead,
            "global_merge_vars" => array(),
            "merge_vars" => array(),
            "tags" => array(),
            "analytics_domains" => array(),
            "metadata" => array(),
            "recipient_metadata" => array(),
            "attachments" => $params['attachment'],
            "images" => array(),
            "server" => $server
        );



        if ($typesender == 'mandrill') {
            $aEmail = !is_array($params['address']) ? $this->makeArraySendTo($params['address']) : $params['address'];
            foreach ($aEmail as $key => $sendEmailTo) {
                $idEmail = ($params['idemail'] && $params['idemail'] != '')
                            ? $params['idemail']
                            : $this->saveTracker($params['idmodule'],$mail_sender,$sendEmailTo['to_address'],addslashes($params['subject']),addslashes($params['contents']));
                if(!$idEmail) {
                    $this->logIt("Error insert in tbtracker, " . $params['msg'] .' - program: ' . $this->program, 3, 'email', __LINE__);
                } else {
                    $paramsDone['idemail'] = $idEmail;
                    $arrMessage['to'] = array(array('email' => $sendEmailTo['to_address'],
                        'name' => $sendEmailTo['to_name'],
                        'type' => 'to'));
                    $arrMessage['body'] = $mail_header . $params['contents'] . $mail_footer;

                    $error_send = $this->isSendDone($mail,$arrMessage,$paramsDone);
                }
            }
        }else{

            if($params['tracker']) {

                $body = $mail_header . $params['contents'] . $mail_footer;
                $aEmail = !is_array($params['address']) ? $this->makeArraySendTo($params['address']) : $params['address'];

                foreach ($aEmail as $key => $sendEmailTo) {
                    $idEmail = ($params['idemail'] && $params['idemail'] != '')
                        ? $params['idemail']
                        : $this->saveTracker($params['idmodule'],$mail_sender,$sendEmailTo['to_address'],addslashes($params['subject']),addslashes($params['contents']));
                    if(!$idEmail) {
                        $this->logIt("Error insert in tbtracker, " . $params['msg'] .' - program: ' . $this->program, 3, 'email', __LINE__);
                    } else {
                        $paramsDone['idemail'] = $idEmail;
                        $arrMessage['to'] = $sendEmailTo['to_address'];
                        $trackerID = '<img src="'.$this->helpdezkUrl.'/tracker/'.$params['modulename'].'/'.$idEmail.'.png" height="1" width="1" />' ;
                        $arrMessage['body'] = $mail_header . $params['contents'] . $mail_footer . $trackerID;
                        echo PHP_EOL . 'send ' . $params['tracker'] . PHP_EOL ;
                        $error_send = $this->isSendDone($mail,$arrMessage,$paramsDone);
                    }
                }
            } else {
                $aEmail = ($params['idemail'] && $params['idemail'] != '')
                    ? $params['idemail']
                    : !is_array($params['address']) ? $this->makeArraySendTo($params['address']) : $params['address'];

                foreach ($aEmail as $key => $sendEmailTo) {
                    $idEmail = $this->saveTracker($params['idmodule'],$mail_sender,$sendEmailTo['to_address'],addslashes($params['subject']),addslashes($params['contents']));
                    if(!$idEmail) {
                        $this->logIt("Error insert in tbtracker, " . $params['msg'] .' - program: ' . $this->program, 3, 'email', __LINE__);
                    } else {
                        $paramsDone['idemail'] = $idEmail;
                        $arrMessage['to'] = $sendEmailTo['to_address'];
                        $arrMessage['body'] = $mail_header . $params['contents'] . $mail_footer;

                        $error_send = $this->isSendDone($mail,$arrMessage,$paramsDone);
                    }
                }
            }
        }


        if ($error_send)
            return false;
        else
            return true;

    }

    public function returnMailer($sender,$token=null)
    {


        $mailerDir = $this->_path . '/includes/classes/pipegrep/sendMail.php';


        if (!file_exists($mailerDir)) {
            die ('ERROR: ' .$mailerDir . ' , does not exist  !!!!') ;
        }

        require_once($mailerDir);

        $mail = new sendMail($sender,$token);

        return $mail;
    }

    public function isSendDone($objmail,$message,$params){
        $done = $objmail->sendEmail($message);

        if ($done['status'] == 'error') {
            if($this->log AND $_SESSION['EM_FAILURE_LOG'] == '1') {
                $this->logIt("Error send email, " . $params['msg'] . ' - program: ' . $this->program, 3, 'email', __LINE__);
                $this->logIt("Error send email, " . $params['msg2'] . ' - Error Info:: ' . $done['result']['message'] . ' - program: ' . $this->program, 3, 'email', __LINE__);
                $this->logIt("Error send email, " . $params['msg'] . ' - Variables: HOST: '.$params['mail_host'].'  DOMAIN: '.$params['mail_domain'].'  AUTH: '.$params['mail_auth'].' PORT: '.$params['mail_port'].' USER: '.$params['mail_username'].' PASS: '.$params['mail_password'].'  SENDER: '.$params['mail_sender'].' - program: ' . $this->program, 7, 'email', __LINE__);
            }
            $error_send = true ;
        } else {
            if($this->log AND $_SESSION['EM_SUCCESS_LOG'] == '1') {
                $toMsg = $params['type_sender'] == 'mandrill' ? "to ". $message['to'][0]['email'] : "to ". $message['to'];
                $senderMsg = " with ".$params['type_sender'];

                $logMsg = ($params['msg'] && $params['msg'] !='') ? $params['msg'] . ' ' .$toMsg . $senderMsg : $toMsg . $senderMsg;

                $this->logIt("Email Succesfully Sent, ".$logMsg  ,6,'email');
            }

            if ($params['type_sender'] == 'mandrill') {
                $this->saveMandrillID($params['idemail'],$done['result'][0]['_id']);
            }

            $this->_updateEmailSendTime($params['idemail']);



            $error_send = false;
        }

        return $error_send;

    }

    function saveMandrillID($idemail,$idmandrill)
    {
        $this->loadModel('admin/tracker_model');
        $dbTracker = new tracker_model();

        $ret = $dbTracker->insertMadrillID($idemail,$idmandrill);
        if(!$ret) {
            return false;
        } else {
            return 'ok';
        }

    }

    function _updateEmailSendTime($idemail)
    {
        $this->loadModel('admin/tracker_model');
        $dbTracker = new tracker_model();

        $ret = $dbTracker->updateEmailSendTime($idemail);
        if(!$ret) {
            return false;
        } else {
            return 'ok';
        }

    }

    public function makeArraySendTo($sentTo)
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
                        $bus = array(
                            'to_name'=> '',
                            'to_address' => $email_destino[$i]
                        );
                        array_push($aRet,$bus);
                    }
                }
            } else {
                $bus = array(
                    'to_name'=> '',
                    'to_address' => $email_destino
                );
                array_push($aRet,$bus);
            }
        } else {
            $bus = array(
                'to_name'=> '',
                'to_address' => $sentTo
            );
            array_push($aRet,$bus);
        }
        return $aRet;
    }

    function saveRecipientEmailID($idemail,$idspool_recipient)
    {
        $this->loadModel('emq/emails_model');
        $dbEmails = new emails_model();

        $ret = $dbEmails->insertRecipientEmailID($idemail,$idspool_recipient);
        if(!$ret) {
            return false;
        } else {
            return 'ok';
        }

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

    public function _getLanguageWord($smartyConfig)
    {
        $smarty = $this->_returnSmarty();
        return  $smarty->getConfigVars($smartyConfig);
    }

    public function _returnSmarty()
    {

        $smartPluginsDir = $this->_getHelpdezkPath(). "/system/smarty_plugins/";
        if (!file_exists($smartPluginsDir)) {
            die ('ERROR: ' .$smartPluginsDir . ' , does not exist  !!!!') ;
        }

        $smartCompileDir = $this->_getHelpdezkPath(). "/system/templates_c/";

        if (!file_exists($smartCompileDir)) {
            if (!mkdir($smartCompileDir, 0777, true)) {
                die ('ERROR: ' .$smartCompileDir . ' , does not exist and could not be created !!!!') ;
            }

        }
        if (!is_writable($smartCompileDir)) {
            if (!chmod($smartCompileDir,0777)){
                die($smartCompileDir . ' is not writable !!!') ;
            }

        }

        switch ($this->_getSmartyVersion()) {
            case 'smarty-old':
                $dirSmarty = $this->_getHelpdezkPath().'/includes/classes/smarty/smarty-old/Smarty.class.php';
                break;
            case 'smarty-2.6.30':
                $dirSmarty = $this->_getHelpdezkPath().'/includes/classes/smarty/smarty-2.6.30/libs/Smarty.class.php';
                break;
            case 'smarty-3.1.32':
                $dirSmarty = $this->_getHelpdezkPath().'/includes/classes/smarty/smarty-3.1.32/libs/Smarty.class.php';
                $dirPluginDefault = $this->_getHelpdezkPath().'/includes/classes/smarty/smarty-3.1.32/libs/plugins';
                break;
        }

        if (!file_exists($dirSmarty))
            die('Smarty Class doesn´t exists: ' . $dirSmarty . ' file: '. __FILE__);

        require_once($dirSmarty);

        $smarty = new Smarty;
        $smarty->debugging = false;
        $smarty->caching = false;
        $smarty->compile_dir = $smartCompileDir;

        $lang_default = $this->getConfig("lang");
        $license =  $this->getConfig("license");

        $smartConfigFile = $this->_getHelpdezkPath().'/app/lang/' . $lang_default . '.txt';
        if (!file_exists($smartConfigFile)) {
            die('Lang file: ' . $smartConfigFile . ' does not exist !!!!') ;
        }

        if (version_compare(Smarty::SMARTY_VERSION, '3', '>=' )) {
            $smarty->configLoad($smartConfigFile, $license);
            $smarty->setPluginsDir(array($dirPluginDefault,$smartPluginsDir));
        } else {
            $smarty->config_load($smartConfigFile, $license);
            $smarty->plugins_dir[] = $smartPluginsDir;
        }

        return $smarty;
    }

    // Since October 03, 2019
    public function _getHelpdezkPath()
    {

        $path_parts = pathinfo(dirname(__FILE__));
        $document_root=$path_parts['dirname'];


        if(substr($document_root, -1)!='/'){
            $document_root=$document_root.'/';
        }


        return  $document_root ;
    }

    // pipetodo [albandes] : Tem que ir para o config.php
    public function _getSmartyVersion()
    {
        return 'smarty-3.1.32' ;
    }

    public function setSmartyVersionNumber($version)
    {
        $this->_smartyVersionNumber = $version;
    }

    public function getSmartyVersionNumber()
    {
        return $this->_smartyVersionNumber;
    }

    public function retornaSmarty()
    {

        $smartPluginsDir = $this->_getHelpdezkPath() . "/system/smarty_plugins/";
        if (!file_exists($smartPluginsDir)) {
            $this->logIt('ERROR: ' . $smartPluginsDir . ' , does not exist  !!!! Method: '. __METHOD__,3,'general',__LINE__);
            return false;
        }

        $smartCompileDir = $this->_getHelpdezkPath() . "/system/templates_c/";

        if (!file_exists($smartCompileDir)) {
            if (!mkdir($smartCompileDir, 0777, true)) {
                $this->logIt('ERROR: ' . $smartCompileDir . ' , does not exist and could not be created !!!! Method: '. __METHOD__,3,'general',__LINE__);
                return false;
            }

        }
        if (!is_writable($smartCompileDir)) {
            if (!chmod($smartCompileDir, 0777)) {
                $this->logIt($smartCompileDir . ' is not writable !!! Method: '. __METHOD__,3,'general',__LINE__);
                return false;
            }

        }

        switch ($this->smartyVersion) {
            case 'smarty-old':
                $dirSmarty = $this->_getHelpdezkPath() . '/includes/classes/smarty/smarty-old/Smarty.class.php';
                break;
            case 'smarty-2.6.30':
                $dirSmarty = $this->_getHelpdezkPath() . '/includes/classes/smarty/smarty-2.6.30/libs/Smarty.class.php';
                break;
            case 'smarty-3.1.32':
                $dirSmarty = $this->_getHelpdezkPath() . '/includes/classes/smarty/smarty-3.1.32/libs/Smarty.class.php';
                $dirPluginDefault = $this->_getHelpdezkPath() . '/includes/classes/smarty/smarty-3.1.32/libs/plugins';
                break;
        }

        if (!file_exists($dirSmarty)){
            $this->logIt('Smarty Class doesn´t exists: ' . $dirSmarty . ' file: ' . __FILE__ .' Method: '. __METHOD__,3,'general',__LINE__);
            return false;
        }
            

        require_once($dirSmarty);

        $smarty = new Smarty;
        $smarty->debugging = false;
        $smarty->caching = false;
        $smarty->template_dir = VIEWS;
        $smarty->compile_dir = $smartCompileDir;

        $lang_default = $this->getConfig("lang");
        $license = $this->getConfig("license");

        $smartConfigFile = $this->_getHelpdezkPath() . '/app/lang/' . $lang_default . '.txt';
        if (!file_exists($smartConfigFile)) {
            $this->logIt('Lang file: ' . $smartConfigFile . ' does not exist !!!! file: ' . __FILE__ .' Method: '. __METHOD__,3,'general',__LINE__);
            return false;
        }

        $this->setSmartyVersionNumber(Smarty::SMARTY_VERSION);

        if (version_compare($this->getSmartyVersionNumber(), '3', '>=')) {
            $smarty->configLoad($smartConfigFile, $license);
            $smarty->setPluginsDir(array($dirPluginDefault, $smartPluginsDir));
        } else {
            $smarty->config_load($smartConfigFile, $license);
            $smarty->plugins_dir[] = $smartPluginsDir;
        }

        $smarty->assign('lang', $lang_default);
        $smarty->assign('date_format', $this->getConfig("date_format"));
        $smarty->assign('hour_format', $this->getConfig("hour_format"));
        $smarty->assign('demo', $this->getConfig("demo"));
        $smarty->assign('theme', $this->getConfig("theme"));
        $smarty->assign('path', path);
        $smarty->assign('id_mask', $this->getConfig('id_mask'));
        $smarty->assign('ein_mask', $this->getConfig('ein_mask'));
        $smarty->assign('zip_mask', $this->getConfig('zip_mask'));
        $smarty->assign('phone_mask', $this->getConfig('phone_mask'));
        $smarty->assign('cellphone_mask', $this->getConfig('cellphone_mask'));

        $smarty->assign('pagetitle', $this->getConfig("page_title"));

        return $smarty;
    }

}

?>