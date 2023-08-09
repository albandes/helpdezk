<?php

namespace App\modules\helpdezk\models\mysql;

final class groupModel
{    
    /**
     * @var int
     */
    private $idGroup;

    /**
     * @var string
     */
    private $groupName;

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
    private $status;
    
    /**
     * @var string
     */
    private $isRepassOnly;
    
    /**
     * @var array
     */
    private $gridList; 
    
    /**
    * @var int
    */
    private $totalRows;
    
    /**
     * @var int
     */
    private $newIdGroup;

    /**
     * @var int
     */
    private $idUser;

    /**
     * @var string
     */
    private $groupLevel;

    /**
     * @var int
     */
    private $personId;

    /**
     * @var int
     */
    private $inGroupFlag;

    /**
     * Get the value of idGroup
     *
     * @return  int
     */ 
    public function getIdGroup()
    {
        return $this->idGroup;
    }

    /**
     * Set the value of idGroup
     *
     * @param  int  $idGroup
     *
     * @return  self
     */ 
    public function setIdGroup(int $idGroup)
    {
        $this->idGroup = $idGroup;

        return $this;
    }

    /**
     * Get the value of groupName
     *
     * @return  string
     */ 
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set the value of groupName
     *
     * @param  string  $groupName
     *
     * @return  self
     */ 
    public function setGroupName(string $groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get the value of idCompany
     *
     * @return  int
     */ 
    public function getIdCompany()
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
    public function setIdCompany(int $idCompany)
    {
        $this->idCompany = $idCompany;

        return $this;
    }

    /**
     * Get the value of companyName
     *
     * @return  string
     */ 
    public function getCompanyName()
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
    public function setCompanyName(string $companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get the value of status
     *
     * @return  string
     */ 
    public function getStatus()
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
    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of isRepassOnly
     *
     * @return  string
     */ 
    public function getIsRepassOnly()
    {
        return $this->isRepassOnly;
    }

    /**
     * Set the value of isRepassOnly
     *
     * @param  string  $isRepassOnly
     *
     * @return  self
     */ 
    public function setIsRepassOnly(string $isRepassOnly)
    {
        $this->isRepassOnly = $isRepassOnly;

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
     * Get the value of newIdGroup
     *
     * @return  int
     */ 
    public function getNewIdGroup()
    {
        return $this->newIdGroup;
    }

    /**
     * Set the value of newIdGroup
     *
     * @param  int  $newIdGroup
     *
     * @return  self
     */ 
    public function setNewIdGroup(int $newIdGroup)
    {
        $this->newIdGroup = $newIdGroup;

        return $this;
    }

    /**
     * Get the value of idUser
     *
     * @return  int
     */ 
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set the value of idUser
     *
     * @param  int  $idUser
     *
     * @return  self
     */ 
    public function setIdUser(int $idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get the value of groupLevel
     *
     * @return  string
     */ 
    public function getGroupLevel()
    {
        return $this->groupLevel;
    }

    /**
     * Set the value of groupLevel
     *
     * @param  string  $groupLevel
     *
     * @return  self
     */ 
    public function setGroupLevel(string $groupLevel)
    {
        $this->groupLevel = $groupLevel;

        return $this;
    }

    /**
     * Get the value of personId
     *
     * @return  int
     */ 
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set the value of personId
     *
     * @param  int  $personId
     *
     * @return  self
     */ 
    public function setPersonId(int $personId)
    {
        $this->personId = $personId;

        return $this;
    }

    /**
     * Get the value of inGroupFlag
     *
     * @return  int
     */ 
    public function getInGroupFlag()
    {
        return $this->inGroupFlag;
    }

    /**
     * Set the value of inGroupFlag
     *
     * @param  int  $inGroupFlag
     *
     * @return  self
     */ 
    public function setInGroupFlag(int $inGroupFlag)
    {
        $this->inGroupFlag = $inGroupFlag;

        return $this;
    }
}