<?php

class operatorview_model extends Model {

    public function getRequests($where = NULL, $limit = NULL, $cod_user, $cod_group_user) {

        $ret = $this->select("(select
          req.code_request,
          req.expire_date,
          req.entry_date,
          req.subject,       
          req.idperson_owner,
          req.idperson_creator,
          req.idperson_juridical as idcompany,
          comp.name              as company,
          stat.user_view              as `status`,
          reqlog.idstatus,
          rtype.name             as `type`,
          rtype.idtype,
          item.iditem,
          serv.idservice,
          serv.service           as service,
          req.execution_order,
          prio.name              as priority,
          prio.idpriority,
          req_grp.ind_in_charge,
          req_grp.aux_operator,
          prio.color,
          item.item,
          pers.name as personname,
          inch.type as typeincharge,
          dep.name as department,
          dep.iddepartment,
          req.idsource,
          req.idattendance_way,
          req.os_number,
          req.serial_number,
          req.description,
          source.source,
          comp.name as company,
          are.idarea,
          reason.idreason
        FROM hdk_tbrequest req,
          tbperson pers,
          tbperson comp,
          hdk_tbdepartment as dep,
          hdk_tbrequest_log reqlog,
          hdk_tbcore_area are,
          hdk_tbcore_type rtype,
          hdk_tbcore_service serv,
          hdk_tbpriority prio,
          hdk_tbcore_item item,
          hdk_tbgroup_has_person pers_gro,
          hdk_tbrequest_group req_grp,
          hdk_tbstatus stat,
          hdk_tbgroup as grp,
          hdk_tbsource as source,
          hdk_tbdepartment_has_person as relat,
          hdk_tbrequest_in_charge as inch,
          hdk_tbreason as reason,
          hdk_tbrequest_log as statlog
        where req.idperson_owner = pers.idperson
            AND req.idperson_juridical = comp.idperson
            AND rtype.idtype = req.idtype
            AND dep.iddepartment = relat.iddepartment
            AND pers.idperson = relat.idperson
            AND serv.idservice = req.idservice
            AND prio.idpriority = req.idpriority
            AND are.idarea = rtype.idarea
            AND item.iditem = req.iditem
            AND stat.idstatus = reqlog.idstatus
            AND req_grp.idgroup = grp.idgroup
            AND pers_gro.idperson = pers.idperson
            AND pers_gro.idgroup = grp.idgroup
            AND reqlog.cod_request = req.code_request
            AND reqlog.cod_request = req_grp.code_request
            AND inch.code_request = req.code_request
            AND req.idsource = source.idsource
            AND req.idreason = reason.idreason
            AND statlog.idstatus = stat.idstatus
            AND statlog.idperson = pers.idperson
            AND inch.id_in_charge = grp.idgroup
            AND req.idrequest_log = statlog.id
            AND inch.type = 'G'
            AND inch.ind_repass = 'N'
            AND req.idperson_creator = '$cod_user'
            AND grp.idgroup in ($cod_group_user)  $where
                         order by reqlog.idstatus desc)
        UNION (select
          req.code_request,
          req.expire_date,
          req.entry_date,
          req.subject,       
          req.idperson_owner,
          req.idperson_creator,
          req.idperson_juridical as idcompany,
          comp.name              as company,
          stat.user_view              as `status`,
          reqlog.idstatus,
          rtype.name             as `type`,
          rtype.idtype,
          item.iditem,
          serv.idservice,
          serv.service           as service,
          req.execution_order,
          prio.name              as priority,
          prio.idpriority,
          req_grp.ind_in_charge,
          req_grp.aux_operator,
          prio.color,
          item.item,
          pers.name as personname,
          inch.type as typeincharge,
          dep.name as department,
          dep.iddepartment,
          req.idsource,
          req.idattendance_way,
          req.os_number,
          req.serial_number,
          req.description,
          source.source,
          comp.name as company,
          are.idarea,
          reason.idreason
        FROM hdk_tbrequest req,
          tbperson pers,
          tbperson comp,
          hdk_tbdepartment as dep,
          hdk_tbrequest_log reqlog,
          hdk_tbcore_area are,
          hdk_tbcore_type rtype,
          hdk_tbcore_service serv,
          hdk_tbpriority prio,
          hdk_tbcore_item item,
          hdk_tbgroup_has_person pers_gro,
          hdk_tbrequest_group req_grp,
          hdk_tbstatus stat,
          hdk_tbgroup as grp,
          hdk_tbsource as source,
          hdk_tbdepartment_has_person as relat,
          hdk_tbrequest_in_charge as inch,
          hdk_tbreason as reason,
          hdk_tbrequest_log as statlog
        where req.idperson_owner = pers.idperson
            AND req.idperson_juridical = comp.idperson
            AND rtype.idtype = req.idtype
            AND dep.iddepartment = relat.iddepartment
            AND pers.idperson = relat.idperson
            AND serv.idservice = req.idservice
            AND prio.idpriority = req.idpriority
            AND are.idarea = rtype.idarea
            AND item.iditem = req.iditem
            AND stat.idstatus = reqlog.idstatus
            AND req_grp.idgroup = grp.idgroup
            AND pers_gro.idperson = pers.idperson
            AND pers_gro.idgroup = grp.idgroup
            AND reqlog.cod_request = req.code_request
            AND reqlog.cod_request = req_grp.code_request
            AND inch.code_request = req.code_request
            AND req.idsource = source.idsource
            AND req.idreason = reason.idreason
            AND statlog.idstatus = stat.idstatus
            AND statlog.idperson = pers.idperson
            AND inch.id_in_charge = pers.idperson
            AND req.idrequest_log = statlog.id
            AND inch.type = 'P'
            AND inch.ind_repass = 'N'
            AND inch.id_in_charge = '$cod_user'
            order by reqlog.idstatus desc) $limit");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getGroupName($id) {
        $ret = $this->db->Execute("select name from hdk_tbgroup where idgroup = '$id'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['name'];
    }

    public function getPersonName($id) {
        $ret = $this->db->Execute("select name from tbperson where idperson = '$id'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['name'];
    }

    public function countRequests($where = NULL) {
        $ret = $this->select("SELECT count(idrequest) as total from hdk_tbrequest req,
          tbperson pers,
          tbperson comp,
          hdk_tbrequest_log reqlog,
          hdk_tbcore_type rtype,
          hdk_tbcore_service serv,
          hdk_tbpriority prio,
          hdk_tbcore_item item,
          hdk_tbgroup_has_person pers_gro,
          hdk_tbrequest_group req_grp,
          hdk_tbstatus stat,
          hdk_tbgroup as grp,
          hdk_tbdepartment_has_person as relat,
          hdk_tbrequest_in_charge as inch,
          hdk_tbdepartment as dep
        where req.idperson_owner = pers.idperson
            AND req.idperson_juridical = comp.idperson
            AND rtype.idtype = req.idtype
            AND dep.iddepartment = relat.iddepartment
            AND pers.idperson = relat.idperson
            AND serv.idservice = req.idservice
            AND prio.idpriority = req.idpriority
            AND item.iditem = req.iditem
            AND stat.idstatus = reqlog.idstatus
            AND req_grp.idgroup = req.code_group
            AND req_grp.idgroup = grp.idgroup
            AND pers_gro.idperson = pers.idperson
            AND pers_gro.idgroup = grp.idgroup
            AND reqlog.cod_request = req.code_request
            AND reqlog.cod_request = req_grp.code_request
            AND inch.code_request = req.code_request $where");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectAttachment($code_request) {
        $ret = $this->db->Execute("select idrequest_attachment from hdk_tbrequest_attachment where code_request='$code_request'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertExecutionOrder($code, $value, $person) {
        $ret = $this->db->Execute("insert into hdk_tbexecutionorder_person (code_request,idperson,exorder) values ('$code','$person','$value')");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function deleteExecutionOrder($code, $value, $person) {
        $ret = $this->db->Execute("delete from hdk_tbexecutionorder_person where code_request = '$code' and idperson = '$person'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function updateExecutionOrder($code, $value, $person) {
        $ret = $this->db->Execute("update hdk_tbexecutionorder_person set exorder='$value' where code_request = '$code' and idperson = '$person'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function checkOrder($code, $value, $person) {
        $ret = $this->db->Execute("select idexecutionorder from hdk_tbexecutionorder_person where code_request = '$code' and idperson = '$person'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getOrder($code, $person) {
        $ret = $this->db->Execute("select exorder from hdk_tbexecutionorder_person where code_request = '$code' and idperson = '$person'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['exorder'];
    }
    public function getSaveDate($date, $format) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $ret = $this->db->Execute("SELECT STR_TO_DATE('$date','$format') as date");
            if (!$ret) {
                $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br>--" . $query;
                $this->error($sError);
                return false;
            }
            return $ret->fields['date'];
        } elseif ($database == 'oci8po') {
            $format = $this->getConfig('oracle_format_date');
            return "TO_DATE('$date', '$format')";            
        }
    }
    
    public function getSaveHour($hour, $format) {
        $ret = $this->db->Execute("SELECT STR_TO_DATE('$hour','$format') as date");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['date'];
    }
	
    public function getDate($date, $format) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "SELECT DATE_FORMAT('$date','$format') as date" ;          
        } elseif ($database == 'oci8po') {
            if ((strpos($date, '-') === false)&&(strpos($date, '/') === false)){
                $query = "SELECT to_char(TO_DATE('$date','YYYYMMDD'), 'DD/MM/YYYY') as \"date\"  from dual";
            }else if(strpos($date, '-') === false){
                $query = "SELECT to_char(TO_DATE('$date','DD/MM/YYYY HH24:MI'), 'DD/MM/YYYY') as \"date\"  from dual";
            }else{
                $query = "SELECT to_char(TO_DATE('$date','YYYY-MM-DD HH24:MI'), 'DD/MM/YYYY') as \"date\" from dual";
            }

        }
        $ret = $this->db->Execute($query);
        if(!$ret) {
            $sError = $query."Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['date'];
    }

    public function getTime($date, $format) {
        $ret = $this->db->Execute("SELECT DATE_FORMAT('$date', '$format') as time");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
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
        $ret = $this->db->Execute("select
          req.code_request,
          req.expire_date,
          req.entry_date,
          req.subject,       
          req.idperson_owner,
          req.idperson_creator,
          req.idperson_juridical as idcompany,
          comp.name              as company,
          stat.user_view             as status,
          reqlog.idstatus,
          rtype.name             as type,
          rtype.idtype,
          item.iditem,
          serv.idservice,
          serv.service           as service,
          req.execution_order,
          prio.name              as priority,
          prio.idpriority,
          req_grp.ind_in_charge,
          req_grp.aux_operator,
          prio.color,
          item.item,
          pers.name as personname,
          inch.type as typeincharge,
          dep.name as department,
          dep.iddepartment,
          req.idsource,
          req.idattendance_way,
          req.os_number,
          req.serial_number,
          req.description,
          source.source,
          are.idarea,
          reason.idreason              
        FROM hdk_tbrequest req,
          tbperson pers,
          tbperson comp,
          hdk_tbdepartment  dep,
          hdk_tbrequest_log reqlog,
          hdk_tbcore_type rtype,
          hdk_tbcore_service serv,
          hdk_tbcore_area are,
          hdk_tbpriority prio,
          hdk_tbcore_item item,
          hdk_tbgroup_has_person pers_gro,
          hdk_tbrequest_group req_grp,
          hdk_tbstatus stat,
          hdk_tbgroup  grp,
          hdk_tbsource  source,
          hdk_tbdepartment_has_person  relat,
          hdk_tbrequest_in_charge  inch,
          hdk_tbreason  reason
        where req.idperson_owner = pers.idperson
            AND req.idperson_juridical = comp.idperson
            AND rtype.idtype = req.idtype
            AND dep.iddepartment = relat.iddepartment
            AND pers.idperson = relat.idperson
            AND serv.idservice = req.idservice
            AND prio.idpriority = req.idpriority
            AND are.idarea = rtype.idarea
            AND item.iditem = req.iditem
            AND stat.idstatus = reqlog.idstatus
            AND req_grp.idgroup = grp.idgroup
            AND pers_gro.idperson = pers.idperson
            AND pers_gro.idgroup = grp.idgroup
            AND reqlog.cod_request = req.code_request
            AND reqlog.cod_request = req_grp.code_request
            AND inch.code_request = req.code_request
            AND req.idsource = source.idsource
            AND req.idreason = reason.idreason
            AND req.code_request = '$code'
            order by idstatus desc");
            if(!$ret) {
                $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
                $this->error($sError);
                return false;
            }
            return $ret;
            }

            public function getNameCreator($creator, $code) {
                $ret = $this->db->Execute("select
          pers.name as personname
        FROM hdk_tbrequest req,
          tbperson pers,
          tbperson comp,
          hdk_tbrequest_log reqlog,
          hdk_tbcore_type rtype,
          hdk_tbcore_service serv,
          hdk_tbpriority prio,
          hdk_tbcore_item item,
          hdk_tbgroup_has_person pers_gro,
          hdk_tbrequest_group req_grp,
          hdk_tbstatus stat,
          hdk_tbgroup as grp,
          hdk_tbdepartment_has_person as relat,
          hdk_tbrequest_in_charge as inch,
          hdk_tbcore_area are,
          hdk_tbdepartment as dep           
        where req.idperson_creator = pers.idperson
            AND req.idperson_juridical = comp.idperson
            AND rtype.idtype = req.idtype
            AND dep.iddepartment = relat.iddepartment
            AND pers.idperson = relat.idperson
            AND serv.idservice = req.idservice
            AND prio.idpriority = req.idpriority
            AND are.idarea = rtype.idarea
            AND item.iditem = req.iditem
            AND stat.idstatus = reqlog.idstatus
            AND req_grp.idgroup = grp.idgroup
            AND pers_gro.idperson = pers.idperson
            AND pers_gro.idgroup = grp.idgroup
            AND reqlog.cod_request = req.code_request
            AND reqlog.cod_request = req_grp.code_request
            AND inch.code_request = req.code_request
            and idperson_creator='$creator'
            AND req.code_request = '$code'");
        return $ret->fields['personname'];
    }

    public function getCompanyName($id) {
        $ret = $this->db->Execute("select comp.name
        FROM hdk_tbdepartment as dep,
        tbperson as comp
        where comp.idperson = dep.idperson
        and iddepartment = '$id'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['name'];
    }

    public function selectAttach($id) {
        $ret = $this->db->Execute("select file_name,idrequest_attachment from hdk_tbrequest_attachment where code_request = '$id'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function countAttachs($id) {
        $ret = $this->db->Execute("select count(idrequest_attachment) as total from hdk_tbrequest_attachment where code_request = '$id'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['total'];
    }

    public function getRequestNotes($id) {
        $ret = $this->db->Execute("select
          idnote,
          idperson,
          description,
          entry_date,
          minutes,
          start_hour,
          finish_hour,
          execution_date,
          public,
          idtype,
          attached_file,
          ip_adress,
          callback
        from hdk_tbnote
        where code_request = '$id'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertNote($code, $person, $note, $date, $totalminutes, $starthour, $finishour, $execdate, $hourtype, $serviceval, $public, $idtype, $ipadress, $callback, $idanexo = null) {
        $ret = $this->db->Execute("insert into hdk_tbnote (code_request,idperson,description,entry_date,minutes,start_hour,finish_hour,execution_date,hour_type,service_value,public,idtype,ip_adress,callback) values ('$code', '$person', '$note', $date, '$totalminutes', '$starthour', '$finishour', '$execdate', '$hourtype', '$serviceval', '$public', '$idtype', '$ipadress', '$callback', $idanexo)");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getUserName($id) {
        $ret = $this->select("select name from tbperson where idperson = '$id'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['name'];
    }

    public function changeRequestStatus($status, $code, $person) {
        $re = $this->db->Execute("insert into hdk_tbrequest_log (cod_request,date,idstatus,idperson) values ('$code',now(),'$status','$person')");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertInCharge($code, $person, $type, $rep) {
        $ret = $this->db->Execute("insert into hdk_tbrequest_in_charge (code_request,id_in_charge,type,ind_repass) values ('$code','$person','$type','$rep')");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRepassGroups() {
        $ret = $this->select("select idgroup, name, level, status
        from hdk_tbgroup
        where status = 'A'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRepassOperators() {
        $ret = $this->select("select idperson, name from tbperson where status = 'A' and idtypeperson = '3'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRepassPartners() {
        $ret = $this->select("select idperson, name from tbperson where status = 'A' and idtypeperson = '5'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getAbilityGroup($idgrp) {
        $ret = $this->select("select grp.name, serv.service, grp.idgroup, serv.idservice
        from hdk_tbcore_service as serv,
        hdk_tbgroup as grp,
        hdk_tbgroup_has_service as relat
        where
        grp.idgroup = relat.idgroup
        and serv.idservice = relat.idservice
        and grp.idgroup = '$idgrp'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    public function getAbilityOperator($idop){
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
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    public function getGroupOperators($idgrp){
        $ret = $this->select("select per.name, per.idperson, grp.name as groupname, grp.idgroup
        from hdk_tbgroup grp,
        tbperson per,
        hdk_tbgroup_has_person as rel
        where per.idperson = rel.idperson
        and grp.idgroup = rel.idgroup
        and grp.idgroup = '$idgrp'
        ");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    public function getOperatorGroups($idgrp){
        $ret = $this->select("select per.name as pername, per.idperson, grp.name , grp.idgroup
        from hdk_tbgroup grp,
        tbperson per,
        hdk_tbgroup_has_person as rel
        where per.idperson = rel.idperson
        and grp.idgroup = rel.idgroup
        and per.idperson = '$idgrp'
        ");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    public function getAbilityPartners($idop){
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
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    
    public function insertRepassRequest($date,$code, $note){
        $ret = $this->select("insert into hdk_tbrequest_repassed (date,idnote,code_request) values ($date,'$note','$code')");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    public function getRepassNote($code){
        $ret = $this->select("select idnote, description, code_request from hdk_tbnote where code_request = '$code' order by idnote DESC");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['idnote'];        
    }
    
    public function getReqLog($code){
        $ret = $this->select("select id from hdk_tbrequest_log where cod_request = '$code' order by id desc limit 1");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['id'];
    }
    
    public function updateReqLog($code, $reqlog){
       $ret = $this->db->Execute("update hdk_tbrequest set idrequest_log = '$reqlog' where code_request = '$code'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

}

?>
