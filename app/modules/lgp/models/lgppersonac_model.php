<?php

    if(class_exists('Model')) {
        class dlgppersonac_model extends Model {}
    } elseif(class_exists('cronModel')) {
        class dlgppersonac_model extends cronModel {}
    } elseif(class_exists('apiModel')) {
        class dlgppersonac_model extends apiModel {}
    }

    class lgppersonac_model extends dlgppersonac_model{
        public $database;
    
        public function __construct()
        {
            parent::__construct();
            $this->database = $this->getConfig('db_connect');
    
    
        }
        
        public function getPersonac($where=null,$order=null,$limit=null,$group=null) {
            $sql = "SELECT a.idperson, a.idtypeperson, a.name personac_name, a.phone_number personac_telephone, 
                            a.cel_phone personac_cellphone, b.ssn_cpf personac_cpf, a.status
                      FROM tbperson a 
           LEFT OUTER JOIN tbnaturalperson b
                        ON a.idperson = b.idperson  
                    $where $group $order $limit"; //echo "{$sql}\n";

                return $this->selectPDO($sql);
        }

        public function getTypePersonName($typename) {
            $sql = "SELECT `idtypeperson` FROM tbtypeperson WHERE `name` = '{$typename}'"; //echo "{$sql}\n";
    
            return $this->selectPDO($sql);
        }

        public function insertPersonac($typepersonid, $personacName, $personacCPF, $personacTel, $personacCell){

            $sql = "INSERT INTO tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,name, phone_number, cel_phone)
            VALUES (:idtypelogin,:idtypeperson,:idnatureperson,:idtheme,:name, :phone_number, :cel_phone)"; //echo $sql;

            try{

                $this->BeginTransPDO();            
                $sth = $this->dbPDO->prepare($sql);

                $sth->execute(array(
                            ":idtypelogin"=>3,
                            ":idtypeperson"=>$typepersonid,
                            ":idnatureperson"=>1,
                            ":idtheme"=>1,
                            ":name"=>$personacName,
                            ":phone_number"=>$personacTel,
                            ":cel_phone"=>$personacCell
                            )
                        );

                $sql_ = "INSERT INTO tbnaturalperson (idperson,ssn_cpf)
                VALUES (:personID,:personCPF)"; //echo $sql

                $sth = $this->dbPDO->prepare($sql_);

                $sth->execute(array(
                    ":personID"=>$this->dbPDO->lastInsertId(),
                    ":personCPF"=>$personacCPF
                    )
                );

                $this->CommitTransPDO();

            }catch(PDOException $ex){
                $this->RollbackTransPDO();
                return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
            }

            return array("success"=>true,"message"=>"","data"=>"");
            
        
        }

        public function updatePersonac($personacID, $personacName,  $personacCPF, $personacTel, $personacCell){

            $sql = "UPDATE tbperson SET `name` = :name, `phone_number` = :phone_number , `cel_phone` = :cel_phone WHERE `idperson` = :id"; //echo $sql;

            try{

                $this->BeginTransPDO();            
                $sth = $this->dbPDO->prepare($sql);

                $sth->execute(array(
                            ":name"=>$personacName,
                            ":phone_number"=>$personacTel,
                            ":cel_phone"=>$personacCell,
                            ":id"=>$personacID
                            )
                        );

                $this->CommitTransPDO();

            }catch(PDOException $ex){
                $this->RollbackTransPDO();
                return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
            }
    
            return $this->updatePersonacSecondStep($personacID, $personacCPF);


        }

        public function updatePersonacSecondStep($personacID, $personacCPF){

            $sql = "UPDATE tbnaturalperson SET `ssn_cpf` = :personCPF WHERE `idperson` = :id"; //echo $sql;

            try{

                $this->BeginTransPDO();            
                $sth = $this->dbPDO->prepare($sql);

                $sth->execute(array(
                            ":personCPF"=>$personacCPF,
                            ":id"=>$personacID
                            )
                        );

                $this->CommitTransPDO();

            }catch(PDOException $ex){
                $this->RollbackTransPDO();
                return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
            }

            return array("success"=>true,"message"=>"","data"=>"");

        }

        public function statusPersonac($personacID,$newStatus){

            $sql = "UPDATE tbperson SET `status` = :newStatus WHERE `idperson` = :id"; //echo $sql;

            try{

                $this->BeginTransPDO();            
                $sth = $this->dbPDO->prepare($sql);

                $sth->execute(array(
                            ":newStatus"=>$newStatus,
                            ":id"=>$personacID
                            )
                        );

                $this->CommitTransPDO();

            }catch(PDOException $ex){
                $this->RollbackTransPDO();
                return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
            }
    
            return array("success"=>true,"message"=>"","data"=>"");

        }

    }