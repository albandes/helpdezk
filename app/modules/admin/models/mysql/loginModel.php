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
}