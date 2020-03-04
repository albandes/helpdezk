<?php
if(class_exists('Model')) {
    class DynamicTrigger_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicTrigger_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicTrigger_model extends apiModel {}
}

class trigger_model extends DynamicTrigger_model
{

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function insertAlertSended($code_request, $idTrigger,$idStatus, $idDeliveryProtocol,$dateTime, $send,$observation)
    {
        $vSQL = "
                INSERT INTO hdk_tbtrigger_alerts (
                  idstatus,
                  idtrigger,
                  iddeliveryprotocol,
                  code_request,
                  date_out,
                  send,
                  observation
                )
                VALUES
                  (
                    '".$idStatus."',
                    '".$idTrigger." ',
                    '".$idDeliveryProtocol."',
                    '".$code_request."',
                    '".$dateTime."',
                    '".$send."',
                    '".$observation."'
                  ) ;
                ";

        $ret = $this->db->Execute($vSQL);
        if (!$ret) {
            $sError = $vSQL." File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }




}

