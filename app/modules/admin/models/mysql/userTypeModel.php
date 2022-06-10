<?php

namespace App\modules\admin\models\mysql;

final class userTypeModel
{    
    /**
     * @var int
     */
    private $idUserType;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var string
     */
    private $permissionGroup; 
    
    /**
     * @var string
     */
    private $langKeyName;    

    /**
     * @var string
     */
    private $status; 
    
    /**
     * @var array
     */
    private $gridList; 
    
    /**
    * @var int
    */
    private $totalRows; 

    /**
     * Get the value of idUserType
     *
     * @return  int
     */ 
    public function getIdUserType()
    {
        return $this->idUserType;
    }

    /**
     * Set the value of idUserType
     *
     * @param  int  $idUserType
     *
     * @return  self
     */ 
    public function setIdUserType(int $idUserType)
    {
        $this->idUserType = $idUserType;

        return $this;
    }

    /**
     * Get the value of userType
     *
     * @return  string
     */ 
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * Set the value of userType
     *
     * @param  string  $userType
     *
     * @return  self
     */ 
    public function setUserType(string $userType)
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * Get the value of permissionGroup
     *
     * @return  string
     */ 
    public function getPermissionGroup()
    {
        return $this->permissionGroup;
    }

    /**
     * Set the value of permissionGroup
     *
     * @param  string  $permissionGroup
     *
     * @return  self
     */ 
    public function setPermissionGroup(string $permissionGroup)
    {
        $this->permissionGroup = $permissionGroup;

        return $this;
    }

    /**
     * Get the value of langKeyName
     *
     * @return  string
     */ 
    public function getLangKeyName()
    {
        return $this->langKeyName;
    }

    /**
     * Set the value of langKeyName
     *
     * @param  string  $langKeyName
     *
     * @return  self
     */ 
    public function setLangKeyName(string $langKeyName)
    {
        $this->langKeyName = $langKeyName;

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
}