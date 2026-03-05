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

	/**
	 * @var int
	 */
	private $idProgram;

	/**
	 * @var string
	 */
	private $sessionId;

	/**
	 * @var string
	 */
	private $twoFactorValidated;

	/**
	 * @var int
	 */
	private $wasSuccessful;

	/**
	 * @var int
	 */
	private $blockWindowMinutes;

	/**
	 * @var int
	 */
	private $maxAttempts;

	/**
	 * @var array
	 */
	private $recentFailedAttemptsList;
	

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

	/**
	 * Get the value of idProgram
	 *
	 * @return  int
	 */ 
	public function getIdProgram()
	{
		return $this->idProgram;
	}

	/**
	 * Set the value of idProgram
	 *
	 * @param  int  $idProgram
	 *
	 * @return  self
	 */ 
	public function setIdProgram(int $idProgram)
	{
		$this->idProgram = $idProgram;

		return $this;
	}

	/**
	 * Get the value of sessionId
	 *
	 * @return  string
	 */ 
	public function getSessionId()
	{
		return $this->sessionId;
	}

	/**
	 * Set the value of sessionId
	 *
	 * @param  string  $sessionId
	 *
	 * @return  self
	 */ 
	public function setSessionId(string $sessionId)
	{
		$this->sessionId = $sessionId;

		return $this;
	}

	/**
	 * Get the value of twoFactorValidated
	 *
	 * @return  string
	 */ 
	public function getTwoFactorValidated()
	{
		return $this->twoFactorValidated;
	}

	/**
	 * Set the value of twoFactorValidated
	 *
	 * @param  string  $twoFactorValidated
	 *
	 * @return  self
	 */ 
	public function setTwoFactorValidated(string $twoFactorValidated)
	{
		$this->twoFactorValidated = $twoFactorValidated;

		return $this;
	}

	/**
	 * Get the value of wasSuccessful
	 *
	 * @return  int
	 */ 
	public function getWasSuccessful()
	{
		return $this->wasSuccessful;
	}

	/**
	 * Set the value of wasSuccessful
	 *
	 * @param  int  $wasSuccessful
	 *
	 * @return  self
	 */ 
	public function setWasSuccessful(int $wasSuccessful)
	{
		$this->wasSuccessful = $wasSuccessful;

		return $this;
	}

	/**
	 * Get the value of blockWindowMinutes
	 *
	 * @return  int
	 */ 
	public function getBlockWindowMinutes()
	{
		return $this->blockWindowMinutes;
	}

	/**
	 * Set the value of blockWindowMinutes
	 *
	 * @param  int  $blockWindowMinutes
	 *
	 * @return  self
	 */ 
	public function setBlockWindowMinutes(int $blockWindowMinutes)
	{
		$this->blockWindowMinutes = $blockWindowMinutes;

		return $this;
	}

	/**
	 * Get the value of maxAttempts
	 *
	 * @return  int
	 */ 
	public function getMaxAttempts()
	{
		return $this->maxAttempts;
	}

	/**
	 * Set the value of maxAttempts
	 *
	 * @param  int  $maxAttempts
	 *
	 * @return  self
	 */ 
	public function setMaxAttempts(int $maxAttempts)
	{
		$this->maxAttempts = $maxAttempts;

		return $this;
	}

	/**
	 * Get the value of recentFailedAttemptsList
	 *
	 * @return  array
	 */ 
	public function getRecentFailedAttemptsList()
	{
		return $this->recentFailedAttemptsList;
	}

	/**
	 * Set the value of recentFailedAttemptsList
	 *
	 * @param  array  $recentFailedAttemptsList
	 *
	 * @return  self
	 */ 
	public function setRecentFailedAttemptsList(array $recentFailedAttemptsList)
	{
		$this->recentFailedAttemptsList = $recentFailedAttemptsList;

		return $this;
	}
}
