<?php

class operatorview_model extends Model {

    public $database;

    public function __construct(){
      	parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function getRequests($cod_user, $entry_date, $expire_date, $wherestatus = NULL,  $wheredata = NULL, $where = NULL, $wheretip = NULL, $limit = NULL, $sortname, $sortorder) 
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
					   e.name               as status,
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
					  $wherestatus
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
			
			if (strlen($limit) > 2 ){
			  $v_limit = explode(",",str_ireplace(array("limit", " "),"",trim($limit)));  
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
					   c.type               as type_in_charge,
					   e.idstatus_source,
					   e.name               as `status`,
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
					  c.ind_track
					from (hdk_tbrequest a,
					  hdk_tbstatus b,
					  hdk_tbrequest_in_charge c,
					  tbperson inc,
					  tbperson own,
					  tbperson comp,
					  hdk_tbstatus e,
					  hdk_tbcore_type f,
					  hdk_tbcore_item g,
					  hdk_tbcore_service h,
					  hdk_tbpriority i)
					where $wheretip
					  $wherestatus
					  $wheredata
					  AND c.id_in_charge = inc.idperson
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
					group by a.code_request 
					order by $sortname $sortorder $limit    
				  ";  
		}   
        //die($v_sql);
        //print $v_sql ;
        //$ret = $this->db->Execute('SET CHARACTER SET utf8');
        $ret = $this->db->Execute($v_sql);
		//$ret = $this->select($v_sql);
		if (!$ret) {
			$sError = $v_sql . " Arq: " . __FILE__ . " Line: " . __LINE__ . "DB ERROR: " . $this->db->ErrorMsg() . " SQL: " . $v_sql;
			$this->error($sError);
			return false;
		}
		return $ret;
    }

