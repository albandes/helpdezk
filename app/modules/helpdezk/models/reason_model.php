<?php
if(class_exists('Model')) {
    class DynamicReason_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicReason_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicReason_model extends apiModel {}
}

class reason_model extends DynamicReason_model {

    //class features_model extends Model {

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function selectReason($where=NULL, $order=NULL, $limit=NULL){
        
        if ($this->database == 'mysqli') {
            $query = "SELECT tbr.idreason, tbr.name reason, tbr.status, tbs.name as service,
                             tbi.name as item, tbt.name as `type`, tba.name as `area`  
                        FROM hdk_tbcore_reason tbr, hdk_tbcore_service tbs, hdk_tbcore_item tbi,
                             hdk_tbcore_type tbt, hdk_tbcore_area tba 
                       WHERE tbs.idservice = tbr.idservice 
                         AND tbs.iditem = tbi.iditem
                         AND tbi.idtype = tbt.idtype
                         AND tbt.idarea = tba.idarea
                       $where $order $limit" ;
        } elseif ($this->database == 'oci8po') {
            $core  = "SELECT tbr.idreason, tbr.name reason, tbr.status, tbs.name as service,
                             tbi.name as item, tbt.name as `type`, tba.name as `area`  
                        FROM hdk_tbcore_reason tbr, hdk_tbcore_service tbs, hdk_tbcore_item tbi,
                                hdk_tbcore_type tbt, hdk_tbcore_area tba 
                        WHERE tbs.idservice = tbr.idservice 
                          AND tbs.iditem = tbi.iditem
                          AND tbi.idtype = tbt.idtype
                          AND tbt.idarea = tba.idarea $where $order";
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
        
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }else{
            return $ret;
        }
    }
	
	public function insertReason($reason, $idservice){

        $query = "INSERT INTO hdk_tbcore_reason (idservice,`name`) 
                       VALUES ($idservice,'$reason')";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        
        return $ret;        
        
    }
    	
	public function updateReason($idreason, $reason, $idservice, $available){

        $query = "UPDATE hdk_tbcore_reason SET 
                            idservice = $idservice,
                            `name` = '$reason',
                            status = '$available'
                   WHERE idreason = $idreason";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        
        return $ret;
    }
	
	public function reasonDelete($idreason){
        $query = "DELETE FROM hdk_tbcore_reason WHERE idreason = '$idreason'";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }
	
	public function getReasonData($where=null){
        $query = "SELECT idreason,tbr.`name` reason, tbt.idarea, tbi.idtype, tbs.iditem, tbr.idservice, tbr.`status`
                    FROM hdk_tbcore_reason tbr, hdk_tbcore_service tbs, hdk_tbcore_item tbi,
                        hdk_tbcore_type tbt, hdk_tbcore_area tba
                   WHERE tbr.idservice = tbs.idservice
                     AND tbs.iditem = tbi.iditem
                     AND tbi.idtype = tbt.idtype
                     AND tbt.idarea = tba.idarea
                  $where";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        return $ret;
    }

    public function updateReasonStatus($id,$newStatus){
        $query = "UPDATE hdk_tbcore_reason SET `status` = '$newStatus' WHERE idreason IN ($id)";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        return $ret;
    }

}