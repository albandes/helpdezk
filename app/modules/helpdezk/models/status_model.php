<?php
class status_model extends Model{
    public function selectStatus($where, $order, $limit){
        return $this->db->Execute("Select idstatus, name, user_view, color, status from hdk_tbstatus $where $order $limit");
    }
    public function countStatus($where, $order, $limit){
        return $this->db->Execute("select count(idstatus) as total from hdk_tbstatus $where $order $limit");
    }
    public function insertStatus($name,$user,$color){
        return $this->db->Execute("insert into hdk_tbstatus (name,user_view,color) values ('$name','$user','$color')");
    }
    public function statusDelete($id){
        return $this->db->Execute("delete from hdk_tbstatus where idstatus='$id'");
    }
    public function selectStatusData($id){
        return $this->select("SELECT name, user_view, color from hdk_tbstatus where idstatus='$id';");
    }
    public function updateStatus($id,$name,$user_view,$color){
        return $this->db->Execute("UPDATE hdk_tbstatus SET name='$name', user_view='$user_view', color='$color' where idstatus='$id'");
    }
    public function statusDeactivate($id){
        return $this->db->Execute("UPDATE hdk_tbstatus set status = 'N' where idstatus in ($id)");
    }
    public function statusActivate($id){
        return $this->db->Execute("UPDATE hdk_tbstatus set status = 'A' where idstatus in ($id)");
    }
}
?>
