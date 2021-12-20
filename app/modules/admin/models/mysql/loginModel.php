<?php

namespace App\modules\admin\models\mysql;

final class loginModel
{
    /**
     *  @var int
     */
    private $idperson; 

    /**
     * @var int
     */
    private $logintype;      
    
    /**
     * @var string
     */
    private $name;    
    
    /**
     * @var string
     */
    private $login;    
    
    /**
     * @var int
     */
    private $idtypeperson;
    
    /**
     * @var bool
     */
    private $isActiveHdk;
    
    /**
     * @var int
     */
    private $idcompany;
    
    /**
     * @var string
     */
    private $companyName;
    
    /**
     * @var string
     */
    private $groupId;
    
    /**
     * @var string
     */
    private $userStatus;

    /**
     * @var string
     */
    private $frmPassword;

    /**
     * @var string
     */
    private $passwordEncrypted;

    /**
     * @var string
     */
    private $frmToken;

    /**
     * Get the value of idperson
     *
     * @return  int
     */ 
    public function getIdperson(): int
    {
        return $this->idperson;
    }

    /**
     * Get the value of logintype
     *
     * @return  int
     */ 
    public function getLogintype(): int
    {
        return $this->logintype;
    }

    /**
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of login
     *
     * @return  string
     */ 
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * Get the value of idtypeperson
     *
     * @return  int
     */ 
    public function getIdtypeperson(): int
    {
        return $this->idtypeperson;
    }

    /**
     * Set the value of idperson
     *
     * @param  int  $idperson
     * @return  self
     */ 
    public function setIdperson(int $idperson): self
    {
        $this->idperson = $idperson;
        return $this;
    }

    /**
     * Set the value of logintype
     *
     * @param  int  $logintype
     * @return  self
     */ 
    public function setLogintype(int $logintype): self
    {
        $this->logintype = $logintype;
        return $this;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     * @return  self
     */ 
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the value of login
     *
     * @param  string  $login
     * @return  self
     */ 
    public function setLogin(string $login): self
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Set the value of idtypeperson
     *
     * @param  int  $idtypeperson
     * @return  self
     */ 
    public function setIdtypeperson(int $idtypeperson): self
    {
        $this->idtypeperson = $idtypeperson;
        return $this;
    }

    /**
     * Get the value of isActiveHdk
     *
     * @return  bool
     */ 
    public function getIsActiveHdk(): bool
    {
        return $this->isActiveHdk;
    }

    /**
     * Set the value of isActiveHdk
     *
     * @param  bool  $isActiveHdk
     *
     * @return  self
     */ 
    public function setIsActiveHdk(bool $isActiveHdk): self
    {
        $this->isActiveHdk = $isActiveHdk;

        return $this;
    }

    /**
     * Get the value of idcompany
     *
     * @return  int
     */ 
    public function getIdcompany(): int
    {
        return $this->idcompany;
    }

    /**
     * Set the value of idcompany
     *
     * @param  int  $idcompany
     *
     * @return  self
     */ 
    public function setIdcompany(int $idcompany): self
    {
        $this->idcompany = $idcompany;

        return $this;
    }

    /**
     * Get the value of companyName
     *
     * @return  string
     */ 
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * Set the value of companyName
     *
     * @param  string  $companyName
     *
     * @return  self
     */ 
    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get the value of groupId
     *
     * @return  string
     */ 
    public function getGroupId(): string
    {
        return $this->groupId;
    }

    /**
     * Set the value of groupId
     *
     * @param  string  $groupId
     *
     * @return  self
     */ 
    public function setGroupId(string $groupId): self
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get the value of userStatus
     *
     * @return  string
     */ 
    public function getUserStatus(): string
    {
        return $this->userStatus;
    }

    /**
     * Set the value of userStatus
     *
     * @param  string  $userStatus
     *
     * @return  self
     */ 
    public function setUserStatus(string $userStatus): self
    {
        $this->userStatus = $userStatus;

        return $this;
    }

    /**
     * Get the value of frmPassword
     *
     * @return  string
     */ 
    public function getFrmPassword(): string
    {
        return $this->frmPassword;
    }

    /**
     * Set the value of frmPassword
     *
     * @param  string  $frmPassword
     *
     * @return  self
     */ 
    public function setFrmPassword(string $frmPassword): self
    {
        $this->frmPassword = $frmPassword;

        return $this;
    }

    /**
     * Get the value of passwordEncrypted
     *
     * @return  string
     */ 
    public function getPasswordEncrypted(): string
    {
        return $this->passwordEncrypted;
    }

    /**
     * Set the value of passwordEncrypted
     *
     * @param  string  $passwordEncrypted
     *
     * @return  self
     */ 
    public function setPasswordEncrypted(string $passwordEncrypted): self
    {
        $this->passwordEncrypted = $passwordEncrypted;

        return $this;
    }

    /**
     * Get the value of frmToken
     *
     * @return  string
     */ 
    public function getFrmToken(): string
    {
        return $this->frmToken;
    }

    /**
     * Set the value of frmToken
     *
     * @param  string  $frmToken
     *
     * @return  self
     */ 
    public function setFrmToken(string $frmToken): self
    {
        $this->frmToken = $frmToken;

        return $this;
    }
}