<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\loginModel;

class loginDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Return an array with login type
     *
     * @param  string $login
     * @return loginModel
     */
    public function getLoginType(string $login): ?loginModel
    {
        
        $sql = "SELECT idtypelogin FROM tbperson WHERE login = :login";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting login type ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        $login = new loginModel();
        $login->setLogintype($aRet['idtypelogin']);
        
        return $login;
    }

        
    /**
     * Return an array with user data
     *
     * @param  string $login
     * @return loginModel
     */
    public function getUserByLogin(string $login): ?loginModel
    {
        
        $sql = "SELECT idperson, `name`, login, idtypeperson FROM tbperson WHERE login = :login";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting login type ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        $login = new loginModel();
        $login->setIdperson($aRet['idperson'])
              ->setName($aRet['name'])
              ->setLogin($aRet['login'])
              ->setIdtypeperson($aRet['idtypeperson']);
        
        return $login;
    }
    
    /**
     * Return an array with user data
     *
     * @param  string $login
     * @param  string $password
     * @return loginModel
     */
    public function getUser(string $login,string $password): ?loginModel
    {
        
        $sql = "SELECT idperson, `name`, login, idtypeperson 
                  FROM tbperson 
                 WHERE (login = :login 
                        AND (password = :password OR password IS NULL)
                        AND status = 'A')";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting user ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        $login = new loginModel();
        $login->setIdperson($aRet['idperson'])
              ->setName($aRet['name'])
              ->setLogin($aRet['login'])
              ->setIdtypeperson($aRet['idtypeperson']);

        return $login;
    }

    public function getRequestsByUser(string $userID): array
    {
        
        $sql = "SELECT COUNT(*) AS amount FROM hdk_tbrequest WHERE idperson_creator = :userID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        return array("success"=>true,"message"=>"","data"=>$aRet);
    }

    public function getUserRequests(string $userID,string $requestCode): array
    {
        
        $sql = "SELECT COUNT(*) as amount FROM hdk_tbrequest WHERE code_request = :requestCode AND idperson_creator = :userID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->bindParam(':requestCode', $requestCode);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        return array("success"=>true,"message"=>"","data"=>$aRet);
    }

    public function checkUser(string $login): ?loginModel
    {        
        $sql = "SELECT login, status FROM tbperson WHERE login = :login";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error("Error checking user data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        $login = new loginModel();
        $login->setUserStatus(($aRet['status'] == "A") ? "A" : "I");
        
        return $login;
    }

    public function getDataSession(int $userID): ?loginModel
    {        
        $sql = "SELECT person.idtypeperson as idtypeperson, person.name as name,  person.login as login,
                        juridical.idperson  as idjuridical, juridical.name as company
                  FROM tbperson person, tbperson juridical, hdk_tbdepartment_has_person rela, hdk_tbdepartment dep
                 WHERE person.idperson = :userID
                   AND person.idperson = rela.idperson
                   AND juridical.idperson = dep.idperson
                   AND dep.iddepartment = rela.iddepartment";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting user data session ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        $login = new loginModel();
        $login->setName($aRet['name'])
              ->setLogin($aRet['login'])
              ->setIdtypeperson($aRet['idtypeperson'])
              ->setIdcompany($aRet['idjuridical'])
              ->setCompanyName($aRet['company']);

        return $login;
    }

    public function getPersonGroups(int $userID): ?loginModel
    {        
        $sql = "SELECT pers.name as personname, pers.idperson, pers.name as groupname, grp.idgroup
                  FROM hdk_tbgroup as grp, tbperson as pers, hdk_tbgroup_has_person as relat
                 WHERE grp.idgroup = relat.idgroup
                   AND pers.idperson = relat.idperson
                   AND pers.idperson = :userID
              ORDER BY grp.idgroup";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting user data session ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $row = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach($row as $k=>$v){
            $groups = "{$v['idgroup']},";           
        }
        
        $groups = substr($groups,0,-1);

        $login = new loginModel();
        $login->setGroupId($groups);

        return $login;
    }

    public function isActiveHelpdezk(): ?loginModel
	{
		$sql =  "SELECT idmodule FROM tbmodule WHERE tableprefix = 'hdk' AND `status` = 'A'";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error check is helpdezk active ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);        
		
        $login = new loginModel();
        $login->setIsActiveHdk((count($row)  > 0 && is_array($row)) ? true : false);

        return $login;

	}

    public function fetchAllGroups(): ?loginModel
    {        
        $sql = "SELECT pers.idperson, pers.name groupname, grp.idgroup
                  FROM hdk_tbgroup  grp, tbperson 	 pers
                 WHERE pers.idperson = grp.idperson
              ORDER BY grp.idgroup";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting all hdk groups ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $row = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach($row as $k=>$v){
            $groups = "{$v['idgroup']},";           
        }

        $groups = substr($groups,0,-1);

        $login = new loginModel();
        $login->setGroupId($groups);

        return $login;
    }


    public function getConfigData(): array
    {        
        $sql = "SELECT idmodule,`name`,`index`,path,smarty,headerlogo,reportslogo,tableprefix 
                  FROM tbmodule
                 WHERE `status` = 'A'";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return array("success"=>true,"message"=>"","data"=>$aRet);
    }

    public function fetchConfigGlobalData(): array
    {        
        $sql = "SELECT session_name, `value` from tbconfig";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting all hdk groups ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $row = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $row;
    }
}