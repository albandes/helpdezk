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

        $this->loadModel('service_model');
        $this->dbService = new service_model();

        $this->_areas = $this->makeArrayAreas();
        $this->_types = $this->makeArrayTypes();
        $this->_itens = $this->makeArrayItens();
        $this->_services = $this->makeArrayServices();


    }


    public function index()
    {


        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();

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

        $char_search	= array("ã", "á", "à", "â", "é", "ê", "í", "õ", "ó", "ô", "ú", "ü", "ç", "ñ", "Ã", "Á", "À", "Â", "É", "Ê", "Í", "Õ", "Ó", "Ô", "Ú", "Ü", "Ç", "Ñ", "ª", "º", " ", ";", ",");
        $char_replace	= array("a", "a", "a", "a", "e", "e", "i", "o", "o", "o", "u", "u", "c", "n", "A", "A", "A", "A", "E", "E", "I", "O", "O", "O", "U", "U", "C", "N", "_", "_", "_", "_", "_");


        if (!empty($_FILES)) {
            $targetPath = $this->helpdezkPath . '/app/uploads/tmp/' ;

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

        //print_r($arrayImport);

        echo 'Success';

        //echo 'Select_country'; // Returns the language file variable, to display the error .

        /*
         https://severalnines.com/database-blog/comparing-cloud-database-options-postgresql
         */

    }

    function writeDataBase($array)
    {

        $i = 1;
        //$this->dbService->BeginTrans();

        foreach ($array as $line) {
            $arrayExplode = explode(';',$line);

            $area = $arrayExplode[0];
            $item = $arrayExplode[1];

            $idArea = $this->saveArea($this->dbService,$area,$i);
            if(!$idArea) return false ;

            //$idType = $this->saveType($this->dbService,$idArea,$item,$i);
            //if(!$idType) return false ;


            $i++;
        }

        //$this->dbService->CommitTrans();
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

    function saveArea($db,$value,$lineNumber)
    {
        if (!array_key_exists($value, $this->_areas)) {
            $rs = $db->selectAreaFromName(trim($value));  // second test is necessary
            if ($rs->RecordCount() == 0) {
                try {
                    $ret = $db->areaInsert($value);
                    $idArea = $db->TableMaxID('hdk_tbcore_area', 'idarea');
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




}

