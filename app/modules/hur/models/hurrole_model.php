<?php

    if(class_exists('Model')) {
        class dhurrole_model extends Model {}
    } elseif(class_exists('cronModel')) {
        class dhurrole_model extends cronModel {}
    } elseif(class_exists('apiModel')) {
        class dhurrole_model extends apiModel {}
    }

    class hurRole_model extends dhurrole_model{
        public $database;
    
        public function __construct()
        {
            parent::__construct();
            $this->database = $this->getConfig('db_connect');
    
    
        }
        
        public function getRole($where=null,$order=null,$limit=null,$group=null) {
            $sql = "SELECT idrole, a.idarea, b.description areaname, a.description rolename, a.status
            FROM hur_tbrole a, hur_tbarea b
            WHERE a.idarea = b.idarea $where $group $order $limit"; //echo "{$sql}\n";
    
            $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

        public function insertRole($area, $roleName){

            $sql = "INSERT INTO hur_tbrole (`idarea`, `description`, `status`) VALUES ($area, '$roleName', 'A')"; //echo $sql;
            
            $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

        public function updateRole($roleID, $areaID, $roleName){

            $sql = "UPDATE hur_tbrole SET `idarea` = $areaID, `description` = '$roleName' WHERE `idrole` = $roleID"; //echo $sql;

            $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');


        }

        public function statusRole($roleID,$newStatus){

            $sql = "UPDATE hur_tbrole SET `status` = '$newStatus' WHERE `idrole` = $roleID"; //echo $sql;

            $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

        }
    }