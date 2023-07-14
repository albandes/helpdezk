<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;

use App\modules\admin\models\mysql\featureModel;
use App\modules\admin\models\mysql\loginModel;

class loginDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Returns login's type
     *
     * @param  loginModel $loginModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getLoginType(loginModel $loginModel): array
    {
        
        $sql = "SELECT idtypelogin FROM tbperson WHERE login = :login";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':login', $loginModel->getLogin());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $loginModel->setLoginType($aRet['idtypelogin'] ? $aRet['idtypelogin'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$loginModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting login type ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

        
    /**
     * Return an array with user data
     *
     * @param  loginModel $loginModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getUserByLogin(loginModel $loginModel): array
    {
        
        $sql = "SELECT idperson, `name`, login, idtypeperson FROM tbperson WHERE login = :login";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':login', $loginModel->getLogin());
            $stmt->execute();
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $loginModel->setIdPerson($aRet['idperson'])
                       ->setName($aRet['name'])
                       ->setLogin($aRet['login'])
                       ->setIdTypePerson($aRet['idtypeperson']);
              
            $ret = true;
            $result = array("message"=>"","object"=>$loginModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting login type ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Return an array with user data
     *
     * @param  loginModel $loginModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getUser(loginModel $loginModel): array
    {
        
        $sql = "SELECT idperson, `name`, login, idtypeperson 
                  FROM tbperson 
                 WHERE (login = :login 
                        AND (password = :password OR password IS NULL)
                        AND status = 'A')";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':login', $loginModel->getLogin());
            $stmt->bindParam(':password', $loginModel->getPasswordEncrypted());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        
            if($aRet){
                $loginModel->setIdPerson($aRet['idperson'])
                           ->setName($aRet['name'])
                           ->setLogin($aRet['login'])
                           ->setIdTypePerson($aRet['idtypeperson']);
            }else{
                $loginModel->setIdPerson(0)
                           ->setIdTypePerson(0);
            }

            $ret = true;
            $result = array("message"=>"","object"=>$loginModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting user ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns user's total of requests
     *
     * @param  loginModel $loginModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getRequestsByUser(loginModel $loginModel): array
    {
        
        $sql = "SELECT COUNT(*) AS amount FROM hdk_tbrequest WHERE idperson_creator = :userID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $loginModel->getIdPerson());
            $stmt->execute();

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $loginModel->setTotalRequests($row['amount']);

            $ret = true;
            $result = array("message"=>"","object"=>$loginModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();            
            $this->loggerDB->error('Error getting total of requests ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns user's total of requests by login and request code
     *
     * @param  loginModel $loginModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getUserRequests(loginModel $loginModel): array
    {
        
        $sql = "SELECT COUNT(*) as amount FROM hdk_tbrequest WHERE code_request = :requestCode AND idperson_creator = :userID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $loginModel->getIdPerson());
            $stmt->bindParam(':requestCode', $loginModel->getRequestCode());
            $stmt->execute();

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $loginModel->setTotalRequests($row['amount']);

            $ret = true;
            $result = array("message"=>"","object"=>$loginModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();            
            $this->loggerDB->error('Error getting total of requests ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Check if user exists by login
     *
     * @param  loginModel $loginModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function checkUser(loginModel $loginModel): array
    {      
        $sql = "SELECT idperson,login, status FROM tbperson WHERE login = :login";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':login', $loginModel->getLogin());
            $stmt->execute();
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $loginModel->setUserStatus(($aRet['status'] == "A") ? "A" : "I")
                       ->setIdPerson((!is_null($aRet['idperson']) && !empty($aRet['idperson'])) ? $aRet['idperson'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$loginModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error checking user data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns user's session data
     *
     * @param  loginModel $loginModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getDataSession(loginModel $loginModel): array
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
            $stmt->bindParam(':userID', $loginModel->getIdPerson());
            $stmt->execute();
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $loginModel->setName($aRet['name'])
                       ->setLogin($aRet['login'])
                       ->setIdTypePerson($aRet['idtypeperson'])
                       ->setIdCompany($aRet['idjuridical'])
                       ->setCompanyName($aRet['company']);
                  
            $ret = true;
            $result = array("message"=>"","object"=>$loginModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting user data session ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns user's groups
     *
     * @param  mixed $loginModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getPersonGroups(loginModel $loginModel): array
    {        
        $sql = "SELECT pers.name as personname, pers.idperson, pers.name as groupname, grp.idgroup
                  FROM hdk_tbgroup as grp, tbperson as pers, hdk_tbgroup_has_person as relat
                 WHERE grp.idgroup = relat.idgroup
                   AND pers.idperson = relat.idperson
                   AND pers.idperson = :userID
              ORDER BY grp.idgroup";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $loginModel->getIdPerson());
            $stmt->execute();
            $row = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach($row as $k=>$v){
                $groups .= "{$v['idgroup']},";           
            }
            
            $groups = substr($groups,0,-1);

            $loginModel->setGroupId($groups);
            $ret = true;
            $result = array("message"=>"","object"=>$loginModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting user data session ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns if the helpdezk module is active or not
     *
     * @param  mixed $loginModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function isActiveHelpdezk(loginModel $loginModel): array
	{
		$sql =  "SELECT idmodule FROM tbmodule WHERE tableprefix = 'hdk' AND `status` = 'A'";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $loginModel->setIsActiveHdk(($row && !empty($row)) ? true : false);
            $ret = true;
            $result = array("message"=>"","object"=>$loginModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error check is helpdezk active ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);

	}
    
    /**
     * Returns all groups
     *
     * @param  mixed $loginModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAllGroups(loginModel $loginModel): array
    {        
        $sql = "SELECT pers.idperson, pers.name groupname, grp.idgroup
                  FROM hdk_tbgroup  grp, tbperson 	 pers
                 WHERE pers.idperson = grp.idperson
              ORDER BY grp.idgroup";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach($row as $k=>$v){
                $groups .= "{$v['idgroup']},";           
            }

            $groups = substr($groups,0,-1);

            $loginModel->setGroupId($groups);
            $ret = true;
            $result = array("message"=>"","object"=>$loginModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting all hdk groups ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }


    /**
     * Returns global settings
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchConfigGlobalData(featureModel $featureModel): array
    {        
        $sql = "SELECT session_name, `value` from tbconfig";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $featureModel->setGlobalSettingsList($row);
            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting global settings ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts login details in tblogindetail table
     * pt_br Grava detalhes do login na tabela tblogindetail
     *
     * @param  loginModel $loginModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertLoginDetail(loginModel $loginModel): array
    {
        
        $sql = "INSERT INTO tblogindetail (idperson, idtypelogin, datelogin, `status`) 
                        VALUES (:personId, :loginTypeId, NOW(), :status)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':personId', $loginModel->getIdPerson());
            $stmt->bindParam(':loginTypeId', $loginModel->getLoginType());
            $stmt->bindParam(':status', $loginModel->getLoginStatus());
            $stmt->execute();

            $loginModel->setLoginDetailId($this->db->lastInsertId());
              
            $ret = true;
            $result = array("message"=>"","object"=>$loginModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error saving login detail ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function getUserByEmail(loginModel $loginModel): array
    {
        
        $sql = "SELECT idperson, `name`, login, idtypeperson FROM tbperson WHERE email = :email";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $loginModel->getUserEmail());
            $stmt->execute();
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $loginModel->setIdPerson($aRet['idperson'])
                       ->setName($aRet['name'])
                       ->setLogin($aRet['login'])
                       ->setIdTypePerson($aRet['idtypeperson']);
              
            $ret = true;
            $result = array("message"=>"","object"=>$loginModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting login type ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}