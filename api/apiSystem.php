<?php
class apiSystem {

    private $_url, $_explode;
    public $_controller, $_action, $_params, $_module, $_config;

    public $_helpdezkUrl ;
    public $_helpdezkPath ;
    public $_helpdezkVersionNumber ;
    public $_version ;
    public $_smartyVersion;

    /**
     * Is set external storage
     *
     * @var bool
     */
    public $_externalStorage;

    /**
     * External Storage Path
     *
     * @var bool
     */
    public $_externalStoragePath;

    /**
     * External Storage Url
     *
     * @var bool
     */
    public $_externalStorageUrl;

    public function __construct()
    {


        $this->setConfig();

        // new

        $this->_helpdezkUrl = $this->getHelpdezkUrl();
        $this->_helpdezkPath = $this->_getRootPath();

        $this->getHelpdezkVersion();

        $this->_helpdezkVersionNumber = $this->getHelpdezkVersionNumber() ;
        $this->_smartyVersion = $this->getSmartyVersion();

        $this->database     = $this->getConfig('db_connect');
        $this->pathDefault  = $this->getConfig('path_default');
        $this->dateFormat 	= $this->getConfig('date_format');
        $this->hourFormat 	= $this->getConfig('hour_format');
        $this->license 		= $this->getConfig('license');
        $this->lang         = $this->getConfig('lang');
        $this->enterprise	= $this->getConfig('enterprise');
        $this->demo	        = $this->getConfig('demo');
        $this->logFile      = $this->_helpdezkPath.'logs/api.log';

        // External storage settings
        $this->_externalStorage     = $this->getExternalStorage();
        if ($this->_externalStorage) {
            $this->_externalStoragePath = $this->getExternalStoragePath();
            $this->_externalStorageUrl = $this->getExternalStorageUrl();
        }


    }

    public function getEnterprise()	{return $this->enterprise;}
    public function getDemo()	    {return $this->demo;}
    public function getLang()		{return $this->lang;}
    public function getPathDefault(){return $this->pathDefault;}
    public function getLicense()	{return $this->license;}
    public function getDateFormat()	{return $this->dateFormat;}
    public function getHourFormat()	{return $this->hourFormat;}
    public function getApiLog()     {return $this->logFile;}

