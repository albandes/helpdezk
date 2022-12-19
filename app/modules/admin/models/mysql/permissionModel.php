<?php
 
namespace App\modules\admin\models\mysql;

final class permissionModel
{    
    /**
     * @var int
     */
    private $permissionId;

    /**
     * @var int
     */
    private $accessTypeId;

    /**
     * @var string
     */
    private $accessType;

    /**
     * @var int
     */
    private $defaultPermissionId;

    /**
     * @var int
     */
    private $programId;

    /**
     * @var string
     */
    private $programName;

    /**
     * @var int
     */
    private $personId;

    /**
     * @var string
     */
    private $personName;

    /**
     * @var string
     */
    private $allow;

    /**
     * @var array
     */
    private $defaultPermissionList;

    /**
     * @var array
     */
    private $userPermissionList;

    /**
     * Get the value of permissionId
     *
     * @return  int
     */ 
    public function getPermissionId()
    {
        return $this->permissionId;
    }

    /**
     * Set the value of permissionId
     *
     * @param  int  $permissionId
     *
     * @return  self
     */ 
    public function setPermissionId(int $permissionId)
    {
        $this->permissionId = $permissionId;

        return $this;
    }

    /**
     * Get the value of accessTypeId
     *
     * @return  int
     */ 
    public function getAccessTypeId()
    {
        return $this->accessTypeId;
    }

    /**
     * Set the value of accessTypeId
     *
     * @param  int  $accessTypeId
     *
     * @return  self
     */ 
    public function setAccessTypeId(int $accessTypeId)
    {
        $this->accessTypeId = $accessTypeId;

        return $this;
    }

    /**
     * Get the value of accessType
     *
     * @return  string
     */ 
    public function getAccessType()
    {
        return $this->accessType;
    }

    /**
     * Set the value of accessType
     *
     * @param  string  $accessType
     *
     * @return  self
     */ 
    public function setAccessType(string $accessType)
    {
        $this->accessType = $accessType;

        return $this;
    }

    /**
     * Get the value of defaultPermissionId
     *
     * @return  int
     */ 
    public function getDefaultPermissionId()
    {
        return $this->defaultPermissionId;
    }

    /**
     * Set the value of defaultPermissionId
     *
     * @param  int  $defaultPermissionId
     *
     * @return  self
     */ 
    public function setDefaultPermissionId(int $defaultPermissionId)
    {
        $this->defaultPermissionId = $defaultPermissionId;

        return $this;
    }

    /**
     * Get the value of programId
     *
     * @return  int
     */ 
    public function getProgramId()
    {
        return $this->programId;
    }

    /**
     * Set the value of programId
     *
     * @param  int  $programId
     *
     * @return  self
     */ 
    public function setProgramId(int $programId)
    {
        $this->programId = $programId;

        return $this;
    }

    /**
     * Get the value of programName
     *
     * @return  string
     */ 
    public function getProgramName()
    {
        return $this->programName;
    }

    /**
     * Set the value of programName
     *
     * @param  string  $programName
     *
     * @return  self
     */ 
    public function setProgramName(string $programName)
    {
        $this->programName = $programName;

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
     * Get the value of personName
     *
     * @return  string
     */ 
    public function getPersonName()
    {
        return $this->personName;
    }

    /**
     * Set the value of personName
     *
     * @param  string  $personName
     *
     * @return  self
     */ 
    public function setPersonName(string $personName)
    {
        $this->personName = $personName;

        return $this;
    }

    /**
     * Get the value of allow
     *
     * @return  string
     */ 
    public function getAllow()
    {
        return $this->allow;
    }

    /**
     * Set the value of allow
     *
     * @param  string  $allow
     *
     * @return  self
     */ 
    public function setAllow(string $allow)
    {
        $this->allow = $allow;

        return $this;
    }

    /**
     * Get the value of defaultPermissionList
     *
     * @return  array
     */ 
    public function getDefaultPermissionList()
    {
        return $this->defaultPermissionList;
    }

    /**
     * Set the value of defaultPermissionList
     *
     * @param  array  $defaultPermissionList
     *
     * @return  self
     */ 
    public function setDefaultPermissionList(array $defaultPermissionList)
    {
        $this->defaultPermissionList = $defaultPermissionList;

        return $this;
    }

    /**
     * Get the value of userPermissionList
     *
     * @return  array
     */ 
    public function getUserPermissionList()
    {
        return $this->userPermissionList;
    }

    /**
     * Set the value of userPermissionList
     *
     * @param  array  $userPermissionList
     *
     * @return  self
     */ 
    public function setUserPermissionList(array $userPermissionList)
    {
        $this->userPermissionList = $userPermissionList;

        return $this;
    }
}