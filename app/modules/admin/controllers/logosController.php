<?php
class Logos extends Controllers{
    public function index(){
        session_start();
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
		$program = $bd->selectProgramIDByController("logos/");
        $typeperson = $bd->selectTypePerson($user);
        $access = $this->access($user, $program, $typeperson);
        $smarty = $this->retornaSmarty();
        $db = new logos_model();
        $headerlogo = $db->getHeaderLogo();
        $smarty->assign('headerlogo', $headerlogo->fields['file_name']);
        $smarty->assign('headerheight', $headerlogo->fields['height']);
        $smarty->assign('headerwidth', $headerlogo->fields['width']);
        $loginlogo = $db->getLoginLogo();
        $smarty->assign('loginlogo', $loginlogo->fields['file_name']);
        $smarty->assign('loginheight', $loginlogo->fields['height']);
        $smarty->assign('loginwidth', $loginlogo->fields['width']);
        $reportslogo = $db->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);
        $smarty->display('logos.tpl.html');
    }
    public function upload(){
        session_start();
        if (!isset($_SESSION['SES_COD_USUARIO'])) {
            die('access denied !!!');
        }
        $this->view('upload.php');
    }    
    public function upload2(){
        session_start();
        if (!isset($_SESSION['SES_COD_USUARIO'])) {
            die('access denied !!!');
        }
        $this->view('upload2.php');
    } 
    public function upload3(){
        session_start();
        if (!isset($_SESSION['SES_COD_USUARIO'])) {
            die('access denied !!!');
        }
        $this->view('upload3.php');
    } 
}
?>
