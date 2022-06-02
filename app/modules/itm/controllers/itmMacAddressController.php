<?php

require_once(HELPDEZK_PATH . '/app/modules/itm/controllers/itmCommonController.php');

class itmMacAddress extends itmCommon {
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

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );
        $this->idprogram =  $this->getIdProgramByController('itmMacAddress');

        $this->modulename = 'itm' ;


        $dbCommon = new common();
        $id = $dbCommon->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }

        $this->loadModel('mac_model');
        $dbMac = new mac_model();
        $this->dbMac = $dbMac;

        $this->loadModel('acd/acdstudent_model');
        $dbProfile = new acdstudent_model();
        $this->dbProfile = $dbProfile;

        $this->loadModel('hur/funcionario_model');
        $dbEmployee = new funcionario_model();
        $this->dbEmployee = $dbEmployee;
    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavItm($smarty);

        $smarty->assign('token', $this->_makeToken()) ;

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('itm-mac.tpl');
        }else{
            $smarty->assign('href', $this->helpdezkUrl.'/bmm/home');
            $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
        }

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
            $sidx ='a.name';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'a.name') $searchField = 'a.name';
            if ( $_POST['searchField'] == 'b.name') $searchField = 'b.name';
            if ( $_POST['searchField'] == 'mac') $searchField = 'mac';
            if ( $_POST['searchField'] == 'ip') $searchField = 'ip';
            if ( $_POST['searchField'] == 'profilename'){
                $nameProfile = addslashes($_POST['searchString']);
                $rsInternalUser = $this->dbMac->getNetUserByName($this->_getSQLOperation($_POST['searchOper'],null,$nameProfile),"ORDER BY `name`");
                $inUser = "";
                while(!$rsInternalUser->EOF){
                    $inUser .= "'".$rsInternalUser->fields['id']."',";
                    $rsInternalUser->MoveNext();
                }
                $inUser = substr($inUser,0,-1);
                $searchField = 'idnetuser';
                $_POST['searchOper'] = 'in';
                $_POST['searchString'] = $inUser;
            }

            if ( $_POST['searchField'] == 'profilename'){
                $where .= " AND idnetuser IN ({$_POST['searchString']}) ";
            }else{
                $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);
            }


        }

        if ($_POST['_search'] == 'true' && $_POST['searchField'] == 'profilename' && $inUser == ""){
            $count['fields']['total'] = 0;
        }
        else{
            $count = $this->dbMac->countHost($where);
        }        

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
        
        if ($_POST['_search'] == 'true' && $_POST['searchField'] == 'profilename' && $inUser == ""){
            $aColumns[] = array();
        }
        else{
            $rsHosts = $this->dbMac->selectHost($where,$order,$limit);

            while (!$rsHosts->EOF) {

                $status_fmt = ($rsHosts->fields['host_status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

                $aColumns[] = array(
                    'id'            => $rsHosts->fields['idhost'],
                    'hosttype'      => $rsHosts->fields['netusertype'],
                    'host'          => $rsHosts->fields['hostname'],
                    'mac'           => $rsHosts->fields['mac'],
                    'ip'            => $rsHosts->fields['ip'],
                    'description'   => $rsHosts->fields['description'],
                    'status'        => $status_fmt,
                    'statusval'     => $rsHosts->fields['host_status']
                );
                $rsHosts->MoveNext();
            }
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count->fields['total'],
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateMac()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenMac($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavItm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('itm-mac-create.tpl');
    }

    public function formUpdateMac()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idhost = $this->getParam('idhost');

        $field = 'idhost,name,mac,idnetusertype,idnetuser,description,timedeactivate';
        $table = 'itm_tbhost';
        $where = "idhost = $idhost";

        $rsHost = $this->dbMac->getHostData($field,$table,$where);

        $this->makeScreenMac($smarty,$rsHost,'update');

        $smarty->assign('token', $token) ;
        $smarty->assign('idhost', $idhost);
        $smarty->assign('oldMac', $rsHost->fields['mac']);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavItm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('itm-mac-update.tpl');

    }

    function makeScreenMac($objSmarty,$rs,$oper)
    {
        $arrNetUser = array(1,2,3,4,5,10);
        $arrInternal = array(1,2,3);
        $arrExternal = array(4,5,10);

        // --- Host Type ---
        $arrTypeHost= $this->_comboTypeHost();

        if ($oper == 'update') {
            $idTypeHost = $rs->fields['idnetusertype'];
        } elseif ($oper == 'create') {
            $idTypeHost = 0;
        }

        $objSmarty->assign('typehostids',  $arrTypeHost['ids']);
        $objSmarty->assign('typehostvals', $arrTypeHost['values']);
        $objSmarty->assign('idtypehost', $idTypeHost );

        // --- Band Rate Limit ---
        $arrBandWidth = $this->_comboBandRateLimit();

        if ($oper == 'update') {
            $rsRateLimit = $this->dbMac->getRateLimit($rs->fields['mac']);

            if ($rsRateLimit->RecordCount() > 0){
                list($idBandUp,$idBandDown) = explode('/',$rsRateLimit->fields['value']);
            }else{
                $idBandUp = '0';
                $idBandDown = '0';
            }
        } elseif ($oper == 'create') {
            $idBandUp = '1024k';
            $idBandDown = '2048k';
        }

        $objSmarty->assign('bandwidthids',  $arrBandWidth['ids']);
        $objSmarty->assign('bandwidthvals', $arrBandWidth['values']);
        $objSmarty->assign('idbandup', $idBandUp );
        $objSmarty->assign('idbanddown', $idBandDown );

        // --- Host name ---
        $objSmarty->assign('hostName',$rs->fields['name']);

        // --- MAC Address ---
        $objSmarty->assign('macNumber',$rs->fields['mac']);

        // --- Internal Users Line ---
        if (in_array($rs->fields['idnetusertype'],$arrNetUser)) {
            if(in_array($rs->fields['idnetusertype'],$arrInternal))
                $objSmarty->assign('hideExternal','hide');

            $lbl = $this->dbMac->getNetUserType("WHERE idnetusertype = {$rs->fields['idnetusertype']}");
            $objSmarty->assign('netuserlbl', $lbl->fields['name']);

            $arrNetUser = $this->comboNetUsers($rs->fields['idnetusertype']);
            $objSmarty->assign('netuserids', $arrNetUser['ids']);
            $objSmarty->assign('netuservals', $arrNetUser['values']);
            $objSmarty->assign('idnetuser', $rs->fields['idnetuser'] );
        }else{
            $objSmarty->assign('hideInternal','hide');
            $objSmarty->assign('hideExternal','hide');
        }

        // --- External Users Line ---
        if (in_array($rs->fields['idnetusertype'],$arrExternal)) {

            list($dtend,$timeend) = explode(' ',$this->formatDateHour($rs->fields['timedeactivate']));
            $objSmarty->assign('dtend',$dtend);
            $objSmarty->assign('timeend',$timeend);
        }


        // --- Description ---
        $objSmarty->assign('description',$rs->fields['description']);

    }

    function createMac()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrNetUser = array(1,2,3,4,5,10);
        $arrExternal = array(4,5,10);

        $host_name      = str_replace(" ","_",trim($_POST['hostName']));
        $mac_number     = strtoupper($_POST['macNumber']);
        $cmbType        = $_POST['cmbTypeHost'];
        $cmbNetUser     = in_array($cmbType,$arrNetUser) ? $_POST['cmbNetUser'] : '(NULL)';
        $deactivate     = in_array($cmbType,$arrExternal) ? str_replace("'", "",$this->formatSaveDate($_POST['dtend']))." ".$_POST['timeend'] : '(NULL)';
        $bandValues     = ($_POST['cmbUpBand'] != '0' || $_POST['cmbDownBand'] != '0') ? $_POST['cmbUpBand'].'/'.$_POST['cmbDownBand']: '';
        $description    = addslashes($_POST['description']);

        $ipFinal = $this->ipAssign($cmbType);

        $this->dbMac->BeginTrans();

        $ins = $this->dbMac->insertHost($host_name,$mac_number,long2ip($ipFinal),$ipFinal,$description,$cmbType,$cmbNetUser,$deactivate);

        if (!$ins) {
            $this->dbMac->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Mac Address  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $insRadCheck = $this->dbMac->insertRadCheck($host_name,$mac_number,$description,$cmbType,$cmbNetUser);
        if(!$insRadCheck){
            $this->dbMac->RollbackTrans();
            if($this->log)
                $this->logIt('Add data into itm_tbcheck  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $insRadReply = $this->dbMac->insertRadReply($mac_number,'Framed-IP-Address',long2ip($ipFinal));
        if(!$insRadReply){
            $this->dbMac->RollbackTrans();
            if($this->log)
                $this->logIt('Add data into itm_tbreply  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($bandValues != ''){
            $insBandValues = $this->dbMac->insertRadReply($mac_number,'Mikrotik-Rate-Limit',$bandValues);
            if(!$insBandValues){
                $this->dbMac->RollbackTrans();
                if($this->log)
                    $this->logIt('Add limit rate into itm_tbreply  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        $aRet = array(
            "idhost" => $ins,
            "status" => 'OK'
        );

        $this->dbMac->CommitTrans();
        echo json_encode($aRet);

    }

    function updateMac()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrNetUser = array(1,2,3,4,5,10);
        $arrExternal = array(4,5,10);

        $idhost         = $_POST['idhost'];
        $oldMac         = $_POST['oldMac'];
        $host_name      = str_replace(" ","_",trim($_POST['hostName']));
        $mac_number     = strtoupper($_POST['macNumber']);
        $cmbType        = $_POST['cmbTypeHost'];
        $cmbNetUser     = in_array($cmbType,$arrNetUser) ? $_POST['cmbNetUser'] : '(NULL)';
        $deactivate     = in_array($cmbType,$arrExternal) ? str_replace("'", "",$this->formatSaveDate($_POST['dtend']))." ".$_POST['timeend'] : '(NULL)';
        $bandValues     = ($_POST['cmbUpBand'] != '0' || $_POST['cmbDownBand'] != '0') ? $_POST['cmbUpBand'].'/'.$_POST['cmbDownBand']: '';
        $description    = addslashes($_POST['description']);

        $this->dbMac->BeginTrans();

        $upd = $this->dbMac->updateHost($host_name,$mac_number,$description,$cmbType,$cmbNetUser,$deactivate,$idhost);
        if (!$upd) {
            $this->dbMac->RollbackTrans();
            if($this->log)
                $this->logIt('Update Mac Address  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $updRadCheck = $this->dbMac->updateRadCheck($host_name,$mac_number,$description,$cmbType,$cmbNetUser,$oldMac);
        if(!$updRadCheck){
            $this->dbMac->RollbackTrans();
            if($this->log)
                $this->logIt('Update Mac Address  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $updRadReply = $this->dbMac->updateRadReply($mac_number,$oldMac);
        if(!$updRadReply){
            $this->dbMac->RollbackTrans();
            if($this->log)
                $this->logIt('Update Mac Address  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($bandValues != ''){
            $rsRateLimit = $this->dbMac->getRateLimit($mac_number);

            if ($rsRateLimit->RecordCount() > 0){
                $retBandValues = $this->dbMac->updateRateLimit($bandValues, $mac_number);
            }else{
                $retBandValues = $this->dbMac->insertRadReply($mac_number,'Mikrotik-Rate-Limit',$bandValues);
            }
            
            if(!$retBandValues){
                $this->dbMac->RollbackTrans();
                if($this->log)
                    $this->logIt('Update limit rate - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }else{
            $delBandValues = $this->dbMac->deleteRateLimit($mac_number);
            if(!$delBandValues){
                $this->dbMac->RollbackTrans();
                if($this->log)
                    $this->logIt('Delete limit rate - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }
        


        $aRet = array(
            "status"   => 'OK'
        );

        $this->dbMac->CommitTrans();
        echo json_encode($aRet);


    }

    function ajaxNetUsers()
    {
        $lbl = $this->dbMac->getNetUserType("WHERE idnetusertype = {$_POST['idtypehost']}");
        $cmblist = $this->comboNetUsersHtml($_POST['idtypehost']);
        
        $aRet = array(
            "lbl" => $lbl->fields['name'],
            "cmblist"   => $cmblist
        );

        echo json_encode($aRet);

    }

    public function comboNetUsersHtml($idtypehost)
    {
        switch($idtypehost){
            case 1:
                $ret = $this->dbProfile->getStudentData();
                $select = '';

                while(!$ret->EOF) {
                    $enrollmentLbl = $ret->fields['idintranet'] ? $ret->fields['idintranet'] : $ret->fields['idperseus'];
                    $lbl = $enrollmentLbl." - " .$ret->fields['name'];

                    $select .= "<option value='{$ret->fields['idperson_profile']}'>$lbl</option>";
                    $ret->MoveNext();
                }

                break;
            case 3:
                $ret = $this->dbProfile->getParentData();
                $select = '';

                while(!$ret->EOF) {
                    $lblID = ($ret->fields['id_card'] && !$ret->fields['cpf']) ? $ret->fields['id_card'] : $ret->fields['cpf'];
                    $lblID = $lblID == " " ? $ret->fields['idperseus'] : $lblID;
                    $lbl = $lblID." - " .$ret->fields['name'];

                    $select .= "<option value='{$ret->fields['idperson_profile']}'>$lbl</option>";
                    $ret->MoveNext();
                }
                
                break;
            case 4:
            case 5:
            case 10:
                $ret = $this->dbMac->getExternalUsers();
                $select = '';

                while(!$ret->EOF) {
                    $select .= "<option value='{$ret->fields['idexternal_user']}'>{$ret->fields['name']}</option>";
                    $ret->MoveNext();
                }

                break;
            default:
                $ret = $this->dbEmployee->getITMEmployeeData('','ORDER BY nome');
                $select = '';

                while(!$ret->EOF) {
                    $select .= "<option value='{$ret->fields['cpf']}'>{$ret->fields['nome']}</option>";
                    $ret->MoveNext();
                }
                
                break;

        }
        
        

        return $select;
    }

    public function hostNameVerification() {
        $type_query = $_POST['type_query'];
        $host_name = $_POST['hostName'];

        if($type_query == 'h'){
            $host_name = str_replace(" ","_",trim($host_name));
            $where =  "name = '$host_name'";
        }else{$where =  "mac = '$host_name'";}

        $where .= isset($_POST['idhost']) ? " AND idhost != {$_POST['idhost']}" : "";

        $ret = $this->dbMac->getHostVerification($where);
        if (!$ret) {
            if($this->log)
                $this->logIt('Host name verification  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($ret->RecordCount() > 0){echo 'false';}
        else{echo 'true';}

    }

    public function hostMacVerification() {
        $mac_number = $_POST['macNumber'];
        $where = "mac = '$mac_number'";
        
        if(!filter_var($mac_number, FILTER_VALIDATE_MAC)){
            echo json_encode($this->getLanguageWord('itm_invalid_msg'));
            return;
        }

        $where .= isset($_POST['idhost']) ? " AND idhost != {$_POST['idhost']}" : "";

        $ret = $this->dbMac->getHostVerification($where);

        if(!$ret){
            if($this->log)
                $this->logIt('Host MAC Address verification  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        if($ret->RecordCount() > 0){echo json_encode($this->getLanguageWord('itm_exists_mac') .$ret->fields['name']);}
        else{echo json_encode(true);}

    }

    /**
     * Assign IP address.
     * @param int $cmbType - Host type ID
     * @access public
     * @return $ipRet
     */
    public function ipAssign($cmbType) {
        $ipReserved = array(0,1,255);
        
        $field = 'ip_aton';
        $table = 'itm_tbhost';
        $orderF = 'ORDER BY ip_aton';


        if($cmbType == 6){ //range 172.16.5.1
            $ipfirst = 2886731010;
            $iplast = 2886731262;
            $where = "(idnetusertype = $cmbType)";
        }elseif($cmbType == 7){//range 172.16.4.1
            $ipfirst = 2886730754;
            $iplast = 2886731006;
            $where = "(idnetusertype = $cmbType)";
        }elseif($cmbType == 9){//range 172.16.255.1 Clients without DHCP
            $ipfirst = 2886795009;
            $iplast = 2886795262;
            $where = "(idnetusertype = $cmbType)";
        }else{//range from 172.16.6.1
            $ipfirst = 2886731266;
            $where = "(ip NOT LIKE '%.1.%' AND ip NOT LIKE '%.2.%' AND ip NOT LIKE '%.3.%' AND ip NOT LIKE '%.4.%' AND ip NOT LIKE '%.5.%')";
            $order = 'ORDER BY ip_aton DESC LIMIT 0, 1';

            $rsip = $this->dbMac->getHostData($field,$table,$where,$order);
            $iplast = $rsip->fields['ip_aton'];

            $ip_tmp = explode('.',long2ip($iplast));

            if($ip_tmp[2] != 255){
                if($ip_tmp[3] == 254){
                    $iplast = $iplast + 4;
                }else{
                    $iplast = $iplast + 1;
                }
            }

        }

        $rsiparr = $this->dbMac->getHostData($field,$table,$where,$orderF);
        $iparray = array();

        while(!$rsiparr->EOF){
            array_push($iparray,$rsiparr->fields['ip_aton']);
            $rsiparr->MoveNext();
        }

        //Assign IP number to host
        for($i=$ipfirst;$i<=$iplast;$i++){
            $tmp = explode('.',long2ip($i));
            if(in_array($tmp[3],$ipReserved)) continue;

            if(!in_array($i,$iparray)){$ipRet = $i; break;}

        }

        return $ipRet;

    }

    public function comboNetUsers($idtypehost)
    {
        switch($idtypehost){
            case 1:
                $ret = $this->dbProfile->getStudentData();

                while(!$ret->EOF) {
                    $enrollmentLbl = $ret->fields['idintranet'] ? $ret->fields['idintranet'] : $ret->fields['idperseus'];
                    $lbl = $enrollmentLbl." - " .$ret->fields['name'];

                    $fieldsID[] = $ret->fields['idperson_profile'];
                    $values[]   = $lbl;

                    $ret->MoveNext();
                }

                break;
            case 3:
                $ret = $this->dbProfile->getParentData();

                while(!$ret->EOF) {
                    $lblID = ($ret->fields['id_card'] && !$ret->fields['cpf']) ? $ret->fields['id_card'] : $ret->fields['cpf'];
                    $lblID = $lblID == " " ? $ret->fields['idperseus'] : $lblID;
                    $lbl = $lblID." - " .$ret->fields['name'];

                    $fieldsID[] = $ret->fields['idperson_profile'];
                    $values[]   = $lbl;

                    $ret->MoveNext();
                }

                break;
            case 4:
            case 5:
            case 10:
                $ret = $this->dbMac->getExternalUsers('','ORDER BY `name`');

                while(!$ret->EOF) {
                    $fieldsID[] = $ret->fields['idexternal_user'];
                    $values[]   = $ret->fields['name'];

                    $ret->MoveNext();
                }

                break;
            default:
                $ret = $this->dbEmployee->getITMEmployeeData('','ORDER BY nome');

                while(!$ret->EOF) {
                    $fieldsID[] = $ret->fields['cpf'];
                    $values[]   = $ret->fields['nome'];

                    $ret->MoveNext();
                }

                break;

        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function changeStatusMac()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $id = $_POST['idhost'];
        $newStatus = $_POST['newstatus'];

        $fields = "mac,idnetusertype";
        $rs = $this->dbMac->getHostData($fields,'itm_tbhost',"idhost = $id");
        if (!$rs) {
            if($this->log)
                $this->logIt('Get Host data - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $mac = $rs->fields['mac'];
        $idnetusertype = $rs->fields['idnetusertype'];

        $this->dbMac->BeginTrans();

        $ipFinal = $newStatus == 'A' ? $this->ipAssign($idnetusertype) : '';
        $ipFinal2 = $newStatus == 'A' ? long2ip($ipFinal) : '';
        $fieldCheck = $newStatus == 'A' ? "value = 'Accept'" : "value = 'Reject'";
        $fieldReply = $newStatus == 'A' ? "value = '$ipFinal2'" : "value = ''";


        $ret = $this->dbMac->setStatusHost($ipFinal2,$ipFinal,$newStatus,$id);

        if (!$ret) {
            $this->dbMac->RollbackTrans();
            if($this->log)
                $this->logIt('Change Host Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $deaRadCheck = $this->dbMac->deaRad($fieldCheck,'itm_tbcheck',"username = '$mac'");
        if(!$deaRadCheck){
            $this->dbMac->RollbackTrans();
            if($this->log)
                $this->logIt('Change Host Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $deaRadReply = $this->dbMac->deaRad($fieldReply,'itm_tbreply',"username = '$mac' AND attribute = 'Framed-IP-Address'");
        if(!$deaRadReply){
            $this->dbMac->RollbackTrans();
            if($this->log)
                $this->logIt('Change Host Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idmac" => $id,
            "status" => 'OK',
            "macstatus" => $newStatus
        );

        $this->dbMac->CommitTrans();

        echo json_encode($aRet);

    }

    function insertExternalUser()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $charSearch = array("(",")","-"," ",".");
        $charReplace = array("","","","","");

        $nameExternal    = addslashes($_POST['nameExternal']);
        $cpfExternal      = str_replace($charSearch,$charReplace,trim($_POST['cpfExternal']));
        $cardIdExternal     = addslashes($_POST['cardIdExternal']);


        $this->dbMac->BeginTrans();

        $ins = $this->dbMac->insertExternalUser($nameExternal,$cpfExternal,$cardIdExternal);

        if (!$ins) {
            $this->dbMac->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Mac Address  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbMac->CommitTrans();

        $aRet = array(
            "idprofile" => $ins,
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }

    public function checkTimeEnd() {
        $dtend = $_POST['dtend'];
        $timeend = $_POST['timeend'];

        if(!$dtend || $dtend == ''){
            echo json_encode($this->getLanguageWord('itm_date_require'));
            return;
        }

        $dtuntil = strtotime(str_replace("'", "",$this->formatSaveDate($dtend))." ".$timeend);

        $dtstart = strtotime("now");
        $dtstart_fmt = date ("H:i", $dtstart);

        if($dtuntil <= $dtstart){
            echo json_encode($this->getLanguageWord('itm_invalid_time'));
        }else{
            echo json_encode(true);
        }

    }

}

?>
