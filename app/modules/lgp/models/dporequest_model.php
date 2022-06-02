<?php

if(class_exists('Model')) {
    class DynamicDPORequest_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicDPORequest_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicDPORequest_model extends apiModel {}
}

class dporequest_model extends DynamicDPORequest_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }
	
	public function getTickets($dtentry,$where=null,$order=null,$limit=null,$group=null)
	{
		$sql = "SELECT DISTINCT a.idrequest,a.code_request,`subject`,`description`,idowner, b.`name` owner_name, idcreator, 
                        c.`name` creator_name, a.idstatus, f.name status_name, user_view, f.color status_color, $dtentry, a.dtentry, 
                        d.id_in_charge, e.name in_charge_name, d.type in_charge_type, b.email owner_email
                  FROM lgp_tbrequest a
                  JOIN lgp_tbrequester b
                    ON (a.idowner = b.idrequester)
                  JOIN tbperson c
                    ON (a.idcreator = c.idperson)
                  JOIN lgp_tbrequest_in_charge d
                    ON (a.code_request = d.code_request AND d.ind_in_charge = 1)
                  JOIN tbperson e
                    ON (d.id_in_charge = e.idperson)
                  JOIN lgp_tbstatus f
                    ON (f.idstatus = a.idstatus)
                 $where $group $order $limit";
		//echo "{$sql}\n";
        return $this->selectPDO($sql);
	}

    public function insertRequest($code_request,$subject,$description,$ownerID,$creatorID,$statusID){
        $sql = "INSERT INTO lgp_tbrequest(code_request,subject,description,idowner,idcreator,idstatus,dtentry) 
                  VALUES(:code_request,:subject,:description,:ownerID,:creatorID,:statusID,NOW())";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":code_request",$code_request);
            $sth->bindParam(":subject",$subject);
            $sth->bindParam(":description",$description);
            $sth->bindParam(":ownerID",$ownerID);
            $sth->bindParam(":creatorID",$creatorID);
            $sth->bindParam(":statusID",$statusID);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbrequest'));
    }

    public function insertRequestCharge($code_request,$inChargeID,$type,$ind_in_charge) {
        $sql = "INSERT INTO lgp_tbrequest_in_charge (code_request,id_in_charge,`type`,ind_in_charge) 
                VALUES (:code_request,:inChargeID,:type,:ind_in_charge)";
        
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":code_request",$code_request);
            $sth->bindParam(":inChargeID",$inChargeID);
            $sth->bindParam(":type",$type);
            $sth->bindParam(":ind_in_charge",$ind_in_charge);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbrequest_in_charge'));
    }    

    public function saveTicketAtt($code_request,$file_name){
        $sql = "INSERT INTO lgp_tbrequest_attachment (code_request,file_name)
                VALUES(:code_request,:file_name)";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":code_request",$code_request);
            $sth->bindParam(":file_name",$file_name);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbrequest_attachment'));
    }

    public function deleteTicketAtt($attachmentID){
        $sql =  "DELETE FROM lgp_tbrequest_attachment WHERE idrequest_attachment = {$attachmentID}";
        
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    public function getRequester($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT idrequester, `name`, cpf, email
                  FROM lgp_tbrequester  
                $where $group $order $limit"; //echo "{$sql}\n";

        return $this->selectPDO($sql);
    }

    /**
     * Grava o novo solicitante
     *
     * @param  string   $requesterName      Nome
     * @param  string   $requesterCPF       CPF
     * @param  string   $requesterEmail     E-mail
     * @return array    Contendo os parametros "success" (true/false), "message" (para erros), "id" ID do solicitante inserido
     */
    public function insertRequester($requesterName,$requesterCPF,$requesterEmail) {
        $sql = "INSERT INTO lgp_tbrequester(`name`,cpf,email) 
                  VALUES(:requesterName,:requesterCPF,:requesterEmail)";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":requesterName",$requesterName);
            $sth->bindParam(":requesterCPF",$requesterCPF);
            $sth->bindParam(":requesterEmail",$requesterEmail);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbrequester'));
    }

    public function getCode() {

        $sql = "SELECT cod_request, cod_month FROM lgp_tbrequest_code WHERE cod_month = " . date("Ym");
        return $this->selectPDO($sql);
    }

    public function countGetCode() {
        $sql = "SELECT count(cod_request) as total FROM lgp_tbrequest_code WHERE cod_month = " . date("Ym");
        return $this->selectPDO($sql);
    }

    public function increaseCode($code_request) {
        $sql = "UPDATE lgp_tbrequest_code 
                   SET cod_request = " . ($code_request + 1) . "
                 WHERE cod_month = " . date("Ym");
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    public function createCode($code_request) {
        $sql = "INSERT INTO lgp_tbrequest_code(cod_request,cod_month) 
                VALUES (".($code_request + 1).",". date("Ym") .")";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    public function getDPOID() {
        $sql = "SELECT idperson, b.`name`, b.email
                  FROM lgp_tbconfig a, tbperson b
                 WHERE a.value = b.login
                   AND a.session_name = 'LGP_DPO_USER'"; //echo "{$sql}\n";

        return $this->selectPDO($sql);
    }

    public function insertNote($code,$person,$note,$date,$public,$idtype,$flgopen=0)
    {
        $sql = "INSERT INTO lgp_tbnote (code_request,idperson,description,dtentry,public,idtypenote,flag_opened) 
                     VALUES (:code,:person,:note,{$date},:public,:idtype,:flgopen)";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":code",$code);
            $sth->bindParam(":person",$person);
            $sth->bindParam(":note",$note);
            //$sth->bindParam(":date",$date);
            $sth->bindParam(":public",$public);
            $sth->bindParam(":idtype",$idtype);
            $sth->bindParam(":flgopen",$flgopen);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbnote'));
    }

    public function getInCharge($code_request)
    {
        $sql = "SELECT a.idrequest_in_charge, a.code_request, a.id_in_charge, a.`type`,a.ind_repass, b.`name`
                  FROM lgp_tbrequest_in_charge  a, tbperson b
                 WHERE a. code_request  = $code_request
                   AND ind_in_charge = 1
                   AND a.id_in_charge = b.idperson
                ";
        return $this->selectPDO($sql);
    }

    public function getRepassGroups($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT a.idgroup, a.idperson, `name`, 'G' `type`,a.`status`
                  FROM `lgp_tbgroup` a, tbperson b 
                 WHERE b.idperson = a.idperson
                 $where $group $order $limit";
        
        return $this->selectPDO($sql);
    }

    public function getRepassUsers($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT idperson, `name`, 'P' `type`,`status`
                    FROM tbperson
                   WHERE idtypeperson IN (2,3,(SELECT idtypeperson FROM tbtypeperson WHERE `name` = 'LGP_personhasaccess'))
                   $where $group $order $limit";
        
        return $this->selectPDO($sql);
    }

    public function insertInCharge($code,$person,$type,$ind,$rep,$track=0) {
        $sql = "INSERT INTO lgp_tbrequest_in_charge (code_request,id_in_charge,`type`,ind_in_charge,ind_repass,ind_track) 
                VALUES (:code,:person,:type,:ind,:rep,:track)";
        
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":code",$code);
            $sth->bindParam(":person",$person);
            $sth->bindParam(":type",$type);
            $sth->bindParam(":ind",$ind);
            $sth->bindParam(":rep",$rep);
            $sth->bindParam(":track",$track);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbrequest_in_charge'));
    }

    public function getTicketAttachs($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT idrequest_attachment,code_request,file_name 
                  FROM lgp_tbrequest_attachment
                $where $group $order $limit";

        return $this->selectPDO($sql);
    }

    public function getIdStatusSource($idstatus) {
        $sql = "SELECT idstatus_source FROM lgp_tbstatus WHERE idstatus = '$idstatus'";

        return $this->selectPDO($sql);
    }

    public function getIdPersonGroup($personID) {
        $sql = "SELECT a.idperson 
                  FROM lgp_tbgroup a,lgp_tbgroup_has_person b 
                 WHERE b.idgroup = a.idgroup
                   AND b.idperson = {$personID}" ;
        
        return $this->selectPDO($sql);
    }

    public function getTicketNotes($code) {
        $sql = "SELECT nt.idnote, pers.idperson, pers.name, nt.description, nt.dtentry,
                       nt.idtypenote, nt.flag_opened
                  FROM (lgp_tbnote AS nt, tbperson AS pers)
                 WHERE code_request = '{$code}' AND pers.idperson = nt.idperson
              ORDER BY idnote DESC" ;
        
        return $this->selectPDO($sql);
    }

    public function getNoteAttachments($idNote){
        $sql = "SELECT a.idnote_attachments, b.filename
                  FROM lgp_tbnote_has_attachments a
            INNER JOIN lgp_tbnote_attachments b
                    ON a.idnote_attachments = b.idnote_attachments
                 WHERE a.idnote = {$idNote}" ;
        
        return $this->selectPDO($sql);
    }

    public function getOperatorGroups($personID) {
        $sql = "SELECT a.idperson, `name`, 'G' `type`,a.`status`
                  FROM `lgp_tbgroup` a, tbperson b, lgp_tbgroup_has_person c
                 WHERE b.idperson = a.idperson
                   AND c.idgroup = a.idgroup
                   AND c.idperson = {$personID}";
        
        return $this->selectPDO($sql);
    }

    public function removeIncharge($code) {
        $sql = "UPDATE lgp_tbrequest_in_charge SET ind_in_charge = '0' WHERE code_request = '$code'";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    public function updateTicketStatus($statusID,$code)
    {
        $sql = "UPDATE lgp_tbrequest SET idstatus = '{$statusID}' WHERE code_request = '{$code}'";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    public function saveNoteAttachment($noteID,$filename)
    {
        $sql = "CALL lgp_insertNoteAttachments($noteID,'{$filename}',@id)";

        try{           
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        $sql2 = "SELECT @id AS idnote_attachments";
        try{           
            $stmt = $this->dbPDO->prepare($sql2);
            $stmt->execute();
            $myResultId = $stmt->fetchColumn();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql2}");
        }
        

        return array("success"=>true,"message"=>"","id"=>$myResultId);
    }

    public function getTypeNote($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT idtypenote, `description`
                  FROM `lgp_tbtypenote`
                 $where $group $order $limit";
        //echo "{$sql}";
        return $this->selectPDO($sql);
    }

    public function getTicketFile($id, $type)
    {
        $field = ($type == 'request') ? "file_name" : "filename";
        $table = ($type == 'request') ? "lgp_tbrequest_attachment" : "lgp_tbnote_attachments";
        $cond = ($type == 'request') ? "idrequest_attachment" : "idnote_attachments";
        
        $sql = "SELECT {$field} file_name FROM {$table} WHERE {$cond} = '{$id}'";
        //echo "{$sql}\n";
        return $this->selectPDO($sql);
    }

    public function getTicketNotesUser($code) {
        $sql = "SELECT nt.idnote, pers.idperson, pers.name, nt.description, nt.dtentry,
                       nt.idtypenote, nt.flag_opened
                  FROM (lgp_tbnote AS nt, tbperson AS pers)
                 WHERE code_request = '{$code}' AND pers.idperson = nt.idperson
                   AND nt.idtypenote != 2 
              ORDER BY nt.dtentry DESC LIMIT 1" ;
        
        return $this->selectPDO($sql);
    }

}