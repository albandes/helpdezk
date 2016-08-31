<?php

class importpeople extends Controllers {

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("importpeople/");
        $access = $this->access($user, $program, $typeperson);

        $smarty = $this->retornaSmarty();
        $smarty->display('importpeople.tpl.html');
    }

    public function upfile() {
        $this->view('manage_people.php');
    }

}