	public function getOracleNumberRequests($cod_user, $entry_date, $expire_date, $wherestatus = NULL,  $wheredata = NULL, $where = NULL, $wheretip = NULL)
	{
		$qry = "
				select distinct 
				   count(a.code_request) total
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
				  $wherestatus
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
			  ";

  	    $rs = $this->select($qry);
		if (!$rs) {
			$sError = $qry . " Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
			$this->error($sError);
			return false;
		}
		while (!$rs->EOF) {
			$ret = $rs->fields['total'];
			$rs->MoveNext();
		}
		return $ret;

	}
    public function getGroupName($id) {
        $ret = $this->db->Execute("select name from hdk_tbgroup where idgroup = '$id'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['name'];
    }

    public function getPersonName($id) {
        $ret = $this->db->Execute("select name from tbperson where idperson = '$id'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['name'];
    }

    public function selectAttachment($code_request) {
       $ret= $this->db->Execute("select idrequest_attachment from hdk_tbrequest_attachment where code_request='$code_request'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertExecutionOrder($code, $value, $person) {
        $ret = $this->db->Execute("insert into hdk_tbexecutionorder_person (code_request,idperson,exorder) values ('$code','$person','$value')");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function deleteExecutionOrder($code, $value, $person) {
        $ret = $this->db->Execute("delete from hdk_tbexecutionorder_person where code_request = '$code' and idperson = '$person'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function updateExecutionOrder($code, $value, $person) {
        $ret = $this->db->Execute("update hdk_tbexecutionorder_person set exorder='$value' where code_request = '$code' and idperson = '$person'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function checkOrder($code, $value, $person) {
        $ret = $this->db->Execute("select idexecutionorder from hdk_tbexecutionorder_person where code_request = '$code' and idperson = '$person'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getOrder($code, $person) {
        $ret = $this->db->Execute("select exorder from hdk_tbexecutionorder_person where code_request = '$code' and idperson = '$person'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['exorder'];
    }

    public function getSaveDate($date, $format) {
        $vSQL = ($this->database == 'oci8po') ? "SELECT TO_CHAR(TO_DATE('$date','DD/MM/YYYY HH24:MI:SS'),'DD/MM/YYYY HH24:MI') as dat from dual" : "SELECT STR_TO_DATE('$date','$format') as dat";
        //die($vSQL);
        $ret = $this->db->Execute($vSQL);

        if (!$ret) {
            $sError = $vSQL."Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields["dat"];
    }


	public function getSaveHour($hour, $format) {
        $ret = ($this->database == 'oci8po') ? $this->db->Execute("SELECT TO_CHAR(TO_DATE('$hour','$format'),'$format') as dat from dual") : $this->db->Execute("SELECT STR_TO_DATE('$hour','$format') as dat");

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields["dat"];
    }	
	
    public function getDate($date, $format) {

        $vSQL = "";
        if ($this->database == 'oci8po'){
            if ((strpos($date, '-') === false)&&(strpos($date, '/') === false)){
                $vSQL = "SELECT to_char(TO_DATE('$date','YYYYMMDD'), 'DD/MM/YYYY') as data from dual";
            }else if(strpos($date, '-') === false){
                $vSQL = "SELECT to_char(TO_DATE('$date','DD/MM/YYYY HH24:MI'), 'DD/MM/YYYY') as data from dual";
            }
            else{
                $vSQL = "SELECT to_char(TO_DATE('$date','YYYY-MM-DD HH24:MI'), 'DD/MM/YYYY') as data from dual";
            }
        }else{
            $vSQL = "SELECT DATE_FORMAT('$date','$format') as data";
        }


        //die($vSQL);
        $ret = $this->db->Execute($vSQL);
        if (!$ret) {
            $sError = $vSQL . "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['data'];
    }

    public function getTime($date, $format) {
        $vSql = ($this->database == 'oci8po') ? "SELECT to_char(TO_DATE('$date','DD/MM/YYYY HH24:MI'), 'HH24:MI') as time from dual" : "SELECT DATE_FORMAT('$date', '$format') as time";
        //die($vSql);
        $ret = $this->select($vSql);

        if (!$ret) {
            $sError = $vSql."Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        $time = $ret->fields['time'];
        return $time;
    }

    public function getDateTime($date, $format) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "SELECT DATE_FORMAT('$date','$format') as date" ;
        } elseif ($database == 'oci8po') {
            $query = "SELECT to_char(TO_DATE('$date', 'RRRR-MM-DD HH24:MI:SS'), 'DD/MM/YYYY HH24:MI') as \"date\" from dual" ;
        }

        $ret = $this->db->Execute($query);
        if(!$ret) {
            $sError = $query . "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['date'];
    }

    public function getRequestData($code) { 
		if($this->database == 'oci8po'){
			$sql = "select
                       req.code_request,
                           TO_CHAR(req.expire_date,'DD/MM/YYYY HH24:MI') expire_date,
                           TO_CHAR(req.entry_date,'DD/MM/YYYY HH24:MI') entry_date,
                           req.flag_opened,
                           req.subject,
                           req.idperson_owner,
                           req.idperson_creator,
                           cre.name AS name_creator,
                           cre.phone_number AS phone_number,
                           cre.cel_phone AS cel_phone,
                           cre.branch_number AS branch_number,
                           req.idperson_juridical AS idcompany,
                           req.idsource,
                           req.extensions_number,
                           source.name AS source,
                           req.idstatus,
                           req.idattendance_way,
                           req.os_number,
                           req.serial_number,
                           req.label,
                           req.description,
                           comp.name AS company,
                           stat.user_view AS status,
                           rtype.name AS TYPE,
                           rtype.idtype,
                           item.iditem,
                           item.name,
                           serv.idservice,
                           serv.name AS service,
                           prio.name AS priority,
                           prio.idpriority,
                           inch.ind_in_charge,
                           inch.id_in_charge,
                           resp.name AS in_charge,
                           prio.color,
                           pers.name AS personname,
                           pers.email,
                           pers.phone_number AS phone,
                           pers.branch_number AS branch,
                           inch.TYPE AS typeincharge,
                           dep.name AS department,
                           dep.iddepartment,
                           source.name,
                           are.idarea
                      FROM hdk_tbrequest req,
                           tbperson pers,
                           tbperson comp,
                           tbperson resp,
                           tbperson cre,
                           hdk_tbdepartment dep,
                           hdk_tbcore_type rtype,
                           hdk_tbcore_service serv,
                           hdk_tbcore_area are,
                           hdk_tbpriority prio,
                           hdk_tbcore_item item,
                           hdk_tbstatus stat,
                           hdk_tbsource source,
                           hdk_tbdepartment_has_person dep_pers,
                           hdk_tbrequest_in_charge inch ,
                            hdk_tbreason reason ,
                            hdk_tbgroup grp
                     WHERE     req.idperson_owner = pers.idperson
                           and req.idreason = reason.idreason(+)
                           and resp.idperson = grp.idperson(+)
                           AND req.idperson_creator = cre.idperson
                           AND req.idstatus = stat.idstatus
                           AND req.idperson_juridical = comp.idperson
                           AND req.idtype = rtype.idtype
                           AND req.idservice = serv.idservice
                           AND req.idpriority = prio.idpriority
                           AND req.idsource = source.idsource
                           AND req.code_request = inch.code_request
                           AND req.iditem = item.iditem
                           AND dep.iddepartment = dep_pers.iddepartment
                           AND pers.idperson = dep_pers.idperson
                           AND are.idarea = rtype.idarea
                           AND inch.id_in_charge = resp.idperson
                           AND inch.ind_in_charge = 1
                           AND req.code_request = '$code'";
		}
		elseif ($this->database == 'mysqlt') {
			$sql = "select
                      req.code_request,
                      req.expire_date,
                      req.entry_date,
                      req.flag_opened,
                      req.subject,
                      req.idperson_owner,
                      req.idperson_creator,
                      cre.name AS name_creator,
                      cre.phone_number       AS phone_number,
  					  cre.cel_phone          AS cel_phone,
  					  cre.branch_number      AS branch_number,
                      req.idperson_juridical as idcompany,
                      req.idsource,
                      req.extensions_number,
                      source.name            as source,
                      req.idstatus,
                      req.idattendance_way,
                      req.os_number,
                      req.serial_number,
					  req.label,
                      req.description,
                      comp.name              as company,
                      stat.user_view         as `status`,
                      rtype.name             as `type`,
                      rtype.idtype,
                      item.iditem,
                      item.name,
                      serv.idservice,
                      serv.name              as service,
                      prio.name              as priority,
                      prio.idpriority,
                      inch.ind_in_charge,
                      inch.id_in_charge,
                      resp.name              as in_charge,
                      prio.color,
                      pers.name              as personname,
                      pers.email,              
                      pers.phone_number      as phone,
                      pers.branch_number     as branch,
                      inch.type              as typeincharge,
                      dep.name               as department,
                      dep.iddepartment,
                      source.name,
                      are.idarea
                    FROM (hdk_tbrequest req,
                       tbperson pers,
                       tbperson comp,
                       tbperson resp,
                       tbperson cre,
                       hdk_tbdepartment as dep,
                       hdk_tbcore_type rtype,
                       hdk_tbcore_service serv,
                       hdk_tbcore_area are,
                       hdk_tbpriority prio,
                       hdk_tbcore_item item,
                       hdk_tbstatus stat,
                       hdk_tbsource as source,
                       hdk_tbdepartment_has_person as dep_pers,
                       hdk_tbrequest_in_charge as inch)
                      left join hdk_tbreason as reason
                        on (req.idreason = reason.idreason)
                    left join hdk_tbgroup as grp on (resp.idperson = grp.idperson)
                    where req.idperson_owner = pers.idperson
                        AND req.idperson_creator = cre.idperson
                        and req.idstatus = stat.idstatus
                        AND req.idperson_juridical = comp.idperson
                        AND req.idtype = rtype.idtype
                        AND req.idservice = serv.idservice
                        AND req.idpriority = prio.idpriority
                        AND req.idsource = source.idsource
                        AND req.code_request = inch.code_request
                        AND req.iditem = item.iditem
                        AND dep.iddepartment = dep_pers.iddepartment
                        AND pers.idperson = dep_pers.idperson
                        AND are.idarea = rtype.idarea
                        AND inch.id_in_charge = resp.idperson
                        AND inch.ind_in_charge = 1
                        AND req.code_request = '$code'";
		}
		
        $ret = $this->db->Execute($sql);

        if (!$ret) {
            $sError = $vSql." Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }

    public function getNameCreator($creator, $code) {
		if($this->database == 'oci8po'){
			$where = "AND req.idreason = reason.idreason AND resp.idperson = grp.idperson";	
		}
		elseif($this->database == 'mysqlt'){
			$where = "AND req.idreason = reason.idreason AND resp.idperson = grp.idperson";
		}
    		
    	
        $ret = $this->db->Execute("select
                  pers.name as personname
                FROM hdk_tbrequest req,
                   tbperson pers,
                   tbperson comp,
                   tbperson resp,
                   hdk_tbdepartment  dep,
                   hdk_tbcore_type rtype,
                   hdk_tbcore_service serv,
                   hdk_tbcore_area are,
                   hdk_tbpriority prio,
                   hdk_tbcore_item item,
                   hdk_tbstatus stat,
                   hdk_tbsource  source,
                   hdk_tbdepartment_has_person  dep_pers,
                   hdk_tbrequest_in_charge  inch,
                   hdk_tbreason  reason ,
                   hdk_tbgroup  grp
                where req.idperson_owner = pers.idperson
                    $where
                    and req.idstatus = stat.idstatus
                    AND req.idperson_juridical = comp.idperson
                    AND req.idtype = rtype.idtype
                    AND req.idservice = serv.idservice
                    AND req.idpriority = prio.idpriority
                    AND req.idsource = source.idsource
                    AND req.code_request = inch.code_request
                    AND req.iditem = item.iditem
                    AND dep.iddepartment = dep_pers.iddepartment
                    AND pers.idperson = dep_pers.idperson
                    AND are.idarea = rtype.idarea
                    AND inch.id_in_charge = resp.idperson
                    AND inch.ind_in_charge = 1
                    and idperson_creator= '$creator'
                    AND req.code_request = '$code'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['personname'];
    }

    public function getCompanyName($id) {
        $ret = $this->db->Execute("select comp.name
            FROM hdk_tbdepartment  dep,
            tbperson  comp
            where comp.idperson = dep.idperson
            and iddepartment = '$id'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['name'];
    }

    public function selectAttach($id) {
        $ret =  $this->db->Execute("select file_name,idrequest_attachment from hdk_tbrequest_attachment where code_request = '$id'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function countAttachs($id) {
        $ret = $this->db->Execute("select count(idrequest_attachment) as total from hdk_tbrequest_attachment where code_request = '$id'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['total'];
    }

    public function getRequestNotes($id) {
        $vSQL = ($this->database == 'oci8po') ? "select
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
              nt.hour_type
            from
              hdk_tbnote  nt,
              tbperson  pers,
              hdk_tbnote_attachment  nta
            where code_request = '$id' and nt.idnote_attachment = nta.idnote_attachment(+) and pers.idperson = nt.idperson  order by idnote desc" : "select
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
              nt.idnote_attachment,
              nt.ip_adress,
              nt.callback,
	      	  nta.file_name,
	      	  TIME_FORMAT(TIMEDIFF(nt.finish_hour,nt.start_hour), '%Hh %imin %ss') AS diferenca,
	      	  nt.hour_type
            from (hdk_tbnote as nt,
            tbperson as pers)
	    left join hdk_tbnote_attachment as nta on (nta.idnote_attachment=nt.idnote_attachment)
            where code_request = '$id' and pers.idperson = nt.idperson  order by idnote desc";
        //die($vSQL);
        $ret = $this->db->Execute($vSQL);

        if (!$ret) {
            $sError = $vSQL."Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertNote($code, $person, $note, $date, $totalminutes, $starthour, $finishour, $execdate, $hourtype, $serviceval = null, $public, $idtype, $ipadress, $callback, $idanexo = NULL) {

        if ($this->database == 'oci8po'){
            $totalminutes = str_replace('.',',',$totalminutes);
            if (strlen($execdate) > 1 && $execdate != "0000-00-00 00:00:00"){
                $execdate =  "to_date('$execdate','DD/MM/YYYY')";
            }else{
                $execdate = " '' ";
            }
            if (strlen($serviceval) == 0){
                $serviceval = 'null';
            }

            if ($date != 'sysdate'){
                $date = "to_date('$date','DD/MM/YYYY HH24:MI')";
            }else{
                $date = "sysdate";
            }

        }
		
		if($this->database == 'oci8po'){
			//$vSQL = "insert into hdk_tbnote (code_request,idperson,description,entry_date,minutes,start_hour,finish_hour,execution_date,hour_type,service_value,public_,idtype,ip_adress,callback, idnote_attachment) values ('$code', '$person', TO_CLOB('$note'), $date, '$totalminutes', '$starthour', '$finishour', $execdate, '$hourtype', $serviceval, '$public', '$idtype', '$ipadress', '$callback', $idanexo)";
            $vSQL = "
                    DECLARE
                        clobVar CLOB := '$note';
                    BEGIN
                        INSERT INTO hdk_tbnote (code_request,idperson,description,entry_date,minutes,start_hour,finish_hour,execution_date,hour_type,service_value,public_,idtype,ip_adress,callback, idnote_attachment) VALUES('$code', '$person',clobVar, $date, '$totalminutes', '$starthour', '$finishour', $execdate, '$hourtype', $serviceval, '$public', '$idtype', '$ipadress', '$callback', $idanexo);
                    END;
                    " ;

		}elseif($this->database == 'mysqlt'){
			$vSQL = "insert into hdk_tbnote (code_request,idperson,description,entry_date,minutes,start_hour,finish_hour,execution_date,hour_type,service_value,public,idtype,ip_adress,callback, idnote_attachment) values ('$code', '$person', '$note', $date, '$totalminutes', '$starthour', '$finishour', '$execdate', '$hourtype', '$serviceval', '$public', '$idtype', '$ipadress', '$callback', $idanexo)";
		}      
        
        $ret = $this->db->Execute($vSQL);
        if (!$ret) {
            $sError = $vSQL."Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    public function insertNoteLastID() {
        return $this->db->Insert_ID( );	
    }

    public function getUserName($id) {
        $ret = $this->select("select name from tbperson where idperson = '$id'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['name'];
    }

    public function changeRequestStatus($status, $code, $person) {
        $vSQL = ($this->database == 'oci8po') ? "insert into hdk_tbrequest_log (cod_request,date_,idstatus,idperson) values ('$code',sysdate,'$status','$person')" : "insert into hdk_tbrequest_log (cod_request,date,idstatus,idperson) values ('$code',now(),'$status','$person')";
        //die($vSQL);
        $ret = $this->db->Execute($vSQL);

        if (!$ret) {
            $sError = $vSQL."Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertInCharge($code, $person, $type, $rep, $ind, $track = 0) {
        $ret = $this->db->Execute("insert into hdk_tbrequest_in_charge (code_request,id_in_charge,ind_in_charge,type,ind_repass,ind_track) values ('$code','$person','$ind','$type','$rep','$track')");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRepassGroups($where = null) {

        $vSQL = ($this->database == 'oci8po') ? "select tbg.idgroup, tbp.idperson, tbp.name, tbg.level_, tbg.status
                from hdk_tbgroup tbg,
                tbperson  tbp
                where tbg.status = 'A' and tbg.idperson = tbp.idperson $where order by 3" : "select tbg.idgroup, tbp.idperson, tbp.name, tbg.level, tbg.status
                from hdk_tbgroup tbg,
                tbperson  tbp
                where tbg.status = 'A' and tbg.idperson = tbp.idperson $where order by 3";
        //die($vSQL);

        $ret = $this->select($vSQL);
        if (!$ret) {
            $sError = $vSQL."Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRepassOperators($where = null) {
        $ret = $this->select("select idperson, name from tbperson where status = 'A' and idtypeperson IN ('1','3') $where order by name");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRepassPartners() {
        $ret = $this->select("select idperson, name from tbperson where status = 'A' and idtypeperson = '5'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getAbilityGroup($idgrp) {
        $ret = $this->select("select grpp.name, serv.name as service, grp.idgroup, serv.idservice
            from hdk_tbcore_service  serv,
            hdk_tbgroup  grp,
            tbperson  grpp,
            hdk_tbgroup_has_service  relat
            where
            grp.idgroup = relat.idgroup
            And grpp.idperson = grp.idperson
            and serv.idservice = relat.idservice
            and grpp.idperson = '$idgrp'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getAbilityOperator($idop) {
        $ret = $this->select("select per.name, serv.name as  service, per.idperson, serv.idservice, grpper.name
        from hdk_tbcore_service serv,
        hdk_tbgroup grp,
        hdk_tbgroup_has_service relat,
        tbperson per,
        tbperson grpper,
        hdk_tbgroup_has_person relatp
        where
        grp.idgroup = relat.idgroup
        AND grpper.idperson = grp.idperson
        and serv.idservice = relat.idservice
        AND relatp.idperson = per.idperson
        AND relatp.idgroup = grp.idgroup
        and per.idperson = '$idop'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getGroupOperators($idgrp) {
        $ret = $this->select("select per.name, per.idperson, grp.idperson as groupname, grp.idgroup
        from hdk_tbgroup grp,
        tbperson per,
        tbperson grppr, 
        hdk_tbgroup_has_person rel
        where per.idperson = rel.idperson
        and grp.idgroup = rel.idgroup
        AND grppr.idperson = grp.idperson
        and grppr.idperson = '$idgrp'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getOperatorGroups($idgrp) {
        $ret = $this->select("select grppr.name as pername, per.idperson, grp.idperson as idpergroup, grp.idgroup
            from hdk_tbgroup grp,
            tbperson per,
            tbperson grppr,
            hdk_tbgroup_has_person rel
            where per.idperson = rel.idperson
            AND grppr.idperson = grp.idperson
            and grp.idgroup = rel.idgroup
            and per.idperson = '$idgrp'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getAbilityPartners($idop) {
        $ret = $this->select("select per.name, serv.service, per.idperson, serv.idservice, grp.name
            from hdk_tbcore_service as serv,
            hdk_tbgroup as grp,
            hdk_tbgroup_has_service as relat,
            tbperson as per,
            hdk_tbgroup_has_person as relatp
            where
            grp.idgroup = relat.idgroup
            and serv.idservice = relat.idservice
            AND relatp.idperson = per.idperson
            AND relatp.idgroup = grp.idgroup
            and per.idperson = '$idop'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRepassRequest($date, $code, $note) {
		if($this->database == 'oci8po') {
			$sql = "insert into hdk_tbrequest_repassed (date_,idnote,code_request) values ($date,'$note','$code')";
		} elseif ($this->database == 'mysqlt') {
			$sql = "insert into hdk_tbrequest_repassed (date,idnote,code_request) values ($date,'$note','$code')";
		}

        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "query; " . $sql;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRepassNote($code) {
        $ret = $this->select("select idnote, description, code_request from hdk_tbnote where code_request = '$code' order by idnote DESC");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['idnote'];
    }

    public function getReqLog($code) {
        $ret = $this->select("select id from hdk_tbrequest_log where cod_request = '$code' order by id desc limit 1");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret->fields['id'];
    }

//possivelmente essa funcao sera apagada


    public function updateReqStatus($id, $code) {
        $ret = $this->db->Execute("update hdk_tbrequest set idstatus = '$id' where code_request = '$code'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function removeIncharge($code) {
        $ret = $this->db->Execute("update hdk_tbrequest_in_charge set ind_in_charge = '0' where code_request = '$code'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function deleteNote($id) {
        $ret = $this->db->Execute("delete from hdk_tbnote where idnote = '$id'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getExtNumber($code) {
        $ret = $this->db->Execute("select extensions_number from hdk_tbrequest where code_request = '$code'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['extensions_number'];
    }

    public function saveExtension($code, $value, $date) {

        if($this->database == 'oci8po') {
			$vSQL = "update hdk_tbrequest set extensions_number = '$value', expire_date = to_date('$date','DD/MM/YYYY HH24:MI') where code_request = '$code'";
        } 
        elseif($this->database == 'mysqlt'){
			$vSQL = "update hdk_tbrequest set extensions_number = '$value', expire_date = $date where code_request = '$code'";
        } 
        
        $ret = $this->db->Execute($vSQL);

        if (!$ret) {
            $sError = $vSQL."Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
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
    
    public function getIdPersonOthersgroup($groups){
        $ret = $this->select("select idperson from hdk_tbgroup_has_person where idgroup in($groups) group by 1  order by 1");
        if(!$ret){
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        } else {
            return $ret;
        }
    }

    public function getQuestions() {
        $ret = $this->select("select idquestion, question from hdk_tbevaluationquestion where status = 'A'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getAnswers($id) {
        $ret = $this->select("SELECT name, icon_name, idevaluation, checked from hdk_tbevaluation where status = 'A' and idquestion = '$id' ORDER BY idevaluation ASC");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertEvaluation($eval, $code, $date) {
        $vSQL = ($this->database == 'oci8po') ? "insert into hdk_tbrequest_evaluation (idevaluation, code_request, date_) values ('$eval','$code',$date)" : "insert into hdk_tbrequest_evaluation (idevaluation, code_request, date) values ('$eval','$code',$date)";
        //die($vSQL);
        $ret = $this->db->Execute($vSQL);
        if (!$ret) {
            $sError = $vSQL."Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    public function clearEvaluation($code) {
        $ret = $this->db->Execute("DELETE FROM hdk_tbrequest_evaluation where code_request = '$code'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getEvaluationGiven($code) {
        $ret = $this->select("select
              name
            from hdk_tbevaluation ev,
              hdk_tbrequest_evaluation reqeval
            where ev.idevaluation = reqeval.idevaluation
            and reqeval.code_request = '$code'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['name'];
    }
    public function updateRequest($code, $type, $item , $service, $reason, $way, $priority) {
        $ret = $this->db->Execute("update hdk_tbrequest set 
                                   idtype=$type, iditem=$item , idservice=$service, idreason=$reason, idattendance_way=$way, idpriority=$priority
                                    where code_request = '$code'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function updateWay($code, $way) {
        $ret = $this->db->Execute("update hdk_tbrequest set idattendance_way=$way where code_request = '$code'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    public function updateFlag($code, $flag) {
        $ret = $this->db->Execute("update hdk_tbrequest set 
                                   flag_opened = $flag
                                   where code_request = '$code'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
	
	public function getIdStatusSource($idstatus){
		$vSql = "select idstatus_source from hdk_tbstatus where idstatus = '$idstatus'";
        $ret = $this->db->Execute($vSql);
        if (!$ret) {
            $sError = $vSql. "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
	}
	
	public function updateRequestStatus($idstatus, $code_request){
		$ret = $this->db->Execute("UPDATE hdk_tbrequest SET idstatus = $idstatus WHERE code_request = '$code_request'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
	}

	public function insertOperatorAux($code_request,$idperson) {
		$sql =	"
				insert into hdk_tbrequest_in_charge 
					(
					code_request, 
					id_in_charge, 
					type, 
					ind_in_charge, 
					ind_repass, 
					ind_track, 
					ind_operator_aux
					)
					values
					(
					'$code_request', 
					'$idperson', 
					'P', 
					0, 
					'N', 
					0, 
					1
					)		
				";
        return $this->db->Execute($sql);
    }   
	
	public function deleteOperatorAux($code_request,$idperson) {
		$sql = "
				DELETE
				FROM hdk_tbrequest_in_charge
				WHERE id_in_charge = $idperson
					 AND code_request = $code_request
					 AND ind_operator_aux = 1		
				";
		return $this->db->Execute($sql);
	}	
	
	public function getNote($idnote){
		$sql = "SELECT a.idperson, a.idnote_attachment, b.file_name FROM hdk_tbnote a LEFT JOIN hdk_tbnote_attachment b  ON a.idnote_attachment = b.idnote_attachment WHERE idnote = '$idnote'";
		return $this->db->Execute($sql);
	}
	
	public function deleteAttachNote($idattach){
		$sql = "DELETE FROM hdk_tbnote_attachment WHERE idnote_attachment = '$idattach'";
		return $this->db->Execute($sql);
	}
	
    public function getPersonPlus($id,$idtypepersonplus) {
		$sql = 	"
				select
					idpersonplus,
					login,
				    password
				from tbperson_plus
				where idtypepersonplus = $idtypepersonplus
					 and idperson = '$id'	
				";
		$ret = $this->db->Execute($sql);		
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
	public function insertPersonPlus($idperson, $login, $password, $idtypepersonplus) {
		$sql =	"
				insert into tbperson_plus
							(
							 idtypepersonplus,
							 idperson,
							 login,
							 password)
				values (
						'$idtypepersonplus',
						'$idperson',
						'$login',
						'$password');
				";
        return $this->db->Execute($sql);
    }   

	public function updatePersonPlus($idpersonplus, $login, $password) {
		$sql =	"
				update tbperson_plus
				set login = '$login',
				   password = '$password'
				where idpersonplus = '$idpersonplus'
				";
        return $this->db->Execute($sql);
    }
    
    public function getNoteAttachment($coderequest) {
		$sql =	"
				SELECT b.idnote_attachment, b.file_name
				FROM hdk_tbnote a
				  LEFT JOIN hdk_tbnote_attachment b
				ON a.idnote_attachment = b.idnote_attachment
				WHERE a.code_request = $coderequest
				ORDER BY idnote DESC
				LIMIT 1
				";
        return $this->db->Execute($sql);
    }   
	
	public function getEntryDate($code_request){
		$sql = 	"SELECT entry_date FROM hdk_tbrequest WHERE code_request = '$code_request'";
		$ret = $this->db->Execute($sql);		
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['entry_date'];
	}
	
	public function updateTime($code_request, array $data){
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

  public function updateDate($code_request, $column){
      if ($this->database == 'oci8po'){
          $sql = "UPDATE hdk_tbrequest_dates set $column = sysdate WHERE code_request = $code_request";
      }
      elseif ($this->database == 'mysqlt'){
          $sql = "UPDATE hdk_tbrequest_dates set $column = NOW() WHERE code_request = $code_request";
      }

      $ret = $this->db->Execute($sql);
      return $ret;
  }
	
	public function getAssumedDate($code_request){
        $vSQL = ($this->database == 'oci8po') ? "SELECT to_char(date_,'DD/MM/YYYY HH24:MI:SS') FROM hdk_tbrequest_log WHERE cod_request = $code_request AND idstatus = 3 and rownum = 1 ORDER BY id ASC " : "SELECT date FROM hdk_tbrequest_log WHERE cod_request = $code_request AND idstatus = 3 ORDER BY id ASC LIMIT 1";
        $ret = $this->db->Execute($vSQL);

        if (!$ret) {
            $sError = $vSQL."Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['date'];
	}
	
	public function getExpendedTime($code_request){
        $vSQL = "SELECT SUM(minutes) as minutes FROM hdk_tbnote WHERE code_request = $code_request";
        $ret = $this->db->Execute($vSQL);
        if (!$ret) {
            $sError = $vSQL." Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['minutes'];
		
	}

  public function insertChangeExpireDate($code, $reason, $idperson) {        
        if ($this->database == 'oci8po'){
            $ret = $this->select("
              INSERT INTO 
                hdk_tbrequest_change_expire 
                (code_request,reason,idperson,changedate) 
                values 
                ('$code','$reason', $idperson,  sysdate)"
            );
        }elseif ($this->database == 'mysqlt'){
            $ret = $this->select("
              INSERT INTO 
                hdk_tbrequest_change_expire 
                (code_request,reason,idperson,changedate) 
                values 
                ('$code','$reason', $idperson,  NOW())"
            );
        }


        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }



		
}
