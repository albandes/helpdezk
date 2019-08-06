<?php

require_once(HELPDEZK_PATH . '/app/modules/admin/controllers/admCommonController.php');

class Modules extends admCommon
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

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);

        $this->loadModel('modules_model');
        $dbModule = new modules_model();
        $this->dbModule = $dbModule;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $smarty->assign('token', $this->_makeToken()) ;

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('modules.tpl');

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
            $sidx ='holiday_date';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'name') $searchField = 'name';
            if ( $_POST['searchField'] == 'status') $searchField = 'status';

            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->dbModule->countModule($where);

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

        $rsModules = $this->dbModule->selectModule($where,$order,$limit);
        
        while (!$rsModules->EOF) {
            
            $status_fmt = ($rsModules->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            
            $aColumns[] = array(
                'id'        => $rsModules->fields['idmodule'],
                'name'      => utf8_decode($rsModules->fields['name']),
                'status'    => $status_fmt,
                'statusval' => $rsModules->fields['status']

            );
            $rsModules->MoveNext();
        }


        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count->fields['total'],
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateModule()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenModules($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        
        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('modules-create.tpl');
    }

    public function formUpdateModule()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idmodule = $this->getParam('idmodule');
        
        $rsModule = $this->dbModule->selectModuleData($idmodule);

        $this->makeScreenModules($smarty,$rsModule,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idmodule', $idmodule);

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('modules-update.tpl');

    }

    function makeScreenModules($objSmarty,$rs,$oper)
    {
        // --- Module description ---
        $plh_msg = $this->getLanguageWord('plh_module_description');
        
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_module_description',$plh_msg);
            else
                $objSmarty->assign('module_description',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_module_description', $plh_msg);            
        } elseif ($oper == 'echo') {
            $objSmarty->assign('module_description',$rs->fields['name']);
        }

         // --- Module path ---
         $plh_path_msg = $this->getLanguageWord('plh_module_path');
        
         if ($oper == 'update') {
             if (empty($rs->fields['path']))
                 $objSmarty->assign('plh_module_path',$plh_path_msg);
             else
                 $objSmarty->assign('module_path',$rs->fields['path']);
         } elseif ($oper == 'create') {
             $objSmarty->assign('plh_module_path', $plh_path_msg);            
         } elseif ($oper == 'echo') {
             $objSmarty->assign('module_path',$rs->fields['path']);
         }

        // --- Smarty variable ---
        $plh_smarty_msg = $this->getLanguageWord('plh_smarty_variable');
        
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_module_smartyvar',$plh_smarty_msg);
            else
                $objSmarty->assign('module_smartyvar',$rs->fields['smarty']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_module_smartyvar', $plh_smarty_msg);            
        } elseif ($oper == 'echo') {
            $objSmarty->assign('module_smartyvar',$rs->fields['smarty']);
        }
        
        // --- Table prefix ---
        $plh_prefix_msg = $this->getLanguageWord('plh_module_prefix');
        
        if ($oper == 'update') {
            if (empty($rs->fields['tableprefix']))
                $objSmarty->assign('plh_module_prefix',$plh_prefix_msg);
            else
                $objSmarty->assign('module_prefix',$rs->fields['tableprefix']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_module_prefix', $plh_prefix_msg);            
        } elseif ($oper == 'echo') {
            $objSmarty->assign('module_prefix',$rs->fields['tableprefix']);
        }

        // --- Default module ---
        if ($oper == 'update') {
            if (empty($rs->fields['defaultmodule']))
                $objSmarty->assign('checkedval','');
            else
                $objSmarty->assign('checkedval','checked="checked"');
        } elseif ($oper == 'create') {
            $objSmarty->assign('checkedval','');            
        } elseif ($oper == 'echo') {
            if (empty($rs->fields['defaultmodule']))
                $objSmarty->assign('checkedval','');
            else
                $objSmarty->assign('checkedval','checked="checked"');
        }

    }

    function createModule()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }        
        
        $MODNAME = addslashes($_POST['txtName']);
        $MODPATH = addslashes($_POST['txtPath']);
        $MODPREFIX = addslashes($_POST['txtPrefix']);
        $MODSMARTY = addslashes($_POST['txtSmartyVar']);
        $MODDEFAULT = isset($_POST['module-default']) ? "YES" : NULL;

        $this->dbModule->BeginTrans();

        $check = $this->dbModule->checkName($MODNAME);
        if(!$check){
            if($this->log)
                $this->logIt('Check if module exists - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check->fields['idmodule']) {
            return false;
        } else {
            if($MODDEFAULT == 'YES'){
                $del = $this->dbModule->removeDefault();

                if(!$del){
                    $this->dbModule->RollbackTrans();
                    if($this->log)
                        $this->logIt('Remove Default Module - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
            }

            $ret = $this->dbModule->insertModule($MODNAME,$MODPATH,$MODSMARTY,$MODPREFIX,$MODDEFAULT);

            if(!$ret){
                $this->dbModule->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Module - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
            
            $retTbConf = $this->dbModule->createConfigTables($MODPREFIX);

            if(!$retTbConf){
                $this->dbModule->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Module - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
            
            $this->dbModule->CommitTrans();
            
            $id_module = $this->dbHoliday->TableMaxID('tbmodule','idmodule');
            
            $aRet = array(
                "idmodule" => $id_module,
                "description" => $MODNAME
            );
        }

        echo json_encode($aRet);

    }

    function updateModule()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idmodule = $_POST['idmodule'];
        $MODNAME = addslashes($_POST['txtName']);
        $MODPATH = addslashes($_POST['txtPath']);
        $MODPREFIX = addslashes($_POST['txtPrefix']);
        $MODSMARTY = addslashes($_POST['txtSmartyVar']);
        $MODDEFAULT = isset($_POST['module-default']) ? "YES" : NULL;

        $this->dbModule->BeginTrans();

        $check = $this->dbModule->checkName($MODNAME);
        if(!$check){
            if($this->log)
                $this->logIt('Check if module exists - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check->fields['idmodule'] && $check->fields['idmodule'] != $idmodule) {
            return false;
        } else {
            if($MODDEFAULT == 'YES'){
                $del = $this->dbModule->removeDefault();

                if(!$del){
                    $this->dbModule->RollbackTrans();
                    if($this->log)
                        $this->logIt('Remove Default Module - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
            }

            $ret = $this->dbModule->updateModule( $idmodule,$MODNAME,$MODPATH,$MODSMARTY,$MODDEFAULT);
            if(!$ret){
                $this->dbModule->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Module - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $this->dbModule->CommitTrans();
            
            $aRet = array(
                "idmodule" => $idmodule,
                "status"   => 'OK'
            );
            
        }

        echo json_encode($aRet);

    }

    function statusModule()
    {
        $idModule = $this->getParam('idmodule');
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbModule->changeModuleStatus($idModule,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Person Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idmodule" => $idModule,
            "status" => 'OK',
            "modulestatus" => $newStatus
        );

        echo json_encode($aRet);

    }

    function saveLogo()
    {
        //echo "aqui";
        $char_search	= array("ã", "á", "à", "â", "é", "ê", "í", "õ", "ó", "ô", "ú", "ü", "ç", "ñ", "Ã", "Á", "À", "Â", "É", "Ê", "Í", "Õ", "Ó", "Ô", "Ú", "Ü", "Ç", "Ñ", "ª", "º", " ", ";", ",");
		$char_replace	= array("a", "a", "a", "a", "e", "e", "i", "o", "o", "o", "u", "u", "c", "n", "A", "A", "A", "A", "E", "E", "I", "O", "O", "O", "U", "U", "C", "N", "_", "_", "_", "_", "_");

        $idmodule = $_POST['idmodule'];
        $this->logIt('Insert Module  - User: '.$idmodule.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        if (!empty($_FILES)) {

            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");
            $targetPath = $this->helpdezkPath . '/app/uploads/logos/' ;
            $fileName = str_replace($char_search, $char_replace, $fileName);
            
            $targetFile = $targetPath . $fileName;
            
            if(!is_dir($targetPath)) {
                mkdir ($targetPath, 0777 ); // criar o diretorio
            }

            if (move_uploaded_file($tempFile,$targetFile)){
                if($this->log){
                    $this->logIt("Save module logo: # ". $idmodule . ' - File: '.$targetFile.' - program: '.$this->program ,7,'general',__LINE__);
                }
            }else {
                if($this->log){
                    $this->logIt("Can't save module logo: # ". $idmodule . ' - File: '.$targetFile.' - program: '.$this->program ,3,'general',__LINE__);
                }
                return false;
            }

            $setCond = "headerlogo = '$fileName'";
            $ret = $this->dbModule->updateModule($idmodule,$setCond);
            if (!$ret) {
                if($this->log)
                    $this->logIt('Update module logo  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        echo $fileName;

    }

    function loadImage()
    {
        $idmodule = $_POST['idmodule'];
        $ret = $this->dbModule->selectModuleData($idmodule);

        if (!$ret) {
            if($this->log)
                $this->logIt('Module logo - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
       
        $targetPath = $this->helpdezkPath . '/app/uploads/logos/' ;
        $resultimagens = [];
        if($ret->fields['headerlogo'] != ''){
            $size = filesize($targetPath.$ret->fields['headerlogo']);
            $resultimagens[] = array(
                'filename'      => $ret->fields['headerlogo'],
                'idmodule'     => $idmodule,
                'size'          => $size
            );
        }

        echo json_encode($resultimagens);
    }

    function removeLogo()
    {
        $idmodule = $_POST['idmodule'];
        $filename = $_POST['filename'];

        $setCond = "headerlogo = ''";
        $ret = $this->dbModule->updateModule($idmodule,$setCond);
        if (!$ret) {
            if($this->log)
                $this->logIt('Update module logo  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $targetPath = $this->helpdezkPath . '/app/uploads/logos/' ;
        unlink($targetPath.$filename);

        $aRet = array(
            "status" => 'OK',
        );
        echo json_encode($aRet);
    }

    function deleteModule()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idmodule = $_POST['idmodule'];

        $this->dbModule->BeginTrans();

        $rs = $this->dbModule->selectModuleData($idmodule);
        if(!$rs){
            if($this->log)
                $this->logIt('Get Module\'s data  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $del = $this->dbModule->deleteConfigTables($rs->fields['tableprefix']);
        if(!$del){
            $this->dbModule->RollbackTrans();
            if($this->log)
                $this->logIt('Delete Module\'s Config tables  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $ret = $this->dbModule->deleteModule("idmodule = $idmodule");
        if(!$ret){
            $this->dbModule->RollbackTrans();
            if($this->log)
                $this->logIt("Delete Module ID: {$idmodule} - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbModule->CommitTrans();

        $aRet = array(
            "idmodule" => $idmodule,
            "status"   => 'OK'
        );

        echo json_encode($aRet);

    }

}