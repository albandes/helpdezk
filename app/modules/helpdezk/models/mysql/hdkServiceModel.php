<?php
 
namespace App\modules\helpdezk\models\mysql;

final class hdkServiceModel
{
    /**
     * @var int
     */
    private $idArea;

    /**
     * @var string
     */
    private $areaName;

    /**
     * @var int
     */
    private $idType;

    /**
     * @var string
     */
    private $typeName;

    /**
     * @var int
     */
    private $idItem;

    /**
     * @var string
     */
    private $itemName;

    /**
     * @var int
     */
    private $idService;

    /**
     * @var string
     */
    private $serviceName;

    /**
     * @var array
     */
    private $areaList;

    /**
     * @var array
     */
    private $typeList;

    /**
     * @var array
     */
    private $itemList;

    /**
     * @var array
     */
    private $serviceList;

    /**
     * @var string
     */
    private $status;

    /**
     * @var int
     */
    private $flagDefault;

     /**
     * @var int
     */
    private $flagClassify;

    /**
     * @var int
     */
    private $idPriority;

    /**
     * @var int
     */
    private $idGroup;

    /**
     * @var int
     */
    private $limitDays;

    /**
     * @var int
     */
    private $limitTime;

    /**
     * @var int
     */
    private $attendanceTime;

    /**
     * @var string
     */
    private $timeType;

    /**
     * @var array
     */
    private $ticketList;

    /**
     * @var string
     */
    private $targetField;

    /**
     * @var string
     */
    private $targetCondition;

    /**
     * @var string
     */
    private $targetIdList;

    /**
     * Get the value of idArea
     *
     * @return  int
     */ 
    public function getIdArea()
    {
        return $this->idArea;
    }

    /**
     * Set the value of idArea
     *
     * @param  int  $idArea
     *
     * @return  self
     */ 
    public function setIdArea(int $idArea)
    {
        $this->idArea = $idArea;

        return $this;
    }

    /**
     * Get the value of areaName
     *
     * @return  string
     */ 
    public function getAreaName()
    {
        return $this->areaName;
    }

    /**
     * Set the value of areaName
     *
     * @param  string  $areaName
     *
     * @return  self
     */ 
    public function setAreaName(string $areaName)
    {
        $this->areaName = $areaName;

        return $this;
    }

    /**
     * Get the value of idType
     *
     * @return  int
     */ 
    public function getIdType()
    {
        return $this->idType;
    }

    /**
     * Set the value of idType
     *
     * @param  int  $idType
     *
     * @return  self
     */ 
    public function setIdType(int $idType)
    {
        $this->idType = $idType;

        return $this;
    }

    /**
     * Get the value of typeName
     *
     * @return  string
     */ 
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * Set the value of typeName
     *
     * @param  string  $typeName
     *
     * @return  self
     */ 
    public function setTypeName(string $typeName)
    {
        $this->typeName = $typeName;

        return $this;
    }

    /**
     * Get the value of idItem
     *
     * @return  int
     */ 
    public function getIdItem()
    {
        return $this->idItem;
    }

    /**
     * Set the value of idItem
     *
     * @param  int  $idItem
     *
     * @return  self
     */ 
    public function setIdItem(int $idItem)
    {
        $this->idItem = $idItem;

        return $this;
    }

    /**
     * Get the value of itemName
     *
     * @return  string
     */ 
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * Set the value of itemName
     *
     * @param  string  $itemName
     *
     * @return  self
     */ 
    public function setItemName(string $itemName)
    {
        $this->itemName = $itemName;

        return $this;
    }

    /**
     * Get the value of idService
     *
     * @return  int
     */ 
    public function getIdService()
    {
        return $this->idService;
    }

    /**
     * Set the value of idService
     *
     * @param  int  $idService
     *
     * @return  self
     */ 
    public function setIdService(int $idService)
    {
        $this->idService = $idService;

        return $this;
    }

    /**
     * Get the value of serviceName
     *
     * @return  string
     */ 
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Set the value of serviceName
     *
     * @param  string  $serviceName
     *
     * @return  self
     */ 
    public function setServiceName(string $serviceName)
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    /**
     * Get the value of areaList
     *
     * @return  array
     */ 
    public function getAreaList()
    {
        return $this->areaList;
    }

    /**
     * Set the value of areaList
     *
     * @param  array  $areaList
     *
     * @return  self
     */ 
    public function setAreaList(array $areaList)
    {
        $this->areaList = $areaList;

        return $this;
    }

    /**
     * Get the value of typeList
     *
     * @return  array
     */ 
    public function getTypeList()
    {
        return $this->typeList;
    }

    /**
     * Set the value of typeList
     *
     * @param  array  $typeList
     *
     * @return  self
     */ 
    public function setTypeList(array $typeList)
    {
        $this->typeList = $typeList;

        return $this;
    }

    /**
     * Get the value of itemList
     *
     * @return  array
     */ 
    public function getItemList()
    {
        return $this->itemList;
    }

