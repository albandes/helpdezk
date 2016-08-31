<?php
class requestinsert_model extends Model{
    public function selectSource(){
        return $this->select("select idsource, name, icon from hdk_tbsource");
    }
    public function selectArea(){
        return $this->select("select idarea, name from hdk_tbcore_area where status = 'A'");
    }
    public function selectType($area){
        return $this->select("select idtype, name, selected from hdk_tbcore_type where idarea = '$area' and status = 'A'");
    }
    public function selectItem($type){
        return $this->select("select iditem, name, selected from hdk_tbcore_item where idtype = '$type' and status = 'A'");
    }
    public function selectService($item){
        return $this->select("select idservice, name, selected from hdk_tbcore_service where iditem = '$item' and status = 'A'");
    }
    public function selectReason($type){
        return $this->select("select idreason , reason from hdk_tbreason where idservice = '$type' and status = 'A'");
    }
    public function selectWay(){
        return $this->select("select idattendanceway, way from hdk_tbattendance_way");
    }
    //nesta funcao teremos 2 campos null no final, que sao os que dizem se vamos inserir o código da solicitacao ou se o banco vai autoincrementar, isso vai depende da variavel de sessao testada la no controller
   public function insertRequest($idperson_creator, $source, $date, $type, $item, $service, $reason, $way, $subject, $description,   $osnumber, $idpriority, $tag, $serial_number, $idjuridical,$expiration_date, $idperson_owner, $idstatus, $campocodigo = null, $code_request=null){
       die("insert into hdk_tbrequest  ($campocodigo `subject`, description, idtype, iditem, idservice, idreason, idpriority, idsource, idperson_creator, entry_date, os_number, label, serial_number, idperson_juridical,expire_date, idattendance_way, idperson_owner, idstatus) 
                                 values($code_request '".$subject."', '".$description."', $type, $item, $service, $reason, $idpriority,  $source,     $idperson_creator,         '".$date."', '".$osnumber."','".$tag."','".$serial_number."',$idjuridical,$expiration_date, $way, $idperson_owner, $idstatus)");
//       if(!$ret) {
//            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
//            $this->error($sError);
//        }
//        return $ret;  
   }
   public function insertRequestLog($code_request, $date, $idstatus, $idperson){
       $ret = $this->db->Execute("insert into hdk_tbrequest_log (cod_request,date,idstatus,idperson) values ($code_request, '$date', $idstatus, $idperson)");
       if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
   }
   public function insertRequestGroup($code_request, $idgroup, $flag, $resp = NULL){
       $ret = $this->db->Execute("insert into hdk_tbrequest_group (code_request, idgroup, ind_in_charge, resp_origin) values($code_request, $idgroup, '$flag', '$resp')");       
       if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
   }
   public function insertRequestGroupResp($code_request, $idgroup,$flag, $resp){
       $ret = $this->db->Execute("insert into hdk_tbrequest_group (code_request, idgroup, ind_in_charge, resp_origin) values($code_request, $idgroup, $flag, $resp)");       
       if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
   }
   public function insertRequestGroupPersonResp($code_request, $id_person,$flag){
       $ret = $this->db->Execute("insert into hdk_tbrequest_group (code_request, idperson, ind_in_charge) values($code_request, $idperson, $flag)");
       if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
   }
   public function getGroupUser($id){
       return $this->select("SELECT  grupo.idgroup as idgroup FROM hdk_tbgroup grupo, hdk_tbgroup_has_person usugrupo WHERE usugrupo.idgroup = grupo.idgroup
                            AND usugrupo.idperson = $id ORDER By grupo.level ASC");
   }
   public function getCode(){
        return $this->select("SELECT COD_REQUEST, COD_MONTH FROM hdk_tbrequest_code WHERE COD_MONTH = ".date("Ym"));
        
    }
    public function countGetCode(){
        $ret=$this->select("SELECT count(COD_REQUEST) as total FROM hdk_tbrequest_code WHERE COD_MONTH = ".date("Ym"));
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
    }
    public function createCode($COD_SOLICITACAO){
        $ret = $this->db->Execute("insert into hdk_tbrequest_code(
						cod_request
						,cod_month
						) values (".
						($COD_SOLICITACAO+1) . ", ".
						date("Ym") . ")");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
    }
    public function lastCode(){
        $ret = $this->select("select max(code_request) as code_request from hdk_tbrequest");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
    }
    public function increaseCode($COD_SOLICITACAO){
        $ret = $this->db->Execute("UPDATE hdk_tbrequest_code SET
					cod_request = ".($COD_SOLICITACAO+1)."
					WHERE
					cod_month = ".date("Ym"));
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
    }
    public function getAnalyst($COD_USUARIO_ANASLITA){
        $ret=$this->select("SELECT name FROM tb_person WHERE idperson = $COD_USUARIO_ANALISTA ");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
    }
    public function checksVipUser($COD_USUARIO){
        $ret=$this->select("select count(idperson) as rec_count from tbperson where idperson = $COD_USUARIO and user_vip = 'Y'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
    }
    
    public function checksVipPriority(){
        $ret=$this->select("select count(idpriority) as rec_count from hdk_tbpriority where VIP = 1");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }
    public function getServPriority($COD_SERVICO){
        $ret=$this->select("SELECT idpriority FROM hdk_tbcore_service WHERE idservice = $COD_SERVICO");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
    }
    public function getDefaultPriority(){
        $ret=$this->select("SELECT idpriority FROM hdk_tbpriority WHERE status = 'A' ORDER by default DESC, order");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
    }
    public function insertRequestDates($COD_SOLICITACAO, $dataO, $dataF){
        $ret = $this->db->Excute("insert into hdk_tbrequest_dates (code_request, finish_date, opening_date) values($COD_SOLICITACAO, '$dataF', '$dataO') ");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
    }
    public function insertRequestTimes($COD_SOLICITACAO, $MIN_TEMPO_ABERTURA, $MIN_TEMPO_ENCERRAMENTO, $MIN_TEMPO_ATENDIMENTO){
        $ret = $this->db->Execute("insert into hdk_tbrequest_times (code_request, min_opening_time, min_closure_time, min_attendance_time)
            values ($COD_SOLICITACAO, $MIN_TEMPO_ABERTURA, $MIN_TEMPO_ENCERRAMENTO, $MIN_TEMPO_ATENDIMENTO)");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
    }
    public function updateRequest_in_Group($COD_GRUPO, $COD_SOLICITACAO){
        $ret = $this->db->Execute("update hdk_tbrequest set code_group= $COD_GRUPO where code_request=$COD_SOLICITACAO");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;  
    }
    public function updateRequestGroupInd($COD_SOLICITACAO){
        $ret = $this->db->Execute("UPDATE hdk_tbrequest_group SET ind_in_charge = 0 WHERE code_request = $COD_SOLICITACAO");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;          
    }
    public function updateRequestGroupInd1($COD_RESP, $COD_SOLICITACAO){
        $ret = $this->db->Execute("UPDATE hdk_tbrequest_group SET ind_in_charge = 1 WHERE idperson = '.$COD_RESP.' AND code_request = '.$COD_SOLICITACAO");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;          
    }
    public function updateRequestGroupInd2($COD_RESP, $COD_SOLICITACAO){
        $ret = $this->db->Execute("UPDATE hdk_tbrequest_group SET ind_in_charge = 1 WHERE idgroup = '.$COD_RESP.' AND code_request = '.$COD_SOLICITACAO");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;                
    }
    public function insertRequestCharge($COD_SOLICITACAO, $ID, $TIPO, $IND_IN_CHARGE){
        $ret = $this->db->Execute("insert into hdk_tbrequest_in_charge (code_request, id_in_charge, type, ind_in_charge) values ('$COD_SOLICITACAO',$ID, '$TIPO', '$IND_IN_CHARGE')");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;        
    }
    public function checkGroup($ID){
       $ret = $this->select("select hdk_tbgroup.idgroup from hdk_tbgroup, hdk_tbgroup_has_person as gp where hdk_tbgroup.idgroup=gp.idgroup
                            and gp.idperson= $ID order by hdk_tbgroup.level");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;        
    }
    public function getGroupFirstLevel(){
        $ret = $this->select("select idgroup from hdk_tbgroup where level= 1 ORDER BY idgroup LIMIT 1");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;        
    }
    public function getGruporesponsavel($COD_SERVICO){
        return $this->select('SELECT idgroup FROM hdk_tbgroup_has_service WHERE idservice = $COD_SERVICO');
    }
    public function getGrupos($match){
        return $this->select("SELECT idgroup, name FROM hdk_tbgroup where status='A' $match ORDER BY name");
    }
    public function getGroupsAttendance($idperson){
        return $this->select("SELECT g.idgroup 
                                FROM hdk_tbgroup_has_person ug
                                LEFT JOIN hdk_tbgroup g ON (ug.idgroup = g.idgroup)
                                WHERE ug.idperson = $idperson 
                                ORDER BY g.level");
    }
    public function getAnalista($COD_SOLICTACAO){
        return $this->select("SELECT idperson, idgroup 
                                FROM hdk_tbrequest_group 
                                WHERE code_request ='$COD_SOLICITACAO'");
    }
    public function selectPriorities(){
        return $this->select("select idpriority, name from hdk_tbpriority");
    }
    public function selectApprovalRules($COD_ITEM, $COD_SERVICO){
        return $this->select("select idapproval, idperson from hdk_approval_rule where iditem=$COD_ITEM and idservice=$COD_SERVICO order by order");
    }
    public function countApprovalRules($COD_ITEM, $COD_SERVICO){
        return $this->select("select count(idapproval) as  total from hdk_approval_rule where iditem=$COD_ITEM and idservice=$COD_SERVICO order by order");
    }
    
    public function getRecalcularPrazo($codItem, $codServico){
        return $this->select("SELECT fl_recalculate FROM hdk_approval_rule WHERE iditem = $codItem AND idservice = $codServico and  and order<=1");
    }
    public function countRecalcularPrazo($codItem, $codServico){
        return $this->select("SELECT count(idapproval) as total FROM hdk_approval_rule WHERE iditem = $codItem AND idservice = $codServico and  and order<=1");
    }
    public function insertApproval($values){
        return $this->db->Execute('INSERT INTO hdk_tbrequest_approval (idapproval, code_solicitacao, order, idperson, fl_recalculate) '.
           'VALUES ' . join	(',', $values));
    }
    public function updateRequestAttach($COD_SOLICITACAO, $COD_ANEXO){
        return $this->db->Execute("UPDATE hdk_tbrequest_attachment SET
						code_request = '$COD_SOLICITACAO'
						WHERE idrequest_attachment = $COD_ANEXO");
    }
    public function insertNote($COD_SOLICITACAO, $COD_USUARIO, $DES_APONTAMENTO, $data, $COD_TIPO_APONTAMENTO, $MIN_TEMPO_ABERTURA,$HOR_INICIAL,$HOR_FINAL,$data,$IP, $tipohora){
        return $this->db->Execute("INSERT INTO hdk_tbnote
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
                                        ,'$tipohora')");
    }
}
?>
