<?php

    if(class_exists('Model')) {
        class dlgpgroup_model extends Model {}
    } elseif(class_exists('cronModel')) {
        class dlgpgroup_model extends cronModel {}
    } elseif(class_exists('apiModel')) {
        class dlgpgroup_model extends apiModel {}
    }

    class lgpgroup_model extends dlgpgroup_model{
        
        public $database;
    
        public function __construct()
        {
            parent::__construct();
            $this->database = $this->getConfig('db_connect');
    
    
        }
        
        public function getGroup($where=null,$order=null,$limit=null,$group=null) {
            $sql = "SELECT idgroup, b.idperson, b.name group_name,c.idperson idcompany, c.name company_name, a.status, b.idtypeperson
            FROM lgp_tbgroup a, tbperson b, tbperson c
            WHERE a.idperson = b.idperson
            AND a.idcompany = c.idperson $where $order $limit $group"; //echo "{$sql}\n";
    
            $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

        public function getTypePersonName($typename) {
            $sql = "SELECT `idtypeperson` FROM tbtypeperson WHERE `name` = '{$typename}'"; //echo "{$sql}\n";
    
            $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }


        public function insertGroup($typeID, $personName, $companyID){

            $sql = "INSERT INTO tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,name)
            VALUES (3,$typeID,1,1,'$personName')"; //echo $sql;
            
            $ret = $this->db->Execute($sql); //echo "{$sql}\n";
            
            if($ret)
                return $this->insertGroupSecondStep($this->db->Insert_ID(), $companyID);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

        public function insertGroupSecondStep($personID, $companyID){

            $sql = "INSERT INTO lgp_tbgroup (idperson,idcompany)
            VALUES ($personID,$companyID)"; //echo $sql

            $ret = $this->db->Execute($sql);

            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret, 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

        }

        public function updateGroup($companyID,$newName, $groupID){

            $sql = "UPDATE tbperson SET `name` = '$newName' WHERE `idperson` = (SELECT `idperson` FROM lgp_tbgroup WHERE `idgroup` = $groupID)"; //echo $sql;

            $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return $this->updateGroupSecondStep($groupID, $companyID);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');


        }

        public function updateGroupSecondStep($groupID, $companyID){

            $sql = "UPDATE lgp_tbgroup SET `idcompany` = $companyID WHERE `idgroup` = $groupID"; //echo $sql;

            $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

        }

        public function statusGroup($groupID,$newStatus){

            $sql = "UPDATE lgp_tbgroup SET `status` = '$newStatus' WHERE `idgroup` = $groupID"; //echo $sql;

            $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

        }

        public function selectGroup($where = NULL, $order = NULL, $limit = NULL) {
            
            $sql = "SELECT idgroup, b.name group_name
            FROM lgp_tbgroup a, tbperson b, tbperson c
            WHERE a.idperson = b.idperson
            AND a.idcompany = c.idperson $where $order $limit"; //echo $sql;

            $ret = $this->db->Execute($sql);
            if(!$ret) {
                $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg() . "<br>Query: " . $sql    ;
                $this->error($sError);
                return false;
            }
            return $ret;
        }

        public function selectPeoples($where, $order){

            $query = "SELECT `idperson`, `name` FROM tbperson $where $order"; //echo $sql;

            $ret = $this->db->Execute($query); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

        }

        public function checkPeopleGroup($idperson, $idgroup){

            $query = "SELECT ghp.idperson, a.`name` 
                    FROM lgp_tbgroup_has_person ghp, tbperson a, tbperson grp, lgp_tbgroup g
                   WHERE a.idperson = ghp.idperson
                     AND g.idgroup = ghp.idgroup
                     AND grp.idperson = g.idperson
                     AND ghp.idperson = $idperson
                     AND g.idgroup = $idgroup"; //echo $query;

            $ret = $this->db->Execute($query); 
                
            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');
 
        }

        public function groupPersonInsert($idgroup, $idperson){

            $query = "INSERT INTO lgp_tbgroup_has_person (`idgroup`, `idperson`) VALUES ($idgroup, $idperson)";

            $ret = $this->db->Execute($query);
    
            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');

        }

        public function groupPersonDelete($idgroup){

            $query = "DELETE FROM lgp_tbgroup_has_person WHERE `idgroup` = $idgroup";

            $ret = $this->db->Execute($query);

            if (!$ret) {
                $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
                $this->error($sError);
                return false;
            }
    
            return $ret;

        }
    }