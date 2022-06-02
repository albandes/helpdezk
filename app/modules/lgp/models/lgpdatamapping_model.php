<?php
if(class_exists('Model')) {
    class DynamicDataMapping_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicDataMapping_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicDataMapping_model extends apiModel {}
}

class lgpdatamapping_model extends DynamicDataMapping_model
{

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function getDataMapping($where=null,$order=null,$limit=null) {
        $sql = "SELECT a.iddado, a.nome, compartilhado, a.idtipotitular, b.nome tipotitular, a.idtipodado, c.nome tipo, 
                        GROUP_CONCAT(DISTINCT k.idfinalidade ORDER BY k.nome) finalidadeids, GROUP_CONCAT(DISTINCT k.nome ORDER BY k.nome) finalidade,
                        GROUP_CONCAT(DISTINCT m.idformatocoleta ORDER BY m.nome) formatoids, GROUP_CONCAT(DISTINCT m.nome ORDER BY m.nome) formato,
                        GROUP_CONCAT(DISTINCT l.idformacoleta ORDER BY l.nome) formaids, GROUP_CONCAT(DISTINCT l.nome ORDER BY l.nome) forma,
                        GROUP_CONCAT(DISTINCT j.idbaselegal ORDER BY j.nome) baseids, GROUP_CONCAT(DISTINCT j.nome ORDER BY j.nome) base,
                        GROUP_CONCAT(DISTINCT i.idarmazenamento ORDER BY i.nome) armazenamentoids, GROUP_CONCAT(DISTINCT i.nome ORDER BY i.nome) armazenamento
                  FROM lgp_tbdado a
                  JOIN lgp_tbtipotitular b
                    ON a.idtipotitular = b.idtipotitular
                  JOIN lgp_tbtipodado c
                    ON a.idtipodado = c.idtipodado
                  JOIN lgp_tbdado_has_armazenamento d
                    ON a.iddado = d.iddado
                  JOIN lgp_tbdado_has_baselegal e
                    ON a.iddado = e.iddado
                  JOIN lgp_tbdado_has_finalidade f
                    ON a.iddado = f.iddado
                  JOIN lgp_tbdado_has_formacoleta g
                    ON a.iddado = g.iddado
                  JOIN lgp_tbdado_has_formatocoleta h
                    ON a.iddado = h.iddado
                  JOIN lgp_tbarmazenamento i
                    ON d.idarmazenamento = i.idarmazenamento
                  JOIN lgp_tbbaselegal j
                    ON e.idbaselegal = j.idbaselegal
                  JOIN lgp_tbfinalidade k
                    ON f.idfinalidade = k.idfinalidade
                  JOIN lgp_tbformacoleta l
                    ON g.idformacoleta = l.idformacoleta
                  JOIN lgp_tbformatocoleta m
                    ON h.idformatocoleta = m.idformatocoleta
                $where
              GROUP BY a.iddado
                $order $limit";

        return $this->selectPDO($sql); 
    }

    public function getHolderType($where=null,$order=null,$group=null,$limit=null) {
        $sql = "SELECT idtipotitular, nome FROM lgp_tbtipotitular
                $where $group $order $limit"; //echo "$sql\n";

        return $this->selectPDO($sql);
    }

    public function getType($where=null,$order=null,$group=null,$limit=null) {
        $sql = "SELECT idtipodado, nome FROM lgp_tbtipodado
                $where $group $order $limit";

        return $this->selectPDO($sql);
    }

    public function getPurpose($where=null,$order=null,$group=null,$limit=null) {
        $sql = "SELECT idfinalidade, nome FROM lgp_tbfinalidade
                $where $group $order $limit";
        //echo"{$sql}\n";
        return $this->selectPDO($sql);
    }

    public function getFormat($where=null,$order=null,$group=null,$limit=null) {
        $sql = "SELECT idformatocoleta, nome FROM lgp_tbformatocoleta
                $where $group $order $limit";

        return $this->selectPDO($sql);
    }

    public function getCollectForm($where=null,$order=null,$group=null,$limit=null) {
        $sql = "SELECT idformacoleta, nome FROM lgp_tbformacoleta
                $where $group $order $limit";
        
        return $this->selectPDO($sql);
    }

