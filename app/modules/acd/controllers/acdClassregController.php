<?php
require_once(HELPDEZK_PATH . '/app/modules/acd/controllers/acdCommonController.php');

class acdClassreg extends acdCommon {
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
        $this->idprogram =  $this->getIdProgramByController('acdClassreg');
        
        $this->loadModel('acdclass_model');
        $this->dbClass = new acdclass_model();

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
        $this->_makeNavAcd($smarty);

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('acd-classreg-grid.tpl');


    }

    public function __comboSerie($idcourse,$type=null)
    {
        if($idcourse != 'X') $where = "WHERE idcurso = $idcourse";

        if($idcourse == 1){
            $where .= ($type && $type == 'ind') ? " AND numero IN (5,6,7,8,9)" : '';
        }

        $ret = $this->dbIndicador->getSerie($where);

        /*$fieldsID[] = "X";
        $values[]   = "Todas";*/

        while(!$ret->EOF){
            $fieldsID[] = ($type && $type == 'ind') ? $ret->fields['numero'] : $ret->fields['idserie'];
            $values[]   = $ret->fields['descricao'];
            $ret->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    /**
     * jsonGrid
     * This method is part of the grid construction and modification cycle
     * Receives data from the front, from the jqGrid object, communicates with the model, and returns new data
     *
     * @return json returns data to jqGrid object
     */
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
            $sidx ='course, serie, nome';
        if(!$sord)
            $sord ='ASC';
        
        //Na pesquisa de dados
        if ($_POST['_search'] == 'true'){

            switch($_POST['searchField']){

                //Se o campo procurado for "course", seu nome é alterado para b.descricao
                case "course":
                    $search_field = "b.descricao";
                    $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$search_field,$_POST['searchString']);
                break;

                //Se o campo procurado for "serie", seu nome é alterado para c.descricao
                case "serie":
                    $search_field = "c.descricao";
                    $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$search_field,$_POST['searchString']);
                break;
                
                //Se o campo procurado for "name_abrev", seu nome é alterado para a.abrev
                case "name_abrev":
                    $search_field = "a.abrev";
                    $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$search_field,$_POST['searchString']);
                break;
                
                //Se o campo procurado
                default:  
                $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);
                break;
            }  
        }

        $rsCount = $this->dbClass->getGrid($where);
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

        $rsClass =  $this->dbClass->getGrid($where,$order,$limit);

        while (!$rsClass['data']->EOF) {
            $status_fmt = ($rsClass['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'idcurso'   => $rsClass['data']->fields['idcurso'],
                'idserie'   => $rsClass['data']->fields['idserie'],
                'id'   => $rsClass['data']->fields['idturma'],
                'course'     => $rsClass['data']->fields['course'],
                'serie'     => $rsClass['data']->fields['serie'],
                'nome'  => $rsClass['data']->fields['nome'],
                'name_abrev' => $rsClass['data']->fields['name_abrev'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsClass['data']->fields['status']    
            );
            $rsClass['data']->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    /**
     * formCreate
     * This method is to build the registration form
     * Therefore, it is called, via Ajax, from the click event on the "new" button
     * 
     * @return void
     */
    public function formCreate(){

        $smarty = $this->retornaSmarty();

        $this->makeScreenClassreg($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('acdclassreg-create.tpl');

    }
   
    /**
     * formUpdate
     * This method is to build the update form
     * Therefore, it is called, via Ajax, from the click event on the "edit" button
     * 
     * @return void
     */
    public function formUpdate(){

        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        //Recebe do evento btnUpdate.click, no grid
        $classID = $this->getParam('classID');
        $rsClass = $this->dbClass->getGrid("AND a.idturma = $classID");
        
        $this->makeScreenClassreg($smarty,$rsClass['data'],'update');

        $smarty->assign('token', $token);

        $smarty->assign('hidden_classID', $classID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('acdclassreg-update.tpl');

    }
    
    /**
     * viewClassreg
     * This method is to build the view form
     * Therefore, it is called, via Ajax, from the click event on the "view" button
     *
     * @return void
     */
    public function viewClassreg(){

        $smarty = $this->retornaSmarty();

        //Recebe do evento btnEcho.click, no grid
        $classID = $this->getParam('classID');

        $rsClass = $this->dbClass->getGrid("AND a.idturma = $classID"); 

        $this->makeScreenClassreg($smarty,$rsClass['data'],'echo'); 

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('acdclassreg-view.tpl');

    }
    
    /**
     * makeScreenClassreg
     * This method is to build any form
     * It is called from the previous methods - formCreate, formUpdate and viewClassreg
     *
     * @param object $objSmarty to access smarty class methods
     * @param array $rs to use the data searched in the database, if any, assigning them to smarty variables
     * @param string $oper to define the type of form in question - whether it is registration, update or view
     * 
     * @return void
     */
    function makeScreenClassreg($objSmarty,$rs,$oper)
    {

        // --- Name e abreviação
        if ($oper == 'update') {

            //The course combo item is pre-selected when the update form is loaded
            $objSmarty->assign('idcourse',$rs->fields['idcurso']);

            //The same should happen with the serial combo
            //But for that, your values ​​related to the selected course must be loaded
            $cSeries = $this->_comboSerie($rs->fields['idcurso']);
            $objSmarty->assign('serieIds', $cSeries['ids']);
            $objSmarty->assign('serieVals', $cSeries['values']);

            //The combo series has been loaded, and now the item is automatically selected
            //The selected series is the one whose id is equal to the id of the series of the record selected in the grid
            $objSmarty->assign('idserie',$rs->fields['idserie']);

            //If the name field is empty
            if (empty($rs->fields['nome']))
                //smarty plh_name gets "Enter class name"
                $objSmarty->assign('plh_name','Informe o nome da turma');
            else
                $objSmarty->assign('class_name',$rs->fields['nome']);

            //If the abrev_name field is empty
            if (empty($rs->fields['name_abrev']))
                //smarty plh_abrev gets "Short class name"
                $objSmarty->assign('plh_abrev','Nome abreviado da turma');
            else
                $objSmarty->assign('class_abrev',$rs->fields['name_abrev']);

            //Assign of the "course" combo
            $courses = $this->_comboCourse();
            $objSmarty->assign('courseIds', $courses['ids']);
            $objSmarty->assign('courseVals',$courses['values']);

        } elseif ($oper == 'create') {
            
            //Se o campo curso estiver vazio
            if (empty($rs->fields['course']))
                //smarty plh_course recebe "Informe o curso da turma"
                $objSmarty->assign('plh_course','Informe o curso da turma');
            else
                $objSmarty->assign('idcourse',$rs->fields['course']);

            //Se o campo serie estiver vazio
            if (empty($rs->fields['serie']))
                //smarty plh_serie recebe "Informe a série da turma"
                $objSmarty->assign('plh_serie','Informe a série da turma');
            else
                $objSmarty->assign('idserie',$rs->fields['serie']);;

            //Se o campo nome estiver vazio
            if (empty($rs->fields['nome']))
                //smarty plh_name recebe "Informe o nome da turma"
                $objSmarty->assign('plh_name','Informe o nome da turma');
            else
                $objSmarty->assign('class_name',$rs->fields['nome']);

            //Se o campo nome_abrev estiver vazio
            if (empty($rs->fields['name_abrev']))
                //smarty plh_abrev recebe "Nome abreviado da turma"
                $objSmarty->assign('plh_abrev','Nome abreviado da turma');
            else
                $objSmarty->assign('class_abrev',$rs->fields['name_abrev']);

            //Assign do combo "curso"
            $courses = $this->_comboCourse();
            $objSmarty->assign('courseIds', $courses['ids']);
            $objSmarty->assign('courseVals', $courses['values']);

        } elseif ($oper == 'echo') {
            $objSmarty->assign('classCourse',$rs->fields['course']);
            $objSmarty->assign('classSerie',$rs->fields['serie']);
            $objSmarty->assign('className',$rs->fields['nome']);
            $objSmarty->assign('classAbrev',$rs->fields['name_abrev']);
        }

    }
    
    /**
     * createClass
     * This method performs the insertion of data communicating the model, that is, the execution of the record
     * It is called, then, when the register button of the created registration form is clicked
     *
     * @return false|json Returns false if the insert fails, or an array with the message "success"
     */
    function createClass()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        //$course = $_POST['cmbClassCourse'];
        $serie = strip_tags($_POST['cmbClassSerie']);
        $class_name = strip_tags(trim($_POST['className']));
        $name_abrev = strip_tags(trim($_POST['classNameAbrev']));

        $this->dbClass->BeginTrans();

        $ret = $this->dbClass->insertClass($class_name,$name_abrev,$serie);

        if (!$ret) {
            if($this->log)
                $this->logIt("Can't insert Class data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbClass->RollbackTrans();
            return false;
        }

    
        $aRet = array(
            "success" => true
        );

        $this->dbClass->CommitTrans();

        echo json_encode($aRet);

    }
    
    /**
     * updateClass
     * This method performs the update of data communicating the model, that is, the execution of the update
     * It is called, then, when the edit button of the created registration form is clicked
     *
     * @return false|json
     */
    function updateClass()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $classID = strip_tags($_POST['classID']);
        $serie = strip_tags($_POST['cmbClassSerie']);
        $class_name = strip_tags(trim($_POST['className']));
        $name_abrev = strip_tags(trim($_POST['classNameAbrev']));

        //echo "ID Turma: $classID, nome da turma: $class_name, nome abreviado: $name_abrev, serie: $serie"; die();

        $this->dbClass->BeginTrans();

        $ret = $this->dbClass->updateClass($classID,$class_name,$name_abrev,$serie);

        if (!$ret) {
            if($this->log)
                $this->logIt("Can't insert Class data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbClass->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "classID" => $classID
        );

        $this->dbClass->CommitTrans();

        echo json_encode($aRet);

    }

    /**
     * ajaxcomboSerie
     * This method builds the series combo options, which varies depending on the course combo choice
     *
     * @return string is inserted into the combo from jquery's .html() method
     */
    function ajaxcomboSerie(){

        $courseID = $_POST['courseID'];

        //Combo "serie"
        $series = $this->__comboSerie($courseID);

        $num_options = count($series['ids']);

        for($cont = 0; $cont < $num_options; $cont ++){

            $op_list .= "<option value='{$series['ids'][$cont]}' >{$series['values'][$cont]}</option>";
        }

        echo $op_list;

    }

    public function statusClass(){

        $classID = $_POST['classID'];
        $newStatus = $_POST['newStatus'];

        $ret = $this->dbClass->statusClass($classID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Class status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "areaID" => $classID
        );

        echo json_encode($aRet);
    }
    
    /**
     * existClass
     * This method is to check if the value that will be entered in the database, via registration or editing, already exists
     * If it already exists, it returns a message, and it appears below the combo, if not, it returns only true
     *
     * @return json 
     */
    public function existClass() {
        $this->protectFormInput();

        //This data is necessary in the two cases, create or update
        $serieID = $_POST['SerieID'];
        $className = $_POST['className'];
        $classNameAbrev = $_POST['classNameAbrev'];

        //This clause search for records where the class name already exists, along with the choosed grade
        if(isset($_POST['className']) && isset($_POST['SerieID'])){

            $where = "AND a.nome = '$className' AND c.idserie = $serieID ";

            $msg = "acd_exists";

        }else if(isset($_POST['classNameAbrev'])){

            $where = "AND a.abrev = '$classNameAbrev' ";

            $msg = "acd_abbrevexists";

        }
        
        //Name and grade can not exists in a record whose ID is different from the current one
        //The user can not modify the data of the current record, and in this case, even if it already exists, the name-serial combination must be accepted
        $where .= isset($_POST['classID']) ? "AND a.idturma <> {$_POST['classID']}" : "";
        
        $check = $this->dbClass->getGrid($where);

        if (!$check) {
            if($this->log)
                $this->logIt("Can't insert Class data. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbClass->RollbackTrans();
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            //Send message that it already exists
            echo json_encode($this->getLanguageWord($msg));
        } else {
            //Is sent true if it does not exist
            echo json_encode(true);
        }
    }
}
