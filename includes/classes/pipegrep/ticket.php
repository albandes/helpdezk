<?php
/**
 * Created by PhpStorm.
 * User: rogerio.albandes
 * Date: 25/09/2019
 * Time: 08:20
 */

class ticket {

    public function __construct($debug = '')
    {
        session_start();

        if ($debug == 'debug')
            $this->debug = true ;

        $this->_loadModel('helpdezk/ticket_model');
        $this->_dbTicket = new ticket_model();

        $this->_loadModel('admin/person_model');
        $this->_dbPerson = new person_model();

        $this->_loadModel('helpdezk/ticketrules_model');
        $this->_dbTicketRules = new ticketrules_model();

        $this->_loadModel('helpdezk/service_model');
        $this->_dbService = new service_model();

        $this->_loadModel('helpdezk/groups_model');
        $this->_dbGroups = new groups_model();


    }


    public function _loadModel($modelName)
    {
        $modelPath = '/app/modules/';

        $arrParts = explode("/", $modelName);
        $class = $arrParts[1];
        $file = HELPDEZK_PATH . $modelPath . $arrParts[0] . '/models/' . $class . '.php';

        spl_autoload_register(function ($class) use( &$file) {
            if (file_exists($file)) {
                require_once($file);
            } else {
                die ('The model file does not exist: ' . $file);
            }
        });

    }

    public function saveRequestAttachments($a_attachments,$code_request,$attach_path)
    {
        $attach_err = 0;
        for($j = 1;$j <= count($a_attachments);$j++)
        {
            if ($this->debug ) { print "Attachment: " . $j . "\n \n"; }

            $filename = addslashes($this->fixText($a_attachments[$j]['filename'])) ;

            if ($this->debug ) { print "Filename: " . $filename . "\n \n"; }

            $idAttachment = $this->_dbTicket->saveTicketAtt($code_request,utf8_encode($filename));

            if (!$idAttachment)
                return false ;

            $file_extension = strrchr($filename, '.');
            $target = $attach_path . $idAttachment . $file_extension;
            // Downloads the attachment, in case of error, delete the database record
            if ($this->debug ) { print "Target: " . $target . "\n \n"; }
            $handle = fopen($target,"w");
            $int = fwrite($handle,$a_attachments[$j]['attachment']);
            fclose($handle);
            if (!$int)
            {
                @unlink($target);
                if ($this->debug ) { print "Unable to save the attachment #$j on the server: " . $filename . "\n \n"; }
                $ret = $this->_dbTicket->deleteAttachNote($idAttachment);
                if(!$ret)
                    return false ;

                $attach_err++;
            }
            else
            {
                if ($this->debug ) { print "Attachment #$j saved successfully: " . $filename . "\n \n"; }
            }


        }

        if($attach_err > 0)
            return false ;

        return true ;



    }

    public function getNewGroupOnlyRepass($idGroup,$idCompany)
    {
        $rsNewGroup = $this->_dbGroups->getNewGroupOnlyRepass($idGroup,$idCompany);
        if(!$rsNewGroup) {
            return false;
        } else {
            return $rsNewGroup;
        }
    }

    public function checkGroupOnlyRepass($idGroup)
    {
        $rsOnlyRep = $this->_dbGroups->checkGroupOnlyRepass($idGroup);
        /*
        if ($rsOnlyRep->fields['repass_only'] == "Y") {
            return true;
        } else {
            return false;
        }
        */

        return ($rsOnlyRep->fields['repass_only'] == "Y" ? true : false);
    }

    public function getIdPersonApprover($idItem, $idService)
    {
        return $this->_dbTicketRules->getIdPersonApproverRule($idItem, $idService);

    }

