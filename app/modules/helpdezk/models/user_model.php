<?php
class user_model extends Model{
    public $database;

    public function __construct(){
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }
								
    public function getRequest($entry_date, $expire_date,$filtrotip = NULL,$where = NULL, $WHERESTATUS = NULL, $sortname = NULL, $sortorder = NULL, $start = NULL, $rp = NULL, $status, $iduser){

        if ($this->database == 'oci8po'){
            $v_sql = "
                select  distinct		   a.code_request       ,
				   a.subject            ,
				   resp.name         	as in_charge,
				   inch.id_in_charge    ,
				   $expire_date			,
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
					$filtrotip
					$WHERESTATUS
					$where
				ORDER BY
					$sortname
					$sortorder
              ";
            if (strlen($limit) > 2 ){
                $v_limit = explode(",",str_ireplace(array("limit", " "),"",trim($limit)));
                $v_sql =  $this->setLimitOracle($v_sql, $v_limit[1], $v_limit[0]);
            }
        }else{
            $v_sql = "
                select SQL_CALC_FOUND_ROWS
				   a.code_request       ,
				   a.subject            ,
				   resp.name         	as in_charge,
				   inch.id_in_charge    ,
				   $expire_date			,
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
					$filtrotip
					$WHERESTATUS
					$where
				ORDER BY
					$sortname
					$sortorder
				LIMIT $start, $rp
              ";
        }

       // die($v_sql);

        $ret = $this->select($v_sql);


		//die($sql);
		/*
         $ret = $this->select("select
                              req.code_request      `code_request`,
                              req.subject           `subject`,
                              resp.name              as in_charge,
                              inch.id_in_charge     id_in_charge,
                              req.expire_date       `expire_date`,
                              req.entry_date        `entry_date`,
                              stat.user_view        `statusview`,
                              req.idtype            `idtype`,
                              req.idperson_owner    `owner`,
                              stat.idstatus         `status`,
                              stat.color            `color_status`,
                              req.flag_opened       `flag_opened`,
                              pers.name             personname,
                              (SELECT count(idrequest_attachment) FROM hdk_tbrequest_attachment WHERE code_request = req.code_request) as totatt
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
                               and inch.ind_in_charge=1                 
                               and req.idperson_owner = $iduser
                                $filtrotip $WHERESTATUS ORDER BY $sortname $sortorder LIMIT $start, $rp");
        */

         if(!$ret) {
            $sError = $v_sql. " Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
        }
        return $ret;
    }
    
    public function countRequest($filtrotip = NULL,$WHERESTATUS = NULL, $iduser){
         $ret = $this->select("SELECT COUNT(distinct req.code_request) as total FROM (hdk_tbrequest req,
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
                               and inch.ind_in_charge=1     
                               and req.idperson_owner = $iduser
                                $filtrotip $WHERESTATUS");
         
         if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
        }
        return $ret->fields['total'];
    }
    
    public function getAttachRequest($code_request){
        $ret = $this->select("SELECT idrequest_attachment, code_request, file_name FROM hdk_tbrequest_attachment WHERE code_request =  $code_request");
         if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }
    
    public function getPriority(){
        $vSQL = ($this->database == 'oci8po') ? "SELECT idpriority, name, order_ AS \"order\", color, default_ as \"default\", vip, limit_hours, limit_days, status FROM hdk_tbpriority" : "SELECT idpriority, `name`, `order`, color, `default`, vip, limit_hours, limit_days, `status` FROM hdk_tbpriority";
        //die($vSQL);
        $ret = $this->select($vSQL);
        if(!$ret) {
            $sError = $vSQL."Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }
    public function getStatus(){
        $ret= $this->select("SELECT idstatus, name, user_view, color, status FROM hdk_tbstatus");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }
    
    public function getInCharge($code_request){
        $ret= $this->select("SELECT idrequest_in_charge, id_in_charge
                                FROM hdk_tbrequest_in_charge
                                WHERE ind_in_charge = 1
                                AND code_request=$code_request");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }
    
    public function getGroup($idgroup){
        $ret= $this->select("SELECT name FROM hdk_tbgroup WHERE idgroup = $idgroup");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }
    
    public function getUser($id_analyzer){
        $ret= $this->select("SELECT name FROM tbperson WHERE idperson = $id_analyzer");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }
    
    
    
    public function getNewRequestsCount($id){
        $ret = $this->select("SELECT COUNT(distinct req.code_request) as count FROM (hdk_tbrequest req,
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
                               and inch.ind_in_charge=1     
                               and req.idperson_owner = $id
                               AND stat.idstatus_source = 1
");
        return $ret->fields['count'];
    }
    public function getInProgressRequestsCount($id){
        $ret = $this->select("SELECT COUNT(distinct req.code_request) as count FROM (hdk_tbrequest req,
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
                               and inch.ind_in_charge=1     
                               and req.idperson_owner = $id
                               AND stat.idstatus_source = 3
");
        return $ret->fields['count'];
    }
    public function getRejectedRequestsCount($id){
        $ret = $this->select("SELECT COUNT(distinct req.code_request) as count FROM (hdk_tbrequest req,
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
                               and inch.ind_in_charge=1     
                               and req.idperson_owner = $id
                               AND stat.idstatus_source = 6
");
        return $ret->fields['count'];
    }
    public function getFinishedRequestsCount($id){
        $ret = $this->select("SELECT COUNT(distinct req.code_request) as count FROM (hdk_tbrequest req,
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
                               and inch.ind_in_charge=1     
                               and req.idperson_owner = $id
                               AND stat.idstatus_source = 5
");
        return $ret->fields['count'];
    }
    public function getWaitingApprovalRequestsCount($id){
        $ret = $this->select("SELECT COUNT(distinct req.code_request) as count FROM (hdk_tbrequest req,
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
                               and inch.ind_in_charge=1     
                               and req.idperson_owner = $id
                               AND stat.idstatus_source = 4");
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
    }    
    
    public function cancelRequest($code, $status){
        return $this->db->Execute("update hdk_tbrequest set idstatus = '$status' where code_request = '$code'");
    }
    
    public function updateLog($code, $status, $person){
        $vSQL = ($this->database == 'oci8po') ? "insert into hdk_tbrequest_log (cod_request,date_,idstatus,idperson) values ('$code',sysdate,'$status','$person')" : "insert into hdk_tbrequest_log (cod_request,date,idstatus,idperson) values ('$code',now(),'$status','$person');";
        //die($vSQL);
        return $this->db->Execute($vSQL);
    }
    
    public function geRequestsCount($idperson,$idstatus){
        $ret = $this->select("SELECT COUNT(distinct a.code_request) as count
								FROM hdk_tbrequest a, hdk_tbstatus b, hdk_tbrequest_in_charge c
								WHERE a.idstatus = b.idstatus
								AND b.idstatus_source = $idstatus
								AND a.idperson_owner = $idperson
								and c.ind_in_charge = 1
								AND c.code_request = a.code_request");
        return $ret->fields['count'];
    }
    
}
