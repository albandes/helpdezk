<?php

class manageattachments_model extends Model{
    public function searchatt(){
        session_start();
        
        if(!isset($_SESSION["SES_COD_ATTACHMENT"])){
            $v_aux = "0";
        }else{
            if (substr($_SESSION["SES_COD_ATTACHMENT"],-1) === ",") {                
                $v_aux = substr($_SESSION["SES_COD_ATTACHMENT"], 0,-1);    

            }else{
                $v_aux = $_SESSION["SES_COD_ATTACHMENT"];    
            }
            
        }
        $v_sql = "SELECT idrequest_attachment, code_request, file_name FROM hdk_tbrequest_attachment WHERE idrequest_attachment in (".$v_aux.") ";

        $ret = $this->select($v_sql);
        if (!$ret) {
            $sError = $v_sql . " Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
        
    }
    public function searchattname($COD_ATT){
        $ret = $this->select("SELECT file_name FROM hdk_tbrequest_attachment WHERE idrequest_attachment = ".$COD_ATT);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    public function delatt($COD_ATT){
        $ret = $this->db->Execute("DELETE from hdk_tbrequest_attachment where idrequest_attachment = ".$COD_ATT);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function saveatt($NOM_FILE){
        $ret = $this->db->Execute("INSERT INTO hdk_tbrequest_attachment (file_name) VALUES ('$NOM_FILE')");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    public function maxatt(){
        $ret= $this->select("SELECT max(idrequest_attachment) as cod FROM hdk_tbrequest_attachment");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    //funcao para os anexos via apontamento
    public function savenoteatt($NOM_FILE){
        $ret = $this->db->Execute("INSERT INTO hdk_tbnote_attachment (file_name) VALUES ('$NOM_FILE')");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function maxnoteatt(){
        $ret= $this->select("SELECT max(idnote_attachment) as cod FROM hdk_tbnote_attachment");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
}
?>
