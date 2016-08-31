<?php

class importservices extends Controllers {

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("importservices/");
        $access = $this->access($user, $program, $typeperson);

        $smarty = $this->retornaSmarty();
        $smarty->display('importservices.tpl.html');
    }
    
    public function upcatalogo() {
        $this->view('manage_services.php');
    }
   
}
