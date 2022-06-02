<?php

    if(class_exists('Model')) {
        class dynamicKnow_model extends Model {}
    } elseif(class_exists('cronModel')) {
        class dynamicKnow_model extends cronModel {}
    } elseif(class_exists('apiModel')) {
        class dynamicKnow_model extends apiModel {}
    }

    class acdknowledgearea_model extends dynamicKnow_model
    {


        public $database;

        public function __construct()
        {
            parent::__construct();
            $this->database = $this->getConfig('db_connect');
        }

        public function getKnowledgearea($where=null,$order=null,$limit=null,$group=null) {
            $sql = "SELECT `idareaconhecimento`,`descricao`,`descricaoabrev`,`cor`, `status` 
                    FROM acd_tbareaconhecimento
                    $where $group $order $limit"; //echo $sql;
        
            $ret = $this->db->Execute($sql); //echo "{$sql}\n";
        
            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

        public function insertKnowledgearea($descricao, $abrev) {

            $sql = "INSERT INTO acd_tbareaconhecimento (descricao, descricaoabrev) 
                    VALUES('$descricao', '$abrev')";

            $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

        public function updateKnowledgearea($acdID,$nome,$descabrev) {
            $sql = "UPDATE acd_tbareaconhecimento
                       SET `descricao` = '$nome',`descricaoabrev` = '$descabrev'
                     WHERE idareaconhecimento = $acdID";
    
            $ret = $this->db->Execute($sql); //echo "{$sql}\n";
    
            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

        public function statusKnowledge($areaID,$newStatus){

            $sql = "UPDATE acd_tbareaconhecimento SET `status` = '$newStatus' WHERE `idareaconhecimento` = $areaID"; //echo $sql;

            $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

            }

    }






?>