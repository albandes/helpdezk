<?php

if(class_exists('Model')) {
    class dynamicBankSlipEmail_model extends Model {}
} elseif(class_exists('cronModel')) {
    class dynamicBankSlipEmail_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class dynamicBankSlipEmail_model extends apiModel {}
}

class bankslipemail_model extends dynamicBankSlipEmail_model
{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function getEmailsStats($where = NULL, $group = NULL, $order = NULL, $limit = NULL){
        $sql = "SELECT a.idspool_recipient,idemail, `subject`,a.email,recipient_email,idemail_status,`description`,color,
                       ts,id,idstate,ts_state,sidx_state, d.`name` student_name
                  FROM fin_viewEmailsStats a, fin_tbspool_recipient_has_student b, acd_tbstudent c, tbperson_profile d,
                        fin_tbbankslip_schedule e
                 WHERE a.idspool_recipient = b.idspool_recipient
                   AND b.idstudent = c.idstudent
                   AND c.idperson_profile = d.idperson_profile
                   AND (a.idcompany = e.idcompany AND a.competence = e.competence)	
                $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function countEmails($where = NULL){
        $sql = "SELECT count(idemail) total  
                  FROM fin_viewEmailsStats a, fin_tbspool_recipient_has_student b, acd_tbstudent c, tbperson_profile d
                 WHERE a.idspool_recipient = b.idspool_recipient
                   AND b.idstudent = c.idstudent
                   AND c.idperson_profile = d.idperson_profile	
                $where";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function getEmailParent($where = NULL){
        $sql = "SELECT idemail_parent, enrollment_id, email FROM emq_tbemail_parent $where";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function insertSpool($idperson,$sendertitle,$subject,$body,$push,$competence,$idcompany){
        $sql = "INSERT INTO fin_tbspool (idperson,sender_title,`subject`,body,idcompany,competence,body_push,dtentry) 
                  VALUES($idperson,'$sendertitle','$subject','$body',$idcompany,'$competence','$push',NOW())";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\n{$sql}");
    }

    public function insertSpoolRecipient($idspool,$recipname,$recipemail,$idserver,$recipPuschID,$recipSendType){
        $sql = "INSERT INTO fin_tbspool_recipient (idspool,recipient_name,recipient_email,dtentry,idemail_server,idrecipient_push,send_ticket_type) 
                  VALUES($idspool,'$recipname','$recipemail',NOW(),$idserver,'$recipPuschID','$recipSendType')";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\n{$sql}");
    }

    public function saveAttachment($idspool,$file_name,$file_dir){
        $sql =  "
                INSERT INTO fin_tbspool_attachment (
                  idspool,
                  file_name,
                  file_dir
                )
                values
                  (
                    $idspool,
                    '$file_name',
                    '$file_dir'
                  ) ;

                ";
        $ret = $this->db->Execute($sql);
        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\n{$sql}");
    }

    public function getSpoolToSend($where = NULL, $group = NULL, $order = NULL, $limit = NULL){
        $sql = "SELECT f.idemail,a.idspool, b.idspool_recipient, sender_title, c.email sender, a.subject, a.body,
                        recipient_name,recipient_email, b.idemail_server, d.name server_name, 
                        GROUP_CONCAT(file_name ORDER BY idspool_attachment) attachments,
                        GROUP_CONCAT(idspool_attachment ORDER BY idspool_attachment) idattachments,
                        GROUP_CONCAT(file_dir ORDER BY idspool_attachment) attach_dir,
                        g.idmodule, h.name module_name, a.body_push, b.idrecipient_push
                  FROM fin_tbspool a
                  JOIN fin_tbspool_recipient b
                    ON a.idspool = b.idspool
                  JOIN tbperson c
                    ON a.idperson = c.idperson
                  JOIN tbemail_server d
                    ON b.idemail_server = d.idemail_server
       LEFT OUTER JOIN fin_tbspool_attachment e
                    ON a.idspool = e.idspool
                  JOIN fin_tbspool_recipient_has_tbemail f
                    ON f.idspool_recipient = b.idspool_recipient
                  JOIN tbemail g
                    ON g.idemail = f.idemail
                  JOIN tbmodule h
                    ON h.idmodule = g.idmodule
                $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function getTrackerStatus(){
        $sql = "SELECT `value` FROM tbconfig WHERE session_name = 'TRACKER_STATUS'";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret->fields['value'];
    }

    public function insertRecipientEmailID($idemail,$idspool_recipient){
        $sql = "INSERT INTO fin_tbspool_recipient_has_tbemail (idemail,idspool_recipient)
                     VALUES ($idemail,$idspool_recipient)";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function updateRecipientSent($idspool_recipient){
        $sql = "UPDATE fin_tbspool_recipient_has_tbemail
                   SET idemail_status = 2
                 WHERE idspool_recipient = $idspool_recipient";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function insertRecipientStudentBind($idrecipient,$idstudent){
        $sql = "INSERT INTO fin_tbspool_recipient_has_student (idspool_recipient,idstudent) 
                  VALUES($idrecipient,$idstudent)";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\n{$sql}");
    }

