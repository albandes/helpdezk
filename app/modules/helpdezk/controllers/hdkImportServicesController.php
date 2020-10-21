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
            echo $arrayServices['message'] ;
            return false ;
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

        //$message = str_replace("%", 'companhia teste', $this->_language ['Manage_service_company_fail'] );
        //$message = $this->_language['Manage_service_area_fail'] .  'area teste' . $this->_language['Manage_service_inf_line'] . '12' . $this->_language['Manage_service_imp_canceled'];
        //return $this->makeMessage('ERROR',$message);

        $lineNumber = 1;
        $this->dbService->BeginTrans();

        foreach ($array as $line) {

            $arrayExplode = explode(';', $line);
            //$arrayExplode = array_map('cleanEncode', $arrayExplode);

            $area = $arrayExplode[0];
            $type = $arrayExplode[1];
            $item = $arrayExplode[2];
            $service = $arrayExplode[3];
            $group = $arrayExplode[4];
            $priority = $arrayExplode[5];
            $numberDays = $arrayExplode[6];
            $numberHours = $arrayExplode[7];


            $company = $arrayExplode[8];


            //$idArea = $this->saveArea($this->dbService,$area,$i);
            //if(!$idArea) return false ;

            //$idType = $this->saveType($this->dbService,$idArea,$item,$i);
            //if(!$idType) return false ;

            // Start Area
            if (!array_key_exists($area, $this->_areas)) {
                $rs = $this->dbService->selectAreaFromName(trim($area));  // second test is necessary
                if ($rs->RecordCount() == 0) {
                    $ret = $this->dbService->areaInsert($area);
                    if ($ret) {
                        $idArea = $this->dbService->TableMaxID('hdk_tbcore_area', 'idarea');
                        if ($this->log)
                            $this->logIt("Import Services, file line " . $lineNumber . ". Include area: " . $area . ", idArea = " . $idArea, 5, 'general');
                    } else {
                        $this->dbService->RollbackTrans();
                        if ($this->log)
                            $this->logIt("Import Services, file line " . $lineNumber . ". Can't include area: " . $area . " - program: " . $this->program, 3, 'general', __LINE__);
                        $message = $this->_language['Manage_service_area_fail'] . $area . $this->_language['Manage_service_inf_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                        return $this->makeMessage('ERROR', $message);
                    }
                } else {
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Area already exists, no need to import: " . $area, 5, 'general');
                }
            } else {
                $idArea = $this->_areas[$area];
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Area already exists, no need to import: " . $area, 5, 'general');
            }
            // - End Area

            // - Start type
            if (!isset($this->_types[$idArea]) || !array_key_exists($type, $this->_types[$idArea])) {
                $ins = $this->dbService->typeInsert($type, 0, 'A', '1', $idArea);
                if ($ins) {
                    $idType = $this->dbService->TableMaxID('hdk_tbcore_type', 'idtype');
                    $this->_types[$idArea][$type] = $idType;
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Include type: " . $type . ", idtype = " . $idType, 5, 'general');
                } else {
                    $this->dbService->RollbackTrans();
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Can't include type: " . $type . " - program: " . $this->program, 3, 'general', __LINE__);
                    $message = $this->_language['Manage_service_type_fail'] . $type . $this->_language['Manage_service_inf_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                    return $this->makeMessage('ERROR', $message);
                }
            } else {
                $idType = $this->_types[$idArea][$type];
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Type already exists, no need to import: " . $type, 5, 'general');
            }
            // - End type

            // - Start Item
            if (!isset($this->_itens[$idType]) || !array_key_exists($item, $this->_itens[$idType])) {
                $ins = $this->dbService->insertItem($item, 0, 'A', 0, $idType);
                if (!$ins) {
                    $this->dbService->RollbackTrans();
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Can't include item: " . $item . " - program: " . $this->program, 3, 'general', __LINE__);
                    $message = $this->_language['Manage_service_item_fail'] . $item . $this->_language['Manage_service_inf_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                    return $this->makeMessage('ERROR', $message);
                }
                $idItem = $this->dbService->TableMaxID('hdk_tbcore_item', 'iditem');
                $this->_itens[$idType][$item] = $idItem;
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Include item: " . $item . ", iditem = " . $idItem, 5, 'general');
            } else {
                $idItem = $this->_itens[$idType][$item];
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Item already exists, no need to import: " . $item, 5, 'general');
            }
            // - End Item

            // - Start Group
            if (!array_key_exists(str_replace(" ", "", $group), $this->_groups)) {

                $idcostumer = $this->dbPerson->selectPersonFromName($company);  // test company
                if (!$idcostumer) {
                    $this->dbService->RollbackTrans();
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Column 8 is required a valid filename company, the company " . $company . ", is not registered! " . " - program: " . $this->program, 3, 'general', __LINE__);
                    $message = str_replace("%", $company, $this->_language ['Manage_service_company_fail']);
                    return $this->makeMessage('ERROR', $message);
                }
                // Include the group name in person table
                $idperson = $this->dbPerson->insertPerson('3', '6', '1', '1', $group, NULL, NULL, 'A', 'N', NULL, NULL, NULL);
                if (!$idperson) {
                    $this->dbService->RollbackTrans();
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Failed to register the group in person table. Group: " . $group . " - program: " . $this->program, 3, 'general', __LINE__);
                    $message = $this->_language['Manage_service_group_fail'] . $group . $this->_language['Manage_service_inf_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                    return $this->makeMessage('ERROR', $message);
                }

                $level = 2;
                $rsGrp = $this->dbGroup->insertGroup($idperson, $level, $idcostumer, 'N');
                if (!$rsGrp) {
                    $this->dbService->RollbackTrans();
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Failed to register the service group. Group: " . $group . " - program: " . $this->program, 3, 'general', __LINE__);

                    $message = $this->_language['Manage_service_group_fail2'] . $group . $this->_language['Manage_service_inf_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                    return $this->makeMessage('ERROR', $message);
                }
                //$codGrupo =  $db_grp->InsertID() ;
                $idGroup = $this->dbService->TableMaxID('hdk_tbgroup', 'idgroup');
                $this->_groups[str_replace(" ", "", $group)] = $idGroup;
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Include group: " . $group . ", idtgroup = " . $idGroup, 5, 'general');
            } else {
                $idGroup = $this->_groups[str_replace(" ", "", $group)];
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Group already exists, no need to import: " . $group, 5, 'general');
            }
            // - End Group

            // - Start Priority

            if (!array_key_exists($priority, $this->_prioryties)) {             // Checks priority, if none exists, use the default
                $idPriority = $this->_prioDefault;
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". It was associated with default priority, because the priority informed does not exist in the system. Priority: " . $priority, 5, 'general');


            } else {
                $idPriority = $this->_prioryties[$priority];
            }
            // - End Priority

            // - Start Service

            //$name = $dados[3] ;

            $rs = $this->dbService->selectService("WHERE `name` = '$service' and iditem = $idItem");
            if (!$rs) {
                $this->dbService->RollbackTrans();
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Failed to determine the service code. Service: " . $service . " - program: " . $this->program, 3, 'general', __LINE__);

                $message = $this->_language['Manage_service_fail_code'] . $service . $this->_language['Manage_service_on_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                return $this->makeMessage('ERROR', $message);
            }

            if ($rs->RecordCount() > 0) {
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Service already exists, no need to import: " . $service, 5, 'general');
                $idservice = $rs->fields['idservice']; // Just read the one already registered (there may be an approval being created now ...).
            } else {

                if (!is_numeric($numberDays)) {
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Column 6 must contain only numeric value. Number of days: " . $numberDays . " - program: " . $this->program, 3, 'general', __LINE__);

                    $message = $this->_language['Manage_service_column_6'] . $numberDays . $this->_language['Manage_service_on_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                    return $this->makeMessage('ERROR', $message);
                }

                // pipetodo : Parei aqui .
                $arrayTime = $this->defineServiceTime($numberHours);
                if (!$arrayTime) {
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Unable to identify the priority service time: " . $numberHours . " - program: " . $this->program, 3, 'general', __LINE__);

                    $message = $this->_language['Manage_service_not_identify_priority'] . $numberHours . $this->_language['Manage_service_on_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                    return $this->makeMessage('ERROR', $message);
                }

                $ins = $this->dbService->serviceInsert($service, 0, 'A', 0, $idItem, $idPriority, $arrayTime[1], $numberDays, $arrayTime[0]);

                if (!$ins) {
                    $this->dbService->RollbackTrans();
                    if ($this->log)
                        $this->logIt("Import Services, file line " . $lineNumber . ". Can´t include service. Service: " . $service . " - program: " . $this->program, 3, 'general', __LINE__);

                    $message = $this->_language['Manage_service_fail_code'] . $service . $this->_language['Manage_service_on_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                    return $this->makeMessage('ERROR', $message);

                }

                $idService = $this->dbService->TableMaxID('hdk_tbcore_service', 'idservice');
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Include service: " . $service . ", idservice = " . $idService, 5, 'general');

            }
            // - End Service

            /*
             * links service with care group
             */
            $ins = $this->dbService->serviceGroupInsert($idService, $idGroup);
            if (!$ins) {
                $this->dbService->RollbackTrans();
                if ($this->log)
                    $this->logIt("Import Services, file line " . $lineNumber . ". Failed to register the relationship between service. Service: " . $idService . " - program: " . $this->program, 3, 'general', __LINE__);
                $message = $this->_language['Manage_service_fail_rel'] . $idService . $this->_language['Manage_service_on_line'] . $lineNumber . $this->_language['Manage_service_imp_canceled'];
                return $this->makeMessage('ERROR', $message);
            }

            // If the layout is 9 columns, we will have the approver name
            if (isset($arrayExplode[9]) AND !empty($arrayExplode[9])) {

                //$aOperator = explode("|",$dados[9]);
                $aOperator = explode("|", $arrayExplode[9]);
                //$db_per = new person_model();

                // Need to test all operators, because I will delete in table hdk_tbapproval_rule.
                foreach ($aOperator as $key => $val) {
                    $rs = $this->dbPerson->selectPerson("AND tbp.name = '$val' and tbtp.idtypeperson = 3");
                    if ($rs->RecordCount() == 0) {
                        // New Is not registered or is not attending
                        $this->dbService->RollbackTrans();
                        if ($this->log)
                            $this->logIt("Import Services, file line " . $lineNumber . ". New Is not registered or is not operator. Operator: " . $val . " - program: " . $this->program, 3, 'general', __LINE__);
                        $message = $this->_language['Manage_service_not_registered'] . $lineNumber . " " . $this->_language['Manage_service_imp_canceled'];
                        return $this->makeMessage('ERROR', $message);
                    }
                }
                // DELETAR

                //$db_rules = new requestrules_model();
                //$db_rules->BeginTrans();
                $this->dbTicketRules->BeginTrans();


                $rs_rules = $this->dbTicketRules->deleteUsersApprove($idItem, $idService);
                if (!$rs_rules) {
                    $this->dbService->RollbackTrans();
                    $this->dbTicketRules->RollbackTrans();
                    die('Falha ao excluir ma tabela hdk_tbapproval_rule. Linha ' . $i . $langVars['Manage_service_imp_canceled']);
                }
                $j = 1;
                // Incluir na tabela hdk_tbapproval_rule
                foreach ($aOperator as $key => $val) {
                    $rs = $db_per->selectPerson("AND tbp.name = '$val' ");
                    $ins = $db_rules->insertUsersApprove($codItem, $idservice, $rs->fields['idperson'], $j, 0);
                    if (!$ins) {
                        $DB->RollbackTrans();
                        $db_rules->RollbackTrans();
                        die('Erro ao gravar na tabela hdk_tbapproval_rule. Atendente ' . $val . 'Linha ' . $i);
                    } else {
                        echo 'Cadastrado aprovador "' . $val . '" ( ' . $langVars['Manage_service_line'] . $i . '), ' . $langVars['Manage_service_in_service'] . $dados[3] . $langVars['Mange_service_order'] . " " . $j . '<br>';
                    }
                    $j++;
                }
                $db_rules->CommitTrans();

                $lineNumber++;
            }

            $commit = $this->dbService->CommitTrans();

        }
    }


    function saveType ($db,$idArea,$value,$lineNumber)
    {

        if (!isset($this->_types[$idArea]) || !array_key_exists($value, $this->_types[$idArea])) {
            $ins = $db->typeInsert($value, 0, 'A', '1', $idArea) ;
            if ($ins) {
                $idType = $db->TableMaxID('hdk_tbcore_type','idtype');
                $this->_types[$idArea][$value] = $idType;
                if ($this->log)
                    $this->logIt("Import Services, file line ".$lineNumber.". Include type: " . $value . ", idtype = " . $idType, 5, 'general');
            } else {
                $db->RollbackTrans();
                if ($this->log)
                    $this->logIt("Import Services, file line ".$lineNumber.". Can't include type: " . $value . ', file line ' . $lineNumber . " - program: " . $this->program, 3, 'general', __LINE__);
                return false;
            }

        } else {
            $idType = $this->_types[$idArea][$value];
            if ($this->log)
                $this->logIt("Import Services, file line ".$lineNumber.". Type already exists, no need to import: " . $value, 5, 'general');

        }

        return $idType;
    }

    public function saveArea($db,$value,$lineNumber)
    {
        if (!array_key_exists($value, $this->_areas)) {
            $rs = $db->selectAreaFromName(trim($value));  // second test is necessary
            if ($rs->RecordCount() == 0) {
                try {
                    $db->areaInsert($value);
                    //$this->dbService->areaInsert($value);

                    //$idArea = $db->TableMaxID('hdk_tbcore_area', 'idarea');
                    $idArea = $this->db->Insert_ID();
                    //$idArea = $this->dbService->TableMaxID('hdk_tbcore_area', 'idarea');
                    if ($this->log)
                        $this->logIt("Import Services, file line ".$lineNumber.". Include area: " . $value . ", idArea = " . $idArea, 5, 'general');
                } catch (Exception $e) {
                    $db->RollbackTrans();
                    if ($this->log)
                        $this->logIt("Import Services, file line ".$lineNumber.". Can't include area: " . $value . ', file line ' . $lineNumber . " - program: " . $this->program, 3, 'general', __LINE__);
                    return false;
                }
                /*
                $ret = $db->areaInsert($value);
                if ($ret) {
                    $idArea = $db->TableMaxID('hdk_tbcore_area', 'idarea');
                    if ($this->log)
                        $this->logIt("Import Services, file line ".$lineNumber.". Include area: " . $value . ", idArea = " . $idArea, 5, 'general');
                } else {
                    $db->RollbackTrans();
                    if ($this->log)
                        $this->logIt("Import Services, file line ".$lineNumber.". Can't include area: " . $value . ', file line ' . $lineNumber . " - program: " . $this->program, 3, 'general', __LINE__);
                    return false;
                }
                */

            } else {
                if ($this->log)
                    $this->logIt("Import Services, file line ".$lineNumber.". Area already exists, no need to import: " . $value, 5, 'general');
            }
        } else {
            $idArea = $this->_areas[$value];
            if ($this->log)
                $this->logIt("Import Services. Area already exists, no need to import: " . $value, 5, 'general');
        }
        return $idArea;
    }

    function readFile($targetFile)
    {

       $error = false;

        $csvData = file_get_contents($targetFile);      // Get the csv data
        $array = explode(PHP_EOL, $csvData);            // separate each line


        foreach ($array as $line) {                     // test number os columns

            $aExplode = explode(';',$line);

            $count = count($aExplode);
            if ($count > 11) {
                $error = true;
            } elseif ($count < 10) {
                $error = true;
            }

        }

        if ($error) {
            $arrayRet = array("error" => true,
                              "message" => "Import_layout_error",
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

