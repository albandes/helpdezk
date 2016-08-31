<?php
class location_model extends Model {
    
    public function insertLocation($value){
        $ret = $this->db->Execute("insert into tblocation (name) values ('$value')");
       if(!$ret) {
                $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
                $this->error($sError);
            }
            return $ret;
        }
}
?>
