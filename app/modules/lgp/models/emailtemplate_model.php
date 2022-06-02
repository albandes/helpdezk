<?php

if(class_exists('Model')) {
    class DynamicEmailTemplateData_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicEmailTemplateData_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicEmailTemplateData_model extends apiModel {}
}

class emailtemplate_model extends DynamicEmailTemplateData_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }   

    
    public function getEmailTemplate($where=null,$order=null,$limit=null,$group=null)
    {
        $sql = "SELECT idtemplate, name, description, status
                  FROM lgp_tbtemplate_email
                  
                
                 $where $group $order $limit";
        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function insertEmailTemplate($emailTemplateName,$description) {
        $sql = "INSERT INTO lgp_tbtemplate_email(`name`, `description`) 
                  VALUES('$emailTemplateName', '$description')";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateEmailTemplate($emailTemplateID,$emailTemplateName,$description) {
        $sql = "UPDATE lgp_tbtemplate_email
                   SET name = '$emailTemplateName',
                        description = '$description'                        
                 WHERE idtemplate = '$emailTemplateID'";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    

    public function changeStatus($emailTemplateID,$newStatus)
    {
        $sql = "UPDATE lgp_tbtemplate_email
                   SET status = '$newStatus'                       
                 WHERE idtemplate = '$emailTemplateID'";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}