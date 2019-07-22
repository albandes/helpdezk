<?php
if(class_exists('Model')) {
    class DynamicDownload_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicDownload_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicDownload_model extends apiModel {}
}

class download_model extends DynamicTicket_model
{

    public function getDownloadNote($id){
        return $this->db->Execute("SELECT * FROM hdk_tbnote_attachment WHERE idnote_attachment = '$id'");
    }

    public function getDownloadRequest($id){
        return $this->db->Execute("SELECT * FROM hdk_tbrequest_attachment WHERE idrequest_attachment = '$id'");
    }

}