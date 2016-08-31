<?php

class evaluation_model extends Model {

    public function selectEvaluation($where, $order, $limit) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "SELECT tbe.idevaluation, tbe.name, tbe.icon_name, tbe.status, tbq.question FROM hdk_tbevaluation tbe, hdk_tbevaluationquestion tbq WHERE tbq.idquestion = tbe.idquestion $where $order $limit";
        } elseif ($database == 'oci8po') {
            $core = "SELECT tbe.idevaluation, tbe.name, tbe.icon_name, tbe.status, tbq.question FROM hdk_tbevaluation tbe, hdk_tbevaluationquestion tbq WHERE tbq.idquestion = tbe.idquestion $where $order";
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

    public function countEvaluation($where = NULL) {
        return $this->db->Execute("select count(idevaluation) as total from hdk_tbevaluation tbe, hdk_tbevaluationquestion tbq where tbq.idquestion = tbe.idquestion $where");
    }

    public function selectQuestion($where = NULL, $order = NULL, $limit = NULL) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "SELECT idquestion, question, status FROM hdk_tbevaluationquestion $where $order $limit";
        } elseif ($database == 'oci8po') {
            $core = "SELECT idquestion, question, status FROM hdk_tbevaluationquestion $where $order";
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
    
    public function selectQuestions() {
        return $this->db->Execute("select idquestion, question, status from hdk_tbevaluationquestion where status ='A'");
    }
	
	public function selectQuestionsAll() {
        return $this->db->Execute("select idquestion, question, status from hdk_tbevaluationquestion");
    }

    public function countQuestion($where = NULL) {
        return $this->db->Execute("select count(idquestion) as total from hdk_tbevaluationquestion $where");
    }

    public function insertEvaluation($idquest, $name, $icon, $checked) {
        return $this->db->Execute("insert into hdk_tbevaluation (idquestion, name, icon_name,checked) values ($idquest,'$name','$icon','$checked')");
    }

    public function updateEvaluation($id, $name, $icon, $question, $checked) {
        return $this->db->Execute("UPDATE hdk_tbevaluation SET name='$name', icon_name='$icon', idquestion='$question', checked='$checked' where idevaluation='$id'");
    }

    public function evaluationDeactivate($id) {
        return $this->db->Execute("UPDATE hdk_tbevaluation set status = 'N' where idevaluation in ($id)");
    }

    public function evaluationActivate($id) {
        return $this->db->Execute("UPDATE hdk_tbevaluation set status = 'A' where idevaluation in ($id)");
    }
    
    public function evaluationQuestionDeactivate($id) {
        return $this->db->Execute("UPDATE hdk_tbevaluationquestion set status = 'N' where idquestion in ($id)");
    }

    public function evaluationQuestionActivate($id) {
        return $this->db->Execute("UPDATE hdk_tbevaluationquestion set status = 'A' where idquestion in ($id)");
    }

    public function evaluationDelete($id) {
        return $this->db->Execute("delete from hdk_tbevaluation where idevaluation='$id'");
    }

    public function saveatt($NOM_FILE) {
        return $this->db->Execute("INSERT INTO hdk_tbevaluation_icon (file_name) VALUES ('$NOM_FILE')");
    }

    public function maxatt() {
        $sel = $this->select("SELECT max(idevaluation_icon) as COD FROM hdk_tbevaluation_icon");
        return $sel;
    }

    public function searchatt() {
        session_start();
        return $this->select("SELECT idrequest_attachment, code_request, file_name FROM hdk_tbrequest_attachment WHERE idrequest_attachment in (" . $_SESSION["SES_COD_ATTACHMENT"] . ") ");
    }

    public function searchattname($COD_ATT) {
        session_start();
        $sel = $this->select("SELECT file_name FROM hdk_tbrequest_attachment WHERE idrequest_attachment = " . $COD_ATT);
        return $sel;
    }

    public function delatt($COD_ATT) {
        return $this->db->Execute("DELETE from hdk_tbrequest_attachment where idrequest_attachment = " . $COD_ATT);
    }

    public function selectEvaluationData($id) {
        return $this->select("select idquestion, name, icon_name, checked from hdk_tbevaluation where idevaluation ='$id'");
    }

    public function insertQuestion($question) {
        return $this->db->Execute("insert into hdk_tbevaluationquestion (question) values ('$question')");
    }
    public function selectQuestionData($id){
        return $this->db->Execute("select question from hdk_tbevaluationquestion where idquestion = '$id'");
    }
    public function updateQuestion($id, $question){
        return $this->db->Execute("update hdk_tbevaluationquestion set question = '$question' where idquestion ='$id'");
    }
	public function clearChecked($idquestion){
		return $this->db->Execute("UPDATE hdk_tbevaluation SET checked = 0 WHERE idquestion ='$idquestion'");
	}

}

?>
