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
     * @var array
     */
    private $gridList;
    
    /**
     * @var int
     */
    private $totalRows;
    
    /**
     * @var array
     */
    private $countryList;

    /**
     * @var array
     */
    private $citiesList;

    /**
     * @var array
     */
    private $neighborhoodList;

    /**
     * @var string
     */
    private $location;

    /**
     * @var array
     */
    private $streetTypeList;

    /**
     * @var array
     */
    private $streetList;

    /**
     * @var string
     */
    private $einCnpj;

    /**
     * @var array
     */
    private $loginTypeList;

    /**
     * @var array
     */
    private $naturalPersonTypeList;

    /**
     * @var array
     */
    private $juridicalPersonTypeList;

    /**
     * @var array
     */
    private $permissionGroupsList;

    /**
     * @var array
     */
    private $locationsList;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $confirmPassword;

    /**
     * @var string
     */
    private $fax;

    /**
     * @var float
     */
    private $timeValue;

    /**
     * @var float
     */
    private $overtimeWork;

    /**
     * @var int
     */
    private $locationId;

    /**
     * @var int
     */
    private $changePasswordFlag;

    /**
     * @var int
     */
    private $themeId;

    /**
     * @var string
     */
    private $contactName;

    /**
     * @var string
     */
    private $obsevation;

    /**
     * @var array
     */
    private $personGroupsList;

     /**
     * @var int
     */
    private $addressTypeId;

    /**
     * @var int
     */
    private $personNatureId;

    /**
     * @var string
     */
    private $personNature;

    /**
     * @var int
     */
    private $permissionGroupId;

    /**
     * @var int
     */
    private $groupId;

    /**
     * @var string
     */
    private $iestadual;

    /**
     * @var array
     */
    private $personGroupsIdList;

    /**
     * @var array
     */
    private $permissionGroupsIdList;

    /**
     * @var array
     */
    private $personList;
    

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

    /**
     * Get the value of gridList
     *
     * @return  array
     */ 
    public function getGridList()
    {
        return $this->gridList;
    }

    /**
     * Set the value of gridList
     *
     * @param  array  $gridList
     *
     * @return  self
     */ 
    public function setGridList(array $gridList)
    {
        $this->gridList = $gridList;

        return $this;
    }

    /**
     * Get the value of totalRows
     *
     * @return  int
     */ 
    public function getTotalRows()
    {
        return $this->totalRows;
    }

    /**
     * Set the value of totalRows
     *
     * @param  int  $totalRows
     *
     * @return  self
     */ 
    public function setTotalRows(int $totalRows)
    {
        $this->totalRows = $totalRows;

        return $this;
    }

    /**
     * Get the value of countryList
     *
     * @return  array
     */ 
    public function getCountryList()
    {
        return $this->countryList;
    }

    /**
     * Set the value of countryList
     *
     * @param  array  $countryList
     *
     * @return  self
     */ 
    public function setCountryList(array $countryList)
    {
        $this->countryList = $countryList;

        return $this;
    }

    /**
     * Get the value of citiesList
     *
     * @return  array
     */ 
    public function getCitiesList()
    {
        return $this->citiesList;
    }

    /**
     * Set the value of citiesList
     *
     * @param  array  $citiesList
     *
     * @return  self
     */ 
    public function setCitiesList(array $citiesList)
    {
        $this->citiesList = $citiesList;

        return $this;
    }

    /**
     * Get the value of neighborhoodList
     *
     * @return  array
     */ 
    public function getNeighborhoodList()
    {
        return $this->neighborhoodList;
    }

    /**
     * Set the value of neighborhoodList
     *
     * @param  array  $neighborhoodList
     *
     * @return  self
     */ 
    public function setNeighborhoodList(array $neighborhoodList)
    {
        $this->neighborhoodList = $neighborhoodList;

        return $this;
    }

    /**
     * Get the value of location
     *
     * @return  string
     */ 
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set the value of location
     *
     * @param  string  $location
     *
     * @return  self
     */ 
    public function setLocation(string $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get the value of streetTypeList
     *
     * @return  array
     */ 
    public function getStreetTypeList()
    {
        return $this->streetTypeList;
    }

    /**
     * Set the value of streetTypeList
     *
     * @param  array  $streetTypeList
     *
     * @return  self
     */ 
    public function setStreetTypeList(array $streetTypeList)
    {
        $this->streetTypeList = $streetTypeList;

        return $this;
    }

    /**
     * Get the value of streetList
     *
     * @return  array
     */ 
    public function getStreetList()
    {
        return $this->streetList;
    }

    /**
     * Set the value of streetList
     *
     * @param  array  $streetList
     *
     * @return  self
     */ 
    public function setStreetList(array $streetList)
    {
        $this->streetList = $streetList;

        return $this;
    }

    /**
     * Get the value of einCnpj
     *
     * @return  string
     */ 
    public function getEinCnpj()
    {
        return $this->einCnpj;
    }

    /**
     * Set the value of einCnpj
     *
     * @param  string  $einCnpj
     *
     * @return  self
     */ 
    public function setEinCnpj(string $einCnpj)
    {
        $this->einCnpj = $einCnpj;

        return $this;
    }

    /**
     * Get the value of loginTypeList
     *
     * @return  array
     */ 
    public function getLoginTypeList()
    {
        return $this->loginTypeList;
    }

    /**
     * Set the value of loginTypeList
     *
     * @param  array  $loginTypeList
     *
     * @return  self
     */ 
    public function setLoginTypeList(array $loginTypeList)
    {
        $this->loginTypeList = $loginTypeList;

        return $this;
    }

    /**
     * Get the value of naturalPersonTypeList
     *
     * @return  array
     */ 
    public function getNaturalPersonTypeList()
    {
        return $this->naturalPersonTypeList;
    }

    /**
     * Set the value of naturalPersonTypeList
     *
     * @param  array  $naturalPersonTypeList
     *
     * @return  self
     */ 
    public function setNaturalPersonTypeList(array $naturalPersonTypeList)
    {
        $this->naturalPersonTypeList = $naturalPersonTypeList;

        return $this;
    }

    /**
     * Get the value of juridicalPersonTypeList
     *
     * @return  array
     */ 
    public function getJuridicalPersonTypeList()
    {
        return $this->juridicalPersonTypeList;
    }

    /**
     * Set the value of juridicalPersonTypeList
     *
     * @param  array  $juridicalPersonTypeList
     *
     * @return  self
     */ 
    public function setJuridicalPersonTypeList(array $juridicalPersonTypeList)
    {
        $this->juridicalPersonTypeList = $juridicalPersonTypeList;

        return $this;
    }

    /**
     * Get the value of permissionGroupsList
     *
     * @return  array
     */ 
    public function getPermissionGroupsList()
    {
        return $this->permissionGroupsList;
    }

    /**
     * Set the value of permissionGroupsList
     *
     * @param  array  $permissionGroupsList
     *
     * @return  self
     */ 
    public function setPermissionGroupsList(array $permissionGroupsList)
    {
        $this->permissionGroupsList = $permissionGroupsList;

        return $this;
    }

    /**
     * Get the value of locationsList
     *
     * @return  array
     */ 
    public function getLocationsList()
    {
        return $this->locationsList;
    }

    /**
     * Set the value of locationsList
     *
     * @param  array  $locationsList
     *
     * @return  self
     */ 
    public function setLocationsList(array $locationsList)
    {
        $this->locationsList = $locationsList;

        return $this;
    }

    /**
     * Get the value of password
     *
     * @return  string
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @param  string  $password
     *
     * @return  self
     */ 
    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of confirmPassword
     *
     * @return  string
     */ 
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * Set the value of confirmPassword
     *
     * @param  string  $confirmPassword
     *
     * @return  self
     */ 
    public function setConfirmPassword(string $confirmPassword)
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    /**
     * Get the value of fax
     *
     * @return  string
     */ 
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set the value of fax
     *
     * @param  string  $fax
     *
     * @return  self
     */ 
    public function setFax(string $fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get the value of timeValue
     *
     * @return  float
     */ 
    public function getTimeValue()
    {
        return $this->timeValue;
    }

    /**
     * Set the value of timeValue
     *
     * @param  float  $timeValue
     *
     * @return  self
     */ 
    public function setTimeValue(float $timeValue)
    {
        $this->timeValue = $timeValue;

        return $this;
    }

    /**
     * Get the value of overtimeWork
     *
     * @return  float
     */ 
    public function getOvertimeWork()
    {
        return $this->overtimeWork;
    }

    /**
     * Set the value of overtimeWork
     *
     * @param  float  $overtimeWork
     *
     * @return  self
     */ 
    public function setOvertimeWork(float $overtimeWork)
    {
        $this->overtimeWork = $overtimeWork;

        return $this;
    }

    /**
     * Get the value of locationId
     *
     * @return  int
     */ 
    public function getLocationId()
    {
        return $this->locationId;
    }

    /**
     * Set the value of locationId
     *
     * @param  int  $locationId
     *
     * @return  self
     */ 
    public function setLocationId(int $locationId)
    {
        $this->locationId = $locationId;

        return $this;
    }

    /**
     * Get the value of changePasswordFlag
     *
     * @return  int
     */ 
    public function getChangePasswordFlag()
    {
        return $this->changePasswordFlag;
    }

    /**
     * Set the value of changePasswordFlag
     *
     * @param  int  $changePasswordFlag
     *
     * @return  self
     */ 
    public function setChangePasswordFlag(int $changePasswordFlag)
    {
        $this->changePasswordFlag = $changePasswordFlag;

        return $this;
    }

    /**
     * Get the value of themeId
     *
     * @return  int
     */ 
    public function getThemeId()
    {
        return $this->themeId;
    }

    /**
     * Set the value of themeId
     *
     * @param  int  $themeId
     *
     * @return  self
     */ 
    public function setThemeId(int $themeId)
    {
        $this->themeId = $themeId;

        return $this;
    }

    /**
     * Get the value of contactName
     *
     * @return  string
     */ 
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set the value of contactName
     *
     * @param  string  $contactName
     *
     * @return  self
     */ 
    public function setContactName(string $contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * Get the value of obsevation
     *
     * @return  string
     */ 
    public function getObsevation()
    {
        return $this->obsevation;
    }

    /**
     * Set the value of obsevation
     *
     * @param  string  $obsevation
     *
     * @return  self
     */ 
    public function setObsevation(string $obsevation)
    {
        $this->obsevation = $obsevation;

        return $this;
    }

    /**
     * Get the value of personGroupsList
     *
     * @return  array
     */ 
    public function getPersonGroupsList()
    {
        return $this->personGroupsList;
    }

    /**
     * Set the value of personGroupsList
     *
     * @param  array  $personGroupsList
     *
     * @return  self
     */ 
    public function setPersonGroupsList(array $personGroupsList)
    {
        $this->personGroupsList = $personGroupsList;

        return $this;
    }

    /**
     * Get the value of addressTypeId
     *
     * @return  int
     */ 
    public function getAddressTypeId()
    {
        return $this->addressTypeId;
    }

    /**
     * Set the value of addressTypeId
     *
     * @param  int  $addressTypeId
     *
     * @return  self
     */ 
    public function setAddressTypeId(int $addressTypeId)
    {
        $this->addressTypeId = $addressTypeId;

        return $this;
    }

    /**
     * Get the value of personNatureId
     *
     * @return  int
     */ 
    public function getPersonNatureId()
    {
        return $this->personNatureId;
    }

    /**
     * Set the value of personNatureId
     *
     * @param  int  $personNatureId
     *
     * @return  self
     */ 
    public function setPersonNatureId(int $personNatureId)
    {
        $this->personNatureId = $personNatureId;

        return $this;
    }

    /**
     * Get the value of personNature
     *
     * @return  string
     */ 
    public function getPersonNature()
    {
        return $this->personNature;
    }

    /**
     * Set the value of personNature
     *
     * @param  string  $personNature
     *
     * @return  self
     */ 
    public function setPersonNature(string $personNature)
    {
        $this->personNature = $personNature;

        return $this;
    }

    /**
     * Get the value of permissionGroupId
     *
     * @return  int
     */ 
    public function getPermissionGroupId()
    {
        return $this->permissionGroupId;
    }

    /**
     * Set the value of permissionGroupId
     *
     * @param  int  $permissionGroupId
     *
     * @return  self
     */ 
    public function setPermissionGroupId(int $permissionGroupId)
    {
        $this->permissionGroupId = $permissionGroupId;

        return $this;
    }

    /**
     * Get the value of groupId
     *
     * @return  int
     */ 
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set the value of groupId
     *
     * @param  int  $groupId
     *
     * @return  self
     */ 
    public function setGroupId(int $groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get the value of iestadual
     *
     * @return  string
     */ 
    public function getIestadual()
    {
        return $this->iestadual;
    }

    /**
     * Set the value of iestadual
     *
     * @param  string  $iestadual
     *
     * @return  self
     */ 
    public function setIestadual(string $iestadual)
    {
        $this->iestadual = $iestadual;

        return $this;
    }

    /**
     * Get the value of personGroupsIdList
     *
     * @return  array
     */ 
    public function getPersonGroupsIdList()
    {
        return $this->personGroupsIdList;
    }

    /**
     * Set the value of personGroupsIdList
     *
     * @param  array  $personGroupsIdList
     *
     * @return  self
     */ 
    public function setPersonGroupsIdList(array $personGroupsIdList)
    {
        $this->personGroupsIdList = $personGroupsIdList;

        return $this;
    }

    /**
     * Get the value of permissionGroupsIdList
     *
     * @return  array
     */ 
    public function getPermissionGroupsIdList()
    {
        return $this->permissionGroupsIdList;
    }

    /**
     * Set the value of permissionGroupsIdList
     *
     * @param  array  $permissionGroupsIdList
     *
     * @return  self
     */ 
    public function setPermissionGroupsIdList(array $permissionGroupsIdList)
    {
        $this->permissionGroupsIdList = $permissionGroupsIdList;

        return $this;
    }

    /**
     * Get the value of personList
     *
     * @return  array
     */ 
    public function getPersonList()
    {
        return $this->personList;
    }

    /**
     * Set the value of personList
     *
     * @param  array  $personList
     *
     * @return  self
     */ 
    public function setPersonList(array $personList)
    {
        $this->personList = $personList;

        return $this;
    }
}