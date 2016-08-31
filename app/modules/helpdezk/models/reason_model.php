<?php
class reason_model extends Model{
    public function selectReason($where = NULL, $order = NULL, $limit = NULL){
        return $this->select("select tbr.idreason, tbr.reason, tbr.status, tbt.name as type from hdk_tbreason as tbr, hdk_tbcore_type as tbt where tbt.idtype = tbr.idtype $where $order $limit");
    }
    public function countReason($where = NULL, $limit = NULL){
        $sel = $this->select("SELECT count(idreason) as total from hdk_tbreason $where $limit");
        return $sel;
    }
    public function selectType(){
        return $this->select("select idtype, name from hdk_tbcore_type where status = 'A'");
    }
    public function insertReason($reason,$type,$available){
        return $this->db->Execute("insert into hdk_tbreason (idtype,reason,status) values ('$type','$reason','$available')");
    }
}
?>
