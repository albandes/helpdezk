<?php
class priority_model extends Model{
    public function selectPriority($where = NULL, $order = NULL, $limit = NULL){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "SELECT tbp.idpriority, tbp.name, tbp.order as ord, tbp.default as def, tbp.color, tbp.limit_days, tbp.limit_hours, status from hdk_tbpriority as tbp $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "SELECT tbp.idpriority, tbp.name, tbp.order_ as ord, tbp.default_ as def, tbp.color, tbp.limit_days, tbp.limit_hours, status from hdk_tbpriority tbp $where $order";
            if($limit){
                $limit = str_replace('LIMIT', "", $limit);
                $p     = explode(",", $limit);
                $start = $p[0] + 1; 
                $end   = $p[0] +  $p[1]; 
                $query =    "
                            SELECT   *
                              FROM   (SELECT                                          
                                            a  .*, ROWNUM rnum
                                        FROM   (  
                                                  
                                                $core 

                                                ) a
                                       WHERE   ROWNUM <= $end)
                             WHERE   rnum >= $start         
                            ";
            }else{
                $query = $core;
            }
        }
        return $this->db->Execute($query);
    }
    public function countPriority($where = NULL, $order = NULL, $limit = NULL){
        $sel = $this->select("SELECT count(idpriority) as total from hdk_tbpriority $where");
        return $sel;
    }
    public function selectNextOrder(){
        return $this->select("select max(tbp.order) as ord FROM hdk_tbpriority as tbp");
    }
    public function insertPriority($name,$order,$color,$default,$vip,$limit_hours,$limit_days){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "INSERT into hdk_tbpriority (name,hdk_tbpriority.order,color,hdk_tbpriority.default,vip,limit_hours,limit_days,hdk_tbpriority.status) values ('$name','$order','$color','$default','$vip','$limit_hours','$limit_days','A')" ;
        } elseif ($database == 'oci8po') {
            $query  = "INSERT into hdk_tbpriority (name,hdk_tbpriority.order_,color,hdk_tbpriority.default_,vip,limit_hours,limit_days,hdk_tbpriority.status) values ('$name','$order','$color','$default','$vip','$limit_hours','$limit_days','A')";
        }
        return $this->db->Execute($query);
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
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "UPDATE hdk_tbpriority set name='$name', `order` = '$order', color='$color', `default` = '$default', vip='$vip', limit_hours='$limit_hours', limit_days='$limit_days' where idpriority='$id'" ;
        } elseif ($database == 'oci8po') {
            $query = "UPDATE hdk_tbpriority set name='$name', order_ = '$order', color='$color', default_ = '$default', vip='$vip', limit_hours='$limit_hours', limit_days='$limit_days' where idpriority='$id'";
        }
        return $this->db->Execute($query);
    }
    public function updateDefaults(){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "update hdk_tbpriority as prio set prio.default = 0 where prio.default=1";
        } elseif ($database == 'oci8po') {
            $query = "update hdk_tbpriority set default_ = 0 where default_ = 1";
        }
        return $this->db->Execute($query);
    }
}
?>
