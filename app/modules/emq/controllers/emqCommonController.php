<?php

require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

/*
 *  Common methods - Academic Module
 */


class emqCommon extends Controllers  {


    public static $_logStatus;

    public function __construct()
    {
        parent::__construct();

        $this->program  = basename( __FILE__ );

        //$this->loadModel('acdindicadoresnotas_model');
        //$dbInd = new acdindicadoresnotas_model();
        //$this->dbIndicador = $dbInd;

        // Log settings
        $objSyslog = new Syslog();
        $this->log  = $objSyslog->setLogStatus() ;
        self::$_logStatus = $objSyslog->setLogStatus() ;
        if ($this->log) {
            $this->_logLevel = $objSyslog->setLogLevel();
            $this->_logHost = $objSyslog->setLogHost();
            if($this->_logHost == 'remote')
                $this->_logRemoteServer = $objSyslog->setLogRemoteServer();
        }

        $this->_serverApi = $this->_getServerApi();


        $this->loadModel('admin/tracker_model');
        $this->dbTracker = $dbTracker = new tracker_model();

        // Tracker Settings
        if($_SESSION['TRACKER_STATUS'] == 1) {
            $this->tracker = true;
        }  else {
            $this->tracker = false;
        }

        $this->modulename = 'Intranet' ;
        $this->idmodule = $this->getIdModule($this->modulename) ;

        $this->loadModel('acd/acdstudent_model');
        $dbStudent = new acdstudent_model();
        $this->dbStudent = $dbStudent;

        $this->loadModel('parent_model');
        $dbParent = new parent_model();
        $this->dbParent = $dbParent;

    }

    public function _makeNavEmq($smarty)
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

        $sessionVal = $_SESSION['emq']['server_api'] ;
        if (isset($sessionVal) && !empty($sessionVal)) {
            return $sessionVal;
        } else {
            if ($this->log)
                $this->logIt('Url da API da Dominio sem valor - Variavel de sessao: $_SESSION[\'emq\'][\'server_api_dominio\']' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false ;
        }

    }

    /**
     **/
    public function _comboSectors()
    {
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $response = file_get_contents($this->_serverApi.'/api/src/public/getsectors',false,$ctx);

        if($response) {
            $response = json_decode($response, true);

            if (!$response['status']){
                if ($this->log)
                    $this->logIt('Nao retornou dados do servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $a = $response['result'];
            //echo "<pre>"; print_r($a); "</pre>";

            foreach ($a as $item){
                $fieldsID[] = $item['CoTipo'];
                $values[]   = $item['NoTipo'];
            }
            $arrRet['ids'] = $fieldsID;
            $arrRet['values'] = $values;
        }else{$arrRet = array();}

        return $arrRet;
    }

    public function _getNoteAttMaxFiles()
    {
        if (version_compare($this->helpdezkVersionNumber, '1.0.1', '>' )) {
            return 5;
        } else {
            return 1;
        }
    }

    public function _getNoteAcceptedFiles()
    {
        // Images
        $images = '.jpg, .jpeg, .png, .gif';
        // Documents
        $documents = '.pdf, .doc, .docx, .ppt, .pptx, .pps, .ppsx, .odt, .xls, .xlsx, .zip';
        // Audio
        $audio = '.mp3, .m4a, .ogg, .wav';
        // Video
        $video = '.mp4, .m4v, .mov, .wmv, .avi, .mpg, .ogv, .3gp, .3g2';

        return $images .','.$documents.','.$audio.','.$video ;
    }

    public function _getTicketAttMaxFiles()
    {
        if (version_compare($this->helpdezkVersionNumber, '1.0.1', '>' )) {
            return 10;
        } else {
            return 1;
        }
    }

    public function _getTicketAcceptedFiles()
    {
        // Images
        $images = '.jpg, .jpeg, .png, .gif';
        // Documents
        $documents = '.pdf, .doc, .docx, .ppt, .pptx, .pps, .ppsx, .odt, .xls, .xlsx, .zip';
        // Audio
        $audio = '.mp3, .m4a, .ogg, .wav';
        // Video
        $video = '.mp4, .m4v, .mov, .wmv, .avi, .mpg, .ogv, .3gp, .3g2';

        return $images .','.$documents.','.$audio.','.$video ;
    }

    public function _getTicketAttMaxFileSize()
    {
        return ini_get(upload_max_filesize);
    }

    public function _personsBySector($idsector)
    {
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $response = file_get_contents($this->_serverApi.'/api/src/public/getsectorsrecip/sector/'.$idsector,false,$ctx);

        if($response) {
            $response = json_decode($response, true);

            if (!$response['status']){
                if ($this->log)
                    $this->logIt('Nao retornou dados do servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $arrRet = $response['result'];

        }else{$arrRet = array();}

        return $arrRet;
    }

    public function _getEmqPersonData($idperson)
    {
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $response = file_get_contents($this->_serverApi.'/api/src/public/getsectorsrecip/person/'.$idperson,false,$ctx);

        if($response) {
            $response = json_decode($response, true);

            if (!$response['status']){
                if ($this->log)
                    $this->logIt('Nao retornou dados do servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $arrRet = $response['result'];

        }else{$arrRet = array();}

        return $arrRet;
    }

    public function _teachersByCourse($idcourse)
    {
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $response = file_get_contents($this->_serverApi.'/api/src/public/getteachersrecip/'.$idcourse,false,$ctx);

        if($response) {
            $response = json_decode($response, true);

            if (!$response['status']){
                if ($this->log)
                    $this->logIt('Nao retornou dados do servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $arrRet = $response['result'];

        }else{$arrRet = array();}

        return $arrRet;
    }

    public function _comboStudent($where = null)
    {
        $rs = $this->dbStudent->getStudentData($where);
        $fieldsID[] = '';
        $values[]   = $this->getLanguageWord('Select');
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idstudent'];
            $values[]   = (($rs->fields['idintranet']) ? $rs->fields['idintranet'] : $rs->fields['idperseus']).' - '.$rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboKinship($where = null)
    {
        $rs = $this->dbStudent->getKinshipData($where,"ORDER BY `description`");
        $fieldsID[] = '';
        $values[]   = $this->getLanguageWord('Select');
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idkinship'];
            $values[]   = $rs->fields['description'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboGender()
    {
        $rs = $this->dbParent->getGender('','ORDER BY description');

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idgender'];
            $values[]   = $rs->fields['description'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

}