<?php

if(class_exists('Model')) {
    class DynamicModule_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicModule_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicModule_model extends apiModel {}
}

class modules_model extends DynamicModule_model {
    public function insertModule($name, $path, $smarty,$prefix,$default=NULL){
        $fcond = isset($default) ? ', defaultmodule' : '';
        $vcond = isset($default) ? ", '$default'" : '';
        $query = "INSERT INTO tbmodule (name, path, smarty, tableprefix $fcond) 
                    VALUES('$name', '$path', '$smarty', '$prefix' $vcond)";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }else{
            return $ret;
        }        
    }

    public function selectModule($where = NULL, $order = NULL, $limit = NULL){

        $database = $this->getConfig('db_connect');
        if ($database == 'mysqli') {
            $ret = $this->select("SELECT idmodule, name, status from tbmodule $where $order $limit");
        } elseif ($database == 'oci8po') {
            $limit = str_replace('LIMIT', "", $limit);
            $p     = explode(",", $limit);
            $start = $p[0]+1; 
            $end   = $p[0]+$p[1]; 
            $query =    "
                        SELECT   *
                          FROM   (SELECT                                          
                                        a  .*, ROWNUM rnum
                                    FROM   (  SELECT idmodule, name, status from tbmodule $where $order ) a
                                   WHERE   ROWNUM <= $end)
                         WHERE   rnum >= $start         
                        ";
            $ret = $this->db->Execute($query);
        }
        return $ret;
    }

    public function countModule($where = NULL, $order = NULL, $limit = NULL){
        $sel = $this->select("SELECT count(idmodule) as total from tbmodule $where $order $limit");
        return $sel;
    }
    
    public function deleteModule($where){
        return $this->delete('tbmodule', $where);
    }

    public function selectModuleData($id){
        $query = "SELECT name, path, smarty, headerlogo, tableprefix, defaultmodule from tbmodule where idmodule ='$id'";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
            
        return $ret;
        
    }

    public function updateModule($id,$name,$path,$smarty,$default=NULL){
        $fcond = isset($default) ? ', defaultmodule = ' : '';
        $vcond = isset($default) ? "'$default'" : '';
           
        
        $query = "UPDATE tbmodule 
                     SET name = '$name',
                         path = '$path',
                         smarty = '$smarty'
                         $fcond $vcond
                   WHERE idmodule = '$id'";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }else{
            return $ret;
        }
    }

    public function changeModuleStatus($id,$newStatus){
       return $this->db->Execute("UPDATE tbmodule SET status = '$newStatus' WHERE idmodule in ($id)");
    }

    public function checkName($name){
        return $this->select("select idmodule from tbmodule where name='$name'");
    }

    public function removeDefault(){
        $query = "UPDATE tbmodule SET defaultmodule = NULL WHERE defaultmodule = 'YES'";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }else{
            return $ret;
        }
    }

    public function createConfigTables($prefix){
        $query = "CALL adm_createConfigTables('$prefix')";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }else{
            return $ret;
        }
    }

    public function deleteConfigTables($prefix){
        $dbDelete = $this->getConfig('db_name');
        $query = "CALL adm_deleteTables('$dbDelete','$prefix',@msg)";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }else{
            return $ret;
        }
    }

}

?>
