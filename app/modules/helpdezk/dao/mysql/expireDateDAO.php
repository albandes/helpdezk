<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;

use App\modules\helpdezk\models\mysql\expireDateModel;

class expireDateDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Returns a object with fingerprint's template
     *
     * @param  expireDateModel $fingerprintModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchBusinessDays(expireDateModel $expireDateModel): array
    {        
        $sql = "SELECT num_day_week, begin_morning, end_morning, begin_afternoon, end_afternoon 
                  FROM hdk_tbwork_calendar_new 
                 WHERE business_day = 1";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $expireDateModel->setBusinessDays($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$expireDateModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting business days. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    
}