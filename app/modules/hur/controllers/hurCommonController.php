<?php

require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

/*
 *  Common methods - Helpdezk Module
 */


class hurCommon extends Controllers  {


    public static $_logStatus;

    public function __construct()
    {
        parent::__construct();

        $this->program  = basename( __FILE__ );
        
        $this->loadModel('funcionario_model');
        $dbFunc = new funcionario_model();
        $this->dbFuncionario = $dbFunc;

        $this->loadModel('candidate_model');
        $dbCandidate = new candidate_model();
        $this->dbCandidate = $dbCandidate;
        
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

        if (!$this->_serverApi)
            die('erro');
        $ret = $this->_atualizaFuncionarios('4');

        if($ret === false ) {
            $this->_erroServerDominio = true;

        } else {
            $this->_erroServerDominio = false;
        }

        $this->loadModel('admin/tracker_model');
        $this->dbTracker = $dbTracker = new tracker_model();

        // Tracker Settings
        if($_SESSION['TRACKER_STATUS'] == 1) {
            $this->tracker = true;
        }  else {
            $this->tracker = false;
        }

        $this->modulename = 'RecursosHumanos' ;
        $this->idmodule = $this->getIdModule($this->modulename) ;

    }

    public function _makeNavHur($smarty)
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

        $sessionVal = $_SESSION['hur']['server_api_dominio'] ;
        if (isset($sessionVal) && !empty($sessionVal)) {
            return $sessionVal;
        } else {
            if ($this->log)
                $this->logIt('Url da API da Dominio sem valor - Variavel de sessao: $_SESSION[\'hur\'][\'server_api_dominio\']' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false ;
        }

    }

    public function _getNumFuncionarios($where = null)
    {
        return $this->dbFuncionario->getNumeroDeFuncionarios($where);
    }


    public function _getFuncionario($where,$order,$group,$limit)
    {
        return $this->dbFuncionario->getFuncionario($where,$order,$group,$limit);
    }

    function _getDiffTime()
    {
        return $this->dbFuncionario->getTempoAtualizacao();
    }

