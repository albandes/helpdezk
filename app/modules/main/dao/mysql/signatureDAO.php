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
	 * @return string
	 */
	public function hasAuthenticatorSecret(int $idperson): string
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
 */
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
}
