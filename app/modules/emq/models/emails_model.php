<?php

if(class_exists('Model')) {
    class dynamicEmail_model extends Model {}
} elseif(class_exists('cronModel')) {
    class dynamicEmail_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class dynamicEmail_model extends apiModel {}
}

class emails_model extends dynamicEmail_model
{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function getEmailsStats($where = NULL, $group = NULL, $order = NULL, $limit = NULL){
        
        $sql = "SELECT d.idemail AS idemail, b.idperson AS idperson, b.subject AS `subject`, c.email AS email,
                        a.recipient_email AS recipient_email, d.idemail_status  AS idemail_status,
                        e.description AS description, e.color AS color, IFNULL(f.ts,UNIX_TIMESTAMP(a.dtentry)) AS ts,
                        h.id AS id,
                        (SELECT idstate FROM emq_tbemail_history WHERE (idemail = h.idemail) ORDER BY ts DESC LIMIT 1) AS idstate,
                        (SELECT ts FROM emq_tbemail_history WHERE (idemail = h.idemail) ORDER BY ts DESC LIMIT 1) AS ts_state,
                        IF(ISNULL((SELECT idstate FROM emq_tbemail_history WHERE (idemail = h.idemail) ORDER BY ts DESC LIMIT 1)),IF((d.idemail_status = 1),0,1),(SELECT idstate FROM emq_tbemail_history WHERE (idemail = h.idemail) ORDER BY ts DESC LIMIT 1)) AS sidx_state
                  FROM emq_tbspool_recipient a
                  JOIN emq_tbspool b
                    ON a.idspool = b.idspool
                  JOIN tbperson c
                    ON b.idperson = c.idperson
                  JOIN emq_tbspool_recipient_has_tbemail d
                    ON a.idspool_recipient = d.idspool_recipient
                  JOIN tbemail_status e
                    ON d.idemail_status = e.idemail_status
                  JOIN tbemail f
                    ON f.idemail = d.idemail
       LEFT OUTER JOIN tbemail_has_mandrill g
                    ON g.idemail = f.idemail
       LEFT OUTER JOIN emq_tbemail_api h
                    ON h.id = g.idmandrill 	
                $where $group $order $limit";
        //echo "{$sql}\n";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function countEmails($where = NULL){
        $sql = "SELECT count(d.idemail) total  
                  FROM emq_tbspool_recipient a 
                  JOIN emq_tbspool b
                    ON a.idspool = b.idspool
                  JOIN tbperson c
                    ON b.idperson = c.idperson
                  JOIN emq_tbspool_recipient_has_tbemail d
		            ON a.idspool_recipient = d.idspool_recipient
                  JOIN tbemail_status e
                    ON d.idemail_status = e.idemail_status
                  JOIN tbemail f
                    ON f.idemail = d.idemail 
       LEFT OUTER JOIN tbemail_has_mandrill g
		            ON g.idemail = f.idemail
       LEFT OUTER JOIN emq_tbemail_api h
		            ON h.id = g.idmandrill       
       LEFT OUTER JOIN emq_tbemail_history i
		            ON i.idemail = h.idemail	
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

    public function insertEmailParent($enrollmentID,$txtEmail){
        $sql = "INSERT INTO emq_tbemail_parent (enrollment_id, email, dtentry) 
                  VALUES('$enrollmentID','$txtEmail',NOW())";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $this->db->insert_Id();
    }

    public function insertSpool($idperson,$sendertitle,$subject,$body){
        $sql = "INSERT INTO emq_tbspool (idperson,sender_title,`subject`,body, dtentry) 
                  VALUES($idperson,'$sendertitle','$subject','$body',NOW())";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $this->db->insert_Id();
    }

    public function insertSpoolRecipient($idspool,$recipname,$recipemail,$idserver){
        $sql = "INSERT INTO emq_tbspool_recipient (idspool,recipient_name,recipient_email,dtentry,idemail_server) 
                  VALUES($idspool,'$recipname','$recipemail',NOW(),$idserver)";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $this->db->insert_Id();
    }

    public function getParent($where = NULL){
        $sql = "SELECT idemail_parent, enrollment_id, email FROM emq_tbemail_parent $where";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function saveAttachment($idspool,$file_name){
        $sql =  "
                INSERT INTO emq_tbspool_attachment (
                  idspool,
                  file_name
                )
                values
                  (
                    $idspool,
                    '$file_name'
                  ) ;

                ";
        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $this->db->Insert_ID( );
    }

    public function getSpoolToSend($where = NULL, $group = NULL, $order = NULL, $limit = NULL){
        $sql = "SELECT f.idemail,a.idspool, b.idspool_recipient, sender_title, c.email sender, a.subject, a.body,
                        recipient_name,recipient_email, b.idemail_server, d.name server_name, 
                        GROUP_CONCAT(file_name ORDER BY idspool_attachment) attachments,
                        GROUP_CONCAT(idspool_attachment ORDER BY idspool_attachment) idattachments,
                        g.idmodule, h.name module_name
                  FROM emq_tbspool a
                  JOIN emq_tbspool_recipient b
                    ON a.idspool = b.idspool
                  JOIN tbperson c
                    ON a.idperson = c.idperson
                  JOIN tbemail_server d
                    ON b.idemail_server = d.idemail_server
       LEFT OUTER JOIN emq_tbspool_attachment e
                    ON a.idspool = e.idspool
                  JOIN emq_tbspool_recipient_has_tbemail f
                    ON f.idspool_recipient = b.idspool_recipient
                  JOIN tbemail g
                    ON g.idemail = f.idemail
                  JOIN tbmodule h
                    ON h.idmodule = g.idmodule
                $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
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
        $sql = "INSERT INTO emq_tbspool_recipient_has_tbemail (idemail,idspool_recipient)
                     VALUES ($idemail,$idspool_recipient)";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function updateRecipientSent($idspool_recipient){
        $sql = "UPDATE emq_tbspool_recipient_has_tbemail
                   SET idemail_status = 2
                 WHERE idspool_recipient = $idspool_recipient";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function insertRecipientStudentBind($idrecipient,$idstudent){
        $sql = "INSERT INTO emq_tbspool_recipient_has_student (idspool_recipient,idstudent) 
                  VALUES($idrecipient,$idstudent)";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function insertBindEmail($idemail,$idspool_recipient,$idemail_status){
        $sql = "INSERT INTO emq_tbspool_recipient_has_tbemail (idspool_recipient,idemail,idemail_status) 
                  VALUES($idspool_recipient,$idemail,$idemail_status)";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function getEmail($idemail){
        $sql = "SELECT b.subject, b.body, c.email, recipient_name,recipient_email, d.idemail_status,description, color, IFNULL(j.ts,
                       UNIX_TIMESTAMP(a.dtentry)) ts, GROUP_CONCAT(file_name) attachs, IFNULL(h.idintranet,h.idperseus) idenrollment, 
                        i.name student_name, idmandrill, l.idstate
                  FROM emq_tbspool_recipient a 
                  JOIN emq_tbspool b
                    ON a.idspool = b.idspool
                  JOIN tbperson c
                    ON b.idperson = c.idperson
                  JOIN emq_tbspool_recipient_has_tbemail d
		            ON a.idspool_recipient = d.idspool_recipient
                  JOIN tbemail_status e
                    ON d.idemail_status = e.idemail_status
       LEFT OUTER JOIN emq_tbspool_attachment f
		            ON f.idspool = b.idspool
       LEFT OUTER JOIN emq_tbspool_recipient_has_student g
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
		         WHERE d.idemail = $idemail
	          GROUP BY d.idemail";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
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

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function getEmailStateName($idstate){
        $sql = "SELECT `state-name` FROM emq_tbemail_state WHERE idstate = $idstate";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function deleteSpoolByID($idspool){
        $sql = "CALL emq_deleteSpool($idspool)";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function updateRecipientStatus($idspool_recipient,$idstatus){
        $sql = "UPDATE emq_tbspool_recipient_has_tbemail
                   SET idemail_status = $idstatus
                 WHERE idspool_recipient = $idspool_recipient";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function getSpoolData($where=null,$group=null,$order=null,$limit=null)
    {
        $sql = "SELECT s.idspool, `subject`, p.name sender_name, p.email sender_email, DATE_FORMAT(dtentry,'%d/%m/%Y') fmt_dtentry
                  FROM emq_tbspool s, tbperson p
                 WHERE s.idperson = p.idperson
                 $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function getSentStats($where = NULL, $order = NULL, $limit = NULL){
        $sql = "SELECT DISTINCT a.idspool, b.subject, DATE_FORMAT(b.dtentry,\"%d/%m/%Y %H:%i:%S\") fmt_dtentry,
                        COUNT(a.idspool_recipient) total_sent,
                        (SELECT COUNT(idemail) FROM emq_tbemail_history WHERE idemail = g.idemail AND idstate = 1) total_delivered,
                        (SELECT COUNT(idemail) FROM emq_tbemail_history WHERE idemail = g.idemail AND idstate = 5) total_opened,
                        (SELECT COUNT(idemail) FROM emq_tbemail_history WHERE idemail = g.idemail AND idstate = 7) total_rejected
                  FROM emq_tbspool_recipient a
                  JOIN emq_tbspool b
                    ON b.idspool = a.idspool
                  JOIN emq_tbspool_recipient_has_tbemail c
                    ON c.idspool_recipient = a.idspool_recipient
                  JOIN tbemail_status d
                    ON d.idemail_status = c.idemail_status
                  JOIN tbemail e
                    ON e.idemail = c.idemail
       LEFT OUTER JOIN tbemail_has_mandrill f
                    ON f.idemail = e.idemail
       LEFT OUTER JOIN emq_tbemail_api g
                    ON g.id = f.idmandrill
                $where 
              GROUP BY a.idspool 
                $order $limit";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getRecipColSize($year=null,$classID){
        $yeartmp = !$year ? date("Y") : $year;

        $sql = "SELECT DISTINCT COUNT(idparent) total
                  FROM acd_tbstudent_has_acd_tbparent a, acd_tbenrollment b, acd_tbturma c, acd_tbserie d
                 WHERE a.idstudent = b.idstudent
                   AND b.idturma = c.idturma
                   AND c.idserie = d.idserie
                   AND (b.idturma IN({$classID}))
                   AND b.year = {$yeartmp}
                   AND record_status = 'A'
                   AND a.email_sms = 'Y'
              GROUP BY a.idstudent,b.idturma
              ORDER BY total DESC LIMIT 1";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getContactGroup($where=NULL,$group=NULL,$order=NULL,$limit=NULL){
        $sql = "SELECT idcontactgroup,`name`,`description`
                  FROM emq_tbcontactgroup
                  $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertContactGroup($ownerID,$groupName,$groupDescription){
        $sql = "INSERT INTO emq_tbcontactgroup (idperson,`name`,`description`)
                     VALUES ($ownerID,'$groupName','$groupDescription')";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertGroupRecipient($groupID,$recipientID,$recipientType,$studentID){
        $sql = "INSERT INTO emq_tbcontactgroup_has_person (idcontactgroup,idcontact,idcontacttype,idstudent)
                     VALUES ($groupID,$recipientID,$recipientType,NULLIF('$studentID','NULL'))";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}");

    }

    public function getGroupRecipType($groupID){
        $sql = "SELECT DISTINCT idcontacttype
                  FROM emq_tbcontactgroup_has_person
                 WHERE idcontactgroup IN ({$groupID})";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getGroupRecip($where=NULL,$group=NULL,$order=NULL,$limit=NULL){
        $sql = "SELECT idcontactgroupperson,idcontactgroup,idcontact,idcontacttype
                  FROM emq_tbcontactgroup_has_person 
                 $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getStudentByContactGroup($groupID,$year,$courseID=null){
        $courseCondF = !$courseID ? "AND f.idcurso IN (1,2,3)" : "AND f.idcurso IN ({$courseID})";
        $sql = "SELECT DISTINCT d.idstudent,pipeLatinToUtf8(c.name) `name`, COUNT(d.idcontactgroupperson) total_contacts
                  FROM acd_tbenrollment a, acd_tbstudent b, tbperson_profile c, emq_tbcontactgroup_has_person d,
                       acd_tbturma e, acd_tbserie f
                 WHERE a.idstudent = b.idstudent
                   AND b.idperson_profile = c.idperson_profile
                   AND a.idstudent = d.idstudent
                   AND a.idturma = e.idturma
                   AND e.idserie = f.idserie
                   AND a.year = {$year}
                   AND a.record_status = 'A'
                   AND d.idcontactgroup = {$groupID}
                   $courseCondF
              GROUP BY d.idstudent";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getGroupParentRecip($where=NULL,$group=NULL,$order=NULL,$limit=NULL){
        $sql = "SELECT idcontactgroupperson,idcontactgroup,idcontact,idcontacttype,
	                    c.name, c.email
                  FROM emq_tbcontactgroup_has_person a, acd_tbparent b, tbperson_profile c
                 WHERE a.idcontact = b.idparent
                   AND b.idperson_profile = c.idperson_profile 
                 $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getGroupRecipColSize($groupID,$year=null){
        $yeartmp = !$year ? date("Y") : $year;

        $sql = "SELECT COUNT(d.idcontactgroupperson) total
                  FROM acd_tbenrollment a, acd_tbstudent b, tbperson_profile c, emq_tbcontactgroup_has_person d,
                       acd_tbturma e, acd_tbserie f
                 WHERE a.idstudent = b.idstudent
                   AND b.idperson_profile = c.idperson_profile
                   AND a.idstudent = d.idstudent
                   AND a.idturma = e.idturma
                   AND e.idserie = f.idserie
                   AND a.year = {$yeartmp}
                   AND a.record_status = 'A'
                   AND d.idcontactgroup IN ({$groupID})
              GROUP BY d.idstudent,f.idcurso
              ORDER BY total DESC LIMIT 1";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getFeaturesData($fields,$table,$where=NULL,$group=NULL,$order=NULL,$limit=NULL){
        $sql = "SELECT $fields FROM $table 	
                $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function getAcdAlertRecip($where=NULL,$group=NULL,$order=NULL,$limit=NULL){
        $sql = "SELECT `name`, email
                  FROM acd_tbalertrecip_has_curso a, tbperson b
                 WHERE a.idperson = b.idperson 	
                $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


}
