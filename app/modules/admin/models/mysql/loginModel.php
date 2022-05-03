<?php

namespace App\modules\admin\models\mysql;

final class loginModel
{
    /**
     *  @var int
     */
    private $idPerson; 

    /**
     * @var int
     */
    private $loginType;      
    
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
    private $idTypePerson;
    
    /**
     * @var bool
     */
    private $isActiveHdk;
    
    /**
     * @var int
     */
    private $idCompany;
    
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
     * @var int
     */
    private $totalRequests;

    /**
     * @var string
     */
    private $requestCode;

    /**
     * Get the value of idPerson
     *
     * @return  int
     */ 
    public function getIdPerson(): int
    {
        return $this->idPerson;
    }

    /**
     * Get the value of loginType
     *
     * @return  int
     */ 
    public function getLoginType(): int
    {
        return $this->loginType;
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
     * Get the value of idTypePerson
     *
     * @return  int
     */ 
    public function getIdTypePerson(): int
    {
        return $this->idTypePerson;
    }

    /**
     * Set the value of idPerson
     *
     * @param  int  $idPerson
     * @return  self
     */ 
    public function setIdPerson(int $idPerson): self
    {
        $this->idPerson = $idPerson;
        return $this;
    }

    /**
     * Set the value of loginType
     *
     * @param  int  $loginType
     * @return  self
     */ 
    public function setLoginType(int $loginType): self
    {
        $this->loginType = $loginType;
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
     * Set the value of idTypePerson
     *
     * @param  int  $idTypePerson
     * @return  self
     */ 
    public function setIdTypePerson(int $idTypePerson): self
    {
        $this->idTypePerson = $idTypePerson;
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
     * Get the value of idCompany
     *
     * @return  int
     */ 
    public function getIdCompany(): int
    {
        return $this->idCompany;
    }

    /**
     * Set the value of idCompany
     *
     * @param  int  $idCompany
     *
     * @return  self
     */ 
    public function setIdCompany(int $idCompany): self
    {
        $this->idCompany = $idCompany;

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

    /**
     * Get the value of totalRequests
     *
     * @return  int
     */ 
    public function getTotalRequests(): int
    {
        return $this->totalRequests;
    }

    /**
     * Set the value of totalRequests
     *
     * @param  int  $totalRequests
     *
     * @return  self
     */ 
    public function setTotalRequests(int $totalRequests): self
    {
        $this->totalRequests = $totalRequests;

        return $this;
    }

    /**
     * Get the value of requestCode
     *
     * @return  string
     */ 
    public function getRequestCode(): string
    {
        return $this->requestCode;
    }

    /**
     * Set the value of requestCode
     *
     * @param  string  $requestCode
     *
     * @return  self
     */ 
    public function setRequestCode(string $requestCode): self
    {
        $this->requestCode = $requestCode;

        return $this;
    }
}