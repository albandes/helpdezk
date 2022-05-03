<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\logoModel;

class logoDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Returns logo's data by name
     *
     * @param  logoModel $logoModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getLogoByName(logoModel $logoModel): array
    {
        
        $sql = "SELECT idlogo, `name`, height, width, file_name FROM tblogos WHERE `name` = :name";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":name",$logoModel->getName());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $logoModel->setId($aRet['idlogo'])
                      ->setName($aRet['name'])
                      ->setHeight($aRet['height'])
                      ->setWidth($aRet['width'])
                      ->setFileName($aRet['file_name']);
            $ret = true;
            $result = array("message"=>"","object"=>$logoModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying get logo data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
}