    public function getApprovalOrder($code_request,$idItem, $idService)
    {
        $count = 1;
        $values = '';

        $numRules = $this->getNumRules($idItem,$idService);
        $rsRules = $this->getRules($idItem, $idService);

        while (!$rsRules->EOF) {
            if($rsRules->fields['order'] == 1)
                $idPersonApprover = $rsRules->fields['idperson'];
            $values .= "(".$rsRules->fields['idapproval'].",". $code_request .",". $rsRules->fields['order'] .",". $rsRules->fields['idperson'] .",". $rsRules->fields['fl_recalculate'] .")";
            if($numRules != $count)
                $values .= ",";
            $count++;
            $rsRules->MoveNext();
        }

        return $values ;

    }

    public function getRules($idItem, $idService)
    {
        $ret = $this->_dbTicketRules->getRule($idItem, $idService);
        if (!$ret) {
            return false ;
        } else {
            return $ret;
        }

    }

    public function getServiceGroup($idService)
    {
        $ret = $this->_dbTicket->getServiceGroup($idService);
        if (!$ret) {
            return false ;
        } else {
            return $ret;
        }
    }

    public function getStartDate ()
    {
        $insertHour = date("H:i");
        $insertDate = date("Y-m-d");
        return $insertDate." ".$insertHour;

    }

    public function getDueDate($idPriority, $idService)
    {
        $insertHour = date("H:i");
        $insertDate = date("Y-m-d");
        $startDate = $this->getStartDate();
        return $this->getExpireDate($startDate, $idPriority, $idService);
    }

    private function checkValidBusinessDay($date,$businessDay,$idcompany = null){

        $this->_loadModel('helpdezk/expiredate_model');
        $db = new expiredate_model();

        $numWeek = date('w',strtotime($date));

        $i = 0;
        while($i == 0){
            while (!array_key_exists($numWeek, $businessDay)) {
                $date = date ("Y-m-d H:i:s", strtotime("+1 day", strtotime($date)));
                $numWeek = date('w',strtotime($date));
            }
            $date_holy = date("Y-m-d",strtotime($date));

            $rsNationalDaysHoliday = $db->getNationalDaysHoliday($date_holy,$date_holy);
            if(!$rsNationalDaysHoliday)
                return false;

            if(isset($idcompany)){
                $rsCompanyDaysHoliday = $db->getCompanyDaysHoliday($date_holy,$date_holy,$idcompany);
                if(!$rsCompanyDaysHoliday){
                    $db->RollbackTrans();
                    return false;
                }
                $daysHoly = $rsNationalDaysHoliday->fields['num_holiday'] + $rsCompanyDaysHoliday->fields['num_holiday'];
            }else{
                $daysHoly = $rsNationalDaysHoliday->fields['num_holiday'];
            }

            if($daysHoly > 0){
                $date = date("Y-m-d H:i:s",strtotime($date." +".$daysHoly." days"));
                $numWeek = date('w',strtotime($date));
            }else{
                $i = 1;
            }
        }
        return $date;
    }

    private function checkValidBusinessHour($date,$businessDay){
        $i = 0;
        while($i == 0){
            $numWeek = date('w',strtotime($date));
            $hour = strtotime(date('H:i:s',strtotime($date)));
            $begin_morning = strtotime($businessDay[$numWeek]['begin_morning']);
            $end_morning = strtotime($businessDay[$numWeek]['end_morning']);
            $begin_afternoon = strtotime($businessDay[$numWeek]['begin_afternoon']);
            $end_afternoon = strtotime($businessDay[$numWeek]['end_afternoon']);
            if($hour >= $begin_morning && $hour <= $end_morning){
                $i = 1;
            }
            else if($hour >= $begin_afternoon && $hour <= $end_afternoon){
                $i = 1;
            }
            else{
                $date = date ("Y-m-d H:i:s", strtotime("+1 hour", strtotime($date)));
                $i = 0;
            }
        }
        return $date;
    }