    public function getLegalGround($where=null,$order=null,$group=null,$limit=null) {
        $sql = "SELECT idbaselegal, nome FROM lgp_tbbaselegal
                $where $group $order $limit";
        
        return $this->selectPDO($sql);
    }

    public function getStorage($where=null,$order=null,$group=null,$limit=null) {
        $sql = "SELECT idarmazenamento, nome FROM lgp_tbarmazenamento
                $where $group $order $limit";

        return $this->selectPDO($sql);
    }

    public function getPerson($where=null,$order=null,$group=null,$limit=null) {
        $sql = "SELECT idperson, `name` FROM tbperson
                $where $group $order $limit";

        return $this->selectPDO($sql);
    }

    public function getSharedWith($where=null,$order=null,$group=null,$limit=null) {
        $sql = "SELECT idperson, `name` FROM tbperson
                $where $group $order $limit";
        
        return $this->selectPDO($sql);
    }

    /**
     * Grava o dado mapeado
     *
     * @param  int      $classificationID   ID da classificação do dado mapeado
     * @param  string   $dataName           Nome do dado mapeado
     * @param  int      $typeID             ID do tipo do dado mapeado
     * @param  string   $shared             Indica se o dado mapeado é compartilhado ou não
     * @return array    Contendo os parametros "success" (true/false), "message" (para erros), "id" ID do dado mapeado inserido
     */
    public function insertDataMap($holderTypeID,$dataName,$typeID,$shared) {
        $sql = "INSERT INTO lgp_tbdado(idtipotitular,nome,idtipodado,compartilhado) 
                  VALUES(:holderTypeID,:dataName,:typeID,:shared)";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":holderTypeID",$holderTypeID);
            $sth->bindParam(":dataName",$dataName);
            $sth->bindParam(":typeID",$typeID);
            $sth->bindParam(":shared",$shared);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbdado'));
    }

    /**
     * Grava vínculo do dado com a finalidade
     *
     * @param  int $dataID ID do dado mapeado
     * @param  int $purposeID ID da finalidade do dado mapeado
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "data" (info do execute)
     */
    public function insertDataPurpose($dataID,$purposeID) {
        $sql = "INSERT INTO lgp_tbdado_has_finalidade(iddado,idfinalidade) 
                  VALUES(:dataID,:purposeID)";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":dataID"=>$dataID,":purposeID"=>$purposeID));
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }
    
