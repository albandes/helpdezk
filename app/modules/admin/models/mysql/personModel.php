<?php
 
namespace App\modules\admin\models\mysql;

final class personModel
{    
    /**
     * @var int
     */
    private $idperson;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $userVip;

    /**
     * @var string
     */
    private $telephone;
    
    /**
     * @var string
     */
    private $branchNumber;
    	    
    /**
     * @var string
     */
    private $cellphone;
        
    /**
     * @var string
     */
    private $typeperson;
        
    /**
     * @var int
     */
    private $idtypeperson;
    
    /**
     * @var string
     */
    private $country;
    
    /**
     * @var int
     */
    private $idcountry;
    
    /**
     * @var string
     */
    private $state;
    
    /**
     * @var string
     */
    private $stateAbbr;
    
    /**
     * @var int
     */
    private $idstate;
    
    /**
     * @var string
     */
    private $neighborhood;
    
    /**
     * @var int
     */
    private $idneighborhood;
    
    /**
     * @var string
     */
    private $city;
    
    /**
     * @var int
     */
    private $idcity;
    
    /**
     * @var string
     */
    private $typestreet;
    
    /**
     * @var int
     */
    private $idtypestreet;
    
    /**
     * @var string
     */
    private $street;
    
    /**
     * @var string
     */
    private $number;
    
    /**
     * @var string
     */
    private $complement;
    
    /**
     * @var string
     */
    private $zipcode;
    
    /**
     * @var string
     */
    private $zipcodeFmt;
    
    /**
     * @var string
     */
    private $ssnCpf;
    
    /**
     * @var string
     */
    private $cpfFmt;
    
    /**
     * @var string
     */
    private $ssnFmt;
    
    /**
     * @var string
     */
    private $rg;
    
    /**
     * @var string
     */
    private $rgoexp;
    
    /**
     * @var string
     */
    private $dtbirth;
    
    /**
     * @var string
     */
    private $mother;
    
    /**
     * @var string
     */
    private $father;
    
    /**
     * @var string
     */
    private $gender;
    
    /**
     * @var int
     */
    private $iddepartment;
    
    /**
     * @var string
     */
    private $department;
    
    /**
     * @var string
     */
    private $company;
    
    /**
     * @var int
     */
    private $idcompany;
    
    /**
     * @var int
     */
    private $idtypelogin;
    
    /**
     * @var string
     */
    private $dtbirthFmt;
    
    /**
     * @var int
     */
    private $idstreet;

    /**
     * @var array
     */
    private $companyList;

