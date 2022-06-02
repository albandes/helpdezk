<?php

    if(class_exists('Model')) {
        class dynamicSubject_model extends Model {}
    } elseif(class_exists('cronModel')) {
        class dynamicSubject_model extends cronModel {}
    } elseif(class_exists('apiModel')) {
        class dynamicSubject_model extends apiModel {}
    }

    class acdsubject_model extends dynamicSubject_model
    {


        public $database;

        public function __construct()
        {
            parent::__construct();
            $this->database = $this->getConfig('db_connect');
        }
    
        public function getSubject($where=null,$order=null,$limit=null,$group=null) {                       
            $sql = "SELECT `iddisciplina`, nome AS `nome`,`sigla`, a.`idareaconhecimento`, b.descricao AS `area`, a.status
            FROM acd_tbdisciplina a LEFT OUTER JOIN acd_tbareaconhecimento b ON a.idareaconhecimento = b.idareaconhecimento
                    $where $group $order $limit"; //echo $sql;
        
            $ret = $this->db->Execute($sql); //echo "{$sql}\n";
        
            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

        public function insertSubject($nome, $idarea, $sigla) {

            $sql = "INSERT INTO acd_tbdisciplina (`iddisciplina`, `nome`, `sigla`, `idareaconhecimento`) 
                    VALUES(DEFAULT, '$nome', '$sigla', $idarea)";

            $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

        public function updateSubject($subID,$nome,$sigla,$area) {
            $sql = "UPDATE acd_tbdisciplina
                       SET `nome` = '$nome',`sigla` = '$sigla', `idareaconhecimento` = '$area'
                     WHERE iddisciplina = $subID";
    
            $ret = $this->db->Execute($sql); //echo "{$sql}\n";
    
            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

        public function statusSubject($subID,$newStatus){
            //echo $areaID, $newStatus; 

            $sql = "UPDATE acd_tbdisciplina SET `status` = '$newStatus' WHERE `iddisciplina` = $subID"; //echo $sql;

            $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

        }

    }






?>