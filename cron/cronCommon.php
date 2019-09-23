<?php

require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

/*
 *  Common methods - Information Technology Management Module
 */


class cronCommon extends cronSystem  {


    public static $_logStatus;

    public function __construct()
    {

        parent::__construct();

        /**
         * Here's the models most used
         */
        $this->loadModel('admin/index_model');
        $dbIndex = new index_model();
        $this->dbIndex = $dbIndex;


        $this->getGlobalSessionData();

        // Log settings
        $objSyslog = new Syslog();
        $this->log  = $objSyslog->setLogStatus() ;
        self::$_logStatus = $objSyslog->setLogStatus() ;
        if ($this->log) {
            $objSyslog->SetFacility(18);
            $this->_logLevel = $objSyslog->setLogLevel();
            $this->_logHost = $objSyslog->setLogHost();
            if($this->_logHost == 'remote')
                $this->_logRemoteServer = $objSyslog->setLogRemoteServer();
        }

        $this->_serverApi = $this->_getServerApi();
        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

        $this->loadModel('admin/tracker_model');
        $this->dbTracker = $dbTracker = new tracker_model();

        // Tracker Settings
        if($_SESSION['TRACKER_STATUS'] == 1) {
            $this->tracker = true;
        }  else {
            $this->tracker = false;
        }


    }

    function _getServerApi()
    {

        /*$sessionVal = $_SESSION['itm']['server_api'] ;
        if (isset($sessionVal) && !empty($sessionVal)) {
            return $sessionVal;
        } else {
            if ($this->log)
                $this->logIt("Url da API sem valor - Variavel de sessao: {$_SESSION['itm']['server_api']}" . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false ;
        }*/
        return $this->getConfig('server_api');

    }

    public function getGlobalSessionData()
    {

        // Global Config Data
        $rsConfig = $this->dbIndex->getConfigGlobalData();
        while (!$rsConfig->EOF) {
            $ses = $rsConfig->fields['session_name'];
            $val = $rsConfig->fields['value'];
            $_SESSION[$ses] = $val;
            $rsConfig->MoveNext();
        }

    }

}