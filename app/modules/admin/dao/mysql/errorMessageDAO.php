<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\errorMessageModel;

class errorMessageDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Returns default module
     *
     * @param  errorMessageModel $errorMessageModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getErrorMessage(errorMessageModel $errorMessageModel): array
    {        
        $sql = "SELECT path, `code`, a.`name`, description, a.smarty, CONCAT(path,'-',`code`) code_fmt
                  FROM tbmsgerror a, tbmodule b
                 WHERE a.idmodule = b.idmodule
                   AND a.idmodule = :moduleId
                   AND `code` = :errorCode";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":moduleId",$errorMessageModel->getIdModule());
            $stmt->bindValue(":errorCode",$errorMessageModel->getErrorCode());
            $stmt->execute();
            
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $errorMessageModel->setModulePath((!is_null($aRet['path']) && !empty($aRet['path'])) ? $aRet['path'] : "")
                              ->setErrorCode((!is_null($aRet['code']) && !empty($aRet['code'])) ? $aRet['code'] : "")
                              ->setName((!is_null($aRet['name']) && !empty($aRet['name'])) ? $aRet['name'] : "")
                              ->setDescription((!is_null($aRet['description']) && !empty($aRet['description'])) ? $aRet['description'] : "")
                              ->setLanguageKeyName((!is_null($aRet['smarty']) && !empty($aRet['smarty'])) ? $aRet['smarty'] : "")
                              ->setFormatedCode((!is_null($aRet['code_fmt']) && !empty($aRet['code_fmt'])) ? $aRet['code_fmt'] : "");
            
            $ret = true;
            $result = array("message"=>"","object"=>$errorMessageModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting error message ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    
}