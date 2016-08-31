<?php
class maq_coletora_model
{
    public function __construct($dbhost, $dbuser, $dbpass, $dbname) {
        $this->db = NewADOConnection('mysqlt');
        if (!$this->db->Connect($dbhost, $dbuser, $dbpass, $dbname)) 
		{
			print "<br>Error connecting to database: " . $db->ErrorNo() . " - " . $db->ErrorMsg();
			die();
        }
    }
    public function select($sql){
        $this->db->SetFetchMode(ADODB_FETCH_ASSOC); 
        $exec = $this->db->Execute($sql); 
        return $exec;
    }

    public function getBilhetesColetora($days) {
		$sql = 	"
				select
				   dtestatcoletora,
				   valor
				from tbestatcoletora
				where date(dtestatcoletora) >= DATE_ADD(date(NOW()), INTERVAL - $days DAY)
					 and date(dtestatcoletora) <= DATE_ADD(date(NOW()), INTERVAL - 1 DAY)
				order by dtestatcoletora desc
				";
		return $this->select($sql);
    }

}

?>
