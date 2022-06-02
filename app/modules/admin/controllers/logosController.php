<?php
require_once(HELPDEZK_PATH . '/app/modules/admin/controllers/admCommonController.php');

class logos  extends admCommon {

    public function __construct(){

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;

        $this->program  = basename( __FILE__ );
        $this->idprogram =  $this->getIdProgramByController('logos');

        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;        

        $this->loadModel('logos_model'); 
        $dbLogo = new logos_model();
        $this->dbLogo = $dbLogo;

        if ($this->_externalStorage) {
            $this->targetPath = $this->_externalStoragePath . '/logos/' ;
            $this->targetUrl = $this->_externalStorageUrl . '/logos/' ;
            $this->targetUrlDefault = $this->_externalStorageUrl . '/logos/default/';
        } else{
            $this->targetPath = $this->helpdezkPath . '/app/uploads/logos/' ;
            $this->targetUrl = $this->helpdezkUrl . '/app/uploads/logos/' ;
            $this->targetUrlDefault = $this->helpdezkUrl . '/app/uploads/logos/default/';
        }

    }

    public function index()
    {
        $smarty = $this->retornaSmarty();

        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        /*
         * The tblogos table needs to have 3 lines with the logo data: header logo, login logo and repots logo.
         * This test is necessary to verify that the table has the correct lines.
         */
        if (!$this->testLogosTable()) {
            if($this->log)
                $this->logIt("There is an error in the tblogos table! - program: ".$this->program ,3,'general',__LINE__);
        }

        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->assign('token', $token) ;
        
        $headerlogo = $this->dbLogo->getHeaderLogo();
        $header = (empty($headerlogo->fields['file_name']) ? $this->targetUrlDefault . 'header.png' : $this->targetUrl . $headerlogo->fields['file_name']);
        $smarty->assign('headerlogo', $header);
        $smarty->assign('headerheight', $headerlogo->fields['height']);
        $smarty->assign('headerwidth', $headerlogo->fields['width']);

        $loginlogo = $this->dbLogo->getLoginLogo();
        $login = (empty($loginlogo->fields['file_name']) ? $this->targetUrlDefault . 'login.png' : $this->targetUrl . $loginlogo->fields['file_name']);
        $smarty->assign('loginlogo', $login);
        $smarty->assign('loginheight', $loginlogo->fields['height']);
        $smarty->assign('loginwidth', $loginlogo->fields['width']);

        $reportslogo = $this->dbLogo->getReportsLogo();
        $reports = (empty($reportslogo->fields['file_name']) ? $this->targetUrlDefault . 'reports.png' : $this->targetUrl . $reportslogo->fields['file_name']);
        $smarty->assign('reportslogo', $reports);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $smarty->display('logos.tpl');

    }

    function saveLogo()
    {
        
        $type = $this->getParam('type');

        $char_search	= array("ã", "á", "à", "â", "é", "ê", "í", "õ", "ó", "ô", "ú", "ü", "ç", "ñ", "Ã", "Á", "À", "Â", "É", "Ê", "Í", "Õ", "Ó", "Ô", "Ú", "Ü", "Ç", "Ñ", "ª", "º", " ", ";", ",");
		$char_replace	= array("a", "a", "a", "a", "e", "e", "i", "o", "o", "o", "u", "u", "c", "n", "A", "A", "A", "A", "E", "E", "I", "O", "O", "O", "U", "U", "C", "N", "_", "_", "_", "_", "_");

        if (!empty($_FILES)) {

            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");
            $fileSize = $_FILES['file']['size'];
            
            $fileName = str_replace($char_search, $char_replace, $fileName);
            
            $targetFile = $this->targetPath . $fileName;
            
            if(!is_dir($this->targetPath)) {
                mkdir ($this->targetPath, 0777 ); // criar o diretorio
            }

            if (move_uploaded_file($tempFile,$targetFile)){
                if($this->log){
                    $this->logIt("Save ".$type." logo - File: ".$targetFile.' - program: '.$this->program ,7,'general',__LINE__);
                }
            }else {
                if($this->log){
                    $this->logIt("Can't save ".$type." logo - File: ".$targetFile.' - program: '.$this->program ,3,'general',__LINE__);
                }
                return false;
            }

            $this->removeOldImage($type);
            $this->processImage($fileName,$fileSize,$type);

        }

        echo $fileName;

    }

    public function processImage( $file,$size,$prefix )
    {

        list($realwidth, $realheight, $type, $attr) = getimagesize($this->targetPath.$file);

        switch ($prefix) {
            case 'header':
                $height = '35';
                break;
            case 'login':
                $height = '70';
                break;
            case 'reports':
                $height = '40';
            break;
        }

        $width = ($height * $realwidth) / $realheight;

        $upload = $this->dbLogo->upload($prefix."_".$file, $height, round($width), $prefix);

        rename($this->targetPath.$file,$this->targetPath.$prefix."_".$file);

    }
    
    public function getImage()
    {
        $prefix = $_POST['type'];

        switch ($prefix) {
            case 'header';
                $headerlogo = $this->dbLogo->getHeaderLogo();
                $height     = $headerlogo->fields['height'];
                $width      = $headerlogo->fields['width'];
                $fileName   = $this->targetUrl . $headerlogo->fields['file_name'];
                break;
            case 'login':
                $loginlogo  = $this->dbLogo->getLoginLogo();
                $fileName   = $this->targetUrl . $loginlogo->fields['file_name'];
                $height     = $loginlogo->fields['height'];
                $width      = $loginlogo->fields['width'];
                break;
            case 'reports':
                $reportslogo = $this->dbLogo->getReportsLogo();
                $fileName    = $this->targetUrl . $reportslogo->fields['file_name'];
                $height      = $reportslogo->fields['height'];
                $width       = $reportslogo->fields['width'];
                break;

        }

            echo "<img src='{$fileName}' height='{$height}px' width='{$width}px'/>";


    }

    public function testLogosTable()
    {
        $rsLogo = $this->dbLogo->getLogosData('header');
        if ($rsLogo->RecordCount() == 0)
            return false ;
        $rsLogo = $this->dbLogo->getLogosData('login');
        if ($rsLogo->RecordCount() == 0)
            return false ;
        $rsLogo = $this->dbLogo->getLogosData('reports');
        if ($rsLogo->RecordCount() == 0)
            return false ;

        return true;

    }
    public function removeOldImage($prefix)
    {
        switch ($prefix) {
            case 'header';
                $headerlogo = $this->dbLogo->getHeaderLogo();
                $fileName   = $headerlogo->fields['file_name'];
                break;
            case 'login':
                $loginlogo  = $this->dbLogo->getLoginLogo();
                $fileName   = $loginlogo->fields['file_name'];
                break;
            case 'reports':
                $reportslogo = $this->dbLogo->getReportsLogo();
                $fileName    = $reportslogo->fields['file_name'];
                break;
        }
        unlink($this->targetPath . $fileName);
        return;
    }

}