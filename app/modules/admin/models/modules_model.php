<?php
class modules_model extends Model{
    public function insertModule($var){
        return $this->db->Execute("INSERT into tbmodule (name) values('$var')");
    }
    public function selectModule($where = NULL, $order = NULL, $limit = NULL){

        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
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
        return $this->select("SELECT name from tbmodule where idmodule ='$id'");
    }
    public function updateModule($id,$name){
        return $this->db->Execute("UPDATE tbmodule set name = '$name' where idmodule = '$id'");
    }
    public function moduleDeactivate($id){
       return $this->db->Execute("UPDATE tbmodule set status = 'N' where idmodule in ($id)");  
    }
    public function moduleActivate($id){
       return $this->db->Execute("UPDATE tbmodule set status = 'A' where idmodule in ($id)");  
    }
    public function checkName($name){
        return $this->select("select idmodule from tbmodule where name='$name'");
    }
}
?>
