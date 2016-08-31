<?php
class relRequestsAddInfo extends Controllers {

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("relSumDepartment/");
        $access = $this->access($user, $program, $typeperson);
        
        $smarty = $this->retornaSmarty();

        $db = new logos_model();

        $reportslogo = $db->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);
		
		$db = new addinfo_model();
		$getAddInfo = $db->getAddInfos(); 
		while (!$getAddInfo->EOF) {
            $value[] = $getAddInfo->fields['idaddinfo'];
            $field[] = $getAddInfo->fields['name'];
            $getAddInfo->MoveNext();
        }		
		$smarty->assign('addinfofield', $field);
        $smarty->assign('addinfovalue', $value);
        
        $smarty->display('relrequestsAddInfo.tpl.html');
    }
	
	public function table_json() {
		include 'includes/classes/pipegrep/pipegrep.php';
		
		$pipe = new pipegrep();
		$db = new relRequestsAddInfo_model();
		
        
        $date_field_hour = "sol.entry_date";
      	$date_apt = $pipe->mysql_date_condition($date_field_hour, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		if ($date_apt) $date .= " AND" . $date_apt;
		
		if($_POST['addInfo'])
			$where .= "AND a.idaddinfo = " . $_POST['addInfo'];
		
		$where .= $date;
		$date_format = $this->getConfig('date_format');
        $select = $db->getSummarizedReq($date_format,$where);
		
		$output = array();
		
        while (!$select->EOF){
			$output[] = array(
            					"code_request"  => $select->fields['code_request'],
            					"person"		=> $select->fields['person'],
            					"date"			=> $select->fields['date'],
            					"addinfo"		=> $select->fields['addinfo']
                            );
            $select->MoveNext();
        }
        echo json_encode($output);
    }
}
