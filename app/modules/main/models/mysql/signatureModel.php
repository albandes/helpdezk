<?php

namespace App\modules\main\models\mysql;

final class signatureModel
{
	/**
	 * @var int
	 */
	private $idsignature;

	/**
	 * @var int
	 */
	private $idperson;

	/**
	 * @var string|null
	 */
	private $type;

	/**
	 * @var int|null
	 */
	private $type_id;

	/**
	 * @var string|null
	 */
	private $hash;

	/**
	 * @var string|null
	 */
	private $signature_date;

	/**
	 * @var string|null
	 */
	private $userSecret2FA;
	
	/**
	 * @var string|null
	 */
	private $filename;

	// Getters e Setters

	public function getIdSignature()
	{
		return $this->idsignature;
	}

	public function setIdSignature(int $idsignature)
	{
		$this->idsignature = $idsignature;
		return $this;
	}

	public function getIdPerson()
	{
		return $this->idperson;
	}

	public function setIdPerson(int $idperson)
	{
		$this->idperson = $idperson;
		return $this;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setType(?string $type)
	{
		$this->type = $type;
		return $this;
	}

	public function getTypeId()
	{
		return $this->type_id;
	}

	public function setTypeId(?int $type_id)
	{
		$this->type_id = $type_id;
		return $this;
	}

	public function getHash()
	{
		return $this->hash;
	}

	public function setHash(?string $hash)
	{
		$this->hash = $hash;
		return $this;
	}

	public function getSignatureDate()
	{
		return $this->signature_date;
	}

	public function setSignatureDate(?string $signature_date)
	{
		$this->signature_date = $signature_date;
		return $this;
	}

	/**
	 * Get the value of userSecret2FA
	 *
	 * @return  string|null
	 */ 
	public function getUserSecret2FA()
	{
		return $this->userSecret2FA;
	}

	/**
	 * Set the value of userSecret2FA
	 *
	 * @param  string|null  $userSecret2FA
	 *
	 * @return  self
	 */ 
	public function setUserSecret2FA($userSecret2FA)
	{
		$this->userSecret2FA = $userSecret2FA;

		return $this;
	}

	/**
	 * Get the value of filename
	 *
	 * @return  string|null
	 */ 
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * Set the value of filename
	 *
	 * @param  string|null  $filename
	 *
	 * @return  self
	 */ 
	public function setFilename($filename)
	{
		$this->filename = $filename;

		return $this;
	}
}
