<?php
class Warnings extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
 
    public function index() {
        $smarty = $this->retornaSmarty();
		$idperson = $_SESSION['SES_COD_USUARIO'];
		$idcompany = $_SESSION['SES_COD_EMPRESA'];
        $bd = new warning_model();		
		$database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $where = "AND (a.dtend > NOW() OR a.dtend = '0000-00-00 00:00:00') AND (a.idmessage NOT IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idperson))";
        } elseif ($database == 'oci8po') {
            $where = "AND (a.dtend > SYSDATE OR a.dtend IS NULL) AND (a.idmessage NOT IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idperson))";
        }
        $rsWarning = $bd->selectWarning($where);
		if ($database == 'mysqlt') {
            $rstotal = $this->found_rows();
            $total = $rstotal->fields['found_rows'];
        } elseif ($database == 'oci8po') {
        	if(isset($rsWarning->fields['rnum']))
            	$total = $rsWarning->fields['rnum'];
            else 
            	$total = 0;
        }
		while (!$rsWarning->EOF) {				
			if($_SESSION['SES_COD_TIPO'] == 2){//USER
				if($rsWarning->fields['total_company'] > 0){
					$checkCompany = $bd->checkCompany($rsWarning->fields['idtopic'], $idcompany);
					if($checkCompany->fields['chk'] == 0){
						$total--;
						$rsWarning->MoveNext();
						continue;
					}
				}	
			}else{
				// by group				
				if($rsWarning->fields['total_group'] > 0){
					$checkGroup = $bd->checkGroup($rsWarning->fields['idtopic'], $_SESSION['SES_PERSON_GROUPS']);
					if($checkGroup->fields['chk'] == 0){
						$total--;
						$rsWarning->MoveNext();
						continue;
					}
				}					
			}	
            $rsWarning->MoveNext();
        }		
		$smarty->assign('total_warnings', $total);
        $smarty->display('warnings_hd.tpl.html');
    }

    public function json() {
    	$smarty = $this->retornaSmarty();
    	$langVars = $smarty->get_config_vars();
		
        $prog = "";
        $path = "";

        $page = $_POST['page'];
        $rp = $_POST['rp'];
        $sortorder = $_POST['sortorder'];

        if (!$sortorder)
            $sortorder = 'asc';

        if (!$page)
            $page = 1;
        if (!$rp)
            $rp = 10;

        $start = (($page - 1) * $rp);

        $limit = "LIMIT $start, $rp";

        $query = $_POST['query'];
        $qtype = $_POST['qtype'];

        $sortname = $_POST['sortname'];
        $sortorder = $_POST['sortorder'];

        $where = "";
        if ($query) {
            switch ($qtype) {
                case 'name':
                    $where .= "AND  $qtype LIKE '$query%' ";
                    break;
                default:
                    $where .= "";
                    break;
            }
        }				
		
        if (!$sortname or !$sortorder) {
            
        } else {
            $order = " ORDER BY $sortname $sortorder ";
        }

        $limit = "LIMIT $start, $rp";
		
		$idperson = $_SESSION['SES_COD_USUARIO'];
		$idcompany = $_SESSION['SES_COD_EMPRESA'];
		$database = $this->getConfig('db_connect');

		switch ($_POST['COD_STATUS']) {
			case '1': // New
				if ($database == 'mysqlt') {
		            $where = "AND (a.dtend > NOW() AND a.dtstart <= NOW() OR a.dtend = '0000-00-00 00:00:00') AND (a.idmessage NOT IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idperson))";
		        } elseif ($database == 'oci8po') {
		            $where = "AND (a.dtend > SYSDATE AND a.dtstart <= SYSDATE OR a.dtend IS NULL) AND (a.idmessage NOT IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idperson))";
		        }				
				break;
			case '2': // Read
				if ($database == 'mysqlt') {
		            $where = "AND (a.dtend > NOW() AND a.dtstart <= NOW() OR a.dtend = '0000-00-00 00:00:00') AND (a.idmessage IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idperson))";
		        } elseif ($database == 'oci8po') {
		            $where = "AND (a.dtend > SYSDATE AND a.dtstart <= SYSDATE OR a.dtend IS NULL) AND (a.idmessage IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idperson))";
		        }
				break;
			case '3': // Closed
				if ($database == 'mysqlt') {
		            $where = "AND (a.dtend < NOW() AND a.dtend != '0000-00-00 00:00:00')";
		        } elseif ($database == 'oci8po') {
		            $where = "AND (a.dtend < SYSDATE AND a.dtend IS NOT NULL)";				
		        }
				break;
			
			default:
				
				break;
		}
		

        $bd = new warning_model();		
		$rsWarning = $bd->selectWarning($where, $order, $limit);
		
		if ($database == 'mysqlt') {
            $rstotal = $this->found_rows();
            $total = $rstotal->fields['found_rows'];
        } elseif ($database == 'oci8po') {
            if(isset($rsWarning->fields['rnum']))
            	$total = $rsWarning->fields['rnum'];
            else 
            	$total = 0;
        }        
		
        $data['page'] = $page;
        $data['total'] = $total;
        $rows = "";
        while (!$rsWarning->EOF) {
				
			if($_SESSION['SES_COD_TIPO'] == 2){//USER
				if($rsWarning->fields['total_company'] > 0){
					$checkCompany = $bd->checkCompany($rsWarning->fields['idtopic'], $idcompany);
					if($checkCompany->fields['chk'] == 0){
						$data['total']--;
						$rsWarning->MoveNext();
						continue;
					}
				}	
			}else{
				// by group
				
				if($rsWarning->fields['total_group'] > 0){
					$checkGroup = $bd->checkGroup($rsWarning->fields['idtopic'], $_SESSION['SES_PERSON_GROUPS']);
					if($checkGroup->fields['chk'] == 0){
						$data['total']--;
						$rsWarning->MoveNext();
						continue;
					}
				}					
			}			
			
			
            $rows[] = array(
                "id" => $rsWarning->fields['idmessage'],
                "cell" => array(
                    "<a href='javascript:;' class='openWarning linhas' rel='".$rsWarning->fields['idmessage']."'>".$rsWarning->fields['title_topic']."</a>",
                    "<a href='javascript:;' class='openWarning linhas' rel='".$rsWarning->fields['idmessage']."'>".$rsWarning->fields['title_warning']."</a>"
                )
            );
            $rsWarning->MoveNext();
        }
        
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }
	
	public function getWarningInfo(){
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$idperson = $_SESSION['SES_COD_USUARIO'];
		$bdw = new warning_model();
		$database = $this->getConfig('db_connect');
        
		$chkRead = $bdw->checkRead($idperson, $id);
		if($chkRead->fields['total'] == 0){
			$bdw->setRead($idperson, $id);
		}		
		
        $rsWarning = $bdw->selectWarning("AND a.idmessage = $id");
		
		
		$smarty->assign('title_topic', $rsWarning->fields['title_topic']);
		$smarty->assign('title_warning', $rsWarning->fields['title_warning']);
		$smarty->assign('description', $rsWarning->fields['description']);
		
        if ($database == 'mysqlt') {
            $smarty->assign('datestart', $this->formatDate($rsWarning->fields['dtstart']));
            $smarty->assign('timestart', $this->formatHour($rsWarning->fields['dtstart']));
        } elseif ($database == 'oci8po') {
            $smarty->assign('datestart', $rsWarning->fields['dtstart']);
            $smarty->assign('timestart', '');
        }	
		
		if($rsWarning->fields['dtend'] == "0000-00-00 00:00:00" || empty($rsWarning->fields['dtend']) ){
			$smarty->assign('until', 'S');
		}else{
            
            if ($database == 'mysqlt') {
                $smarty->assign('dateend', $this->formatDate($rsWarning->fields['dtend']));
                $smarty->assign('timeend', $this->formatHour($rsWarning->fields['dtend']));
            } elseif ($database == 'oci8po') {
                $smarty->assign('dateend', $rsWarning->fields['dtend']);
                $smarty->assign('timeend', '');
            }
			$smarty->assign('until', 'N');
		}		
		$smarty->assign('showin', $rsWarning->fields['showin']);		
		$smarty->display('modais/warning.tpl.html');
	}
	
}