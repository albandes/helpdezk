<?php
class relOperator extends Controllers {

    public function index() {
        
        $smarty = $this->retornaSmarty();

        $db = new logos_model();

        $reportslogo = $db->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);
        
        $smarty->display('relOperator.tpl.html');
    }
	
	public function table_json() {
		include 'includes/classes/pipegrep/pipegrep.php';
		
		$pipe = new pipegrep();
		$db = new relOperator_model();
		$db_person = new person_model();
        
        $date_field_hour = "apont.entry_date";
		// 30/09/2014 09:27 Rafael Stoever
      	$date_interval_hour = $pipe->oracle_date_condition($date_field_hour, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		if ($date_interval_hour) $date_interval_hour = "AND " . $date_interval_hour;
		
		$date_field_request = "solicitacao.entry_date";
		// 30/09/2014 09:27 Rafael Stoever
      	$date_interval_request = $pipe->oracle_date_condition($date_field_request, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;		
		if ($date_interval_request) $date_interval_request = "AND " . $date_interval_request;
		
		
        $select = $db_person->selectPerson("AND tbp.idtypeperson IN(1,3) and tbp.status = 'A' ", "ORDER BY name ASC");
		
		$output = array();
		
        while (!$select->EOF) {
            $idperson = $select->fields['idperson'];
            $personname = $select->fields['name'];
			
            
			$rs = $db->getSummarizedOperator($idperson, $date_interval_hour, $date_interval_request);
			$company = $rs->fields['company'];
			// changed 30/09/2014 09:27 Rafael Stoever
			$new = $rs->fields['new'];
			// changed 30/09/2014 09:27 Rafael Stoever
			$repassed = $rs->fields["repassed"];
			// changed 30/09/2014 09:27 Rafael Stoever
			$on_attendance = $rs->fields["on_attendance"];
			// changed 30/09/2014 09:27 Rafael Stoever
			$finish = $rs->fields["finish"];
			$total_req = $new + $repassed + $on_attendance + $finish;			
			// changed 30/09/2014 09:27 Rafael Stoever
			$normal = $pipe->conv_minute_hour($rs->fields["total_normal"]);
			// changed 30/09/2014 09:27 Rafael Stoever
			$extra = $pipe->conv_minute_hour($rs->fields["total_extra"]);
			// changed 30/09/2014 09:27 Rafael Stoever
			$telephone = $pipe->conv_minute_hour($rs->fields["total_telephone"]);
			// changed 30/09/2014 09:27 Rafael Stoever
			$total_hour = $pipe->conv_minute_hour(($rs->fields["total_normal"] + $rs->fields["total_extra"] + $rs->fields["total_telephone"]));
			// changed 30/09/2014 09:27 Rafael Stoever
			$total_hour_no_convert = number_format(($rs->fields["total_normal"] + $rs->fields["total_extra"] + $rs->fields["total_telephone"])/60, 2, ",",".");			
			$output['result'][$rs->fields['department']]['company'] = $company; 
			$output['result'][$rs->fields['department']]['user'][] = array(
            					"name"  		=> $personname,
            					"company"		=> $company,
            					"new"			=> $new,
            					"repassed"		=> $repassed,
            					"on_attendance"	=> $on_attendance,
            					"finish"		=> $finish,
            					"total_req"		=> $total_req,
            					"normal"		=> $normal,
            					"extra"			=> $extra,
            					"tel"			=> $telephone,
            					"total_hour"	=> $total_hour
                            ) ;
						
            $total_all_req += $total_req;
			// changed 30/09/2014 09:27 Rafael Stoever
			$total_all_hour += $rs->fields["total_normal"] + $rs->fields["total_extra"] + $rs->fields["total_telephone"];
			
			$output['result'][$rs->fields['department']]['total']['total_all_req'] += $total_req;
			// changed 30/09/2014 09:27 Rafael Stoever
			$output['result'][$rs->fields['department']]['total']['total_all_hour'] += $rs->fields["total_normal"] + $rs->fields["total_extra"] + $rs->fields["total_telephone"];
			
            $select->MoveNext();
        }		
		
		//Get value of "total_all_hour" for each department and convert the hour
		foreach ($output['result'] as $key => $val) {
			$output['result'][$key]['total']['total_all_hour'] = $pipe->conv_minute_hour($output['result'][$key]['total']['total_all_hour']);
		}		
		
		$output['total_all'] = array(
            					"total_all_req"  	=> $total_all_req,
            					"total_all_hour"	=> $pipe->conv_minute_hour($total_all_hour)
		                      ) ;

        echo json_encode($output);

    }
}
?>
