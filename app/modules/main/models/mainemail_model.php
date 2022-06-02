<?php
if(class_exists('Model')) {
    class DynamicMainEmail_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicMainEmail_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicMainEmail_model extends apiModel {}
}

class mainemail_model extends DynamicMainEmail_model
{

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');


    }

    public function getEmailCron($where)
    {
        $query =    "
                    SELECT
                       idemailcron,
                       idmodule,
                       code,
                       date_in,
                       date_out,
                       send,
                       tag
                    FROM tbemailcron
                    $where
                    ";
        $ret = $this->db->Execute($query);
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');
    }

    public function updateEmailCron($set)
    {
        $query =    "
                    UPDATE tbemailcron
                    $set
                    ";
        $ret = $this->db->Execute($query);
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');
    }

}