    /**
     * Set the value of itemList
     *
     * @param  array  $itemList
     *
     * @return  self
     */ 
    public function setItemList(array $itemList)
    {
        $this->itemList = $itemList;

        return $this;
    }

    /**
     * Get the value of serviceList
     *
     * @return  array
     */ 
    public function getServiceList()
    {
        return $this->serviceList;
    }

    /**
     * Set the value of serviceList
     *
     * @param  array  $serviceList
     *
     * @return  self
     */ 
    public function setServiceList(array $serviceList)
    {
        $this->serviceList = $serviceList;

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
     * Get the value of flagDefault
     *
     * @return  int
     */ 
    public function getFlagDefault()
    {
        return $this->flagDefault;
    }

    /**
     * Set the value of flagDefault
     *
     * @param  int  $flagDefault
     *
     * @return  self
     */ 
    public function setFlagDefault(int $flagDefault)
    {
        $this->flagDefault = $flagDefault;

        return $this;
    }

    /**
     * Get the value of flagClassify
     *
     * @return  int
     */ 
    public function getFlagClassify()
    {
        return $this->flagClassify;
    }

    /**
     * Set the value of flagClassify
     *
     * @param  int  $flagClassify
     *
     * @return  self
     */ 
    public function setFlagClassify(int $flagClassify)
    {
        $this->flagClassify = $flagClassify;

        return $this;
    }

    /**
     * Get the value of idPriority
     *
     * @return  int
     */ 
    public function getIdPriority()
    {
        return $this->idPriority;
    }

    /**
     * Set the value of idPriority
     *
     * @param  int  $idPriority
     *
     * @return  self
     */ 
    public function setIdPriority(int $idPriority)
    {
        $this->idPriority = $idPriority;

        return $this;
    }

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
     * Get the value of limitDays
     *
     * @return  int
     */ 
    public function getLimitDays()
    {
        return $this->limitDays;
    }

    /**
     * Set the value of limitDays
     *
     * @param  int  $limitDays
     *
     * @return  self
     */ 
    public function setLimitDays(int $limitDays)
    {
        $this->limitDays = $limitDays;

        return $this;
    }

    /**
     * Get the value of limitTime
     *
     * @return  int
     */ 
    public function getLimitTime()
    {
        return $this->limitTime;
    }

    /**
     * Set the value of limitTime
     *
     * @param  int  $limitTime
     *
     * @return  self
     */ 
    public function setLimitTime(int $limitTime)
    {
        $this->limitTime = $limitTime;

        return $this;
    }

    /**
     * Get the value of attendanceTime
     *
     * @return  int
     */ 
    public function getAttendanceTime()
    {
        return $this->attendanceTime;
    }

    /**
     * Set the value of attendanceTime
     *
     * @param  int  $attendanceTime
     *
     * @return  self
     */ 
    public function setAttendanceTime(int $attendanceTime)
    {
        $this->attendanceTime = $attendanceTime;

        return $this;
    }

    /**
     * Get the value of timeType
     *
     * @return  string
     */ 
    public function getTimeType()
    {
        return $this->timeType;
    }

    /**
     * Set the value of timeType
     *
     * @param  string  $timeType
     *
     * @return  self
     */ 
    public function setTimeType(string $timeType)
    {
        $this->timeType = $timeType;

        return $this;
    }

    /**
     * Get the value of ticketList
     *
     * @return  array
     */ 
    public function getTicketList()
    {
        return $this->ticketList;
    }

    /**
     * Set the value of ticketList
     *
     * @param  array  $ticketList
     *
     * @return  self
     */ 
    public function setTicketList(array $ticketList)
    {
        $this->ticketList = $ticketList;

        return $this;
    }

    /**
     * Get the value of targetField
     *
     * @return  string
     */ 
    public function getTargetField()
    {
        return $this->targetField;
    }

    /**
     * Set the value of targetField
     *
     * @param  string  $targetField
     *
     * @return  self
     */ 
    public function setTargetField(string $targetField)
    {
        $this->targetField = $targetField;

        return $this;
    }

    /**
     * Get the value of targetCondition
     *
     * @return  string
     */ 
    public function getTargetCondition()
    {
        return $this->targetCondition;
    }

    /**
     * Set the value of targetCondition
     *
     * @param  string  $targetCondition
     *
     * @return  self
     */ 
    public function setTargetCondition(string $targetCondition)
    {
        $this->targetCondition = $targetCondition;

        return $this;
    }

    /**
     * Get the value of targetIdList
     *
     * @return  string
     */ 
    public function getTargetIdList()
    {
        return $this->targetIdList;
    }

    /**
     * Set the value of targetIdList
     *
     * @param  string  $targetIdList
     *
     * @return  self
     */ 
    public function setTargetIdList(string $targetIdList)
    {
        $this->targetIdList = $targetIdList;

        return $this;
    }
}