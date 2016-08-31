<?php
class category_model extends Model{
    public function insertcategory($var){
        return $this->db->Execute("INSERT into dsh_tbcategory (title) values('$var')");
    }
    public function selectCategory($where = NULL, $order = NULL, $limit = NULL){
        return $this->select("SELECT idcategory, title, status from dsh_tbcategory $where $order $limit");

    }
    public function countCategory($where = NULL, $order = NULL, $limit = NULL){
        $sel = $this->select("SELECT count(idcategory) as total from dsh_tbcategory $where $order $limit");
        return $sel;
    }

    public function selectCategoryData($id){
        return $this->select("SELECT title from dsh_tbcategory where idcategory ='$id'");
    }
    public function updatecategory($id,$name){
        return $this->db->Execute("UPDATE dsh_tbcategory set title = '$name' WHERE idcategory = '$id'");
    }
    public function categoryDeactivate($id){
       return $this->db->Execute("UPDATE dsh_tbcategory set status = 'N' where idcategory in ($id)");  
    }
    public function categoryActivate($id){
       return $this->db->Execute("UPDATE dsh_tbcategory set status = 'A' where idcategory in ($id)");  
    }
    public function checkName($name){
        return $this->select("select idcategory from dsh_tbcategory where title='$name'");
    }
}
?>
