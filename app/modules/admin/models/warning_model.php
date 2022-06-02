<?php
class warning_model extends Model{

	/**
	 * Returns recordset of warnings
	 *
	 * @param  string   $where   Sql where
	 * @param  string   $order   Sql order
	 * @param  string   $limit   Sql limit
	 * @return object   Returns a recordset with helpdezk warnings
	 *
	 * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
	 *
	 *
	 * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
	 */
	public function selectWarning($where = NULL, $order = NULL, $limit = NULL){
        $database = $this->getConfig('db_connect');
		// strpos($database, 'mysql') !== false
		if ($this->isMysql($database)) {
			$ret = $this->select("SELECT
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
									  (select count(*) from bbd_topic_company WHERE idtopic = a.idtopic) + (select count(*) from bbd_topic_group WHERE idtopic = a.idtopic) as total
									FROM bbd_tbmessage a, bbd_topic b
									WHERE a.idtopic = b.idtopic 
         								$where $order $limit");
		} elseif ($database == 'oci8po') {
			if($limit){
				$limit = str_replace('LIMIT', "", $limit);
				$p     = explode(",", $limit);
				$start = $p[0]+1; 
				$end   = $p[0]+$p[1]; 
				$query =	"
							SELECT   *
							  FROM   (SELECT                                          
											a  .*, ROWNUM rnum
										FROM   (  SELECT
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
													  (select count(*) from bbd_topic_company WHERE idtopic = a.idtopic) + (select count(*) from bbd_topic_group WHERE idtopic = a.idtopic) as total
													FROM bbd_tbmessage a, bbd_topic b
													WHERE a.idtopic = b.idtopic 
				         								$where $order) a
									   WHERE   ROWNUM <= $end)
							 WHERE   rnum >= $start			
							";
			}else{
				$query = "SELECT
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
						  (select count(*) from bbd_topic_company WHERE idtopic = a.idtopic) + (select count(*) from bbd_topic_group WHERE idtopic = a.idtopic) as total
						FROM bbd_tbmessage a, bbd_topic b
						WHERE a.idtopic = b.idtopic 
								$where $order";
							
			}

            //die($query);

			$ret = $this->db->Execute($query);
		}
										
		if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br>Query: " . $query;
            $this->error($sError);
        }
        return $ret;
    }	
		
    public function insertTopic($data){
        $query = "INSERT into bbd_topic (title, default_display, fl_emailsent) values ('".$data['title']."' , '".$data['default_display']."' , '".$data['fl_emailsent']."')";
        $ret = $this->db->Execute($query);
        return $ret;        
    }
	
	public function insertTopicCompany($data){
        return $this->insert("bbd_topic_company",$data);
    }
	
	public function getTopicCompany($id){
        return $this->select("SELECT idcompany FROM bbd_topic_company WHERE idtopic = $id ");
    }
	
	public function insertTopicGroup($data){
        return $this->insert("bbd_topic_group",$data);
    }
	
	public function insertWarning($data){
        $database = $this->getConfig('db_connect');
        if ($database == 'oci8po') {
            $sql = "insert into bbd_tbmessage (idtopic,idperson,title,description,dtcreate,dtstart,dtend,sendemail,showin,emailsent) values (".$data['idtopic'].",".$data['idperson'].",".$data['title'].",".$data['description'].",".$data['dtcreate'].",".$data['dtstart'].",".$data['dtend'].",".$data['sendemail'].",".$data['showin'].",".$data['emailsent'].")";

           //die($sql);
            return $this->db->Execute($sql);
        }
        else{
            return $this->insert("bbd_tbmessage",$data);
        }

	}
	
	public function updateWarning($data, $id){		

		$sql = "UPDATE bbd_tbmessage 
					SET idtopic = ".$data['idtopic'].", 
						title = ".$data['title'].", 
						description = ".$data['description'].", 
						dtstart = ".$data['dtstart'].", 
						dtend = ".$data['dtend'].", 
						sendemail = ".$data['sendemail'].", 
						showin = ".$data['showin']."
					WHERE idmessage = $id";
		$ret = $this->db->Execute($sql);
		if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }
	
	public function getTopicGroup($id){
        return $this->select("SELECT idgroup FROM bbd_topic_group WHERE idtopic = $id ");
    }
	
	public function selectTopics(){
		return $this->select("SELECT idtopic, title FROM bbd_topic");
	}
	
	public function selectTopic($id){
		return $this->select("SELECT idtopic, title, default_display, fl_emailsent, (select count(*) from bbd_topic_company WHERE idtopic = $id) + (select count(*) from bbd_topic_group WHERE idtopic = $id) as total FROM bbd_topic WHERE idtopic = $id");
	}
	
	public function updateTopic($data, $id){
        return $this->update("bbd_topic",$data,"idtopic = ".$id);
    }
	
	public function clearTopicGroup($id){
        return $this->delete("bbd_topic_group", "idtopic = ".$id);
    }
    
    public function clearTopicCompany($id){
        return $this->delete("bbd_topic_company", "idtopic = ".$id);
    }
	
	public function InsertID() {
        return $this->db->Insert_ID();
    }

    public function getTotalWarning($where = null)
    {

        $query =   "  SELECT
                              COUNT(a.idmessage) as total
                        FROM bbd_tbmessage a, bbd_topic b
                       WHERE a.idtopic = b.idtopic 
                      $where";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret->fields['total'];

    }

    public function getTopic($where = null, $order = null , $group = null , $limit = null )
    {

        $query =    "
                    SELECT
                      idtopic,
                      title,
                      default_display,
                      fl_emailsent
                    FROM bbd_topic
                    $where $order $group $limit
                    ";
        $ret = $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;
    }

    public function getGroups($where = null, $order = null , $group = null , $limit = null )
    {

        $query =    "
                    SELECT
                           tbg.idgroup,
                           tbp.name ,
                           tbg.idperson,
                           tbg.level as lvl,
                           tbg.status,
                           tbp2.name   as company
                      FROM hdk_tbgroup tbg,
                           tbperson tbp,
                           tbperson tbp2
                     WHERE tbg.idperson = tbp.idperson
                       AND tbp2.idperson = tbg.idcustomer
                    $where $order $group $limit
                    ";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;
    }

    public function getTotalTopic($where = null)
    {

        $query =   "SELECT
                              COUNT(idtopic) as total
                      FROM bbd_topic 
                      $where";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret->fields['total'];

    }

    public function getTopicGroups($idtopic,$idgroup)
    {

        $query =    "
                    SELECT
                           idtopicgroup
                      FROM bbd_topic_group
                     WHERE idtopic = $idtopic
                       AND idgroup = $idgroup
                    ";
        $ret = $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;
    }

    public function getTopicCorporations($idtopic,$idcompany)
    {

        $query =    "
                    SELECT
                           idtopiccompany
                      FROM bbd_topic_company
                     WHERE idtopic = $idtopic
                       AND idcompany = $idcompany
                    ";
        $ret = $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;
    }

    public function getCountTopicGroup($idtopic)
    {

        $query =    "
                    SELECT COUNT(idtopicgroup) as total FROM bbd_topic_group
                     WHERE idtopic = $idtopic
                    ";
        $ret = $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret->fields['total'];
    }

    public function getCountTopicCorp($idtopic)
    {

        $query =    "
                    SELECT COUNT(idtopiccompany) as total FROM bbd_topic_company
                     WHERE idtopic = $idtopic
                    ";
        $ret = $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret->fields['total'];
    }
  
  
  
  

	
		
   
}
?>
