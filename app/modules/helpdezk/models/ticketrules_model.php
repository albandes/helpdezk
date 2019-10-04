<?php
if(class_exists('Model')) {
    class DynamicTicketRules_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicTicketRules_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicTicketRules_model extends apiModel {}
}

class ticketrules_model extends DynamicTicketRules_model {

    public $database;

    public function __construct(){
      parent::__construct();
          $this->database = $this->getConfig('db_connect');
    }    			

	public function getRule($iditem, $idservice){
		if ($this->database == 'oci8po') {
			return $this->db->Execute("SELECT idapproval, idperson, order_ \"order\", fl_recalculate FROM hdk_tbapproval_rule WHERE iditem = $iditem AND idservice = $idservice ORDER BY order_ ASC");
		}
		else
		{
			return $this->db->Execute("SELECT idapproval, idperson, `order`, fl_recalculate FROM hdk_tbapproval_rule WHERE iditem = $iditem AND idservice = $idservice ORDER BY `order` ASC");
		}
	}

    public function getIdPersonApproverRule($iditem, $idservice)
    {

        $rs = $this->db->Execute("SELECT idperson FROM hdk_tbapproval_rule WHERE iditem = $iditem AND idservice = $idservice AND `order` = 1 ");
        return $rs->fields['idperson'] ;

    }

	public function insertApproval($values){
		if ($this->database == 'oci8po') {
			return $this->db->Execute('INSERT INTO hdk_tbrequest_approval (idapproval, request_code, order_ , idperson, fl_recalculate) '.
           'VALUES ' . $values);
		}
		else
		{
        	return $this->db->Execute('INSERT INTO hdk_tbrequest_approval (idapproval, request_code, `order`, idperson, fl_recalculate) '.
           'VALUES ' . $values);
    	}
    }
	
	public function checkApproval($code_request){
		if ($this->database == 'oci8po') {
			return $this->db->Execute("SELECT idperson, order_ \"order\" FROM hdk_tbrequest_approval WHERE request_code = $code_request AND idnote IS NULL AND fl_rejected = 0 AND rownum = 1 ORDER BY order_ ");
		}
		else
		{
			return $this->db->Execute("SELECT idperson, `order` FROM hdk_tbrequest_approval WHERE request_code = $code_request AND idnote IS NULL AND fl_rejected = 0 ORDER BY `order` LIMIT 1");
		}
	}
	
	public function checkApprovalBt($code_request){
		if ($this->database == 'oci8po') {
			return $this->db->Execute("SELECT a.idperson, a.order_ \"order\" FROM hdk_tbrequest_approval a, hdk_tbrequest b WHERE a.request_code = b.code_request AND idnote IS NULL AND fl_rejected = 0 AND b.idstatus != 6 AND a.request_code = $code_request ");
		}
		else
		{
			return $this->db->Execute("SELECT a.idperson, a.`order` FROM hdk_tbrequest_approval a, hdk_tbrequest b WHERE a.request_code = b.code_request AND idnote IS NULL AND fl_rejected = 0 AND b.idstatus != 6 AND a.request_code = $code_request ");
		}
	}
	
	public function checkNumApp($code_request){
		return $this->db->Execute("SELECT count(*) as num_approve FROM hdk_tbrequest_approval  WHERE request_code = $code_request  AND idnote IS NULL AND fl_rejected = 0");
	}
	
	public function getLastApproval($code_request){
		if ($this->database == 'oci8po') {
			return $this->db->Execute("SELECT idperson, order_ \"order\" FROM hdk_tbrequest_approval WHERE request_code = $code_request AND rownum = 1 AND idnote IS NOT NULL ORDER BY order_ DESC ");
		}
		else
		{
			return $this->db->Execute("SELECT idperson, `order` FROM hdk_tbrequest_approval WHERE request_code = $code_request AND idnote IS NOT NULL ORDER BY `order` DESC LIMIT 1");
		}
	}
	
	public function updateApprovalNote($idnote, $idperson, $code_request){
		return $this->db->Execute("UPDATE hdk_tbrequest_approval SET idnote = $idnote WHERE request_code = $code_request AND idperson = $idperson");
	}
	
