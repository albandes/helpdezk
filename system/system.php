<?php

class System {

    private $_url, $_explode;
    public $_controller, $_action, $_params, $_module, $_config, $_version;
    public $_smartyVersion ;
    var $pdfAligns, $pdfWidths, $pdfLeftMargin, $pdfLogo, $pdfTitle, $a_pdfHeaderData, $pdfPage, $pdfFontFamily, $pdfFontStyle, $pdfFontSyze;

    public $_logLevel ;
    public $_logHost ;
    public $_logRemoteServer ;
    public $_logFacility;

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
        $this->setUrl();
        $this->setExplode();
        $this->setModule();
        $this->setController();
        $this->setAction();
        $this->setParams();

        $this->database     = $this->getConfig('db_connect');
        $this->pathDefault  = $this->getConfig('path_default');
        $this->dateFormat 	= $this->getConfig('date_format');
        $this->hourFormat 	= $this->getConfig('hour_format');
        $this->langDefault  = $this->getConfig('lang');

        $this->printDate    = $this->getPrintDate();
        $this->logDateHour  = $this->getlogDateHour();
        $this->helpdezkUrl  = $this->getHelpdezkUrl();
        $this->helpdezkPath = $this->getHelpdezkPath();

        // Helpdezk Logos
        //$this->headerLogoImage = $this->getHeaderLogoImage();


        // Version settings
        $this->getHelpdezkVersion();
        $this->helpdezkName = $this->getHelpdezkName();
        $this->helpdezkType = $this->getHelpdezkType();
        $this->helpdezkVersionNumber = $this->getHelpdezkVersionNumber();
        $this->smartyVersion = $this->getSmartyVersion();
        $this->demoVersion = $this->getConfig('demo');

        $this->jquery = $this->getJqueryVersion();
        $this->summernote = $this->getSummerNoteVersion();

        // External storage settings
        $this->_externalStorage     = $this->getExternalStorage();
        if ($this->_externalStorage) {
            $this->_externalStoragePath = $this->getExternalStoragePath();
            $this->_externalStorageUrl = $this->getExternalStorageUrl();
        }

