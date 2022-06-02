<?php
require_once(HELPDEZK_PATH . '/app/modules/lmm/controllers/lmmCommonController.php');
   
class lmmColor extends lmmCommon {
    
    public function __construct()
     {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );
        $this->idprogram =  $this->getIdProgramByController('lmmColor');
        
        $this->loadModel('color_model');
        $this->dbColor = new color_model();

        $this->loadModel('titles_model');
        $this->dbTitles = new titles_model();

    }

    public function index()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('lmm-color-grid.tpl');
    }


    public function jsonGrid()
    {
        $this->validasessao();
        $this->protectFormInput();
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
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);
        }

        $rsCount = $this->dbColor->getColor($where);

        if (!$rsCount['success']) {
            if($this->log)
                $this->logIt("Can't get Cor. {$rsCount['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $count = $rsCount['data']->RecordCount();

        if( $count > 0 && $rows > 0) {
            $total_pages = ceil($count/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
            $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";

        $rsBrand =  $this->dbColor->getColor($where,$order,$limit);

        while (!$rsBrand['data']->EOF) {
            $status_fmt = ($rsBrand['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $padrao_fmt = ($rsBrand['data']->fields['default'] == 'Y' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsBrand['data']->fields['idcolor'],
                'color'         => $rsBrand['data']->fields['name'],                
                'status_fmt'    => $status_fmt,
                'status'        => $rsBrand['data']->fields['status'],
                'padrao_fmt'    => $padrao_fmt,
                'padrao'        => $rsBrand['data']->fields['default']

            );
            $rsBrand['data']->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    //FUNCIONALIDADES ESPECÍFICAS DESSE PROGRAMA
    public function formCreate()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenColor($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmcolor-create.tpl');
    }

    //Ver Scm update
    public function formUpdate()    
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbColor->getColor("WHERE idcolor= $lmmID");

        $this->makeScreenColor($smarty,$rsBrand['data'],'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('idcolor', $lmmID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmcolor-update.tpl');

    }


    public function viewColor()
    {
        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbColor->getColor("WHERE idcolor = $lmmID");

        $this->makeScreenColor($smarty,$rsBrand['data'],'echo');

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmcolor-view.tpl');

    }

    function makeScreenColor($objSmarty,$rs,$oper)
    {
        // --- Name ---
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_color','Informe o nome.');
            else
                $objSmarty->assign('color',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_color','Informe o Nome.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('color',$rs->fields['name']);
        }

        // --- Name ---
        if ($oper == 'update' || $oper == 'echo') {
            $objSmarty->assign('checkdefault',$rs->fields['default'] == "Y" ? "checked=checked" : "");                
        }

    }


    function createColor()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $color= trim($_POST['color']);
        $status = isset($_POST['citystatus']) ? 0 : 1;
        $default = isset($_POST['cityDefault']) ? 1 : 0;     

        $this->dbColor->BeginTrans();

        if($default==1){
            $rem = $this->dbColor->removerPadrao();

            if (!$rem['success']) {
                if($this->log)
                    $this->logIt("Can't remover Color Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbColor->RollbackTrans();
                return false;
            }

        }

        $ret = $this->dbColor->insertColor($color,$status,$default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbColor->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "brandID" => $ret['id']
        );

        $this->dbColor->CommitTrans();

        echo json_encode($aRet);

    }
       


    function updateColor()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $msg ="";
        $lmmID = $_POST['idcolor'];
        $color= trim($_POST['color']);        
        $default = isset($_POST['cityDefault']) ? 1 : 0;     

        $this->dbColor->BeginTrans();  

        if($default==1){   
            $rem = $this->dbColor->removerPadrao();             
            if (!$rem['success']) {
                if($this->log)
                    $this->logIt("Can't remove Color Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbColor->RollbackTrans();
                return false;
            }

        }


        $ret = $this->dbColor->updateColor($lmmID,$color,$default);
        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbColor->RollbackTrans();
            return false;
        }

        $color=$this->_comboColor();        
        if(sizeof($color['default'])<=0){
            $retcolor = $this->dbColor->changePadrao($color['ids'][0]);           
            if (!$retcolor['success']) {
                if($this->log)
                    $this->logIt("Can't update Color Padrao. {$retcolor['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }           
           $msg=$color['ids'][0]==$lmmID?$this->getLanguageWord('Edit_the_field_Pettern'):"";
     
        }        
    

        $aRet = array(
            "success"   => true,
            "brandID"   => $lmmID,
            "message"  =>$msg
        );

        $this->dbColor->CommitTrans();

        echo json_encode($aRet);

    }


    function deleteColor()
    {
        $this->protectFormInput();
        
        $lmmID = $_POST['idcolor_modal'];

        $this->dbColor->BeginTrans();

        $ret = $this->dbColor->deleteColor($lmmID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbColor->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID
        );

        $this->dbColor->CommitTrans();

        echo json_encode($aRet);

    }

    function padraoColor()
    {
        $lmmID = $_POST['colorID'];

        $rem = $this->dbColor->removerPadrao();

        if (!$rem['success']) {
            if($this->log)
                $this->logIt("Can't update Color Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }


        $ret = $this->dbColor->changePadrao($lmmID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Color Padrao. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "BrandID" => $lmmID
        );

        echo json_encode($aRet);

    }


    function existColor()
    {    
      
        $color = $_POST['color'];

        $where = "WHERE  name = '$color'";        
  
        $check =  $this->dbColor->getColor($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get Color. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        } else {

            echo json_encode(true);

        }

    }

    function existColorUp()
    {       
      
        $color =trim($_POST['color']);
        $where = "WHERE  name = '$color'";
        $where .= (isset($_POST['idcolor'])) ? "AND idcolor!= {$_POST['idcolor']}" : "";
  
        $check =  $this->dbColor->getColor($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get Color. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        } else {

            echo json_encode(true);

        }

    }



    function statusColor()
    {
        $lmmID = $_POST['colorID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbColor->changeStatus($lmmID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Color status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "BrandID" => $lmmID
        );

        echo json_encode($aRet);

    }

    function checksColor(){

        $lmmID = $_POST['colorID'];

        $where = "WHERE  a.idcolor = $lmmID ";

        $check =  $this->dbTitles->getTitles($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get titles. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $where1 = "WHERE  idcolor = $lmmID AND `default` = 'Y'";

        $check1 =  $this->dbColor->getColor($where1);        
        if (!$check1['success']) {
            if($this->log)
                $this->logIt("Can't get color. {$check1['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode(array('success'=>true,'message'=>$this->getLanguageWord('Alert_delete_field')));

        }elseif ($check1['data']->RecordCount() > 0) {
            
            echo json_encode(array('success'=>true,'message'=>$this->getLanguageWord('Alert_delete_field_pettern')));

        }else {

            echo json_encode(array('success'=>false,'message'));

        }
    }
    

}