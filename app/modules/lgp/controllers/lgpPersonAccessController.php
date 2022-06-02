<?php
require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpPersonAccess extends lgpCommon {
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
        $this->idprogram =  $this->getIdProgramByController('lgpPersonAccess');
        
        $this->loadModel('lgppersonac_model');
        $this->dbPersonac = new lgppersonac_model();

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
        $this->_makeNavlgp($smarty);

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->assign('token', $this->_makeToken());

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('lgp-personac-grid.tpl');


    }

    public function jsonGrid()
    {
        $this->validasessao();
        $this->protectFormInput();
        $rsTypePerson = $this->dbPersonac->getTypePersonName("LGP_personhasaccess");
        $typePersonID = $rsTypePerson['data'][0]['idtypeperson'];
        $where = "WHERE a.idtypeperson = {$typePersonID} ";

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='nome';
        if(!$sord)
            $sord ='ASC';
        
        if ($_POST['_search'] == 'true'){

            if($_POST['searchField'] == "personac_name"){

                $search_field = "a.name";

                $where .= 'AND ' . $this->getJqGridOperation($_POST['searchOper'],$search_field,$_POST['searchString']);

            }else if($_POST['searchField'] == "personac_cpf"){

                $search_field = "b.ssn_cpf";

                $where .= 'AND ' . $this->getJqGridOperation($_POST['searchOper'],$search_field,$_POST['searchString']);

            }

        }

        $rsCount = $this->dbPersonac->getPersonac($where);
        $count = count($rsCount['data']);

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

        $rsPersonac =  $this->dbPersonac->getPersonac($where, $order,$limit);

        if (!$rsPersonac['success']) {
            if($this->log)
                $this->logIt("Can't get Personac data. {$rsPersonac['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        //Variable "row" takes the line, and "record" takes the record itself - from the line
        foreach($rsPersonac['data'] as $row => $record){

            $status_fmt = ($record['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            
            //Each position of this array is like a row with fields
            $aColumns[$row] = array(
                'id'                => $record['idperson'],
                'idtypeperson'          => $record['idtypeperson'],
                'personac_name'          => $record['personac_name'],
                'personac_cpf'          => $record['personac_cpf'],
                'personac_telephone'          => $record['personac_telephone'],
                'personac_cellphone'          => $record['personac_cellphone'],
                'status_fmt'    => $status_fmt,
                'status'        => $record['status']

            );
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreate()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenPersonac($smarty,'','create');

        $smarty->assign('token', $this->_makeToken());
        $typePerson = $this->dbPersonac->getTypePersonName("LGP_personhasaccess");

        $smarty->assign('typepersonid', $typePerson['data'][0]['idtypeperson']);
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lgppersonac-create.tpl');
    }

    public function formUpdate()
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $personacID = $this->getParam('personacID');
        $rsPersonac = $this->dbPersonac->getPersonac("WHERE a.idperson = $personacID"); 

        $this->makeScreenPersonac($smarty,$rsPersonac['data'][0],'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_personacID', $personacID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lgppersonac-update.tpl');

    }

    public function viewPersonac()
    {
        $smarty = $this->retornaSmarty();

        $personacID = $this->getParam('personacID');
        $rsPersonac = $this->dbPersonac->getPersonac("WHERE a.idperson = $personacID");

        $this->makeScreenPersonac($smarty,$rsPersonac['data'][0],'echo');

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lgppersonac-view.tpl');

    }


    function makeScreenPersonac($objSmarty,$rs,$oper)
    {
        
        // --- Name ---
        if ($oper == 'update') {

            if (empty($rs['personac_name'])){
                
                $objSmarty->assign('plh_nome',$this->getLanguageWord("infor_nome"));

            }else{

                $objSmarty->assign('personacName',$rs['personac_name']);

            }

            if (empty($rs['personac_cpf'])){
                
                $objSmarty->assign('plh_cpf',$this->getLanguageWord("Placeholder_cpf"));

            }else{

                $objSmarty->assign('personacCPF',$rs['personac_cpf']);

            }

            if (empty($rs['personac_telephone'])){
                
                $objSmarty->assign('plh_telephone',$this->getLanguageWord("Placeholder_phone"));

            }else{

                $objSmarty->assign('personacTelephone',$rs['personac_telephone']);

            }

            if (empty($rs['personac_cellphone'])){
                
                $objSmarty->assign('plh_cellphone',$this->getLanguageWord("Placeholder_cellphone"));

            }else{

                $objSmarty->assign('personacCPhone',$rs['personac_cellphone']);

            }

        } elseif ($oper == 'create') {

            if (empty($rs['personac_name'])){
                
                $objSmarty->assign('plh_nome',$this->getLanguageWord("infor_nome"));

            }else{

                $objSmarty->assign('personacName',$rs['personac_name']);

            }

            if (empty($rs['personac_cpf'])){
                
                $objSmarty->assign('plh_cpf',$this->getLanguageWord("Placeholder_cpf"));

            }else{

                $objSmarty->assign('personacCPF',$rs['personac_cpf']);

            }

            if (empty($rs['personac_telephone'])){
                
                $objSmarty->assign('plh_telephone',$this->getLanguageWord("Placeholder_phone"));

            }else{

                $objSmarty->assign('personacTelephone',$rs['personac_telephone']);

            }

            if (empty($rs['personac_cellphone'])){
                
                $objSmarty->assign('plh_cellphone',$this->getLanguageWord("Placeholder_cellphone"));

            }else{

                $objSmarty->assign('personacCPhone',$rs['personac_cellphone']);

            }
               

        } elseif ($oper == 'echo') {

            $objSmarty->assign('personacName',$rs['personac_name']);
            $objSmarty->assign('personacCPF',$rs['personac_cpf']);
            $objSmarty->assign('personacTelephone',$rs['personac_telephone']);
            $objSmarty->assign('personacCPhone',$rs['personac_cellphone']);

        }

    }

    function createPersonac()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $typepersonid = strip_tags($_POST['typepersonid']);
        $personacName = strip_tags($_POST['personacName']);
        $personacCPF = $this->replaceMaskChar(strip_tags($_POST['personacCPF']));
        $personacTel = $this->replaceMaskChar(strip_tags($_POST['personacTelephone']));
        $personacCell = $this->replaceMaskChar(strip_tags($_POST['personacCPhone']));

        $ret = $this->dbPersonac->insertPersonac($typepersonid, $personacName,  $personacCPF, $personacTel, $personacCell);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Personac data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;

        }

        $aRet = array(
            "success" => true,
            "personacID" => $ret['id']
        );

        echo json_encode($aRet);

    }

    function updatePersonac()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        //ARRUMAR
        $personacID = strip_tags($_POST['personacID']);
        $personacName = strip_tags($_POST['personacName']);
        $personacCPF = $this->replaceMaskChar(strip_tags($_POST['personacCPF']));
        $personacTel = $this->replaceMaskChar(strip_tags($_POST['personacTelephone']));
        $personacCell = $this->replaceMaskChar(strip_tags($_POST['personacCPhone']));

        $ret = $this->dbPersonac->updatePersonac($personacID, $personacName,  $personacCPF, $personacTel, $personacCell);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Personac data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success"   => true,
            "personacID" => $personacID
        );

        echo json_encode($aRet);

    }

    public function statusPersonac(){

        $personacID = strip_tags($_POST['personacID']);
        $newStatus = strip_tags($_POST['newStatus']);

        $ret = $this->dbPersonac->statusPersonac($personacID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Personac status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "personacID" => $personacID
        );

        echo json_encode($aRet);
    }

    //Validation
    public function existpersonac() {
        $this->protectFormInput();

        $personacName = strip_tags($_POST['personacName']);
        $personacCPF = $this->replaceMaskChar(strip_tags($_POST['personacCPF']));

        $where = "WHERE a.name = '{$personacName}' AND b.ssn_cpf = '$personacCPF'"; //echo $where;
        $where .= isset($_POST['personacID']) ? " AND a.idperson <> {$_POST['personacID']}" : "";

        $check = $this->dbPersonac->getPersonac($where);
        if (count($check['data']) > 0) {            
            echo json_encode($this->getLanguageWord("Alert_record_exist"));
        } else {
            echo json_encode(true);
        }
    }

    public function replaceMaskChar($word){

        $arr1 = array("(", ")", " ", ".", "-");

        $arr2 = array("", "", "", "", "");

        return strip_tags(str_replace($arr1, $arr2, $word));

    }

    function checkCPF()
    {
        $valuecheck = $this->replaceMaskChar(strip_tags($_POST['personacCPF']));
        $where = "WHERE b.ssn_cpf = '$valuecheck'"; //echo $where;
        $where .= isset($_POST['personacID']) ? " AND a.idperson <> {$_POST['personacID']}" : "";

        $valid = $this->validateCPF($valuecheck);

        if(!$valid){
            echo json_encode($this->getLanguageWord("Alert_invalid_cpf"));
        }else{
            $ret = $this->dbPersonac->getPersonac($where);

            if(count($ret['data']) > 0){
                echo json_encode($this->getLanguageWord("cpf_exists"));
            }else{
                echo json_encode(true);
            }
        }
    }

    function validateCPF($cpf)
    {
        $arrInvalid = array('00000000000','11111111111','22222222222','33333333333','44444444444','55555555555','66666666666','77777777777','88888888888','99999999999');

        $cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);

        if(strlen($cpf) != 11 || in_array($cpf,$arrInvalid)){ return FALSE;}
        else{
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return FALSE;
                }
            }
            return TRUE;
        }
    }

    function checkPhones()
    {

        $valuecheck = isset($_POST['personacTelephone']) ? $this->replaceMaskChar(strip_tags($_POST['personacTelephone'])) : $this->replaceMaskChar(strip_tags($_POST['personacCPhone']));
        $number_type = isset($_POST['personacTelephone']) ? "Tel" : "Cell";

        //echo "$valuecheck, | $number_type"; die();
        //echo strlen($valuecheck);

        $valid = $this->validatePhone($valuecheck, $number_type);

        if(!$valid){

            echo json_encode($this->getLanguageWord("Alert_invalid_number"));

        }else{

            echo json_encode(true);
        }
    }

    function validatePhone($number, $type)
    {

        //echo $type;

        if($type == "Tel"){
            
            $arrInvalid = array('0000000000','1111111111','2222222222','3333333333','4444444444','5555555555','6666666666','7777777777','8888888888','9999999999');

            //Se a quantidade de caracteres for menor do que 10, ou conter alguma das sequências inválidas
            if(strlen($number) != 10 || in_array($number,$arrInvalid)){ 

                return FALSE;

            }else{

                return TRUE;

            }

    
        }else if($type == "Cell"){

            $arrInvalid = array('00000000000','11111111111','22222222222','33333333333','44444444444','55555555555','66666666666','77777777777','88888888888','99999999999');

            //Se a quantidade de caracteres for menor do que 10, ou conter alguma das sequências inválidas
            if(strlen($number) != 11 || in_array($number,$arrInvalid)){ 

                return FALSE;

            }else{

                return TRUE;

            }
            


        }
            
    }


}