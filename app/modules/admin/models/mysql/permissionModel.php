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
     * @var int
     */
    private $personTypeId;

     /**
     * @var array
     */
    private $userTypePermissionList;

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
    private $oldProgramId;

    /**
     * @var string
     */
    private $accessStart;

    /**
     * @var string
     */
    private $accessEnd;

    /**
     * @var int
     */
    private $programAccessId;

    /**
     * @var int
     */
    private $oldProgramAccessId;

    /**
     * @var int
     */
    private $programTypeId;

    /**
     * @var int
     */
    private $programReferenceId;

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

    /**
     * Get the value of personTypeId
     *
     * @return  int
     */ 
    public function getPersonTypeId()
    {
        return $this->personTypeId;
    }

    /**
     * Set the value of personTypeId
     *
     * @param  int  $personTypeId
     *
     * @return  self
     */ 
    public function setPersonTypeId(int $personTypeId)
    {
        $this->personTypeId = $personTypeId;

        return $this;
    }

    /**
     * Get the value of userTypePermissionList
     *
     * @return  array
     */ 
    public function getUserTypePermissionList()
    {
        return $this->userTypePermissionList;
    }

    /**
     * Set the value of userTypePermissionList
     *
     * @param  array  $userTypePermissionList
     *
     * @return  self
     */ 
    public function setUserTypePermissionList(array $userTypePermissionList)
    {
        $this->userTypePermissionList = $userTypePermissionList;

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
     * Get the value of oldProgramId
     *
     * @return  int
     */ 
    public function getOldProgramId()
    {
        return $this->oldProgramId;
    }

    /**
     * Set the value of oldProgramId
     *
     * @param  int  $oldProgramId
     *
     * @return  self
     */ 
    public function setOldProgramId(int $oldProgramId)
    {
        $this->oldProgramId = $oldProgramId;

        return $this;
    }

    /**
     * Get the value of accessStart
     *
     * @return  string
     */ 
    public function getAccessStart()
    {
        return $this->accessStart;
    }

    /**
     * Set the value of accessStart
     *
     * @param  string  $accessStart
     *
     * @return  self
     */ 
    public function setAccessStart(string $accessStart)
    {
        $this->accessStart = $accessStart;

        return $this;
    }

    /**
     * Get the value of accessEnd
     *
     * @return  string
     */ 
    public function getAccessEnd()
    {
        return $this->accessEnd;
    }

    /**
     * Set the value of accessEnd
     *
     * @param  string  $accessEnd
     *
     * @return  self
     */ 
    public function setAccessEnd(string $accessEnd)
    {
        $this->accessEnd = $accessEnd;

        return $this;
    }

    /**
     * Get the value of programAccessId
     *
     * @return  int
     */ 
    public function getProgramAccessId()
    {
        return $this->programAccessId;
    }

    /**
     * Set the value of programAccessId
     *
     * @param  int  $programAccessId
     *
     * @return  self
     */ 
    public function setProgramAccessId(int $programAccessId)
    {
        $this->programAccessId = $programAccessId;

        return $this;
    }

    /**
     * Get the value of oldProgramAccessId
     *
     * @return  int
     */ 
    public function getOldProgramAccessId()
    {
        return $this->oldProgramAccessId;
    }

    /**
     * Set the value of oldProgramAccessId
     *
     * @param  int  $oldProgramAccessId
     *
     * @return  self
     */ 
    public function setOldProgramAccessId(int $oldProgramAccessId)
    {
        $this->oldProgramAccessId = $oldProgramAccessId;

        return $this;
    }

    /**
     * Get the value of programTypeId
     *
     * @return  int
     */ 
    public function getProgramTypeId()
    {
        return $this->programTypeId;
    }

    /**
     * Set the value of programTypeId
     *
     * @param  int  $programTypeId
     *
     * @return  self
     */ 
    public function setProgramTypeId(int $programTypeId)
    {
        $this->programTypeId = $programTypeId;

        return $this;
    }

    /**
     * Get the value of programReferenceId
     *
     * @return  int
     */ 
    public function getProgramReferenceId()
    {
        return $this->programReferenceId;
    }

    /**
     * Set the value of programReferenceId
     *
     * @param  int  $programReferenceId
     *
     * @return  self
     */ 
    public function setProgramReferenceId(int $programReferenceId)
    {
        $this->programReferenceId = $programReferenceId;

        return $this;
    }
}