<?php

class operatoraux_model extends Model {

    public function selectAreaItens() {
        return $this->select("select tba.idarea as area, tba.name as area_name, tbt.idtype as type, tbt.status as type_status, tba.status as area_status,
       tbt.name as type_name, tbt.idarea as area_pai from hdk_tbcore_area as tba, hdk_tbcore_type as tbt
       where tba.idarea = tbt.idarea");
    }

    public function selectItens($id) {
        return $this->select("select tbi.iditem as item, tbi.name as item_name, tbt.idtype as type, tbi.status as item_status, tbi.idtype as type_pai, tbt.name as type_name from hdk_tbcore_item as tbi, hdk_tbcore_type as tbt where tbt.idtype = '$id' and tbt.idtype = tbi.idtype");
    }

    public function selectTypeName($id) {
        $ret = $this->select("select name from hdk_tbcore_type where idtype='$id'");
        return $ret->fields['name'];
    }

    public function selectItemName($id) {
        $ret = $this->select("select name from hdk_tbcore_item where iditem='$id'");
        return $ret->fields['name'];
    }

    public function selectServices($id) {
        return $this->select("select tbs.idservice as service, tbs.name as service_name, tbi.iditem as item, tbs.status as service_status, tbs.iditem as item_pai, tbi.name as item_name from hdk_tbcore_item as tbi, hdk_tbcore_service as tbs where tbi.iditem = '$id' and tbi.iditem = tbs.iditem");
    }

    public function selectService($where = NULL, $order = NULL, $limit = NULL) {
        return $this->select("SELECT idservice,iditem,idpriority,name,status,selected,classify,time_attendance,hours_attendance,days_attendance,
                ind_hours_minutes from hdk_tbcore_service $where $order $limit");
    }

    public function insertItem($name, $default, $status, $classify, $idtype2) {
        return $this->db->Execute("insert into hdk_tbcore_item (name,selected,status,classify,idtype) values ('$name','$default','$status','$classify','$idtype2')");
    }

    public function selectGroups() {
        return $this->select("select idgroup,name from hdk_tbgroup");
    }

    public function selectPriority() {
        return $this->select("select idpriority,name from hdk_tbpriority");
    }

    public function serviceInsert($name2, $vardefault, $availableitem, $classifyitem, $iditem2, $priority, $time, $days, $limit_time) {
        return $this->db->Execute("insert into hdk_tbcore_service (iditem,idpriority,name,status,selected,classify,hours_attendance,days_attendance,ind_hours_minutes) values ('$iditem2','$priority','$name2','$availableitem','$vardefault','$classifyitem','$limit_time','$days','$time')");
    }

    public function selectAreas() {
        return $this->select("select name, idarea, status from hdk_tbcore_area order by name");
    }

    public function selectAvailabeAreas() {
        return $this->select("select name, idarea from hdk_tbcore_area where status = 'A' order by name");
    }

    public function areaInsert($name) {
        return $this->db->Execute("insert into hdk_tbcore_area (name) values ('$name')");
    }

    public function typeInsert($name, $vardefault, $status, $classify, $area) {
        return $this->db->Execute("insert into hdk_tbcore_type (name,selected,status,classify,idarea) values ('$name', '$vardefault', '$status', '$classify', '$area')");
    }

    public function selectTypeEdit($id) {
        return $this->select("select name, status, selected, classify, idarea from hdk_tbcore_type where idtype= '$id'");
    }

    public function selectAreaEdit($id) {
        return $this->select("select name from hdk_tbcore_area where idarea= '$id'");
    }

    public function selectItemEdit($id) {
        return $this->select("select name, status, selected, classify from hdk_tbcore_item where iditem= '$id'");
    }

    public function selectServiceEdit($id) {
        return $this->select("select name, status, selected, classify, time_attendance, hours_attendance, days_attendance, ind_hours_minutes  from hdk_tbcore_service where idservice= '$id'");
    }

    public function serviceGroupInsert($idservice, $group) {
        return $this->db->Execute("insert into hdk_tbgroup_has_service (idgroup,idservice) values ('$group','$idservice')");
    }

    public function selectMax() {
        return $this->db->Execute("select max(idservice) as last from hdk_tbcore_service");
    }

    public function selectMaxType() {
        return $this->db->Execute("select max(idtype) as last from hdk_tbcore_type");
    }

    public function selectMaxItem() {
        return $this->db->Execute("select max(iditem) as last from hdk_tbcore_item");
    }

    public function selectServiceGroup($id) {
        $ret = $this->db->Execute("select idgroup from hdk_tbgroup_has_service where idservice='$id'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectServicePriority($id) {
        return $this->db->Execute("select idpriority from hdk_tbcore_service where idservice = '$id'");
    }

    public function updateType($id, $name, $area, $vardefault, $status, $classify) {
        return $this->db->Execute("update hdk_tbcore_type set name = '$name', idarea = '$area', selected='$vardefault', status = '$status', classify = '$classify' where idtype = '$id'");
    }

    public function updateItem($id, $name, $vardefault, $status, $classify) {
        return $this->db->Execute("update hdk_tbcore_item set name = '$name', selected='$vardefault', status = '$status', classify = '$classify' where iditem = '$id'");
    }

    public function updateArea($id, $name) {
        return $this->db->Execute("update hdk_tbcore_area set name = '$name' where idarea = '$id'");
    }

    public function selectPrevGroup($id) {
        return $this->select("select idgroup from hdk_tbgroup_has_service where idservice='$id'");
    }

    public function updateServiceGroup($id, $group) {
        return $this->db->Execute("update hdk_tbgroup_has_service set idgroup='$group' where idservice = '$id'");
    }

    public function updateService($id, $name, $vardefault, $availableitem, $classifyitem, $priority, $time, $days, $limit_time) {
        return $this->db->Execute("update hdk_tbcore_service set name='$name', selected='$vardefault', status= '$availableitem', classify='$classifyitem', idpriority='$priority',hours_attendance = '$limit_time',days_attendance = '$days',ind_hours_minutes='$time' where idservice='$id'");
    }

    public function selectItem($id) {
        return $this->db->Execute("select iditem from hdk_tbcore_service where idservice = '$id'");
    }

    public function selectType($id) {
        return $this->db->Execute("select idtype from hdk_tbcore_item where iditem = '$id'");
    }

    public function areaChangeStatus($id, $check) {
        return $this->db->Execute("update hdk_tbcore_area set status = '$check' where idarea = '$id'");
    }

    public function typeChangeStatus($id, $check) {
        return $this->db->Execute("update hdk_tbcore_type set status = '$check' where idtype = '$id'");
    }

    public function itemChangeStatus($id, $check) {
        return $this->db->Execute("update hdk_tbcore_item set status = '$check' where iditem = '$id'");
    }

    public function serviceChangeStatus($id, $check) {
        return $this->db->Execute("update hdk_tbcore_service set status = '$check' where idservice = '$id'");
    }

    public function updateDefaults() {
        return $this->db->Execute("update hdk_tbcore_type as tp set tp.default = 0 where tp.default = 1");
    }

    public function getTypeFromAreas($id) {
        return $this->select("select tbt.idtype as type, tbt.status as type_status,
       tbt.name as type_name, tbt.idarea from hdk_tbcore_type as tbt where idarea = '$id' order by type_name ASC");
    }

}

?>
