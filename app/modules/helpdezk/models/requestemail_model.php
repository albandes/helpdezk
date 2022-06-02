<?php
if(class_exists('Model')) {
    class DynamicRequestEmail_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicRequestEmail_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicRequestEmail_model extends apiModel {}
}

class requestemail_model extends DynamicRequestEmail_model {

    //class features_model extends Model {

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function getRequestEmail($where=NULL, $order=NULL, $limit=NULL){
        
        if ($this->database == 'mysqli') {
            $query = "SELECT idgetemail, serverurl, servertype, serverport, user, `password`, ind_create_user, 
                             ind_delete_server, idservice, filter_from, filter_subject, login_layout, 
                             email_response_as_note  
                        FROM hdk_tbgetemail $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "SELECT idgetemail, serverurl, servertype, serverport, user, `password`, ind_create_user, 
                             ind_delete_server, idservice, filter_from, filter_subject, login_layout, 
                             email_response_as_note  
                        FROM hdk_tbgetemail $where $order";
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
	
	public function insertRequestEmail($serverurl, $servertype, $serverport, $emailacct, $password, $idservice, $filter_from, $filter_subject, $ind_create_user, $ind_delete_server, $login_layout, $email_response_as_note){
        
        if ($this->database == 'mysqli') {
            $query = "INSERT INTO hdk_tbgetemail (serverurl,servertype,serverport,user,password,ind_create_user,ind_delete_server,idservice,filter_from,filter_subject,login_layout,email_response_as_note) 
                           VALUES ('$serverurl','$servertype','$serverport','$emailacct','$password','$ind_create_user','$ind_delete_server','$idservice','$filter_from','$filter_subject','$login_layout','$email_response_as_note')";
        } elseif ($this->database == 'oci8po') {
            $query = "INSERT INTO hdk_tbgetemail (serverurl,servertype,serverport,user_,password,ind_create_user,ind_delete_server,idservice,filter_from,filter_subject,login_layout,email_response_as_note) 
                           VALUES ('$serverurl','$servertype','$serverport','$emailacct','$password','$ind_create_user','$ind_delete_server','$idservice','$filter_from','$filter_subject','$login_layout','$email_response_as_note')";
        }

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        
        return $ret;        
        
    }
    
    public function insertRequestEmailDepartment($idgetemail, $iddepartment){
        $query = "INSERT INTO hdk_tbgetemaildepartment (idgetemail,iddepartment) VALUES ('$idgetemail', '$iddepartment')";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        
        return $ret;
    }
    
    public function deleteRequestEmailDepartment($idgetemail){
        $query = "DELETE FROM hdk_tbgetemaildepartment WHERE idgetemail = $idgetemail";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        
        return $ret;
    }
    
    public function getRequestEmailDepartment($idgetemail){
        $query = "SELECT iddepartment FROM hdk_tbgetemaildepartment WHERE idgetemail = $idgetemail";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        
        return $ret;
    }
    	
	public function updateRequestEmail($id, $serverurl, $servertype, $serverport, $emailacct, $password, $idservice, $filter_from, $filter_subject, $ind_create_user, $ind_delete_server, $login_layout, $email_response_as_note){
        
        if ($this->database == 'mysqli') {
            $query = "UPDATE hdk_tbgetemail SET 
                            serverurl = '$serverurl',
                            servertype = '$servertype',
                            serverport = '$serverport',
                            user = '$emailacct',
                            password = '$password',
                            ind_create_user = '$ind_create_user',
                            ind_delete_server = '$ind_delete_server',
                            idservice = '$idservice',
                            filter_from = '$filter_from',
                            filter_subject = '$filter_subject',
                            login_layout = '$login_layout',
                            email_response_as_note = '$email_response_as_note'
                            WHERE idgetemail = $id";
        } elseif ($this->database == 'oci8po') {
            $query = "UPDATE hdk_tbgetemail SET 
                            serverurl = '$serverurl',
                            servertype = '$servertype',
                            serverport = '$serverport',
                            user_ = '$emailacct',
                            password = '$password',
                            ind_create_user = '$ind_create_user',
                            ind_delete_server = '$ind_delete_server',
                            idservice = '$idservice',
                            filter_from = '$filter_from',
                            filter_subject = '$filter_subject',
                            login_layout = '$login_layout',
                            email_response_as_note = '$email_response_as_note'
                            WHERE idgetemail = $id";
        }

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        
        return $ret;
    }
	
	public function requestEmailDelete($id){
        $query = "DELETE FROM hdk_tbgetemail WHERE idgetemail='$id'";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }
	
	public function InsertID() {
        return $this->db->Insert_ID();
    }

    public function requestEmailData($where=null){
        $query = "SELECT a.idgetemail, serverurl, servertype, serverport, `user`, `password`, ind_create_user, 
                            ind_delete_server, a.idservice, filter_from, filter_subject, login_layout, 
                            email_response_as_note, e.idarea,d.idtype,c.iditem, f.idperson, b.iddepartment  
                    FROM hdk_tbgetemail a
         LEFT OUTER JOIN hdk_tbgetemaildepartment b
                      ON b.idgetemail = a.idgetemail
                    JOIN hdk_tbcore_service c
                      ON c.idservice = a.idservice
                    JOIN hdk_tbcore_item d
                      ON d.iditem = c.iditem
                    JOIN hdk_tbcore_type e
                      ON e.idtype = d.idtype
         LEFT OUTER JOIN hdk_tbdepartment f
                      ON f.iddepartment = b.iddepartment
                  $where";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        return $ret;
    }


    public function getRequestCodEmail($id){
        $query = " select idrequest, code_request from hdk_tbrequest where code_email = '".$id."'  ";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }

    public function getCountRequest($id){
        $query = " SELECT COUNT(*) as total FROM hdk_tbrequest WHERE code_request =  '".$id."'  ";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret->fields['total'];
    }

    public function getRequestFromNote($where){
        $query = "SELECT code_request FROM hdk_tbnote ".$where."'  ";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }

}