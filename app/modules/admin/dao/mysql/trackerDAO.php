<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\trackerModel;

class trackerDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Insert tracker's data into the database
     *
     * @param  mixed $trackerModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTracker(trackerModel $trackerModel): array
    {        
        $sql = "INSERT INTO tbemail (idmodule, `from`, `to`, subject, body)
                     VALUES (:idmodule,:from,:to,:subject,:body)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idmodule', $trackerModel->getIdState());
            $stmt->bindParam(':from', $trackerModel->getName());
            $stmt->bindParam(':to', $trackerModel->getDtFoundation());
            $stmt->bindParam(':subject', $trackerModel->getIsDefault());
            $stmt->bindParam(':body', $trackerModel->getIsDefault());
            $stmt->execute();

            $trackerModel->setIdEmail($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$trackerModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying insert tracker ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Insert email link with mandrill ID into the database
     *
     * @param  mixed $trackerModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertMadrillID(trackerModel $trackerModel): array
    {
        $sql = "INSERT INTO tbemail_has_mandrill (idemail, idmandrill)
                       VALUES(:idEmail,:idMandrill)";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idEmail', $trackerModel->getIdEmail());
            $stmt->bindParam(':idMandrill', $trackerModel->getIdMandrill());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$trackerModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying insert tracker ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         

        return array("status"=>$ret,"push"=>$result);
    }

     /**
     * Update email's timestamp into the database
     *
     * @param  mixed $trackerModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateEmailSendTime(trackerModel $trackerModel): array
    {
        $sql = "UPDATE tbemail
                   SET ts = UNIX_TIMESTAMP()
				 WHERE idemail = :idEmail";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idEmail', $trackerModel->getIdEmail());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$trackerModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying insert tracker ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         

        return array("status"=>$ret,"push"=>$result);
    }

}