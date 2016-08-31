<?php
class reason_model extends Model{
    public function selectReason($where = NULL, $order = NULL, $limit = NULL){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "SELECT tbr.idreason, tbr.reason, tbr.status, tbs.name as service from hdk_tbreason tbr, hdk_tbcore_service tbs where tbs.idservice = tbr.idservice $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "SELECT tbr.idreason, tbr.reason, tbr.status, tbs.name as service from hdk_tbreason tbr, hdk_tbcore_service tbs where tbs.idservice = tbr.idservice $where $order";
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
    public function countReason($where = NULL){
        $sel = $this->select("SELECT count(idreason) as total from hdk_tbreason tbr, hdk_tbcore_service tbs where tbs.idservice = tbr.idservice $where");
        return $sel;
    }
    public function selectService(){
        return $this->select("select idservice, name from hdk_tbcore_service where status = 'A'");
    }
    public function insertReason($reason,$type,$available){
        return $this->db->Execute("insert into hdk_tbreason (idservice,reason,status) values ('$type','$reason','$available')");
    }
    public function selectReasonData($id){
        return $this->db->Execute("select idservice, reason, status from hdk_tbreason where idreason = '$id'");
    }
    public function selectAllServices(){
        return $this->select("select idservice, name from hdk_tbcore_service");
    }
    public function deactivateReason($id){
        return $this->db->Execute("UPDATE hdk_tbreason set status = 'N' where idreason in ($id)");
    }
    public function activateReason($id){
        return $this->db->Execute("UPDATE hdk_tbreason set status = 'A' where idreason in ($id)");
    }
    public function reasonDelete($id){
        return $this->db->Execute("delete from hdk_tbreason where idreason in ($id)");
    }
    public function updateReason($id, $reason, $type, $available){
        return $this->db->Execute("update hdk_tbreason set reason = '$reason', idservice = '$type', status = '$available' where idreason = '$id'");
    }
}
?>
