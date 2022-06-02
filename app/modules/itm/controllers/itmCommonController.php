<?php

require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

/*
 *  Common methods - Information Technology Management Module
 */


class itmCommon extends Controllers  {


    public static $_logStatus;

    public function __construct()
    {

        parent::__construct();

        /**
         * Here's the models most used
         */
        $this->loadModel('mac_model');
        $dbMac = new mac_model();
        $this->dbMac = $dbMac;

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

        //$this->_serverApi = $this->_getServerApi();
        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

        $this->loadModel('admin/tracker_model');
        $this->dbTracker = $dbTracker = new tracker_model();

        // Tracker Settings
        if($_SESSION['TRACKER_STATUS'] == 1) {
            $this->tracker = true;
        }  else {
            $this->tracker = false;
        }

        $this->modulename = 'ITM' ;
        $this->idmodule =  $this->getIdModule($this->modulename);


    }

    public function _makeNavItm($smarty)
    {
        $idPerson = $_SESSION['SES_COD_USUARIO'];
        $listRecords = $this->makeMenuByModule($idPerson,$this->idmodule);
        $moduleinfo = $this->getModuleInfo($this->idmodule);

        //$smarty->assign('displayMenu_1',1);
        $smarty->assign('listMenu_1',$listRecords);
        $smarty->assign('moduleLogo',$moduleinfo->fields['headerlogo']);
        $smarty->assign('modulePath',$moduleinfo->fields['path']);
    }

    function _getServerApi()
    {

        $sessionVal = $_SESSION['itm']['server_api'] ;
        if (isset($sessionVal) && !empty($sessionVal)) {
            return $sessionVal;
        } else {
            if ($this->log)
                $this->logIt("Url da API sem valor - Variavel de sessao: {$_SESSION['itm']['server_api']}" . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false ;
        }

    }

    public function _comboTypeHost()
    {
        $rs = $this->dbMac->getNetUserType();

        $fieldsID[] = '';
        $values[]   = $this->getLanguageWord('Select');

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idnetusertype'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboBandRateLimit()
    {
        $arrRet['ids'] = array('256k','512k','1024k','2048k','5120k','10240k','0');
        $arrRet['values'] = array('256k','512k','1M','2M','5M','10M', $this->getLanguageWord('itm_band_no_limit'));

        return $arrRet;
    }

    public function _displayButtons($smarty,$permissions)
    {
        (isset($permissions[1]) && $permissions[1] == "Y") ? $smarty->assign('display_btn_add', '') : $smarty->assign('display_btn_add', 'hide');
        (isset($permissions[2]) && $permissions[2] == "Y") ? $smarty->assign('display_btn_edit', '') : $smarty->assign('display_btn_edit', 'hide');
        (isset($permissions[2]) && $permissions[2] == "Y") ? $smarty->assign('display_btn_enable', '') : $smarty->assign('display_btn_enable', 'hide');
        (isset($permissions[2]) && $permissions[2] == "Y") ? $smarty->assign('display_btn_disable', '') : $smarty->assign('display_btn_disable', 'hide');
        (isset($permissions[3]) && $permissions[3] == "Y") ? $smarty->assign('display_btn_delete', '') : $smarty->assign('display_btn_delete', 'hide');
        (isset($permissions[4]) && $permissions[4] == "Y") ? $smarty->assign('display_btn_export', '') : $smarty->assign('display_btn_export', 'hide');
        (isset($permissions[5]) && $permissions[5] == "Y") ? $smarty->assign('display_btn_email', '') : $smarty->assign('display_btn_email', 'hide');
        (isset($permissions[6]) && $permissions[6] == "Y") ? $smarty->assign('display_btn_sms', '') : $smarty->assign('display_btn_sms', 'hide');
    }

    /**
     * Returns the sql sintax, according JQgrid types
     *
     * @param string $oper Name of the PqGrid operation
     * @param string $column Field to search
     * @param string $search String to search
     * @return string    SQL statement or error message
     *
     */
    public function _getSQLOperation($oper, $column=null, $search)
    {
        switch ($oper) {
            case 'eq' : // equal
                $ret = ((!$column) ? "" : "pipeLatinToUtf8(" . $column . ")") . ' = ' . "pipeLatinToUtf8('" . $search . "')";
                break;
            case 'ne': // not equal
                $ret = ((!$column) ? "" : "pipeLatinToUtf8(" . $column . ")") . ' != ' . "pipeLatinToUtf8('" . $search . "')";
                break;
            case 'lt': // less
                $ret = ((!$column) ? "" : $column) . ' < ' . $search;
                break;
            case 'le': // less or equal
                $ret = ((!$column) ? "" : $column) . ' <= ' . $search;
                break;
            case 'gt': // greater
                $ret = ((!$column) ? "" : $column) . ' > ' . $search;
                break;
            case 'ge': // greater or equal
                $ret = ((!$column) ? "" : $column) . ' >= ' . $search;
                break;
            case 'bw': // begins with
                $search = str_replace("_", "\_", $search);
                $ret = ((!$column) ? "" : "pipeLatinToUtf8(" . $column . ")") . ' LIKE ' . "pipeLatinToUtf8('" . $search . '%' . "')";
                break;
            case 'bn': //does not begin with
                $ret = ((!$column) ? "" : "pipeLatinToUtf8(" . $column . ")") . ' NOT LIKE ' . "pipeLatinToUtf8('" . $search . '%' . "')";
            case 'in': // is in
                $ret = ((!$column) ? "" : "pipeLatinToUtf8(" . $column . ")") . ' IN (' . "pipeLatinToUtf8('" . $search . "')" . ')';
                break;
            case 'ni': // is not in
                $ret = ((!$column) ? "" : "pipeLatinToUtf8(" . $column . ")") . ' NOT IN (' . "pipeLatinToUtf8('" . $search . "')" . ')';
                break;
            case 'ew': // ends with
                $search = str_replace("_", "\_", $search);
                $ret = ((!$column) ? "" : "pipeLatinToUtf8(" . $column . ")") . ' LIKE ' . "pipeLatinToUtf8('" . '%' . rtrim($search) . "')";
                break;
            case 'en': // does not end with
                $search = str_replace("_", "\_", $search);
                $ret = ((!$column) ? "" : "pipeLatinToUtf8(" . $column . ")") . ' NOT LIKE ' . "pipeLatinToUtf8('" . '%' . rtrim($search) . "')";
                break;
            case 'cn': // contains
                $search = str_replace("_", "\_", $search);
                $ret = ((!$column) ? "" : "pipeLatinToUtf8(" . $column . ")") . ' LIKE ' . "pipeLatinToUtf8('" . '%' . $search . '%' . "')";
                break;
            case 'nc': // does not contain
                $search = str_replace("_", "\_", $search);
                $ret = ((!$column) ? "" : "pipeLatinToUtf8(" . $column . ")") . ' NOT LIKE ' . "pipeLatinToUtf8('" . '%' . $search . '%' . "')";
                break;
            case 'nu': //is null
                $ret = ((!$column) ? "" : $column) . ' IS NULL';
                break;
            case 'nn': // is not null
                $ret = ((!$column) ? "" : $column) . ' IS NOT NULL';
                break;
            default:
                die('Operator invalid in grid search !!!' . " File: " . __FILE__ . " Line: " . __LINE__);
                break;
        }

        return $ret;
    }

    public function makeLog($idprogram,$iduser,$ope,$idrecord){

    }

}