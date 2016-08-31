<?php

class relPessoa extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("relPessoa/");
        $access = $this->access($user, $program, $typeperson);
		
        $smarty = $this->retornaSmarty();

        $db = new logos_model();
        $reportslogo = $db->getReportsLogo();
		
		$db2 = new person_model();
        $rs = $db2->getTypePerson();
       
		while (!$rs->EOF) {
            $campos[] = $rs->fields['idtypeperson'];
            $valores[] = utf8_encode($rs->fields['name']);
            $rs->MoveNext();
        }
		
		$smarty->assign('typepersonid', $campos);
        $smarty->assign('typepersonval', $valores);
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);
        $smarty->display('relPessoa.tpl.html');
    }

    public function table_json() {
		$db = new person_model();
        
		if($_POST['typeperson'] != "ALL")
        	$where = "AND  a.idtypeperson =".$_POST['typeperson'];
        $rs = $db->getTableReports($where);
        
        $output = array();
        while (!$rs->EOF) {
        	$company = $rs->fields['company'];
			if(!$company) $company = "";
            $output[] = array(
            					"login"    		=> $rs->fields['login'],
            					"name"  		=> $rs->fields['name'],
            					"typeperson"  	=> utf8_encode($rs->fields['typeperson']),
            					"company"  	=> $company
                            ) ;
            $rs->MoveNext();
        }     
        echo json_encode($output);

    }

}


?>