    /**
     * @var array
     */
    private $stateList;
   

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
     * Set the value of idperson
     *
     * @param  int  $idperson
     *
     * @return  self
     */ 
    public function setIdperson(int $idperson): self
    {
        $this->idperson = $idperson;

        return $this;
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
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */ 
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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
     * Set the value of login
     *
     * @param  string  $login
     *
     * @return  self
     */ 
    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get the value of email
     *
     * @return  string
     */ 
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param  string  $email
     *
     * @return  self
     */ 
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of status
     *
     * @return  string
     */ 
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  string  $status
     *
     * @return  self
     */ 
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of userVip
     *
     * @return  string
     */ 
    public function getUserVip(): string
    {
        return $this->userVip;
    }

    /**
     * Set the value of userVip
     *
     * @param  string  $userVip
     *
     * @return  self
     */ 
    public function setUserVip(string $userVip): self
    {
        $this->userVip = $userVip;

        return $this;
    }

    /**
     * Get the value of telephone
     *
     * @return  string
     */ 
    public function getTelephone(): string
    {
        return $this->telephone;
    }

    /**
     * Set the value of telephone
     *
     * @param  string  $telephone
     *
     * @return  self
     */ 
    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get the value of branchNumber
     *
     * @return  string
     */ 
    public function getBranchNumber(): string
    {
        return $this->branchNumber;
    }

    /**
     * Set the value of branchNumber
     *
     * @param  string  $branchNumber
     *
     * @return  self
     */ 
    public function setBranchNumber(string $branchNumber): self
    {
        $this->branchNumber = $branchNumber;

        return $this;
    }

    /**
     * Get the value of cellphone
     *
     * @return  string
     */ 
    public function getCellphone(): string
    {
        return $this->cellphone;
    }

    /**
     * Set the value of cellphone
     *
     * @param  string  $cellphone
     *
     * @return  self
     */ 
    public function setCellphone(string $cellphone): self
    {
        $this->cellphone = $cellphone;

        return $this;
    }

    /**
     * Get the value of typeperson
     *
     * @return  string
     */ 
    public function getTypeperson(): string
    {
        return $this->typeperson;
    }

    /**
     * Set the value of typeperson
     *
     * @param  string  $typeperson
     *
     * @return  self
     */ 
    public function setTypeperson(string $typeperson): self
    {
        $this->typeperson = $typeperson;

        return $this;
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
     * Set the value of idtypeperson
     *
     * @param  int  $idtypeperson
     *
     * @return  self
     */ 
    public function setIdtypeperson(int $idtypeperson): self
    {
        $this->idtypeperson = $idtypeperson;

        return $this;
    }

    /**
     * Get the value of country
     *
     * @return  string
     */ 
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * Set the value of country
     *
     * @param  string  $country
     *
     * @return  self
     */ 
    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the value of idcountry
     *
     * @return  int
     */ 
    public function getIdcountry(): int
    {
        return $this->idcountry;
    }

    /**
     * Set the value of idcountry
     *
     * @param  int  $idcountry
     *
     * @return  self
     */ 
    public function setIdcountry(int $idcountry): self
    {
        $this->idcountry = $idcountry;

        return $this;
    }

    /**
     * Get the value of state
     *
     * @return  string
     */ 
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Set the value of state
     *
     * @param  string  $state
     *
     * @return  self
     */ 
    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get the value of stateAbbr
     *
     * @return  string
     */ 
    public function getStateAbbr(): string
    {
        return $this->stateAbbr;
    }

    /**
     * Set the value of stateAbbr
     *
     * @param  string  $stateAbbr
     *
     * @return  self
     */ 
    public function setStateAbbr(string $stateAbbr): self
    {
        $this->stateAbbr = $stateAbbr;

        return $this;
    }

    /**
     * Get the value of idstate
     *
     * @return  int
     */ 
    public function getIdstate(): int
    {
        return $this->idstate;
    }

    /**
     * Set the value of idstate
     *
     * @param  int  $idstate
     *
     * @return  self
     */ 
    public function setIdstate(int $idstate): self
    {
        $this->idstate = $idstate;

        return $this;
    }

    /**
     * Get the value of neighborhood
     *
     * @return  string
     */ 
    public function getNeighborhood(): string
    {
        return $this->neighborhood;
    }

    /**
     * Set the value of neighborhood
     *
     * @param  string  $neighborhood
     *
     * @return  self
     */ 
    public function setNeighborhood(string $neighborhood): self
    {
        $this->neighborhood = $neighborhood;

        return $this;
    }

    /**
     * Get the value of idneighborhood
     *
     * @return  int
     */ 
    public function getIdneighborhood(): int
    {
        return $this->idneighborhood;
    }

    /**
     * Set the value of idneighborhood
     *
     * @param  int  $idneighborhood
     *
     * @return  self
     */ 
    public function setIdneighborhood(int $idneighborhood): self
    {
        $this->idneighborhood = $idneighborhood;

        return $this;
    }

    /**
     * Get the value of city
     *
     * @return  string
     */ 
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @param  string  $city
     *
     * @return  self
     */ 
    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get the value of idcity
     *
     * @return  int
     */ 
    public function getIdcity(): int
    {
        return $this->idcity;
    }

    /**
     * Set the value of idcity
     *
     * @param  int  $idcity
     *
     * @return  self
     */ 
    public function setIdcity(int $idcity): self
    {
        $this->idcity = $idcity;

        return $this;
    }

    /**
     * Get the value of typestreet
     *
     * @return  string
     */ 
    public function getTypestreet(): string
    {
        return $this->typestreet;
    }

    /**
     * Set the value of typestreet
     *
     * @param  string  $typestreet
     *
     * @return  self
     */ 
    public function setTypestreet(string $typestreet): self
    {
        $this->typestreet = $typestreet;

        return $this;
    }

    /**
     * Get the value of idtypestreet
     *
     * @return  int
     */ 
    public function getIdtypestreet(): int
    {
        return $this->idtypestreet;
    }

    /**
     * Set the value of idtypestreet
     *
     * @param  int  $idtypestreet
     *
     * @return  self
     */ 
    public function setIdtypestreet(int $idtypestreet): self
    {
        $this->idtypestreet = $idtypestreet;

        return $this;
    }

    /**
     * Get the value of street
     *
     * @return  string
     */ 
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * Set the value of street
     *
     * @param  string  $street
     *
     * @return  self
     */ 
    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get the value of number
     *
     * @return  string
     */ 
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Set the value of number
     *
     * @param  string  $number
     *
     * @return  self
     */ 
    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get the value of complement
     *
     * @return  string
     */ 
    public function getComplement(): string
    {
        return $this->complement;
    }

    /**
     * Set the value of complement
     *
     * @param  string  $complement
     *
     * @return  self
     */ 
    public function setComplement(string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    /**
     * Get the value of zipcode
     *
     * @return  string
     */ 
    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    /**
     * Set the value of zipcode
     *
     * @param  string  $zipcode
     *
     * @return  self
     */ 
    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get the value of zipcodeFmt
     *
     * @return  string
     */ 
    public function getZipcodeFmt(): string
    {
        return $this->zipcodeFmt;
    }

    /**
     * Set the value of zipcodeFmt
     *
     * @param  string  $zipcodeFmt
     *
     * @return  self
     */ 
    public function setZipcodeFmt(string $zipcodeFmt): self
    {
        $this->zipcodeFmt = $zipcodeFmt;

        return $this;
    }

    /**
     * Get the value of ssnCpf
     *
     * @return  string
     */ 
    public function getSsnCpf(): string
    {
        return $this->ssnCpf;
    }

    /**
     * Set the value of ssnCpf
     *
     * @param  string  $ssnCpf
     *
     * @return  self
     */ 
    public function setSsnCpf(string $ssnCpf): self
    {
        $this->ssnCpf = $ssnCpf;

        return $this;
    }

    /**
     * Get the value of cpfFmt
     *
     * @return  string
     */ 
    public function getCpfFmt(): string
    {
        return $this->cpfFmt;
    }

    /**
     * Set the value of cpfFmt
     *
     * @param  string  $cpfFmt
     *
     * @return  self
     */ 
    public function setCpfFmt(string $cpfFmt): self
    {
        $this->cpfFmt = $cpfFmt;

        return $this;
    }

    /**
     * Get the value of ssnFmt
     *
     * @return  string
     */ 
    public function getSsnFmt(): string
    {
        return $this->ssnFmt;
    }

    /**
     * Set the value of ssnFmt
     *
     * @param  string  $ssnFmt
     *
     * @return  self
     */ 
    public function setSsnFmt(string $ssnFmt): self
    {
        $this->ssnFmt = $ssnFmt;

        return $this;
    }

    /**
     * Get the value of rg
     *
     * @return  string
     */ 
    public function getRg(): string
    {
        return $this->rg;
    }

    /**
     * Set the value of rg
     *
     * @param  string  $rg
     *
     * @return  self
     */ 
    public function setRg(string $rg): self
    {
        $this->rg = $rg;

        return $this;
    }

    /**
     * Get the value of rgoexp
     *
     * @return  string
     */ 
    public function getRgoexp(): string
    {
        return $this->rgoexp;
    }

    /**
     * Set the value of rgoexp
     *
     * @param  string  $rgoexp
     *
     * @return  self
     */ 
    public function setRgoexp(string $rgoexp): self
    {
        $this->rgoexp = $rgoexp;

        return $this;
    }

    /**
     * Get the value of dtbirth
     *
     * @return  string
     */ 
    public function getDtbirth(): string
    {
        return $this->dtbirth;
    }

    /**
     * Set the value of dtbirth
     *
     * @param  string  $dtbirth
     *
     * @return  self
     */ 
    public function setDtbirth(string $dtbirth): self
    {
        $this->dtbirth = $dtbirth;

        return $this;
    }

    /**
     * Get the value of mother
     *
     * @return  string
     */ 
    public function getMother(): string
    {
        return $this->mother;
    }

    /**
     * Set the value of mother
     *
     * @param  string  $mother
     *
     * @return  self
     */ 
    public function setMother(string $mother): self
    {
        $this->mother = $mother;

        return $this;
    }

    /**
     * Get the value of father
     *
     * @return  string
     */ 
    public function getFather(): string
    {
        return $this->father;
    }

    /**
     * Set the value of father
     *
     * @param  string  $father
     *
     * @return  self
     */ 
    public function setFather(string $father): self
    {
        $this->father = $father;

        return $this;
    }

    /**
     * Get the value of gender
     *
     * @return  string
     */ 
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * Set the value of gender
     *
     * @param  string  $gender
     *
     * @return  self
     */ 
    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get the value of iddepartment
     *
     * @return  int
     */ 
    public function getIddepartment(): int
    {
        return $this->iddepartment;
    }

    /**
     * Set the value of iddepartment
     *
     * @param  int  $iddepartment
     *
     * @return  self
     */ 
    public function setIddepartment(int $iddepartment): self
    {
        $this->iddepartment = $iddepartment;

        return $this;
    }

    /**
     * Get the value of department
     *
     * @return  string
     */ 
    public function getDepartment(): string
    {
        return $this->department;
    }

    /**
     * Set the value of department
     *
     * @param  string  $department
     *
     * @return  self
     */ 
    public function setDepartment(string $department): self
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get the value of company
     *
     * @return  string
     */ 
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * Set the value of company
     *
     * @param  string  $company
     *
     * @return  self
     */ 
    public function setCompany(string $company): self
    {
        $this->company = $company;

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
     * Get the value of idtypelogin
     *
     * @return  int
     */ 
    public function getIdtypelogin(): int
    {
        return $this->idtypelogin;
    }

    /**
     * Set the value of idtypelogin
     *
     * @param  int  $idtypelogin
     *
     * @return  self
     */ 
    public function setIdtypelogin(int $idtypelogin): self
    {
        $this->idtypelogin = $idtypelogin;

        return $this;
    }

    /**
     * Get the value of dtbirthFmt
     *
     * @return  string
     */ 
    public function getDtbirthFmt(): string
    {
        return $this->dtbirthFmt;
    }

    /**
     * Set the value of dtbirthFmt
     *
     * @param  string  $dtbirthFmt
     *
     * @return  self
     */ 
    public function setDtbirthFmt(string $dtbirthFmt): self
    {
        $this->dtbirthFmt = $dtbirthFmt;

        return $this;
    }

    /**
     * Get the value of idstreet
     *
     * @return  int
     */ 
    public function getIdstreet(): int
    {
        return $this->idstreet;
    }

    /**
     * Set the value of idstreet
     *
     * @param  int  $idstreet
     *
     * @return  self
     */ 
    public function setIdstreet(int $idstreet): self
    {
        $this->idstreet = $idstreet;

        return $this;
    }

    /**
     * Get the value of companyList
     *
     * @return  array
     */ 
    public function getCompanyList(): array
    {
        return $this->companyList;
    }

    /**
     * Set the value of companyList
     *
     * @param  array  $companyList
     *
     * @return  self
     */ 
    public function setCompanyList(array $companyList): self
    {
        $this->companyList = $companyList;

        return $this;
    }

    /**
     * Get the value of stateList
     *
     * @return  array
     */ 
    public function getStateList(): array
    {
        return $this->stateList;
    }

    /**
     * Set the value of stateList
     *
     * @param  array  $stateList
     *
     * @return  self
     */ 
    public function setStateList(array $stateList): self
    {
        $this->stateList = $stateList;

        return $this;
    }
}