	public function updateReproveNote($idnote, $idperson, $code_request){
		return $this->db->Execute("UPDATE hdk_tbrequest_approval SET idnote = $idnote, fl_rejected = 1 WHERE request_code = $code_request AND idperson = $idperson");
	}
	
	public function updateReturnApp($code_request, $idperson, $order){
		if ($this->database == 'oci8po') {
			return $this->db->Execute("UPDATE hdk_tbrequest_approval SET idnote = NULL WHERE request_code = $code_request AND idperson = $idperson AND order_ = $order");
		}
		else
		{
			return $this->db->Execute("UPDATE hdk_tbrequest_approval SET idnote = NULL WHERE request_code = $code_request AND idperson = $idperson AND `order` = $order");
		}
	}
	
	public function getRespOriginal($code_request){
		if ($this->database == 'oci8po') {
			return $this->db->Execute("select id_in_charge, type from hdk_tbrequest_in_charge where code_request = $code_request AND rownum = 1 ORDER BY idrequest_in_charge ");
		}
		else
		{
			return $this->db->Execute("select id_in_charge, type from hdk_tbrequest_in_charge where code_request = $code_request ORDER BY idrequest_in_charge LIMIT 1");
		}
	}
	
	public function getRecalculate($code_request){
		if ($this->database == 'oci8po') {
			return $this->db->Execute("SELECT fl_recalculate as recalculate, a.iditem, a.idservice, a.idpriority FROM hdk_tbrequest a, hdk_tbapproval_rule b where a.iditem = b.iditem AND a.idservice = b.idservice AND a.code_request = $code_request AND rownum=1 ");
		}
		else
		{
			return $this->db->Execute("SELECT fl_recalculate as recalculate, a.iditem, a.idservice, a.idpriority FROM hdk_tbrequest a, hdk_tbapproval_rule b where a.iditem = b.iditem AND a.idservice = b.idservice AND a.code_request = $code_request LIMIT 0,1");
		}			
	}

	public function getUsers($iditem, $idservice){
		$query = "SELECT per.idperson, per.name 
					FROM tbperson per 
				   WHERE per.idperson NOT IN (SELECT DISTINCT app.idperson 
				   								FROM hdk_tbapproval_rule app 
											   WHERE app.iditem = $iditem 
											   	 AND app.idservice = $idservice) 
					 AND per.idtypeperson IN('1','3') 
					 AND per.status = 'A' 
				ORDER BY per.name ASC";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

		return $ret;
    }
	
	public function getUsersApprove($iditem, $idservice){
		if ($this->database == 'mysqli') {
            $query = "SELECT per.idperson, per.name, app.fl_recalculate 
						FROM tbperson per, hdk_tbapproval_rule app 
					   WHERE per.idperson = app.idperson 
					   	 AND app.iditem = $iditem 
						 AND app.idservice = $idservice 
					ORDER BY app.order ASC" ;
        } elseif ($this->database == 'oci8po') {
            $query = "SELECT per.idperson, per.name, app.fl_recalculate 
						FROM tbperson per, hdk_tbapproval_rule app 
					   WHERE per.idperson = app.idperson 
					     AND app.iditem = $iditem 
						 AND app.idservice = $idservice 
					ORDER BY app.order_ ASC";
        }
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

		return $ret;
    }
    
	public function insertUsersApprove($iditem, $idservice, $idperson, $order, $recalculate){
		if ($this->database == 'mysqli') {
			$query = "INSERT INTO hdk_tbapproval_rule (iditem,idservice,idperson,`order`,fl_recalculate) 
						VALUES ('$iditem','$idservice','$idperson','$order','$recalculate')" ;
        } elseif ($this->database == 'oci8po') {
			$query = "INSERT INTO hdk_tbapproval_rule (iditem,idservice,idperson,order_,fl_recalculate) 
						VALUES ('$iditem','$idservice','$idperson','$order','$recalculate')";
        }
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

		return $ret;
    }
	
	public function deleteUsersApprove($iditem, $idservice){
		$query = "DELETE FROM  hdk_tbapproval_rule WHERE idservice = '$idservice' AND iditem = '$iditem'";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

		return $ret;
	}
	
}
?>
