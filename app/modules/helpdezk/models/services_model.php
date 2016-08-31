<?php
class services_model extends Model{
    public function selectAreaItens(){
       return $this->select("select tba.idarea as area, tba.name as area_name, tbt.idtype as type, tbt.status as type_status, tba.status as area_status,
       tbt.name as type_name, tbt.idarea as area_pai from hdk_tbcore_area as tba, hdk_tbcore_type as tbt
       where tba.idarea = tbt.idarea");
    }
    public function selectItens($id){
       return $this->select("select tbi.iditem as item, tbi.name as item_name, tbt.idtype as type, tbi.status as item_status, tbi.idtype as type_pai, tbt.name as type_name from hdk_tbcore_item as tbi, hdk_tbcore_type as tbt where tbt.idtype = '$id' and tbt.idtype = tbi.idtype");
    }
    public function selectTypeName($id){
        $ret = $this->select("select name from hdk_tbcore_type where idtype='$id'");
        return $ret->fields['name'];
    }
    public function selectItemName($id){
        $ret = $this->select("select name from hdk_tbcore_item where iditem='$id'");
        return $ret->fields['name'];
    }
    public function selectServices($id){
       return $this->select("select tbs.idservice as service, tbs.name as service_name, tbi.iditem as item, tbs.status as service_status, tbs.iditem as item_pai, tbi.name as item_name from hdk_tbcore_item as tbi, hdk_tbcore_service as tbs where tbi.iditem = '$id' and tbi.iditem = tbs.iditem");
    }
    public function selectService($where = NULL, $order = NULL, $limit = NULL){
        return $this->select("SELECT idservice,iditem,idpriority,name,status,selected,classify,time_attendance,hours_attendance,days_attendance,
                ind_hours_minutes from hdk_tbcore_service $where $order $limit");
    }
    public function insertItem($name,$default,$status,$classify,$idtype2){
        return $this->db->Execute("insert into hdk_tbcore_item (name,selected,status,classify,idtype) values ('$name','$default','$status','$classify','$idtype2')");
    }
    public function selectGroups(){
        return $this->select("select idgroup,name from hdk_tbgroup");
    }
    public function selectPriority(){
        return $this->select("select idpriority,name from hdk_tbpriority");
    }
    public function serviceInsert($name2,$vardefault,$availableitem,$classifyitem,$iditem2,$priority,$group,$time,$days,$limit_time){
        return $this->db->Execute("insert into hdk_tbcore_service (iditem,idpriority,name,status,selected,classify,hours_attendance,days_attendance,ind_hours_minutes) values ('$iditem2','$priority','$name2','$availableitem','$vardefault','$classifyitem','$limit_time','$days','$time')");
    }
    public function selectAreas(){
        return $this->select("select name, idarea, status from hdk_tbcore_area order by name");
    }
    public function selectAvailabeAreas(){
        return $this->select("select name, idarea from hdk_tbcore_area where status = 'A' order by name");
    }
    public function areaInsert($name){
        return $this->db->Execute("insert into hdk_tbcore_area (name) values ('$name')");
    }
    public function typeInsert($name, $vardefault, $status, $classify, $area){
        return $this->db->Execute("insert into hdk_tbcore_type (name,selected,status,classify,idarea) values ('$name', '$vardefault', '$status', '$classify', '$area')");
    }
    public function selectItemEdit($id){
        return $this->select("select name, status, selected, classify, idarea from hdk_tbcore_type where idtype= '$id'");
    }
}
?>