    function _atualizaFuncionarios($horas)
    {

        if ($this->_getDiffTime() > $horas or $horas == 0)
        {
           /*
            1 - SOC EDUCACIONAL MARIO QUINTANA LTDA
            2 - INSTITUTO EDUCACIONAL MQ - EIRELI
            3 - SOCIEDADE EDUCACIONAL TRES VENDAS LTDA
            */

            $arr = array(1, 2, 3);
            $ctx = stream_context_create(array(
                    'http' => array(
                        'timeout' => 30
                    )
                )
            );
            $j = 0;
            foreach ($arr as &$value) {

                $idEmpresa =  $value;

                $response = file_get_contents($this->_serverApi.'/api/src/public/funcionarios/'.$idEmpresa,false,$ctx);

                if(!$response) {
                    if ($this->log)
                        $this->logIt('Sem conexao com o servidor da Dominio - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                    return false;
                }

                $response = json_decode($response, true);

                if (!$response['status']){
                    if ($this->log)
                        $this->logIt('Nao retornou dados do servidor da Dominio - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                    return false;
                }

                $ret = $this->dbFuncionario->deleteFuncionario($idEmpresa);

                if (!$ret) {
                    if ($this->log)
                        $this->logIt('Delete Funcionario - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                }

                $a = $response['result'];
                for ($i = 0; $i < sizeof($a); $i++)
                {
                    $nome =  $a[$i]['nome'];
                    $cargo =  $a[$i]['cargo'];
                    $rg =  $a[$i]['rg'];
                    $sexo =  $a[$i]['sexo'];
                    $dtnasc =  $a[$i]['dtnasc'];
                    $setor =  $a[$i]['setor'];
                    $empresa =  $a[$i]['empresa'];
                    $cpf =  $a[$i]['cpf'];
                    $dtadmissao =  $a[$i]['dtadmissao'];
                    $ret = $this->dbFuncionario->insertFuncionario($idEmpresa,$nome,$cargo,$rg,$sexo,$dtnasc,$setor,$empresa,$cpf,$dtadmissao);
                    if (!$ret) {
                        if ($this->log)
                            $this->logIt('Insere Funcionario - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                        return false;
                    }
                    $j++;
                }
            }
            if ($this->log)
                $this->logIt($j .' funcionarios atualizados do db Dominio: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 6, 'general', __LINE__);

            $this->_updateDataHoraAtualizacao();

            return $j;
        } else {
            return 0;
        }
        return 0;
    }

    function _updateDataHoraAtualizacao()
    {
        $this->dbFuncionario->updateDataHoraAtualizacao();
    }

    function _getNumFuncEmpresa($idempresa)
    {
        return $this->dbFuncionario->getNumeroDeFuncionarios('where idempresa = '.$idempresa);
    }

    public function _getDataAtualizacao()
    {
        return $this->dbFuncionario->getDataAtualizacao();
    }

    public function _getCandidate($where,$order=null,$group=null,$limit=null)
    {
        return $this->dbCandidate->getCandidate($where,$order,$group,$limit);
    }

    public function _getNumCandidates($where = null)
    {
        return $this->dbCandidate->getNumCandidates($where);
    }

    public function _comboArea()
    {
        $rs = $this->dbCandidate->getArea('','ORDER BY description');
        $fieldsID[] = '';
        $values[]   = '';
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idarea'];
            $values[]   = $rs->fields['description'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboRole($idarea=null)
    {
        $where = "";
        if($idarea) $where = "WHERE idarea = $idarea";

        $rs = $this->dbCandidate->getRole($where,'ORDER BY description');
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idrole'];
            $values[]   = $rs->fields['description'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _getCandidateFile($idcurriculum,$type)
    {
        return $this->dbCandidate->getCandidateFile("WHERE idcurriculum = $idcurriculum AND typefile = '$type'");
    }

    /**
     * Method to send e-mails
     *
     * @author Rogerio Albandes <rogerio.albandes@pipegrep.com.br>
     *
     * @param string  $subject E-mail subject
     * @param string  $body  E-mail body
     * @param array   $address Addreaesse
     * @param array   $attachment Addreaesse
     *
     * @return string true|false
     */
    public function _sendEmailDefault($emailTitle,$body,$address,$attachment=null)
    {
        $this->loadModel('scm/scmemailconfig_model');
        $this->dbEmailConfig = $dbEmailConfig = new scmemailconfig_model();
        $this->_tokenOperatorLink = false;

        $dbCommon = new common();
        $emconfigs = $dbCommon->getEmailConfigs();
        $tempconfs = $dbCommon->getTempEmail();

        $rsSender = $dbEmailConfig->getUserEmail($_SESSION['SES_COD_USUARIO']);
        $mail_sender = $rsSender->fields['email'];
        $mail_title = $_SESSION['SES_NAME_PERSON'];

        $params = array(
            "subject" => $emailTitle,
            "contents" => $body,
            "sender_name" => $mail_title,
            "sender" => $mail_sender,
            "address" => $address,
            "attachment" => $attachment,
            "idmodule" => $this->idmodule,
            "modulename" => $this->module_name,
            "msg" => "",
            "msg2" => "",
            "tracker" => false
        );
        
        $done = $this->sendEmailDefault($params);

        if (!$done) {
            if($this->log)
                $this->logIt("Can't send e-mail - Program: {$this->program}" ,3,'general',__LINE__);
            return false ;
        } else {
            return true ;
        }

    }

    function _saveTracker($idmodule,$mail_sender,$sentTo,$subject,$body)
    {
        $ret = $this->dbTracker->insertEmail($idmodule,$mail_sender,$sentTo,$subject,$body);
        if(!$ret) {
            return false;
        } else {
            return $ret;
        }

    }

    public function _isEmailDone($objmail,$params){
        $done = $objmail->Send();
        if (!$done) {
            if($this->log AND $_SESSION['EM_FAILURE_LOG'] == '1') {
                $objmail->SMTPDebug = 5;
                $objmail->Send();
                $this->logIt("Error send email, " . $params['subject'] . ' - program: ' . $this->program, 3, 'email', __LINE__);
                $this->logIt("Error send email, " . $params['subject'] . ' - Error Info:: ' . $objmail->ErrorInfo . ' - program: ' . $this->program, 3, 'email', __LINE__);
                $this->logIt("Error send email, " . $params['subject'] . ' - Variables: HOST: '.$params['mail_host'].'  DOMAIN: '.$params['mail_domain'].'  AUTH: '.$params['mail_auth'].' PORT: '.$params['mail_port'].' USER: '.$params['mail_username'].' PASS: '.$params['mail_password'].'  SENDER: '.$params['mail_sender'].' - program: ' . $this->program, 7, 'email', __LINE__);
            }
            $error_send = true ;
        } else {
            if($this->log AND $_SESSION['EM_SUCCESS_LOG'] == '1') {
                $this->logIt("Email Succesfully Sent, ". $params['subject']  ,6,'email');
            }
            $error_send = false ;
        }

        return $error_send;

    }

    public function _getInitialGrid($where,$order=null,$group="name",$limit=null)
    {
        return $this->dbCandidate->getCandidateInitialGrid($where,$order,$group,$limit);
    }

    function _removerAcentos($str) {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z', 'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?', 'ç', 'Ç', "'");
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o','c','C', " ");
        return str_replace($a, $b, $str);
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
}