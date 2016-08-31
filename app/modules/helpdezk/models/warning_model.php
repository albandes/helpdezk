<?php
class warning_model extends Model{  
    
    public function selectWarning($where = NULL, $order = NULL, $limit = NULL){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "SELECT
                          a.idmessage,
                          b.idtopic,
                          b.title as title_topic,
                          a.title as title_warning,
                          a.description,
                          a.dtcreate,
                          a.dtstart,
                          a.dtend,
                          a.showin,
                          a.sendemail,
                          (select count(*) from bbd_topic_company WHERE idtopic = a.idtopic) as total_company,
                          (select count(*) from bbd_topic_group WHERE idtopic = a.idtopic) as total_group
                        FROM bbd_tbmessage a, bbd_topic b
                        WHERE a.idtopic = b.idtopic 
                        AND (a.showin = 1 OR a.showin = 3)
                                $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "SELECT
                          a.idmessage,
                          b.idtopic,
                          b.title as title_topic,
                          a.title as title_warning,
                          a.description,
                          to_char(a.dtcreate,'DD/MM/YYYY hh24:MI') dtcreate,
                          to_char(a.dtstart,'DD/MM/YYYY hh24:MI') dtstart,
                          to_char(a.dtend,'DD/MM/YYYY hh24:MI') dtend,
                          a.showin,
                          a.sendemail,
                          (select count(*) from bbd_topic_company WHERE idtopic = a.idtopic) as total_company,
                          (select count(*) from bbd_topic_group WHERE idtopic = a.idtopic) as total_group
                        FROM bbd_tbmessage a, bbd_topic b
                        WHERE a.idtopic = b.idtopic 
                        AND (a.showin = 1 OR a.showin = 3)
                        $where $order";
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
                $query =    "
                            SELECT   *
                              FROM   (SELECT                                          
                                            a  .*, ROWNUM rnum
                                        FROM   (  
                                                  
                                                $core 

                                                ) a
                                       )
                            ";
            }
        }
		//die($query);
        return $this->db->Execute($query);

    }
    
    public function checkCompany($idtopic, $idcompany){
        return $this->select("SELECT COUNT(*) as chk FROM bbd_topic_company WHERE idtopic = $idtopic AND idcompany = $idcompany");
    }
    
    public function checkGroup($idtopic, $idsgroup){
        return $this->select("SELECT COUNT(*) as chk FROM bbd_topic_group WHERE idtopic = $idtopic AND idgroup IN ($idsgroup)");
    }
    
    public function checkRead($idperson, $idmessage){
        return $this->select("SELECT COUNT(*) as total FROM bbd_tbread WHERE idperson = $idperson AND idmessage = $idmessage");
    }
    
    public function setRead($idperson, $idmessage){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $data = array("dtread" => "NOW()", "idperson" => $idperson, "idmessage" => $idmessage);
        } elseif ($database == 'oci8po') {
            $data = array("dtread" => "SYSDATE", "idperson" => $idperson, "idmessage" => $idmessage);
        }       
        return $this->insert("bbd_tbread",$data);
    }
   
}
?>
