<?php

require_once(HELPDEZK_PATH . '/app/modules/helpdezk/controllers/hdkCommonController.php');

class hdkEmailConfig extends hdkCommon
{
    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct()
    {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        $this->idPerson = $_SESSION['SES_COD_USUARIO'];

        $this->modulename = 'helpdezk' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);

        $this->loadModel('emailconfig_model');
        $dbEmailConfig = new emailconfig_model();
        $this->dbEmailConfig = $dbEmailConfig;

        $this->logIt("entrou  :".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,7,'general',__LINE__);

    }

    public function index()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();


        $this->makeNavVariables($smarty);
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);

        $smarty->assign('token', $token) ;

        $smarty->display('email-config.tpl');

    }

    public function jsonGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

        $where = '';

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='name';
        if(!$sord)
            $sord ='ASC';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'b.title') $searchField = 'b.title';
            if ( $_POST['searchField'] == 'a.title') $searchField = 'a.title';
            if ( $_POST['searchField'] == 'a.dtcreate') $searchField = 'a.dtcreate';
            if ( $_POST['searchField'] == 'a.dtstart') $searchField = 'a.dtstart';
            if ( $_POST['searchField'] == 'a.dtend') $searchField = 'a.dtend';

            $where .= 'AND ' . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->dbEmailConfig->countConfigs($where);

        if( $count->fields['total'] > 0 && $rows > 0) {
            $total_pages = ceil($count->fields['total']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";
        //

        $rsEmailConfs = $this->dbEmailConfig->selectConfigs($where,$order,$limit);
//
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);
//
        while (!$rsEmailConfs->EOF) {
            $status_fmt = ($rsEmailConfs->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

            $aColumns[] = array(
                'id'     => $rsEmailConfs->fields['idconfig'],
                'name'        => $langVars[$rsEmailConfs->fields['smarty']],
                'statuslbl'        => $status_fmt,
                'status'     => $rsEmailConfs->fields['status']

            );
            $rsEmailConfs->MoveNext();
        }
        //

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count->fields['total'],
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateTemplate()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();

        $smarty->assign('token', $token) ;

        $this->makeNavVariables($smarty);
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        $smarty->assign('summernote_version', $this->summernote);
        $smarty->display('template-create.tpl');
    }

    public function formUpdateTemplate()
    {
        $idConfig = $this->getParam('id');
        //echo "<pre>"; print_r($_SESSION); echo "</pre>";
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();

        $getid = $this->dbEmailConfig->getTemplate($idConfig);
        $temp = $getid->fields['idtemplate'];
        $data = $this->dbEmailConfig->getTemplateData($temp);
        $smarty->assign('tempsubject', $data->fields['name']);
        $smarty->assign('description', $data->fields['description']);

        $smarty->assign('hidden_idtemplate', $temp);
        $smarty->assign('token', $token) ;

        $this->makeNavVariables($smarty);
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        $smarty->assign('summernote_version', $this->summernote);
        $smarty->display('template-update.tpl');

    }

    function makeScreenWarning($objSmarty,$rs,$oper)
    {

        // --- Tópico ---
        if ($oper == 'update') {
            $idTopicEnable = $rs->fields['idtopic'];
        } elseif ($oper == 'create') {
            $idTopicEnable = 1;
        }
        $arrTopic = $this->comboTopic();
        $objSmarty->assign('topicids',  $arrTopic['ids']);
        $objSmarty->assign('topicvals', $arrTopic['values']);
        $objSmarty->assign('idtopic', $idTopicEnable );

        // --- Título ---
        if ($oper == 'update') {
            if (empty($rs->fields['title_warning']))
                $objSmarty->assign('plh_title','Informe o título do aviso.');
            else
                $objSmarty->assign('title_warning',$rs->fields['title_warning']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_title','Informe o título do aviso.');
        }

        // --- Descrição ---
        if ($oper == 'update') {
            if (empty($rs->fields['description']))
                $objSmarty->assign('plh_description','Informe a descrição do aviso.');
            else
                $objSmarty->assign('description',$rs->fields['description']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_description','Informe a descrição do aviso.');
        }

        // --- Data inicio aviso ---
        if ($oper == 'update') {
            if ($rs->fields['dtstart'] == '0000-00-00 00:00:00'){
                $objSmarty->assign('plh_dtstart','Informe a data.');
                $objSmarty->assign('plh_timestart','Informe o horário.');
            }else{
                list($starttmp,$timetmp) = explode(' ',$rs->fields['dtstart']);
                list($yeartmp,$monthtmp,$daytmp) = explode('-',$starttmp);
                $startfmt = $daytmp.'/'.$monthtmp.'/'.$yeartmp;
                $objSmarty->assign('dtstart',$startfmt);
                $objSmarty->assign('timestart',$timetmp);
            }
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_dtstart',date('d/m/Y
            '));
            $objSmarty->assign('plh_timestart',date('H:i'));
        }

        // --- Data encerramento aviso ---
        if ($oper == 'update') {
            if ($rs->fields['dtend'] == '0000-00-00 00:00:00'){
                $objSmarty->assign('flagUntil','S');
                $objSmarty->assign('checkedUntil','checked=checked');
            }else{
                list($endtmp,$endtmtmp) = explode(' ',$rs->fields['dtend']);
                list($yeartmp,$monthtmp,$daytmp) = explode('-',$endtmp);
                $endtfmt = $daytmp.'/'.$monthtmp.'/'.$yeartmp;
                $objSmarty->assign('dtend',$endtfmt);
                $objSmarty->assign('timeend',$endtmtmp);
                $objSmarty->assign('flagUntil','N');
                $objSmarty->assign('checkedUntil','');
            }
        } elseif ($oper == 'create') {
            $objSmarty->assign('flagUntil','S');
        }

        // --- Envia Email ---
        if ($oper == 'update') {
            if ($rs->fields['sendemail'] == 'N'){
                $objSmarty->assign('checkedSend','');
            }else{
                $objSmarty->assign('checkedSend','checked=checked');
            }
        } elseif ($oper == 'create') {
            $objSmarty->assign('checkedSend','');
        }

        // --- Mostrar em ---
        if ($oper == 'update') {
            $idShowinEnable = $rs->fields['showin'];
        } elseif ($oper == 'create') {
            $idShowinEnable = 1;
        }
        $arrShowin = $this->comboShowIn();
        $objSmarty->assign('showinids',  $arrShowin['ids']);
        $objSmarty->assign('showinvals', $arrShowin['values']);
        $objSmarty->assign('idshowin', $idShowinEnable );


    }

    function createTemplate()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        } 

        $idperson = $_SESSION['SES_COD_USUARIO'];
        $database = $this->getConfig('db_connect');

        if ($database == 'oci8po') {
            $dtStart = $this->formatSaveDateHour($_POST['dtstart']." ".$_POST['timestart']);
            $dtStart = $this->oracleDate($dtStart);
        }else {
            $dtStart = "'".str_replace("'", "",$this->formatSaveDate($_POST['dtstart']))." ".$_POST['timestart']."'";
        }

        if ($database == 'mysqli') {
            $now = "NOW()";
            if($_POST['warningend'] == "S"){//Até ser encerrado
                $dtEnd = "'0000-00-00 00:00:00'";
            }else{
                $dtEnd = "'".str_replace("'", "",$this->formatSaveDate($_POST['dtend']))." ".$_POST['timeend']."'";
            }
        }elseif ($database == 'oci8po') {
            $now = "SYSDATE";
            if($_POST['warningend'] == "S"){//Até ser encerrado
                $dtEnd = "NULL";
            }else{
                $dtEnd = $this->formatSaveDateHour($_POST['dtend']." ".$_POST['timeend']);
                $dtEnd = $this->oracleDate($dtEnd);
            }
        }

        $data = array(
            "idtopic" 		=> $_POST['topic'],
            "idperson" 		=> $idperson,
            "title" 		=> "'".addslashes($_POST['title'])."'",
            "description" 	=> "'".addslashes($_POST['description'])."'",
            "dtcreate" 		=> $now,
            "dtstart" 		=> $dtStart,
            "dtend" 		=> $dtEnd,
            "sendemail"		=> "'".$_POST['sendemailconf']."'",
            "showin"		=> $_POST['showin'],
            "emailsent"		=> 0
        );

        $this->dbWarning->BeginTrans();

        $ret = $this->dbWarning->insertWarning($data);

        if (!$ret) {
            $this->dbWarning->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Warning  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "status" => 'OK'
        );

        $this->dbWarning->CommitTrans();
        echo json_encode($aRet);

    }

    function updateTemplate()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        //echo "<pre>"; print_r($_POST); echo "</pre>";
        $id = $_POST['idtemplate'];
        $name = addslashes($_POST['templateName']);
        $description = addslashes($_POST['description']);

        $this->dbEmailConfig->BeginTrans();

        $ret = $this->dbEmailConfig->updateTemplate($id, $name, $description);

        if (!$ret) {
            $this->dbEmailConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update E-mail Template  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "status"   => 'OK'
        );

        $this->dbEmailConfig->CommitTrans();
        echo json_encode($aRet);

    }

    function changeStatus()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $idconfig = $_POST['idconfig'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbEmailConfig->changeConfStatus($idconfig,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Email Config Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idconf" => $idconfig,
            "status" => 'OK',
            "personstatus" => $newStatus
        );

        echo json_encode($aRet);

    }

}