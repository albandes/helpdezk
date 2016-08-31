<?php
class downloads_model extends Model{
    public function getDownloads($where, $order, $limit){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
          $ret = $this->select("select
                                  down.iddownload,             
                                  down.name,
                                  down.description,
                                  down.file_name,
                                  down.date as dt,
                                  down.version_description,
                                  cat.category,
                                  cat.iddownloadcategory,
                                  down.status
                                from hdk_tbdownload as down,
                                hdk_tbdownload_category as cat
                                where cat.iddownloadcategory = down.iddownloadcategory $where $order $limit");
        } elseif ($database == 'oci8po') {
          $limit = str_replace('LIMIT', "", $limit);
          $p     = explode(",", $limit);
          $start = $p[0]+1; 
          $end   = $p[0]+$p[1]; 
          $query =  "
                SELECT   *
                  FROM   (SELECT                                          
                        a  .*, ROWNUM rnum
                      FROM   (select
                                down.iddownload,             
                                down.name,
                                down.description,
                                down.file_name,
                                down.date_ as dt,
                                down.version_description,
                                cat.category,
                                cat.iddownloadcategory,
                                down.status
                              from hdk_tbdownload down,
                              hdk_tbdownload_category cat
                              where cat.iddownloadcategory = down.iddownloadcategory $where $order) a
                       WHERE   ROWNUM <= $end)
                 WHERE   rnum >= $start     
                ";
          $ret = $this->select($query);
        }
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getDownloadData($id){
    	$database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
			$date = "down.date as dt";
		}elseif ($database == 'oci8po') {
			$date = "down.date_ as dt";
		}
        return $this->select("select
		  down.iddownload,             
		  down.name,
		  down.restricted,
		  down.description,
		  down.file_name,
		  $date,
		  down.version_description,
		  down.instruction,
		  down.file_url,
		  down.download_file_name as downloadname,
		  cat.category,
		  cat.iddownloadcategory
		from hdk_tbdownload down,
		hdk_tbdownload_category cat
		where cat.iddownloadcategory = down.iddownloadcategory and down.iddownload = '$id'");
    }
    
    public function countDownloads($where = NULL){
        return $this->select("select count(iddownload) as total from hdk_tbdownload $where");
    }
    public function getDownloadWithCategories(){
        return $this->select("select
  down.name,
  down.description,
  down.date,
  down.iddownload,
  down.version_description,
  cat.category as category,
  cat.iddownloadcategory as idcategory,
  down.iddownloadcategory as cat_pai,
  down.instruction,
  down.file_name,
  down.download_file_name
from hdk_tbdownload_category as cat,
  hdk_tbdownload as down
where down.iddownloadcategory = cat.iddownloadcategory");
    }
    public function countMaxCategories(){
        $ret = $this->select("select max(iddownloadcategory) as total from hdk_tbdownload_category");
        return $ret->fields['total'];
    }
    public function selectCategories(){
        return $this->select("select category, iddownloadcategory from hdk_tbdownload_category");
    }
    
     public function savefile($NOM_FILE) {
        return $this->db->Execute("INSERT INTO hdk_tbdownload_files (filename) VALUES ('$NOM_FILE')");
    }

    public function maxfile() {
        if ($database == 'mysqlt') {
           $ret = $this->select("SELECT Auto_increment as COD FROM information_schema.tables WHERE table_name = 'hdk_tbdownload' AND table_schema = DATABASE()");
        } elseif ($database == 'oci8po') {
            $sel = $this->select("SELECT hdk_tbdownload_iddownload_seq.nextval as COD FROM dual");  
        }
        return $sel;
    }

    public function searchfile() {
        session_start();
        return $this->select("SELECT iddownload from hdk_tbdownload WHERE iddownload in (" . $_SESSION["SES_COD_ATTACHMENT"] . ") ");
    }

    public function searchfilename($COD_ATT) {
        session_start();
        $sel = $this->select("SELECT filename FROM hdk_tbdownload WHERE iddownloadfile = " . $COD_ATT);
        return $sel;
    }

    public function delfile($COD_ATT) {
        return $this->db->Execute("DELETE from hdk_tbdownload where iddownloadfile = " . $COD_ATT);
    }
    
    public function insertDownloadCategory($name){
        return $this->db->Execute("insert into hdk_tbdownload_category (category) values ('$name')"); 
    }
    
    public function selectMaxDownCategory(){
        $ret = $this->db->Execute("select max(iddownloadcategory) as total from hdk_tbdownload_category"); 
        return $ret->fields['total']; 
    }
    
    public function insertDownload($cat, $title, $desc, $filename, $date, $downloadfilename, $file_url, $version, $restrict, $instruction){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
           $ret = $this->select("insert into hdk_tbdownload (iddownloadcategory,name,description,file_name,date,download_file_name,file_url,version_description,restricted,instruction) values ('$cat','$title','$desc','$filename',$date,'$downloadfilename','$file_url','$version','$restrict','$instruction')");
        } elseif ($database == 'oci8po') {
            if(!$filename) $filename = " ";
           if(!$downloadfilename) $downloadfilename = " ";            
           $query =  "INSERT INTO hdk_tbdownload 
                                (iddownloadcategory, 
                                 name, 
                                 description, 
                                 file_name, 
                                 DATE_, 
                                 download_file_name, 
                                 file_url, 
                                 version_description, 
                                 RESTRICTED, 
                                 instruction) 
                    VALUES      ('$cat', 
                                 '$title', 
                                 RAWTOHEX('$desc'), 
                                 '$filename', 
                                 $date, 
                                 '$downloadfilename', 
                                 '$file_url', 
                                 '$version', 
                                 '$restrict', 
                                 RAWTOHEX('$instruction')) ";
            $ret = $this->select($query);
        }
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function getInstruction($id){
        return $this->db->Execute("select instruction from hdk_tbdownload where iddownload = '$id'");
    }
    
    public function updateDownload($cat, $title, $desc, $filename, $date, $downloadfilename, $url, $version, $restrict, $instruction, $id){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
           $ret = $this->select("update hdk_tbdownload set iddownloadcategory = '$cat', name = '$title',description = '$desc',file_name = '$filename',date = $date,download_file_name = '$downloadfilename',file_url = '$url',version_description = '$version',restricted = '$restrict',instruction = '$instruction' where iddownload =  '$id'");
        } elseif ($database == 'oci8po') {
            if(!$filename) $filename = " ";
           if(!$downloadfilename) $downloadfilename = " ";
           $query =  "UPDATE hdk_tbdownload
                        SET
                              iddownloadcategory = '$cat',
                              name = '$title',
                              description = RAWTOHEX('$desc'),
                              file_name = '$filename',
                              date_ = $date,
                              download_file_name = '$downloadfilename',
                              file_url = '$url',
                              version_description = '$version',
                              restricted = '$restrict',
                              instruction = RAWTOHEX('$instruction')
                        WHERE iddownload =  '$id'";
            $ret = $this->select($query);
        }
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;

    }
    
    public function downloadDeactivate($id) {
        return $this->db->Execute("UPDATE hdk_tbdownload set status = 'N' where iddownload in ($id)");
    }

    public function downloadActivate($id) {
        return $this->db->Execute("UPDATE hdk_tbdownload set status = 'A' where iddownload in ($id)");
    }
    
    public function downloadDelete($id) {
        return $this->db->Execute("delete from hdk_tbdownload where iddownload='$id'");
    }
    
    public function updateFilename($downloadfilename, $id, $filename){
        return $this->db->Execute("update hdk_tbdownload set download_file_name = '$downloadfilename', file_name = '$filename', file_url = '' where iddownload =  '$id'");
    }
}
?>
