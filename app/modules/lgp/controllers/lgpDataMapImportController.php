<?php

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpDataMapImport extends lgpCommon
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

        $this->modulename = 'LGPD';
        $this->idmodule = $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('lgpDataMapImport');

        $this->saveMode = $this->_s3bucketStorage ? "aws-s3" : 'disk';

        if($this->saveMode == "aws-s3"){
            $bucket = $this->getConfig('s3bucket_name');
            $this->filePath = "https://{$bucket}.s3.amazonaws.com/tmp/";
        }else{
            if($this->_externalStorage) {
                $this->filePath = $this->_externalStoragePath.'/tmp/';
            } else {
                $this->filePath =  $this->helpdezkUrl.'/app/downloads/lgp/';
            }
        }

        $this->loadModel('lgpdatamapping_model');
        $this->dbDataMap = new lgpdatamapping_model();

        $this->_holderType = $this->makeArrayHolderType();
        $this->_types = $this->makeArrayTypes();
        $this->_purposes = $this->makeArrayPurposes();
        $this->_formats = $this->makeArrayFormat();
        $this->_forms = $this->makeArrayForms();
        $this->_legalgrounds = $this->makeArrayLegalGrounds();
        $this->_storages = $this->makeArrayStorages();

    }


    public function index()
    {

        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $this->makeNavVariables($smarty);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('pathToFile', $this->filePath."lgp_import_layout.pdf") ;

        $aRelCompany = $this->_comboCompanies();
        $smarty->assign('companyids', $aRelCompany['ids']); 
        $smarty->assign('companyvals', $aRelCompany['values']); 
        $smarty->assign('idcompany', '');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('lgp-datamap-import.tpl');

    }

    function processFile()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $companyID = $_POST['companyID'];

        $char_search	= array("ã", "á", "à", "â", "é", "ê", "í", "õ", "ó", "ô", "ú", "ü", "ç", "ñ", "Ã", "Á", "À", "Â", "É", "Ê", "Í", "Õ", "Ó", "Ô", "Ú", "Ü", "Ç", "Ñ", "ª", "º", " ", ";", ",");
        $char_replace	= array("a", "a", "a", "a", "e", "e", "i", "o", "o", "o", "u", "u", "c", "n", "A", "A", "A", "A", "E", "E", "I", "O", "O", "O", "U", "U", "C", "N", "_", "_", "_", "_", "_");


        if (!empty($_FILES)) {

            if ($this->_externalStorage) {
                $targetPath = $this->_externalStoragePath . '/tmp/';
            } else {
                $targetPath = $this->helpdezkPath . '/app/uploads/tmp/';
            }


            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");
            $fileSize = $_FILES['file']['size'];

            $fileName = str_replace($char_search, $char_replace, $fileName);
            $targetFile = $targetPath . $fileName ;

            if(!is_dir($targetPath)) {
                $this->logIt('Target Directory: '. $targetPath.' does not exists, I will try to create it. - program: '.$this->program ,6,'general',__LINE__);
                if (!mkdir ($targetPath, 0777 )) {
                    $this->logIt('I could not create the directory: '.$targetPath.' - program: '.$this->program ,3,'general',__LINE__);
                    $msg = str_replace("%",$targetPath,$this->getLanguageWord('enable_create_dir')); 
                    echo json_encode(array("success"=>false,"message"=>"{$msg}"));
                    exit;
                }
            }

            if(!is_writable($targetPath)) {
                if($this->log)
                    $this->logIt("Target Directory: ".$targetPath.' is not writable - program: '.$this->program ,3,'general',__LINE__);

                $msg = str_replace("%",$targetPath,$this->getLanguageWord('target_dir_is_not_writable')); 
                echo json_encode(array("success"=>false,"message"=>"{$msg}"));
                exit;
            }
            
            if (move_uploaded_file($tempFile,$targetFile)){
                if($this->log)
                    $this->logIt("Save file: ".$targetFile.' - program: '.$this->program ,7,'general',__LINE__);
            }else {
                if($this->log)
                    $this->logIt("Can't save file: ".$targetFile.' - program: '.$this->program ,3,'general',__LINE__);
                
                $msg = "{$this->getLanguageWord('cant_save_file')} {$targetFile}";
                echo json_encode(array("success"=>false,"message"=>"{$msg}"));
                exit;
            }

        }

        $arrayData = $this->readFile($targetFile);
        //echo "<pre>",print_r($arrayData,true),"</pre>";
        if ($arrayData['error']) {
            if($this->log)
                $this->logIt("Can't import file: ".$targetFile.', layout error - program: '.$this->program ,3,'general',__LINE__);
            
            echo json_encode(array("success"=>false,"message"=>"{$arrayData['message']}"));
            exit;
        } else {
            $arrayImport = $arrayData['return'];
        }
        
        $ret = $this->writeDataBase($arrayImport,$companyID);
        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't save data from file: ".$targetFile.' - program: '.$this->program ,3,'general',__LINE__);
            echo json_encode(array("success"=>false,"message"=>"{$ret['message']}"));          
        }else{
            echo json_encode(array("success"=>true,"message"=>""));
        }

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
            } elseif ($count < 10 && ($count == 1 && $aExplode[0] != '')) {
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

    function writeDataBase($array,$companyID)
    {

        $lineNumber = 1; 

        foreach ($array as $line) {

            $arrayExplode = explode(';', $line);
            
            if(count($arrayExplode) > 1 && $arrayExplode[0] != ''){
                $dataHolderType = $arrayExplode[0];
                $dataName = $arrayExplode[1];
                $dataType = $arrayExplode[2];
                $dataPurpose = explode('|',$arrayExplode[3]);
                $dataFormat = explode('|',$arrayExplode[4]);
                $dataFormCollect = explode('|',$arrayExplode[5]);
                $dataLegalGround = explode('|',$arrayExplode[6]);
                $dataStorage = explode('|',$arrayExplode[7]);
                $dataPerson = explode('|',$arrayExplode[8]);
                $dataShared = $arrayExplode[9];
                $dataSharedW = explode('|',$arrayExplode[10]);

                // --- Holder Type ---
                $retHolderType = $this->saveHolderType($this->dbDataMap,$dataHolderType,$lineNumber);
                if(!$retHolderType['success']){
                    return $retHolderType;
                }
                $idHolderType = $retHolderType['idholdertype'];
                
                // --- Type ---
                $retType = $this->saveType($this->dbDataMap,$dataType,$lineNumber);
                if(!$retType['success']){
                    return $retType;
                }
                $idType = $retType['idtype'];
                
                // --- Data ---
                $retData = $this->saveData($this->dbDataMap,$idHolderType,$idType,$dataName,$dataShared,$lineNumber);
                if(!$retData['success']){
                    return $retData;
                }

                $idData = $retData['iddata'];

                // --- Purpose ---
                $retPurpose = $this->savePurpose($this->dbDataMap,$idData,$dataPurpose,$lineNumber);
                if(!$retPurpose['success']){
                    return $retPurpose;
                }                

                // --- Collection Format ---
                $retFormat = $this->saveFormat($this->dbDataMap,$idData,$dataFormat,$lineNumber);
                if(!$retFormat['success']){
                    return $retFormat;
                }

                // --- Form of collection ---
                $retFormCollect = $this->saveFormCollect($this->dbDataMap,$idData,$dataFormCollect,$lineNumber);
                if(!$retFormCollect['success']){
                    return $retFormCollect;
                }

                // --- Legal Ground ---
                $retLegalGround = $this->saveLegalGround($this->dbDataMap,$idData,$dataLegalGround,$lineNumber);
                if(!$retLegalGround['success']){
                    return $retLegalGround;
                }

                // --- Storage ---
                $retStorage = $this->saveStorage($this->dbDataMap,$idData,$dataStorage,$lineNumber);
                if(!$retStorage['success']){
                    return $retStorage;
                }

                // --- Person/Group ---
                $retPerson = $this->savePerson($this->dbDataMap,$idData,$dataPerson,$companyID,$lineNumber);
                if(!$retPerson['success']){
                    return $retPerson;
                }

                // If shared
                if ($dataShared == 'S') {
                    $retSharedWith = $this->saveSharedWith($this->dbDataMap,$idData,$dataSharedW,$lineNumber);
                    if(!$retSharedWith['success']){
                        return $retSharedWith;
                    }
                }

                $lineNumber++;
            }
        }
        return array("success"=>true,"message"=>"");
    }

    function makeArrayHolderType()
    {
        $tmpHolderType = $this->dbDataMap->getHolderType();
        if(!$tmpHolderType['success']){
            if($this->log)
                $this->logIt("{$tmpHolderType['message']} - Program: " . $this->program, 3, 'general', __LINE__);
            return false;
        }

        foreach ($tmpHolderType['data'] as $k=>$v) {
            $holderType[trim($v['nome'])] = $v['idtipotitular'];
        }
        return $holderType;
    }

    function makeArrayTypes()
    {
        $tmpTypes = $this->dbDataMap->getType();
        if(!$tmpTypes['success']){
            if($this->log)
                $this->logIt("{$tmpTypes['message']} - Program: " . $this->program, 3, 'general', __LINE__);
            return false;
        }

        foreach ($tmpTypes['data'] as $k=>$v) {
            $types[trim($v['nome'])] = $v['idtipodado'];
        }
        return $types;
    }

    function makeArrayPurposes()
    {
        $tmpPurposes = $this->dbDataMap->getPurpose();
        if(!$tmpPurposes['success']){
            if($this->log)
                $this->logIt("{$tmpPurposes['message']} - Program: " . $this->program, 3, 'general', __LINE__);
            return false;
        }

        foreach ($tmpPurposes['data'] as $k=>$v) {
            $purposes[trim($v['nome'])] = $v['idfinalidade'];
        }
        return $purposes;
    }

    function makeArrayFormat()
    {
        $tmpFormat = $this->dbDataMap->getFormat();
        if(!$tmpFormat['success']){
            if($this->log)
                $this->logIt("{$tmpFormat['message']} - Program: " . $this->program, 3, 'general', __LINE__);
            return false;
        }

        foreach ($tmpFormat['data'] as $k=>$v) {
            $format[trim($v['nome'])] = $v['idformatocoleta'];
        }
        return $format;
    }

    function makeArrayForms()
    {
        $tmpForm = $this->dbDataMap->getCollectForm();
        if(!$tmpForm['success']){
            if($this->log)
                $this->logIt("{$tmpForm['message']} - Program: " . $this->program, 3, 'general', __LINE__);
            return false;
        }

        foreach ($tmpForm['data'] as $k=>$v) {
            $form[trim($v['nome'])] = $v['idformacoleta'];
        }
        return $form;
    }

    function makeArrayLegalGrounds()
    {
        $tmpLegal = $this->dbDataMap->getLegalGround();
        if(!$tmpLegal['success']){
            if($this->log)
                $this->logIt("{$tmpLegal['message']} - Program: " . $this->program, 3, 'general', __LINE__);
            return false;
        }

        foreach ($tmpLegal['data'] as $k=>$v) {
            $legal[trim($v['nome'])] = $v['idbaselegal'];
        }
        return $legal;
    }

    function makeArrayStorages()
    {
        $tmpStorage = $this->dbDataMap->getStorage();
        if(!$tmpStorage['success']){
            if($this->log)
                $this->logIt("{$tmpStorage['message']} - Program: " . $this->program, 3, 'general', __LINE__);
            return false;
        }

        foreach ($tmpStorage['data'] as $k=>$v) {
            $storage[trim($v['nome'])] = $v['idarmazenamento'];
        }
        return $storage;
    }
    
    public function saveHolderType($db,$value,$lineNumber)
    {
        $valSearch = trim(utf8_encode($value)); $value = trim($value);
        //echo "{$valSearch}\n",print_r($this->_classification,true),"\n";
        if (!array_key_exists($valSearch, $this->_classification)) {
            
            $rs = $db->getHolderType("WHERE nome = '{$valSearch}'");  // second test is necessary
            if(!$rs['success']){
                if($this->log)
                    $this->logIt("Error: {$rs['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$this->getLanguageWord('data_holdertype_not_found')}");
            }

            if (count($rs['data']) == 0){                
                $ret = $db->insertHolderType($valSearch);
                if(!$ret['success']){
                    if($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include holder type: " . $valSearch . ". {$ret['message']} - program: " . $this->program, 3, 'general', __LINE__);
                    
                    $msg = "{$this->getLanguageWord('cant_insert_holdertype_data')}: {$valSearch}, {$this->getLanguageWord('Manage_service_inf_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}"; 
                    return array("success"=>false,"message"=>"{$msg}");
                }

                $holderTypeID = $ret['id'];
                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include holder type: " . $valSearch . ", idtipotitular = " . $holderTypeID, 5, 'general');
                
            } else {
                $holderTypeID = $rs['data'][0]['idtipotitular'];
                if ($this->log)
                    $this->logIt("Import Data mapping, file line ".$lineNumber.". Holder type already exists, no need to import: " . $valSearch, 5, 'general');
            }
        } else {            
            $holderTypeID = $this->_holderType[$valSearch];
            if ($this->log)
                $this->logIt("Import Data mapping, file line ".$lineNumber.". Holder type already exists, no need to import: " . $valSearch, 5, 'general');
        }
        return array("success"=>true,"idholdertype"=>$holderTypeID);
    }

    public function saveType($db,$value,$lineNumber)
    {
        $valSearch = trim(utf8_encode($value)); $value = trim($value);
        //echo "{$valSearch}\n",print_r($this->_types,true),"\n";
        if (!array_key_exists($valSearch, $this->_types)) {
            
            $rs = $db->getType("WHERE nome = '{$valSearch}'");  // second test is necessary
            if(!$rs['success']){
                if($this->log)
                    $this->logIt("Error: {$rs['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$this->getLanguageWord('data_type_not_found')}");
            }

            if (count($rs['data']) == 0) {               
                $ret = $db->insertType($valSearch);
                if(!$ret['success']){
                    if($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include type: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                    
                    $msg = "{$this->getLanguageWord('cant_insert_type_data')}: {$valSearch}, {$this->getLanguageWord('Manage_service_inf_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}"; 
                    return array("success"=>false,"message"=>"{$msg}");
                }

                $typeID = $ret['id'];
                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include type: " . $valSearch . ", idtipo = " . $typeID, 5, 'general');
                
            } else {
                $typeID = $rs['data'][0]['idtipodado'];
                if ($this->log)
                    $this->logIt("Import Data mapping, file line ".$lineNumber.". Type already exists, no need to import: " . $valSearch, 5, 'general');
            }
        } else {            
            $typeID = $this->_types[$valSearch];
            if ($this->log)
                $this->logIt("Import Data mapping, file line ".$lineNumber.". Type already exists, no need to import: " . $valSearch, 5, 'general');
        }
        return array("success"=>true,"idtype"=>$typeID);
    }

    public function saveData($db,$holderTypeID,$typeID,$value,$flgShared,$lineNumber)
    {
        $valSearch = trim(utf8_encode($value)); $value = trim($value);
        
        $rs = $db->getDataMap("WHERE nome = '{$valSearch}' AND idtipotitular = {$holderTypeID} AND idtipodado = {$typeID}");  // second test is necessary
        if(!$rs['success']){
            if($this->log)
                $this->logIt("Error: {$rs['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return array("success"=>false,"message"=>"{$this->getLanguageWord('data_not_found')}");
        }

        if (count($rs['data']) == 0) {               
            $ret = $db->insertDataMap($holderTypeID,$valSearch,$typeID,$flgShared);
            if(!$ret['success']){
                if($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include type: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                
                $msg = "{$this->getLanguageWord('cant_insert_data')}: {$valSearch}, {$this->getLanguageWord('Manage_service_inf_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}"; 
                return array("success"=>false,"message"=>"{$msg}");
            }

            $dataID = $ret['id'];
            if ($this->log)
                $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include data: " . $valSearch . ", iddado = " . $dataID, 5, 'general');
            
        } else {
            $dataID = $rs['data'][0]['iddado'];
            if ($this->log)
                $this->logIt("Import Data mapping, file line ".$lineNumber.". Data already exists, no need to import: " . $valSearch, 5, 'general');
        }

        return array("success"=>true,"iddata"=>$dataID);
    }

    public function savePurpose($db,$dataID,$value,$lineNumber)
    {
        foreach($value as $k=>$v){
            
            $valSearch = trim(utf8_encode($v)); $v = trim($v);
            
            if (!array_key_exists($valSearch, $this->_purposes)) {
                
                $rs = $db->getPurpose("WHERE nome = '{$valSearch}'");  // second test is necessary
                if(!$rs['success']){
                    if($this->log)
                        $this->logIt("Error: {$rs['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
                }
                
                if (count($rs['data']) == 0) {               
                    $ret = $db->insertPurpose($valSearch);
                    if(!$ret['success']){
                        if($this->log)
                            $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include purpose: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                        
                        $msg = "{$this->getLanguageWord('cant_insert_purpose')}: {$valSearch}, {$this->getLanguageWord('Manage_service_inf_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}"; 
                        return array("success"=>false,"message"=>"{$msg}");
                    }
                    
                    $purposeID = $ret['id'];
                    if ($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include purpose: " . $valSearch . ", idfinalidade = " . $purposeID, 5, 'general');
                    
                } else {
                    $purposeID = $rs['data'][0]['idfinalidade'];
                    if ($this->log)
                        $this->logIt("Import Data mapping, file line ".$lineNumber.". Purpose already exists, no need to import: " . $valSearch, 5, 'general');
                }
            } else {            
                $purposeID = $this->_purposes[$valSearch];
                if ($this->log)
                    $this->logIt("Import Data mapping, file line ".$lineNumber.". Purpose already exists, no need to import: " . $valSearch, 5, 'general');
            }
            //echo "{$datdID} - {$purposeID}\n";
            $retLink = $db->getLinkPurpose($dataID,$purposeID); //check if exists the link between datum and purpose
            if(!$retLink['success']){
                if($this->log)
                    $this->logIt("Error: {$retLink['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
            }

            if (count($retLink['data']) == 0) { 
                $insLink = $db->insertDataPurpose($dataID,$purposeID);
                if(!$insLink['success']){
                    if($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include link between datum and purpose: {$dataID} - {$valSearch} - {$insLink['message']} - program: " . $this->program, 3, 'general', __LINE__);
                    
                    $msgIns = "{$this->getLanguageWord('cant_insert_link_purpose')}: {$valSearch}, {$this->getLanguageWord('Manage_service_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}";
                    return array("success"=>false,"message"=>$msgIns);
                }

                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include link between datum and purpose: " . $valSearch, 5, 'general');
                
            }
        }
        
        return array("success"=>true,"message"=>"");
    }

    public function saveFormat($db,$dataID,$value,$lineNumber)
    {
        foreach($value as $k=>$v){
            
            $valSearch = trim(utf8_encode($v)); $v = trim($v);
            
            if (!array_key_exists($valSearch, $this->_formats)) {
                
                $rs = $db->getFormat("WHERE nome = '{$valSearch}'");  // second test is necessary
                if(!$rs['success']){
                    if($this->log)
                        $this->logIt("Error: {$rs['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
                }
                
                if (count($rs['data']) == 0) {               
                    $ret = $db->insertFormat($valSearch);
                    if(!$ret['success']){
                        if($this->log)
                            $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include collection format: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                        
                        $msg = "{$this->getLanguageWord('cant_insert_format')}: {$valSearch}, {$this->getLanguageWord('Manage_service_inf_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}"; 
                        return array("success"=>false,"message"=>"{$msg}");
                    }
                    
                    $formatID = $ret['id'];
                    if ($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include collection format: " . $valSearch . ", idformatocoleta = " . $formatID, 5, 'general');
                    
                } else {
                    $formatID = $rs['data'][0]['idformatocoleta'];
                    if ($this->log)
                        $this->logIt("Import Data mapping, file line ".$lineNumber.". Collection format already exists, no need to import: " . $valSearch, 5, 'general');
                }
            } else {            
                $formatID = $this->_formats[$valSearch];
                if ($this->log)
                    $this->logIt("Import Data mapping, file line ".$lineNumber.". Collection format already exists, no need to import: " . $valSearch, 5, 'general');
            }
            
            $retLink = $db->getLinkFormat($dataID,$formatID); //check if exists the link between datum and collection format
            if(!$retLink['success']){
                if($this->log)
                    $this->logIt("Error: {$retLink['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
            }

            if (count($retLink['data']) == 0) { 
                $insLink = $db->insertDataFormat($dataID,$formatID);
                if(!$insLink['success']){
                    if($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include link between datum and collection format: {$dataID} - {$valSearch} - {$insLink['message']} - program: " . $this->program, 3, 'general', __LINE__);
                    
                    $msgIns = "{$this->getLanguageWord('cant_insert_link_format')}: {$valSearch}, {$this->getLanguageWord('Manage_service_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}";
                    return array("success"=>false,"message"=>$msgIns);
                }

                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include link between datum and purpose: " . $valSearch, 5, 'general');
                
            }else{
                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Link between datum and collection format already exists, no need to import: " . $valSearch, 5, 'general');
            }
        }
        
        return array("success"=>true,"message"=>"");
    }

    public function saveFormCollect($db,$dataID,$value,$lineNumber)
    {
        foreach($value as $k=>$v){
            
            $valSearch = trim(utf8_encode($v)); $v = trim($v);
            
            if (!array_key_exists($valSearch, $this->_forms)) {
                
                $rs = $db->getCollectForm("WHERE nome = '{$valSearch}'");  // second test is necessary
                if(!$rs['success']){
                    if($this->log)
                        $this->logIt("Error: {$rs['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
                }
                
                if (count($rs['data']) == 0) {               
                    $ret = $db->insertFormCollect($valSearch);
                    if(!$ret['success']){
                        if($this->log)
                            $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include collection format: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                        
                        $msg = "{$this->getLanguageWord('cant_insert_form_collect')}: {$valSearch}, {$this->getLanguageWord('Manage_service_inf_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}"; 
                        return array("success"=>false,"message"=>"{$msg}");
                    }
                    
                    $formatColID = $ret['id'];
                    if ($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include form of collection: " . $valSearch . ", idformacoleta = " . $formatColID, 5, 'general');
                    
                } else {
                    $formatColID = $rs['data'][0]['idformacoleta'];
                    if ($this->log)
                        $this->logIt("Import Data mapping, file line ".$lineNumber.". Form of collection already exists, no need to import: " . $valSearch, 5, 'general');
                }
            } else {            
                $formatColID = $this->_forms[$valSearch];
                if ($this->log)
                    $this->logIt("Import Data mapping, file line ".$lineNumber.". Form of collection already exists, no need to import: " . $valSearch, 5, 'general');
            }
            
            $retLink = $db->getLinkFormCollect($dataID,$formatColID); //check if exists the link between datum and form of collection
            if(!$retLink['success']){
                if($this->log)
                    $this->logIt("Error: {$retLink['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
            }

            if (count($retLink['data']) == 0) { 
                $insLink = $db->insertDataCollectForm($dataID,$formatColID);
                if(!$insLink['success']){
                    if($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include link between data and form of collection: {$dataID} - {$valSearch} - {$insLink['message']} - program: " . $this->program, 3, 'general', __LINE__);
                    
                    $msgIns = "{$this->getLanguageWord('cant_insert_link_form_collect')}: {$valSearch}, {$this->getLanguageWord('Manage_service_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}";
                    return array("success"=>false,"message"=>$msgIns);
                }

                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include link between data and form of collection: " . $valSearch, 5, 'general');
                
            }else{
                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Link between data and form of collection already exists, no need to import: " . $valSearch, 5, 'general');
            }
        }
        
        return array("success"=>true,"message"=>"");
    }

    public function saveLegalGround($db,$dataID,$value,$lineNumber)
    {
        foreach($value as $k=>$v){
            
            $valSearch = trim(utf8_encode($v)); $v = trim($v);
            
            if (!array_key_exists($valSearch, $this->_legalgrounds)) {
                
                $rs = $db->getLegalGround("WHERE nome = '{$valSearch}'");  // second test is necessary
                if(!$rs['success']){
                    if($this->log)
                        $this->logIt("Error: {$rs['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
                }
                
                if (count($rs['data']) == 0) {               
                    $ret = $db->insertLegalGround($valSearch);
                    if(!$ret['success']){
                        if($this->log)
                            $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include legal ground: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                        
                        $msg = "{$this->getLanguageWord('cant_insert_legal_ground')}: {$valSearch}, {$this->getLanguageWord('Manage_service_inf_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}"; 
                        return array("success"=>false,"message"=>"{$msg}");
                    }
                    
                    $legalGroundID = $ret['id'];
                    if ($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include legal ground: " . $valSearch . ", idbaselegal = " . $legalGroundID, 5, 'general');
                    
                } else {
                    $legalGroundID = $rs['data'][0]['idbaselegal'];
                    if ($this->log)
                        $this->logIt("Import Data mapping, file line ".$lineNumber.". Legal ground already exists, no need to import: " . $valSearch, 5, 'general');
                }
            } else {            
                $legalGroundID = $this->_legalgrounds[$valSearch];
                if ($this->log)
                    $this->logIt("Import Data mapping, file line ".$lineNumber.". Legal ground already exists, no need to import: " . $valSearch, 5, 'general');
            }
            
            $retLink = $db->getLinkLegalGround($dataID,$legalGroundID); //check if exists the link between datum and form of collection
            if(!$retLink['success']){
                if($this->log)
                    $this->logIt("Error: {$retLink['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
            }

            if (count($retLink['data']) == 0) { 
                $insLink = $db->insertDataLegalGround($dataID,$legalGroundID);
                if(!$insLink['success']){
                    if($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include link between datum and legal ground: {$dataID} - {$valSearch} - {$insLink['message']} - program: " . $this->program, 3, 'general', __LINE__);
                    
                    $msgIns = "{$this->getLanguageWord('cant_insert_link_legal_ground')}: {$valSearch}, {$this->getLanguageWord('Manage_service_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}";
                    return array("success"=>false,"message"=>$msgIns);
                }

                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include link between datum and legal ground: " . $valSearch, 5, 'general');
                
            }else{
                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Link between datum and legal ground already exists, no need to import: " . $valSearch, 5, 'general');
            }
        }
        
        return array("success"=>true,"message"=>"");
    }

    public function saveStorage($db,$dataID,$value,$lineNumber)
    {
        foreach($value as $k=>$v){
            
            $valSearch = trim(utf8_encode($v)); $v = trim($v);
            
            if (!array_key_exists($valSearch, $this->_storages)) {
                
                $rs = $db->getStorage("WHERE nome = '{$valSearch}'");  // second test is necessary
                if(!$rs['success']){
                    if($this->log)
                        $this->logIt("Error: {$rs['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
                }
                
                if (count($rs['data']) == 0) {               
                    $ret = $db->insertStorage($valSearch);
                    if(!$ret['success']){
                        if($this->log)
                            $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include storage: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                        
                        $msg = "{$this->getLanguageWord('cant_insert_storage')}: {$valSearch}, {$this->getLanguageWord('Manage_service_inf_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}"; 
                        return array("success"=>false,"message"=>"{$msg}");
                    }
                    
                    $storageID = $ret['id'];
                    if ($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include storage: " . $valSearch . ", idarmazenamento = " . $storageID, 5, 'general');
                    
                } else {
                    $storageID = $rs['data'][0]['idarmazenamento'];
                    if ($this->log)
                        $this->logIt("Import Data mapping, file line ".$lineNumber.". Storage already exists, no need to import: " . $valSearch, 5, 'general');
                }
            } else {            
                $storageID = $this->_storages[$valSearch];
                if ($this->log)
                    $this->logIt("Import Data mapping, file line ".$lineNumber.". Storage already exists, no need to import: " . $valSearch, 5, 'general');
            }
            
            $retLink = $db->getLinkStorage($dataID,$storageID); //check if exists the link between datum and form of collection
            if(!$retLink['success']){
                if($this->log)
                    $this->logIt("Error: {$retLink['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
            }

            if (count($retLink['data']) == 0) { 
                $insLink = $db->insertDataStorage($dataID,$storageID);
                if(!$insLink['success']){
                    if($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include link between datum and storage: {$dataID} - {$valSearch} - {$insLink['message']} - program: " . $this->program, 3, 'general', __LINE__);
                    
                    $msgIns = "{$this->getLanguageWord('cant_insert_link_storage')}: {$valSearch}, {$this->getLanguageWord('Manage_service_line')} {$lineNumber}. {$this->getLanguageWord('Manage_service_imp_canceled')}";
                    return array("success"=>false,"message"=>$msgIns);
                }

                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include link between datum and storage: " . $valSearch, 5, 'general');
                
            }else{
                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Link between datum and storage already exists, no need to import: " . $valSearch, 5, 'general');
            }
        }
        
        return array("success"=>true,"message"=>"");
    }

    public function savePerson($db,$dataID,$value,$companyID,$lineNumber)
    {
        foreach($value as $k=>$v){

            $aPerson = explode("/",$v);

            $ret = (strtoupper($aPerson[1]) == 'P') ? $this->processPerson($db,$dataID,$aPerson[0],$companyID,$lineNumber) : $this->processGroup($db,$dataID,$aPerson[0],$companyID,$lineNumber);

            if(!$ret['success']){
                if($this->log)
                    $this->logIt("Error: {$ret['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$ret['message']}");
            }
            
        }
        
        return array("success"=>true,"message"=>"");
    }

    function processPerson($db,$dataID,$personName,$companyID,$lineNumber){
        $valSearch = utf8_encode(addslashes(trim($personName))); $valIns = utf8_encode(trim($personName)); $personName = trim($personName);

        //Search person's type of LGPD module
        $rsTypePerson = $db->getLgpTypePerson('LGP_personhasaccess');
        if(!$rsTypePerson['success']){
            if($this->log)
                $this->logIt("Error: {$rsTypePerson['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return array("success"=>false,"message"=>"{$this->getLanguageWord('error_get_lgptypeperson')}");
        }

        $lgpTypePerson = $rsTypePerson['data'][0]['idtypeperson'];

        $rs = $db->getPerson("WHERE `name` = '{$valSearch}'");  // second test is necessary
        if(!$rs['success']){
            if($this->log)
                $this->logIt("Error: {$rs['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
        }
        
        if (count($rs['data']) == 0) {
            $ret = $db->insertPerson($valIns,$lgpTypePerson);
            if(!$ret['success']){
                if($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include person: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                
                return array("success"=>false,"message"=>"{$this->getLanguageWord('cant_insert_person')}");
            }
            
            $personID = $ret['id'];
            if ($this->log)
                $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include person: " . $valSearch . ", idperson = " . $personID, 5, 'general');
            
        } else {
            $personID = $rs['data'][0]['idperson'];
            if ($this->log)
                $this->logIt("Import Data mapping, file line ".$lineNumber.". Person already exists, no need to import: " . $valSearch, 5, 'general');
        }

        $retLink = $db->getLinkPerson($dataID,$personID,'P'); //check if exists the link between data and form of collection
        if(!$retLink['success']){
            if($this->log)
                $this->logIt("Error: {$retLink['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
        }

        if (count($retLink['data']) == 0) { 
            $insLink = $db->insertDataPerson($dataID,$personID,'P');
            if(!$insLink['success']){
                if($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include link between data and person: {$dataID} - {$valSearch} - {$insLink['message']} - program: " . $this->program, 3, 'general', __LINE__);
                
                $msgIns = "{$this->getLanguageWord('cant_insert_link_person')}: {$valSearch}, {$this->getLanguageWord('Manage_service_line')} {$lineNumber}";
                return array("success"=>false,"message"=>$msgIns);
            }

            if ($this->log)
                $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include link between data and person: " . $valSearch, 5, 'general');
            
        }else{
            if ($this->log)
                $this->logIt("Import Data mapping, file line " . $lineNumber . ". Link between data and person already exists, no need to import: " . $valSearch, 5, 'general');
        }

        return array("success"=>true,"message"=>"");
    }

    function processGroup($db,$dataID,$groupName,$companyID,$lineNumber){
        $valSearch = utf8_encode(trim(addslashes($groupName))); $valIns = utf8_encode(trim($groupName)); $groupName = trim($groupName);

        //Search group's type of LGPD module
        $rsTypeGroup = $db->getLgpTypePerson('LGP_group');
        if(!$rsTypeGroup['success']){
            if($this->log)
                $this->logIt("Error: {$rsTypeGroup['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return array("success"=>false,"message"=>"{$this->getLanguageWord('error_get_lgptypegroup')}");
        }

        $lgpTypeGroup = $rsTypeGroup['data'][0]['idtypeperson'];

        $rs = $db->getGroup("AND b.name = '{$valSearch}' AND idcompany = {$companyID}");  // second test is necessary
        if(!$rs['success']){
            if($this->log)
                $this->logIt("Error: {$rs['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
        }
        
        if (count($rs['data']) == 0) {               
            $ret = $db->insertPerson($valSearch,$lgpTypeGroup);
            if(!$ret['success']){
                if($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include into tbperson, group: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                
                return array("success"=>false,"message"=>"{$this->getLanguageWord('cant_insert_group')}");
            }

            $personID = $ret['id'];

            $retGrp = $db->insertGroup($personID,$companyID);
            if(!$retGrp['success']){
                if($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include group: " . $valSearch . " - program: " . $this->program, 3, 'general', __LINE__);
                
                return array("success"=>false,"message"=>"{$this->getLanguageWord('cant_insert_group')}");
            }
            
            $groupID = $retGrp['id'];
            
            if ($this->log)
                $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include group: " . $valSearch . ", idgroup = " . $groupID, 5, 'general');
            
        } else {
            $groupID = $rs['data'][0]['idgroup'];
            if ($this->log)
                $this->logIt("Import Data mapping, file line ".$lineNumber.". Person already exists, no need to import: " . $valSearch, 5, 'general');
        }

        $retLink = $db->getLinkPerson($dataID,$groupID,'G'); //check if exists the link between data and form of collection
        if(!$retLink['success']){
            if($this->log)
                $this->logIt("Error: {$retLink['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valSearch}");
        }

        if (count($retLink['data']) == 0) { 
            $insLink = $db->insertDataPerson($dataID,$groupID,'G');
            if(!$insLink['success']){
                if($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include link between data and group: {$dataID} - {$valSearch} - {$insLink['message']} - program: " . $this->program, 3, 'general', __LINE__);
                
                $msgIns = "{$this->getLanguageWord('cant_insert_link_group')}: {$valSearch}, {$this->getLanguageWord('Manage_service_line')} {$lineNumber}";
                return array("success"=>false,"message"=>$msgIns);
            }

            if ($this->log)
                $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include link between data and group: " . $valSearch, 5, 'general');
            
        }else{
            if ($this->log)
                $this->logIt("Import Data mapping, file line " . $lineNumber . ". Link between data and group already exists, no need to import: " . $valSearch, 5, 'general');
        }

        return array("success"=>true,"message"=>"");
    }

    public function saveSharedWith($db,$dataID,$value,$lineNumber)
    {
        foreach($value as $k=>$v){

            $aPerson = explode("-",$v); 
            $natureID = $aPerson[1] == 'F' ? 1 : 2;
            $phone = trim($aPerson[2]);
            $mobile = trim($aPerson[3]);
            $contact = utf8_encode(trim($aPerson[4]));

            $valSearch = utf8_encode(addslashes(trim($aPerson[0]))); $valIns = utf8_encode(trim($aPerson[0])); $personName = trim($aPerson[0]);

            //Search person's type of LGPD module
            $rsTypePerson = $db->getLgpTypePerson('LGP_operator');
            if(!$rsTypePerson['success']){
                if($this->log)
                    $this->logIt("Error: {$rsTypePerson['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$this->getLanguageWord('error_get_lgptypeperson')}");
            }

            $lgpTypePerson = $rsTypePerson['data'][0]['idtypeperson'];

            $rs = $db->getPerson("WHERE `name` = '{$valSearch}' AND idtypeperson = {$lgpTypePerson}");  // second test is necessary
            if(!$rs['success']){
                if($this->log)
                    $this->logIt("Error: {$rs['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valIns}");
            }
            
            if (count($rs['data']) == 0) {
                $ret = $db->insertOperator($valIns,$lgpTypePerson,$natureID,$phone,$mobile);
                if(!$ret['success']){
                    if($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include operator: " . $valIns . " - program: " . $this->program, 3, 'general', __LINE__);
                    
                    return array("success"=>false,"message"=>"{$this->getLanguageWord('cant_insert_operator')}");
                }
                
                $operatorID = $ret['id'];

                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include operator: " . $valIns . ", idperson = " . $operatorID, 5, 'general');
                
            } else {
                $operatorID = $rs['data'][0]['idperson'];
                if ($this->log)
                    $this->logIt("Import Data mapping, file line ".$lineNumber.". Operator already exists, no need to import: " . $valIns, 5, 'general');
            }

            if($natureID == 2){
                $this->saveContact($db,$operatorID,$contact,$lineNumber);
            }

            $retLink = $db->getLinkOperator($dataID,$operatorID); //check if exists the link between data and operator
            if(!$retLink['success']){
                if($this->log)
                    $this->logIt("Error: {$retLink['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valIns}");
            }

            if (count($retLink['data']) == 0) { 
                $insLink = $db->insertSharedWith($dataID,$operatorID);
                if(!$insLink['success']){
                    if($this->log)
                        $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include link between data and operator: {$dataID} - {$valIns} - {$insLink['message']} - program: " . $this->program, 3, 'general', __LINE__);
                    
                    $msgIns = "{$this->getLanguageWord('cant_insert_link_operator')}: {$valIns}, {$this->getLanguageWord('Manage_service_line')} {$lineNumber}";
                    return array("success"=>false,"message"=>$msgIns);
                }

                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Include link between data and operator: " . $valIns, 5, 'general');
                
            }else{
                if ($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Link between data and operator already exists, no need to import: " . $valIns, 5, 'general');
            }
            
        }
        
        return array("success"=>true,"message"=>"");
    }

    function saveContact($db,$operatorID,$value,$lineNumber){
        $rs = $db->getJuridical("WHERE idperson = {$operatorID}");  // second test is necessary
        if(!$rs['success']){
            if($this->log)
                $this->logIt("Error: {$rs['message']}.\n Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return array("success"=>false,"message"=>"{$this->getLanguageWord('msg_erro_query')}: {$valIns}");
        }
        
        if (count($rs['data']) == 0) {
            $retContact = $db->insertContact($operatorID,$contact);
            if(!$retContact['success']){
                if($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't include juridical operator's contact: " . $valIns . " - program: " . $this->program, 3, 'general', __LINE__);
                
                return array("success"=>false,"message"=>"{$this->getLanguageWord('cant_insert_operator_contact')}");
            }
        }else{
            $retContact = $db->updateContact($operatorID,$contact,$rs['data'][0]['idjuridicalperson']);
            if(!$retContact['success']){
                if($this->log)
                    $this->logIt("Import Data mapping, file line " . $lineNumber . ". Can't update juridical operator's contact: " . $valIns . " - program: " . $this->program, 3, 'general', __LINE__);
                
                return array("success"=>false,"message"=>"{$this->getLanguageWord('cant_insert_operator_contact')}");
            }
        }

        return array("success"=>true,"message"=>"");
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