    /**
     * Grava vínculo do dado com o formato de coleta
     *
     * @param  int $dataID ID do dado mapeado
     * @param  int $formatID ID do formato de coleta
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "data" (info do execute)
     */
    public function insertDataFormat($dataID,$formatID) {
        $sql = "INSERT INTO lgp_tbdado_has_formatocoleta(iddado,idformatocoleta) 
                  VALUES(:dataID,:formatID)";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":dataID"=>$dataID,":formatID"=>$formatID));
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    /**
     * Grava vínculo do dado com a forma de coleta
     *
     * @param  int $dataID ID do dado mapeado
     * @param  int $collectID ID da forma de coleta
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "data" (info do execute)
     */
    public function insertDataCollectForm($dataID,$collectID) {
        $sql = "INSERT INTO lgp_tbdado_has_formacoleta(iddado,idformacoleta) 
                  VALUES($dataID,$collectID)";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":dataID"=>$dataID,":formatID"=>$formatID));
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    /**
     * Grava vínculo do dado com a base legal
     *
     * @param  int $dataID ID do dado mapeado
     * @param  int $legalGroundID ID da base legal
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "data" (info do execute)
     */
    public function insertDataLegalGround($dataID,$legalGroundID) {
        $sql = "INSERT INTO lgp_tbdado_has_baselegal(iddado,idbaselegal) 
                  VALUES(:dataID,:legalGroundID)";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":dataID"=>$dataID,":legalGroundID"=>$legalGroundID));
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    /**
     * Grava vínculo do dado com o armazenamento
     *
     * @param  int $dataID ID do dado mapeado
     * @param  int $storageID ID armazenamento do dado (BD Intranet, arquivos impressos, etc)
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "data" (info do execute)
     */
    public function insertDataStorage($dataID,$storageID) {
        $sql = "INSERT INTO lgp_tbdado_has_armazenamento(iddado,idarmazenamento) 
                  VALUES(:dataID,:storageID)";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":dataID"=>$dataID,":storageID"=>$storageID));
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    /**
     * Grava vínculo do dado com a pessoa que tem acesso ao dado
     *
     * @param  int $dataID - ID do dado mapeado
     * @param  int $personID - ID do usuario que tem acesso ao dado
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "data" (info do execute)
     */
    public function insertDataPerson($dataID,$personID,$typeID) {
        $sql = "INSERT INTO lgp_tbdado_has_person(iddado,idperson,`type`) 
                  VALUES(:dataID,:personID,:typeID)";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":dataID"=>$dataID,":personID"=>$personID,":typeID"=>$typeID));
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    /**
     * Grava vínculo do dado com a pessoa com quem é compartilhado o dado
     *
     * @param  int $dataID - ID do dado mapeado
     * @param  int $operatorID - ID da pessoa/instituição/empresa com que é compartilhado
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "data" (info do execute)
     */
    public function insertSharedWith($dataID,$operatorID) {
        $sql = "INSERT INTO lgp_tbdado_has_operador(iddado,idperson) 
                  VALUES(:dataID,:operatorID)";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":dataID"=>$dataID,":operatorID"=>$operatorID));
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }
    
    /**
     * deleteDataBind
     *
     * @param  mixed $dataID - ID do dado mapeado 
     * @param  mixed $table - Nome da table onde será removida a informação
     * @return void
     */
    public function deleteDataBind($dataID,$table) {
        $sql = "DELETE FROM {$table} WHERE iddado = :dataID";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":dataID",$dataID);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    public function updateDataMap($dataID,$holderTypeID,$dataName,$typeID,$shared) {
        $sql = "UPDATE lgp_tbdado
                   SET idtipotitular = :holderTypeID,
                        nome = :dataName,
                        idtipodado = :typeID,
                        compartilhado = :shared
                 WHERE iddado = :dataID";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":holderTypeID",$holderTypeID);
            $sth->bindParam(":dataName",$dataName);
            $sth->bindParam(":typeID",$typeID);
            $sth->bindParam(":shared",$shared);
            $sth->bindParam(":dataID",$dataID);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    /**
     * Grava a classificação do dado mapeado
     *
     * @param  string $classificationName Nome da classificação do dado mapeado
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "id" (ID da classificação inserida)
     */
    public function insertHolderType($holderTypeName) {
        $sql = "INSERT INTO lgp_tbtipotitular (nome) VALUES (:name)";
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":name"=>$holderTypeName));
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbtipotitular'));
    }

    /**
     * Grava o tipo do dado mapeado
     *
     * @param  string $typeName Nome do tipo do dado mapeado
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "id" (ID do tipo inserido)
     */
    public function insertType($typeName) {
        $sql = "INSERT INTO lgp_tbtipodado (nome) VALUES (:name)";
        try{            
            $this->BeginTransPDO();
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":name"=>$typeName));
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbtipodado'));
    }

    public function getDataMap($where=null,$order=null,$group=null,$limit=null) {
        $sql = "SELECT iddado, idtipotitular, idtipodado, nome, compartilhado FROM lgp_tbdado
                $where $group $order $limit"; //echo "$sql\n";

        return $this->selectPDO($sql);
    }

    /**
     * Grava a finalidade do dado mapeado
     *
     * @param  string $purposeName Nome da finalidade do dado mapeado
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "id" (ID do tipo inserido)
     */
    public function insertPurpose($purposeName) {
        $sql = "INSERT INTO lgp_tbfinalidade (nome) VALUES (:name)";
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":name"=>$purposeName)); //echo "{$sql}\n";
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbfinalidade'));
    }

    public function getLinkPurpose($dataID,$purposeID) {
        $sql = "SELECT iddado, idfinalidade FROM lgp_tbdado_has_finalidade
                 WHERE iddado = {$dataID} AND idfinalidade = {$purposeID}"; //echo "$sql\n";

        return $this->selectPDO($sql);
    }

    /**
     * Grava o formato de coleta do dado mapeado
     *
     * @param  string $formatName Nome do formato de coleta do dado mapeado
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "id" (ID do tipo inserido)
     */
    public function insertFormat($formatName) {
        $sql = "INSERT INTO lgp_tbformatocoleta (nome) VALUES (:name)";
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":name"=>$formatName)); //echo "{$sql}\n";
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbformatocoleta'));
    }

    public function getLinkFormat($dataID,$formatID) {
        $sql = "SELECT iddado, idformatocoleta FROM lgp_tbdado_has_formatocoleta
                 WHERE iddado = {$dataID} AND idformatocoleta = {$formatID}"; //echo "$sql\n";

        return $this->selectPDO($sql);
    }

    /**
     * Grava a forma de coleta do dado mapeado
     *
     * @param  string $formCollectName Nome da forma de coleta do dado mapeado
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "id" (ID do tipo inserido)
     */
    public function insertFormCollect($formCollectName) {
        $sql = "INSERT INTO lgp_tbformacoleta (nome) VALUES (:name)";
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":name"=>$formCollectName)); //echo "{$sql}\n";
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbformacoleta'));
    }

    public function getLinkFormCollect($dataID,$formCollectID) {
        $sql = "SELECT iddado, idformacoleta FROM lgp_tbdado_has_formacoleta
                 WHERE iddado = {$dataID} AND idformacoleta = {$formCollectID}"; //echo "$sql\n";

        return $this->selectPDO($sql);
    }

    /**
     * Grava a base legal do dado mapeado
     *
     * @param  string $legalGroundName Nome da forma de coleta do dado mapeado
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "id" (ID do tipo inserido)
     */
    public function insertLegalGround($legalGroundName) {
        $sql = "INSERT INTO lgp_tbbaselegal (nome) VALUES (:name)";
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":name"=>$legalGroundName)); //echo "{$sql}\n";
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbbaselegal'));
    }

    public function getLinkLegalGround($dataID,$legalGroundID) {
        $sql = "SELECT iddado, idbaselegal FROM lgp_tbdado_has_baselegal
                 WHERE iddado = {$dataID} AND idbaselegal = {$legalGroundID}"; //echo "$sql\n";

        return $this->selectPDO($sql);
    }

    /**
     * Grava o armazenamento do dado mapeado
     *
     * @param  string $storageName Nome do armazenamento do dado mapeado
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "id" (ID do tipo inserido)
     */
    public function insertStorage($storageName) {
        $sql = "INSERT INTO lgp_tbarmazenamento (nome) VALUES (:name)";
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":name"=>$storageName)); //echo "{$sql}\n";
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbbaselegal'));
    }

    public function getLinkStorage($dataID,$storageID) {
        $sql = "SELECT iddado, idarmazenamento FROM lgp_tbdado_has_armazenamento
                 WHERE iddado = {$dataID} AND idarmazenamento = {$storageID}"; //echo "$sql\n";

        return $this->selectPDO($sql);
    }

    public function getLgpTypePerson($typeName) {
        $sql = "SELECT idtypeperson FROM tbtypeperson WHERE `name` = '{$typeName}'";

        return $this->selectPDO($sql);
    }

    /**
     * Grava a pessoa que acessa ao dado mapeado
     *
     * @param  string   $personName Nome da pessoa que acessa ao dado mapeado
     * @param  int      $typeID     Tipo da pessoa que acessa ao dado mapeado
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "id" (ID do tipo inserido)
     */
    public function insertPerson($personName,$typeID) {
        $sql = "INSERT INTO tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,`name`)
                     VALUES (3,:typeID,1,1,:personName)";
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":personName",$personName);
            $sth->bindParam(":typeID", $typeID);
            $sth->execute(); //echo "{$sql} {$personName} {$typeID}\n";
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('tbperson'));
    }

    public function getLinkPerson($dataID,$personID,$typeID) {
        $sql = "SELECT iddado, idperson FROM lgp_tbdado_has_person
                 WHERE iddado = {$dataID} AND idperson = {$personID} AND `type` = '{$typeID}'"; //echo "$sql\n";

        return $this->selectPDO($sql);
    }

    public function getGroup($where=null,$order=null,$group=null,$limit=null) {
        $sql = "SELECT idgroup, b.idperson, b.`name` group_name,c.idperson idcompany, c.`name` company_name, a.status
                  FROM lgp_tbgroup a, tbperson b, tbperson c
                 WHERE a.idperson = b.idperson
                   AND a.idcompany = c.idperson
                $where $group $order $limit"; //echo "$sql\n";

        return $this->selectPDO($sql);
    }

    /**
     * Grava o grupo que acessa ao dado mapeado
     *
     * @param  string   $personID   ID do grupo na tabela tbperson
     * @param  int      $companyID  ID da empresa à qual pertence o grupo
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "id" (ID do tipo inserido)
     */
    public function insertGroup($personID,$companyID) {
        $sql = "INSERT INTO lgp_tbgroup (idperson,idcompany)
                     VALUES (:personID,:companyID)";
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute(array(":personID"=>$personID,":companyID"=>$companyID)); //echo "{$sql}\n";
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('lgp_tbgroup'));
    }

    /**
     * Grava a pessoa com quem é compartilhado o dado mapeado
     *
     * @param  string   $operatorName   Nome da pessoa 
     * @param  int      $typeID         Tipo da pessoa que acessa ao dado mapeado
     * @param  int      $natureID       ID da natureza (Física/Jurídica) da pessoa 
     * @param  string   $phone          Telefone
     * @param  string   $mobile         Celular
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "id" (ID do tipo inserido)
     */
    public function insertOperator($operatorName,$typeID,$natureID,$phone,$mobile) {
        $sql = "INSERT INTO tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,`name`,phone_number,cel_phone)
                     VALUES (3,:typeID,:natureID,1,:operatorName,:phone,:mobile)";
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":operatorName",$operatorName);
            $sth->bindParam(":typeID", $typeID);
            $sth->bindParam(":natureID", $natureID);
            $sth->bindParam(":phone", $phone);
            $sth->bindParam(":mobile", $mobile);
            $sth->execute(); //echo "{$sql} {$personName} {$typeID}\n";
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('tbperson'));
    }

    public function getJuridical($where=null,$order=null,$group=null,$limit=null) {
        $sql = "SELECT idjuridicalperson, idperson, contact_person FROM tbjuridicalperson
                $where $group $order $limit"; //echo "$sql\n";

        return $this->selectPDO($sql);
    }

    /**
     * Grava o nome da pessoa para contato na instituição (caso o operador seja pessoa jurídica)
     *
     * @param  int      $operatorID   ID do operador 
     * @param  string   $contact      Nome da pessoa
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "id" (ID do tipo inserido)
     */
    public function insertContact($operatorID,$contact) {
        $sql = "INSERT INTO tbjuridicalperson (idperson,contact_person) 
                     VALUES (:operatorID,:contact)";
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":operatorID",$operatorID);
            $sth->bindParam(":contact", $contact);
            $sth->execute(); //echo "{$sql} {$personName} {$typeID}\n";
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('tbperson'));
    }

    /**
     * Atualiza o nome da pessoa para contato na instituição (caso o operador seja pessoa jurídica)
     * 
     * @param  string   $contact        Nome da pessoa
     * @param  int      $juridicalID    ID na tabela tbjuridicalperson
     * @return array Contendo os parametros "success" (true/false), "message" (para erros), "id" (ID do tipo inserido)
     */
    public function updateContact($contact,$juridicalID) {
        $sql = "UPDATE tbjuridicalperson 
                   SET contact_person = :contact
                 WHERE idjuridicalperson = :juridicalID";
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":contact", $contact);
            $sth->bindParam(":juridicalID",$juridicalID);
            $sth->execute(); //echo "{$sql} {$personName} {$typeID}\n";
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('tbperson'));
    }

    public function getLinkOperator($dataID,$personID) {
        $sql = "SELECT iddado, idperson FROM lgp_tbdado_has_operador
                 WHERE iddado = {$dataID} AND idperson = {$personID}"; //echo "$sql\n";

        return $this->selectPDO($sql);
    }

    public function getPersonAccess($where=null,$order=null,$group=null,$limit=null,$addType=null) {
        $sql = "SELECT idperson, `name`, `type`, `status`
                FROM (SELECT idperson, `name`, 'P' `type`,`status`
                  FROM tbperson
                 WHERE idtypeperson IN (2,3$addType)
                 UNION 
                SELECT a.idgroup idperson, `name`, 'G' `type`,a.`status`
                  FROM `lgp_tbgroup` a, tbperson b 
                 WHERE b.idperson = a.idperson) tmp
                $where $group $order $limit"; //echo "$sql\n";

        return $this->selectPDO($sql);
    }

    public function getDataMappingEdit($where=null,$order=null,$limit=null,$addType) {
        $sql = "SELECT a.iddado, a.nome, compartilhado, a.idtipotitular, b.nome tipotitular, a.idtipodado, c.nome tipo, 
                        GROUP_CONCAT(DISTINCT m.idfinalidade ORDER BY m.nome) finalidadeids, GROUP_CONCAT(DISTINCT m.nome ORDER BY m.nome) finalidade,
                        GROUP_CONCAT(DISTINCT o.idformatocoleta ORDER BY o.nome) formatoids, GROUP_CONCAT(DISTINCT o.nome ORDER BY o.nome) formato,
                        GROUP_CONCAT(DISTINCT n.idformacoleta ORDER BY n.nome) formaids, GROUP_CONCAT(DISTINCT n.nome ORDER BY n.nome) forma,
                        GROUP_CONCAT(DISTINCT l.idbaselegal ORDER BY l.nome) baseids, GROUP_CONCAT(DISTINCT l.nome ORDER BY l.nome) base,
                        GROUP_CONCAT(DISTINCT k.idarmazenamento ORDER BY k.nome) armazenamentoids, GROUP_CONCAT(DISTINCT k.nome ORDER BY k.nome) armazenamento,
                        GROUP_CONCAT(DISTINCT CONCAT(p.idperson,'|',i.type) ORDER BY p.name) personaccids, GROUP_CONCAT(DISTINCT p.name ORDER BY p.name) personacc,
                        GROUP_CONCAT(DISTINCT r.idperson ORDER BY r.name) operadorids, GROUP_CONCAT(DISTINCT r.name ORDER BY r.name) operador
                  FROM lgp_tbdado a
                  JOIN lgp_tbtipotitular b
                    ON a.idtipotitular = b.idtipotitular
                  JOIN lgp_tbtipodado c
                    ON a.idtipodado = c.idtipodado
                  JOIN lgp_tbdado_has_armazenamento d
                    ON a.iddado = d.iddado
                  JOIN lgp_tbdado_has_baselegal e
                    ON a.iddado = e.iddado
                  JOIN lgp_tbdado_has_finalidade f
                    ON a.iddado = f.iddado
                  JOIN lgp_tbdado_has_formacoleta g
                    ON a.iddado = g.iddado
                  JOIN lgp_tbdado_has_formatocoleta h
                    ON a.iddado = h.iddado
                  JOIN lgp_tbdado_has_person i
                    ON a.iddado = i.iddado
       LEFT OUTER JOIN lgp_tbdado_has_operador j
                    ON a.iddado = j.iddado
                  JOIN lgp_tbarmazenamento k
                    ON d.idarmazenamento = k.idarmazenamento
                  JOIN lgp_tbbaselegal l
                    ON e.idbaselegal = l.idbaselegal
                  JOIN lgp_tbfinalidade m
                    ON f.idfinalidade = m.idfinalidade
                  JOIN lgp_tbformacoleta n
                    ON g.idformacoleta = n.idformacoleta
                  JOIN lgp_tbformatocoleta o
                    ON h.idformatocoleta = o.idformatocoleta
                  JOIN (SELECT idperson, `name`, 'P' `type`,`status`
                          FROM tbperson
                         WHERE idtypeperson IN (2,3$addType)
                         UNION 
                        SELECT a.idgroup idperson, `name`, 'G' `type`,a.`status`
                          FROM `lgp_tbgroup` a, tbperson b 
                         WHERE b.idperson = a.idperson) p
                    ON (i.idperson = p.idperson AND
                       i.`type` = p.`type`) 
       LEFT OUTER JOIN tbperson r
                    ON j.idperson = r.idperson
                $where
              GROUP BY a.iddado
                $order $limit"; //echo "{$sql}\n";

        return $this->selectPDO($sql); 
    }
    

}