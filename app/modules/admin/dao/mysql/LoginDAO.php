<?php

namespace App\modules\admin\dao\mysql;
use App\core\Database;

class LoginDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Return an array with login type
     *
     * @param  string $login
     * @return array
     */
    public function getLoginType(string $login): array
    {
        
        $sql = "SELECT idtypelogin FROM tbperson WHERE login = :login";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        return array("success"=>true,"message"=>"","idtypelogin"=>$aRet['idtypelogin']);
    }

        
    /**
     * Return an array with user data
     *
     * @param  string $login
     * @return array
     */
    public function getUserByLogin(string $login): array
    {
        
        $sql = "SELECT idperson, idtypelogin, `name`, login, idtypeperson FROM tbperson login = :login";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        return array("success"=>true,"message"=>"","data"=>$aRet);
    }
    
    /**
     * Return an array with user data
     *
     * @param  string $login
     * @param  string $password
     * @return array
     */
    public function getUser(string $login,string $password): array
    {
        
        $sql = "SELECT idperson, idtypelogin, `name`, login, idtypeperson 
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
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        return array("success"=>true,"message"=>"","data"=>$aRet);
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

    public function checkUser(string $login): array
    {        
        $sql = "SELECT login, status FROM tbperson WHERE login = :login";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (count($aRet) > 0) {
            $status = ($aRet['status'] == "A") ? "A" : "I";
        }

        return array("success"=>true,"message"=>"","data"=>$status);
    }

    public function selectDataSession(int $userID): array
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
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return array("success"=>true,"message"=>"","data"=>$aRet);
    }

    public function selectPersonGroups(int $userID): array
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
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $row = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach($row as $k=>$v){
            $groups = "{$v['idgroup']},";           
        }
        
        $groups = substr($groups,0,-1);

        return array("success"=>true,"message"=>"","data"=>$groups);
    }

    public function isActiveHelpdezk(): array
	{
		$sql =  "SELECT idmodule FROM tbmodule WHERE tableprefix = 'hdk' AND `status` = 'A'";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);        
		$isactive = (count($row)  > 0) ? true : false;
        return array("success"=>true,"message"=>"","isactive"=>$isactive);

	}

    public function selectAllGroups(): array
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
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $row = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach($row as $k=>$v){
            $groups = "{$v['idgroup']},";           
        }

        $groups = substr($groups,0,-1);

        return array("success"=>true,"message"=>"","data"=>$groups);
    }

    public function getActiveModules(): array
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

    public function getConfigDataByModule(string $prefix): array
    {        
        $prefix = $prefix . '_tbconfig';
        $sql = "SELECT session_name, value FROM :prefix";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':prefix', $prefix);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return array("success"=>true,"message"=>"","data"=>$aRet);
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
}