<?php
class priority_model extends Model{
    public function selectPriority($where = NULL, $order = NULL, $limit = NULL){
        return $this->db->Execute("SELECT tbp.idpriority, tbp.name, tbp.order, tbp.color, tbp.limit_days, tbp.limit_hours, status from hdk_tbpriority as tbp $where $order $limit");
    }
    public function countPriority($where = NULL, $order = NULL, $limit = NULL){
        $sel = $this->select("SELECT count(idpriority) as total from hdk_tbpriority $where");
        return $sel;
    }
    public function selectNextOrder(){
        return $this->select("select max(tbp.order) as ord FROM hdk_tbpriority as tbp");
    }
    public function insertPriority($name,$order,$color,$default,$vip,$limit_hours,$limit_days){
        return $this->db->Execute("INSERT into hdk_tbpriority (name,hdk_tbpriority.order,color,hdk_tbpriority.default,vip,limit_hours,limit_days,hdk_tbpriority.status) values ('$name','$order','$color','$default','$vip','$limit_hours','$limit_days','A')");
    }
    public function selectPriorityData($id){
        return $this->select("SELECT tbp.name, tbp.order, tbp.color, tbp.default, tbp.vip, tbp.limit_hours, tbp.limit_days, status from hdk_tbpriority as tbp where idpriority = '$id'");
    }
    public function priorityDeactivate($id){
        return $this->db->Execute("UPDATE hdk_tbpriority set status = 'N' where idpriority in ($id)");
    }
    public function priorityActivate($id){
        return $this->db->Execute("UPDATE hdk_tbpriority set status = 'A' where idpriority in ($id)");
    }
    public function priorityDelete($id){
        return $this->db->Execute("delete from hdk_tbpriority where idpriority='$id'");
    }
    public function editPriority($id ,$name, $order, $color, $default, $vip, $limit_hours, $limit_days){
        return $this->db->Execute("UPDATE hdk_tbpriority as tbp set tbp.name='$name', tbp.order = '$order', tbp.color='$color', tbp.default = '$default', tbp.vip='$vip', tbp.limit_hours='$limit_hours', tbp.limit_days='$limit_days' where tbp.idpriority='$id'");
    }
    public function updateDefaults(){
        return $this->db->Execute("update hdk_tbpriority as prio set prio.default = 0 where prio.default=1");
    }
}
?>
