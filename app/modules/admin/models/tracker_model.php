<?php
class tracker_model extends Model{


		
    public function insertEmail($idmodule,$from,$to,$subject,$body){
        $query = "	INSERT INTO tbemail (idmodule, `from`, `to`, subject, body)
					VALUES
					  (
						$idmodule,
						'$from',
						'$to',
						'$subject',
						'$body'
					  ) ;
					";

        $ret = $this->db->Execute($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "DB ERROR: " . $this->db->ErrorMsg() . " QUERY: " . $query;
            $this->error($sError);
            return false;
        } else {
            return $this->db->Insert_ID();;
        }

    }
	


	

  
  
  
  

	
		
   
}
?>
