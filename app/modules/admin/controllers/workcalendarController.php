<?php

class workcalendar extends Controllers {
    public $database;
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
        $this->database = $this->getConfig('db_connect');
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("workcalendar/");
        $access = $this->access($user, $program, $typeperson);
        $smarty = $this->retornaSmarty();		
		$db = new workcalendar_model();
		$getCalendar = $db->getWorkCalendar();		
		while (!$getCalendar->EOF) {			
			$calendar[$getCalendar->fields['num_day_week']] = array(
																		"business_day" 		=> $getCalendar->fields['business_day'],
																		"begin_morning" 	=> $getCalendar->fields['begin_morning'],
																		"end_morning" 		=> $getCalendar->fields['end_morning'],
																		"begin_afternoon" 	=> $getCalendar->fields['begin_afternoon'],
																		"end_afternoon" 	=> $getCalendar->fields['end_afternoon']
																	);
            $getCalendar->MoveNext();
        }		
		$smarty->assign('calendar', $calendar);
        $smarty->display('workcalendar.tpl.html');
    }	
	
    public function save() {   			
		$db = new workcalendar_model();
		$db->BeginTrans();	
		
		for($i = 0; $i <= 6; $i++){			
			if($_POST['day'][$i]) $bday = 1; else $bday = 0;
            if ($this->database == 'oci8po') {
                $data = array(
                    "business_day" 		=> $bday,
                    "begin_morning" 	=> $_POST['morning'][$i],
                    "end_morning" 		=> $_POST['morning2'][$i],
                    "begin_afternoon" 	=> $_POST['afternoon'][$i],
                    "end_afternoon" 	=> $_POST['afternoon2'][$i]
                );
            }
            else
            {
                $data = array(
                    "business_day" 		=> $bday,
                    "begin_morning" 	=> $this->formatSaveHour($_POST['morning'][$i]),
                    "end_morning" 		=> $this->formatSaveHour($_POST['morning2'][$i]),
                    "begin_afternoon" 	=> $this->formatSaveHour($_POST['afternoon'][$i]),
                    "end_afternoon" 	=> $this->formatSaveHour($_POST['afternoon2'][$i])
                );
            }

			$updCalendar = $db->updateWorkCalendar($data, $i);
			if(!$updCalendar){
				$db->RollbackTrans();
				return false;
			}			
		}		
		$db->CommitTrans();
		echo "OK";
    }  
}