<?php

    if(class_exists('Model')) {
        class dhurarea_model extends Model {}
    } elseif(class_exists('cronModel')) {
        class dhurarea_model extends cronModel {}
    } elseif(class_exists('apiModel')) {
        class dhurarea_model extends apiModel {}
    }

    class hurarea_model extends dhurarea_model{
        public $database;
    
        public function __construct()
        {
            parent::__construct();
            $this->database = $this->getConfig('db_connect');
    
    
        }

        //PARA AS OPERAÇÕES SEGUINTES DEVE SER CRIADA A TABELA DO PROGRAMA
        
        public function getArea($where=null,$order=null,$limit=null,$group=null) {
            $sql = "SELECT `idarea`, `description`, `status` FROM hur_tbarea $where $group $order $limit"; //echo "{$sql}\n";
    
            $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

        public function insertArea($areaName){

            $sql = "INSERT INTO hur_tbarea (`description`, `status`) VALUES ('$areaName', 'A')"; //echo $sql;
            
            $ret = $this->db->Execute($sql); //echo "{$sql}\n";
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

        public function updateArea($areaID,$areaName){

            $sql = "UPDATE hur_tbarea SET `description` = '$areaName' WHERE `idarea` = $areaID"; //echo $sql;

            $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');


        }

        public function statusArea($areaID,$newStatus){
            //echo $areaID, $newStatus; 

            $sql = "UPDATE hur_tbarea SET `status` = '$newStatus' WHERE `idarea` = $areaID"; //echo $sql;

            $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

        }
    }