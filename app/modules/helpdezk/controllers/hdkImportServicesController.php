<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 14/10/2019
 * Time: 17:54
 */

require_once(HELPDEZK_PATH . '/app/modules/helpdezk/controllers/hdkCommonController.php');

class hdkImportServices extends hdkCommon
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

        $this->modulename = 'helpdezk';
        $this->idmodule = $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('hdkImportServices');

        $this->loadModel('service_model');
        $this->dbService = new service_model();

        $this->loadModel('groups_model');
        $this->dbGroups = new groups_model();

        $this->loadModel('admin/person_model');
        $this->dbPerson = new person_model();

        $this->loadModel('groups_model');
        $this->dbGroup = new groups_model();

        $this->loadModel('ticketrules_model');
        $this->dbTicketRules = new ticketrules_model();


        $this->_areas = $this->makeArrayAreas();
        $this->_types = $this->makeArrayTypes();
        $this->_itens = $this->makeArrayItens();
        $this->_services = $this->makeArrayServices();
        $this->_groups = $this->makeArrayGroups();

        $arrayRet = $this->makeArrayPrioryties();

        $this->_prioryties = $arrayRet['priority'];
        $this->_prioTime = $arrayRet['time'];
        $this->_prioDefault = $arrayRet['default'];

        $this->_language = $this->getLangVars($this->retornaSmarty());

    }


    public function index()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);


        $this->makeNavVariables($smarty);
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);


        //$tabServices = $this->makeServicesList();
        //$smarty->assign("tabservices",$tabServices);
        $smarty->assign('token', $token) ;

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('import_services.tpl');

    }


    function processFile()
    {

        $type = $this->getParam('type');

        // pipetodo [albandes] : Retornar a mensagem de erro pronta, usando o $this->_language

        $char_search	= array("ã", "á", "à", "â", "é", "ê", "í", "õ", "ó", "ô", "ú", "ü", "ç", "ñ", "Ã", "Á", "À", "Â", "É", "Ê", "Í", "Õ", "Ó", "Ô", "Ú", "Ü", "Ç", "Ñ", "ª", "º", " ", ";", ",");
        $char_replace	= array("a", "a", "a", "a", "e", "e", "i", "o", "o", "o", "u", "u", "c", "n", "A", "A", "A", "A", "E", "E", "I", "O", "O", "O", "U", "U", "C", "N", "_", "_", "_", "_", "_");


        if (!empty($_FILES)) {

            if ($this->_externalStorage) {
                $targetPath = $this->_externalStoragePath . '/tmp/' ;
            } else {
                $targetPath = $this->helpdezkPath . '/app/uploads/tmp/' ;
            }


            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");
            $fileSize = $_FILES['file']['size'];

            $fileName = str_replace($char_search, $char_replace, $fileName);
            $targetFile = $targetPath . $fileName ;

            if(!is_dir($this->targetPath)) {
                mkdir ($this->targetPath, 0777 ); // criar o diretorio
            }

            if(!is_writable($targetPath)) {
                if($this->log)
                    $this->logIt("Target Directory : ".$targetPath.' is not writable - program: '.$this->program ,3,'general',__LINE__);
                echo 'Import_not_writable';
                return false;
            }
            
            if (move_uploaded_file($tempFile,$targetFile)){
                if($this->log)
                    $this->logIt("Save file: ".$targetFile.' - program: '.$this->program ,7,'general',__LINE__);
            }else {
                if($this->log)
                    $this->logIt("Can't save file: ".$targetFile.' - program: '.$this->program ,3,'general',__LINE__);
                echo 'Import_error_file'; // Returns the language file variable, to display de error .
                return false;
            }


            //$this->removeOldImage($type);
            //$this->processImage($fileName,$fileSize,$type);

        }

        $arrayServices = $this->readFile($targetFile);
        if ($arrayServices['error']) {
            if($this->log)
                $this->logIt("Can't import file: ".$targetFile.', layout error - program: '.$this->program ,3,'general',__LINE__);
            echo json_encode($this->makeMessage("ERROR",$arrayServices['message']));
            exit;
        } else {
            $arrayImport = $arrayServices['return'];
        }


        $ret = $this->writeDataBase($arrayImport);

        echo json_encode($ret);

        //print_r($arrayImport);

        //echo 'Success';

        //echo 'Select_country'; // Returns the language file variable, to display the error .

        /*
         https://severalnines.com/database-blog/comparing-cloud-database-options-postgresql
         */

    }

    function writeDataBase($array)
    {

        $lineNumber = 1;
        $this->dbService->BeginTrans();

        foreach ($array as $line) {

            $arrayExplode = explode(';', $line);

            if(count($arrayExplode) > 1 && $arrayExplode[0] != ''){
                $area = $arrayExplode[0];
                $type = $arrayExplode[1];
                $item = $arrayExplode[2];
                $service = $arrayExplode[3];
                $group = $arrayExplode[4];
                $priority = $arrayExplode[5];
                $numberDays = $arrayExplode[6];
                $numberHours = $arrayExplode[7];
                $company = $arrayExplode[8];

                // --- Area ---
                $retArea = $this->saveArea($this->dbService,$area,$lineNumber);
                if(!$retArea['success']){
                    return $retArea['message'];
                }
                $idArea = $retArea['idarea'];
                
                // --- Type ---
                $retType = $this->saveType($this->dbService,$idArea,$type,$lineNumber);
                if(!$retType['success']){
                    return $retType['message'];
                }
                $idType = $retType['idtype'];

                // --- Item ---
                $retItem = $this->saveItem($this->dbService,$idType,$item,$lineNumber);
                if(!$retItem['success']){
                    return $retItem['message'];
                }

                $idItem = $retItem['iditem'];

                // --- Start Priority ---
                if (!array_key_exists($priority, $this->_prioryties)) {// Checks priority, if none exists, use the default
                    $idPriority = $this->_prioDefault;
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". It was associated with default priority, because the priority informed does not exist in the system. Priority: " . $priority, 5, 'general');
                } else {
                    $idPriority = $this->_prioryties[$priority];
                }
                // --- End Priority ---

                // --- Service ---
                $retService = $this->saveService($this->dbService,$idItem,$service,$numberDays,$numberHours,$idPriority,$lineNumber);
                if(!$retService['success']){
                    return $retService['message'];
                }

                $idService = $retService['idservice'];

                // --- Group ---
                $retGroup = $this->saveGroup($this->dbService,$company,$group,$lineNumber);
                if(!$retGroup['success']){
                    return $retGroup['message'];
                }

                $idGroup = $retGroup['idgroup'];

                // --- links service with care group ---
                $retLinkSrvGrp = $this->linkServiceGroup($this->dbService,$idService,$idGroup,$service,$lineNumber);
                if(!$retLinkSrvGrp['success']){
                    return $retLinkSrvGrp['message'];
                }

                // If the layout is 10 columns, we will have the approver name
                if (isset($arrayExplode[9]) AND !empty($arrayExplode[9])) {
                    $retApprOpe = $this->saveApprOperator($this->dbService,$arrayExplode[9],$idItem,$idService,$service,$lineNumber);
                    if(!$retApprOpe['success']){
                        return $retApprOpe['message'];
                    }
                }

                $lineNumber++;
            }
            

        }

        $commit = $this->dbService->CommitTrans();

        return $this->makeMessage('OK',$this->getLanguageWord('Import_services_success'));
    }

    public function saveArea($db,$value,$lineNumber)
    {
        $valSearch = trim(utf8_encode($value)); $value = trim($value);
        if (!array_key_exists($valSearch, $this->_areas)) {
            $db->BeginTrans();
            $rs = $db->selectAreaFromName($valSearch);  // second test is necessary
            if ($rs->RecordCount() == 0) {
                $ret = $db->areaInsert($valSearch);
                
                if ($ret) {
                    $idArea = $db->TableMaxID('hdk_tbcore_area', 'idarea');
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Include area: " . $valSearch . ", idArea = " . $idArea, 5, 'general');
                } else {
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Can't include area: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                    $db->RollbackTrans();
                    $message = $this->_language['Manage_service_area_fail'] . $valSearch . $this->_language['Manage_service_inf_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                    return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
                }
            
                $db->CommitTrans();
            } else {
                $idArea = $rs->fields['idarea'];
                if ($this->log)
                    $this->logIt("Import Services, file line ".$lineNumber.". Area already exists, no need to import: " . $valSearch, 5, 'general');
            }
        } else {
            $idArea = $this->_areas[$valSearch];
            if ($this->log)
                $this->logIt("Import Services. Area already exists, no need to import: " . $value, 5, 'general');
        }
        return array("success"=>true,"idarea"=>$idArea);
    }

    function saveType($db,$idArea,$value,$lineNumber)
    {
        $valSearch = trim(utf8_encode($value)); $value = trim($value);
        if (!isset($this->_types[$idArea]) || !array_key_exists($valSearch, $this->_types[$idArea])) {
            $db->BeginTrans();
            
            $rs = $db->getTypeByName($valSearch,$idArea);  // second test is necessary
            if(!$rs['success']){
                if ($this->log)
                $this->logIt("Import Services, file line " . $lineNumber . ". {$rs['message']} - program: " . $this->program, 3, 'general', __LINE__);
            
                return array("success"=>false,"message"=>$this->makeMessage('ERROR', $this->getLanguageWord('generic_error_msg')));
            }

            if ($rs['data']->RecordCount() == 0) {
                $ins = $db->typeInsert($valSearch, 0, 'A', '1', $idArea);
                if ($ins) {
                    $idType = $db->TableMaxID('hdk_tbcore_type', 'idtype');
                    $this->_types[$idArea][$valSearch] = $idType;
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Include type: " . $valSearch . ", idtype = " . $idType, 5, 'general');
                } else {
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Can't include type: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                    $db->RollbackTrans();
                    $message = $this->_language['Manage_service_type_fail'] . $valSearch . $this->_language['Manage_service_inf_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                    return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
                }
            }else{
                $idType = $rs['data']->fields['idtype'];
                if ($this->log)
                    $this->logIt("Import Services. Type already exists, no need to import: " . $valSearch, 5, 'general');
            }
            
            $db->CommitTrans();
        } else {
            $idType = $this->_types[$idArea][$valSearch];
            if ($this->log)
                $this->logIt("Import Services, file line " . $lineNumber . ". Type already exists, no need to import: " . $valSearch, 5, 'general');
        }

        return array("success"=>true,"idtype"=>$idType);
    }

    function saveItem($db,$idType,$value,$lineNumber)
    {
        $valSearch = trim(utf8_encode($value)); $value = trim($value);
        if (!isset($this->_itens[$idType]) || !array_key_exists($valSearch, $this->_itens[$idType])) {
            
            $rs = $db->getTypeByName($valSearch,$idType);  // second test is necessary
            if(!$rs['success']){
                if ($this->log)
                $this->logIt("Import Services, file line " . $lineNumber . ". {$rs['message']} - program: " . $this->program, 3, 'general', __LINE__);
            
                return array("success"=>false,"message"=>$this->makeMessage('ERROR', $this->getLanguageWord('generic_error_msg')));
            }

            if ($rs['data']->RecordCount() == 0) {
                $ins = $db->insertItem($valSearch, 0, 'A', 0, $idType);
                if (!$ins) {
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Can't include item: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                    
                    $db->RollbackTrans();
                    $message = $this->_language['Manage_service_item_fail'] . $valSearch . $this->_language['Manage_service_inf_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                    return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
                }
                $idItem = $db->TableMaxID('hdk_tbcore_item', 'iditem');
                $this->_itens[$idType][$valSearch] = $idItem;
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Include item: " . $valSearch . ", iditem = " . $idItem, 5, 'general');
            }else{
                $idItem = $rs['data']->fields['iditem'];
                if ($this->log)
                    $this->logIt("Import Services. Type already exists, no need to import: " . $valSearch, 5, 'general');
            }
            
        } else {
            $idItem = $this->_itens[$idType][$valSearch];
            if ($this->log)
                $this->logIt("Import Services, file line " . $lineNumber . ". Item already exists, no need to import: " . $valSearch, 5, 'general');
        }

        return array("success"=>true,"iditem"=>$idItem);
    }

    function saveService($db,$idItem,$value,$numberDays,$numberHours,$idPriority,$lineNumber)
    {
        $value = utf8_encode($value);

        $rs = $db->selectService("WHERE `name` = '$value' and iditem = $idItem");
        if (!$rs) {
            if ($this->log)
                $this->logIt("Import Services, file line " . $lineNumber . ". Failed to determine the service code. Service: " . $value . " - program: " . $this->program, 3, 'general', __LINE__);
            
            $db->RollbackTrans();
            $message = $this->_language['Manage_service_fail_code'] . $value . $this->_language['Manage_service_on_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
            return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
        }

        if ($rs->RecordCount() > 0) {
            if ($this->log)
                $this->logIt("Import Services, file line " . $lineNumber . ". Service already exists, no need to import: " . $value, 5, 'general');
            $idService = $rs->fields['idservice']; // Just read the one already registered (there may be an approval being created now ...).
        } else {

            if (!is_numeric($numberDays)) {
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Column 6 must contain only numeric value. Number of days: " . $numberDays . " - program: " . $this->program, 3, 'general', __LINE__);

                $message = $this->_language['Manage_service_column_6'] . $numberDays . $this->_language['Manage_service_on_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
            }

            $arrayTime = $this->defineServiceTime($numberHours);
            if (!$arrayTime) {
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Unable to identify the priority service time: " . $numberHours . " - program: " . $this->program, 3, 'general', __LINE__);

                $message = $this->_language['Manage_service_not_identify_priority'] . $numberHours . $this->_language['Manage_service_on_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
            }

            $ins = $db->serviceInsert($value, 0, 'A', 0, $idItem, $idPriority, $arrayTime[1], $numberDays, $arrayTime[0]);

            if (!$ins) {
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Can´t include service. Service: " . $value . " - program: " . $this->program, 3, 'general', __LINE__);

                $db->RollbackTrans();
                $message = $this->_language['Manage_service_fail_code'] . $value . $this->_language['Manage_service_on_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));

            }

            $idService = $db->TableMaxID('hdk_tbcore_service', 'idservice');
            if ($this->log)
                $this->logIt("Import Services, file line " . $lineNumber . ". Include service: " . $value . ", idservice = " . $idService, 5, 'general');

        }

        return array("success"=>true,"idservice"=>$idService);
    }

    function saveGroup($db,$company,$group,$lineNumber)
    {
        $group = utf8_encode($group);
        if (!array_key_exists(str_replace(" ", "", $group), $this->_groups)) {

            $idcostumer = $this->dbPerson->selectPersonFromName(trim($company));  // test company
            if (!$idcostumer) {
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Column 8 is required a valid filename company, the company " . $company . ", is not registered! " . " - program: " . $this->program, 3, 'general', __LINE__);
                
                $db->RollbackTrans();
                $message = str_replace("%", $company, $this->_language ['Manage_service_company_fail']);
                return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
            }
            // Include the group name in person table
            $idperson = $this->dbPerson->insertPerson('3', '6', '1', '1', $group, NULL, NULL, 'A', 'N', NULL, NULL, NULL);
            if (!$idperson) {
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Failed to register the group in person table. Group: " . $group . " - program: " . $this->program, 3, 'general', __LINE__);
                
                $db->RollbackTrans();
                $message = $this->_language['Manage_service_group_fail'] . $group . $this->_language['Manage_service_inf_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
            }

            $level = 2;
            $rsGrp = $this->dbGroup->insertGroup($idperson, $level, $idcostumer, 'N');
            if (!$rsGrp) {
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Failed to register the service group. Group: " . $group . " - program: " . $this->program, 3, 'general', __LINE__);

                $db->RollbackTrans();
                $message = $this->_language['Manage_service_group_fail2'] . $group . $this->_language['Manage_service_inf_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
            }
            //$codGrupo =  $db_grp->InsertID() ;
            $idGroup = $db->TableMaxID('hdk_tbgroup', 'idgroup');
            $this->_groups[str_replace(" ", "", $group)] = $idGroup;
            if ($this->log)
                $this->logIt("Import Services, file line " . $lineNumber . ". Include group: " . $group . ", idtgroup = " . $idGroup, 5, 'general');
        } else {
            $idGroup = $this->_groups[str_replace(" ", "", $group)];
            if ($this->log)
                $this->logIt("Import Services, file line " . $lineNumber . ". Group already exists, no need to import: " . $group, 5, 'general');
        }

        return array("success"=>true,"idgroup"=>$idGroup);
    }

    function linkServiceGroup($db,$idService,$idGroup,$service,$lineNumber)
    {
        $checkSrvGrp = $db->getServiceGroup("WHERE idservice = {$idService} AND idgroup = {$idGroup}");
        if(!$checkSrvGrp['success']){
            if ($this->log)
                $this->logIt("Import Services, file line " . $lineNumber . ". {$checkSrvGrp['message']} - program: " . $this->program, 3, 'general', __LINE__);
            
                return array("success"=>false,"message"=>$this->makeMessage('ERROR', $this->getLanguageWord('generic_error_msg')));
        }

        if($checkSrvGrp['data']->RecordCount() <= 0){
            $ins = $db->serviceGroupInsert($idService, $idGroup);
            if (!$ins) {
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Failed to register the relationship between service. Service: " . $idService . " - program: " . $this->program, 3, 'general', __LINE__);
                
                $db->RollbackTrans();
                $message = $this->_language['Manage_service_fail_rel'] . $idService . $this->_language['Manage_service_on_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
            }
        }else{
            if ($this->log)
                $this->logIt("Import Services, file line " . $lineNumber . ". Service link with group already exists, no need to import: " . utf8_encode($service), 5, 'general');
        }

        return array("success"=>true,"message"=>"");
    }

    function saveApprOperator ($db,$operator,$idItem,$idService,$service,$lineNumber)
    {
        $aOperator = explode("|", $operator);

        // Need to test all operators, because I will delete in table hdk_tbapproval_rule.
        foreach ($aOperator as $key => $val) {
            $valSearch = utf8_encode($val);
            $rs = $this->dbPerson->selectPerson("AND tbp.name = '{$valSearch}' and tbtp.idtypeperson = 3");
            if ($rs->RecordCount() == 0) {
                // New Is not registered or is not attending
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". New Is not registered or is not operator. Operator: " . $val . " - program: " . $this->program, 3, 'general', __LINE__);
                
                $db->RollbackTrans();
                $message = $this->_language['Manage_service_not_registered'] . $this->_language['Manage_service_on_line'] . $lineNumber . " " . $this->_language['Manage_service_imp_canceled'];
                return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
            }
        }
        
        // DELETE
        $this->dbTicketRules->BeginTrans();

        $rs_rules = $this->dbTicketRules->deleteUsersApprove($idItem, $idService);
        if (!$rs_rules) {
            $db->RollbackTrans();
            $this->dbTicketRules->RollbackTrans();
            $message = str_replace('%','hdk_tbapproval_rule',$this->_language['delete_from_table_failure']) . $this->_language['Manage_service_on_line'] . $lineNumber . " " . $this->_language['Manage_service_imp_canceled'];
            return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
        }
        
        $j = 1;
        // Add into hdk_tbapproval_rule
        foreach ($aOperator as $key => $val) {
            $valSearch = utf8_encode($val);
            $rs = $this->dbPerson->selectPerson("AND tbp.name = '$valSearch' ");
            $ins = $this->dbTicketRules->insertUsersApprove($idItem, $idService, $rs->fields['idperson'], $j, 0);
            if (!$ins) {
                $db->RollbackTrans();
                $this->dbTicketRules->RollbackTrans();
                $message = str_replace('%','hdk_tbapproval_rule',$this->_language['insert_table_failure']) .". ". $this->_language['Operator'] . ": " . $val . $this->_language['Manage_service_on_line'] . $lineNumber . " " . $this->_language['Manage_service_imp_canceled'];
                return array("success"=>false,"message"=>$this->makeMessage('ERROR', $message));
            } else {
                if ($this->log)
                    $this->logIt('Registered approver "' . $val . '" ( ' . $this->_language['Manage_service_line'] . $lineNumber . '), ' . $this->_language['Manage_service_in_service'] . utf8_encode($service). $this->_language['Mange_service_order'] . " " . $j, 5, 'general');
            }
            $j++;
        }
        $this->dbTicketRules->CommitTrans();

        return array("success"=>true,"message"=>"");
    }

    function readFile($targetFile)
    {

       $error = false;

        $csvData = file_get_contents($targetFile);      // Get the csv data
        $array = explode(PHP_EOL, $csvData);            // separate each line


        foreach ($array as $line) {                     // test number os columns

            $aExplode = explode(';',$line);

            $count = count($aExplode);
            if ($count > 10) {
                $error = true;
            } elseif ($count < 9 && ($count == 1 && $aExplode[0] != '')) {
                $error = true;
            }
        }

        if ($error) {
            $arrayRet = array("error" => true,
                              "message" => $this->getLanguageWord('Import_layout_error'),
                              "return" => ""
            );
        } else {
            $arrayRet = array("error" => false,
                              "message" => "",
                              "return" => $array
            );
        }

        return $arrayRet;

    }

    function makeArrayServices()
    {
        $tmpServices = $this->dbService->selectItemService();
        foreach ($tmpServices as $data) {
            $services[$data['iditem']][$data['name']] = $data['idservice'];
        }
        return $services;
    }

    function makeArrayItens()
    {

        $tmpItens = $this->dbService->selectTypeItem();
        foreach ($tmpItens as $data) {
            $itens[$data['idtype']][$data['name']] = $data['iditem'];
        }
        return $itens ;
    }

    function makeArrayAreas()
    {
        $tmpAreas = $this->dbService->selectAreas();
        foreach ($tmpAreas as $data) {
            $areas[trim($data['name'])] = $data['idarea'];
        }
        return $areas;
    }

    function makeArrayTypes()
    {
        $tmpTypes = $this->dbService->selectAreaType();
        foreach ($tmpTypes as $data) {
            $types[$data['idarea']][$data['name']] = $data['idtype'];
        }
        return $types;
    }

    function makeArrayGroups()
    {
        $rsGroups = $this->dbGroups->selectGroup();
        while (!$rsGroups->EOF) {
            $groups[str_replace(" ", "", $rsGroups->fields['name'])] = $rsGroups->fields['idgroup'];
            $rsGroups->MoveNext();
        }
        return $groups;
    }

    function makeArrayPrioryties()
    {
        $allPrio =  $this->dbService->selectPriorityData();

        foreach ($allPrio as $prio) {
            $prioridades[$prio['name']] = $prio['idpriority'];
            $tempoPrioridades[$prio['idpriority']] = array('DIAS' => $prio['limit_days'], 'HORAS' => $prio['limit_hours']);
            if ($prio['def'] == 1) {
                $prioridadePadrao = $prio['idpriority'];
            }
        }

        $arrayRet = array
        (
            'priority' => $prioridades,
            'time'     => $tempoPrioridades,
            'default'  => $prioridadePadrao
        );

        return $arrayRet;
    }

    function cleanEncode($str)
    {
        return utf8_encode(trim(addslashes($str)));
    }

    function makeMessage($status,$message)
    {
        $aRet = array(
            "status" => $status,
            "message" => $message
        );
        return $aRet;
    }

    function defineServiceTime($str)
    {
        $str = strtoupper($str);
        $time = preg_match("/[H-M]/", $str);

        if (strpos($str, 'H') === false) {
            if (strpos($str, 'M') === false) {
                return false ;
            } else {
                $pos = strpos($str, 'M') ;
            }
        } else {
            $pos = strpos($str, 'H') ;
        }
        $ind_hours_minutes = substr($str, -1);
        $hours_attendance = substr($str, 0,$pos);
        if(!$hours_attendance) $hours_attendance = 0;
        return array($hours_attendance,  $ind_hours_minutes);
    }

}

