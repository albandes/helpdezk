<?php

if(class_exists('Model')) {
    class DynamicService_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicService_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicService_model extends apiModel {}
}

class service_model extends DynamicService_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');


    }

    public function selectAreaItens() {
        $query = "SELECT tba.idarea as area, tba.name as area_name, tbt.idtype as type, tbt.status as type_status, 
                         tba.status as area_status, tbt.name as type_name, tbt.idarea as area_pai 
                    FROM hdk_tbcore_area tba, hdk_tbcore_type tbt 
                   WHERE tba.idarea = tbt.idarea";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectItens($id) {
        $query = "SELECT tbi.iditem as item, tbi.name as item_name, tbt.idtype as type, 
                          tbi.status as item_status, tbi.idtype as type_pai, tbt.name as type_name 
                    FROM hdk_tbcore_item tbi, hdk_tbcore_type tbt 
                   WHERE tbt.idtype = '$id' 
                     AND tbt.idtype = tbi.idtype 
                ORDER BY item_name ASC";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectTypeName($id) {
        $query = "SELECT name FROM hdk_tbcore_type WHERE idtype='$id'";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret->fields['name'];
    }

    public function selectItemName($id) {
        $query = "SELECT name FROM hdk_tbcore_item WHERE iditem='$id'";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret->fields['name'];
    }

    public function selectServices($id) {
        $query = "SELECT tbs.idservice as service, tbs.name as service_name, tbi.iditem as item, 
                          tbs.status as service_status, tbs.iditem as item_pai, tbi.name as item_name 
                    FROM hdk_tbcore_item tbi, hdk_tbcore_service tbs 
                   WHERE tbi.iditem = '$id' 
                     AND tbi.iditem = tbs.iditem 
                ORDER BY service_name ASC";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectService($where = NULL, $order = NULL, $limit = NULL) {
        return $this->select("SELECT idservice,iditem,idpriority,name,status,selected,classify,time_attendance,hours_attendance,days_attendance,ind_hours_minutes from hdk_tbcore_service $where $order $limit");
    }

    public function insertItem($name, $default, $status, $classify, $idtype2) {
        $query = "INSERT INTO hdk_tbcore_item (name,selected,status,classify,idtype) 
                  VALUES ('$name','$default','$status','$classify','$idtype2')";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectGroups() {
        return $this->select("select idgroup,name from hdk_tbgroup");
    }

    public function selectPriority() {
        return $this->select("select idpriority,name from hdk_tbpriority");
    }

    public function serviceInsert($name2, $vardefault, $availableitem, $classifyitem, $iditem2, $priority, $time, $days, $limit_time) {
        $query = "INSERT INTO hdk_tbcore_service (iditem,idpriority,name,status,selected,classify,hours_attendance,days_attendance,ind_hours_minutes) 
                  VALUES ('$iditem2','$priority','$name2','$availableitem','$vardefault','$classifyitem','$limit_time','$days','$time')";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;

        return $this->db->Execute("insert into hdk_tbcore_service (iditem,idpriority,name,status,selected,classify,hours_attendance,days_attendance,ind_hours_minutes) values ('$iditem2','$priority','$name2','$availableitem','$vardefault','$classifyitem','$limit_time','$days','$time')");
    }

    public function selectAreas() {
        $query = "SELECT name, idarea, status FROM hdk_tbcore_area ORDER BY name";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectAvailabeAreas() {
        return $this->select("select name, idarea from hdk_tbcore_area where status = 'A' order by name");
    }

    public function areaInsert($name,$default='msqli') {
        if ($this->database == 'mysqli') {
            $query = "INSERT INTO hdk_tbcore_area (name,`default`) VALUES ('$name','$default')" ;
        } elseif ($this->database == 'oci8po') {
            $query = "INSERT INTO hdk_tbcore_area (name,default_) VALUES ('$name','$default')";

        }
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function typeInsert($name, $vardefault, $status, $classify, $area) {
        $query = "INSERT INTO hdk_tbcore_type (name,selected,status,classify,idarea) 
                    VALUES ('$name', '$vardefault', '$status', '$classify', '$area')";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectTypeEdit($id) {
        $query = "SELECT name, status, selected, classify, idarea FROM hdk_tbcore_type WHERE idtype= '$id'";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectAreaEdit($id) {
        if ($this->database == 'mysqli') {
            $query = "SELECT name, `default` as def FROM hdk_tbcore_area WHERE idarea= '$id'" ;
        } elseif ($this->database == 'oci8po') {
            $query = "SELECT name, default_ as def FROM hdk_tbcore_area WHERE idarea= '$id'";

        }
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectItemEdit($id) {
        $query = "select name, status, selected, classify, idtype from hdk_tbcore_item where iditem= '$id'";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectServiceEdit($id) {
        $query = "SELECT name, status, selected, classify, time_attendance, hours_attendance, 
                         days_attendance, ind_hours_minutes, idpriority  
                    FROM hdk_tbcore_service 
                   WHERE idservice= '$id'";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function serviceGroupInsert($idservice, $group) {
        $query = "INSERT INTO hdk_tbgroup_has_service (idgroup,idservice) VALUES ('$group','$idservice')";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectMax() {
        $query = "SELECT max(idservice) AS last FROM hdk_tbcore_service";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectMaxType() {
        return $this->db->Execute("select max(idtype) as last from hdk_tbcore_type");
    }

    public function selectMaxItem() {
        return $this->db->Execute("select max(iditem) as last from hdk_tbcore_item");
    }

    public function selectServiceGroup($id) {
        if ($this->database == 'mysqli') {
            $query = "SELECT serv.name as service, serv.idservice, grp.idgroup, grppers.name as groupname, grp.level as lvl 
                        FROM hdk_tbcore_service serv, hdk_tbgroup grp, tbperson grppers, hdk_tbgroup_has_service relat 
                       WHERE relat.idgroup = grp.idgroup 
                         AND serv.idservice = relat.idservice 
                         AND relat.idservice ='$id' 
                         AND grppers.idperson = grp.idperson" ;
        } elseif ($this->database == 'oci8po') {
            $query = "SELECT serv.name as service, serv.idservice, grp.idgroup, grppers.name as groupname, grp.level_ as lvl 
                        FROM hdk_tbcore_service serv, hdk_tbgroup grp, tbperson grppers, hdk_tbgroup_has_service relat 
                       WHERE relat.idgroup = grp.idgroup 
                         AND serv.idservice = relat.idservice 
                         AND relat.idservice ='$id' 
                         AND grppers.idperson = grp.idperson";

        }
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return $this->db->Execute("");
        }
        elseif ($database == 'oci8po') {
            return $this->db->Execute("");
        }

        
    }

    public function selectServicePriority($id) {
        return $this->db->Execute("select idpriority from hdk_tbcore_service where idservice = '$id'");
    }

    public function updateType($id, $name, $area, $vardefault, $status, $classify) {
        $query = "UPDATE hdk_tbcore_type 
                     SET name = '$name', 
                         idarea = '$area', 
                         selected = '$vardefault', 
                         status = '$status', 
                         classify = '$classify' 
                   WHERE idtype = '$id'";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function updateItem($id, $name, $vardefault, $status, $classify) {
        $query = "UPDATE hdk_tbcore_item 
                     SET name = '$name', 
                         selected='$vardefault', 
                         status = '$status', 
                         classify = '$classify' 
                   WHERE iditem = '$id'";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function updateArea($id, $name, $default) {
        if ($this->database == 'mysqli') {
            $query = "UPDATE hdk_tbcore_area SET name = '$name', `default` = '$default' WHERE idarea = '$id'" ;
        } elseif ($this->database == 'oci8po') {
            $query = "UPDATE hdk_tbcore_area SET name = '$name', default_ = '$default' WHERE idarea = '$id'";

        }
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectPrevGroup($id) {
        $query = "SELECT idgroup FROM hdk_tbgroup_has_service WHERE idservice='$id'";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
        return $this->select("select idgroup from hdk_tbgroup_has_service where idservice='$id'");
    }

    public function updateServiceGroup($id, $group) {
        $query = "UPDATE hdk_tbgroup_has_service SET idgroup='$group' WHERE idservice = '$id'";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function updateService($id, $name, $vardefault, $availableitem, $classifyitem, $priority, $time, $days, $limit_time) {
        $query = "UPDATE hdk_tbcore_service 
                     SET name='$name', 
                         selected='$vardefault', 
                         status= '$availableitem', 
                         classify='$classifyitem', 
                         idpriority='$priority',
                         hours_attendance = '$limit_time',
                         days_attendance = '$days',
                         ind_hours_minutes='$time' 
                   WHERE idservice='$id'";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
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
        $query = "UPDATE hdk_tbcore_type SET status = '$check' WHERE idtype = '$id'";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function itemChangeStatus($id, $check) {
        $query = "UPDATE hdk_tbcore_item SET status = '$check' WHERE iditem = '$id'";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function serviceChangeStatus($id, $check) {
        $query = "UPDATE hdk_tbcore_service SET status = '$check' WHERE idservice = '$id'";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function updateDefaults() {
        return $this->db->Execute("update hdk_tbcore_type as tp set tp.default = 0 where tp.default = 1");
    }

    public function getTypeFromAreas($id) {
        $query = "SELECT tbt.idtype as type, tbt.status as type_status,tbt.name as type_name, tbt.idarea 
                    FROM hdk_tbcore_type tbt 
                   WHERE idarea = '$id' 
                ORDER BY type_name ASC";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectAreaType() {
        return $this->select("select idarea,name,idtype from hdk_tbcore_type order by idarea,name");
    }

    public function selectTypeItem() {
        return $this->select("select idtype, name, iditem from hdk_tbcore_item order by idtype, name");
    }

    public function selectItemService() {
        return $this->select("select iditem, name, idservice from hdk_tbcore_service order by iditem, name");
    }

    public function selectPriorityData() {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return $this->select("select idpriority,name,`default` as def,limit_hours,limit_days from hdk_tbpriority order by idpriority");
        }elseif ($database == 'oci8po') {
            return $this->select("select idpriority,name,default_ as def,limit_hours,limit_days from hdk_tbpriority order by idpriority");
        }
    }

    public function selectAreaFromName($name) {
        return $this->select("select idarea from hdk_tbcore_area where name = trim('$name')");
    }

    public function InsertID() {
        return $this->db->Insert_ID( );
    }

    public function clearDefaultArea(){
        if ($this->database == 'mysqli') {
            $query = "UPDATE hdk_tbcore_area tp SET tp.default = 0 WHERE tp.default = '1'" ;
        } elseif ($this->database == 'oci8po') {
            $query = "UPDATE hdk_tbcore_area tp SET tp.default_ = 0 WHERE tp.default_ = '1'";

        }
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function clearDefaultType($area){
        $query = "UPDATE hdk_tbcore_type tp SET tp.selected = 0 WHERE tp.selected = 1 AND idarea = $area";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function clearDefaultItem($type){
        $query = "UPDATE hdk_tbcore_item tp SET tp.selected = 0 WHERE tp.selected = 1 AND idtype = $type";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function getIdTypeByItem($iditem){
        $ret = $this->select("select idtype from hdk_tbcore_item where iditem='$iditem'");
        return $ret->fields['idtype'];
    }

    public function clearDefaultService($item){
        $query = "UPDATE hdk_tbcore_service tp SET tp.selected = 0 WHERE tp.selected = 1 AND iditem = $item";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectAllServices() {
        $query = "SELECT serv.name as service, serv.idservice,
                         item.name as item, item.iditem
                    FROM hdk_tbcore_service serv,
                         hdk_tbcore_item item
                   WHERE serv.iditem = item.iditem";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
        return $this->db->Execute("");
    }

    // Since September 25, 2019
    public function getCoreByService($idservice)
    {
       $sql =   "
                 SELECT
                  a.idpriority,
                  a.iditem,
                  c.idtype,
                  d.idarea
                FROM
                  hdk_tbcore_service a,
                  hdk_tbcore_item b,
                  hdk_tbcore_type c,
                  hdk_tbcore_area d
                WHERE a.iditem = b.iditem
                  AND b.idtype = c.idtype
                  AND c.idarea = d.idarea
                  AND a.idservice = $idservice
                ";
       return $this->db->Execute($sql);
    }
}