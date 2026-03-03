<?php

namespace App\modules\main\dao\mysql;

use App\core\Database;
use App\modules\main\models\mysql\signatureModel;

class signatureDAO extends Database
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Cria uma nova assinatura
	 * @param signatureModel $model
	 * @return array
	 */
	public function createSignature(signatureModel $model): array
	{
		try {

			$hash = hash(
				'sha256',
				$model->getIdPerson() .
					$model->getType() .
					$model->getTypeId() .
					date('Y-m-d H:i:s')
			);
			$model->setHash($hash);

			$sql = "INSERT INTO tbsignatures (idperson, type, type_id, hash, signature_date)
                    VALUES (:idperson, :type, :type_id, :hash, NOW())";
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(':idperson', $model->getIdPerson());
			$stmt->bindValue(':type', $model->getType());
			$stmt->bindValue(':type_id', $model->getTypeId());
			$stmt->bindValue(':hash', $model->getHash());
			$stmt->execute();

			$model->setIdSignature($this->db->lastInsertId());

			$aret = true;
			$result = [
				"message" => "",
				"object" => $model
			];
		} catch (\PDOException $ex) {
			$msg = $ex->getMessage();
			$this->loggerDB->error("Error saving signature with single model.", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__, 'DB Message' => $msg]);
			$aret = false;
			$result = [
				"message" => $msg,
				"object" => null
			];
		}

		return array("status" => $aret, "push" => $result);
	}

	/**
	 * Busca uma assinatura pelo id
	 * @param int $idsignature
	 * @return array
	 */
	public function getSignature(int $idsignature): array
	{
		try {
			$sql = "SELECT * FROM tbsignatures WHERE idsignature = :idsignature";
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(':idsignature', $idsignature);
			$stmt->execute();
			$row = $stmt->fetch(\PDO::FETCH_ASSOC);

			if ($row) {
				$model = new signatureModel();
				$model->setIdSignature($row['idsignature']);
				$model->setIdPerson($row['idperson']);
				$model->setType($row['type']);
				$model->setTypeId($row['type_id']);
				$model->setHash($row['hash']);
				$model->setSignatureDate($row['signature_date']);
			}
			$aret = true;
			$result = array("message" => "", "object" => $model);
		} catch (\PDOException $ex) {
			$msg = $ex->getMessage();
			$this->loggerDB->error("Error fetching signature.", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__, 'DB Message' => $msg]);
			$aret = false;
			$result = array("message" => $msg, "object" => null);
		}


		return array("status" => $aret, "push" => $result);
	}
	
	/**
	 * Verifica se o usuário possui secret_authenticator cadastrado
	 * @param int $idperson
	 */
	public function hasAuthenticatorSecret(int $idperson)
	{
			$sql = "SELECT secret_authenticator FROM tbperson WHERE idperson = :idperson LIMIT 1";
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(':idperson', $idperson);
			$stmt->execute();
			$row = $stmt->fetch(\PDO::FETCH_ASSOC);

			return $row['secret_authenticator'];
	}

	/**
	 * Salva o secret_authenticator para o usuário
	 * @param int $idperson
	 * @param string $secret
	 * @return array
	 * */
	public function saveAuthenticatorSecret(int $idperson, string $secret): array
	{
		try {
			$sql = "UPDATE tbperson SET secret_authenticator = :secret WHERE idperson = :idperson";
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(':secret', $secret);
			$stmt->bindValue(':idperson', $idperson);
			$stmt->execute();

			$aret = true;
			$result = ["message" => "", "object" => null];
		} catch (\PDOException $ex) {
			$msg = $ex->getMessage();
			$this->loggerDB->error("Error saving secret_authenticator.", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__, 'DB Message' => $msg]);
			$aret = false;
			$result = ["message" => $msg, "object" => null];
		}

		return ["status" => $aret, "push" => $result];
	}
	
	/**
	 * getUser2FASecret
	 * 
	 * en_us Returns the user’s 2FA secret
	 * pt_br Retorna o secret 2FA do usuário
	 *
	 * @param  signatureModel $signatureModel
	 * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
	 */
	public function getUser2FASecret(signatureModel $signatureModel): array
	{
		$sql = "SELECT secret_authenticator FROM tbperson WHERE idperson = :personId";
		
		try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":personId",$signatureModel->getIdPerson());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $signatureModel->setUserSecret2FA((!is_null($aRet['secret_authenticator']) && !empty($aRet['secret_authenticator'])) ? $aRet['secret_authenticator'] : null);
            
            $ret = true;
            $result = array("message"=>"","object"=>$signatureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting user's 2FA secret", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
	}
	
	/**
	 * checkUser2FAValidated
	 *
	 * @param  mixed $signatureModel
	 * @return array
	 */
	public function checkUser2FAValidated(signatureModel $signatureModel): array
	{
		$sql = "SELECT 1 FROM tbtwo_factor_attempts WHERE idperson = :userId AND idprogram = :programId AND session_id = :sessionId AND was_successful = 1 LIMIT 1";

		try{
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(":userId", $signatureModel->getIdPerson());
			$stmt->bindValue(":programId", $signatureModel->getIdProgram());
			$stmt->bindValue(":sessionId", $signatureModel->getSessionId());
			$stmt->execute();

			$validated = $stmt->fetchColumn() ? true : false;

			// seta no model se já validou ou não
			$signatureModel->setTwoFactorValidated($validated);

			$ret = true;
			$result = array("message"=>"","object"=>$signatureModel);
		}catch(\PDOException $ex){
			$msg = $ex->getMessage();
			$this->loggerDB->error("Error checking user's 2FA validation", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__,'DB Message' => $msg]);

			$ret = false;
			$result = array("message"=>$msg,"object"=>null);
		}

		return array("status"=>$ret,"push"=>$result);
	}
	
	/**
	 * insert2FAAttempt
	 *
	 * @param  mixed $signatureModel
	 * @return array
	 */
	public function insert2FAAttempt(signatureModel $signatureModel): array
	{
		$sql = "INSERT INTO tbtwo_factor_attempts (idperson, idprogram, attempted_at, was_successful, session_id)
										   VALUES (:userId, :programId, NOW(), :wasSuccessful, :sessionId)";

		try{
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(":userId", $signatureModel->getIdPerson());
			$stmt->bindValue(":programId", $signatureModel->getIdProgram());
			$stmt->bindValue(":sessionId", $signatureModel->getSessionId());
			$stmt->bindValue(":wasSuccessful", $signatureModel->getWasSuccessful());
			$stmt->execute();

			$ret = true;
			$result = array("message"=>"","object"=>$signatureModel);

		}catch(\PDOException $ex){
			$msg = $ex->getMessage();
			$this->loggerDB->error("Error saving the two-factor authentication attempt.", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__,'DB Message' => $msg]);

			$ret = false;
			$result = array("message"=>$msg,"object"=>null);
		}

		return array("status"=>$ret,"push"=>$result);
	}
	
	/**
	 * getRecentFailedAttempts
	 *
	 * @param  mixed $signatureModel
	 * @return array
	 */
	public function getRecentFailedAttempts(signatureModel $signatureModel): array
	{
		$sql = "SELECT idtwo_factor_attempts
        		  FROM tbtwo_factor_attempts
        		 WHERE idperson = :userId
        		   AND idprogram = :programId
        		   AND session_id = :sessionId
        		   AND was_successful = 0
        		   AND attempted_at >= (NOW() - INTERVAL {$signatureModel->getBlockWindowMinutes()} MINUTE)
        	  ORDER BY attempted_at DESC
        		 LIMIT {$signatureModel->getMaxAttempts()}";

		try{
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(":userId", $signatureModel->getIdPerson());
			$stmt->bindValue(":programId", $signatureModel->getIdProgram());
			$stmt->bindValue(":sessionId", $signatureModel->getSessionId());
			$stmt->execute();

			$aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

			$signatureModel->setRecentFailedAttemptsList(($aRet && is_array($aRet)) ? $aRet : array());

			$ret = true;
			$result = array("message"=>"","object"=>$signatureModel);
		}catch(\PDOException $ex){
			$msg = $ex->getMessage();
			$this->loggerDB->error("Error fetching recent failures.", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__,'DB Message' => $msg]);

			$ret = false;
			$result = array("message"=>$msg,"object"=>null);
		}

		return array("status"=>$ret,"push"=>$result);
	}
}