    public function insertBindEmail($idemail,$idspool_recipient,$idemail_status){
        $sql = "INSERT INTO fin_tbspool_recipient_has_tbemail (idspool_recipient,idemail,idemail_status) 
                  VALUES($idspool_recipient,$idemail,$idemail_status)";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\n{$sql}");
    }

    public function getEmail($idemail){
        $sql = "SELECT b.subject, b.body,c.email, recipient_name,recipient_email, d.idemail_status,description, color, IFNULL(j.ts,
                       UNIX_TIMESTAMP(a.dtentry)) ts, GROUP_CONCAT(file_name) attachs, IFNULL(h.idintranet,h.idperseus) idenrollment, 
                        i.name student_name, idmandrill, l.idstate, esrv.name srvsend, b.competence, b.idcompany,
                        GROUP_CONCAT(CONCAT(file_dir,'/',file_name)) attachslinks, GROUP_CONCAT(file_dir) attachsdir, 
                        b.body_push, a.idemail_server, a.idrecipient_push, g.idstudent, GROUP_CONCAT(f.idspool_attachment) attachsid,
                        a.idspool
                  FROM fin_tbspool_recipient a 
                  JOIN fin_tbspool b
                    ON a.idspool = b.idspool
                  JOIN tbperson c
                    ON b.idperson = c.idperson
                  JOIN fin_tbspool_recipient_has_tbemail d
		            ON a.idspool_recipient = d.idspool_recipient
                  JOIN tbemail_status e
                    ON d.idemail_status = e.idemail_status
       LEFT OUTER JOIN fin_tbspool_attachment f
		            ON f.idspool = b.idspool
       LEFT OUTER JOIN fin_tbspool_recipient_has_student g
		            ON g.idspool_recipient = a.idspool_recipient
       LEFT OUTER JOIN acd_tbstudent h
		            ON h.idstudent = g.idstudent
       LEFT OUTER JOIN tbperson_profile i
		            ON i.idperson_profile = h.idperson_profile
		          JOIN tbemail j
                    ON j.idemail = d.idemail
       LEFT OUTER JOIN tbemail_has_mandrill k
		            ON k.idemail = j.idemail
       LEFT OUTER JOIN emq_tbemail_api l
		            ON l.id = k.idmandrill
		          JOIN tbemail_server esrv
		            ON esrv.idemail_server = a.idemail_server
		         WHERE d.idemail = $idemail
	          GROUP BY d.idemail";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else{
            $msg = "File: ".__FILE__." Method: ".__METHOD__."\n{$this->db->ErrorMsg()}\n{$sql}";
            return array('success' => false, 'message' => $msg);
        }
    }

    public function getLogMandrill($idmandrill){
        $sql = "SELECT ehis.idstate, `state-name`, FROM_UNIXTIME(ehis.ts,'%d/%m/%Y %H:%s:%i') ts_send, FROM_UNIXTIME(eopen.ts,'%d/%m/%Y %H:%s:%i') ts_open, 
                        FROM_UNIXTIME(ebou.ts,'%d/%m/%Y %H:%s:%i') ts_bounce, FROM_UNIXTIME(edef.ts,'%d/%m/%Y %H:%s:%i') ts_deferral, FROM_UNIXTIME(espa.ts,'%d/%m/%Y %H:%s:%i') ts_spam, 
                        FROM_UNIXTIME(erej.ts,'%d/%m/%Y %H:%s:%i') ts_reject, bounce_description, ebou.diag bounce_diag, edef.diag def_diag,
                        os_icon, mobile, city, country, user_agent, ua_family
                   FROM emq_tbemail_history ehis
                   JOIN emq_tbemail_api eapi
                     ON eapi.idemail = ehis.idemail
                   JOIN emq_tbemail_state esta
                     ON esta.idstate = ehis.idstate
        LEFT OUTER JOIN emq_tbemail_open eopen
                     ON eopen.idemail = ehis.idemail
        LEFT OUTER JOIN emq_tbemail_bounce ebou
                     ON ebou.idemail = ehis.idemail
        LEFT OUTER JOIN emq_tbemail_deferral edef
                     ON edef.idemail = ehis.idemail
        LEFT OUTER JOIN emq_tbemail_spam espa
                     ON espa.idemail = ehis.idemail
        LEFT OUTER JOIN emq_tbemail_reject erej
                     ON erej.idemail = ehis.idemail
                  WHERE eapi.id = '$idmandrill'
               ORDER BY ehis.ts DESC";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else{
            $msg = "File: ".__FILE__." Method: ".__METHOD__."\n{$this->db->ErrorMsg()}\n{$sql}";
            return array('success' => false, 'message' => $msg);
        }
    }

    public function getEmailStateName($idstate){
        $sql = "SELECT `state-name` FROM emq_tbemail_state WHERE idstate = $idstate";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function getTemplate($sessionName){
        $sql = "SELECT a.`name`, a.description, a.template_push 
                    FROM fin_tbtemplate_email a, fin_tbconfig_has_template b, fin_tbconfig c
                   WHERE a.idtemplate = b.idtemplate
                     AND c.idconfig = b.idconfig
                     AND c.session_name = '$sessionName'";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' =>$ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertSchedule($personID,$companyID,$competence){
        $sql = "INSERT INTO fin_tbbankslip_schedule (idperson,idcompany,competence,dtentry) 
                  VALUES($personID,$companyID,'$competence',NOW())";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'id' =>$ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'id' => '');

    }

    public function getSchedule($where=NULL,$group=NULL,$order=NULL,$limit=NULL){
        $sql = "SELECT idschedule, idcompany, b.name company, competence, DATE_FORMAT(dtentry,'%d/%m/%Y') fmt_dtentry,
                       idperseus,course_condition
                  FROM fin_tbbankslip_schedule a, tbperson b, fin_tbcompany_has_legacy c
                 WHERE a.idcompany = b.idperson
                   AND a.idcompany = c.idperson
                $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertBankSlip($params){
        $sql = "INSERT INTO fin_tbbankslip (idstudent,idboleto,parcela,vencimento,competencia,multa,
                              juro,valor,nossonumero,cedente,banco,sacado,enderecocobranca,cep,cidade,nomecedente,
                              linhadigitavel,cnpjcedente,contacedente,dvcontacedente,idbanco,flagprotesto,idcompany,dtentry)
                     VALUES ({$params['idstudent']},{$params['idboleto']},'{$params['idparcela']}','{$params['vencimento']}',
                             '{$params['competencia']}',{$params['multa']},{$params['juro']},{$params['valor']},'{$params['nossonumero']}',
                             '{$params['cedente']}','{$params['banco']}','{$params['sacado']}','{$params['enderecocobranca']}','{$params['cep']}','{$params['cidade']}',
                             '{$params['nomecedente']}','{$params['linhadigitavel']}','{$params['cnpjcedente']}','{$params['contacedente']}',
                             '{$params['dvcontacedente']}','{$params['idbanco']}','{$params['flagprotesto']}',
                             {$params['idcompany']},NOW())";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function updateScheduleProcess($scheduleID){
        $sql = "UPDATE fin_tbbankslip_schedule SET dtprocess = NOW()
                 WHERE idschedule = $scheduleID";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getBankSlip($where = NULL, $group = NULL, $order = NULL, $limit = NULL){
        $sql = "SELECT a.idbankslip,idstudent,idboleto,parcela,vencimento,competencia,multa,
                        juro,valor,nossonumero,cedente,banco,sacado,enderecocobranca,
                        cep,cidade,nomecedente,
                        linhadigitavel,cnpjcedente,contacedente,dvcontacedente,idbanco,flagprotesto, 
                          c.name sender_name,c.email sender_email, b.idperson idsender, a.idcompany, 
                          d.branch_code agencia
                  FROM fin_tbbankslip a, fin_tbbankslip_schedule b, tbperson c, fin_tbbank_has_company d
                 WHERE (a.idcompany = b.idcompany AND a.competencia = b.competence)
                   AND b.idperson = c.idperson
                   AND (a.idcompany = d.idperson AND a.idbanco = d.idperseus)
                  $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getFINFeatures($where = NULL, $group = NULL, $order = NULL, $limit = NULL){
        $sql = "SELECT session_name, `value` FROM fin_tbconfig 	
                $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateBankSlipProcess($bankslipID){
        $sql = "UPDATE fin_tbbankslip SET dtprocess = NOW()
                 WHERE idbankslip = $bankslipID";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getDiscount($where = NULL, $group = NULL, $order = NULL, $limit = NULL){
        $sql = "SELECT iddiscount, discount_year, discount_value
                  FROM fin_tbbankslip_discount
                  $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    function makeErrorMessage($line,$method,$error,$query='')
    {
        $aRet = array(
            "status" => 'Error',
            "message" => "[DB Error] method: " . $method . ", line: " . $line . ", Db message: " . $error . ", Query: " . $query
        );
        return $aRet;
    }

    public function updateRecipientStatus($idspool_recipient,$idstatus){
        $sql = "UPDATE fin_tbspool_recipient_has_tbemail
                   SET idemail_status = $idstatus
                 WHERE idspool_recipient = $idspool_recipient";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updatePushStatus($idspool_recipient,$idstatus){
        $sql = "UPDATE fin_tbspool_recipient_has_tbemail
                   SET push_status = $idstatus
                 WHERE idspool_recipient = $idspool_recipient";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}
