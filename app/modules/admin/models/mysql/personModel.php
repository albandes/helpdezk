<?php
 
namespace App\modules\admin\models\mysql;

final class personModel
{    
    /**
     * @var int
     */
    private $idPerson;

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
    private $TypePerson;
        
    /**
     * @var int
     */
    private $idTypePerson;
    
    /**
     * @var string
     */
    private $country;
    
    /**
     * @var int
     */
    private $idCountry;
    
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
    private $idState;
    
    /**
     * @var string
     */
    private $neighborhood;
    
    /**
     * @var int
     */
    private $idNeighborhood;
    
    /**
     * @var string
     */
    private $city;
    
    /**
     * @var int
     */
    private $idCity;
    
    /**
     * @var string
     */
    private $typeStreet;
    
    /**
     * @var int
     */
    private $idTypeStreet;
    
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
    private $zipCode;
    
    /**
     * @var string
     */
    private $zipCodeFmt;
    
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
    private $rgoExp;
    
    /**
     * @var string
     */
    private $dtBirth;
    
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
    private $idDepartment;
    
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
    private $idCompany;
    
    /**
     * @var int
     */
    private $idTypeLogin;
    
    /**
     * @var string
     */
    private $dtBirthFmt;
    
    /**
     * @var int
     */
    private $idStreet;

    /**
     * @var array
     */
    private $companyList;

    /**
     * @var array
     */
    private $stateList;
   

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
     * Set the value of idPerson
     *
     * @param  int  $idPerson
     *
     * @return  self
     */ 
    public function setIdPerson(int $idPerson): self
    {
        $this->idPerson = $idPerson;

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
     * Get the value of TypePerson
     *
     * @return  string
     */ 
    public function getTypePerson(): string
    {
        return $this->TypePerson;
    }

    /**
     * Set the value of TypePerson
     *
     * @param  string  $TypePerson
     *
     * @return  self
     */ 
    public function setTypePerson(string $TypePerson): self
    {
        $this->TypePerson = $TypePerson;

        return $this;
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
     * Set the value of idTypePerson
     *
     * @param  int  $idTypePerson
     *
     * @return  self
     */ 
    public function setIdTypePerson(int $idTypePerson): self
    {
        $this->idTypePerson = $idTypePerson;

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
     * Get the value of idCountry
     *
     * @return  int
     */ 
    public function getIdCountry(): int
    {
        return $this->idCountry;
    }

    /**
     * Set the value of idCountry
     *
     * @param  int  $idCountry
     *
     * @return  self
     */ 
    public function setIdCountry(int $idCountry): self
    {
        $this->idCountry = $idCountry;

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
     * Get the value of idState
     *
     * @return  int
     */ 
    public function getIdState(): int
    {
        return $this->idState;
    }

    /**
     * Set the value of idState
     *
     * @param  int  $idState
     *
     * @return  self
     */ 
    public function setIdState(int $idState): self
    {
        $this->idState = $idState;

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
     * Get the value of idNeighborhood
     *
     * @return  int
     */ 
    public function getIdNeighborhood(): int
    {
        return $this->idNeighborhood;
    }

    /**
     * Set the value of idNeighborhood
     *
     * @param  int  $idNeighborhood
     *
     * @return  self
     */ 
    public function setIdNeighborhood(int $idNeighborhood): self
    {
        $this->idNeighborhood = $idNeighborhood;

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
     * Get the value of idCity
     *
     * @return  int
     */ 
    public function getIdCity(): int
    {
        return $this->idCity;
    }

    /**
     * Set the value of idCity
     *
     * @param  int  $idCity
     *
     * @return  self
     */ 
    public function setIdCity(int $idCity): self
    {
        $this->idCity = $idCity;

        return $this;
    }

    /**
     * Get the value of typeStreet
     *
     * @return  string
     */ 
    public function getTypeStreet(): string
    {
        return $this->typeStreet;
    }

    /**
     * Set the value of typeStreet
     *
     * @param  string  $typeStreet
     *
     * @return  self
     */ 
    public function setTypeStreet(string $typeStreet): self
    {
        $this->typeStreet = $typeStreet;

        return $this;
    }

    /**
     * Get the value of idTypeStreet
     *
     * @return  int
     */ 
    public function getIdTypeStreet(): int
    {
        return $this->idTypeStreet;
    }

    /**
     * Set the value of idTypeStreet
     *
     * @param  int  $idTypeStreet
     *
     * @return  self
     */ 
    public function setIdTypeStreet(int $idTypeStreet): self
    {
        $this->idTypeStreet = $idTypeStreet;

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
     * Get the value of zipCode
     *
     * @return  string
     */ 
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * Set the value of zipCode
     *
     * @param  string  $zipCode
     *
     * @return  self
     */ 
    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get the value of zipCodeFmt
     *
     * @return  string
     */ 
    public function getZipCodeFmt(): string
    {
        return $this->zipCodeFmt;
    }

    /**
     * Set the value of zipCodeFmt
     *
     * @param  string  $zipCodeFmt
     *
     * @return  self
     */ 
    public function setZipCodeFmt(string $zipCodeFmt): self
    {
        $this->zipCodeFmt = $zipCodeFmt;

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
     * Get the value of rgoExp
     *
     * @return  string
     */ 
    public function getRgoExp(): string
    {
        return $this->rgoExp;
    }

    /**
     * Set the value of rgoExp
     *
     * @param  string  $rgoExp
     *
     * @return  self
     */ 
    public function setRgoExp(string $rgoExp): self
    {
        $this->rgoExp = $rgoExp;

        return $this;
    }

    /**
     * Get the value of dtBirth
     *
     * @return  string
     */ 
    public function getDtBirth(): string
    {
        return $this->dtBirth;
    }

    /**
     * Set the value of dtBirth
     *
     * @param  string  $dtBirth
     *
     * @return  self
     */ 
    public function setDtBirth(string $dtBirth): self
    {
        $this->dtBirth = $dtBirth;

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
     * Get the value of idDepartment
     *
     * @return  int
     */ 
    public function getIdDepartment(): int
    {
        return $this->idDepartment;
    }

    /**
     * Set the value of idDepartment
     *
     * @param  int  $idDepartment
     *
     * @return  self
     */ 
    public function setIdDepartment(int $idDepartment): self
    {
        $this->idDepartment = $idDepartment;

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
     * Get the value of idTypeLogin
     *
     * @return  int
     */ 
    public function getIdTypeLogin(): int
    {
        return $this->idTypeLogin;
    }

    /**
     * Set the value of idTypeLogin
     *
     * @param  int  $idTypeLogin
     *
     * @return  self
     */ 
    public function setIdTypeLogin(int $idTypeLogin): self
    {
        $this->idTypeLogin = $idTypeLogin;

        return $this;
    }

    /**
     * Get the value of dtBirthFmt
     *
     * @return  string
     */ 
    public function getDtBirthFmt(): string
    {
        return $this->dtBirthFmt;
    }

    /**
     * Set the value of dtBirthFmt
     *
     * @param  string  $dtBirthFmt
     *
     * @return  self
     */ 
    public function setDtBirthFmt(string $dtBirthFmt): self
    {
        $this->dtBirthFmt = $dtBirthFmt;

        return $this;
    }

    /**
     * Get the value of idStreet
     *
     * @return  int
     */ 
    public function getIdStreet(): int
    {
        return $this->idStreet;
    }

    /**
     * Set the value of idStreet
     *
     * @param  int  $idStreet
     *
     * @return  self
     */ 
    public function setIdStreet(int $idStreet): self
    {
        $this->idStreet = $idStreet;

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