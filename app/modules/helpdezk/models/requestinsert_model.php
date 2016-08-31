<?php

class requestinsert_model extends Model {

    public $database;

    public function __construct(){
      parent::__construct();
          $this->database = $this->getConfig('db_connect');

    }

    public function selectSource() {
        $ret =  $this->select("select idsource, name, icon from hdk_tbsource ORDER BY name ASC");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectArea() {
        $ret = $this->select("select idarea, name from hdk_tbcore_area where status = 'A' ORDER BY name ASC");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectType($area) {
        $ret = $this->select("select idtype, name, selected from hdk_tbcore_type where idarea = '$area' and status = 'A' ORDER BY name ASC");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectItem($type) {
        $ret = $this->select("select iditem, name, selected from hdk_tbcore_item where idtype = '$type' and status = 'A' ORDER BY name ASC");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectService($item) {
        $ret = $this->select("select idservice, name, selected from hdk_tbcore_service where iditem = '$item' and status = 'A' ORDER BY name ASC");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectReason($type) {
        $ret = $this->select("select idreason , reason from hdk_tbreason where idservice = '$type' and status = 'A' ORDER BY reason ASC");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectWay() {
        $ret= $this->select("select idattendanceway, way from hdk_tbattendance_way ORDER BY way ASC");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
	
	public function insertWay($way){
        $vSQL = ($this->database == 'oci8po') ? "insert into hdk_tbattendance_way (way) values ('".$way."')" : "insert into hdk_tbattendance_way (way) values ('".$way."')";
        //die($vSQL);
        $ret = $this->select($vSQL);
        return $ret;
    }
	
	public function InsertID() {
        return $this->db->Insert_ID( );	
    }

    //nesta funcao teremos 2 campos null no final, que sao os que dizem se vamos inserir o código da solicitacao ou se o banco vai autoincrementar, isso vai depende da variavel de sessao testada la no controller
    public function insertRequest($idperson_creator, $source, $date, $type, $item, $service, $reason, $way, $subject, $description, $osnumber, $idpriority, $tag, $serial_number, $idjuridical, $expiration_date, $idperson_owner, $idstatus, $code_request) {
        
//        $ret = $this->db->Execute("insert into hdk_tbrequest  (code_request, `subject`, description, idtype, iditem, idservice, idreason, idpriority, idsource, idperson_creator, entry_date, os_number, label, serial_number, idperson_juridical,expire_date, idattendance_way, idperson_owner, idstatus) 
//                                 values($code_request, '" . $subject . "', '" . $description . "', $type, $item, $service, $reason, $idpriority,  $source,     $idperson_creator,          $date, '" . $osnumber . "','" . $tag . "','" . $serial_number . "',$idjuridical,'" . $expiration_date . "', $way, $idperson_owner, $idstatus)");
        
        
        if($this->database == 'oci8po'){
            /*
        	$sql = "insert into hdk_tbrequest  (code_request, subject, description, idtype, iditem, idservice, idreason, idpriority, idsource, idperson_creator, entry_date, os_number, label, serial_number, idperson_juridical,expire_date, idattendance_way, idperson_owner, idstatus)
                    values($code_request, '" . $subject . "', '" . $description . "', $type, $item, $service, $reason, $idpriority,  $source,     $idperson_creator, TO_DATE('".$date."','DD/MM/YYYY HH24:MI:SS') , '" . $osnumber . "','" . $tag . "','" . $serial_number . "',$idjuridical,TO_DATE('" . $expiration_date . "','DD/MM/YYYY HH24:MI:SS'), $way, $idperson_owner, $idstatus)";
            */
            $sql =  "
                    DECLARE
                        clobVar CLOB := '$description';
                    BEGIN
                        INSERT INTO hdk_tbrequest (
                               code_request,
                               SUBJECT,
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
                        VALUES
                        (
                              $code_request,
                              '$subject',
                              clobVar,
                              $type,
                              $item,
                              $service,
                              $reason,
                              $idpriority,
                              $source,
                              $idperson_creator,
                              TO_DATE('$date','DD/MM/YYYY HH24:MI:SS'),
                              '$osnumber',
                              '$tag',
                              '$serial_number',
                              $idjuridical,
                              TO_DATE('$expiration_date','DD/MM/YYYY HH24:MI:SS'),
                              $way,
                              $idperson_owner,
                              $idstatus
                           ) ;
                    END;

                    ";
        }elseif($this->database == 'mysqlt'){
			$sql = "insert into hdk_tbrequest  (code_request, `subject`, description, idtype, iditem, idservice, idreason, idpriority, idsource, idperson_creator, entry_date, os_number, label, serial_number, idperson_juridical,expire_date, idattendance_way, idperson_owner, idstatus)
                                        values($code_request, '" . $subject . "', '" . $description . "', $type, $item, $service, $reason, $idpriority,  $source,     $idperson_creator,         '$date' , '" . $osnumber . "','" . $tag . "','" . $serial_number . "',$idjuridical,'" . $expiration_date . "', $way, $idperson_owner, $idstatus)";        	
        }
        
		//die($sql);
        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() ."SQL: ".$sql;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function insertRequest2($idperson_creator, $source, $date, $type, $item, $service, $reason, $way, $subject, $description, $osnumber, $idpriority, $tag, $serial_number, $idjuridical, $expiration_date, $idperson_owner, $idstatus, $code_request) {

        $ret = ($this->database == 'oci8po') ? "insert into hdk_tbrequest  (code_request, subject, description, idtype, iditem, idservice, idreason, idpriority, idsource, idperson_creator, entry_date, os_number, label, serial_number, idperson_juridical,expire_date, idattendance_way, idperson_owner, idstatus)
                                 values($code_request, '" . $subject . "', '" . $description . "', $type, $item, $service, $reason, $idpriority,  $source,     $idperson_creator, TO_DATE('".$date."','DD/MM/YYYY HH24:MI:SS') , '" . $osnumber . "','" . $tag . "','" . $serial_number . "',$idjuridical,TO_DATE('" . $expiration_date . "','DD/MM/YYYY HH24:MI:SS'), $way, $idperson_owner, $idstatus)" :
                                        "insert into hdk_tbrequest  (code_request, `subject`, description, idtype, iditem, idservice, idreason, idpriority, idsource, idperson_creator, entry_date, os_number, label, serial_number, idperson_juridical,expire_date, idattendance_way, idperson_owner, idstatus)
                                        values($code_request, '" . $subject . "', '" . $description . "', $type, $item, $service, $reason, $idpriority,  $source,     $idperson_creator,         '$date' , '" . $osnumber . "','" . $tag . "','" . $serial_number . "',$idjuridical,'" . $expiration_date . "', $way, $idperson_owner, $idstatus)";
        //$ret = "insert into hdk_tbrequest  (code_request, `subject`, description, idtype, iditem, idservice, idreason, idpriority, idsource, idperson_creator, entry_date, os_number, label, serial_number, idperson_juridical,expire_date, idattendance_way, idperson_owner, idstatus)
        //                      values($code_request, '" . $subject . "', '" . $description . "', $type, $item, $service, $reason, $idpriority,  $source,     $idperson_creator,         $date , '" . $osnumber . "','" . $tag . "','" . $serial_number . "',$idjuridical,'" . $expiration_date . "', $way, $idperson_owner, $idstatus)";
        
        
         if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRequestLog($code_request, $date, $idstatus, $idperson) {
        $vSQL = ($this->database == 'oci8po') ? "insert into hdk_tbrequest_log (cod_request,date_,idstatus,idperson) values ($code_request, to_date('$date','YYYY-MM-DD HH24:MI:SS'), $idstatus, $idperson)" : "insert into hdk_tbrequest_log (cod_request,date,idstatus,idperson) values ($code_request, '$date', $idstatus, $idperson)";
        //die($vSQL);
        $ret = $this->db->Execute($vSQL);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRequestGroup($code_request, $idgroup, $flag, $resp = NULL) {
        $ret = $this->db->Execute("insert into hdk_tbrequest_group (code_request, idgroup, ind_in_charge, resp_origin) values($code_request, $idgroup, '$flag', '$resp')");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRequestGroupResp($code_request, $idgroup, $flag, $resp) {
        $ret = $this->db->Execute("insert into hdk_tbrequest_group (code_request, idgroup, ind_in_charge, resp_origin) values($code_request, $idgroup, $flag, $resp)");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRequestGroupPersonResp($code_request, $id_person, $flag) {
        $ret = $this->db->Execute("insert into hdk_tbrequest_group (code_request, idperson, ind_in_charge) values($code_request, $idperson, $flag)");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getGroupUser($id) {
        $ret = $this->select("SELECT  grupo.idgroup as idgroup FROM hdk_tbgroup grupo, hdk_tbgroup_has_person usugrupo WHERE usugrupo.idgroup = grupo.idgroup
                            AND usugrupo.idperson = $id ORDER By grupo.level ASC");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getCode() {
    	if($this->database == 'oci8po'){
    		$vSql = "SELECT cod_request, cod_month FROM hdk_tbrequest_code WHERE COD_MONTH = " . date("Ym");
    	}elseif($this->database == 'mysqlt'){
    		$vSql = "SELECT cod_request, cod_month FROM hdk_tbrequest_code WHERE COD_MONTH = " . date("Ym");
    	}
		
        $ret = $this->select($vSql);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function countGetCode() {
        $ret = $this->select("SELECT count(COD_REQUEST) as total FROM hdk_tbrequest_code WHERE COD_MONTH = " . date("Ym"));
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function createCode($COD_SOLICITACAO) {
        $vSql = ($this->database == 'oci8po') ? "insert into hdk_tbrequest_code(
						cod_request
						,cod_month
						) values (" .
            ($COD_SOLICITACAO + 1) . ", " .
            date("Ym") . ")" : "insert into hdk_tbrequest_code(
						cod_request
						,cod_month
						) values (" .
            ($COD_SOLICITACAO + 1) . ", " .
            date("Ym") . ")";
        //die($vSql);
        $ret = $this->db->Execute($vSql);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function lastCode() {
        $ret = $this->select("select max(code_request) as code_request from hdk_tbrequest");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function increaseCode($COD_SOLICITACAO) {
        $ret = $this->db->Execute("UPDATE hdk_tbrequest_code SET
					cod_request = " . ($COD_SOLICITACAO + 1) . "
					WHERE
					cod_month = " . date("Ym"));
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getAnalyst($COD_USUARIO_ANASLITA) {
        $ret = $this->select("SELECT name FROM tb_person WHERE idperson = $COD_USUARIO_ANALISTA ");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function checksVipUser($COD_USUARIO) {
        $ret = $this->select("select count(idperson) as rec_count from tbperson where idperson = $COD_USUARIO and user_vip = 'Y'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function checksVipPriority() {
        $ret = $this->select("select count(idpriority) as rec_count, idpriority from hdk_tbpriority where VIP = 1 group by idpriority");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
        ;
    }

    public function getServPriority($COD_SERVICO) {
        $ret = $this->select("SELECT idpriority FROM hdk_tbcore_service WHERE idservice = $COD_SERVICO");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getDefaultPriority() {
        if ($this->database == 'oci8po') {
            $ret = $this->select("SELECT idpriority FROM hdk_tbpriority WHERE default_ = 1 AND status = 'A'");
        }
        else
        {
            $ret = $this->select("SELECT idpriority FROM hdk_tbpriority WHERE `default` = 1 AND `status` = 'A'");
        }
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRequestDates($COD_SOLICITACAO, $dataO, $dataF) {
        $ret = $this->db->Excute("insert into hdk_tbrequest_dates (code_request, finish_date, opening_date) values($COD_SOLICITACAO, '$dataF', '$dataO') ");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRequestTimes($COD_SOLICITACAO, $MIN_TEMPO_ABERTURA, $MIN_TEMPO_ENCERRAMENTO, $MIN_TEMPO_ATENDIMENTO) {

        $vSql = "insert into hdk_tbrequest_times (code_request, min_opening_time, min_closure_time, min_attendance_time)
            values ($COD_SOLICITACAO, $MIN_TEMPO_ABERTURA, $MIN_TEMPO_ENCERRAMENTO, $MIN_TEMPO_ATENDIMENTO)";

        $ret = $this->db->Execute($vSql);
        if (!$ret) {
            $sError = $vSql. " Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
	
	public function insertRequestTimesNew($CODE_REQUEST, $MIN_OPENING_TIME = 0, $MIN_ATTENDANCE_TIME = 0, $MIN_EXPENDED_TIME = 0, $MIN_TELEPHONE_TIME = 0, $MIN_CLOSURE_TIME = 0) {
        $ret = $this->db->Execute("insert into hdk_tbrequest_times
									            (CODE_REQUEST,
									             MIN_OPENING_TIME,
									             MIN_ATTENDANCE_TIME,
									             MIN_EXPENDED_TIME,
										     	 MIN_TELEPHONE_TIME,
										    	 MIN_CLOSURE_TIME)
									values ($CODE_REQUEST,
									        $MIN_OPENING_TIME,
									        $MIN_ATTENDANCE_TIME,
									        $MIN_EXPENDED_TIME,
									        $MIN_TELEPHONE_TIME,
										$MIN_CLOSURE_TIME)");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRequestDate($code_request) {
        $ret = $this->db->Execute("insert into hdk_tbrequest_dates (code_request) values ($code_request)");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }	

    public function updateRequest_in_Group($COD_GRUPO, $COD_SOLICITACAO) {
        $ret = $this->db->Execute("update hdk_tbrequest set code_group= $COD_GRUPO where code_request=$COD_SOLICITACAO");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function updateRequestGroupInd($COD_SOLICITACAO) {
        $ret = $this->db->Execute("UPDATE hdk_tbrequest_group SET ind_in_charge = 0 WHERE code_request = $COD_SOLICITACAO");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function updateRequestGroupInd1($COD_RESP, $COD_SOLICITACAO) {
        $ret = $this->db->Execute("UPDATE hdk_tbrequest_group SET ind_in_charge = 1 WHERE idperson = '.$COD_RESP.' AND code_request = '.$COD_SOLICITACAO");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function updateRequestGroupInd2($COD_RESP, $COD_SOLICITACAO) {
        $ret = $this->db->Execute("UPDATE hdk_tbrequest_group SET ind_in_charge = 1 WHERE idgroup = '.$COD_RESP.' AND code_request = '.$COD_SOLICITACAO");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertRequestCharge($COD_SOLICITACAO, $ID, $TIPO, $IND_IN_CHARGE) {
        $ret = $this->db->Execute("insert into hdk_tbrequest_in_charge (code_request, id_in_charge, type, ind_in_charge) values ('$COD_SOLICITACAO',$ID, '$TIPO', '$IND_IN_CHARGE')");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function checkGroup($ID) {
        $ret = $this->select("select hdk_tbgroup.idgroup from hdk_tbgroup, hdk_tbgroup_has_person as gp where hdk_tbgroup.idgroup=gp.idgroup
                            and gp.idperson= $ID order by hdk_tbgroup.level");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getGroupFirstLevel() {
        if ($this->database == 'oci8po') {
           $ret = $this->select("select idgroup from hdk_tbgroup where level= 1 and rownum = 1 ORDER BY idgroup ");
        }
        else
        {
            $ret = $this->select("select idgroup from hdk_tbgroup where level= 1 ORDER BY idgroup LIMIT 1");
        }
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getGruporesponsavel($COD_SERVICO) {
        $ret = $this->select('SELECT idgroup FROM hdk_tbgroup_has_service WHERE idservice = $COD_SERVICO');
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getGrupos($match) {
        $ret = $this->select("SELECT grp.idgroup, per.name FROM hdk_tbgroup grp, tbperson per where grp.idperson = per.idperson AND grp.status = 'A'  $match ORDER BY name");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getGroupsAttendance($idperson) {
        $ret = $this->select("SELECT g.idgroup 
                                FROM hdk_tbgroup_has_person ug
                                LEFT JOIN hdk_tbgroup g ON (ug.idgroup = g.idgroup)
                                WHERE ug.idperson = $idperson 
                                ORDER BY g.level");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getAnalista($COD_SOLICTACAO) {
        $ret = $this->select("SELECT idperson, idgroup 
                                FROM hdk_tbrequest_group 
                                WHERE code_request ='$COD_SOLICITACAO'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectPriorities() {
        $ret =  $this->select("select idpriority, name from hdk_tbpriority");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectApprovalRules($COD_ITEM, $COD_SERVICO) {
        $ret = $this->select("select idapproval, idperson from hdk_approval_rule where iditem=$COD_ITEM and idservice=$COD_SERVICO order by order");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function countApprovalRules($COD_ITEM, $COD_SERVICO) {
        $ret = $this->select("select count(idapproval) as  total from hdk_approval_rule where iditem=$COD_ITEM and idservice=$COD_SERVICO order by order");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRecalcularPrazo($codItem, $codServico) {
        $ret = $this->select("SELECT fl_recalculate FROM hdk_approval_rule WHERE iditem = $codItem AND idservice = $codServico and  order<=1");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function countRecalcularPrazo($codItem, $codServico) {
        $ret = $this->select("SELECT count(idapproval) as total FROM hdk_approval_rule WHERE iditem = $codItem AND idservice = $codServico and  order<=1");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertApproval($values) {
        $ret = $this->db->Execute('INSERT INTO hdk_tbrequest_approval (idapproval, code_solicitacao, order, idperson, fl_recalculate) ' .
                                        'VALUES ' . join(',', $values));
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function updateRequestAttach($COD_SOLICITACAO, $COD_ANEXO) {
        $ret = $this->db->Execute("UPDATE hdk_tbrequest_attachment SET
                        			code_request = '$COD_SOLICITACAO'
						WHERE idrequest_attachment = $COD_ANEXO");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertNote($COD_SOLICITACAO, $COD_USUARIO, $DES_APONTAMENTO, $data, $COD_TIPO_APONTAMENTO, $MIN_TEMPO_ABERTURA, $HOR_INICIAL, $HOR_FINAL, $data, $IP, $tipohora) {
        $vSQL = ($this->database == 'oci8po') ? "INSERT INTO hdk_tbnote
                   (code_request
                   ,idperson
                   ,description
                   ,entry_date
                   ,idtype
                   ,minutes
                   ,start_hour
                   ,finish_hour
                   ,execution_date
                   ,ip_adress
                   ,hour_type)
                                   VALUES(
                    ' $COD_SOLICITACAO'
                    , $COD_USUARIO
                    ,'$DES_APONTAMENTO'
                    ,sysdate
                    , $COD_TIPO_APONTAMENTO
                    , $MIN_TEMPO_ABERTURA
                    ,'$HOR_INICIAL'
                    ,'$HOR_FINAL'
                    ,sysdate
                    ,'$IP'
                                        ,$tipohora)" : "INSERT INTO hdk_tbnote
				   (code_request
				   ,idperson
				   ,description
				   ,entry_date
				   ,idtype
				   ,minutes
				   ,start_hour
				   ,finish_hour
				   ,execution_date
				   ,ip_adress
				   ,hour_type)
                                   VALUES(
					' $COD_SOLICITACAO'
					, $COD_USUARIO
					,'$DES_APONTAMENTO'
					,now()
					, $COD_TIPO_APONTAMENTO
					, $MIN_TEMPO_ABERTURA
					,'$HOR_INICIAL'
					,'$HOR_FINAL'
					,now()
					,'$IP'
                                        ,'$tipohora')";
       // die($vSQL);
        $ret = $this->db->Execute($vSQL);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
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
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['idperson'];
    }
	
	public function getDefaultArea() {
        if ($this->database == 'oci8po') {
            $ret = $this->db->Execute("SELECT idarea FROM hdk_tbcore_area WHERE default_ = 1");
        }
        else
        {
            $ret = $this->db->Execute("SELECT idarea FROM hdk_tbcore_area WHERE `default` = 1");
        }
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['idarea'];
    }

}

?>
