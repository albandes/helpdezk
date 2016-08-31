<?php
class requestEmail_model extends Model{
    	
    public function getRequestEmail($where, $order, $limit){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "SELECT idgetemail, serverurl, servertype, serverport, user, password, ind_create_user, ind_delete_server, idservice, filter_from, filter_subject, login_layout, email_response_as_note  FROM hdk_tbgetemail $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "SELECT idgetemail, serverurl, servertype, serverport, user, password, ind_create_user, ind_delete_server, idservice, filter_from, filter_subject, login_layout, email_response_as_note  FROM hdk_tbgetemail $where $order";
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
	
	public function insertRequestEmail($serverurl, $servertype, $serverport, $emailacct, $password, $idservice, $filter_from, $filter_subject, $ind_create_user, $ind_delete_server, $login_layout, $email_response_as_note){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return $this->db->Execute("insert into hdk_tbgetemail (serverurl,servertype,serverport,user,password,ind_create_user,ind_delete_server,idservice,filter_from,filter_subject,login_layout,email_response_as_note) values ('$serverurl','$servertype','$serverport','$emailacct','$password','$ind_create_user','$ind_delete_server','$idservice','$filter_from','$filter_subject','$login_layout','$email_response_as_note')");
        } elseif ($database == 'oci8po') {
            return $this->db->Execute("insert into hdk_tbgetemail (serverurl,servertype,serverport,user_,password,ind_create_user,ind_delete_server,idservice,filter_from,filter_subject,login_layout,email_response_as_note) values ('$serverurl','$servertype','$serverport','$emailacct','$password','$ind_create_user','$ind_delete_server','$idservice','$filter_from','$filter_subject','$login_layout','$email_response_as_note')");
        }        
    }
    
    public function insertRequestEmailDepartment($idgetemail, $iddepartment){
        return $this->db->Execute("insert into hdk_tbgetemaildepartment (idgetemail,iddepartment) values ('$idgetemail', '$iddepartment')");
    }
    
    public function deleteRequestEmailDepartment($idgetemail){
        return $this->db->Execute("DELETE FROM hdk_tbgetemaildepartment WHERE idgetemail = $idgetemail");
    }
    
    public function getRequestEmailDepartment($idgetemail){
        return $this->db->Execute("SELECT iddepartment FROM hdk_tbgetemaildepartment WHERE idgetemail = $idgetemail");
    }
    	
	public function updateRequestEmail($id, $serverurl, $servertype, $serverport, $emailacct, $password, $idservice, $filter_from, $filter_subject, $ind_create_user, $ind_delete_server, $login_layout, $email_response_as_note){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return $this->db->Execute("UPDATE hdk_tbgetemail set 
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
                            where idgetemail = $id");
        } elseif ($database == 'oci8po') {
            return $this->db->Execute("UPDATE hdk_tbgetemail set 
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
                            where idgetemail = $id");
        }
    }
	
	public function requestEmailDelete($id){
        return $this->db->Execute("delete from hdk_tbgetemail where idgetemail='$id'");
    }
	
	public function InsertID() {
        return $this->db->Insert_ID();
    }
	
	
   
    
    
 
}