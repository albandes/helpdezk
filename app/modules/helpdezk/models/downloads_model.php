<?php
class downloads_model extends Model{
    public function getDownloads($where, $order, $limit){
        return $this->select("select
  down.iddownload,             
  down.name,
  down.description,
  down.file_name,
  down.date,
  down.version_description,
  cat.category,
  cat.iddownloadcategory,
  down.status
from hdk_tbdownload as down,
hdk_tbdownload_category as cat
where cat.iddownloadcategory = down.iddownloadcategory $where $order $limit");
    }
    
    public function getDownloadData($id){
        return $this->select("select
  down.iddownload,             
  down.name,
  down.description,
  down.file_name,
  down.version_description,
  down.instruction,
  down.file_url,
  down.download_file_name as downloadname,
  cat.category,
  cat.iddownloadcategory
from hdk_tbdownload as down,
hdk_tbdownload_category as cat
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
        $sel = $this->select("SELECT max(iddownload) as COD FROM hdk_tbdownload");
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
        return $this->db->Execute("insert into hdk_tbdownload_category (category) values ('$name');"); 
    }
    
    public function selectMaxDownCategory(){
        $ret = $this->db->Execute("select max(iddownloadcategory) as total from hdk_tbdownload_category"); 
        return $ret->fields['total']; 
    }
    
    public function insertDownload($cat, $title, $desc, $filename, $date, $downloadfilename, $url, $version, $restrict, $instruction){
        return $this->db->Execute("insert into hdk_tbdownload (iddownloadcategory,name,description,file_name,date,download_file_name,file_url,version_description,restricted,instruction) values ('$cat', '$title', '$desc', '$filename', '$date', '$downloadfilename', '$url', '$version', '$restrict', '$instruction')");
    }
    public function getInstruction($id){
        return $this->db->Execute("select instruction from hdk_tbdownload where iddownload = '$id'");
    }
    
    public function updateDownload($cat, $title, $desc, $filename, $date, $downloadfilename, $url, $version, $restrict, $instruction, $id){
        return $this->db->Execute("update hdk_tbdownload set iddownloadcategory = '$cat', name = '$title',description = '$desc',file_name = '$filename',date = '$date',download_file_name = '$downloadfilename',file_url = '$url',version_description = '$version',restricted = '$restrict',instruction = '$instruction' where iddownload =  '$id'");
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
    
    public function getDownloadFromCategory($id){
        return $this->db->Execute("select  down.name,
  down.description,
  down.date,
  down.iddownload,
  down.version_description,
  down.iddownloadcategory as cat_pai,
  down.instruction,
  down.file_name,
  down.download_file_name from hdk_tbdownload as down where iddownloadcategory = '$id'");
    }

	public function getDownloadNote($id){
		return $this->db->Execute("SELECT * FROM hdk_tbnote_attachment WHERE idnote_attachment = '$id'");
	}

public function getDownloadRequest($id){
		return $this->db->Execute("SELECT * FROM hdk_tbrequest_attachment WHERE idrequest_attachment = '$id'");
	}
}
?>
