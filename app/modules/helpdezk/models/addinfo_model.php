<?php
class addinfo_model extends Model{
    public function getAddInfos() {
        $ret = $this->db->Execute("SELECT idaddinfo, name FROM hdk_tbaddinfo ORDER BY name ASC");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
	
    public function setRequestAddInfo($code_request, $idaddinfo){
        $ret = $this->db->Execute("insert into hdk_tbrequest_addinfo (idaddinfo,code_request) values ($idaddinfo,'$code_request')");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
	
	public function getRequestAddInfos($code_request) {
        $ret = $this->db->Execute("SELECT b.idaddinfo, b.name FROM hdk_tbrequest_addinfo a, hdk_tbaddinfo b WHERE a.idaddinfo = b.idaddinfo AND code_request = '$code_request'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
	
	public function clearRequestAddInfos($code_request) {
        $ret = $this->db->Execute("DELETE FROM hdk_tbrequest_addinfo WHERE code_request = '$code_request'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
	
}
