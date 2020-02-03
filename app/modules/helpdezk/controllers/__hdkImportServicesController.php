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

        $this->loadModel('emailconfig_model');
        $dbEmailConfig = new emailconfig_model();
        $this->dbEmailConfig = $dbEmailConfig;

        $this->logIt("entrou  :" . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program, 7, 'general', __LINE__);

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

            if(!is_writable($targetFile)) {
                $this->logIt("Target Directory : ".$targetFile.' is not writable - program: '.$this->program ,3,'general',__LINE__);
                echo 'Import_not_writable';
                return false;
            }
            
            if (move_uploaded_file($tempFile,$targetFile)){
                if($this->log){
                    $this->logIt("Save file: ".$targetFile.' - program: '.$this->program ,7,'general',__LINE__);
                }
            }else {
                if($this->log){
                    $this->logIt("Can't save file: ".$targetFile.' - program: '.$this->program ,3,'general',__LINE__);
                }
                echo 'Import_error_file'; // Returns the language file variable, to display de error .
                return false;
            }

            //$this->removeOldImage($type);
            //$this->processImage($fileName,$fileSize,$type);

        }


        echo 'Success';

        //echo 'Select_country'; // Returns the language file variable, to display de error .

        /*
         https://severalnines.com/database-blog/comparing-cloud-database-options-postgresql

         */

    }



}

