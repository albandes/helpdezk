<?php
class status_model extends Model{
    public function selectStatus($where, $order, $limit){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "Select idstatus, name, user_view, color, status from hdk_tbstatus $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "Select idstatus, name, user_view, color, status from hdk_tbstatus $where $order";
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
    public function countStatus($where = NULL){
        return $this->db->Execute("select count(idstatus) as total from hdk_tbstatus $where");
    }
    public function insertStatus($name,$user,$color,$groupby){
        return $this->db->Execute("insert into hdk_tbstatus (name,user_view,color,idstatus_source) values ('$name','$user','$color','$groupby')");
    }
    public function statusDelete($id){
        return $this->db->Execute("delete from hdk_tbstatus where idstatus='$id'");
    }
    public function selectStatusData($id){
        return $this->select("SELECT name, user_view, color, idstatus_source from hdk_tbstatus where idstatus='$id'");
    }
    public function updateStatus($id,$name,$user_view,$color,$groupby){
        return $this->db->Execute("UPDATE hdk_tbstatus SET name='$name', user_view='$user_view', color='$color', idstatus_source='$groupby' where idstatus='$id'");
    }
    public function statusDeactivate($id){
        return $this->db->Execute("UPDATE hdk_tbstatus set status = 'N' where idstatus in ($id)");
    }
    public function statusActivate($id){
        return $this->db->Execute("UPDATE hdk_tbstatus set status = 'A' where idstatus in ($id)");
    }
}
?>
