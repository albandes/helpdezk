<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\programModel;

class programDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Return an array with Program to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array  Parameters returned in array: 
     *                [status = true/false
     *                 push =  [message = PDO Exception message 
     *                          object = model's object]]
     */
    public function queryPrograms($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT tbp.idprogram, tbp.name, pvoc.key_value name_fmt,
                       tbm.name module, mvoc.key_value module_fmt, 
                       tbtp.name category, pcvoc.key_value category_fmt,  
                       tbp.controller, tbp.status
                  FROM tbprogram tbp
                  JOIN tbprogramcategory tbtp 
                    ON tbtp.idprogramcategory = tbp.idprogramcategory
                  JOIN tbmodule tbm
                    ON tbtp.idmodule = tbm.idmodule
                  JOIN tbvocabulary pvoc
                    ON pvoc.key_name = tbp.smarty
                  JOIN tblocale ploc
                    ON (ploc.idlocale = pvoc.idlocale AND
                        LOWER(ploc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                  JOIN tbvocabulary mvoc
                    ON mvoc.key_name = tbm.smarty
                  JOIN tblocale mloc
                    ON (mloc.idlocale = mvoc.idlocale AND
                        LOWER(mloc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                  JOIN tbvocabulary pcvoc
                    ON pcvoc.key_name = tbtp.smarty
                  JOIN tblocale pcloc
                    ON (pcloc.idlocale = pcvoc.idlocale AND
                        LOWER(pcloc.name) = LOWER('{$_ENV['DEFAULT_LANG']}')) 
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $programModel = new programModel();
            $programModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$programModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting programs ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return an array with total of Programs to display in grid
     *
     * @param  string $where
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function countPrograms($where=null): array
    {        
        $sql = "SELECT COUNT(tbp.idprogram) total
                  FROM tbprogram tbp
                  JOIN tbprogramcategory tbtp 
                    ON tbtp.idprogramcategory = tbp.idprogramcategory
                  JOIN tbmodule tbm
                    ON tbtp.idmodule = tbm.idmodule
                  JOIN tbvocabulary pvoc
                    ON pvoc.key_name = tbp.smarty
                  JOIN tblocale ploc
                    ON (ploc.idlocale = pvoc.idlocale AND
                        LOWER(ploc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                  JOIN tbvocabulary mvoc
                    ON mvoc.key_name = tbm.smarty
                  JOIN tblocale mloc
                    ON (mloc.idlocale = mvoc.idlocale AND
                        LOWER(mloc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                  JOIN tbvocabulary pcvoc
                    ON pcvoc.key_name = tbtp.smarty
                  JOIN tblocale pcloc
                    ON (pcloc.idlocale = pcvoc.idlocale AND
                        LOWER(pcloc.name) = LOWER('{$_ENV['DEFAULT_LANG']}')) 
                $where";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $programModel = new programModel();
            $programModel->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$programModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting programs ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    
}