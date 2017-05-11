<?php
class Logos extends Controllers{

    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->validasessao();
        $this->logosDir = 'app/uploads/logos/';
    }

    public function header()
    {
        $this->index('header');
    }

    public function login()
    {
        $this->index('login');
    }

    public function index($loc){
        session_start();
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
		$program = $bd->selectProgramIDByController("logos/".$loc);
        $typeperson = $bd->selectTypePerson($user);
        $access = $this->access($user, $program, $typeperson);
        $smarty = $this->retornaSmarty();
        $db = new logos_model();
        switch ($loc) {
            case 'header':
                $headerlogo = $db->getHeaderLogo();
                $smarty->assign('headerlogo', $headerlogo->fields['file_name']);
                $smarty->assign('headerheight', $headerlogo->fields['height']);
                $smarty->assign('headerwidth', $headerlogo->fields['width']);
                break;
            case 'login':
                $loginlogo = $db->getLoginLogo();
                $smarty->assign('loginlogo', $loginlogo->fields['file_name']);
                $smarty->assign('loginheight', $loginlogo->fields['height']);
                $smarty->assign('loginwidth', $loginlogo->fields['width']);
                break;
            case 'reports':
                $reportslogo = $db->getReportsLogo();
                $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
                $smarty->assign('reportsheight', $reportslogo->fields['height']);
                $smarty->assign('reportswidth', $reportslogo->fields['width']);
                break;
        }

        $smarty->assign('location', $loc);
        $smarty->display('logos.tpl.html');
    }

    /**
     * Show the insert modal.
     *
     * Since May 09, 2017
     * @access public
     */
    public function modalStart(){

        $loc = $this->getParam('loc') ;
        $smarty = $this->retornaSmarty();

        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $program = $bd->selectProgramIDByController("logos/".$loc);
        $typeperson = $bd->selectTypePerson($user);
        $access = $this->access($user, $program, $typeperson);

        $db = new logos_model();
        switch($loc){
            case 'header':
                $headerlogo = $db->getHeaderLogo();
                $smarty->assign('logo',     $headerlogo->fields['file_name']);
                $smarty->assign('height',   $headerlogo->fields['height']);
                $smarty->assign('width',    $headerlogo->fields['width']);
                break;
            case 'login':
                $loginlogo = $db->getLoginLogo();
                $smarty->assign('logo',     $loginlogo->fields['file_name']);
                $smarty->assign('height',   $loginlogo->fields['height']);
                $smarty->assign('width',    $loginlogo->fields['width']);
                break;
            case 'reports':
                $reportslogo = $db->getReportsLogo();
                $smarty->assign('logo',     $reportslogo->fields['file_name']);
                $smarty->assign('height',   $reportslogo->fields['height']);
                $smarty->assign('width',    $reportslogo->fields['width']);
                break;

        }

        $smarty->assign('location', $loc);
        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->display('modals/logos/start.tpl.html');
    }

    /**
     * Upload File
     *
     * Since May 09, 2017
     * @access public
     */
    public function upload()
    {
        if(isset($_POST['flagFirstUpload'])) {
            if (!$this->_checkToken()) return false;
        }

        $data = array();
        $prefix = $_POST['prefix'];

        // First
        if(isset($_GET['url']))
        {

            $error = false;
            $files = array();

            foreach($_FILES as $file)
            {

                if(move_uploaded_file($file['tmp_name'], $this->logosDir .basename($file['name'])))
                {
                    if(!$this->checkImage($this->logosDir .basename($file['name']))) {
                        unlink($this->logosDir .basename($file['name']));
                        $error = true;
                    } else {
                        $this->removeOldImage($prefix);
                        $this->processImage($file['name'],$file['size'],$prefix);
                        $files[] = $prefix.'_'.$file['name'];
                    }
                }
                else
                {
                    $error = true;
                }
            }
            $data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);
        }
        // Second
        else
        {
            $data = array('success' => 'Form was submitted', 'formData' => $_POST);
        }
        if(isset($_POST['flagFirstUpload'])) {
            $data = array('success' => 'Form was submitted', 'formData' => $_POST);
        }

        echo json_encode($data);

    }

    public function checkImage($image)
    {
        if (exif_imagetype($image) != IMAGETYPE_JPEG  && exif_imagetype($image) != IMAGETYPE_PNG) {
            // http://stackoverflow.com/questions/676949/best-way-to-determine-if-a-url-is-an-image-in-php
            return false ;
        } else {
            return true;
        }

    }
    public function processImage( $file,$size,$prefix )
    {

        list($realwidth, $realheight, $type, $attr) = getimagesize($this->logosDir.$file);

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

        $bd = new logos_model();
        $upload = $bd->upload($prefix."_".$file, $height, round($width), $prefix);

        rename($this->logosDir.$file,$this->logosDir.$prefix."_".$file);

    }
    public function getImage()
    {
        $prefix = $this->getParam('prefix');

        $db = new logos_model();
        switch ($prefix) {
            case 'header';
                $headerlogo = $db->getHeaderLogo();
                $height     = $headerlogo->fields['height'];
                $width      = $headerlogo->fields['width'];
                $fileName   = $headerlogo->fields['file_name'];
                break;
            case 'login':
                $loginlogo = $db->getLoginLogo();
                $fileName   = $loginlogo->fields['file_name'];
                $height     = $loginlogo->fields['height'];
                $width      = $loginlogo->fields['width'];
                break;
            case 'reports':
                $reportslogo = $db->getReportsLogo();
                $fileName   = $reportslogo->fields['file_name'];
                $height     = $reportslogo->fields['height'];
                $width      = $reportslogo->fields['width'];
                break;

        }

            echo json_encode(array("height" => $height, "width" => $width, "file" => $fileName ));


    }
    public function removeOldImage($prefix)
    {
        $db = new logos_model();
        switch ($prefix) {
            case 'header';
                $headerlogo = $db->getHeaderLogo();
                $fileName   = $headerlogo->fields['file_name'];
                break;
            case 'login':
                $loginlogo  = $db->getLoginLogo();
                $fileName   = $loginlogo->fields['file_name'];
                break;
            case 'reports':
                $reportslogo = $db->getReportsLogo();
                $fileName    = $reportslogo->fields['file_name'];
                break;
        }
        unlink($this->logosDir . $fileName);
        return;
    }

}
?>
