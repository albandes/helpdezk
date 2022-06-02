<?php

//class cms_sourceforgecountrys_model extends Model {

class cms_sourceforgecountrys_model
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
	
    public function getDownloads($days,$limit) 
	{
		$sql = 	"
				select
				  a.idcountry,
				  COUNT(a.idcountry_downloads) as total
				from tbcountry_downloads a
				-- WHERE DATE_SUB(CURDATE(),INTERVAL $days DAY) <= a.date
				where a.date > '2012-09-27'
				group by a.idcountry
				order by total desc
				limit $limit
				";	
		return $this->select($sql);
    }

    public function getDownloadsByCountry($idcountry,$days) 
	{
		$sql = 	"
				select 
				a.date,
				b.name,
				COUNT(a.idcountry_downloads) as total
				from tbcountry_downloads a, tbcountry b
				-- WHERE DATE_SUB(CURDATE(),INTERVAL 180 DAY) <= a.date
				where a.date > '2012-09-27'
				and a.idcountry = b.idcountry 
				and a.idcountry = '$idcountry'
				group by a.date
				order by a.date asc
				";	
		return $this->select($sql);
    }

}

?>
