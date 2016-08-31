<?php

class auxdb_model extends Model {

    public function __construct()
    {
        $dbAuxData = $this->GetDbAuxData();

        if($dbAuxData->fields['amt'] != 0)
        {
            if ($dbAuxData->fields['dbtype'] == 'mysql') {
                if(empty($dbAuxData->fields['dbport'])) {
                    $hostname = $dbAuxData->fields['dbhostname'] ;
                } else {
                    $hostname = $dbAuxData->fields['dbhostname'] . ':' . $dbAuxData->fields['dbport'] ;
                }
                $this->dbAux = &ADONewConnection('mysql');
                if (!$this->dbAux->Connect($hostname,$dbAuxData->fields['dbusername'],$dbAuxData->fields['dbpassword'],$dbAuxData->fields['dbname'])
                ) {
                    error_reporting(E_ALL);
                    die("<br>Error connecting to Auxiliary Database: " . "  " . $this->dbAux->ErrorNo() . " - " . $this->dbAux->ErrorMsg());
                }
            }
        }

    }

    public function GetDbAuxData()
    { die('aqui');
        $dbCommon = new common();
        return $dbCommon->getAuxDB();
    }

    public function testAux()
    {
        if(!$this->dbAux)
            die("Arq: " . __FILE__ . " Line: " . __LINE__ . " , Auxiliary Database not open !!!");

        $query =    "
                    SELECT
                      CoThP,
                      CoTurma,
                      CoProfessor,
                      Ano
                    FROM
                      extramq.turma_has_professor
                    LIMIT 0, 10
                    ";

        $ret = $this->dbAux->Execute($query) ;
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

}

?>
