<?php
class relPessoa_model extends Model{
    /*
    public function selectrelPessoa($sql, $where = NULL, $order = NULL, $limit = NULL){
       return $this->select("$sql $where $order $limit");
        
    }
    */

    public function countModule($where = NULL, $order = NULL, $limit = NULL){
        $sel = $this->select("SELECT count(idmodule) as total from tbmodule $where $order $limit");
        return $sel;
    }
}
?>