    /**
     * Save base64 image
     *
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     * @since 1.1.7 First time this was introduced.
     *
     * @param string  $base64ImageString            Base64 image string to save
     * @param string  $outputFileName               Filename to save
     * @param string  $pth          File path       Path with slash at the end
     *
     *
     * @return boll Sucess|Error
     *
     */
    function saveBase64File($base64ImageString, $outputFileName, $path="" )
    {

        $splited = explode(',', substr( $base64ImageString , 5 ) , 2);
        $mime=$splited[0];
        $data=$splited[1];

        $saved_file = file_put_contents( $path . $outputFileName, base64_decode($data) );

        if (($saved_file === false) || ($saved_file == -1)) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * Teste is string is base64 image
     *
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     * @since 1.1.7 First time this was introduced.
     *
     * @param string  $str            Base64 image string to test
     *
     * @return boll Sucess|Error
     *
     */
    function isBase64Encoded($str)
    {
        try
        {
            $decoded = base64_decode($str, true);

            if ( base64_encode($decoded) === $str ) {
                return true;
            }
            else {
                return false;
            }
        }
        catch(Exception $e)
        {
            // If exception is caught, then it is not a base64 encoded string
            return false;
        }

    }


    /**
     * Returns External Storage Url
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     * @since 1.1.6 First time this was introduced.
     *
     * @return string External Storage Url
     *
     */
    public function getExternalStorageUrl()
    {
        $externalStorageUrl = $this->getConfig('external_storage_url') ;

        if (empty($externalStorageUrl))
            die("The external url is empty in config.php. Method: " . __METHOD__ . ", line: " . __LINE__);

        if (substr($externalStorageUrl, -1) == "/" || substr($externalStorageUrl, -1) == "\\")
            $externalStorageUrl = substr($externalStorageUrl, 0, -1);

        return $externalStorageUrl ;
    }

    /**
     * Returns if external storage is set
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     * @since 1.1.6 First time this was introduced.
     *
     * @return bool  true|false
     *
     */
    public function getExternalStorage()
    {
        $externalStorage = $this->getConfig('external_storage') ;
        if (empty($externalStorage) || $externalStorage == false)
            return false;
        else
            return $externalStorage ;
    }

    /**
     * Returns External Storage Path
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     * @since 1.1.6 First time this was introduced.
     *
     * @return string External Storage Path
     *
     */
    public function getExternalStoragePath()
    {
        $externalStoragePath = $this->getConfig('external_storage_path') ;

        if (substr($externalStoragePath, -1) == "/" || substr($externalStoragePath, -1) == "\\") {
            $externalStoragePath = substr($externalStoragePath, 0, -1);
        }

        $array = array( "/files",
            "/helpdezk/attachments/",
            "/helpdezk/noteattachments/",
            "/helpdezk/dashboard/",
            "/tmp",
            "/logs",
            "/icons",
            "/logos/default/",
            "/photos/default/");

        if (!file_exists($externalStoragePath)) {
            die ("The external storage directory does not exist: {$externalStoragePath}, method: ". __METHOD__ .", line: ". __LINE__ );
        } else {
            $arrayExist = array();
            $arrayWrite = array();
            $displayError = false;
            foreach ($array as $dir) {
                if (!is_writable($externalStoragePath . $dir)) {
                    array_push ($arrayWrite , $externalStoragePath . $dir );
                }
                if (!file_exists($externalStoragePath . $dir )) {
                    array_push ($arrayExist , $externalStoragePath . $dir );
                }
            }
            if (count($arrayWrite) > 0 ) {
                foreach ($arrayWrite as $writeError) {
                    echo "The external storage sub directory(s) does not exist: {$writeError} <br>";
                }
                $displayError = true;
            }

            if (count($arrayExist) > 0 ) {
                foreach ($arrayExist as $dirError) {
                    echo "The external storage sub directory(s) does not exist: {$dirError} <br>";
                }
                $displayError = true;
            }

            if ($displayError)
                die("method: ". __METHOD__ .", line: ". __LINE__ );
        }

        return $externalStoragePath;
    }


    // new
    public function _changePassword($idPerson,$newPassword)
    {
        $data = new person_model();
        return $data->changePassword($idPerson, $newPassword);

    }
    // new
    public function getHelpdezkVersion()
    {
        // Read the version.txt file
        $versionFile = $this->_helpdezkPath . "version.txt" ;
        if (is_readable($versionFile)) {
            $info = file_get_contents($versionFile,FALSE, NULL, 0, 50);

            if ($info) {
                $this->_version = trim($info);
            } else {
                $this->_version = '1.0';
            }
        } else {
            $this->_version = '1.0';
        }

    }

    // new
    public function getHelpdezkVersionNumber()
    {

        $exp = explode('-',$this->_version);
        return $exp[2];
    }

    // new
    function isMysql($haystack)
    {
        return strpos($haystack, 'mysql') !== false;
    }

    // new
    public function getHelpdezkUrl()
    {

        $hdkUrl = $this->getConfig('hdk_url');
        if(substr($hdkUrl, 0,1) == '/')
            $hdkUrl = substr($hdkUrl,0,-1);
        return $hdkUrl;
    }

    public function setConfig($type = null, $value = null)
    {

        include $this->_getRootPath(). 'includes/config/config.php';

        if($type && $value){
            $this->_config[$type] = $value;
        } else{
            $this->_config = $config;
        }

    }

    public function getConfig($param)
    {
        //echo 'The calling function: ' . $param . ' - ' . debug_backtrace()[1]['function'];
        if (empty($param))
            return " ";
        else
            return $this->_config[$param];
    }

    public function _getRootPath()
    {
        $path_parts = pathinfo(dirname(__FILE__));
        return str_replace("\\","/",$path_parts['dirname']).'/' ;
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

    // Since November 03, 2017
    public function getAdoDbVersion()
    {
        $adodb = $this->getConfig('adodb');
        if (empty($adodb))
            $adodb = 'adodb-5.20.9';
        return $adodb;
    }

    public function retornaSmarty() {
        //die(SMARTY);
        $smartPluginsDir = $this->getHelpdezkPath(). "/system/smarty_plugins/";
        if (!file_exists($smartPluginsDir)) {
            die ('ERROR: ' .$smartPluginsDir . ' , does not exist  !!!!') ;
        }

        $smartCompileDir = $this->getHelpdezkPath(). "/system/templates_c/";

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


        switch ($this->_smartyVersion) {
            case 'smarty-old':
                $dirSmarty = $this->_helpdezkPath . 'includes/classes/smarty/smarty-old/Smarty.class.php';
                break;
            case 'smarty-2.6.30':
                $dirSmarty = $this->_helpdezkPath . 'includes/classes/smarty/smarty-2.6.30/libs/Smarty.class.php';
                break;
            case 'smarty-3.1.32':
                $dirSmarty = $this->_helpdezkPath . 'includes/classes/smarty/smarty-3.1.32/libs/Smarty.class.php';
                $dirPluginDefault = $this->_helpdezkPath . 'includes/classes/smarty/smarty-3.1.32/libs/plugins';
                break;
        }


        require_once($dirSmarty);

        $smarty = new Smarty;
        $smarty->debugging = false;
        $smarty->caching = false;

        //$smarty->template_dir = VIEWS;

        $smarty->compile_dir = $smartCompileDir;

        $lang_default = $this->getConfig("lang");
        $license =  $this->getConfig("license");

        $smartConfigFile = $this->getHelpdezkPath().'/app/lang/' . $lang_default . '.txt';
        if (!file_exists($smartConfigFile)) {
            die('Lang file: ' . $smartConfigFile . ' does not exist !!!!') ;
        }

        $this->setSmartyVersionNumber(Smarty::SMARTY_VERSION);

        if (version_compare($this->getSmartyVersionNumber(), '3', '>=' )) {
            $smarty->configLoad($smartConfigFile, $license);
            $smarty->setPluginsDir(array($dirPluginDefault,$smartPluginsDir));
        } else {
            $smarty->config_load($smartConfigFile, $license);
            $smarty->plugins_dir[] = $smartPluginsDir;
        }

        $smarty->assign('lang',         $lang_default);
        $smarty->assign('date_format',  $this->getConfig("date_format"));
        $smarty->assign('hour_format',  $this->getConfig("hour_format"));
        $smarty->assign('demo',         $this->getConfig("demo"));
        $smarty->assign('theme',        $this->getConfig("theme"));
        $smarty->assign('path',         path);
        $smarty->assign('id_mask', $this->getConfig('id_mask'));
        $smarty->assign('ein_mask', $this->getConfig('ein_mask'));
        $smarty->assign('zip_mask', $this->getConfig('zip_mask'));
        $smarty->assign('phone_mask', $this->getConfig('phone_mask'));
        $smarty->assign('cellphone_mask', $this->getConfig('cellphone_mask'));

        $smarty->assign('pagetitle',    $this->getConfig("page_title"));

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
    }

//

    public function formatDate($date) {
        $dbCommon = new common();
        $dateafter = $dbCommon->getDate($date, $this->getConfig("date_format"));
        return $dateafter;
    }

    public function formatHour($date) {
        $bd = new common();
        $dateafter = $bd->getDate($date, $this->getConfig("hour_format"));
        return $dateafter;
    }

    public function formatDateHour($date) {
        $bd = new common();
        $dateafter = $bd->getDateTime($date, $this->getConfig("date_format")." ".$this->getConfig("hour_format"));
        return $dateafter;
    }

    public function formatSaveDate($date) {

        $dbCommon = new common();
        $dateafter = $dbCommon->getSaveDate($date, $this->getConfig("date_format"));
        $database = $this->getConfig('db_connect');
        if ($this->isMysql($database)) {
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
        $dbCommon = new common();
        $dateafter = $dbCommon->getSaveDate($date, $this->getConfig("date_format")." ".$this->getConfig("hour_format"));
        $database = $this->getConfig('db_connect');
        if ($this->isMysql($database)) {
            return "'".$dateafter."'";
        } elseif ($database == 'oci8po') {
            return $dateafter;
        }
    }


//
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
     * @author Rogerio Albandes <rogerio.albandes@pipegrep.com.br>
     *
     * @param string  $str String to write
     * @param string  $file  Log filename
     *
     * @since April 28, 2017
     *
     * @return string true|false
     */
    function logit($str, $file)
    {
        if (!file_exists($file)) {
            if($fp = fopen($file, 'a')) {
                @fclose($fp);
                return $this->logit($str, $file);
            } else {
                return false;
            }
        }
        if (is_writable($file)) {
            $str = time().'	'.$str;
            $handle = fopen($file, "a+");
            fwrite($handle, $str."\r\n");
            fclose($handle);
            return true;
        } else {
            return false;
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


    /**
     * Writes a message to the log file.
     *
     * @param  string  $message  The message to write
     * @param  string  $type     The type of log message (e.g. INFO, DEBUG, ERROR, etc.)
     * @param  string  $logfile  The log's file
     *
     * Since May 16, 2017
     */
    public function log($message, $type = 'INFO',$logfile)
    {

        // Set the name of the log file
        $filename = $logfile;

        if ( ! file_exists($filename))
        {
            // Create the log file
            file_put_contents($filename, '');

            // Allow anyone to write to log files
            chmod($filename, 0666);
        }

        // Write the message into the log file
        // Format: time --- type: message
        file_put_contents($filename, date($this->getPrintDate()) .' -- '.$type.': '.$message.PHP_EOL, FILE_APPEND);

    }

    function _getLangVar($var){

        $trimmed = file($this->_getRootPath()."app/lang/".$this->getLang().".txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($trimmed as $line_num => $line) {
            if (substr($line, 0, 1) == '#')
                continue;

            $line_exp = explode("=",$line);
            $langVar[trim($line_exp[0])] = substr(trim($line_exp[1]), 1, -1);

        }

        return $langVar[$var];
    }

    function _getSessionValues($idPerson)
    {
        $bd = new index_model();
        $data = $bd->getConfigData();
        while (!$data->EOF) {
            $ses = $data->fields['session_name'];
            $val = $data->fields['value'];
            $config[$ses] = $val;
            $data->MoveNext();
        }

        $typeuser = $bd->selectDataSession($idPerson);
        $config['SES_NAME_PERSON'] = $typeuser->fields['name'];
        $config['SES_TYPE_PERSON'] = $typeuser->fields['idtypeperson'];
        $config['SES_IND_CODIGO_ANOMES'] = true;
        $config['SES_COD_EMPRESA'] = $typeuser->fields['idjuridical'];
        $config['SES_COD_TIPO'] = $typeuser->fields['idtypeperson'];

        return $config;

    }

    public function _getEspecificValueSession($idSession)
    {
        $bd = new index_model();
        $data = $bd->getConfigValue($idSession);
        if($data){
            return $data;
        } else {
            return false ;
        }

    }

    public function _getIpAddress()
    {

        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && ip2long($_SERVER["HTTP_X_FORWARDED_FOR"]) !== false) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } elseif (isset($_SERVER["HTTP_CLIENT_IP"])  && ip2long($_SERVER["HTTP_CLIENT_IP"]) !== false) {
                $ip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $ip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR') && ip2long(getenv('HTTP_X_FORWARDED_FOR')) !== false) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP') && ip2long(getenv('HTTP_CLIENT_IP')) !== false) {
                $ip = getenv('HTTP_CLIENT_IP');
            } else {
                $ip = getenv('REMOTE_ADDR');
            }
        }

        if ($ip == '::1')
            $ip ='127.0.01' ;
        return $ip;
    }

    function _getHelpdezkVersion($var){
        $file = fopen($this->_getRootPath()."version.txt", 'r');
        if($this->getEnterprise()) {
            $type = 'enterprise';
        } else {
            if($this->getDemo()) {
                $type = 'demo';
            }  else {
                $type = 'community';
            }
        }
        $version = 'helpdezk-'.$type.'-'.fgets($file);
        fclose($file);
        return $version;
    }

    /**
     * Check if a given ip is in a network
     * @param  string $ip    IP to check in IPV4 format eg. 127.0.0.1
     * @param  string $range IP/CIDR netmask eg. 127.0.0.0/24, also 127.0.0.1 is accepted and /32 assumed
     * @return boolean true if the ip is in this range / false if not.
     */
    function _ipInRange( $ip, $range ) {
        if ( strpos( $range, '/' ) == false ) {
            $range .= '/32';
        }
        // $range is in IP/CIDR format eg 127.0.0.1/24
        list( $range, $netmask ) = explode( '/', $range, 2 );
        $range_decimal = ip2long( $range );
        $ip_decimal = ip2long( $ip );
        $wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
        $netmask_decimal = ~ $wildcard_decimal;
        return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
    }

    public function getSmartyVersion()
    {
        return 'smarty-3.1.32' ;
    }

    public function setSmartyVersionNumber($version)
    {
        $this->_smartyVersionNumber = $version;
    }

    public function getSmartyVersionNumber()
    {
        return $this->_smartyVersionNumber ;
    }

    /**
     * Method to send e-mails
     *
     * @author Rogerio Albandes <rogerio.albandes@pipegrep.com.br>
     *
     * @param string  $subject      E-mail subject
     * @param string  $body         E-mail body
     * @param array   $address      Addreaesse
     * @param boolean $log          If it will log
     * @param string  $log_text     Log text
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

        $mail->From = $mail_sender;
        $mail->FromName = $mail_title;

        if ($mail_host)
            $mail->Host = $mail_host;
        if (isset($mail_port) AND !empty($mail_port)) {
            $mail->Port = $mail_port;
        }

        $mail->Mailer = $mail_method;
        $mail->SMTPAuth = $mail_auth;
        /*
        if (strpos($mail_username,'gmail') !== false) {
            $mail->SMTPSecure = "tls";
        }
        */

        if ($emconfigs['EM_TLS'])
            $mail->SMTPSecure = 'tls';

        $mail->Username = $mail_username;
        $mail->Password = $mail_password;

        $mail->AltBody 	= "HTML";
        $mail->Subject 	= '=?UTF-8?B?'.base64_encode($params['subject']).'?=';

        $mail->SetLanguage('br', $this->_helpdezkPath . "/includes/classes/phpMailer/language/");

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

        // Tracker
        if($params['tracker']) {

            $body = $mail_header . $params['contents'] . $mail_footer;
            $aEmail = $this->makeArrayTracker($params['address']);

            foreach ($aEmail as $key => $sendEmailTo) {
                $idEmail = $this->saveTracker($params['idmodule'],$mail_sender,$sendEmailTo,addslashes($params['subject']),addslashes($params['contents']));
                if(!$idEmail) {
                    $this->logIt("Error insert in tbtracker, " . $params['msg'] .' - program: ' . $this->program, 3, 'email', __LINE__);
                } else {
                    $mail->AddAddress($sendEmailTo);
                    $trackerID = '<img src="'.$this->helpdezkUrl.'/tracker/'.$this->modulename.'/'.$idEmail.'.png" height="1" width="1" />' ;
                    $mail->Body = $mail_header . $params['contents'] . $mail_footer . $trackerID;
                    $error_send = $this->isEmailDone($mail,$paramsDone);
                }
                $mail->ClearAddresses();
            }
        } else {
            //Checks for more than 1 email address at recipient
            $this->makeSentTo($mail,$params['address']);
            $mail->Body = $mail_header . $params['contents'] . $mail_footer;
            $error_send = $this->isEmailDone($mail,$paramsDone);
        }

        $mail->ClearAttachments();
        if ($error_send)
            return false;
        else
            return true;

    }

    // Since October 25, 2017
    public function returnPhpMailer()
    {

        $phpMailerDir = $this->_helpdezkPath . 'includes/classes/phpMailer/class.phpmailer.php';

        if (!file_exists($phpMailerDir)) {
            die ('ERROR: ' .$phpMailerDir . ' , does not exist  !!!!') ;
        }

        require_once($phpMailerDir);

        $mail = new phpmailer();

        return $mail;
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
            /*
            if($this->log AND $_SESSION['EM_SUCCESS_LOG'] == '1') {
                $this->logIt("Email Succesfully Sent, ". $params['msg']  ,6,'email');
            }
            */

            $error_send = false ;
        }

        return $error_send;

    }
}

