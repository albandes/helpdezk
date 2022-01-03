<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;

use App\modules\admin\models\mysql\popConfigModel;
use App\modules\admin\models\mysql\moduleModel;
use App\modules\admin\models\mysql\featureModel;

class featureDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Returns object with POP settings
     *
     * @param  popConfigModel $popConfigModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchPopConfigs(popConfigModel $popSettings): array
    {
        
        $sql = "SELECT session_name, `value` FROM tbconfig WHERE idconfigcategory = 12";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach($row as $k=>$v){
                switch($v['session_name']){
                    case 'POP_HOST':
                        $popSettings->setHost($v['value']);
                        break;
                    case 'POP_PORT':
                        $popSettings->setPort($v['value']);
                        break;
                    case 'POP_TYPE':
                        $popSettings->setType($v['value']);
                        break;
                    case 'POP_DOMAIN':
                        $popSettings->setDomain($v['value']);
                        break;
                    default:
                        break;
                }          
            }

            $ret = true;
            $result = array("message"=>"","object"=>$popSettings);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting all hdk groups ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    // It has not been standardized as it is not yet used in controllers or services
    public function getArrayConfigs(int $confCategoryID): array
    {
        
        $sql = "SELECT session_name, `value` FROM tbconfig WHERE idconfigcategory = :confCategoryID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':confCategoryID', $confCategoryID);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = array();
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $ses = $row['session_name'];
            $val = $row['value'];
            $confs[$ses] = $val;            
        }

        return array("success"=>true,"message"=>"","data"=>$confs);
    }

    // It has not been standardized as it is not yet used in controllers or services
    public function getConfigValue(string $confName): array
    {        
        $sql = "SELECT `value` FROM tbconfig WHERE session_name = :confName";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':confName', $confName);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        return array("success"=>true,"message"=>"","data"=>$aRet);
    }
    
    /**
     * Returns user's settings
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchUserSettings(featureModel $featureModel): array
    {        
        $sql = "SELECT idconfiguser,idperson,idlocale,idtheme,grid_operator,grid_operator_width,
                        grid_user,grid_user_width
                  FROM hdk_tbconfig_user
                 WHERE idperson = :userID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $featureModel->getUserID());
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $featureModel->setUserSettingsList($rows);
            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting user's settings ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Check if a table exists in DB
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function tableExists(featureModel $featureModel): array
    {        
        $sql = "SELECT COUNT(*) as exist
                  FROM information_schema.tables 
                 WHERE table_schema = '{$_ENV['DB_NAME']}' 
                   AND table_name = :tableName";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':tableName', $featureModel->getTableName());
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            $flg = ($row['exist'] > 0) ? true : false;
            $featureModel->setExistTable($flg);
            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error checking table exists ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return module path by user type
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getPathModuleByTypePerson(featureModel $featureModel): array
    {        
        $sql = "SELECT `path`
                  FROM tbmodule a
       LEFT OUTER JOIN tbtypeperson_has_module b
                    ON b.idmodule = a.idmodule
                 WHERE b.idtypeperson =  :typePerson";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':typePerson', $featureModel->getUserType());
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            $featureModel->setPath($row['path']);
            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting module's path", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}