    public function getExpireDate($startDate = null, $idPriority = null, $idService = null){

        if(!isset($startDate)){$startDate = date("Y-m-d H:i:s");}

        $this->_loadModel('helpdezk/expiredate_model');
        $_db = new expiredate_model();

        // If have service id
        if(isset($idService)){
            $idcompany = $_db->getIdCustumerByService($idService);

            $rsExpireDateService = $_db->getExpireDateService($idService);
            if(!$rsExpireDateService){
                return false;
            }

            $days = $rsExpireDateService->fields['days_attendance'];
            $time = $rsExpireDateService->fields['hours_attendance'];
            $type_time = $rsExpireDateService->fields['ind_hours_minutes'];

            if($days > 0){
                $days_sum = "+".$days." days";
            }
            if($time > 0){
                if($type_time == "H"){
                    $time_sum = "+".$time." hour";
                }
                elseif($type_time == "M"){
                    $time_sum = "+".$time." minutes";
                }
            }
        }

        // If have priority id and time and days are zero
        if(isset($idPriority) && $time == 0 && $days == 0){
            $rsExpireDatePriority = $_db->getExpireDatePriority($idPriority);
            if(!$rsExpireDatePriority){
                $_db->RollbackTrans();
                return false;
            }
            $days = $rsExpireDatePriority->fields['limit_days'];
            $time = $rsExpireDatePriority->fields['limit_hours'];

            if($days > 0){
                $days_sum = "+".$days." days";
            }
            if($time > 0){
                $time_sum = "+".$time." hour";
            }
        }

        if($time == 0 && $days == 0){
            $days_sum = "+0 day";
            $time_sum = "+0 hour";
            return $startDate;
        }

        $data_sum = date("Y-m-d H:i:s",strtotime($startDate." ".$days_sum." ".$time_sum));

        $date_holy_start = date("Y-m-d",strtotime($startDate)); // Separate only the inicial date to check for holidays in the period
        $date_holy_end = date("Y-m-d",strtotime($data_sum)); //Separate only the final date to check for holidays in the period

        $rsNationalDaysHoliday = $_db->getNationalDaysHoliday($date_holy_start,$date_holy_end); // Verifies the quantity of holidays in the period
        if(!$rsNationalDaysHoliday)
            return false;


        if(isset($idcompany)){
            $rsCompanyDaysHoliday = $_db->getCompanyDaysHoliday($date_holy_start,$date_holy_end,$idcompany); // Verifies the quantity of company�s holidays in the period
            if(!$rsCompanyDaysHoliday)
                return false;
            $sum_days_holidays = $rsNationalDaysHoliday->fields['num_holiday'] + $rsCompanyDaysHoliday->fields['num_holiday'];
        }else{
            $sum_days_holidays = $rsNationalDaysHoliday->fields['num_holiday'];
        }

        // Add holidays
        $data_sum = date("Y-m-d H:i:s",strtotime($data_sum." +".$sum_days_holidays." days"));

        // Working days
        $rsBusinessDays = $_db->getBusinessDays();
        if(!$rsBusinessDays)
            return false;

        while (!$rsBusinessDays->EOF) {
            $businessDay[$rsBusinessDays->fields['num_day_week']] = array(
                "begin_morning" 	=> $rsBusinessDays->fields['begin_morning'],
                "end_morning" 		=> $rsBusinessDays->fields['end_morning'],
                "begin_afternoon" 	=> $rsBusinessDays->fields['begin_afternoon'],
                "end_afternoon" 	=> $rsBusinessDays->fields['end_afternoon']
            );
            $rsBusinessDays->MoveNext();
        }

        $date_check_start = date("Y-m-d",strtotime($startDate));
        $date_check_end = date("Y-m-d",strtotime($data_sum));
        $addNotBussinesDay = 0;

        // Non-working days
        while (strtotime($date_check_start) <= strtotime($date_check_end)) {
            $numWeek = date('w',strtotime($date_check_start));
            if (!array_key_exists($numWeek, $businessDay)) {
                $addNotBussinesDay++;
            }
            $date_check_start = date ("Y-m-d", strtotime("+1 day", strtotime($date_check_start)));
        }

        $data_sum = date("Y-m-d H:i:s",strtotime($data_sum." +".$addNotBussinesDay." days")); // Add non-working days
        $data_check_bd = $this->checkValidBusinessDay($data_sum,$businessDay,$idcompany);
        $data_sum = $this->checkValidBusinessHour($data_check_bd,$businessDay); // Verify if the time is the interval of service

        // If you change the day, check to see if it is a working day
        if(strtotime(date("Y-m-d",strtotime($data_check_bd))) != strtotime(date("Y-m-d",strtotime($data_sum)))){
            $data_check_bd = $this->checkValidBusinessDay($data_sum,$businessDay,$idcompany);
            return $data_check_bd;
        }else{
            return $data_sum;
        }

    }