        $this->logFile = $this->getLogFile('general');
        $this->logFileEmail  = $this->getLogFile('email');

    }

    /**
     * Destroys the session and sends it to the login page, used for unauthorized access.
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     *
     * @return void
     *
     */
    public function accessDenied()
    {
        $this->sessionDestroy();
        header('Location:' . $this->helpdezkUrl . '/admin/login');

    }

    public function protectFormInput()
    {
        if(!empty($_POST)) {
            foreach ($_POST as $mKey => $mValue)
            {
                $arrayPOST[ $mKey ] = $this->_protect($mValue);
            }
            $_POST = $arrayPOST;
        }

        if(!empty($_GET)) {
            foreach ($_GET as $mKey => $mValue)
            {
                $arrayGET[ $mKey ] = $this->_protect($mValue);
            }
            $_GET  = $arrayGET;
        }
    }

    /**
     * Return a string / Array protected against SQL / Blind / XSS Injection
     *
     * @param $str
     * @return array|string
     */
    public function _protect($str) {
        $allowableTags = '<p><br><span><div><strong><H1><b><u><i>';
        if( !is_array( $str ) ) {
            $str = preg_replace( '/\b(from|select|insert|delete|where|drop|union|order|update|database|FROM|SELECT|INSERT|DELETE|WHERE|DROP|UNION|ORDER|UPDATE|DATABASE|AND|and|HAVING|having|SLEEP|sleep|OR|or)\b/i', '', $str );
            $str = preg_replace( '/\b(&lt;|<)?script(\/?(&gt;|>(.*))?)\b/i', '', $str );
            $tbl = get_html_translation_table( HTML_ENTITIES );
            $tbl = array_flip( $tbl );
            $str = addslashes( $str );
            $str = strip_tags( $str, $allowableTags );
            return strtr( $str, $tbl );
        } else {
            return array_filter( $str, "_protect" );
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


    public function getArrayScreenFields($idModule,$personType,$formId)
    {
        $dbCommon = new common();
        $rsScreen =  $dbCommon->getScreenFieldEnable($idModule,$personType,$formId);
        $arrScreen = array();
        while (!$rsScreen->EOF) {
            $arrScreen[$rsScreen->fields['fieldid']] = $rsScreen->fields['enable'];
            $rsScreen->MoveNext();
        }
        return $arrScreen ;

    }

    public function  getScreenFieldEnable($arrScreen,$fieldid)
    {

        if (array_key_exists($fieldid, $arrScreen)) {
            if ($arrScreen[$fieldid] == 'Y')
                return true;
            else
                return false;
        } else {
            return true;
        }

    }

    function getLogFile($logType)
    {

        if ($this->_externalStorage) {
            $dirLog = $this->_externalStoragePath . '/logs/';
        } else{
            $dirLog = $this->getHelpdezkPath().'/logs/';
        }

        if(!is_dir($dirLog)) {
            mkdir ($dirLog, 0777 ); // create dir
        }else{
            if(!is_writable($dirLog)) {    //validation
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

    // Since December, 03
    public function makeNavVariables($smarty,$module = 'helpdezk')
    {

        $idPerson = $_SESSION['SES_COD_USUARIO'];
        // Modules
        $aModules = $this->getArrayModulesByPerson($idPerson);
        $smarty->assign(modules, $aModules);

        // Menu
        //if ($this->getConfig('module_default') == 'helpdezk')
        if($this->isActiveHelpdezk())
            $hasHelpdezk = true;
        else
            $hasHelpdezk = false;

        if($_SESSION['SES_COD_USUARIO'] == 1){$smarty->assign('isroot', true);}
        if($_SESSION['SES_TYPE_PERSON'] == 1 && $_SESSION['SES_COD_USUARIO'] != 1){$smarty->assign('hasadmin', true);}
        $smarty->assign('adminhome', $this->helpdezkUrl.'/admin/home/index');
        $smarty->assign('adminlogo', 'adm_header.png');
        $smarty->assign('navlogin', $idPerson == 1 ? $_SESSION['SES_NAME_PERSON'] : $_SESSION['SES_LOGIN_PERSON']);


        $smarty->assign('hashelpdezk', $hasHelpdezk);
        $smarty->assign('helpdezkhome', $this->helpdezkUrl.'/helpdezk/home/index');
        $smarty->assign('logout', $this->helpdezkUrl . '/main/home/logout');

        // Title
        $smarty->assign('title', $this->getConfig('page_title'));
        // Warnings
        $smarty->assign('total_warnings', $this->getNumNewEwarnings($idPerson));
        // Logo
        $smarty->assign('headerlogo_url', $this->getHeaderLogoFullUrl());

        // JS Variables
        $smarty->assign('path', path);
        $smarty->assign('theme', $this->getTheme());
        $smarty->assign('lang', $this->langDefault);
        $smarty->assign('id_mask', $this->getConfig('id_mask'));
        $smarty->assign('ein_mask', $this->getConfig('ein_mask'));
        $smarty->assign('zip_mask', $this->getConfig('zip_mask'));
        $smarty->assign('phone_mask', $this->getConfig('phone_mask'));
        $smarty->assign('cellphone_mask', $this->getConfig('cellphone_mask'));

        $mascdatetime = $this->dateFormat . ' ' . $this->hourFormat;
        $smarty->assign('mascdatetime', str_replace('%','',$mascdatetime));

        $mascdate = $this->dateFormat;
        $smarty->assign('mascdate', str_replace('%','',$mascdate));

        if(!$_SESSION['SES_TIME_SESSION'])
            $smarty->assign('timesession', 600);
        else
            $smarty->assign('timesession', $_SESSION['SES_TIME_SESSION']);
        // End JS variables

        $smarty->assign('jquery_version', $this->jquery);
        $smarty->assign('jqgrid_i18nFile', $this->getFileI18n('jqgrid'));
        $smarty->assign('navBar', 'file:'.$this->getHelpdezkPath().'/app/modules/main/views/nav-main.tpl');

        $this->makePersonData($smarty);
        $this->makeConfigExternalData($smarty);

    }
    public function makeFooterVariables($smarty)
    {
        $smarty->assign('version', $this->helpdezkName);
        $smarty->assign('footer', 'file:'.$this->getHelpdezkPath().'/app/modules/main/views/footer-main.tpl');
        $smarty->assign('configusermodal', 'file:'.$this->getHelpdezkPath().'/app/modules/helpdezk/views/modals/main/modalPersonData.tpl');
        $smarty->assign('userpwdmodal', 'file:'.$this->getHelpdezkPath().'/app/modules/helpdezk/views/modals/main/modal-change-user-password.tpl');
        $smarty->assign('configExternalModal', 'file:'.$this->getHelpdezkPath().'/app/modules/main/views/modal/main/modal-config-external.tpl');

    }
    public function isActiveHelpdezk()
    {

        $dbCommon = new common();
        return $dbCommon->isActiveHelpdezk();
    }

    public function pathModuleDefault()
    {
        $dbCommon = new common();
        $rs = $dbCommon->getModule("where defaultmodule='YES'");
        return $rs->fields['path'];
    }

    public function getHelpdezkVersionNumber()
    {
        $exp = explode('-',$this->_version);
        return $exp[2];
    }

    public function getHelpdezkType()
    {
        $exp = explode('-',$this->_version);
        return $exp[1];
    }

    public function getHelpdezkName()
    {
        return $this->_version;
    }

    public function getHelpdezkVersion()
    {
        // Read the version.txt file
        $versionFile = $this->helpdezkPath . "/version.txt" ;

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

    /**
     * Returns Header logo Url
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     * @since 1.1.6 First time this was introduced.
     *
     * @return string Header logo Url
     *
     */
    public function getHeaderLogoFullUrl()
    {
        $image = $this->getHeaderLogoImage();
        if ($this->_externalStorage) {
            return $this->_externalStorageUrl . '/logos/' . $image ;
        } else{
            return $this->helpdezkUrl . '/app/uploads/logos/' . $image ;
        }
    }

    /**
     * Returns Login logo Url
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     * @since 1.1.6 First time this was introduced.
     *
     * @return string Login logo Url
     *
     */
    public function getLoginLogoFullUrl()
    {
        $image = $this->getLoginLogoImage();
        if ($this->_externalStorage) {
            return $this->_externalStorageUrl . '/logos/' . $image ;
        } else{
            return $this->helpdezkUrl . '/app/uploads/logos/' . $image ;
        }
    }

    public function getReportsLogoFullUrl()
    {

    }

    /**
     * Returns Header logo's Image Path
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     * @since 1.1.6 First time this was introduced.
     *
     * @return Header logo's Image Path
     *
     */
    public function getHeaderLogoImage()
    {
        $dbCommon = new common();
        $rsLogo =  $dbCommon->getHeaderLogo();

        if($this->_externalStorage) {
            $pathLogoImage = $this->_externalStoragePath . '/logos/' . $rsLogo->fields['file_name'] ;
        } else {
            $pathLogoImage = $this->helpdezkPath . '/app/uploads/logos/' . $rsLogo->fields['file_name'] ;
        }

        if(empty($rsLogo->fields['file_name']) or !file_exists($pathLogoImage))
            return 'default/header.png';
        else
            return $rsLogo->fields['file_name'];
    }

    public function getHeaderLogoHeight()
    {
        $dbCommon = new common();
        $rsLogo =  $dbCommon->getHeaderLogo();

        if($this->_externalStorage) {
            $pathLogoImage = $this->_externalStoragePath . '/logos/' . $rsLogo->fields['file_name'] ;
        } else {
            $pathLogoImage = $this->helpdezkPath . '/app/uploads/logos/' . $rsLogo->fields['file_name'] ;
        }

        if(empty($rsLogo->fields['height']) or !file_exists($pathLogoImage))
            return '35';
        else
            return $rsLogo->fields['height'];

    }

    public function getHeaderLogoWidth()
    {
        $dbCommon = new common();
        $rsLogo =  $dbCommon->getHeaderLogo();

        if($this->_externalStorage) {
            $pathLogoImage = $this->_externalStoragePath . '/logos/' . $rsLogo->fields['file_name'] ;
        } else {
            $pathLogoImage = $this->helpdezkPath . '/app/uploads/logos/' . $rsLogo->fields['file_name'] ;
        }

        if(empty($rsLogo->fields['width']) or !file_exists($pathLogoImage))
            return '97';
        else
            return $rsLogo->fields['width'];

    }

    /**
     * Returns login logo's Image Path
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     * @since 1.1.6 First time this was introduced.
     *
     * @return Login logo's Image Path
     *
     */
    public function getLoginLogoImage()
    {
        $dbCommon = new common();
        $rsLogo =  $dbCommon->getLoginLogo();

        if($this->_externalStorage) {
            $pathLogoImage = $this->_externalStoragePath . '/logos/' . $rsLogo->fields['file_name'] ;
        } else {
            $pathLogoImage = $this->helpdezkPath . '/app/uploads/logos/' . $rsLogo->fields['file_name'] ;
        }

        if(empty($rsLogo->fields['file_name']) or !file_exists($pathLogoImage))
            return 'default/login.png';
        else
            return $rsLogo->fields['file_name'];
    }

    public function getLoginLogoHeight()
    {
        $dbCommon = new common();
        $rsLogo =  $dbCommon->getLoginLogo();

        if($this->_externalStorage) {
            $pathLogoImage = $this->_externalStoragePath . '/logos/' . $rsLogo->fields['file_name'] ;
        } else {
            $pathLogoImage = $this->helpdezkPath . '/app/uploads/logos/' . $rsLogo->fields['file_name'] ;
        }

        if(empty($rsLogo->fields['height']) or !file_exists($pathLogoImage))
            return '70';
        else
            return $rsLogo->fields['height'];
    }

    public function getLoginLogoWidth()
    {
        $dbCommon = new common();
        $rsLogo =  $dbCommon->getLoginLogo();

        if($this->_externalStorage) {
            $pathLogoImage = $this->_externalStoragePath . '/logos/' . $rsLogo->fields['file_name'] ;
        } else {
            $pathLogoImage = $this->helpdezkPath . '/app/uploads/logos/' . $rsLogo->fields['file_name'] ;
        }

        if(empty($rsLogo->fields['width']) or !file_exists($pathLogoImage))
            return '154';
        else
            return $rsLogo->fields['width'];

    }

    public function getReportsLogoImage()
    {
        $dbCommon = new common();
        $rsLogo =  $dbCommon->getReportsLogo();
        if(empty($rsLogo->fields['file_name'])  or !file_exists($this->helpdezkPath.'/app/uploads/logos/'. $rsLogo->fields['file_name'] ))
            return 'default/reports.png';
        else
            return $rsLogo->fields['file_name'];

    }

    public function getReportsLogoHeight()
    {
        $dbCommon = new common();
        $rsLogo =  $dbCommon->getReportsLogo();
        if(empty($rsLogo->fields['height']))
            return '40';
        else
            return $rsLogo->fields['height'];

    }

    public function getReportsLogoWidth()
    {
        $dbCommon = new common();
        $rsLogo =  $dbCommon->getReportsLogo();
        if(empty($rsLogo->fields['width']))
            return '110';
        else
            return $rsLogo->fields['width'];

    }

    /**
     * Returns the modules that the user has access to
     *
     * @param string    $idPerson   Person Id
     * @return array    Modules array
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function getArrayModulesByPerson($idPerson)
    {
        $smarty = $this->retornaSmarty();

        $this->loadModel('admin/index_model');
        $dbIndex = new index_model();

        $rsModules = $this->getPersonModules($idPerson);
        $aModules = array();
        while (!$rsModules->EOF) {
            $prefix = $rsModules->fields['tableprefix'];
            $data = $dbIndex->getConfigDataByModule($prefix);

            if (!$data) {
                $this->logIt('Modules do not have config tables: ' . $prefix.'_tbconfig'. ' and ' . $prefix.'_tbconfigcategory - program: '. $this->program ,3,'general',__LINE__);
            }else{
                $aModules[] = array('idmodule' => $rsModules->fields['idmodule'],
                    'path' => $rsModules->fields['path'],
                    'class' => $rsModules->fields['class'],
                    'headerlogo' => $rsModules->fields['headerlogo'],
                    'reportslogo' => $rsModules->fields['reportslogo'],
                    //'varsmarty' => $smarty->getConfigVars($rsModules->fields['smarty']));
                    'varsmarty' => $smarty->getConfigVars($rsModules->fields['smarty']));
            }

            $rsModules->MoveNext();

        }

        return $aModules;

    }

    /**
     * Returns the sql sintax, according JQgrid types
     *
     * @param string    $oper    Name of the PqGrid operation
     * @param string    $column  Field to search
     * @param string    $search  Column to search
     * @return boolean|string    False is not exists ou file extention
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function getJqGridOperation($oper,$column, $search)
    {
        switch($oper) {
            case 'eq' : // equal
                $ret = "pipeLatinToUtf8(".$column.")" . ' = ' . "pipeLatinToUtf8('" . $search . "')";
                break;
            case 'ne': // not equal
                $ret = "pipeLatinToUtf8(".$column.")" . ' != ' . "pipeLatinToUtf8('" . $search . "')";
                break;
            case 'lt': // less
                $ret = $column . ' < ' . $search;
                break;
            case 'le': // less or equal
                $ret = $column . ' <= ' . $search;
                break;
            case 'gt': // greater
                $ret = $column . ' > ' . $search;
                break;
            case 'ge': // greater or equal
                $ret = $column . ' >= ' . $search;
                break;
            case 'bw': // begins with
                $search = str_replace("_", "\_", $search);
                $ret = "pipeLatinToUtf8(".$column.")" . ' LIKE ' . "pipeLatinToUtf8('" . $search . '%' . "')";
                break;
            case 'bn': //does not begin with
                $ret = "pipeLatinToUtf8(".$column.")" . ' NOT LIKE ' . "pipeLatinToUtf8('" . $search . '%' . "')";
            case 'in': // is in
                $ret = "pipeLatinToUtf8(".$column.")" . ' IN ('. "pipeLatinToUtf8('" . $search . "')" . ')';
                break;
            case 'ni': // is not in
                $ret = "pipeLatinToUtf8(".$column.")" . ' NOT IN ('. "pipeLatinToUtf8('" . $search . "')" . ')';
                break;
            case 'ew': // ends with
                $search = str_replace("_", "\_", $search);
                $ret = "pipeLatinToUtf8(".$column.")" . ' LIKE ' . "pipeLatinToUtf8('" . '%' . rtrim($search) . "')";
                break;
            case 'en': // does not end with
                $search = str_replace("_", "\_", $search);
                $ret = "pipeLatinToUtf8(".$column.")" . ' NOT LIKE ' . "pipeLatinToUtf8('" . '%' .  rtrim($search) . "')";
                break;
            case 'cn': // contains
                $search = str_replace("_", "\_", $search);
                $ret = "pipeLatinToUtf8(".$column.")" . ' LIKE ' .  "pipeLatinToUtf8('" . '%' . $search .'%' . "')" ;
                break;
            case 'nc': // does not contain
                $search = str_replace("_", "\_", $search);
                $ret = "pipeLatinToUtf8(".$column.")" . ' NOT LIKE ' .  "pipeLatinToUtf8('" .'%' . $search . '%' .  "')" ;
                break;
            case 'nu': //is null
                $ret = $column . ' IS NULL';
                break;
            case 'nn': // is not null
                $ret = $column . ' IS NOT NULL';
                break;
            default:
                die('Operator invalid in grid search !!!' . " File: " . __FILE__ . " Line: " . __LINE__ );
                break;
        }

        return $ret;
    }

    /**
     * Returns the image file format( Only allowed formats: GIF, PNG, JPEG ans BMP)
     *
     * Used for some cases where you can upload various formats and at the time of showing,
     * we do not know what format it is in. The method tests if the file exists and verifies
     * that the format is compatible
     *
     * @param string    $file    Image file
     * @return boolean|string    False is not exists ou file extention
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function getImageFileFormat($file)
    {
        if ($this->_externalStorage) {
            $target = $this->_externalStoragePath . $file.'.*';
        } else{
            $target = $this->getHelpdezkPath() . $file.'.*';
        }

        //$arrImages = glob($this->getHelpdezkPath().$file.'.*');

        $arrImages = glob($target);

        if(empty($arrImages))
            return false ;

        foreach ($arrImages as &$imgFile) {
            if(in_array( exif_imagetype($imgFile) , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))) {
                switch (exif_imagetype($imgFile)) {
                    case 1:
                        $ext = 'gif';
                        break;
                    case 2:
                        $ext = 'jpg';
                        break;
                    case 3:
                        $ext = 'png';
                        break;
                    case 6:
                        $ext = 'bmp';
                }
                return $ext;
            }
        }
        return false;
    }

    /**
     * Returns the i18n code for use in JS or anything else
     *
     * Used because some JS has includes with lowercase and others with uppercase
     *
     * @param string     $use       Script name
     * @return string|boolen        Name of include or false
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function getFileI18n($use)
    {
        $i18n = $this->getConfig('lang');

        switch($use){
            case 'jqgrid':
                if($i18n == 'pt_BR') {
                    $file = 'grid.locale-pt-br.js';
                } elseif( $i18n == 'en_US') {
                    $file = 'grid.locale-en.js';
                } else {
                    $file = false;
                }
            break;
            default:
                 $file = false;
            break;
        }

        return $file ;
    }

    public function getPersonById($idPerson)
    {
        $this->loadModel('admin/person_model');
        $dbPerson  = new person_model();
        $rsPerson = $dbPerson->selectPerson('AND tbp.idperson='.$idPerson);
        return $rsPerson;
    }

    /**
     * Returns Config External APIs Data
     *
     * @param int       $idPerson    Person Id
     * @return resource Recordset
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function getConfigExternalById($idPerson)
    {
        $this->loadModel('admin/userconfig_model');
        $dbUserConfig = new userconfig_model();

        $aRet = $dbUserConfig->getExternalSettings($idPerson);
        return $aRet['id'];
    }

    /**
     * Returns the number of new warnings (don´t read)
     *
     * @param int       $idPerson    Person Id
     * @param int       $idTypePerson
     * @return int      Number of not read warnings
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function getNumNewEwarnings($idPerson)
    {

        $this->loadModel('admin/warning_model');
        $dbWarning = new warning_model();
        $database = $this->getConfig('db_connect');

        if ($this->isMysql($database)) {
            $and = "AND (a.dtend > NOW() OR a.dtend = '0000-00-00 00:00:00') AND (a.idmessage NOT IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idPerson))" ;
        } elseif ($database == 'oci8po') {
            $and = "AND (a.dtend > SYSDATE OR a.dtend = '') AND (a.idmessage NOT IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idPerson))" ;
        }

        $rsWarning = $dbWarning->selectWarning($and);

        if ($this->isMysql($database)) {
            $total = $rsWarning->RecordCount();
        } elseif ($database == 'oci8po') {
            $total = $rsWarning->fields['rnum'];
            if(!$total) $total = 0;
        }

        while (!$rsWarning->EOF) {
            if($_SESSION['SES_COD_TIPO'] == $this->getIdTypePerson($idPerson)){
                if($rsWarning->fields['total_company'] > 0){
                    $checkCompany = $dbCommom->checkCompany($rsWarning->fields['idtopic'], $idcompany);
                    if($checkCompany->fields['check'] == 0){
                        $total--;
                        $rsWarning->MoveNext();
                        continue;
                    }
                }
            }else{
                // by group
                if($rsWarning->fields['total_group'] > 0){
                    $checkGroup = $dbCommom->checkGroup($rsWarning->fields['idtopic'], $_SESSION['SES_PERSON_GROUPS']);
                    if($checkGroup->fields['check'] == 0){
                        $total--;
                        $rsWarning->MoveNext();
                        continue;
                    }
                }
            }
            $rsWarning->MoveNext();
        }

        return $total;
    }

    // Since October 28, 2017
    public function getPersonModules($idperson)
    {

        $dbCommon = new common();
        return $dbCommon->getExtraModulesPerson($idperson,$this->getIdTypePerson($idperson));
    }

    public function getIdTypePerson($idperson)
    {
        $dbCommon = new common();
        return $dbCommon->getIdTypePerson($idperson);
    }

    // Since October 28, 2017
    public function getHelpdezkUrl()
    {

        $hdkUrl = $this->getConfig('hdk_url');
        if(substr($hdkUrl, 0,1) == '/')
            $hdkUrl = substr($hdkUrl,0,-1);
        return $hdkUrl;
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
        // if in localhost document root is D:/xampp/htdocs
        $document_root=$_SERVER['DOCUMENT_ROOT'];
        if(substr($document_root, -1)!='/'){
            $document_root=$document_root.'/';
        }
        return  realpath($document_root.$path) ;
    }

    public function getPersonName($idperson)
    {
        $dbCommon = new common();
        return $dbCommon->getPersonName($idperson);
    }

    // Since October 25, 2017
    public function returnPhpMailer()
    {

        $phpMailerDir = $this->getHelpdezkPath() . '/includes/classes/phpMailer/class.phpmailer.php';

        if (!file_exists($phpMailerDir)) {
            die ('ERROR: ' .$phpMailerDir . ' , does not exist  !!!!') ;
        }

        require_once($phpMailerDir);

        $mail = new phpmailer();

        return $mail;
    }

    // Since October 27, 2017
    public function returnProtectSql()
    {

        $phpProtectSql = $this->getHelpdezkPath() . '/includes/classes/ProtectSql/ProtectSql.php';

        if (!file_exists($phpProtectSql)) {
            die ('ERROR: ' .$phpProtectSql . ' , does not exist  !!!!') ;
        }

        require_once($phpProtectSql);
        $ProtectSql = new sqlinj;
        return $ProtectSql;
    }

    /**
     * Method to create random passwords
     *
     * @author Thiago Belem <contato@thiagobelem.net>
     *
     * @param integer $tamanho Size of the new password
     * @param boolean $maiusculas If it will have capital letters
     * @param boolean $numeros If it will have numbers
     * @param boolean $simbolos  If it will have symbols
     *
     * @return string A senha gerada
     */
    public function generateRandomPassword($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false)
    {
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';

        $caracteres .= $lmin;
        if ($maiusculas) $caracteres .= $lmai;
        if ($numeros) $caracteres .= $num;
        if ($simbolos) $caracteres .= $simb;

        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++)
        {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand-1];
        }
        return $retorno;
    }

    // Since October 19, 2017
    public function getLanguageWord($smartyConfig)
    {
        $smarty = $this->retornaSmarty();
        return  $smarty->getConfigVars($smartyConfig);
    }

    // Since October 16, 2017
    function isMysql($haystack)
    {
        return strpos($haystack, 'mysql') !== false;
    }

    // Since November 03, 2017
    public function getAdoDbVersion()
    {
        $adodb = $this->getConfig('adodb');
        if (empty($adodb))
            $adodb = 'adodb-5.20.9';
        return $adodb;
    }

    // Since November 03, 2017
    public function getJqueryVersion()
    {
        $jquery = $this->getConfig('jquery');
        if (empty($jquery))
            $jquery = 'jquery-2.1.1.js';
        $jqueryPath = $this->helpdezkPath.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR ;
        if (!file_exists($jqueryPath . $jquery))
            die('There is no Jquery file in: ' . $jqueryPath . $jquery);
        return $jquery;
    }

    // Since November 29, 2017
    public function getSummerNoteVersion()
    {
        $summer = $this->getConfig('summernote_version');
        if (empty($summer))
            $summer = '0.0.8';
        return $summer;
    }
    /*
    public function setSmartyVersionNumber()
    {
        $this->_smartyVersionNumber = 'smarty3.1.2';
    }
    */
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

    public function getLangVars($smarty)
    {

        if (version_compare($this->getSmartyVersionNumber(), '3', '>=' ))
            $langVars = $smarty->getConfigVars();
        else
            $langVars = $smarty->getConfigVars();

        return $langVars;
    }

    // Since October 16, 2017
    function getTheme()
    {
        // return 'parracho';
        return $this->getConfig('theme');
    }

    // Since May 28, 2017
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
    /*
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
    }*/

    function getlogDateHour()
    {
        $dateHour = $this->getConfig('log_date_format');
        if (empty($dateHour)){
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

	public function getConfig($param){
		return $this->_config[$param];
	}
	
	public function setConfig($type = null, $value = null) {

        if ((include './includes/config/config.php') == false) {
            die('The config file does not exist: ' . 'includes/config/config.php, line '.__LINE__ . '!!!');
        }

        if($type && $value){
        	$this->_config[$type] = $value;
        }else{
        	$this->_config = $config;
        }

    }

    private function setUrl() {
        $_GET['url'] = (isset($_GET['url']) ? $_GET['url'] : '/admin/');
        $this->_url = $_GET['url'];
        //die($this->_url) ;
        if ($_GET['url'] == 'admin/' || $_GET['url'] == '/admin/') {        	
			$path_default = $this->getConfig("path_default");
			if(substr($path_default, 0,1)!='/'){
			    $path_default='/'.$path_default;
			}
			if ($path_default == "/..") {   
				$path_default = "";
			}			
            header('Location:' . $path_default . '/admin/home');
        }
    }

    private function setExplode() {
        $this->_explode = explode('/', $this->_url);
    }

    private function setModule() {
        $this->_module = $this->_explode[0];
    }

    private function setController() {
        $this->_controller = $this->_explode[1];
    }

    private function setAction() {
        $ac = (!isset($this->_explode[2]) || $this->_explode[2] == NULL || $this->_explode[2] == "index" ? "index" : $this->_explode[2]);
        $this->_action = $ac;
    }

    private function setParams() {
        unset($this->_explode[0], $this->_explode[1], $this->_explode[2]);
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
    }

    // http://localhost/git/helpdezk/admin/login/getWarning/id/0 union select 1,2,password,name,login,6,7,8,9,10,11 from tbperson#
    public function getParam($name = NULL) {
        if ($name != NULL) {
            return $this->_protect($this->_params[$name]);
            //return $this->_params[$name];
        } else {
            return $this->_params;
        }
    }

    public function run() {
        $controller_path = CONTROLLERS . $this->_controller . 'Controller.php';

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

    public function retornaSmarty()
    {

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

        switch ($this->smartyVersion) {
            case 'smarty-old':
                $dirSmarty = $this->getHelpdezkPath().'/includes/classes/smarty/smarty-old/Smarty.class.php';
                break;
            case 'smarty-2.6.30':
                $dirSmarty = $this->getHelpdezkPath().'/includes/classes/smarty/smarty-2.6.30/libs/Smarty.class.php';
                break;
            case 'smarty-3.1.32':
                $dirSmarty = $this->getHelpdezkPath().'/includes/classes/smarty/smarty-3.1.32/libs/Smarty.class.php';
                $dirPluginDefault = $this->getHelpdezkPath().'/includes/classes/smarty/smarty-3.1.32/libs/plugins';
                break;
        }

        if (!file_exists($dirSmarty))
            die('Smarty Class doesn´t exists: ' . $dirSmarty . ' file: '. __FILE__);

        require_once($dirSmarty);

        $smarty = new Smarty;
        $smarty->debugging = false;
        $smarty->caching = false;
        $smarty->template_dir = VIEWS;
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

    public function returnPhpExcel()
    {
        require_once DOCUMENT_ROOT . path . '/includes/classes/PHPExcel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        return $objPHPExcel;
    }
    /*
    function SetAligns($a){
        //Configura o array dos alinhamentos de coluna
        $this->pdfAligns=$a;
    }
    function SetWidths($w){
        //Configura o array da largura das colunas
        $this->pdfWidths=$w;
    }
    */

    /* Start PDF Methods */
    public function returnFpdf()
    {
        require_once(FPDF . 'fpdf.php');
        $pdf = new FPDF;
        return $pdf;

    }

    // class FPDF with extention to parsehtml
    public function returnHtml2pdf() {
        require_once(FPDF . 'html2pdf.php');
        $pdf = new html2Pdf();
        return $pdf;

    }

    public function SetPdfFontFamily($pdfFontFamily)
    {
        $this->pdfFontFamily = $pdfFontFamily;
    }

    public function SetPdfFontStyle($pdfFontStyle)
    {
        $this->pdfFontStyle = $pdfFontStyle;
    }

    public function setPdfFontSyze($pdfFontSyze)
    {
        $this->pdfFontSyze = $pdfFontSyze;
    }

    function SetpdfLeftMargin($leftMargin){
        $this->pdfLeftMargin=$leftMargin;
    }

    function SetPdfLogo($logo){
        $this->pdfLogo=$logo;
    }

    public function makePdfLineBlur($objPdf, $text)
    {
        foreach($text as $k=>$v){
            $objPdf->SetFillColor(200,220,255);
            $objPdf->Cell($v['cellWidth'],$v['cellHeight'],$v['title'],0,0,$v['titleAlign'],1);
        }
        $objPdf->Ln(6);
    }

    public function makePdfLine($objPdf,$leftMargin, $width)
    {
        $objPdf->Ln(2);
        $objPdf->Cell($leftMargin);
        $objPdf->Line($objPdf->GetX(),$objPdf->GetY(), $width, $objPdf->GetY());
        $objPdf->Ln(2);
    }

    function SetPdfPage($page){
        $this->pdfPage=$page;
    }

    function SetPdfTitle($title){
        $this->pdfTitle=$title;
    }

    function SetPdfHeaderData($a_headerData){
        $this->a_pdfHeaderData=$a_headerData;
    }

    public function ReportPdfHeader($pdf){

        if(file_exists($this->pdfLogo)) {
            $pdf->Image($this->pdfLogo, 10 + $this->pdfLeftMargin, 8);
        }

        $pdf->Ln(2);

        $pdf->SetFont($this->pdfFontFamily,'B',10);
        $pdf->Cell($this->pdfLeftMargin);
        $pdf->Cell(0, 5, $this->pdfTitle, 0, 0, 'C');

        $pdf->SetFont($this->pdfFontFamily,'I',6);
        $pdf->Cell(0, 5, $this->pdfPage . ' ' . $pdf->PageNo() . '/{nb}', 0, 0, 'R');
        $pdf->Ln(7);
        $pdf->Cell($this->pdfLeftMargin);
        $pdf->Line($pdf->GetX(), $pdf->GetY(), 198, $pdf->GetY());

        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);
        $pdf->Cell($this->pdfLeftMargin);

        $pdf->Ln(8);
        return $pdf ;
    }

    public function ReportPdfCabec($pdf)
    {
        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);
        $pdf->SetFillColor(211,211,211);
        $pdf->Cell($this->pdfLeftMargin);

        for ($row = 0; $row < count($this->a_pdfHeaderData); $row++) {
            $pdf->Cell($this->a_pdfHeaderData[$row]['width'], 4, $this->a_pdfHeaderData[$row]['title'], 0, 0, $this->a_pdfHeaderData[$row]['align'], 1);
        }

        $pdf->Ln(5);
        return $pdf ;
    }

    public function ReportPdfRow($pdf,$data){
        //Calcula a altura da fila
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->ReportPdfNbLines($pdf,$this->a_pdfHeaderData[$i]['width'],$data[$i]));
        $h=5*$nb;
        //Insere um salto de página primeiramente se for necessario
        $this->ReportPdfCheckPageBreak($pdf,$h);
        //Desenha as células da linha
        for($i=0;$i<count($data);$i++){
            $w=$this->a_pdfHeaderData[$i]['width'];
            $a=isset($this->a_pdfHeaderData[$i]['align']) ? $this->a_pdfHeaderData[$i]['align'] : 'C';
            //Salva a posição atual
            $x=$pdf->GetX();
            $y=$pdf->GetY();
            //Draw the border
            $pdf->Rect($x,$y,$w,$h,'F');
            //Imprime o texto
            $pdf->MultiCell($w,5,$data[$i],0,$a);
            //Coloca a posição para a direita da célula
            $pdf->SetXY($x+$w,$y);
        }
        //Va para a próxima linha
        $pdf->Ln($h);
        return $pdf;
    }

    public function ReportPdfCheckPageBreak($pdf,$h){
        if($pdf->GetY()+$h>$pdf->PageBreakTrigger) {
            $pdf->AddPage($pdf->CurOrientation);
            $this->ReportPdfHeader($pdf);
            $this->ReportPdfCabec($pdf);
            $pdf->SetFillColor(255,255,255);
        }
    }

    public function ReportPdfNbLines($pdf,$w,$txt){

        $cw=&$pdf->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$pdf->rMargin-$pdf->x;
        $wmax=($w-2*$pdf->cMargin)*1000/$pdf->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb){
            $c=$s[$i];
            if($c=="\n"){
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax){
                if($sep==-1){
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    /* End PDF Methods */

    public function parse_ajax($arr) {
        $i = 0;
        $line = array();
        foreach ($arr as &$value) {
            $line[$i] = explode("\t", $value);
            $i++;
        }
        return $line;
    }

    public function access($smarty, $user, $idprogram, $type) {

        $bd = new common();
        $groupperm = $bd->selectGroupPermission($user, $idprogram );

        $perm = array();

        $perm = array();

        while (!$groupperm->EOF) {
            $program = $groupperm->fields['programname'];
            $perm[$program] = 'N' ;
            if ($perm[$program] != $groupperm->fields['allow'] ) {
                if ($perm[$program] == 'N') {
                    $perm[$program] = $groupperm->fields['allow'];
                }
            }

            $groupperm->MoveNext();
        }

        $personperm = $bd->selectPersonPermission($user, $idprogram);
		if ($personperm->fields['allow']) {
			while (!$personperm->EOF) {
				$program = $personperm->fields['programname'];
                if ($perm[$program] != $groupperm->fields['allow']) {
                    $perm[$program] = $groupperm->fields['allow'];
                }
				$allow = $personperm->fields['allow'];
				$perm[$program] = $allow;
				$personperm->MoveNext();
			}
		}

        $string_array = implode('|', $perm);
        $smarty->assign('string_array', $string_array);
        $smarty->assign('access', "string_array.split('|')");

        return $perm;
    }

	public function noAccess($access){
		if(count($access) > 0){
			$permAccess = array_values($access);
			if($permAccess[0] != "Y"){
				$smarty = $this->retornaSmarty();
                $dir = str_replace("\\","/", __DIR__) ;
                $path_tpl = str_replace("system","",$dir) ;
                $smarty->display('file:'.$path_tpl.'/app/modules/admin/views/nopermission.tpl.html');
				die();
			}
		}else{
			$smarty = $this->retornaSmarty();
			$smarty->display('nopermission.tpl.html');
			die();
		}
	}

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
	
    public function sendEmail($operation, $code_request, $reason = NULL) {

        $hdk_url = $this->getConfig('hdk_url');
        $smarty = $this->retornaSmarty();
        $bd = new emailconfig_model();
        if (!isset($operation)) {
            print("Email code not provided");
            return false;
        }
        $destinatario = "";
        //## ENVIA E-MAIL PARA O GRUPO AO REGISTRAR UMA SOLICITACAO ##===
        switch ($operation) {
            case "record":
				$COD_RECORD = $bd->getEmailIdBySession("NEW_REQUEST_OPERATOR_MAIL");				
                //$COD_RECORD = "16"; // Esse é o padrão

                $rsTemplate = $bd->getTemplateData($COD_RECORD);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                //           ---------------------------------------------------------------------

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $destinatario = $userEmail->Fields('email');
                }
                $assunto = $rsTemplate->fields['name'];
                eval("\$assunto = \"$assunto\";");
                

                break;

            case 'assume':
				$COD_ASSUME = $bd->getEmailIdBySession("NEW_ASSUMED_MAIL");
                //$COD_ASSUME = "1";
                $rsTemplate = $bd->getTemplateData($COD_ASSUME);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);
				
				$reqEmail = $bd->getRequesterEmail($code_request);
                $destinatario = $reqEmail->fields['email'];
				$typeuser = $reqEmail->fields['idtypeperson'];

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				if($typeuser == 2)
					$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				else
                	$LINK_USER = "<a href='".$hdk_url."helpdezk/operator#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
					
                $date = date('Y-m-d H:i');
                $ASSUME = $this->formatDate($date);

                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $assunto = $rsTemplate->Fields("name");
                eval("\$assunto = \"$assunto\";");
                //$destinatario = $rsMail->Fields("DES_EMAIL");

                break;

            case 'close':
				$COD_CLOSE = $bd->getEmailIdBySession("FINISH_MAIL");
                //$COD_CLOSE = "2";
                $rsTemplate = $bd->getTemplateData($COD_CLOSE);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

				$reqEmail = $bd->getRequesterEmail($code_request);
                $destinatario = $reqEmail->fields['email'];
				$typeuser = $reqEmail->fields['idtypeperson'];
				
                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $FINISH_DATE = $this->formatDate($date);
				
				$ev = new evaluation_model();
				$tk = $ev->getToken($code_request);
				$token = $tk->fields['token'];
				if($token)
					$LINK_EVALUATE =  $hdk_url."helpdezk/evaluate/index/token/".$token;
				
				
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				if($typeuser == 2)
					$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				else
                	$LINK_USER = "<a href='".$hdk_url."helpdezk/operator#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $assunto = $rsTemplate->Fields('name');
                eval("\$assunto = \"$assunto\";");
                //$destinatario = $rsMail->Fields("DES_EMAIL");

                break;

            case 'reject':
				$COD_REJECT = $bd->getEmailIdBySession("REJECTED_MAIL");
                //$COD_REJECT = "3";
                $rsTemplate = $bd->getTemplateData($COD_REJECT);				
				
                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);
				
				$reqEmail = $bd->getRequesterEmail($code_request);
				$destinatario = $reqEmail->fields['email'];
                
				$typeuser = $reqEmail->fields['idtypeperson'];
				
                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $REJECTION = $this->formatDate($date);
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				if($typeuser == 2)
					$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				else
                	$LINK_USER = "<a href='".$hdk_url."helpdezk/operator#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                $USER = $notes->fields["name"];
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;

                //require_once('../includes/solicitacao_detalhe.php');
                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->Fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->Fields("description"));
                eval("\$conteudo = \"$conteudo\";");


                $motivo = "<u>" . $l_eml["lb_motivo_rejeicao"] . "</u> " . $reason;
                $goto = ('usuario/solicita_detalhes.php?COD_SOLICITACAO=' . $COD_SOLICITACAO);
                $url = '<a href="' . $url_helpdesk . 'index.php?url=' . urlencode($goto) . '">' . $l_eml["link_solicitacao"] . '</a>';

                $assunto = $rsTemplate->Fields("name");
                eval("\$assunto = \"$assunto\";");
                //$destinatario = $rsMail->Fields("DES_EMAIL");

                break;

            case 'user_note' :
				$COD_ASSUME = $bd->getEmailIdBySession("USER_NEW_NOTE_MAIL");
                //$COD_ASSUME = "13";
                $rsTemplate = $bd->getTemplateData($COD_ASSUME);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);
				
				$reqEmail = $bd->getRequesterEmail($code_request);
                $destinatario = $reqEmail->fields['email'];
				$typeuser = $reqEmail->fields['idtypeperson'];
				
                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $FINISH_DATE = $this->formatDate($date);
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				if($typeuser == 2)
					$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				else
                	$LINK_USER = "<a href='".$hdk_url."helpdezk/operator#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                	if($notes->fields['idtype'] != 2){
	                    $table.= "<tr><td height=28><font size=2 face=arial>";
	                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
	                    $table.= "</font><br></td></tr>";
					}
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;


                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

             
                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");


                $assunto = $rsTemplate->Fields('name');
                eval("\$assunto = \"$assunto\";");
                //$destinatario = $rsMail->Fields("DES_EMAIL");
                
                if($_SESSION['SES_ATTACHMENT_OPERATOR_NOTE']){
                	if (path == "/..") {
	                    $file = DOCUMENT_ROOT . '/app/uploads/helpdezk/noteattachments/';
					} else {
	                    $file = DOCUMENT_ROOT . path .  '/app/uploads/helpdezk/noteattachments/';
	                }					
                	$attachment = $bdop->getNoteAttachment($code_request);
					if($attachment->fields['idnote_attachment'] && $attachment->fields['file_name']){
						$attachment_name = $attachment->fields['file_name'];
						$ext = strrchr($attachment_name, '.');
						$attachment_dest = $file.$attachment->fields['idnote_attachment'].$ext;						
					}					
                }

                break;

            case 'operator_note' :
				$COD_ASSUME = $bd->getEmailIdBySession("OPERATOR_NEW_NOTE");
                //$COD_ASSUME = "43";
                $rsTemplate = $bd->getTemplateData($COD_ASSUME);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $FINISH_DATE = $this->formatDate($date);
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
								
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");


                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];


                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {




                    $userEmail = $bd->getUserEmail($inchid);
                    $destinatario = $userEmail->fields['email'];
                }

                $assunto = $rsTemplate->Fields('name');
                eval("\$assunto = \"$assunto\";");
                //$destinatario = $rsMail->Fields("DES_EMAIL");

                break;

            case 'reopen':
				$COD_ASSUME = $bd->getEmailIdBySession("REQUEST_REOPENED");
               // $COD_ASSUME = "8";
                $rsTemplate = $bd->getTemplateData($COD_ASSUME);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $DATE = $this->formatDate($date);
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $destinatario = $userEmail->fields['email'];
                }

                $assunto = $rsTemplate->Fields('name');
                eval("\$assunto = \"$assunto\";");
                //$destinatario = $rsMail->Fields("DES_EMAIL");

                break;
                
			case "afterevaluate":
				$COD_RECORD = $bd->getEmailIdBySession("EM_EVALUATED");
                //$COD_RECORD = "4"; // Esse é o padrão

                $rsTemplate = $bd->getTemplateData($COD_RECORD);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $EVALUATION = $bdop->getEvaluationGiven($code_request);
                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                //           ---------------------------------------------------------------------

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $destinatario = $userEmail->Fields('email');
                }

                $assunto = $rsTemplate->fields['name'];
                eval("\$assunto = \"$assunto\";");

                break;
        
			case "repass":
				$COD_RECORD = $bd->getEmailIdBySession("REPASS_REQUEST_OPERATOR_MAIL");
                //$COD_RECORD = "82"; // Esse é o padrão

                $rsTemplate = $bd->getTemplateData($COD_RECORD);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                //           ---------------------------------------------------------------------

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $destinatario = $userEmail->Fields('email');
                }

                $assunto = $rsTemplate->fields['name'];
                eval("\$assunto = \"$assunto\";");
                

			break;

            case "approve":
				$COD_RECORD = $bd->getEmailIdBySession("SES_REQUEST_APPROVE");
                //$COD_RECORD = "83"; // Esse é o padrão

                $rsTemplate = $bd->getTemplateData($COD_RECORD);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";

                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                //           ---------------------------------------------------------------------

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $destinatario = $userEmail->Fields('email');
                }

                $assunto = $rsTemplate->fields['name'];
                eval("\$assunto = \"$assunto\";");
                

                break;
		
			case "operator_reject":
				$COD_REJECT = $bd->getEmailIdBySession("SES_MAIL_OPERATOR_REJECT");
				//$COD_REJECT = "84";
                $rsTemplate = $bd->getTemplateData($COD_REJECT);
			
                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);
				
				$grpEmails = $bd->getEmailsfromGroupOperators($_SESSION['SES_MAIL_OPERATOR_REJECT_ID']);
                while (!$grpEmails->EOF) {
                    if (!$destinatario) {
                        $destinatario = $grpEmails->Fields('email');
                    } else {
                        $destinatario .= ";" . $grpEmails->Fields('email');
                    }
                    $grpEmails->MoveNext();
                }
                
				$typeuser = $reqEmail->fields['idtypeperson'];
				
                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $REJECTION = $this->formatDate($date);
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				if($typeuser == 2)
					$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				else
                	$LINK_USER = "<a href='".$hdk_url."helpdezk/operator#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                $USER = $notes->fields["name"];
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->Fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->Fields("description"));
                eval("\$conteudo = \"$conteudo\";");

                $motivo = "<u>" . $l_eml["lb_motivo_rejeicao"] . "</u> " . $reason;

                $assunto = $rsTemplate->Fields("name");
                eval("\$assunto = \"$assunto\";");
				
			break;
	
		}


        $bd = new features_model();
        $emconfigs = $bd->getEmailConfigs();
        $tempconfs = $bd->getTempEmail();

        $nom_titulo = $emconfigs['EM_TITLE'];
        $mail_metodo = 'smtp';
        $mail_host = $emconfigs['EM_HOSTNAME'];
        $mail_dominio = $emconfigs['EM_DOMAIN'];
        $mail_auth = $emconfigs['EM_AUTH'];
        $mail_username = $emconfigs['EM_USER'];
        $mail_password = $emconfigs['EM_PASSWORD'];
        $mail_remetente = $emconfigs['EM_SENDER'];
        $mail_cabecalho = $tempconfs['EM_HEADER'];
        $mail_rodape = $tempconfs['EM_FOOTER'];        
        $mail_port  = $emconfigs['EM_PORT'];

        // print("HOST: $mail_host DOMAIN: $mail_dominio AUTH: $mail_auth USER: $mail_username PASS: $mail_password SENDER: $mail_remetente CABEÇ: $mail_cabecalho RODA: $mail_rodape <BR/>");


        //require_once("includes/classes/phpMailer/class.phpmailer.php");
        //$mail = new phpmailer();
        $mail = $this->returnPhpMailer();
        $mail->From = $mail_remetente;
        $mail->FromName = $nom_titulo;
        if ($mail_host)
            $mail->Host = $mail_host;
        if (isset($mail_port) AND !empty($mail_port)) {
            $mail->Port = $mail_port;
        }

        $mail->Mailer = $mail_metodo;
        $mail->SMTPAuth = $mail_auth;
        if (strpos($mail_username,'gmail') !== false) {
            $mail->SMTPSecure = "tls";
        }
        $mail->Username = $mail_username;
        $mail->Password = $mail_password;
        $mail->Body = $mail_cabecalho . $conteudo . $mail_rodape;
        $mail->AltBody = "HTML";
        $mail->Subject = utf8_decode($assunto);
        
		if($attachment_dest && $attachment_name){
			$mail->AddAttachment($attachment_dest, $attachment_name);
			$mail->SetFrom($mail_remetente, $nom_titulo);
		}


        //Checks for more than 1 email address at recipient
        $jaExiste = array();
        if (preg_match("/;/", $destinatario)) {
            $email_destino = explode(";", $destinatario);
            if (is_array($email_destino)) {
                for ($i = 0; $i < count($email_destino); $i++) {
                    // Se o endereço de e-mail NÃO estiver no array, envia e-mail e coloca no array
                    // Se já tiver no array, não envia novamente, evitando mails duplicados
                    if (!in_array($email_destino[$i], $jaExiste)) {
                        $mail->AddAddress($email_destino[$i]);
                        $jaExiste[] = $email_destino[$i];
                    }
                }

            } else {

                $mail->AddAddress($email_destino);
            }
        } else {
            $mail->AddAddress($destinatario);
        }

        $mail->SetLanguage('br', DOCUMENT_ROOT . "email/language/");
        $mail->AddAddress('rogerio.albandes@marioquintana.com.br');
        $done = $mail->Send();
        

        if (!$done) {
            if ($_SESSION['EM_FAILURE_LOG'] == '1') {
                $mail->SMTPDebug = true;
                $mail->Send();

                $this->logit("[".date($this->getPrintDate())."]" . " Line: " .  __LINE__ . " - Error send email, request " . $REQUEST .', operation: ' . $operation , $this->logFileEmail);
                $this->logit("[".date($this->getPrintDate())."]" . " Error Info: " . $mail->ErrorInfo , $this->logFileEmail);
            }
        } else {
            if ($_SESSION['EM_SUCCESS_LOG'] == '1') {
                $this->logit("[".date($this->getPrintDate())."]" . " Line: " .  __LINE__ . " - Email Succesfully Sent, request " . $REQUEST .', operation: ' . $operation , $this->logFileEmail);
            }
        }


    }

    // Since November 03, 2017
    public function sessionValidate($mob = null) {
        if (!isset($_SESSION['SES_COD_USUARIO'])) {
            if($mob){
                echo 1;
            }else{
                $this->sessionDestroy();
                header('Location:' . $this->helpdezkUrl . '/admin/login');
            }
        }
    }

    // Since November 03, 2017
    public function sessionDestroy()
    {
        session_start();
        session_unset();
        session_destroy();
    }

    public function validasessao($mob = null) {
        if (!isset($_SESSION['SES_COD_USUARIO'])) {
        	if($mob){
        		echo 1;
			}else{
        		echo '<META HTTP-EQUIV="Refresh" Content="0; URL='.path . '/admin/login">';	
        	}
        }
    }

    public function _sanitize()
    {
        if (isset($headers['X-CSRF-TOKEN'])) {
            if ($headers['X-CSRF-TOKEN'] !== $_SESSION['X-CSRF-TOKEN']) {
                return (json_encode(['error' => 'Wrong CSRF token.']));
            }
        } else {
            return (json_encode(['error' => 'No CSRF token.']));
        }

    }

    public function found_rows(){
        $dbCommon = new common();
        $ret = $dbCommon->foundRows();
		return $ret;
    }

    public function BrasilianCurrencyToMysql($get_valor)
    {
        $source = array('.', ',');
        $replace = array('', '.');
        $valor = str_replace($source, $replace, $get_valor);
        return $valor;
    }

    public function getModuleParam($module,$param) {
        $dbCommon = new common() ;
        return $dbCommon->getValueParam($module,$param) ;
    }

    public function getActiveModules()
    {
        $dbCommon = new common() ;
        return $dbCommon->getActiveModules();

    }
    // Encrypt Function
    public function mc_encrypt($encrypt, $key){
        $encrypt = serialize($encrypt);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
        $key = pack('H*', $key);
        $mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
        $passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
        $encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
        return $encoded;
    }

    // Decrypt Function
    public function mc_decrypt($decrypt, $key){
        $decrypt = explode('|', $decrypt.'|');
        $decoded = base64_decode($decrypt[0]);
        $iv = base64_decode($decrypt[1]);
        if(strlen($iv)!==mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){ return false; }
        $key = pack('H*', $key);
        $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
        $mac = substr($decrypted, -64);
        $decrypted = substr($decrypted, 0, -64);
        $calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
        if($calcmac!==$mac){ return false; }
        $decrypted = unserialize($decrypted);
        return $decrypted;
    }

    public function makeMenuTreeView($idPerson, $idmodule='')
    {

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $dbCommon = new common();

        $programcount = $dbCommon->countPrograms($idmodule);

        $andModule = 'm.idmodule = ' . $idmodule ;

        $groupperm = $dbCommon->getPermissionMenu($idPerson, $andModule) ;

        if($groupperm){
            while (!$groupperm->EOF) {
                $allow = $groupperm->fields['allow'];
                $program = $groupperm->fields['program'];
                $idmodule_pai = $groupperm->fields['idmodule_pai'];
                $module = $groupperm->fields['module'];
                $idmodule_origem = $groupperm->fields['idmodule_origem'];
                $category = $groupperm->fields['category'];
                $category_pai = $groupperm->fields['category_pai'];
                $idcategory_origem = $groupperm->fields['idcategory_origem'];
                $controller = $groupperm->fields['controller'];
                $idprogram = $groupperm->fields['idprogram'];
                $prsmarty = $groupperm->fields['pr_smarty'];
                $ctsmarty = $groupperm->fields['cat_smarty'];
                $perm[$idprogram] = array('program' => $program, 'smartypr' => $prsmarty, 'smartyct' => $ctsmarty, 'idmodule_pai' => $idmodule_pai, 'module' => $module, 'idmodule_origem' => $idmodule_origem, 'category' => $category, 'category_pai' => $category_pai, 'idcategory_origem' => $idcategory_origem, 'controller' => $controller, 'idprogram' => $idprogram, 'allow' => $allow);
                $groupperm->MoveNext();
            }
        }

        for ($j = 1; $j <= $programcount; $j++) {
            if (in_array($perm[$j]['idmodule_pai'], $modules) || $perm[$j]['allow'] != 'Y') {

            } else {
                $modules[$perm[$j]['idmodule_pai']] = array('idmodule' => $perm[$j]['idmodule_pai'], 'module' => $perm[$j]['module']);
            }

            //agrupa as categorias tirando as duplicadas
            if (in_array($perm[$j]['category_pai'], $categories) || $perm[$j]['allow'] != 'Y') {

            } else {
                $categories[$perm[$j]['category_pai']] = array('idmodule_origem' => $perm[$j]['idmodule_origem'], 'category' => $perm[$j]['category'], 'idcategory' => $perm[$j]['category_pai'], 'smarty' => $perm[$j]['smartyct']);
            }

            //agrupa os programas separando os duplicados
            if (in_array($perm[$j]['idprogram'], $programs) || $perm[$j]['allow'] != 'Y') {

            } else {
                $programs[$perm[$j]['idprogram']] = array('idprogram' => $perm[$j]['idprogram'],'idcategory_origem' => $perm[$j]['idcategory_origem'], 'program' => $perm[$j]['program'], 'controller' => $perm[$j]['controller'], 'smarty' => $perm[$j]['smartypr']);
            }
        }

        $countmodules    = $dbCommon->countModules();
        $countcategories = $dbCommon->countCategories();

        $lista = "<ul id='menu' class='filetree'>";
        for ($i = 0; $i < $countmodules; $i++) {
            if($modules[$i + 1]['module']){
                $lista.="<li><span>" . $modules[$i + 1]['module'] . "</span>";
                $lista.="<ul>";
                for ($j = 0; $j <= $countcategories; $j++) {
                    if ($modules[$i + 1]['idmodule'] == ($categories[$j + 1]['idmodule_origem'])) {
                        $cat = $categories[$j + 1]['smarty'];
                        $lista.="<li><span>" . $langVars[$cat] . "</span>";
                        $lista.="<ul>";
                        for ($k = 0; $k <= $programcount; $k++) {
                            if ($categories[$j + 1]['idcategory'] == ($programs[$k + 1]['idcategory_origem'])) {
                                $pr = $programs[$k + 1]['smarty'];
                                $checkbar = substr($programs[$k + 1]['controller'], -1);
                                if($checkbar != "/") $checkbar = "/";
                                else $checkbar = "";
                                $lista.="<li><span><a href='#/" . $programs[$k + 1]['controller'] . "' class='loadMenu' rel='" . $programs[$k + 1]['controller'] . $checkbar."' >" . $langVars[$pr] . "</a></span></li>";
                            }
                        }
                        $lista.="</ul></li>";
                    }
                }
                $lista.="</ul></li>";
            }
        }
        $lista.="</ul>";

        return $lista;

    }

    // Since 1.0.1
    public function makeMenuBycategory($idPerson,$idmodule,$idcategory)
    {
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $dbCommon = new common();

        $andModule = " m.idmodule = " . $idmodule . " AND cat.idmodule = " . $idcategory ;

        $groupperm = $dbCommon->getPermissionMenu($idPerson, $andModule) ;

        $list='';
        if($groupperm){
            while (!$groupperm->EOF) {
                $allow = $groupperm->fields['allow'];
                $path  = $groupperm->fields['path'];
                $program = $groupperm->fields['program'];
                $controller = $groupperm->fields['controller'];
                $prsmarty = $groupperm->fields['pr_smarty'];

                if ($allow == 'Y') {
                    $checkbar = substr($groupperm->fields['controller'], -1);
                    if($checkbar != "/") $checkbar = "/";
                    else $checkbar = "";
                    $list .="<li><a href='" . $this->helpdezkUrl . "/".$path."/" . $controller . $checkbar."index' >" . $langVars[$prsmarty] . "</a></li>";
                }

                $groupperm->MoveNext();
            }
        }

        return $list;

    }


    // -- Combos ---

    public function comboCountries()
    {
        $dbCommon = new common();

        $rs = $dbCommon->getCountry();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idcountry'];
            $values[]   = $rs->fields['printablename'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function getIdCountryDefault()
    {
        return $_SESSION['COUNTRY_DEFAULT'];
    }

    public function getIdStateDefault()
    {
        return $_SESSION['hdk']['STATE_DEFAULT'] ;
    }

    public function getIdCityDefault($idState)
    {
        // get the first city in table
        $dbCommon = new common();
        $rs = $dbCommon->getCity(" where idstate = $idState"," ORDER BY name ASC", "LIMIT 1");
        return $rs->fields['idcity'];
    }

    public function comboStates($idCountry)
    {
        $dbCommon = new common();

        $rs = $dbCommon->getState("where idcountry = $idCountry");
        while (!$rs->EOF) {
            $name = $rs->fields['idstate'] != 1 ? $rs->fields['name'] : $this->getLanguageWord('Select_state');
            $fieldsID[] = $rs->fields['idstate'];
            $values[]   = $name;
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function comboCity($idState)
    {
        $dbCommon = new common();
//die('id'.$idState);
        $rs = $dbCommon->getCity("where idstate = $idState");
        while (!$rs->EOF) {
            $name = $rs->fields['idcity'] != 1 ? $rs->fields['name'] : $this->getLanguageWord('Select_city');
            $fieldsID[] = $rs->fields['idcity'];
            $values[]   = utf8_encode($name);
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function comboNeighborhood($idCity)
    {
        $dbCommon = new common();

        $rs = $dbCommon->getNeighborhood("where idcity = $idCity");
        while (!$rs->EOF) {
            $name = $rs->fields['idneighborhood'] != 1 ? $rs->fields['name'] : $this->getLanguageWord('Select_city');
            $fieldsID[] = $rs->fields['idneighborhood'];
            $values[]   = $name;
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function comboStatesHtml($idCountry)
    {

        $arrType = $this->comboStates($idCountry);
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            if ($arrType['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrType['values'][$indexKey]."</option>";
        }
        return $select;
    }

    public function comboCitesHtml($idState)
    {

        $arrType = $this->comboCity($idState);
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            if ($arrType['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrType['values'][$indexKey]."</option>";
        }
        return $select;
    }

    public function comboNeighborhoodHtml($idCity)
    {

        $arrType = $this->comboneighborhood($idCity);
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            if ($arrType['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrType['values'][$indexKey]."</option>";
        }
        return $select;
    }

    public function comboTypeStreet()
    {
        $location = $this->getConfig('lang');
        $dbCommon = new common();

        $rs = $dbCommon->getTypeStreet("where location = '" . $this->getConfig('lang') ."'");
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idtypestreet'];
            $values[]   = utf8_encode($rs->fields['name']);
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }
    // --
    public function makeJsonUtf8Compat($aParam)
    {
        $array = array_map('htmlentities',$aParam);
        $json = html_entity_decode(json_encode($array));
        return $json;
    }

    public function loadModel($modelName)
    {
        $modelPath = 'app/modules/';

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

    public function getIdCategoryByName($name)
    {

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

    public function makeLogin($name)
    {
        $this->loadModel('admin/person');
        $dbPerson = new person_model();

        $aName = $arr = explode(' ', $this->clearAccent($name));
        $num = count($aName);
        $login = strtolower($aName[0].'.'.$aName[$num-1]);

        for ($x = 0; $x <= 100; $x++) {
            if($x == 0)
                $login = $login;
            else
                $test = $login.'_'.$x;
            if (!$dbPerson->isLogin($test)){
                return $test;
            }
        }

        return false;

    }

    public function clearAccent($string)
    {
         $LetraProibi = Array(",",".","'","\"","&","|","!","#","$","¨","*","(",")","`","´","<",">",";","=","+","§","{","}","[","]","^","~","?","%");
         $special =  Array('Á','È','ô','Ç','á','è','Ò','ç','Â','Ë','ò','â','ë','Ø','Ñ','À','Ð','ø','ñ','à','ð','Õ','Å','õ','Ý','å','Í','Ö','ý','Ã','í','ö','ã','Î','Ä','î','Ú','ä','Ì','ú','Æ','ì','Û','æ','Ï','û','ï','Ù','®','É','ù','©','é','Ó','Ü','Þ','Ê','ó','ü','þ','ê','Ô','ß','‘','’','‚','“','”','„');
         $clearspc = Array('A','A','o','Ç','a','e','o','c','A','E','o','a','e','o','N','A','D','o','n','a','o','O','A','o','y','a','I','O','y','A','i','o','a','I','A','i','U','a','I','u','A','i','U','a','I','u','i','U','','E','u','c','e','O','U','p','E','o','u','b','e','O','b','','','','','','');
         $newId = str_replace($special, $clearspc, $string);
         $newId = str_replace($LetraProibi, "", trim($newId));
         return strtolower($newId);
        }

    public function formatMask($string, $mask)
    {
        $aValid = array('0','9','X','#');
        $aToFormat = str_split($string);
        $aMask = str_split($mask);
        $outputLen = strlen($mask)-1;

        $j=0;
        $format = '';
        $tempChar = '';
        for($i=0; $i<=$outputLen; $i++){
            $tempChar = substr($mask,$i,1);
            if (in_array($tempChar,$aValid)){
                $format .= $aToFormat[$j];
                $j++;
            } else {
                $format .= $aMask[$i];
            }
        }
        return $format;

    }

    public function getIdNeighborhoodDefault($idCity)
    {
        // get the first city in table
        $dbCommon = new common();
        $rs = $dbCommon->getNeighborhood(" where idcity = $idCity"," ORDER BY name ASC", "LIMIT 1");
        return $rs->fields['idneighborhood'];
    }

    public function getIdProgramByController($programcontroller)
    {
        $dbCommon = new common();
        $id = $dbCommon->selectProgramIDByController($programcontroller) ;
        if(!$id) {
            die('Program don\'t exists in tbprogram !!!') ;
        } else {
            return $id ;
        }
    }

    public function getModuleInfo($idmodule)
    {
        $dbCommon = new common();
        $ret = $dbCommon->getModule("WHERE idmodule = $idmodule") ;
        if(!$ret) {
            die('Module don\'t exists in tbmodule !!!') ;
        } else {
            return $ret;
        }
    }

    public function makeMenuByModule($idPerson,$idmodule)
    {
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $dbCommon = new common();
        $rsCat = $dbCommon->getModulesCategoryAtive($idPerson, $idmodule) ;

        $list='';
        if($rsCat){
            while (!$rsCat->EOF) {

                $list .= "<li class='dropdown'>
                    <a aria-expanded='false' role='button' href='#' class='dropdown-toggle' data-toggle='dropdown'>". $smarty->getConfigVars($rsCat->fields['cat_smarty']) ."<span class='caret'></span></a>
                    <ul role='menu' class='dropdown-menu'>";

                $andModule = " m.idmodule = " . $idmodule . " AND cat.idprogramcategory = " . $rsCat->fields['category_id'] ;
                $groupperm = $dbCommon->getPermissionMenu($idPerson, $andModule);

                if($groupperm){
                    while (!$groupperm->EOF) {
                        $allow = $groupperm->fields['allow'];
                        $path  = $groupperm->fields['path'];
                        $program = $groupperm->fields['program'];
                        $controller = $groupperm->fields['controller'];
                        $prsmarty = $groupperm->fields['pr_smarty'];

                        if ($allow == 'Y') {
                            $checkbar = substr($groupperm->fields['controller'], -1);
                            if($checkbar != "/") $checkbar = "/";
                            else $checkbar = "";
                            $list .="<li><a href='" . $this->helpdezkUrl . "/".$path."/" . $controller . $checkbar."index' >" . $langVars[$prsmarty] . "</a></li>";
                        }

                        $groupperm->MoveNext();
                    }
                }

                $list .= "</ul>
                </li>";

                $rsCat->MoveNext();
            }
        }

        return $list;

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

    function makeMenuAdmin($smarty)
    {
        $this->loadModel('admin/programs_model');
        $dbProgram = new programs_model();

        $rs = $this->getActiveModules();
        $list = '';

        if($_SESSION['SES_COD_USUARIO'] == 1 || $_SESSION['SES_TYPE_PERSON'] == 1){
            $cond = " AND tp.idtypeperson = 1";
        }else{
            $cond = " AND tp.idtypeperson IN
                        (SELECT idtypeperson
                           FROM tbpersontypes
                          WHERE idperson = '".$_SESSION['SES_COD_USUARIO']."' )";
        }

        while (!$rs->EOF) {
            $rsCat = $dbProgram->getModulesCategoryAtive($_SESSION['SES_COD_USUARIO'],$rs->fields['idmodule'],$cond);
            if($rsCat->RecordCount() > 0){
                $list .= "<li class='dropdown-submenu'>
                            <a tabindex='-1' href='#'>". $smarty->getConfigVars($rs->fields['smarty']) ."</a>
                            <ul class='dropdown-menu'>";


                while (!$rsCat->EOF) {
                    $list .= "<li class='dropdown-submenu'>
                            <a tabindex='-1' href='#'>". $smarty->getConfigVars($rsCat->fields['cat_smarty']) ."</a>
                            <ul class='dropdown-menu'>";

                    $andModule = " m.idmodule = " . $rs->fields['idmodule'] . " AND cat.idprogramcategory = " . $rsCat->fields['category_id'] ;
                    $groupperm = $dbProgram->getPermissionMenu($_SESSION['SES_COD_USUARIO'], $andModule, $cond);

                    if($groupperm){
                        while (!$groupperm->EOF) {
                            $allow = $groupperm->fields['allow'];
                            $path  = $groupperm->fields['path'];
                            $program = $groupperm->fields['program'];
                            $controller = $groupperm->fields['controller'];
                            $prsmarty = $groupperm->fields['pr_smarty'];

                            $checkbar = substr($groupperm->fields['controller'], -1);
                            if($checkbar != "/") $checkbar = "/";
                            else $checkbar = "";

                            $controllertmp = ($checkbar != "") ? $controller : substr($controller,0,-1);
                            $controller_path = 'app/modules/' . $path . '/controllers/' . $controllertmp . 'Controller.php';

                            if (!file_exists($controller_path)) {
                                $this->logIt("The controller does not exist: " . $controller_path. ' - program: '. $this->program ,3,'general',__LINE__);
                            }else{
                                if ($allow == 'Y') {

                                    $list .="<li><a href='" . $this->helpdezkUrl . "/".$path."/" . $controller . $checkbar."index' >" . $smarty->getConfigVars($prsmarty) . "</a></li>";
                                }
                            }

                            $groupperm->MoveNext();
                        }
                    }
                    $list .= "</ul>
                </li>";
                    $rsCat->MoveNext();
                }

                $list .= "</ul>
                </li>";

            }

            $rs->MoveNext();
        }
        //echo $list;
        return $list;
    }

    public function makeNavAdmin($smarty)
    {
        $idPerson = $_SESSION['SES_COD_USUARIO'];
        $listRecords = $this->makeMenuAdmin($smarty);
        $moduleinfo = $this->getModuleInfo(1);

        $smarty->assign('displayMenu_Adm',1);
        $smarty->assign('listMenu_Adm',$listRecords);
        $smarty->assign('moduleLogo',$moduleinfo->fields['headerlogo']);
        $smarty->assign('modulePath',$moduleinfo->fields['path']);
    }

    function makeConfigExternalData($smarty)
    {
        $idPerson = $_SESSION['SES_COD_USUARIO'];
        $rsExternal = $this->getConfigExternalById($idPerson);
        while (!$rsExternal->EOF) {
            if ($rsExternal->fields['idexternalapp'] == 50 && $rsExternal->fields['fieldname'] == 'key' ) {
                $smarty->assign('trello_key',$rsExternal->fields['value']);
            } elseif ($rsExternal->fields['idexternalapp'] == 50 && $rsExternal->fields['fieldname'] == 'token' ){
                $smarty->assign('trello_token',$rsExternal->fields['value']);
            } elseif ($rsExternal->fields['idexternalapp'] == 51 && $rsExternal->fields['fieldname'] == 'key' ) {
                $smarty->assign('pushover_key',$rsExternal->fields['value']);
            } elseif ($rsExternal->fields['idexternalapp'] == 51 && $rsExternal->fields['fieldname'] == 'token' ){
                $smarty->assign('pushover_token',$rsExternal->fields['value']);
            }
            $rsExternal->MoveNext();
        }
    }

    function makePersonData($smarty)
    {
        $cod_usu = $_SESSION['SES_COD_USUARIO'];

        if ($this->_externalStorage) {
            $imgFormat = $this->getImageFileFormat('/photos/'.$cod_usu);
        } else{
            $imgFormat = $this->getImageFileFormat('/app/uploads/photos/'.$cod_usu);
        }

        if ($imgFormat)
            $imgPhoto = $cod_usu.'.'.$imgFormat;
         else
            $imgPhoto = 'default/no_photo.png';

        if ($this->_externalStorage) {
            $smarty->assign('person_photo_nav', $this->_externalStorageUrl.'/photos/'. $imgPhoto."?=".Date('U'));          // force refresh image
        } else{
            $smarty->assign('person_photo_nav', $this->getHelpdezkUrl().'/app/uploads/photos/'. $imgPhoto."?=".Date('U'));  //force refresh image
        }

        $rsPerson = $this->getPersonById($cod_usu);

        $address = $rsPerson->fields['street']. ' ' . $rsPerson->fields['number'];
        if (!empty($rsPerson->fields['complement']))
            $address .= ' /'.$rsPerson->fields['complement'];

        $smarty->assign('user_name', $rsPerson->fields['name']);
        $smarty->assign('user_department', $rsPerson->fields['department']);
        $smarty->assign('user_company', $rsPerson->fields['company']);
        $smarty->assign('user_city', $rsPerson->fields['city']);

        $smarty->assign('user_number', $rsPerson->fields['number']);
        $smarty->assign('user_street', $rsPerson->fields['street']);
        $smarty->assign('user_typestreet', $rsPerson->fields['typestreet']);
        $smarty->assign('user_complement', $rsPerson->fields['complement']);
        $smarty->assign('user_city', $rsPerson->fields['city']);
        $smarty->assign('user_state', $rsPerson->fields['state_abbr']);
        $zip = $this->formatMask($rsPerson->fields['zipcode'],$this->getConfig('zip_mask'));
        $smarty->assign('user_zip', $zip);
        $phone = $this->formatMask($rsPerson->fields['telephone'],$this->getConfig('phone_mask'));
        $smarty->assign('user_phone',$phone);
        $cellphone = $this->formatMask($rsPerson->fields['cellphone'],$this->getConfig('cellphone_mask'));
        $smarty->assign('user_cellphone',$cellphone);

        // Update user data - Screen
        $personType = $rsPerson->fields['idtypeperson'];
        $aScreenAccess = $this->getArrayScreenFields(2,$personType,'persondata_form');

        $smarty->assign('login',$_SESSION['SES_LOGIN_PERSON']);
        $smarty->assign('id_person',$_SESSION['SES_COD_USUARIO']);

        // --- Person Name ---
        $smarty->assign('person_name_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_name') ? '' : 'disabled') ) ;

        if (empty($rsPerson->fields['name']))
            $smarty->assign('placeholder_name',$this->getLanguageWord('Placeholder_name'));
        else
            $smarty->assign('person_name',$rsPerson->fields('name'));

        // --- SSN (USA) or CPF (Brazil) ---
        $smarty->assign('ssn_cpf_disabled',($this->getScreenFieldEnable($aScreenAccess,'ssn_cpf') ? '' : 'disabled') ) ;

        if (empty($rsPerson->fields['ssn_cpf']))
            $smarty->assign('placeholder_ssn_cpf', $this->getLanguageWord('Placeholder_cpf'));
        $ssnCpf = '';
        $smarty->assign('ssn_cpf', $rsPerson->fields['ssn_cpf']);

        // --- Gender ---
        $smarty->assign('person_gender_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_gender') ? '' : 'disabled') ) ;
        $arrGender = $this->comboGender();
        $smarty->assign('genderids',  $arrGender['ids']);
        $smarty->assign('gendervals', $arrGender['values']);
        $smarty->assign('idgender',   $rsPerson->fields['gender']);

        // --- Email ---
        $smarty->assign('person_email_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_email') ? '' : 'disabled') ) ;
        if (empty($rsPerson->fields['email']))
            $smarty->assign('placeholder_email',$this->getLanguageWord('Placeholder_email'));
        else
            $smarty->assign('person_email',$rsPerson->fields['email']);

        // --- Date Birthday ---
        $smarty->assign('person_dtbirth_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_dtbirth') ? '' : 'disabled') ) ;
        if (empty($rsPerson->fields['dtbirth']) or $rsPerson->fields['dtbirth'] == '0000-00-00')
            $smarty->assign('placeholder_dtbirth',$this->getConfig('date_placeholder'));
        else
            $smarty->assign('person_dtbirth',$this->formatDate($rsPerson->fields['dtbirth']));

        // --- Phone Number ---
        $smarty->assign('person_phone_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_phone') ? '' : 'disabled') ) ;
        if (empty($rsPerson->fields['telephone']))
            $smarty->assign('placeholder_phone',$this->getLanguageWord('Placeholder_phone'));
        else
            $smarty->assign('person_phone',$rsPerson->fields['telephone']);

        // --- Branch Number ---
        $smarty->assign('person_branch_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_branch') ? '' : 'disabled') ) ;
        if (empty($rsPerson->fields['branch_number']))
            $smarty->assign('person_branch','');
        else
            $smarty->assign('person_branch',$rsPerson->fields['branch_number']);

        // --- Cellphone Number ---
        $smarty->assign('person_cellphone_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_cellphone') ? '' : 'disabled') ) ;
        if (empty($rsPerson->fields['cellphone']))
            $smarty->assign('placeholder_cellphone',$this->getLanguageWord('Placeholder_cellphone'));
        else
            $smarty->assign('person_cellphone',$rsPerson->fields['cellphone']);

        // --- Country ---
        $smarty->assign('person_country_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_country') ? '' : 'disabled') ) ;
        if ($rsPerson->fields['idcountry'] <= 1)
            $idCountryEnable = $this->getIdCountryDefault();
        else
            $idCountryEnable = $rsPerson->fields['idcountry'];
        $arrCountry = $this->comboCountries();
        $smarty->assign('countryids',  $arrCountry['ids']);
        $smarty->assign('countryvals', $arrCountry['values']);
        $smarty->assign('idcountry', $idCountryEnable  );

        // --- State ---
        if ($rsPerson->fields['idstate'] <= 1)
            $idStateEnable = $this->getIdStateDefault();
        else
            $idStateEnable = $rsPerson->fields['idstate'];
        $arrCountry = $this->comboStates($idCountryEnable);
        $smarty->assign('stateids',  $arrCountry['ids']);
        $smarty->assign('statevals', $arrCountry['values']);
        $smarty->assign('idstate',   $idStateEnable);

        // --- City ---
        if ($rsPerson->fields['idcity'] <= 1)
            $idCityEnable = 1;
        else
            $idCityEnable = $rsPerson->fields['idcity'];

        $arrCity = $this->comboCity($idStateEnable);
        $smarty->assign('cityids',  $arrCity['ids']);
        $smarty->assign('cityvals', $arrCity['values']);
        $smarty->assign('idcity',   $idCityEnable);

        // --- Zipcode ---
        if (empty($rsPerson->fields['zipcode']))
            $smarty->assign('placeholder_zipcode',$this->getLanguageWord('Placeholder_zipcode'));
        else
            $smarty->assign('person_zipcode',$rsPerson->fields['zipcode']);

        // --- Neighborhood ---
        if ($rsPerson->fields['idneighborhood'] <= 1)
            $idNeighborhoodEnable = 1;
        else
            $idNeighborhoodEnable = $rsPerson->fields['idneighborhood'];
        $arrNeighborhood = $this->comboNeighborhood($idCityEnable);
        $smarty->assign('neighborhoodids',  $arrNeighborhood['ids']);
        $smarty->assign('neighborhoodvals', $arrNeighborhood['values']);
        $smarty->assign('idneighborhood',   $idNeighborhoodEnable);

        // --- Type Street ---
        if ($rsPerson->fields['idtypestreet'] == 'Choose')
            $idTypeStreetEnable = '';
        else
            $idTypeStreetEnable = $rsPerson->fields['idtypestreet'];
        $arrTypestreet = $this->comboTypeStreet();
        $smarty->assign('typestreetids',  $arrTypestreet['ids']);
        $smarty->assign('typestreetvals', $arrTypestreet['values']);
        $smarty->assign('idtypestreet', $idTypeStreetEnable  );

        // --- Address ---
        if ($rsPerson->fields['street'] == 'Choose')
            $smarty->assign('placeholder_address',$this->getLanguageWord('Placeholder_address'));
        else
            $smarty->assign('person_address',$rsPerson->fields['street']);

        // --- Number ---
        $smarty->assign('person_number',$rsPerson->fields['number']);

        // --- Complement ---
        $smarty->assign('person_complement',$rsPerson->fields['complement']);

        $change_pass = $this->getChangePassById($cod_usu);
        $smarty->assign('changepass', $change_pass);


    }

    public function comboGender()
    {

        $fieldsID[] = 'M';
        $values[]   = 'Masculino';
        $fieldsID[] = 'F';
        $values[]   = 'Feminino';

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function getChangePassById($idPerson)
    {
        $this->loadModel('admin/person_model');
        $dbSysPerson  = new person_model();
        $rsChangePwd = $dbSysPerson->getChangePass($idPerson);
        return $rsChangePwd;
    }

}
?>
