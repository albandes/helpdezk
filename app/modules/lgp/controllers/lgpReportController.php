<?php 

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpReport extends lgpCommon
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
        $this->modulename = 'LGPD' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Program log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('lgpReport');

        // Model instance of this program
        $this->loadModel('lpgreport_model');
        $this->dbReport = new lgpreport_model();

        //Instance of the "logos" class, from the admin module
        $this->loadModel('admin/logos_model');
        $dbLogo = new logos_model();
        $this->dbLogo = $dbLogo;

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
        $this->_makeNavLgp($smarty);


        // Processing of data from the logos_model class
        $reportslogo = $this->dbLogo->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);

        // ASSIGN COMBOS 

        // Configuration of the report type filter combo
        $aRelType = $this->_comboRelType();
        $smarty->assign('TypeIds', array_keys($aRelType)); // Returns an array with the original keys
        $smarty->assign('TypeVals', array_values($aRelType)); // Returns an array with the values ​​of the original
        $smarty->assign('idtype', 0);

        // Configuration of the report HolderType filter combo
        $aRelClass = $this->_comboHolderType();
        $smarty->assign('HolderTypeIds', $aRelClass['ids']); // Returns an array with the original keys
        $smarty->assign('HolderTypeVals', $aRelClass['values']); // Returns an array with the values ​​of the original
        $smarty->assign('idholdertype', '');

        // Configuration of the report data type filter combo
        $aRelDataType = $this-> _comboType();
        $smarty->assign('DatatypeIds', $aRelDataType['ids']); // Returns an array with the original keys
        $smarty->assign('DatatypeVals', $aRelDataType['values']); // Returns an array with the values ​​of the original
        $smarty->assign('iddatatype', '');

        // Configuration of the report storage filter combo
        $aRelStorage = $this->_comboStorage();
        $smarty->assign('StorageIds', $aRelStorage['ids']); // Returns an array with the original keys
        $smarty->assign('StorageVals', $aRelStorage['values']); // Returns an array with the values ​​of the original
        $smarty->assign('idstorage', '');

        // Configuration of the report whom_shared filter combo
        $typeOperator = $this->dbReport->getLgpTypePerson("LGP_operator"); //First get the respective idtyperson

        //Where combo SharedWith
        $where = count($typeOperator['data']) > 0 ? "WHERE idtypeperson = {$typeOperator['data'][0]['idtypeperson']}" : null;

        $aSharedWithPersons = $this->_comboSharedWith($where, "ORDER BY `name`");
        $smarty->assign('PersonsharedIds', $aSharedWithPersons['ids']); // Returns an array with the original keys
        $smarty->assign('PersonsharedVals', $aSharedWithPersons['values']); // Returns an array with the values ​​of the original
        $smarty->assign('idpersonshared', '');

        // Configuration of the report who_access filter combo //Reuse of $typePerson variable value 
        $typePersonAc = $this->dbReport->getLgpTypePerson("LGP_personhasaccess"); //First get the respective idtyperson
        
        $accID = count($typePersonAc['data']) > 0 ? ",{$typePersonAc['data'][0]['idtypeperson']}" : "";

        $aRelPerson = $this->_comboPerson("WHERE `status` = 'A'","ORDER BY `name`",null,null,$accID);

        $smarty->assign('personaccessesopts',  $aRelPerson['opts']);
        $smarty->assign('idpersonaccesses', array(""));

        // Security token processing
        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Processing another template
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language

        // Processing the template
        $smarty->display('lgp-report.tpl');

    }

    public function _comboRelType(){

        // getLanguageWord() search for the parameter in the vocabulary
        // The parameter is a key_name registered in the vocabulary program
        $type_options = array(
            1=> $this->getLanguageWord('lgp_whoaccess'),
            2=> $this->getLanguageWord('lgp_shared')
        );

        return $type_options;

    }

    /*
    *  Programming after form submission
    */
    public function getReport(){

        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $relType = $_POST['cmbType']; // Receive the type of form

        // Array for sending the type of report and period (start and end date) to the defineTable () method
        $arrType = array(
            "report_type" => $relType
        );

        // Cases for the chosen report types
        // In each, the SQL search has different specifications
        // Each return then delivers different data that will be used to form the data ouput
        switch($relType){

            case "1": // Report Type: Who Access
                
                $holdertype = $_POST['cmbHoldertype'];
                $data_type = $_POST['cmbDatatype'];
                $storage = $_POST['cmbStorage'];
                
                //Get idtypeperson of LGP_personhasaccess by the name value
                $search_idtype = $this->dbReport->getLgpTypePerson("LGP_personhasaccess");
                
                $tmpuser_id = $search_idtype['data'][0]['idtypeperson'];

                //If the variable $holdertype is equal to "ALL", will not be included in the query
                $where = $holdertype == "ALL" ? "": "WHERE a.idtipotitular = $holdertype"; 

                //If the datatype if equal to "ALL", will not be included in the query
                //If the variable $where value, until here, is empty, her value will be WHERE, else, will be AND
                $where .= $data_type == "ALL" ? "":  ($where == "" ? "WHERE a.idtipodado = $data_type" : " AND a.idtipodado = $data_type"); 

                //If the storage is equal to "ALL", will not be included in the query with a specific value
                //If the variable $where value, until here, is empty, her value will be WHERE, else, will be AND
                $where .= $storage == "ALL" ? "":  ($where == "" ?  "WHERE k.idarmazenamento = $storage" : " AND k.idarmazenamento = $storage");
                
                //If the value is different from "ALL", will be something like number | letter, being letter possibilities "P" or "G"
                //So, in this case, the string is broken with explode native funcion, turning into array like [number, letter]
                if($_POST['cmbWhoaccess'] != "ALL"){
                    $who_access = explode("|", $_POST['cmbWhoaccess']); //ID|P or ID|G
                    if($who_access[1] == "P"){
                        $rsGroups = $this->dbReport->getPersonGroups("AND d.idperson = {$who_access[0]} ");
                        if (!$rsGroups['success']) {
                            if($this->log)
                                $this->logIt("{$rsGroups['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                            return false;
                        }

                        $groupIDs = "";
                        foreach($rsGroups['data'] as $key=>$val){
                            $groupIDs .= "{$val[idgroup]},";
                        }

                        $groupIDs = substr($groupIDs,0,-1);

                        $where .= $where == "" ? "WHERE (p.idperson = {$who_access[0]} AND p.type = '{$who_access[1]}' OR (p.idperson IN($groupIDs) AND p.type = 'G'))" : " AND (p.idperson = {$who_access[0]} AND p.type = '{$who_access[1]}' OR (p.idperson IN($who_access[0]) AND p.type = 'G'))";
                    }else{
                        $where .= $where == "" ? "WHERE p.idperson = {$who_access[0]} AND p.type = '{$who_access[1]}'" : " AND p.idperson = {$who_access[0]} AND p.type = '{$who_access[1]}'";
                    }
                }else{
                    $who_access = "ALL";
                }
                
                // The variable receives the return from SQL
                $ret = $this->dbReport->getDataMapping($where, $order, $limit, $tmpuser_id);

                //print_r($ret); die();

                if (!$ret['success']) {
                    if($this->log)
                        $this->logIt("{$ret['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }

                //Subheader
                // If it is equal to "ALL" the value will be "All", if not the name of the field brought by SQL
                $subcab = array(
                    "{$this->getLanguageWord('lgp_type')}"=> $this->getLanguageWord('lgp_whoaccess'),
                    "{$this->getLanguageWord('lgp_holdertype')}"=> $holdertype == "ALL" ? "{$this->getLanguageWord('all')}" : $ret['data'][0]['tipotitular'],
                    "{$this->getLanguageWord('lgp_datatype')}"=> $data_type == "ALL" ? "{$this->getLanguageWord('all')}" : $ret['data'][0]['tipo'],
                    "{$this->getLanguageWord('lgp_storage')}"=> $storage == "ALL" ? "{$this->getLanguageWord('all')}" : $ret['data'][0]['armazenamento'],
                    "{$this->getLanguageWord('lgp_whoaccess')}"=> $who_access == "ALL" ? "{$this->getLanguageWord('all')}" : $ret['data'][0]['personacc']
                );

                //If not data is return by the search
                if($ret['data'] == NULL){
                    
                    // Modal "Record not found"
                    echo json_encode(false);

                }else{

                    // Create the table using the data gathered by the research
                    $table_data = $this->defineTable($arrType, $ret['data'], $subcab);

                    // Returns, for Ajax, the final content of the concatenation performed in the defineTable() method
                    // The final product is a string of the contents of the table body
                    echo json_encode($table_data);

                }


            break;

            case "2": // Report Type: Shared

                $holdertype = $_POST['cmbHoldertype'];
                $data_type = $_POST['cmbDatatype'];
                $storage = $_POST['cmbStorage'];
                $shared_whom_id = $_POST['cmbSharedWhom'];

                
                //echo "$holdertype | $data_type | $storage | $shared_whom_id"; die();

                //Get idtypeperson of LGP_personhasaccess by the name value
                $search_idtype = $this->dbReport->getLgpTypePerson("LGP_personhasaccess");
                
                $tmpuser_id = $search_idtype['data'][0]['idtypeperson'];

                //Se $holdertype for ALL não é incluido, se não recebe WHERE
                $where = $holdertype == "ALL" ? "": "WHERE a.idtipotitular = $holdertype"; 

                //Se o datatype for ALL não é incluido, se não, se o where for vazio recebe WHERE, se não recebe AND
                $where .= $data_type == "ALL" ? "":  ($where == "" ? "WHERE a.idtipodado = $data_type" : " AND a.idtipodado = $data_type"); 

                //Se o storage for ALL não é incluido, se não, se o where for vazio recebe WHERE, se não recebe AND
                $where .= $storage == "ALL" ? "":  ($where == "" ?  "WHERE k.idarmazenamento = $storage" : " AND k.idarmazenamento = $storage");

                //If the $shared_whom_id is equal to "ALL", will not be included in the query
                //If is not equal to all, if where is empty, receives WHERE, else, receives AND
                $where .= $shared_whom_id == "ALL" ? "": ($where == "" ?  "WHERE j.idperson = '$shared_whom_id'" : " AND j.idperson = '$shared_whom_id'");

                // The variable receives the return from SQL
                $ret = $this->dbReport->getDataMapping($where, $order, $limit, $tmpuser_id);

                //print_r($ret); die();

                if (!$ret['success']) {
                    if($this->log)
                        $this->logIt("{$ret['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }

                //Subheader
                // If it is equal to "ALL" the value will be "All", if not the name of the field brought by SQL
                $subcab = array(
                    "{$this->getLanguageWord('lgp_type')}"=> $this->getLanguageWord('lgp_sharedwhom'),
                    "{$this->getLanguageWord('lgp_holdertype')}"=> $holdertype == "ALL" ? "{$this->getLanguageWord('all')}" : $ret['data'][0]['tipotitular'],
                    "{$this->getLanguageWord('lgp_datatype')}"=> $data_type == "ALL" ? "{$this->getLanguageWord('all')}" : $ret['data'][0]['tipo'],
                    "{$this->getLanguageWord('lgp_storage')}"=> $storage == "ALL" ? "{$this->getLanguageWord('all')}" : $ret['data'][0]['armazenamento'],
                    "{$this->getLanguageWord('lgp_sharedwhom')}"=> $shared_whom_id == "ALL" ? "{$this->getLanguageWord('all')}" : $shared_whom_name['data'][0]['operador']
                );

                 //If not data is return by the search
                 if($ret['data'] == NULL){
                    
                    // Modal "Record not found"
                    echo json_encode(false);

                }else{

                    // Create the table using the data gathered by the research
                    $table_data = $this->defineTable($arrType, $ret['data'], $subcab);

                    // Returns, for Ajax, the final content of the concatenation performed in the defineTable() method
                    // The final product is a string of the contents of the table body
                    echo json_encode($table_data);

                }

            break;

        }


    }

    public function defineTable($arrType = array(), $data_search = array(), $subheader = array()){

        switch($arrType['report_type']){

            case "1": // Construction of the table "Summarized by company"

                // First, the variable receives a fixed header
                $tr_list .= "<tr>
                <th>{$this->getLanguageWord('lgp_holdertype')}</th>
                <th>{$this->getLanguageWord('lgp_data')}</th>
                <th>{$this->getLanguageWord('lgp_type')}</th>
                <th>{$this->getLanguageWord('lgp_forwhat')}</th>
                <th>{$this->getLanguageWord('lgp_format')}</th>
                <th>{$this->getLanguageWord('lgp_formofcollection')}</th>
                <th>{$this->getLanguageWord('lgp_legalbase')}</th>
                <th>{$this->getLanguageWord('lgp_storage')}</th>
                <th>{$this->getLanguageWord('lgp_shared')}</th>
                </tr>";

                $arrDataRow = array();
                // Construction inside the loop

                //Variable "row" takes the line, and "record" takes the record itself - from the line
                foreach($data_search as $row => $record){

                    $holdertype = $record['tipotitular'];
                    $name = $record['nome'];
                    $type = $record['tipo'];
                    $for_what = $record['finalidade'];
                    $format = $record['formato'];
                    $form_collection = $record['forma'];
                    $legal_base = $record['base'];
                    $storage = $record['armazenamento'];
                    $who_access = $record['personacc'];
                    $shared = $record['compartilhado'];

                    $tr_list .= "<tr><td class = 'text-left'>{$holdertype}</td>
                    <td class = 'text-left'>{$name}</td>
                    <td class = 'text-left'>{$type}</td>
                    <td class = 'text-left'>{$for_what}</td>
                    <td class = 'text-left'>{$format}</td>
                    <td class = 'text-left'>{$form_collection}</td>^
                    <td class = 'text-left'>{$legal_base}</td>
                    <td class = 'text-left'>{$storage}</td>
                    <td class = 'text-left'>{$shared}</td>";

                    array_push($arrDataRow, array($holdertype, $name, $type, 
                    $for_what, 
                    $format, 
                    $form_collection, 
                    $legal_base,
                    $storage, 
                    $who_access, 
                    $shared));

                }
    
                // This session is created so that the data can be used in the Export method
                // If not, the data would have to be returned to Ajax, and from itself back to PHP
                $_SESSION['reportDataPDF'] =  array(
                    "pdf_title" => "{$this->getLanguageWord('lgp_datamapping')}",
                    "subheader" => $subheader,
                    "tabhead" => array("{$this->getLanguageWord('lgp_holdertype')}","{$this->getLanguageWord('lgp_data')}","{$this->getLanguageWord('lgp_type')}",
                    "{$this->getLanguageWord('lgp_forwhat')}","{$this->getLanguageWord('lgp_format')}","{$this->getLanguageWord('lgp_formofcollection')}","{$this->getLanguageWord('lgp_legalbase')}",
                    "{$this->getLanguageWord('lgp_storage')}","{$this->getLanguageWord('lgp_whoaccess')}","{$this->getLanguageWord('lgp_sharedabbrev')}"),
                    "data_rows" => $arrDataRow,
                    "wth" => array(22,25,15,30,20,30,30,33,40,10),
                    "orientation" => "L",
                    "wLine" => 285,
                    "wh2" => 270,
                    "wrow" => array(22,25,15,30,20,30,30,33,40,10),
                    "alignrow" => array("L","L","L","L","L","L","L","L","L","C")
                );

            break;

            case '2':

                    // First, the variable receives a fixed header
                    $tr_list .= "<tr>
                    <th class = 'col-sm-2'>{$this->getLanguageWord('lgp_holdertype')}</th>
                    <th class = 'col-sm-2'>{$this->getLanguageWord('lgp_data')}</th>
                    <th class = 'col-sm-2'>{$this->getLanguageWord('lgp_type')}</th>
                    <th class = 'col-sm-2'>{$this->getLanguageWord('lgp_forwhat')}</th>
                    <th class = 'col-sm-2'>{$this->getLanguageWord('lgp_format')}</th>
                    <th class = 'col-sm-2'>{$this->getLanguageWord('lgp_formofcollection')}</th>
                    <th class = 'col-sm-1' style = 'max-width: 120px;'>{$this->getLanguageWord('lgp_legalbase')}</th>
                    <th class = 'col-sm-2'>{$this->getLanguageWord('lgp_storage')}</th>
                    <th class = 'col-sm-2'>{$this->getLanguageWord('lgp_shared')}</th>
                    <th class = 'col-sm-2'>{$this->getLanguageWord('lgp_sharedwhom')}</th>
                    </tr>";

                    $arrDataRow = array();
                    // Construction inside the loop

                    //Variable "row" takes the line, and "record" takes the record itself - from the line
                    foreach($data_search as $row => $record){

                    $holdertype = $record['tipotitular'];
                    $name = $record['nome'];
                    $type = $record['tipo'];
                    $for_what = $record['finalidade'];
                    $format = $record['formato'];
                    $form_collection = $record['forma'];
                    $legal_base = $record['base'];
                    $storage = $record['armazenamento'];
                    $shared = $record['compartilhado'];
                    $whom_shared = $record['operador'];
                    
                    //Se não for compatilhado, não é listado
                    if($shared == "S"){

                        $tr_list .= "<tr><td class = 'text-left col-sm-2'>{$holdertype}</td>
                        <td class = 'text-left col-sm-2'>{$name}</td>
                        <td class = 'text-left col-sm-2'>{$type}</td>
                        <td class = 'text-left col-sm-2'>{$for_what}</td>
                        <td class = 'text-left col-sm-2'>{$format}</td>
                        <td class = 'text-left col-sm-2'>{$form_collection}</td>^
                        <td class = 'text-left col-sm-1' style = 'max-width: 120px; word-wrap: break-word;'>{$legal_base}</td>
                        <td class = 'text-left col-sm-2'>{$storage}</td>
                        <td class = 'text-center col-sm-2'>{$shared}</td>
                        <td class = 'text-left col-sm-2'>{$whom_shared}</td>";

                        // This array receives the data used in the table data of the table
                        array_push($arrDataRow, array($holdertype, $name, $type, 
                        $for_what, 
                        $format, 
                        $form_collection, 
                        $legal_base,
                        $storage, 
                        $shared, 
                        $whom_shared));

                    }
    
                    // This session is created so that the data can be used in the Export method
                    // If not, the data would have to be returned to Ajax, and from itself back to PHP
                    $_SESSION['reportDataPDF'] =  array(
                        "pdf_title" => "{$this->getLanguageWord('lgp_datamapping')}",
                        "subheader" => $subheader,
                        "tabhead" => array("{$this->getLanguageWord('lgp_holdertype')}","{$this->getLanguageWord('lgp_data')}","{$this->getLanguageWord('lgp_type')}",
                        "{$this->getLanguageWord('lgp_forwhat')}","{$this->getLanguageWord('lgp_format')}","{$this->getLanguageWord('lgp_formofcollection')}","{$this->getLanguageWord('lgp_legalbase')}",
                        "{$this->getLanguageWord('lgp_storage')}","{$this->getLanguageWord('lgp_shared')}","{$this->getLanguageWord('lgp_sharedwhom')}"),
                        "data_rows" => $arrDataRow,
                        "wth" => array(22,25,15,30,20,30,30,33,20,40),
                        "orientation" => "L",
                        "wLine" => 285,
                        "wh2" => 270,
                        "wrow" => array(22,25,15,30,20,30,30,33,20,40),
                        "alignrow" => array("L","L","L","L","L","L","L","L","C","L")
                    );


                    }

            break;
        
        }

        // Return of the built table, in either case
        return $tr_list;

    }

    public function exportReport(){

        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        // Recovery of data saved in the session
        // Each case built a different table, and a session with data for each circumstance
        // Thus, the session variable was used to allow the export of data for each circumstance
        $arrReportData = $_SESSION['reportDataPDF'];
        
        switch ($_POST['typeFile']) {

            //if the export choice was "CSV"
            // In this case, only data from table data (td) are sent
            case "CSV": 
            
                $fileNameUrl = $this->makeCsvFile($arrReportData['data_rows']);

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
            
            //if the export choice was "PDF"
            // Calling the method of building the PDF, sending the composition data from the table
            // In this case, the dimension data from the table is also sent
            case "PDF": 

                echo $this->makePdfFile($arrReportData); 
                break;
        }

    }

    public function makeCsvFile($arrReportData){

        $csv = array();

        foreach ($arrReportData as $row=>$val) {
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

    public function makePdfFile($rowsData){

        // class FPDF with extension to parsehtml
        // Create a instance of library
        $pdf = $this->_returnfpdfhdk();

        //Font parameters to be used in the report.
        $FontFamily = 'Arial';
        $FontStyle  = '';
        $FontSize   = 8;
        $CelHeight = 4;

        $title =  html_entity_decode(utf8_decode($rowsData['pdf_title']),ENT_QUOTES, "ISO8859-1"); //Title //$rowsData['titulo1']?
        $PdfPage = (utf8_decode($this->getLanguageWord('PDF_Page'))) ; //Page numbering
        $leftMargin = 10; 

        $logo = array("file" => $this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage(),
            "posx" => $leftMargin + 10,
            "posy" => 8
        );

        //PRODUÇÃO DO PDF COM OS DADOS
        //Cycles through the data array that forms the subheader
        //Its accessible via the "subcabecalho" key of the session variable data structure
        foreach($rowsData['subheader'] as $key => $value){

            $h2[$key] = array(

                "txt"=>html_entity_decode(utf8_decode("{$key}: {$value}"),ENT_QUOTES, "ISO8859-1"), 
                "width"=>$rowsData['wh2'],
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>1,
                "fill"=>0,
                "align" => 'L');

        }

        // Number of headers
        $table_total_col = count($rowsData['data_rows'][0]); 
        // Dynamic header configuration
        for($tb_th = 0; $tb_th < $table_total_col; $tb_th++){

            //$rowsData['tabhead'] contains an indexed array with the names of the headers
            $th[$tb_th] = array("txt"=>html_entity_decode(utf8_decode($rowsData['tabhead'][$tb_th]),ENT_QUOTES, "ISO8859-1"),
                    "width"=> $rowsData['wth'][$tb_th], 
                    "height" => $CelHeight,
                    "border" => 0,
                    "ln"=> $tb_th == ($table_total_col-1) ? 1: 0, //If it is the last column, it gets 1, if not, it gets 0
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

        foreach($rowsData['data_rows'] as $linha => $registro){

            // "Normal" lines are created for listing items
            $pdf->setRowFillColor(205,205,205);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell($leftMargin);

            // Now we go through the data array of the current position
            foreach($registro as $campo => $valor){ 
                
                //A variável $valor pega o valor da atual coluna/célula/campo
                
                //Se o valor atual tiver palavras separadas por vírgula, troque-as por quebras de linha
                //Se não, apenas considere o valor como está
                $td[$campo] = utf8_decode(strpos($valor, ",") ? str_replace(",", "",  $valor) :  $valor);
        
            }

            //print_r($td); die();
         
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


}