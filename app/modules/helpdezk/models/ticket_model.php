<?php
if(class_exists('Model')) {
    class DynamicTicket_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicTicket_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicTicket_model extends apiModel {}
}

class ticket_model extends DynamicTicket_model
{

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');


    }

    public function getIdStatusSource($idstatus){
        $vSql = "select idstatus_source from hdk_tbstatus where idstatus = '$idstatus'";
        $ret = $this->db->Execute($vSql);
        if (!$ret) {
            $sError = $vSql. "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['idstatus_source'];
    }

    public function getRequest($entry_date, $expire_date, $where = NULL, $sortname = NULL, $sortorder = NULL, $start = NULL, $rp = NULL, $iduser){

        if ($this->database == 'oci8po'){
            $v_sql = "
                select  distinct		   a.code_request       ,
				   a.subject            ,
				   resp.name         	as in_charge,
				   inch.id_in_charge    ,
				   a.expire_date        ,
				   $expire_date			,
				   a.entry_date         ,
				   $entry_date			,
				   b.user_view          as statusview,
				   a.idtype             ,
				   a.idperson_owner     as owner,
				   b.idstatus           as status,
				   b.color              as color_status,
				   a.flag_opened        ,
				   pers.name            as personname,
				   a.entry_date as xx,
				   (SELECT
					   count(idrequest_attachment)
					FROM hdk_tbrequest_attachment
					WHERE code_request = a.code_request) as totatt
				FROM hdk_tbrequest a,
					hdk_tbstatus b,
					tbperson pers,
					tbperson resp,
					hdk_tbrequest_in_charge inch
				WHERE a.idstatus = b.idstatus
					AND a.idperson_owner = pers.idperson
					AND inch.id_in_charge = resp.idperson
					and inch.code_request = a.code_request
					and a.code_request = inch.code_request
					and inch.ind_in_charge = 1
					and a.idperson_owner = $iduser
					$where
				ORDER BY
					$sortname
					$sortorder
              ";
            if (strlen($rp) > 2 ){
                $v_limit = explode(",",str_ireplace(array("limit", " "),"",trim($rp)));
                $v_sql =  $this->setLimitOracle($v_sql, $v_limit[1], $v_limit[0]);
            }
        }else{
            $v_sql = "
                select SQL_CALC_FOUND_ROWS
				   a.code_request       ,
				   a.subject            ,
				   resp.name         	as in_charge,
				   inch.id_in_charge    ,
				   a.expire_date        ,
				   $expire_date			,
				   a.entry_date         ,
				   $entry_date			,
				   b.user_view          as statusview,
				   a.idtype             ,
				   a.idperson_owner     as owner,
				   b.idstatus           as status,
				   b.color              as color_status,
				   a.flag_opened        ,
				   pers.name            as personname,
				   (SELECT
					   count(idrequest_attachment)
					FROM hdk_tbrequest_attachment
					WHERE code_request = a.code_request) as totatt
				FROM (hdk_tbrequest a,
					hdk_tbstatus b,
					tbperson pers,
					tbperson resp,
					hdk_tbrequest_in_charge inch)
				WHERE a.idstatus = b.idstatus
					AND a.idperson_owner = pers.idperson
					AND inch.id_in_charge = resp.idperson
					and inch.code_request = a.code_request
					and a.code_request = inch.code_request
					and inch.ind_in_charge = 1
					and a.idperson_owner = $iduser
					$where
				ORDER BY
					$sortname
					$sortorder
				LIMIT $start, $rp
              ";
        }

		//echo($v_sql);
        $ret = $this->select($v_sql);

        if(!$ret) {
            $sError = $v_sql. " File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
        }
        return $ret;
    }

    public function getNumberRequests($where = NULL, $iduser){
        $query =		     "
							  SELECT COUNT(distinct a.code_request) as total FROM (hdk_tbrequest a,
								  hdk_tbstatus b,
								  tbperson pers,
								  tbperson resp,
								  hdk_tbrequest_in_charge inch)
								  left join hdk_tbgroup grp
									on (inch.id_in_charge = grp.idperson
										and resp.idperson = grp.idperson)
                              WHERE a.idstatus = b.idstatus
	                              AND a.idperson_owner = pers.idperson
								  AND inch.id_in_charge = resp.idperson
								  AND inch.code_request = a.code_request
								  AND a.code_request = inch.code_request
								  AND inch.ind_in_charge=1
								  AND a.idperson_owner = $iduser
								  $where
                              ";
        //echo($query);
		$ret = $this->select($query);
        if(!$ret) {
            $sError = $query . "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
        }
        return $ret->fields['total'];
    }

    public function getRequestData($where=null,$order=null,$limit=null) {
        $sql = " SELECT * FROM hdk_viewRequestData  $where  $order  $limit";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getTicketStats($lang,$where=null,$order=null,$limit=null) {

        $sql = " SELECT
                  TIMESTAMPDIFF(SECOND, NOW(), a.expire_date) AS seconds,
                  pipeFormatDateTime('$lang',a.expire_date) AS expire_date,
                  UNIX_TIMESTAMP(a.expire_date) AS ts_expire,
                  a.code_request,
                  a.subject
                FROM
                  hdk_viewRequestData a   $where  $order  $limit";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function updateFlag($code, $flag) {
		$ret = $this->db->Execute("update hdk_tbrequest set
                                   flag_opened = $flag
                                   where code_request = '$code'");
		if (!$ret) {
			$sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
			$this->error($sError);
			return false;
		}
		return $ret;
	}

    public function selectArea() {
        $sql = "
                SELECT
                  a.idarea,
                  a.name,
                  (SELECT
                    CASE
                      WHEN b.default = a.idarea
                      THEN 1
                      ELSE 0
                    END
                  FROM
                    hdk_tbcore_default b
                  WHERE b.table = 'area') AS `default`
                FROM
                  hdk_tbcore_area a
                WHERE a.status = 'A'
                ORDER BY a.name ASC
                ";

        $ret = $this->select($sql);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectIdCoreDefault($table)
    {
        $sql =  "
                SELECT
                  `default`
                FROM
                  hdk_tbcore_default
                WHERE `table` = '$table'
                ";
        $ret = $this->select($sql);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['default'];

    }

    public function selectType($area, $order = 'ORDER BY name ASC') {
        $sql = "
                SELECT
                  a.idtype,
                  a.name,
                  COALESCE ((SELECT
                    CASE
                      WHEN b.default = a.idtype
                      THEN 1
                      ELSE 0
                    END
                  FROM
                    hdk_tbcore_default b
                  WHERE b.table = 'type'
                    AND b.default = a.idtype)  ,0) AS `default`
                FROM
                  hdk_tbcore_type a
                WHERE idarea = '$area'
                  AND a.status = 'A'

                $order
                ";

        $ret = $this->select($sql);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectItem($type, $order = 'ORDER BY name ASC') {
        $sql = "
                SELECT
                  a.iditem,
                  a.name,
                  COALESCE ((SELECT
                    CASE
                      WHEN b.default = a.iditem
                      THEN 1
                      ELSE 0
                    END
                  FROM
                    hdk_tbcore_default b
                  WHERE b.table = 'item'
                    AND b.default = a.iditem)  ,0) AS `default`
                FROM
                  hdk_tbcore_item a
                WHERE idtype = '$type'
                  AND a.status = 'A'

                $order
                ";

        $ret = $this->select($sql);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;


    }

    public function selectService($item,$order = 'ORDER BY name ASC') {
        $sql = "
                SELECT
                  a.idservice,
                  a.name,
                  COALESCE ((SELECT
                    CASE
                      WHEN b.default = a.idservice
                      THEN 1
                      ELSE 0
                    END
                  FROM
                    hdk_tbcore_default b
                  WHERE b.table = 'service'
                    AND b.default = a.idservice)  ,0) AS `default`
                FROM
                  hdk_tbcore_service a
                WHERE iditem = '$item'
                  AND a.status = 'A'
                $order
                ";

        $ret = $this->select($sql);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;

    }

    public function selectReason($service,$order = 'ORDER BY name ASC') {
        $sql = "
                SELECT
                  a.idreason,
                  a.name,
                  COALESCE (
                    (SELECT
                      CASE
                        WHEN b.default = a.idreason
                        THEN 1
                        ELSE 0
                      END
                    FROM
                      hdk_tbcore_default b
                    WHERE b.table = 'reason'
                      AND b.default = a.idreason),
                    0
                  ) AS `default`
                FROM
                  hdk_tbcore_reason a
                WHERE idservice = '$service'
                  AND a.status = 'A'
                $order
               ";

        $ret = $this->select($sql);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectPriorities() {
        $ret =  $this->select("select idpriority, name from hdk_tbpriority");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectWay() {
        $ret= $this->select("select idattendanceway, way from hdk_tbattendance_way ORDER BY way ASC");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectAttach($id)
    {
        $ret =  $this->db->Execute("select file_name,idrequest_attachment from hdk_tbrequest_attachment where code_request = '$id'");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getQuestions()
    {
        $ret = $this->select("select idquestion, question from hdk_tbevaluationquestion where status = 'A'");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getAnswers($id)
    {
        $ret = $this->select("SELECT name, icon_name, idevaluation, checked from hdk_tbevaluation where status = 'A' and idquestion = '$id' ORDER BY idevaluation ASC");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function changeRequestStatus($status, $code, $person, $reopened=0)
    {
        $vSQL = "insert into hdk_tbrequest_log (cod_request,date,idstatus,idperson,reopened) values ('$code',now(),'$status','$person','$reopened')";
        $ret = $this->db->Execute($vSQL);
        if (!$ret) {
            $sError = $vSQL." File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertNote($code, $person, $note, $date, $totalminutes, $starthour, $finishour, $execdate, $hourtype, $serviceval = null, $public, $idtype, $ipadress, $callback, $flgopen, $idanexo = NULL,$code_email = NULL)
    {
         $vSQL = "insert into hdk_tbnote (code_request,idperson,description,entry_date,minutes,start_hour,finish_hour,execution_date,hour_type,service_value,public,idtype,ip_adress,callback,flag_opened,code_email) values ('$code', '$person', '$note', $date, '$totalminutes', '$starthour', '$finishour', '$execdate', '$hourtype', '$serviceval', '$public', '$idtype', '$ipadress', '$callback', '$flgopen', '$code_email')";

        $ret = $this->db->Execute($vSQL);
        if (!$ret) {
            $sError = $vSQL." File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertNoteLastID() {
        return $this->db->Insert_ID( );
    }

    public function updateReqStatus($id, $code)
    {
        $ret = $this->db->Execute("update hdk_tbrequest set idstatus = '$id' where code_request = '$code'");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function clearEvaluation($code)
    {
        $ret = $this->db->Execute("DELETE FROM hdk_tbrequest_evaluation where code_request = '$code'");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertEvaluation($eval, $code, $date)
    {
        $vSQL = ($this->database == 'oci8po') ? "insert into hdk_tbrequest_evaluation (idevaluation, code_request, date_) values ('$eval','$code',$date)" : "insert into hdk_tbrequest_evaluation (idevaluation, code_request, date) values ('$eval','$code',$date)";

        $ret = $this->db->Execute($vSQL);
        if (!$ret) {
            $sError = $vSQL." File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function updateDate($code_request, $column)
    {
        if ( $this->isMysql($this->database)){
            $sql = "UPDATE hdk_tbrequest_dates set $column = NOW() WHERE code_request = $code_request";
        } else {
            $sql = "UPDATE hdk_tbrequest_dates set $column = sysdate WHERE code_request = $code_request";
        }

        $ret = $this->db->Execute($sql);
        return $ret;
    }

    public function saveEmailCron($coderequest,$operation)
    {
        $query =    "
                    INSERT INTO hdk_tbrequest_emailcron
                                (
                                 code_request,
                                 date_in,
                                 operation,
                                 send)
                    VALUES (
                            '$coderequest',
                            NOW(),
                            '$operation',
                            0);
                    ";
        $ret = $this->db->Execute($query);
        if (!$ret) {
            $sError = $query ." File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
    }

    public function getEvaluationGiven($code) {
        $ret = $this->select("select
                                  name
                                from hdk_tbevaluation ev,
                                  hdk_tbrequest_evaluation reqeval
                                where ev.idevaluation = reqeval.idevaluation
                                and reqeval.code_request = '$code'");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['name'];
    }

    public function getCodeRequestById($idRequest)
    {
        $ret = $this->select( "SELECT code_request FROM hdk_tbrequest WHERE idrequest = 12949 ");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['code_request'];
    }

    public function getRequestNotes($id) {
        if($this->isMysql($this->database)) {
            $vSQL = "
                    SELECT
                      nt.idnote,
                      pers.idperson,
                      pers.name,
                      nt.description,
                      nt.entry_date,
                      nt.minutes,
                      nt.start_hour,
                      nt.finish_hour,
                      nt.execution_date,
                      nt.public,
                      nt.idtype,
                      nt.ip_adress,
                      nt.callback,
                      TIME_FORMAT(TIMEDIFF(nt.finish_hour, nt.start_hour), '%Hh %imin %ss') AS diferenca,
                      nt.hour_type,
                      nt.flag_opened
                    FROM (hdk_tbnote AS nt,
                          tbperson AS pers)
                    WHERE code_request = '$id' AND pers.idperson = nt.idperson
                    ORDER BY idnote DESC
                    ";
        } else {
            $vSQL = "select
                          nt.idnote,
                          pers.idperson,
                          pers.name,
                          nt.description,
                          to_char(nt.entry_date,'DD/MM/YYYY HH24:MI') entry_date,
                          nt.minutes,
                          nt.start_hour,
                          nt.finish_hour,
                          to_char(nt.execution_date,'DD/MM/YYYY HH24:MI') execution_date,
                          nt.public_,
                          nt.idtype,
                          nt.idnote_attachment,
                          nt.ip_adress,
                          nt.callback,
                          nta.file_name,
                          TO_CHAR(TRUNC(SYSDATE) + ( NUMTODSINTERVAL(TO_DATE(nt.finish_hour,'HH24:MI:SS') - to_date(nt.start_hour, 'HH24:MI:SS'), 'DAY')),'HH24:MI:SS') AS diferenca,
                          nt.hour_type,
                          nt.flag_opened
                        from
                          hdk_tbnote  nt,
                          tbperson  pers,
                          hdk_tbnote_attachment  nta
                        where code_request = '$id' and nt.idnote_attachment = nta.idnote_attachment(+) and pers.idperson = nt.idperson  order by idnote desc";
        }


        $ret = $this->db->Execute($vSQL);

        if (!$ret) {
            $sError = $vSQL." File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getNoteAttchByCodeRequest($code_request)
    {
        $sql =  "
                SELECT
                  *
                FROM hdk_tbnote_attachments
                WHERE idnote_attachments IN (SELECT
                  idnote_attachments
                FROM hdk_tbnote_has_attachments
                WHERE hdk_tbnote_has_attachments.idnote = (SELECT
                  hdk_tbnote.idnote
                FROM hdk_tbnote
                WHERE hdk_tbnote.code_request = $code_request
                ORDER BY hdk_tbnote.idnote DESC
                LIMIT 1))
                ";

        $ret = $this->db->Execute($sql);

        if (!$ret) {
            $sError = $sql." File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }

        return $ret;

    }

    public function getNoteAttachments($idNote)
    {
        //$sql = "SELECT idnote, idnote_attachments FROM hdk_tbnote_has_attachments WHERE idnote = $idNote";

        $sql =  "
                    SELECT
                      hdk_tbnote_attachments.idnote_attachments,
                      hdk_tbnote_attachments.filename
                    FROM hdk_tbnote_has_attachments
                      INNER JOIN hdk_tbnote_attachments
                        ON hdk_tbnote_has_attachments.idnote_attachments = hdk_tbnote_attachments.idnote_attachments
                    WHERE hdk_tbnote_has_attachments.idnote = $idNote
                ";
        $ret = $this->db->Execute($sql);
        if ($this->db->ErrorNo() != 0) {
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        } else {
            return $ret;
        }

    }

    public function getNote($idnote){
        $sql = "SELECT a.idperson, a.idnote_attachment, b.file_name FROM hdk_tbnote a LEFT JOIN hdk_tbnote_attachments b  ON a.idnote_attachment = b.idnote_attachments WHERE idnote = '$idnote'";
        $ret = $this->db->Execute($sql);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;

    }

    public function getNoteMessagesFromOperator($idperson)
    {
        $sql =  "
                SELECT
                  n.*,
                  r.idtype,
                  r.iditem,
                  r.idservice,
                  r.subject AS tck_subject,
                  r.description AS tck_description
                FROM
                  hdk_viewNotesData n,
                  hdk_tbrequest r
                WHERE n.code_request IN
                  (SELECT
                    hdk_tbrequest.code_request
                  FROM
                    hdk_tbrequest
                    INNER JOIN hdk_tbstatus
                      ON hdk_tbrequest.idstatus = hdk_tbstatus.idstatus
                    INNER JOIN hdk_tbrequest_in_charge
                      ON hdk_tbrequest.code_request = hdk_tbrequest_in_charge.code_request
                  WHERE hdk_tbstatus.idstatus_source = 3
                    AND hdk_tbrequest.idperson_owner = $idperson
                    AND hdk_tbrequest_in_charge.ind_in_charge = 1)
                  AND n.idperson <> $idperson
                  AND n.idtype = 1
                  AND r.code_request = n.code_request
                ORDER BY n.entry_date DESC
                ";

        $ret = $this->db->Execute($sql);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;

    }

    public function deleteNote($id) {
        $sql = "CALL hdk_deleteNote(".$id.")";

        //echo($sql);

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        } else {
            return $ret;
        }

    }

    public function deleteAttachNote($idattach){
        $sql = "DELETE FROM hdk_tbnote_attachment WHERE idnote_attachment = '$idattach'";
        return $this->db->Execute($sql);
    }

    public function cancelRequest($code, $status){
        return $this->db->Execute("update hdk_tbrequest set idstatus = '$status' where code_request = '$code'");
    }

    public function getTicketFile($id, $type)
    {
        if ($type == 'request'){
            $query = "SELECT file_name FROM hdk_tbrequest_attachment WHERE idrequest_attachment = '$id'";
        } elseif ($type == 'note'){
            $query = "SELECT filename as file_name FROM hdk_tbnote_attachments WHERE idnote_attachments = '$id'";
        }
        $ret = $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }

        return $ret->fields['file_name'];
    }

    public function saveNoteAttachment($idNote,$filename)
    {
        $query = "
                CALL hdk_insertNoteAttachments(".$idNote.",'".$filename."',@id);
                 ";

        $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0) {
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        } else {
            $rs = $this->db->Execute('SELECT @id as idnote_attachments');
            return $rs->fields['idnote_attachments'];
        }

    }

    public function getCode() {

        $vSql = "SELECT cod_request, cod_month FROM hdk_tbrequest_code WHERE COD_MONTH = " . date("Ym");

        $ret = $this->select($vSql);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function countGetCode() {
        $ret = $this->select("SELECT count(COD_REQUEST) as total FROM hdk_tbrequest_code WHERE COD_MONTH = " . date("Ym"));
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function increaseCode($code_request) {
        $ret = $this->db->Execute(
                                  " UPDATE hdk_tbrequest_code SET
                                       cod_request = " . ($code_request + 1) . "
                                    WHERE
                                       cod_month = " . date("Ym")
                                 );
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function createCode($code_request) {
        $vSql = "insert into hdk_tbrequest_code( cod_request,cod_month) values (" . ($code_request + 1) . ", " . date("Ym") . ")";

        $ret = $this->db->Execute($vSql);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function checksVipUser($idPerson) {
        $ret = $this->select("select count(idperson) as rec_count from tbperson where idperson = $idPerson and user_vip = 'Y'");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function checksVipPriority() {
        $ret = $this->select("select count(idpriority) as rec_count from hdk_tbpriority where vip = 1 group by idpriority");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
        ;
    }

    public function getVipPriority() {
        $ret = $this->select("select idpriority from hdk_tbpriority where vip = 1 group by idpriority");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
        ;
    }

    public function getServPriority($idService) {
        $ret = $this->select("SELECT idpriority FROM hdk_tbcore_service WHERE idservice = $idService");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getDefaultPriority() {
         $ret = $this->select("SELECT idpriority FROM hdk_tbpriority WHERE `default` = 1 AND `status` = 'A'");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRequest($idperson_creator, $source, $date, $type, $item, $service, $reason, $way, $subject, $description, $osnumber, $idpriority, $tag, $serial_number, $idjuridical, $expiration_date, $idperson_owner, $idstatus, $code_request)
    {

            $sql = "
                    insert into hdk_tbrequest  (code_request,
                                                `subject`,
                                                description,
                                                idtype,
                                                iditem,
                                                idservice,
                                                idreason,
                                                idpriority,
                                                idsource,
                                                idperson_creator,
                                                entry_date,
                                                os_number,
                                                label,
                                                serial_number,
                                                idperson_juridical,
                                                expire_date,
                                                idattendance_way,
                                                idperson_owner,
                                                idstatus
                                              )
                                              values
                                              (
                                              $code_request,
                                              '" . $subject . "',
                                              '" . $description . "',
                                              $type,
                                              $item,
                                              $service,
                                              $reason,
                                              $idpriority,
                                              $source,
                                              $idperson_creator,
                                              '$date' ,
                                              '" . $osnumber . "',
                                              '" . $tag . "',
                                              '" . $serial_number . "',
                                              $idjuridical,
                                              '" . $expiration_date . "',
                                              $way,
                                              $idperson_owner,
                                              $idstatus
                                              )
                   ";

        $ret = $this->db->Execute($sql);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() ."SQL: ".$sql;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function getServiceGroup($idservice) {
        $ret = $this->db->Execute("select grp.idperson
                                    from hdk_tbgroup grp,
                                    hdk_tbcore_service serv,
                                    hdk_tbgroup_has_service grp_serv
                                    where grp.idgroup = grp_serv.idgroup
                                    and serv.idservice = grp_serv.idservice
                                    and serv.idservice = '$idservice'");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['idperson'];
    }

    public function insertRequestCharge($code_request, $idInCharge, $type, $ind_in_charge) {
        $ret = $this->db->Execute("insert into hdk_tbrequest_in_charge (code_request, id_in_charge, type, ind_in_charge) values ('$code_request',$idInCharge, '$type', '$ind_in_charge')");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRequestTimesNew($code_request, $minOpeningTime = 0, $minAttendanceTime = 0, $minExpendedTime = 0, $minTelephoneTime = 0, $minClosureTime = 0) {
        $ret = $this->db->Execute("insert into
                                   hdk_tbrequest_times
									            (
									             CODE_REQUEST,
									             MIN_OPENING_TIME,
									             MIN_ATTENDANCE_TIME,
									             MIN_EXPENDED_TIME,
										     	 MIN_TELEPHONE_TIME,
										    	 MIN_CLOSURE_TIME
										    	 )
									values
									            (
									            $code_request,
                                                $minOpeningTime,
                                                $minAttendanceTime,
                                                $minExpendedTime,
                                                $minTelephoneTime,
                                                $minClosureTime
                                                )
								  ");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRequestDate($code_request) {
        $ret = $this->db->Execute("insert into hdk_tbrequest_dates (code_request) values ($code_request)");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRequestLog($code_request, $date, $idstatus, $idperson) {


            $vSQL = "insert into hdk_tbrequest_log (cod_request,date,idstatus,idperson) values ($code_request, '$date', $idstatus, $idperson)";


        $ret = $this->db->Execute($vSQL);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getInCharge($code_request)
    {
        $sql =  "
                SELECT
                  a. idrequest_in_charge ,
                  a. code_request ,
                  a. id_in_charge ,
                  a. `type` ,
                  a. ind_in_charge ,
                  a. ind_repass ,
                  a. ind_track ,
                  a. ind_operator_aux ,
                  b. `name`
                FROM
                   hdk_tbrequest_in_charge  a,
                  tbperson b
                WHERE a. code_request  = $code_request
                  AND ind_in_charge = 1
                  AND a. id_in_charge  = b. idperson
                ";
        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function saveTicketAtt($code_request,$file_name){
        $sql =  "
                INSERT INTO hdk_tbrequest_attachment (
                  code_request,
                  file_name
                )
                values
                  (
                    '$code_request',
                    '$file_name'
                  ) ;

                ";

        $ret = $this->db->Execute($sql);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $this->db->Insert_ID( );
    }

    public function deleteTicketAtt($idAttachment){
        $sql =  " delete from hdk_tbrequest_attachment where idrequest_attachment = '".$idAttachment."'" ;
        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return true;
    }
    public function selectUser($where = NULL, $order = NULL, $limit = NULL) {
        
        if ($this->database == 'mysqli') {
            $query = "select
                                person.idperson,
                                person.name        as pname,
                                juridical.idperson as idcompany,
                                juridical.name     as cname
                              from tbperson person,
                                tbperson juridical,
                                hdk_tbdepartment_has_person rela,
                                hdk_tbdepartment dep
                              where person.idperson = rela.idperson
                                  AND juridical.idperson = dep.idperson
                                  AND dep.iddepartment = rela.iddepartment
                                  AND person.status = 'A' $where $order $limit" ;
        } elseif ($this->database ==  'oci8po') {
            $core  = "select
                                person.idperson,
                                person.name        as pname,
                                juridical.idperson as idcompany,
                                juridical.name     as cname
                              from tbperson person,
                                tbperson juridical,
                                hdk_tbdepartment_has_person rela,
                                hdk_tbdepartment dep
                              where person.idperson = rela.idperson
                                  AND juridical.idperson = dep.idperson
                                  AND dep.iddepartment = rela.iddepartment
                                  AND person.status = 'A' $where $order";
            if($limit){
                $limit = str_replace('LIMIT', "", $limit);
                $p     = explode(",", $limit);
                $start = $p[0] + 1; 
                $end   = $p[0] +  $p[1]; 
                $query =    "
                            SELECT   *
                              FROM   (SELECT                                          
                                            a  .*, ROWNUM rnum
                                        FROM   (  
                                                  
                                                $core 

                                                ) a
                                       WHERE   ROWNUM <= $end)
                             WHERE   rnum >= $start         
                            ";
            }else{
                $query = $core;
            }
        }
        $ret =  $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;        
    }

    public function selectSource() {
        $query = "select idsource, name, icon from hdk_tbsource ORDER BY name ASC";
        $ret =  $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRepassGroups($where = null) {

        $query = ($this->database == 'oci8po') ? "SELECT tbg.idgroup, tbp.idperson, tbp.name, tbg.level_, tbg.status
                FROM hdk_tbgroup tbg,
                tbperson  tbp
                WHERE tbg.status = 'A' AND tbg.idperson = tbp.idperson $where ORDER BY 3" : "SELECT tbg.idgroup, tbp.idperson, tbp.name, tbg.level, tbg.status
                FROM hdk_tbgroup tbg,
                tbperson  tbp
                WHERE tbg.status = 'A' AND tbg.idperson = tbp.idperson $where ORDER BY 3";

        $ret =  $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRepassOperators($where = null) {
        $query = "SELECT idperson, name FROM tbperson WHERE status = 'A' and idtypeperson IN ('1','3') $where ORDER BY name";
        $ret =  $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRepassPartners() {
        $query = "SELECT idperson, name FROM tbperson WHERE status = 'A' AND idtypeperson = '5'";
        $ret =  $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getAbilityGroup($idgrp) {
        $query = "SELECT grpp.name, serv.name as service, grp.idgroup, serv.idservice
                    FROM hdk_tbcore_service  serv,
                         hdk_tbgroup  grp,
                         tbperson  grpp,
                         hdk_tbgroup_has_service  relat
                   WHERE
                         grp.idgroup = relat.idgroup
                     AND grpp.idperson = grp.idperson
                     AND serv.idservice = relat.idservice
                     AND grpp.idperson = '$idgrp'";
        $ret =  $this->db->Execute($query); 
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getAbilityOperator($idop) {
        $query = "SELECT per.name, serv.name as service, per.idperson, serv.idservice, grpper.name
                    FROM hdk_tbcore_service serv,
                         hdk_tbgroup grp,
                         hdk_tbgroup_has_service relat,
                         tbperson per,
                         tbperson grpper,
                         hdk_tbgroup_has_person relatp
                   WHERE
                         grp.idgroup = relat.idgroup
                     AND grpper.idperson = grp.idperson
                     AND serv.idservice = relat.idservice
                     AND relatp.idperson = per.idperson
                     AND relatp.idgroup = grp.idgroup
                     AND per.idperson = '$idop'";
        $ret =  $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getGroupOperators($idgrp) {
        $query = "SELECT per.name, per.idperson, grp.idperson as groupname, grp.idgroup
                    FROM hdk_tbgroup grp,
                         tbperson per,
                         tbperson grppr, 
                         hdk_tbgroup_has_person rel
                   WHERE per.idperson = rel.idperson
                     AND grp.idgroup = rel.idgroup
                     AND grppr.idperson = grp.idperson
                     AND grppr.idperson = '$idgrp'";
        $ret =  $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getOperatorGroups($idgrp) {
        $query = "SELECT grppr.name as pername, per.idperson, grp.idperson as idpergroup, grp.idgroup
                    FROM hdk_tbgroup grp,
                         tbperson per,
                         tbperson grppr,
                         hdk_tbgroup_has_person rel
                   WHERE per.idperson = rel.idperson
                     AND grppr.idperson = grp.idperson
                     AND grp.idgroup = rel.idgroup
                     AND per.idperson = '$idgrp'";
        $ret =  $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getAbilityPartners($idop) {
        $query = "SELECT per.name, serv.service, per.idperson, serv.idservice, grp.name
                    FROM hdk_tbcore_service as serv,
                         hdk_tbgroup as grp,
                         hdk_tbgroup_has_service as relat,
                         tbperson as per,
                         hdk_tbgroup_has_person as relatp
                   WHERE
                         grp.idgroup = relat.idgroup
                     AND serv.idservice = relat.idservice
                     AND relatp.idperson = per.idperson
                     AND relatp.idgroup = grp.idgroup
                     AND per.idperson = '$idop'";
        $ret =  $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertInCharge($code, $person, $type, $ind, $rep, $track = 0) {
        $query = "INSERT INTO hdk_tbrequest_in_charge (code_request,id_in_charge,type,ind_in_charge,ind_repass,ind_track) 
                       VALUES ('$code','$person','$type','$ind','$rep','$track')";
        $ret =  $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function removeIncharge($code) {
        $query = "UPDATE hdk_tbrequest_in_charge SET ind_in_charge = '0' WHERE code_request = '$code'";
        $ret =  $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function checkApprovalBt($code){
        if ($this->database == 'oci8po') {
            $query = "SELECT a.idperson, a.order_ \"order\" FROM hdk_tbrequest_approval a, hdk_tbrequest b WHERE a.request_code = b.code_request AND idnote IS NULL AND fl_rejected = 0 AND b.idstatus != 6 AND a.request_code = $code ";
        }
        else
        {
            $query = "SELECT a.idperson, a.`order` FROM hdk_tbrequest_approval a, hdk_tbrequest b WHERE a.request_code = b.code_request AND idnote IS NULL AND fl_rejected = 0 AND b.idstatus != 6 AND a.request_code = $code ";
        }

        $ret =  $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getIdPersonGroup($groups) {
        $sql = "select idperson from hdk_tbgroup where idgroup in ($groups)" ;
        //die($sql) ;
        $ret = $this->select($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>" . $sql;
            $this->error($sError);
            return false;
        } else {
            return $ret;
        }
    }

    public function getTypeNote($where = NULL, $order = NULL, $limit = NULL) {
        $sql = "select idtypenote, description from hdk_tbnote_type $where $order $limit" ;
        //die($sql) ;
        $ret = $this->select($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>" . $sql;
            $this->error($sError);
            return false;
        } else {
            return $ret;
        }
    }

    public function updateRequest($code, $type, $item , $service, $reason, $way, $priority) {
        $sql = "UPDATE hdk_tbrequest 
                   SET idtype = $type, iditem = $item , 
                       idservice = $service, idreason = $reason, 
                       idattendance_way = $way, idpriority = $priority
                 WHERE code_request = '$code'" ;
        //die($sql) ;
        $ret = $this->select($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>" . $sql;
            $this->error($sError);
            return false;
        } else {
            return $ret;
        }
    }

    /**
     * Returns object containing people's id and name who are not, or are as ind_operator_aux,
     * depending on the parameter of the condition
     *
     * @access public
     * @param int $code_request Request Id.
     * @param string $in_notin Condition Parameter.
     * @return array Person's Information
     */
    public function getOperatorAuxCombo($code_request, $in_notin)
    {
        $sql = ($this->database == 'oci8po') ? "SELECT
				   a.idperson,
				   ltrim(a.name) AS name
				FROM tbperson a
			   WHERE a.idperson " : "SELECT
				     a.idperson,
				     ltrim(a.name) AS `name`
				FROM tbperson a
			   WHERE a.idperson ";

        if	($in_notin == 'in' ) {
            $sql .= "IN" ;
        } elseif ($in_notin == 'not') {
            $sql .= "NOT IN";
        }
        $sql .=	" (SELECT a.id_in_charge
					 FROM hdk_tbrequest_in_charge a
					WHERE a.code_request = '$code_request'
                      AND a.ind_operator_aux = 1
					  AND a.type = 'P'
					  	)
					AND a.idtypeperson IN (1,3)
					AND a.status = 'A'
			   ORDER BY a.name asc
				";

        //die($sql) ;
        $ret = $this->select($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>" . $sql;
            $this->error($sError);
            return false;
        } else {
            return $ret;
        }
    }

    public function insertOperatorAux($code_request,$idperson) {
        $sql =	"INSERT INTO hdk_tbrequest_in_charge 
					(code_request, id_in_charge, type, ind_in_charge, ind_repass, ind_track, ind_operator_aux)
					VALUES
					('$code_request', '$idperson', 'P', 0, 'N', 0, 1)";

        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>" . $sql;
            $this->error($sError);
            return false;
        } else {
            return $ret;
        }
    }

    public function deleteOperatorAux($code_request,$idperson) {
        $sql = "DELETE FROM hdk_tbrequest_in_charge
				 WHERE id_in_charge = $idperson
				   AND code_request = $code_request
				   AND ind_operator_aux = 1		
				";

        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>" . $sql;
            $this->error($sError);
            return false;
        } else {
            return $ret;
        }
    }

    public function getRepassNote($code) {
        $sql = "SELECT idnote, description, code_request FROM hdk_tbnote WHERE code_request = '$code' ORDER BY idnote DESC";
        $ret = $this->db->Execute($sql);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>" . $sql;
            $this->error($sError);
            return false;
        } else {
            return $ret->fields['idnote'];
        }
    }

    public function insertRepassRequest($date, $code, $note) {
        if($this->database == 'oci8po') {
            $sql = "INSERT INTO hdk_tbrequest_repassed (date_,idnote,code_request) VALUES ($date,'$note','$code')";
        } elseif ($this->database == 'mysqli') {
            $sql = "INSERT INTO hdk_tbrequest_repassed (date,idnote,code_request) VALUES ($date,'$note','$code')";
        }

        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "query; " . $sql;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getEntryDate($code_request){
        $sql = 	"SELECT entry_date FROM hdk_tbrequest WHERE code_request = '$code_request'";
        $ret = $this->db->Execute($sql);

        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "query; " . $sql;
            $this->error($sError);
            return false;
        }
        return $ret->fields['entry_date'];
    }

    public function getAssumedDate($code_request){
        $sql = ($this->database == 'oci8po') ? "SELECT to_char(date_,'DD/MM/YYYY HH24:MI:SS') FROM hdk_tbrequest_log WHERE cod_request = $code_request AND idstatus = 3 and rownum = 1 ORDER BY id ASC " : "SELECT date FROM hdk_tbrequest_log WHERE cod_request = $code_request AND idstatus = 3 ORDER BY id ASC LIMIT 1";
        $ret = $this->db->Execute($sql);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "query; " . $sql;
            $this->error($sError);
            return false;
        }
        return $ret->fields['date'];
    }

    public function getExpendedTime($code_request){
        $sql = "SELECT SUM(minutes) as minutes FROM hdk_tbnote WHERE code_request = $code_request";
        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "query; " . $sql;
            $this->error($sError);
            return false;
        }
        return $ret->fields['minutes'];

    }

    public function updateRequestTimes($code_request, array $data){
        $table = "hdk_tbrequest_times";
        $where = "CODE_REQUEST = '$code_request'";
        $ret = $this->update($table, $data, $where);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getExtNumber($code) {
        $sql = "SELECT extensions_number FROM hdk_tbrequest WHERE code_request = '$code'";

        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "query; " . $sql;
            $this->error($sError);
            return false;
        }
        return $ret->fields['extensions_number'];
    }

    public function saveExtension($code, $value, $date) {

        if($this->database == 'oci8po') {
            $sql = "UPDATE hdk_tbrequest SET extensions_number = '$value', expire_date = to_date('$date','DD/MM/YYYY HH24:MI') WHERE code_request = '$code'";
        }
        elseif($this->database == 'mysqli'){
            $sql = "UPDATE hdk_tbrequest SET extensions_number = '$value', expire_date = '$date' WHERE code_request = '$code'";
        }

        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "query; " . $sql;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertChangeExpireDate($code, $reason, $idperson) {
        if ($this->database == 'oci8po'){
            $sql = "
              INSERT INTO 
                hdk_tbrequest_change_expire 
                (code_request,reason,idperson,changedate) 
                values 
                ('$code','$reason', $idperson,  sysdate)";
        }elseif ($this->database == 'mysqli'){
            $sql = "
              INSERT INTO 
                hdk_tbrequest_change_expire 
                (code_request,reason,idperson,changedate) 
                values 
                ('$code','$reason', $idperson,  NOW())";
        }

        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "query; " . $sql;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertWay($way){
        $sql = ($this->database == 'oci8po') ? "INSERT INTO hdk_tbattendance_way (way) VALUES ('".$way."')" : "INSERT INTO hdk_tbattendance_way (way) VALUES ('".$way."')";
        //die($sql);
        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "query; " . $sql;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRequests($cod_user, $entry_date, $expire_date, $wheredata = NULL, $where = NULL, $wheretip = NULL, $start = NULL, $rp = NULL, $sortname = NULL, $sortorder = NULL)
    {

        if ($this->database == 'oci8po'){
            $v_sql = "
					select distinct 
					   a.code_request,
					   $expire_date,
					   $entry_date ,
					   a.entry_date as entry_date_order,
					   a.expire_date as expire_date_order,
					   a.subject,
					   a.idperson_owner,
					   a.flag_opened,
					   own.name             as personname,
					   a.idperson_juridical as idcompany,
					   comp.name            as company,
					   c.type               as type_in_charge,
					   e.idstatus_source,
					   e.name               as statusview,
					   e.color               as s_color,
					   f.name               as type,
					   g.name               as item,
					   h.name               as service,
					   i.name               as priority,
					   i.color               as p_color,
					   (SELECT
						 name
					  FROM tbperson x,
						 hdk_tbrequest_in_charge y
					  WHERE y.ind_in_charge = 1
						 AND y.id_in_charge = x.idperson
						 AND y.code_request = a.code_request
					  and rownum = 1 ) AS in_charge,
					  (SELECT
						   id_in_charge
						 FROM tbperson x,
						   hdk_tbrequest_in_charge y
						 WHERE y.ind_in_charge = 1
							 AND y.code_request = a.code_request
						 and rownum = 1 ) AS id_in_charge,
					  (SELECT 
						count(idrequest_attachment) 
					  FROM hdk_tbrequest_attachment 
					  WHERE code_request = a.code_request) as totatt,
					  c.ind_track
					from hdk_tbrequest a,
					  hdk_tbstatus b,
					  hdk_tbrequest_in_charge c,
					  tbperson inc,
					  tbperson own,
					  tbperson comp,
					  hdk_tbstatus e,
					  hdk_tbcore_type f,
					  hdk_tbcore_item g,
					  hdk_tbcore_service h,
					  hdk_tbpriority i
					where $wheretip
					  $wheredata
					  AND c.id_in_charge = inc.idperson
					  and c.idRequest_in_charge = (select max (idRequest_in_charge) from hdk_tbrequest_in_charge cx where cx.code_request = c.code_request )
					  and a.idstatus = b.idstatus
					  and a.code_request = c.code_request
					  and a.idperson_owner = own.idperson
					  and a.idperson_juridical = comp.idperson
					  and e.idstatus = a.idstatus
					  and a.idtype = f.idtype
					  and a.iditem = g.iditem
					  and a.idservice = h.idservice
					  and a.idpriority = i.idpriority
					  $where                
					order by $sortname $sortorder     
				  ";

            if (strlen($rp) > 2 ){
                $v_limit = explode(",",str_ireplace(array("limit", " "),"",trim($rp)));
                $v_sql =  $this->setLimitOracle($v_sql, $v_limit[1], $v_limit[0]);
            }

        }else{
            $v_sql = "
					select SQL_CALC_FOUND_ROWS
					   a.code_request,
					   $expire_date,
					   $entry_date ,
					   a.entry_date as entry_date_order,
					   a.expire_date as expire_date_order,
					   a.subject,
					   a.idperson_owner,
					   a.flag_opened,
					   own.name             as personname,
					   a.idperson_juridical as idcompany,
					   comp.name            as company,
					   -- c.id_in_charge       as id_in_charge,
					   inch.type               as type_in_charge,
					   e.idstatus_source,
					   e.name               as `statusview`,
					   e.color               as s_color,
					   f.name               as `type`,
					   g.name               as item,
					   h.name               as service,
					   i.name               as priority,
					   i.color               as p_color,
					   (SELECT
						 `name`
					  FROM tbperson x,
						 hdk_tbrequest_in_charge y
					  WHERE y.ind_in_charge = 1
						 AND y.id_in_charge = x.idperson
						 AND y.code_request = a.code_request
					  LIMIT 1 ) AS in_charge,
					  (SELECT
						   `id_in_charge`
						 FROM tbperson x,
						   hdk_tbrequest_in_charge y
						 WHERE y.ind_in_charge = 1
							 AND y.code_request = a.code_request
						 LIMIT 1 ) AS id_in_charge,
					  (SELECT 
						count(idrequest_attachment) 
					  FROM hdk_tbrequest_attachment 
					  WHERE code_request = a.code_request) as totatt,
					  inch.ind_track
					from (hdk_tbrequest a,
					  hdk_tbstatus b,
					  hdk_tbrequest_in_charge inch,
					  tbperson inc,
					  tbperson own,
					  tbperson comp,
					  hdk_tbstatus e,
					  hdk_tbcore_type f,
					  hdk_tbcore_item g,
					  hdk_tbcore_service h,
					  hdk_tbpriority i)
					where $wheretip
					  $wheredata
					  AND inch.id_in_charge = inc.idperson
					  and a.idstatus = b.idstatus
					  and a.code_request = inch.code_request
					  and a.idperson_owner = own.idperson
					  and a.idperson_juridical = comp.idperson
					  and e.idstatus = a.idstatus
					  and a.idtype = f.idtype
					  and a.iditem = g.iditem
					  and a.idservice = h.idservice
					  and a.idpriority = i.idpriority
					  $where
					group by a.code_request 
					order by $sortname $sortorder 
					LIMIT $start, $rp    
				  ";
        }
        //die($v_sql);
        //print $v_sql ;
        //$ret = $this->db->Execute('SET CHARACTER SET utf8');
        $ret = $this->db->Execute($v_sql);
        //$ret = $this->select($v_sql);
        if (!$ret) {
            $sError = " Arq: " . __FILE__ . " Line: " . __LINE__ . "DB ERROR: " . $this->db->ErrorMsg() . " SQL: " . $v_sql;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getNumberRequestsAtt($cod_user, $wheredata = NULL, $where = NULL, $wheretip = NULL){
        $query = "SELECT COUNT(distinct a.code_request) AS total FROM (hdk_tbrequest a,
					  hdk_tbstatus b,
					  hdk_tbrequest_in_charge inch,
					  tbperson inc,
					  tbperson own,
					  tbperson comp,
					  hdk_tbstatus e,
					  hdk_tbcore_type f,
					  hdk_tbcore_item g,
					  hdk_tbcore_service h,
					  hdk_tbpriority i)
					WHERE $wheretip
					  $wheredata
					  AND inch.id_in_charge = inc.idperson
					  AND a.idstatus = b.idstatus
					  AND a.code_request = inch.code_request
					  AND a.idperson_owner = own.idperson
					  AND a.idperson_juridical = comp.idperson
					  AND e.idstatus = a.idstatus
					  AND a.idtype = f.idtype
					  AND a.iditem = g.iditem
					  AND a.idservice = h.idservice
					  AND a.idpriority = i.idpriority
					  $where";
        //echo($query);
        $ret = $this->select($query);
        if(!$ret) {
            $sError = $query . "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg() . "Query: " . $query  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['total'];
    }

    public function getWaitingApprovalRequestsCount($where = NULL, $id){
        $query = "SELECT COUNT(distinct req.code_request) as count FROM (hdk_tbrequest req,
                              hdk_tbstatus stat,
                              tbperson pers,
                              tbperson resp,
                              hdk_tbrequest_in_charge inch)
                             left join hdk_tbgroup grp
                                on (inch.id_in_charge = grp.idperson
                                    and resp.idperson = grp.idperson)
                            WHERE req.idstatus = stat.idstatus
                                AND req.idperson_owner = pers.idperson
                            AND inch.id_in_charge = resp.idperson
                                and inch.code_request = req.code_request
                                and req.code_request = inch.code_request
                               and inch.ind_in_charge = 1     
                               and req.idperson_owner = $id
                               AND stat.idstatus_source = 4
                               $where ";
        //echo($query);
        $ret = $this->select($query);
        if(!$ret) {
            $sError = $query . "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg() . "Query: " . $query  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['count'];
    }

    public function getWaitingApprovalRequestsCountByDate($id){
        return $this->db->Execute("SELECT
									  (SELECT
									     date
									   FROM hdk_tbrequest_log logreq
									   WHERE idstatus = 4
									       AND logreq.cod_request = req.code_request
									   ORDER BY date DESC
									   LIMIT 1) as dt_approval
									FROM (hdk_tbrequest req,
									   hdk_tbstatus stat,
									   tbperson pers,
									   tbperson resp,
									   hdk_tbrequest_in_charge inch)
									  left join hdk_tbgroup grp
									    on (inch.id_in_charge = grp.idperson
									        and resp.idperson = grp.idperson)
									WHERE req.idstatus = stat.idstatus
									    AND req.idperson_owner = pers.idperson
									    AND inch.id_in_charge = resp.idperson
									    and inch.code_request = req.code_request
									    and req.code_request = inch.code_request
									    and inch.ind_in_charge = 1
									    and req.idperson_owner = $id
									    AND stat.idstatus_source = 4");
        //echo($query);
        $ret = $this->select($query);
        if(!$ret) {
            $sError = $query . "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg() . "Query: " . $query  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function updateFlagNote($code, $flag, $idperson) {
        $ret = $this->db->Execute("UPDATE hdk_tbnote set
                                   flag_opened = $flag
                                   WHERE code_request = '$code' 
                                   AND idperson != $idperson");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

}