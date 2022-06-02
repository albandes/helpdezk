<?php 

require_once(HELPDEZK_PATH . '/app/modules/helpdezk/controllers/hdkCommonController.php');

class hdkRelSolicitacoes extends hdkCommon
{

    /**
     * Create an instance, check session time
     * Common controller construct
     * Recovery of data gathered by common
     * Instantiation of the program model
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     * 
     * @access public
     */
    public function __construct()
    {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        $this->idPerson = $_SESSION['SES_COD_USUARIO'];

        // Module to which the program belongs
        $this->modulename = 'Helpdezk' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Program log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('hdkRelSolicitacoes');

        // Model instance of this program
        $this->loadModel('relsolicitacoes_model');
        $this->dbRelSolicitacoes = new relsolicitacoes_model();

        //Instance of the "logos" class, from the admin module
        $this->loadModel('admin/logos_model');
        $dbLogo = new logos_model();
        $this->dbLogo = $dbLogo;

        /*$this->loadModel('ticket_model');
        $this->dbTicket = new ticket_model();*/

    }
  
    /**
     * index
     * smarty processing of the program's home page
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function index()
    {

        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        // Processing basic templates
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);

        // Processing of data from the logos_model class
        $reportslogo = $this->dbLogo->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);

        // Configuration of the report type filter combo
        $aRelType = $this->_comboReportType();
        $smarty->assign('reltypeids', array_keys($aRelType)); // Returns an array with the original keys
        $smarty->assign('reltypevals', array_values($aRelType)); // Returns an array with the values ​​of the original
        $smarty->assign('idreltype', '');

        // Assign the combo of the Company field // Data are retrieved from Common, directly
         $aRelCompany = $this->_comboCompanies($idCompany);
         $smarty->assign('adviserids', $aRelCompany['ids']); 
         $smarty->assign('adviservals', $aRelCompany['values']); 
         $smarty->assign('idcompany', '');

        // Assignment of the Attendants field combo // Data is retrieved from Common, directly
        // Called again, to change the list of attendants when a company is selected
         $aRelOperator = $this->_comboRepassUsers('operator');
         $smarty->assign('operatorids', $aRelOperator['ids']); 
         $smarty->assign('operatorvals', $aRelOperator['values']); 
         $smarty->assign('idattendance', '');

        // Area filter combo assignment // Data is retrieved from Common, directly
         $aRelArea = $this->_comboArea();
         $smarty->assign('areaids', $aRelArea['ids']); 
         $smarty->assign('areavals', $aRelArea['values']); 
         $smarty->assign('idarea', '');

        // Assign the combo of the Type field // Data is retrieved from Common, directly
         $aRelType = $this->_comboType($idArea);
         $smarty->assign('typeids', $aRelType['ids']); 
         $smarty->assign('typevals', $aRelType['values']); 
         $smarty->assign('idtype', '');

        // Item combination sign // Data is retrieved from Common, directly
         $aRelItem = $this->_comboItem($idType);
         $smarty->assign('itemids', $aRelItem['ids']); 
         $smarty->assign('itemvals', $aRelItem['values']); 
         $smarty->assign('iditem', '');

        // Service field combo assignment // Data is retrieved from Common, directly
         $aRelService = $this->_comboService($idItem);
         $smarty->assign('serviceids', $aRelService['ids']); 
         $smarty->assign('servicevals', $aRelService['values']); 
         $smarty->assign('idservice', '');

        // Reason field combo sign // Data is retrieved from Common, directly
        $aRelReason = $this->_comboReason($idService);
        $smarty->assign('reasonids', $aRelReason['ids']); 
        $smarty->assign('reasonvals', $aRelReason['values']); 
        $smarty->assign('idreason', '');

        // Assignment of the Attendance field combo // Data is retrieved from Common, directly
        $aRelAttendance = $this->_comboWay();
        $smarty->assign('attendanceids', $aRelAttendance['ids']); 
        $smarty->assign('attendancevals', $aRelAttendance['values']); 
        $smarty->assign('idattendance', '');

        // Assign the combo of the Period field // Data is retrieved from the created method
        $aRelTime = $this->_comboReportTime();
        $smarty->assign('timeids', array_keys($aRelTime)); 
        $smarty->assign('timevals', array_values($aRelTime)); 
        $smarty->assign('idtime', '');
 
        // Security token processing
        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Processing another template
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language

        // Processing the template
        $smarty->display('hdk-relsolicitacoes.tpl');

    }

    //   
    /**
     * Data array for the "report type" filter 
     *
     * @return array "report type" filter options
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function _comboReportType()
    {

        // getLanguageWord() search for the parameter in the vocabulary
        // The parameter is a key_name registered in the vocabulary program
        $relTypes = array(
            1=> $this->getLanguageWord('rel_resumidoempresa'), 
            2=> $this->getLanguageWord('rel_resumidoatendente'), 
            3=> $this->getLanguageWord('rel_resumidoarea'),
            4=> $this->getLanguageWord('rel_resumidotipo'),
            5=> $this->getLanguageWord('rel_resumidoitem'),
            6=> $this->getLanguageWord('rel_resumidoservico'),
            7=> $this->getLanguageWord('rel_resumidotipoatend'),
            8=> $this->getLanguageWord('rel_solicitfinal')
        );

        return $relTypes;
        
    }
        
    /**
     * _comboReportTime
     * Data array of "period" filter
     *
     * @return array "period" filter options
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function _comboReportTime()
    {

        // getLanguageWord() search for the parameter in the vocabulary
        // The parameter is a key_name registered in the vocabulary program
        $relTime = array(
            1=> $this->getLanguageWord('rel_interval_week'), 
            2=> $this->getLanguageWord('rel_interval_tweek'), 
            3=> $this->getLanguageWord('rel_interval_month'), 
            4=> $this->getLanguageWord('rel_date_choose')  
        );

        return $relTime;
        
    }

        
    /**
     * getReport
     * Receives form data, via $ _POST
     * Data is sent from the $("#btnSearch").click(function() {});
     *
     * @return json returns, for Ajax, the structure of the table body
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function getReport(){

        //echo "ok";
        
        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
   
        $relType = $_POST['cmbRelType']; // Receive the type of form
        $relPeriodo = $_POST['cmbTipoPeriodo']; // Receive the period type 

        // Definition of the start date and end date, based on the choice of the period, in the form
        if($relPeriodo == 4){
            $dtstart = str_replace("'", "", $this->formatSaveDate($_POST['dtstart']));
            $dtfinish = str_replace("'", "", $this->formatSaveDate($_POST['dtfinish']));              
        }else if($repPeriodo == 1){
            $dtstart = date("Y-m-d",strtotime("-7 days"));
            $dtfinish = date("Y-m-d");
        }else if($repPeriodo == 2){
            $dtstart = date("Y-m-d",strtotime("-15 days"));
            $dtfinish = date("Y-m-d");
        }else if($relPeriodo == 3){
            $dtstart = date("Y-m-d",strtotime("-30 days"));
            $dtfinish = date("Y-m-d");
        }

        // Array for sending the type of report and period (start and end date) to the defineTable () method
        $arrTypeTime = array(
            "tipo_rel" => $relType, 
            "periodo_rel" => array($dtstart, $dtfinish)
        );

        //echo $relType;

        // Cases for the chosen report types
        // In each, the SQL search has different specifications
        // Each return then delivers different data that will be used to form the data ouput
        switch($relType){

            case "1": // Summarized by company
                
                $relEmpresa = $_POST['cmbEmpresa'];
                
                // Search specifications
                $where = $relEmpresa != "ALL" ? "AND a.idperson = {$relEmpresa} " : "";
                $where .= "AND c.entry_date BETWEEN '$dtstart' AND '$dtfinish'";
                
                // The variable receives the return from SQL
                $ret = $this->dbRelSolicitacoes->getFormData_Rel1($where);

                if (!$ret['success']) {
                    if($this->log)
                        $this->logIt("{$ret['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }

                //Subheader
                // If it is equal to "ALL" the value will be "All", if not the name of the field brought by SQL
                $subcab = array("{$this->getLanguageWord('hdk_company')}"=>$company = $relEmpresa == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['company_name']);

                // If no data is returned for the "Company" field
                if($ret['data']->fields[0] == NULL){
                    
                    // Modal "Record not found"
                    echo json_encode(false);

                }else{

                    // Create the table using the data gathered by the research
                    $table_data = $this->defineTable($arrTypeTime, $ret['data'], $subcab);

                    // Returns, for Ajax, the final content of the concatenation performed in the defineTable() method
                    // The final product is a string of the contents of the table body
                    echo json_encode($table_data);

                }


            break;

            case "2": // Summarized by attendant
                
                $relAtendente = $_POST['cmbAtendente'];
                $relAtendenteC = $_POST['cmbEmpresa']; 

                // Search specifications
                $where =  $relAtendenteC  != "ALL" ? "AND c.idperson = {$relAtendenteC} " : "";
                $where .= $relAtendente != "ALL" ? "AND a.idperson = {$relAtendente} " : "";
                $where .= "AND d.status = 'A' AND g.ind_in_charge = 1";
                $relPeriodo = $_POST['cmbTipoPeriodo'];
                $group = "GROUP BY a.idperson";
                $order = "ORDER BY operator";
                
                $dtinterval_note = "DATE(e.entry_date) BETWEEN '$dtstart ' AND ' $dtfinish'";
                $dtinterval_request = "DATE(f.entry_date) BETWEEN '$dtstart ' AND ' $dtfinish'";

                // The variable receives the return from SQL
                $ret = $this->dbRelSolicitacoes->getFormData_Rel2($dtinterval_note, $dtinterval_request, $where, $group, $order);


                if (!$ret['success']) {
                    if($this->log)
                        $this->logIt("{$ret['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }

                //Subheader
                // If it is equal to "ALL" the value will be "All", if not the name of the field brought by SQL
                $subcab = array("{$this->getLanguageWord('hdk_company')}"=>$company = $relAtendenteC == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['company'],
                "{$this->getLanguageWord('hdk_attendant')}"=>$operator = $relAtendente == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['operator']);

                 // If no data is returned for the "Attendant" field
                 if($ret['data']->fields[0] == NULL){
                    
                    // Modal "Record not found"
                    echo json_encode(false);

                }else{

                    // Method to build the table with the collected data
                    $table_data = $this->defineTable($arrTypeTime, $ret['data'], $subcab);

                    // Returns, for Ajax, the final content of the concatenation performed in the defineTable() method
                    // The final product is a string of the contents of the table body
                    echo json_encode($table_data);

                }

               
            break;

            case "3": // Summarized by Area

                $relArea = $_POST['cmbArea'];

                //Search specifications
                $where .= $relArea == "ALL" ? "" : "AND a.idarea = $relArea ";
                $where .= "AND a.entry_date BETWEEN '$dtstart' AND '$dtfinish' ";
                $field = "AREA `area`"; //pipeLatinToUtf8(type) type;
                $group = "GROUP BY a.idarea";
                
                // The variable receives the return from SQL
                $ret = $this->dbRelSolicitacoes->getFormDataArea($field, $where, $group, $order);

                if (!$ret['success']) {
                    if($this->log)
                        $this->logIt("{$ret['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }

                //Subheader
                // If it is equal to "ALL" the value will be "All", if not the name of the field brought by SQL
                $subcab = array("{$this->getLanguageWord('Area')}"=>$area = $relArea == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['area']);
                
                 // If no data is returned for the "Company" field
                 if($ret['data']->fields[0] == NULL){
                    
                    // Modal "Record not found"
                    echo json_encode(false);

                }else{

                     // Method to build the table with the collected data
                    $table_data = $this->defineTable($arrTypeTime, $ret['data'], $subcab);

                    // Returns, for Ajax, the final content of the concatenation performed in the defineTable() method
                    // The final product is a string of the contents of the table body
                    echo json_encode($table_data);

                }

            break;

            case "4": // Summarized by type

                //echo "ok";

                $relArea = $_POST['cmbArea'];
                $relTipo = $_POST['cmbTipo'];

                //Search specifications
                $where .= $relArea == "ALL" ? "" : "AND a.idarea = $relArea ";
                $where .= $relTipo == "ALL" ? "" : "AND a.idtype = $relTipo ";
                $where .= "AND a.entry_date BETWEEN '$dtstart' AND '$dtfinish'";
                $field = "`AREA` `area`, `type` `type`";
                $group = "GROUP BY a.idtype";
                $order = "ORDER BY `area`, `type`";

                // The variable receives the return from SQL
                $ret = $this->dbRelSolicitacoes->getFormDataArea($field, $where, $group, $order);

                if (!$ret['success']) {
                    if($this->log)
                        $this->logIt("{$ret['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
                
                //Subheader
                // If it is equal to "ALL" the value will be "All", if not the name of the field brought by SQL

                $subcab = array("{$this->getLanguageWord('APP_typeLabel')}"=>$relTipo = $relTipo == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['type'],
                "{$this->getLanguageWord('Area')}"=>$area = $relArea == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['area']);


                 // If no data is returned for the "Company" field
                 if($ret['data']->fields[0] == NULL){
                    
                    // Modal "Record not found"
                    echo json_encode(false);

                }else{

                     // Method to build the table with the collected data
                    $table_data = $this->defineTable($arrTypeTime, $ret['data'], $subcab);

                    // Returns, for Ajax, the final content of the concatenation performed in the defineTable() method
                    // The final product is a string of the contents of the table body
                    echo json_encode($table_data);

                }
                
            break;

            case "5": // Summarized by Item

                $relArea = $_POST['cmbArea'];
                $relTipo = $_POST['cmbTipo'];
                $relItem = $_POST['cmbItem'];

                // Search specifications
                $where .= $relArea == "ALL" ? "" : "AND a.idarea = $relArea ";
                $where .= $relTipo == "ALL" ? "" : "AND a.idtype = $relTipo ";
                $where .= $relItem == "ALL" ? "" : "AND a.iditem = $relItem ";
                $where .= "AND a.entry_date BETWEEN '$dtstart' AND '$dtfinish'";
                $field = "AREA `area`, type `type`, item `item`";
                $group = "GROUP BY a.iditem";
                $order = "ORDER BY area, type, item";

                // The variable receives the return from SQL
                $ret = $this->dbRelSolicitacoes->getFormDataArea($field, $where, $group, $order);

                if (!$ret['success']) {
                    if($this->log)
                        $this->logIt("{$ret['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }


                 // If it is equal to "ALL" the value will be "All", if not the name of the field brought by SQL
                $subcab = array("{$this->getLanguageWord('Area')}"=>$area = $relArea == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['area'], 
                "{$this->getLanguageWord('APP_typeLabel')}"=>$tipo = $relTipo == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['type'],
                "{$this->getLanguageWord('APP_itemLabel')}"=>$tipo = $relItem == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['item']);

                
                 // If no data is returned for the "Company" field
                 if($ret['data']->fields[0] == NULL){
                    
                    // Modal "Record not found"
                    echo json_encode(false);

                }else{

                    // Method to build the table with the collected data
                    $table_data = $this->defineTable($arrTypeTime, $ret['data'], $subcab);

                    // Returns, for Ajax, the final content of the concatenation performed in the defineTable() method
                    // The final product is a string of the contents of the table body
                    echo json_encode($table_data);

                }

            break;

            case "6": // Summarized by Service

                $relArea = $_POST['cmbArea'];
                $relTipo = $_POST['cmbTipo'];
                $relItem = $_POST['cmbItem'];
                $relServico = $_POST['cmbServico'];
                $relMotivo = $_POST['cmbMotivo'];

                 // Search specifications
                $where .= $relArea == "ALL" ? "" : "AND a.idarea = $relArea ";
                $where .= $relTipo == "ALL" ? "" : "AND a.idtype = $relTipo ";
                $where .= $relItem == "ALL" ? "" : "AND a.iditem = $relItem ";
                $where .= $relServico == "ALL" ? "" : "AND a.service = $relServico";
                $where .= "AND a.entry_date BETWEEN '$dtstart' AND '$dtfinish'";
                $field = "AREA `area`, type `type`, item `item`, service `service`";
                $group = "GROUP BY a.idservice";
                $order = "ORDER BY area, type, item, service";

                // The variable receives the return from SQL
                $ret = $this->dbRelSolicitacoes->getFormDataArea($field, $where, $group, $order);

                if (!$ret['success']) {
                    if($this->log)
                        $this->logIt("{$ret['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }

                //Subheader
                // If it is equal to "ALL" the value will be "All", if not the name of the field brought by SQL

                $subcab = array("{$this->getLanguageWord('Area')}"=>$area = $relArea == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['area'], 
                "{$this->getLanguageWord('APP_typeLabel')}"=>$tipo = $relTipo == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['type'], 
                "{$this->getLanguageWord('APP_itemLabel')}"=>$item = $relItem == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['item'],

                
                 // If no data is returned for the "Company" field
                 if($ret['data']->fields[0] == NULL){
                    
                    // Modal "Record not found"
                    echo json_encode(false);

                }else{

                     // Method to build the table with the collected data
                    $table_data = $this->defineTable($arrTypeTime, $ret['data'], $subcab);

                    // Returns, for Ajax, the final content of the concatenation performed in the defineTable() method
                    // The final product is a string of the contents of the table body
                    echo json_encode($table_data);

                }

            break;

            case "7": // Summarized by Type of attendance

                $relCompany = $_POST['cmbEmpresa'];
                $relAtend = $_POST['cmbTipoatend'];

                // Search specifications
                $where .= $relAtend == "ALL" ? "" : "AND a.idattendance_way = $relAtend ";
                $where .= "AND a.entry_date BETWEEN '$dtstart' AND '$dtfinish'";
                $field = "way_name `atendimento`";
                $group = "GROUP BY a.idattendance_way";
                $order = "ORDER BY way_name";

                // The variable receives the return from SQL
                $ret = $this->dbRelSolicitacoes->getFormDataArea($field, $where, $group, $order);

                if (!$ret['success']) {
                    if($this->log)
                        $this->logIt("{$ret['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }

                //Subheader
                // If it is equal to "ALL" the value will be "All", if not the name of the field brought by SQL
                $subcab = array("{$this->getLanguageWord('hdk_attendance')}"=>$typeattend = $relAtend == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['way_name']);
                
                 // If no data is returned for the "Company" field
                 if($ret['data']->fields[0] == NULL){
                    
                    // Modal "Record not found"
                    echo json_encode(false);

                }else{

                     // Method to build the table with the collected data
                    $table_data = $this->defineTable($arrTypeTime, $ret['data'], $subcab);

                    // Returns, for Ajax, the final content of the concatenation performed in the defineTable() method
                    // The final product is a string of the contents of the table body
                    echo json_encode($table_data);

                }

            break;

            case "8": // Summarized by Finished Requests

                $relEmpresa = $_POST['cmbEmpresa'];
                $relAtendente = $_POST['cmbAtendente'];

                // Search specifications
                $where .= "WHERE b.CODE_REQUEST = a.code_request";
                $where .= " AND c.code_request = b.CODE_REQUEST";
                $where .= " AND d.idperson = c.id_in_charge";
                $where .= " AND a.code_request = e.code_request";
                $where .= " AND c.ind_in_charge = 1";
                $where .= " AND f.idstatus = a.idstatus";
                $where .= " AND a.idstatus IN (4, 5)";
                $where .= " AND e.finish_date BETWEEN '$dtstart 00:00:00' AND '$dtfinish 23:59:59'";
                //$group = "";
                $order = "ORDER BY d.name";

                if($relAtendente != "ALL"){
                    $where .= " AND c.id_in_charge = $relAtendente";
                }

                // The variable receives the return from SQL
                $ret = $this->dbRelSolicitacoes->getFinishedRequests($where, $group, $order);

                if (!$ret['success']) {
                    if($this->log)
                        $this->logIt("{$ret['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }

                //SUBCABEÇALHO
                $Cwhere = $relEmpresa != "ALL" ? " AND a.idperson = {$relEmpresa} " : "";
                $Cret = $this->dbRelSolicitacoes->getFormData_Rel1($Cwhere);

                //Subheader
                // If it is equal to "ALL" the value will be "All", if not the name of the field brought by SQL
                $subcab = array("{$this->getLanguageWord('hdk_company')}"=>$company = $relEmpresa == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $Cret['data']->fields['company_name'], 
                "{$this->getLanguageWord('hdk_attendance')}"=>$operator = $relAtendente == "ALL" ? "{$this->getLanguageWord('hdk_all')}" : $ret['data']->fields['Operator']);
                
                 // If no data is returned for the "Company" field
                 if($ret['data']->fields[0] == NULL){
                    
                    // Modal "Record not found"
                    echo json_encode(false);

                }else{


                    // Method to build the table with the collected data
                    $table_data = $this->defineTable($arrTypeTime, $ret['data'], $subcab);

                    // Returns, for Ajax, the final content of the concatenation performed in the defineTable() method
                    // The final product is a string of the contents of the table body
                    echo json_encode($table_data);

                }

            break;

        }
    }

    /**
     * defineTable
     * Method to build the table from the data it receives
     * Note that in each case, the table is constructed by concatenation
     * The final product, in all cases, is a string of the contents of the body of the table
     * 
     * @param  array $arrTypeTime array with the report type, and the start and end date values
     * 
     * @param  array $dados_pesquisa data returned by SELECT
     * 
     * @param  array $subcabecalho options selected in each "parent" filter
     * 
     * @return string structure that makes up the body of the table
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function defineTable($arrTypeTime = array(), $dados_pesquisa = array(), $subcabecalho = array()){

        // Retrieving array dates
        $dtstart = $arrTypeTime['periodo_rel'][0];
        $dtfinish = $arrTypeTime['periodo_rel'][1];
       
        switch($arrTypeTime['tipo_rel']){

            case "1": // Construction of the table "Summarized by company"

                // First, the variable receives a fixed header
                $tr_list .= "<tr>
                <th>{$this->getLanguageWord('hdk_company')}</th>
                <th>{$this->getLanguageWord('hdk_minutes')}</th>
                <th>{$this->getLanguageWord('hdk_requests')}</th>
                </tr>";

                $arrList = array();
                // Construction inside the loop
                while(!$dados_pesquisa->EOF){

                    // Now the data that will be used in the table's <td> are rescued
                    $company= "{$dados_pesquisa->fields['company_name']}";
                    $tempo_total = "{$dados_pesquisa->fields['total_min']}";
                    $totalRequest =  "{$dados_pesquisa->fields['total_request']}";
                    
                    // If the total time is more than 60 minutes
                    // Express time in hour and minute units
                    if($tempo_total > 60){

                        $horas = intval($tempo_total/60);
                        $min = intval($tempo_total%60);
                        $TimeInt = intval($dados_pesquisa->fields['total_min']);
                        $tempo_total = "$TimeInt - {$horas}h{$min}min"; 

                    }else if($tempo_total < 0){

                        $tempo_total = 0;
                    }

                    //Now, the variable next to the fixed header you received, adds the structure of the <tds> and their values
                    

                   $tr_list .= "<tr>
                         <td>{$company}</td>
                         <td class = 'text-right'>{$tempo_total}</td>
                        <td class = 'text-right'>{$totalRequest}</td>
                    </tr>";
                    
                    // This array receives the data used in the table data of the table
                    array_push($arrList, array($company,$tempo_total,$totalRequest));
                    
                    // To prevent everlasting loops
                    $dados_pesquisa->MoveNext();
    
                }

                // Merging two arrays // Key-value pair for the period will be in the first position
                $periodo = array("periodo"=>"De {$dtstart} até {$dtfinish}");
                $subcabecalhof = array_merge($periodo, $subcabecalho);
    
                // This session is created so that the data can be used in the Export method
                // If not, the data would have to be returned to Ajax, and from itself back to PHP
                $_SESSION['reportData'] =  array(
                    "titulo1" => "{$this->getLanguageWord('rel_resumidoempresa')}",
                    "subcabecalho" => $subcabecalhof,
                    "tabhead" => array("{$this->getLanguageWord('hdk_company')}","{$this->getLanguageWord('hdk_minutes')}","{$this->getLanguageWord('hdk_requests')}"),
                    "rows" => $arrList,
                    "wth" => array(100,40,40),
                    "orientation" => "P",
                    "wLine" => 200,
                    "wh2" => 190,
                    "wrow" => array(100,40,40),
                    "alignrow" => array("L","R","R")
                );

            break;
        
            case "2": // Construction of the table "Summarized by attendant"

                $arrList = array();

                // First, the variable receives a fixed header
                $tr_list .= "<tr>
                                <th>{$this->getLanguageWord('hdk_attendant')}</th>
                                <th>{$this->getLanguageWord('hdk_departament')}</th>
                                <th>{$this->getLanguageWord('hdk_company')}</th>
                                <th>{$this->getLanguageWord('hdk_totaltime')}</th>
                                <th>{$this->getLanguageWord('hdk_pluralnew')}</th>
                                <th>{$this->getLanguageWord('hdk_passedon')}</th>
                                <th>{$this->getLanguageWord('hdk_inattendance')}</th>
                                <th>{$this->getLanguageWord('hdk_finished')}</th>
                            </tr>";

                while(!$dados_pesquisa->EOF){

                    // Now the data that will be used in the table's <td> are rescued
                    $atendente= "{$dados_pesquisa->fields['operator']}";
                    $departamento = "{$dados_pesquisa->fields['department']}";
                    $empresa = "{$dados_pesquisa->fields['company']}";
                    $total_tempo =  "{$dados_pesquisa->fields['TOTAL_TEMPO']}";

                    // If the total time is more than 60 minutes
                    // Express time in hour and minute units
                    if($total_tempo > 60){

                        $horas = intval($total_tempo/60);
                        $min = intval($total_tempo%60);
                        $total_tempo = "{$dados_pesquisa->fields['TOTAL_TEMPO']} - {$horas}h{$min}min"; 

                    }else if($tempo_total < 0){

                        $tempo_total = 0;
                    }

                    $novos = "{$dados_pesquisa->fields['NEW']}";
                    $repassados = "{$dados_pesquisa->fields['REPASSED']}";
                    $em_atendimento = "{$dados_pesquisa->fields['ON_ATTENDANCE']}";
                    $finalizado = "{$dados_pesquisa->fields['FINISH']}";

                    //Now, the variable next to the fixed header you received, adds the structure of the <tds> and their values
                    $tr_list .= "<tr>
                            <td>{$atendente}</td>
                            <td>{$departamento}</td>
                            <td>{$empresa}</td>
                            <td class = 'text-right'>{$total_tempo}</td>
                            <td class = 'text-right'>{$novos}</td>
                            <td class = 'text-right'>{$repassados}</td>
                            <td class = 'text-right'>{$em_atendimento}</td>
                            <td class = 'text-right'>{$finalizado}</td>

                        </tr>";

                        // This array receives the data used in the table data of the table
                        array_push($arrList, array($atendente, $departamento, $empresa, $total_tempo, $novos, $repassados, $em_atendimento, $finalizado));

                        // To prevent everlasting loops
                        $dados_pesquisa->MoveNext();
                }

                // Merging two arrays // Key-value pair for the period will be in the first position
                $periodo = array("periodo"=>"De {$dtstart} até {$dtfinish}");
                $subcabecalhof = array_merge($periodo, $subcabecalho);
                
                // This session is created so that the data can be used in the Export method
                // If not, the data would have to be returned to Ajax, and from itself back to PHP
                $_SESSION['reportData'] =  array(
                    "titulo1" => "{$this->getLanguageWord('rel_resumidoatendente')}",
                    "subcabecalho" => $subcabecalhof,
                    "tabhead" => array("{$this->getLanguageWord('hdk_attendant')}", "{$this->getLanguageWord('hdk_departament')}", "{$this->getLanguageWord('hdk_company')}", "{$this->getLanguageWord('hdk_totaltime')}", "{$this->getLanguageWord('hdk_pluralnew')}", 
                    "{$this->getLanguageWord('hdk_passedon')}", "{$this->getLanguageWord('hdk_inattendance')}", "{$this->getLanguageWord('hdk_finished')}"),
                    "rows" => $arrList,
                    "wth" => array(55,45,42,35,17,23,28,20),
                    "orientation" => "L",
                    "wLine" => 285,
                    "wh2" => 270,
                    "wrow" => array(55,45,42,35,17,23,28,20),
                    "alignrow" => array("L","C","C","R","R","R","R","R")
                    );

            break;

            case "3": // Construction of the table "Summarized by area"

                $arrList = array();

                // First, the variable receives a fixed header
                $tr_list .= "<tr>
                                <th>{$this->getLanguageWord('Area')}</th>
                                <th>{$this->getLanguageWord('hdk_totaltime')}</th>
                            </tr>";

                while(!$dados_pesquisa->EOF){

                    // Now the data that will be used in the table's <td> are rescued
                    $area = "{$dados_pesquisa->fields['area']}";
                    $tempo_total = "{$dados_pesquisa->fields['total_time']}";

                    // If the total time is more than 60 minutes
                    // Express time in hour and minute units
                    if($tempo_total > 60){

                        $horas = intval($tempo_total/60);
                        $min = intval($tempo_total%60);
                        $tempo_total = "{$dados_pesquisa->fields['total_time']} - {$horas}h{$min}min"; 

                    }else if($tempo_total < 0){

                        $tempo_total = 0;
                    }

                    //Now, the variable next to the fixed header you received, adds the structure of the <tds> and their values
                    $tr_list .= "<tr>
                            <td>{$area}</td>
                            <td class = 'text-right'>{$tempo_total}</td>
                        </tr>";

                        // This array receives the data used in the table data of the table
                        array_push($arrList, array($area, $tempo_total));

                        // To prevent everlasting loops
                        $dados_pesquisa->MoveNext();
                }

                // Merging two arrays // Key-value pair for the period will be in the first position
                $periodo = array("periodo"=>"De {$dtstart} até {$dtfinish}");
                $subcabecalhof = array_merge($periodo, $subcabecalho);
                    
                // This session is created so that the data can be used in the Export method
                // If not, the data would have to be returned to Ajax, and from itself back to PHP
                $_SESSION['reportData'] =  array(
                    "titulo1" => "{$this->getLanguageWord('rel_resumidoarea')}",
                    "subcabecalho" => $subcabecalhof,
                    "tabhead" => array("{$this->getLanguageWord('Area')}", "{$this->getLanguageWord('hdk_totaltime')}"),
                    "rows" => $arrList,
                    "wth" => array(120,60),
                    "orientation" => "P",
                    "wLine" => 200,
                    "wh2" => 190,
                    "wrow" => array(120,60),
                    "alignrow" => array("L","R")
                    );

            break;

            case "4": // Construction of the "Summarized by type" table

                $arrList = array();

                // First, the variable receives a fixed header
                $tr_list .= "<tr>
                    <th>{$this->getLanguageWord('APP_typeLabel')}</th>
                    <th>{$this->getLanguageWord('hdk_totaltime')}</th>
                </tr>";

                // For creating the table with colspan
                $areaAtual = "X";
                while(!$dados_pesquisa->EOF){

                    // If the current area is different from the previous area, that is, the "so far current"
                    if($dados_pesquisa->fields['area'] != $areaAtual){

                        // The previous area becomes the current area
                        $areaAtual = $dados_pesquisa->fields['area'];

                        // A new colspan cell is created that will receive the name of the area
                        $tr_list .= "<tr>
                                <td class = 'text-center' colspan='2' style='background-color: #78909c;color: #000;'>
                                    {$dados_pesquisa->fields['area']}
                                </td>
                            </tr>";
                    
                    
                    }// If not, if the current area is the same as "previous area" (current so far)

                    // Now the data that will be used in the table's <td> are rescued
                    $area = $dados_pesquisa->fields['area'];
                    $tipo = $dados_pesquisa->fields['type'];
                    $tempo_total = $dados_pesquisa->fields['total_time'];

                    // If the total time is more than 60 minutes
                    // Express time in hour and minute units
                    if($tempo_total > 60){

                        $horas = intval($tempo_total/60);
                        $min = intval($tempo_total%60);
                        $tempo_total = "{$dados_pesquisa->fields['total_time']} - {$horas}h{$min}min"; 

                    }else if($tempo_total < 0){

                        $tempo_total = 0;
                    }

                    //Now, the variable next to the fixed header you received, adds the structure of the <tds> and their values
                    $tr_list .= "<tr>
                        <td>{$tipo}</td>
                        <td class = 'text-right'>{$tempo_total}</td>
                    </tr>";

                    // This array receives the data used in the table data of the table
                    array_push($arrList, array($area, $tipo, $tempo_total));

                    // To prevent everlasting loops
                    $dados_pesquisa->MoveNext();

                }

                // Merging two arrays // Key-value pair for the period will be in the first position
                $periodo = array("periodo"=>"De {$dtstart} até {$dtfinish}");
                $subcabecalhof = array_merge($periodo, $subcabecalho);
  
                // This session is created so that the data can be used in the Export method
                // If not, the data would have to be returned to Ajax, and from itself back to PHP
                $_SESSION['reportData'] =  array(
                    "titulo1" => "{$this->getLanguageWord('rel_resumidotipo')}",
                    "subcabecalho" => $subcabecalhof,
                    "tabhead" => array("{$this->getLanguageWord('APP_typeLabel')}", "{$this->getLanguageWord('hdk_totaltime')}"),
                    "rows" => $arrList,
                    "wth" => array(120,60),
                    "orientation" => "P",
                    "wLine" => 200,
                    "wh2" => 190,
                    "wrow" => array(120,60),
                    "alignrow" => array("L","R"),
                    "colspan" => true
                    );

            break;

            case "5": // Construction of the "Summarized by item" table

                $arrList = array();

                // First, the variable receives a fixed header
                $tr_list .= "<tr>
                    <th>{$this->getLanguageWord('APP_itemLabel')}</th>
                    <th>{$this->getLanguageWord('hdk_totaltime')}</th>
                </tr>";

                // For creating the table with colspan
                $areaAtual = "X";
                $tipoAtual = "X";
                while(!$dados_pesquisa->EOF){

                    // If the current area is different from the previous area, that is, the "so far current"
                    if($dados_pesquisa->fields['area'] != $areaAtual){

                        // The previous area becomes the current area
                        $areaAtual = $dados_pesquisa->fields['area'];

                        // A new colspan cell is created that will receive the name of the area
                        $tr_list .= "<tr>
                                <td class = 'text-center' colspan='2' style='background-color: #78909c;color: #000;'>
                                    {$dados_pesquisa->fields['area']}
                                </td>
                            </tr>";
                    
                    
                    }// If not, if the current area is the same as "previous area" (current so far)

                    if($dados_pesquisa->fields['type'] != $tipoAtual){

                        // The previous type becomes the current type
                        $tipoAtual = $dados_pesquisa->fields['type'];

                        // A new colspan cell is created that will receive the name of the type
                        $tr_list .= "<tr>
                                <td class = 'text-center' colspan='2' style='background-color: #90a4ae;color: #000;'>
                                    {$dados_pesquisa->fields['type']}
                                </td>
                            </tr>";
                    
                    
                    }// If not, if the current type is the same as "previous type" (current so far)

                    // Now the data that will be used in the table's <td> are rescued
                    $item = "{$dados_pesquisa->fields['item']}";
                    $tempo_total = "{$dados_pesquisa->fields['total_time']}";

                    // If the total time is more than 60 minutes
                    // Express time in hour and minute units
                    if($tempo_total > 60){

                        //$int_total = intval($dados_pesquisa->fields['TOTAL_TEMPO']);
                        $horas = intval($tempo_total/60);
                        $min = intval($tempo_total%60);
                        $tempo_total = "{$dados_pesquisa->fields['total_time']} - {$horas}h{$min}min"; 

                    }else if($tempo_total < 0){

                        $tempo_total = 0;
                    }

                    //Now, the variable next to the fixed header you received, adds the structure of the <tds> and their values
                    $tr_list .= "<tr>
                            <td>{$item}</td>
                            <td class = 'text-right'>{$tempo_total}</td>
                        </tr>";

                        // This array receives the data used in the table data of the table
                        array_push($arrList, array($areaAtual, $tipoAtual, $item, $tempo_total));

                        // To prevent everlasting loops
                        $dados_pesquisa->MoveNext();
                }

                // Merging two arrays // Key-value pair for the period will be in the first position
                $periodo = array("periodo"=>"De {$dtstart} até {$dtfinish}");
                $subcabecalhof = array_merge($periodo, $subcabecalho);
                    
                // This session is created so that the data can be used in the Export method
                // If not, the data would have to be returned to Ajax, and from itself back to PHP
                $_SESSION['reportData'] =  array(

                    "titulo1" => "{$this->getLanguageWord('rel_resumidoitem')}",
                    "subcabecalho" => $subcabecalhof,
                    "tabhead" => array("{$this->getLanguageWord('APP_itemLabel')}", "{$this->getLanguageWord('hdk_totaltime')}"),
                    "rows" => $arrList,
                    "wth" => array(120,60),
                    "orientation" => "P",
                    "wLine" => 200,
                    "wh2" => 190,
                    "wrow" => array(120,60),
                    "alignrow" => array("L","R"),
                    "colspan" => true,
                    "subrow_type" => true
                    );


            break;

            case "6": // Construction of the table "Summarized by service"

                $arrList = array();

                // First, the variable receives a fixed header
                $tr_list .= "<tr>
                    <th>{$this->getLanguageWord('hdk_service')}</th>
                    <th class = 'text-right'>{$this->getLanguageWord('hdk_totaltime')}</th>
                </tr>";

                 // For creating the table with colspan
                 $areaAtual = "X";
                 $tipoAtual = "X";
                 $itemAtual = "X";
                 while(!$dados_pesquisa->EOF){
 
                     // If the current area is different from the previous area, that is, the "so far current"
                     if($dados_pesquisa->fields['area'] != $areaAtual){
 
                         // The previous area becomes the current area
                         $areaAtual = $dados_pesquisa->fields['area'];
 
                         // A new colspan cell is created that will receive the name of the area
                         $tr_list .= "<tr>
                                 <td class = 'text-center' colspan='2' style='background-color: #78909c;color: #000;'>
                                     {$dados_pesquisa->fields['area']}
                                 </td>
                             </tr>";
                     
                     
                     }// If not, if the current area is the same as "previous area" (current so far)
 
                     if($dados_pesquisa->fields['type'] != $tipoAtual){
 
                         // The previous type becomes the current type
                         $tipoAtual = $dados_pesquisa->fields['type'];
 
                         // A new colspan cell is created that will receive the name of the type
                         $tr_list .= "<tr>
                                 <td class = 'text-center' colspan='2' style='background-color: #90a4ae;color: #000;'>
                                     {$dados_pesquisa->fields['type']}
                                 </td>
                             </tr>";
                     
                     
                     }// If not, if the current type is the same as "previous type" (current so far)

                    if($dados_pesquisa->fields['item'] != $itemAtual){

                        // The previous type becomes the current type
                        $itemAtual = $dados_pesquisa->fields['item'];

                        // A new colspan cell is created that will receive the name of the type
                        $tr_list .= "<tr>
                                <td class = 'text-center' colspan='2' style='background-color: #b0bec5;color: #000;'>
                                    {$dados_pesquisa->fields['item']}
                                </td>
                            </tr>";
                    
                    
                    }// If not, if the current type is the same as "previous type" (current so far)
                     

                    // Now the data that will be used in the table's <td> are rescued
                    $servico = "{$dados_pesquisa->fields['service']}";
                    $tempo_total = "{$dados_pesquisa->fields['total_time']}";

                    // If the total time is more than 60 minutes
                    // Express time in hour and minute units
                    if($tempo_total > 60){

                        $horas = intval($tempo_total/60);
                        $min = intval($tempo_total%60);
                        $tempo_total = "{$dados_pesquisa->fields['total_time']} - {$horas}h{$min}min"; 

                    }else if($tempo_total < 0){

                        $tempo_total = 0;
                    }

                    //Now, the variable next to the fixed header you received, adds the structure of the <tds> and their values
                    $tr_list .= "<tr>
                            <td>{$servico}</td>
                            <td class = 'text-right'>{$tempo_total}</td>
                        </tr>";

                        // This array receives the data used in the table data of the table
                        array_push($arrList, array($areaAtual, $tipoAtual, $itemAtual, $servico, $tempo_total));

                        // To prevent everlasting loops
                        $dados_pesquisa->MoveNext();
                }

                 // Merging two arrays // Key-value pair for the period will be in the first position
                $periodo = array("periodo"=>"De {$dtstart} até {$dtfinish}");
                $subcabecalhof = array_merge($periodo, $subcabecalho);
                    
                // This session is created so that the data can be used in the Export method
                // If not, the data would have to be returned to Ajax, and from itself back to PHP
                $_SESSION['reportData'] =  array(

                    "titulo1" => "{$this->getLanguageWord('rel_resumidoservico')}",
                    "subcabecalho" => $subcabecalhof,
                    "tabhead" => array("{$this->getLanguageWord('hdk_service')}", "{$this->getLanguageWord('hdk_totaltime')}"),
                    "rows" => $arrList,
                    "wth" => array(120,60),
                    "orientation" => "P",
                    "wLine" => 200,
                    "wh2" => 190,
                    "wrow" => array(120,60),
                    "alignrow" => array("L","R"),
                    "colspan" => true,
                    "subrow_type" => true,
                    "subrow_item" => true
                    );


            break;

            case "7": // Construction of the table "Summarized by Attendance"

                $arrList = array();

                // First, the variable receives a fixed header
                $tr_list .= "<tr>
                    <th>{$this->getLanguageWord('hdk_attendance')}</th>
                    <th>{$this->getLanguageWord('hdk_totaltime')}</th>
                </tr>";

                while(!$dados_pesquisa->EOF){

                    // Now the data that will be used in the table's <td> are rescued
                    $tipo_atendimento = "{$dados_pesquisa->fields['atendimento']}";
                    $tempo_total = "{$dados_pesquisa->fields['total_time']}";

                    // If the total time is more than 60 minutes
                    // Express time in hour and minute units
                    if($tempo_total > 60){

                        $horas = intval($tempo_total/60);
                        $min = intval($tempo_total%60);
                        $tempo_total = "{$dados_pesquisa->fields['total_time']} - {$horas}h{$min}min"; 

                    }else if($tempo_total < 0){

                        $tempo_total = 0;
                    }

                    //Now, the variable next to the fixed header you received, adds the structure of the <tds> and their values
                    $tr_list .= "<tr>
                            <td>{$tipo_atendimento}</td>
                            <td class = 'text-right'>{$tempo_total}</td>
                        </tr>";

                        // This array receives the data used in the table data of the table
                        array_push($arrList, array($tipo_atendimento, $tempo_total));

                        // To prevent everlasting loops
                        $dados_pesquisa->MoveNext();
                }

                // Merging two arrays // Key-value pair for the period will be in the first position
                $periodo = array("periodo"=>"De {$dtstart} até {$dtfinish}");
                $subcabecalhof = array_merge($periodo, $subcabecalho);
                    
                // This session is created so that the data can be used in the Export method
                // If not, the data would have to be returned to Ajax, and from itself back to PHP
                $_SESSION['reportData'] =  array(
                    "titulo1" => "{$this->getLanguageWord('rel_resumidotipoatend')}",
                    "subcabecalho" => $subcabecalhof,
                    "tabhead" => array("{$this->getLanguageWord('hdk_attendance')}", "{$this->getLanguageWord('hdk_totaltime')}"),
                    "rows" => $arrList,
                    "wth" => array(120,60),
                    "orientation" => "P",
                    "wLine" => 200,
                    "wh2" => 190,
                    "wrow" => array(120,60),
                    "alignrow" => array("L","R")
                    );

            break;

            case "8": // Construction of the table "Finished Requests"

                $arrList = array();

                // First, the variable receives a fixed header
                $tr_list .= "<tr>
                    <th>{$this->getLanguageWord('PDF_code')}</th>
                    <th>{$this->getLanguageWord('hdk_subject')}</th>
                    <th>{$this->getLanguageWord('hdk_attendant')}</th>
                    <th>{$this->getLanguageWord('hdk_minutes')}</th>
                    <th>{$this->getLanguageWord('APP_statusLabel')}</th>
                </tr>";

                while(!$dados_pesquisa->EOF){

                    // Now the data that will be used in the table's <td> are rescued
                    $code = "{$dados_pesquisa->fields['Code']}";
                    $subject = "{$dados_pesquisa->fields['Subject']}";
                    $operator = "{$dados_pesquisa->fields['Operator']}";
                    $tempo_total = "{$dados_pesquisa->fields['Minutes']}";
                    $status = "{$dados_pesquisa->fields['Status']}";

                    // If the total time is more than 60 minutes
                    // Express time in hour and minute units
                    if($tempo_total > 60){

                        $horas = intval($tempo_total/60);
                        $min = intval($tempo_total%60);
                        $tempo_total = "{$dados_pesquisa->fields['Minutes']} - {$horas}h{$min}min"; 

                    }else if($tempo_total < 0){

                        $tempo_total = 0;
                    }

                    //Now, the variable next to the fixed header you received, adds the structure of the <tds> and their values
                    $tr_list .= "<tr>
                            <td>{$code}</td>
                            <td>{$subject}</td>
                            <td>{$operator}</td>
                            <td class = 'text-right'>{$tempo_total}</td>
                            <td>{$status}</td>
                        </tr>";

                        // This array receives the data used in the table data of the table
                        array_push($arrList, array($code, $subject, $operator, $tempo_total, $status));

                        // To prevent everlasting loops
                        $dados_pesquisa->MoveNext();
                }

                // Merging two arrays // Key-value pair for the period will be in the first position
                $periodo = array("periodo"=>"De {$dtstart} até {$dtfinish}");
                $subcabecalhof = array_merge($periodo, $subcabecalho);
                    
                // This session is created so that the data can be used in the Export method
                // If not, the data would have to be returned to Ajax, and from itself back to PHP
                $_SESSION['reportData'] =  array(
                    "titulo1" => "{$this->getLanguageWord('rel_solicitfinal')}",
                    "subcabecalho" => $subcabecalhof,
                    "tabhead" => array("{$this->getLanguageWord('PDF_code')}", "{$this->getLanguageWord('hdk_subject')}", "{$this->getLanguageWord('hdk_attendant')}", "{$this->getLanguageWord('hdk_minutes')}", "{$this->getLanguageWord('APP_statusLabel')}"),
                    "rows" => $arrList,
                    "wth" => array(35,80,70,30,50),
                    "orientation" => "L",
                    "wLine" => 285,
                    "wh2" => 270,
                    "wrow" => array(35,80,70,30,50),
                    "alignrow" => array("L","C","C","R","C")
                    );

            break;

        }

         // Return of the built table, in either case
         return $tr_list;
    }
    
    /**
     * exportReport
     * Method that will define where, or how the data will be exported
     * 
     * @return string returns file url
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function exportReport()
    {

        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        // Recovery of data saved in the session
        // Each case built a different table, and a session with data for each circumstance
        // Thus, the session variable was used to allow the export of data for each circumstance
        $arrRelData = $_SESSION['reportData'];
        

        switch ($_POST['typeFile']) {

            case "CSV": //if the export choice was "CSV"
                // In this case, only data from table data (td) are sent
                $fileNameUrl = $this->makeCsvFile($arrRelData['rows']);

                header('Set-Cookie: fileDownload=true; path=/');
                header('Cache-Control: max-age=60, must-revalidate');
                header("Content-type: text/csv");
                header('Content-Disposition: attachment; filename="'.$fileNameUrl.'"');

                echo $fileNameUrl;
                break;

            case "XLS":

                header('Set-Cookie: fileDownload=true; path=/');
                header('Cache-Control: max-age=60, must-revalidate');
                header('Content-type: application/x-msexcel');
                header('Content-Length: ' . filesize($fileNameWrite));
                header('Content-Transfer-Encoding: binary');
                header('Content-Disposition: attachment; filename="'.$fileNameUrl.'"');

                echo $table;

                break;

            case "PDF": //if the export choice was "PDF"
                // Calling the method of building the PDF, sending the composition data from the table
                // In this case, the dimension data from the table is also sent
                echo $this->makePdfFile($arrRelData); 
                break;
        }

    }
    
    /**
     * makeCsvFile
     * Receive only data from table data (td)
     * In this method, only the data itself, returned from SQL, is used
     * 
     * @param array $rowsData data saved in the 'report_data' session, in the 'rows' index
     * 
     * @return string csv file url
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function makeCsvFile($rowsData) 
    {

        $csv = array();

        foreach ($rowsData as $row=>$val) {
            $bus = "";
            foreach($val as $r=>$v){
                // Each variable receives the configuration of each cell
                $bus .= str_replace('&nbsp;', '', $this->clearAccent($v))."|";
            }
            // In the last item, the concatention point is subtracted (because there will be no next item)
            $bus = substr($bus,0,-1);

            // The $csv variable receives the final structure, of concatenated cells
           array_push($csv, $bus) ;
        }

       $filename = $_SESSION['SES_NAME_PERSON'] . "_report_".time().".csv";
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp')) {
            if( !chmod($this->helpdezkPath . '/app/downloads/tmp', 0777) )
                $this->logIt("Export Enrollment Report " . ' - Directory ' . $this->helpdezkPath . '/app/tmp' . ' is not writable ' ,3,'general',__LINE__);

        }

        $fp = fopen($fileNameWrite, 'w');

        if(!$_POST['txtDelimiter'])	$_POST['txtDelimiter'] = ",";
        foreach ($csv as $line) {
            fputcsv($fp, explode('|', $line), $_POST['txtDelimiter']);
        }
        fclose($fp);

        $fileNameUrl = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

        // Returns the file URL
        return $fileNameUrl;

    }
    
    /**
     * makePdfFile
     * Receive all session data
     * In this method the table is built to be exported to PDF, based on the data of the session variable
     * 
     * @param array $rowsData data saved in the 'report_data' session
     * 
     * @return string pdf file url
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function makePdfFile($rowsData) 
    {

        // class FPDF with extension to parsehtml
        // Create a instance of library
        $pdf = $this->_returnfpdfhdk();

        //Font parameters to be used in the report.
        $FontFamily = 'Arial';
        $FontStyle  = '';
        $FontSize   = 8;
        $CelHeight = 4;

        $title =  html_entity_decode(utf8_decode($rowsData['titulo1']),ENT_QUOTES, "ISO8859-1"); //Title //$rowsData['titulo1']?
        $PdfPage = (utf8_decode($this->getLanguageWord('PDF_Page'))) ; //Page numbering
        $leftMargin = 10; 

        $logo = array("file" => $this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage(),
            "posx" => $leftMargin + 10,
            "posy" => 8
        );

        //Cycles through the data array that forms the subheader
        //Its accessible via the "subcabecalho" key of the session variable data structure
        foreach($rowsData['subcabecalho'] as $key => $value){

            // If the value of the $ key is "periodo", the period is inserted in the first line of the sub-header
            if($key == "periodo"){

                $h2[$key] = array(

                        "txt"=>html_entity_decode(utf8_decode($value),ENT_QUOTES, "ISO8859-1"), 
                        "width"=>$rowsData['wh2'],
                        "height" => $CelHeight,
                        "border" => 0,
                        "ln"=>1,
                        "fill"=>0,
                        "align" => 'C');

            // If not, it will be the other data that make up the subheader
            // Some filters are only accessible from others
            // These data will be the choice of "parent" filters, if any
            }else if($key != "periodo"){

                $h2[$key] = array(

                    "txt"=>html_entity_decode(utf8_decode("{$key}: {$value}"),ENT_QUOTES, "ISO8859-1"), 
                    "width"=>$rowsData['wh2'],
                    "height" => $CelHeight,
                    "border" => 0,
                    "ln"=>1,
                    "fill"=>0,
                    "align" => 'L');

            }

        }

        // Number of headers
        $table_total_col = count($rowsData['rows'][0]); 
        // Dynamic header configuration
        for($tb_th = 0; $tb_th < $table_total_col; $tb_th++){

            //$rowsData['tabhead'] contains an indexed array with the names of the headers
            $th[$tb_th] = array("txt"=>html_entity_decode(utf8_decode($rowsData['tabhead'][$tb_th]),ENT_QUOTES, "ISO8859-1"),
                    "width"=> $rowsData['wth'][$tb_th], 
                    "height" => $CelHeight,
                    "border" => 0,
                    "ln"=> $tb_th == ($table_total_col-1) ? 1: 0, //Se for a última coluna, recebe 1, se não, recebe 0
                    "fill"=>1,
                    "align" => 'C');
        }

        $headerparams = array(
            "leftMargin" => $leftMargin,
            "pdfpage" => $PdfPage,
            "FontFamily" => $FontFamily,
            "FontStyle"  => $FontStyle,
            "FontSyze"  => $FontSize,
            "logo" => $logo,
            "title" => $title,
            "tableHeader" => $th,
            "h2" => $h2,
            "lineWidth" => $rowsData['wLine']
        );

        $pdf->AliasNbPages();
        $pdf->AddPage($rowsData['orientation'],'A4',$headerparams); //Add new page in file

        $pdf->SetWidths($rowsData['wrow']); 
        $pdf->SetAligns($rowsData['alignrow']);

        // Creating the table with colspan
        $area_atual = "X";
        $tipo_atual = "X";
        $item_atual = "X";
        // $rowsData['rows'] has, in each of its indexes, another data array
        // Each of these data arrays has, in the first position 0, the name of the area
        foreach($rowsData['rows'] as $linha => $value){

            // If there is, in the data array, the key "colspan"
            if(isset($rowsData['colspan'])){
                // If the field "area" of the current line is different from the previous one
                // In the first loop will be
                if($rowsData['rows'][$linha][0] != $area_atual){

                    // The "area" field of the current line becomes the "area_atual"
                    $area_atual = $rowsData['rows'][$linha][0];

                    // The colspan column is created with the value / name of the current area
                    $pdf->setFillColor(120,144,156);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->Cell($leftMargin);            
                    $pdf->Cell(180.1,4,html_entity_decode(utf8_decode($area_atual),ENT_QUOTES, "ISO8859-1"),0,1,'C',1);
                    
                }
            
            }

            // If there is, in the data array, the key "colspan"
            if(isset($rowsData['subrow_type'])){
                // If the field "area" of the current line is different from the previous one
                // In the first loop will be
                if($rowsData['rows'][$linha][1] !=  $tipo_atual){

                    // The "area" field of the current line becomes the "area_atual"
                    $tipo_atual = $rowsData['rows'][$linha][1];

                    // The colspan column is created with the value / name of the current area
                    $pdf->setFillColor(144,164,174);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->Cell($leftMargin);            
                    $pdf->Cell(180.1,4,html_entity_decode(utf8_decode($tipo_atual),ENT_QUOTES, "ISO8859-1"),0,1,'C',1);
                
                }
            
            }

            // If there is, in the data array, the key "colspan"
            if(isset($rowsData['subrow_item'])){
                // If the field "area" of the current line is different from the previous one
                // In the first loop will be
                if($rowsData['rows'][$linha][2] !=  $item_atual){


                    // The "area" field of the current line becomes the "area_atual"
                    $item_atual = $rowsData['rows'][$linha][2];


                    // The colspan column is created with the value / name of the current area
                    $pdf->setFillColor(176,190,197);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->Cell($leftMargin);            
                    $pdf->Cell(180.1,4,html_entity_decode(utf8_decode($item_atual),ENT_QUOTES, "ISO8859-1"),0,1,'C',1);
                
                }
            
            }
            

            // "Normal" lines are created for listing items
            $pdf->setRowFillColor(205,205,205);
            $pdf->SetTextColor(0,0,0);

            $pdf->Cell($leftMargin);

            // Now we go through the data array of the current position
            foreach($value as $k => $v){               
                
                // If the colspan key exists, the first field of the line / record has already been used, so if you use only $ v it will be repeated
                // So, if there is a colspan key, the value must be the one at index + 1 // So index 0 is skipped
                if(isset($rowsData['colspan']) && (!isset($rowsData['subrow_type']) && !isset($rowsData['subrow_item']) && !isset($rowsData['subrow_item']))){

                    $valuetmp = $value[$k +1];

                }else if(isset($rowsData['colspan']) && isset($rowsData['subrow_type']) && !isset($rowsData['subrow_item'])){

                    $valuetmp = $value[$k +2];

                }else if(isset($rowsData['colspan']) && isset($rowsData['subrow_type']) && isset($rowsData['subrow_item'])){

                    $valuetmp = $value[$k +3];

                }else{

                    $valuetmp = $v;
                }

                $td[$k] = html_entity_decode(utf8_decode($valuetmp),ENT_QUOTES, "ISO8859-1");
            }

            $pdf->row($td);
        }

        $filename = $_SESSION['SES_NAME_PERSON'] . "_report_".time().".pdf";
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;
        $fileNameUrl = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp')) {
            if( !chmod($this->helpdezkPath . '/app/downloads/tmp', 0777) )
                $this->logIt("Export Person Report " . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp' . ' is not writable ' ,3,'general',__LINE__);

        }

        $pdf->Output($fileNameWrite,'F');

        // Returns the file URL
        return $fileNameUrl;

    }
    
    /**
     * _returnfpdfhdk
     * fpdfhdk class instance
     * This class is the original FPDF class, but changed
     * 
     * @return object fpdfhdk class instance
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function _returnfpdfhdk() {
        require_once(FPDF . 'fpdfhdk.php');
        $pdf = new fpdfhdk();
        return $pdf;
    }
        
    /**
     * ajaxCustomer
     * Receives the option selected in the "Company" filter when the report type is "Summarized by Attendant"
     * 
     * @return string attendant options 
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function ajaxCustomer()
    {
        // Rescue of the selected company
        $CompanyID = $_POST['companyID'];

        // Calling the method that will perform the change to the "Attendant" filter
        // Returns to Ajax the method return
        echo $this->comboCustomerHtml($CompanyID);
    }

    /**
     * comboCustomerHtml
     * Changing the "Attendant" filter options based on the selection of the Company filter
     * 
     * @param string $CompanyID selected company
     * 
     * @return string attendant options 
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function comboCustomerHtml($CompanyID)
    {

        $where = $CompanyID == "ALL" ? "" : " AND c.idperson = {$CompanyID}";
        
        $select = "<option value='' selected=selected >".$this->getLanguageWord('Select')."</option>";
        $select .= "<option value='ALL' >".$this->getLanguageWord('all')."</option>";
        
        $arrType = $this->dbRelSolicitacoes->comboAtendente($where);

        while ( !$arrType['data']->EOF ) {
            $select .= "<option value='{$arrType['data']->fields['idperson']}' >". $arrType['data']->fields['operator'] ."</option>";
            $arrType['data']->MoveNext();
        }

        // Returns the options of the attendants that are part of the selected company
        return $select;
    }

}  

?>