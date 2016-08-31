<?php
class programs_model extends Model{
    public function insertProgram($name,$controller,$idpc){
        return $this->db->Execute("INSERT into tbprogram (name,controller,idprogramcategory,status) values('$name','$controller','$idpc','A')");
    }
    public function selectProgram($where = NULL, $order = NULL, $limit = NULL){
        return $this->select("SELECT tbp.idprogram, tbp.name, tbp.controller, tbm.name as module, tbp.status, tbtp.name as category from tbprogram as tbp, tbmodule as tbm, tbprogramcategory as tbtp where tbtp.idmodule = tbm.idmodule and tbtp.idprogramcategory = tbp.idprogramcategory $where $order $limit");
    }
    public function countProgram($where = NULL, $order = NULL, $limit = NULL){
        $sel = $this->select("SELECT count(idprogram) as total from tbprogram $where $order $limit");
        return $sel;
    }
    public function selectModules(){
        $sel = $this->select("SELECT idmodule, name from tbmodule");
        return $sel;
    }
    public function selectCategory($id){
        return $this->select("Select idprogramcategory, name from tbprogramcategory where idmodule = $id");
    }
    public function countCategory($id){
        return $this->select("SELECT count(idprogramcategory) from tbprogramcategory where idprogramcategory = $id");
    }
    public function programDeactivate($id){
       return $this->db->Execute("UPDATE tbprogram set status = 'N' where idprogram in ($id)");  
    }
    public function programActivate($id){
       return $this->db->Execute("UPDATE tbprogram set status = 'A' where idprogram in ($id)");  
    }
    public function categoryInsert($name,$module){
       return $this->db->Execute("insert into tbprogramcategory (name,idmodule) values ('$name',$module)");
    }
    public function lastIdCategory(){
      $ret = $this->db->Execute("select max(idprogramcategory) as last from tbprogramcategory");
      return $ret->fields['last'];
    }
    public function selectProgramData($id){
        return $this->db->Execute("select name, controller, idprogramcategory from tbprogram where idprogram='$id'");
    }
} 
?>
