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
}
