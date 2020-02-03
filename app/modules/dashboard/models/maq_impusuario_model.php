<?php
class maq_impusuario_model
{
    public function __construct($dbhost, $dbuser, $dbpass, $dbname) {
		//die($teste);
        //include 'includes/config/config.php';
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
	
    public function getPrinting($days,$limit) 
	{
		$sql = 	"
				select
				   user,
				   date(date),
				   sum(pages) as paginas,
				   (
					select
					sum(pages)
					from jobs_log
					WHERE DATE_SUB(CURDATE(),INTERVAL $days DAY) <= date 
					) as total
				from jobs_log
				WHERE DATE_SUB(CURDATE(),INTERVAL $days DAY) <= date 
				group by user
				order by paginas desc
				limit $limit
				";	
		return $this->select($sql);
    }

    public function getPrintingByDay($idperson,$days) 
	{
		$sql = 	"
				select
				   user,
				   date(date) as dtjob,
				   sum(pages*copies) as paginas
				from jobs_log
				WHERE DATE_SUB(CURDATE(),INTERVAL $days DAY) <= date 
					 and user = '$idperson'
				group by date(date)
				";	
		return $this->select($sql);
    }

}

?>