    function getVipPriority()
    {
        $rsVipPriority = $this->_dbTicket->getVipPriority();
        return $rsVipPriority->fields['idpriority'];
    }

    function getServicePriority($idService)
    {
        $rsServicePrio = $this->_dbTicket->getServPriority($idService);

        $idPriority = $rsServicePrio->fields['idpriority'];

        if(!$idPriority){
            $rsDefault = $this->_dbTicket->getDefaultPriority();
            $idPriority = $rsDefault->fields['idpriority'];
        }

        return $idPriority;
    }

    function hasVipPriority()
    {
        $rsVipPriority = $this->_dbTicket->checksVipPriority();
        if ($rsVipPriority->fields['rec_count'] > 0)
            return true;
        else
            return false;
    }

    public function isVipUser($idPerson)
    {
        $rsVipuser = $this->_dbTicket->checksVipUser($idPerson);
        if ($rsVipuser->fields['rec_count'] > 0)
            return true;
        else
            return false;

    }

    public function getNumRules($idItem, $idService)
    {
        $rsRules = $this->_dbTicketRules->getRule($idItem, $idService);
        return $rsRules->RecordCount();

    }

    public function getAreaTypeItemByService($idservice)
    {
        $rsCore = $this->_dbService->getCoreByService($idservice);
        if(!$rsCore) {
            return false;
        } else {
            return $rsCore;
        }
    }

    public function createRequestCode(){
        $this->_dbTicket->BeginTrans();

        $rsCode = $this->_dbTicket->getCode();
        if(!$rsCode){
            $this->_dbTicket->RollbackTrans();
            return false;
        }
        // Count month code
        $rsCountCode = $this->_dbTicket->countGetCode();
        if(!$rsCountCode){
            $this->_dbTicket->RollbackTrans();
            return false;
        }
        // If have code request
        if ($rsCountCode->fields['total']) {
            $code_request = $rsCode->fields["cod_request"];
            // Will increase the code of request
            $rsIncressCode = $this->_dbTicket->increaseCode($code_request);
            if(!$rsIncressCode){
                $this->_dbTicket->RollbackTrans();
                return false;
            }
        }
        else {
            //If not have code request will create a new
            $code_request = 1;
            $rsCreateCode = $this->_dbTicket->createCode($code_request);
            if(!$rsCreateCode){
                $this->_dbTicket->RollbackTrans();
                return false;
            }
        }

        $code_request = str_pad($code_request, 6, '0', STR_PAD_LEFT);
        $code_request = date("Ym") . $code_request;
        $this->_dbTicket->CommitTrans();
        return $code_request;
    }

    public function getIdPersonJuridical($idperson)
    {
        $rsPerson = $this->_dbPerson->selectPerson(" AND tbp.idperson = $idperson");
        if(!$rsPerson) {
            return false;
        } else {
            return $rsPerson->fields['idcompany'];
        }

    }

    function fixText($str)
    {
        $subject = '';
        $subject_array = imap_mime_header_decode($str);
        foreach ($subject_array AS $obj){
            $charset = strtoupper($obj->charset);
            if($charset == "ISO-8859-1" || $charset == "WINDOWS-1252"){
                $subject .= utf8_encode(rtrim($obj->text, "\t"));
            }else{
                $subject .= rtrim($obj->text, "\t");
            }

        }
        return $subject;
    }

}