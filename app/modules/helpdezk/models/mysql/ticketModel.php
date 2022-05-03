<?php
 
namespace App\modules\helpdezk\models\mysql;

final class ticketModel
{
    /**
     * @var string
     */
    private $ticketCode;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $solution;

    /**
     * @var int
     */
    private $idInCharge;

    /**
     * @var string
     */
    private $inCharge;

     /**
     * @var string
     */
    private $inChargeType;

    /**
     * @var int
     */
    private $indInCharge;

    /**
     * @var string
     */
    private $expireDate;

    /**
     * @var string
     */
    private $expireDateFmt;

    /**
     * @var string
     */
    private $entryDate;

    /**
     * @var string
     */
    private $entryDateFmt;

    /**
     * @var int
     */
    private $idStatus;

    /**
     * @var string
     */
    private $status;

    /**
     * @var int
     */
    private $idOwner;

    /**
     * @var string
     */
    private $owner;

    /**
     * @var string
     */
    private $ownerEmail;

    /**
     * @var string
     */
    private $ownerPhone;

    /**
     * @var string
     */
    private $ownerBranch;

    /**
     * @var int
     */
    private $idDepartment;

    /**
     * @var string
     */
    private $department;

    /**
     * @var int
     */
    private $idCreator;

    /**
     * @var string
     */
    private $creator;

    /**
     * @var string
     */
    private $creatorPhone;

    /**
     * @var string
     */
    private $creatorMobile;

    /**
     * @var string
     */
    private $creatorBranch;

    /**
     * @var int
     */
    private $idCompany;

    /**
     * @var string
     */
    private $company;

    /**
     * @var int
     */
    private $idSource;

    /**
     * @var string
     */
    private $source;

     /**
     * @var int
     */
    private $extensionsNumber;

    /**
     * @var int
     */
    private $idAttendanceWay;

    /**
     * @var string
     */
    private $attendanceWay;

    /**
     * @var string
     */
    private $osNumber;

    /**
     * @var string
     */
    private $serialNumber;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $idArea;

    /**
     * @var string
     */
    private $area;

    /**
     * @var int
     */
    private $idType;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $idItem;

    /**
     * @var string
     */
    private $item;

    /**
     * @var int
     */
    private $idService;

    /**
     * @var string
     */
    private $service;

     /**
     * @var int
     */
    private $idPriority;

    /**
     * @var string
     */
    private $priority;

    /**
     * @var string
     */
    private $color;

    /**
     * @var int
     */
    private $idReason;

    /**
     * @var string
     */
    private $reason;

    /**
     * @var string
     */
    private $linkToken;

    /**
     * @var array
     */
    private $gridList;

    /**
     * @var int
     */
    private $totalRows;
    
    /**
     * @var string
     */
    private $recipientEmail;

    /**
     * Get the value of ticketCode
     *
     * @return  string
     */ 
    public function getTicketCode()
    {
        return $this->ticketCode;
    }

    /**
     * Set the value of ticketCode
     *
     * @param  string  $ticketCode
     *
     * @return  self
     */ 
    public function setTicketCode(string $ticketCode)
    {
        $this->ticketCode = $ticketCode;

        return $this;
    }

    /**
     * Get the value of subject
     *
     * @return  string
     */ 
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the value of subject
     *
     * @param  string  $subject
     *
     * @return  self
     */ 
    public function setSubject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return  string
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string  $description
     *
     * @return  self
     */ 
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of idInCharge
     *
     * @return  int
     */ 
    public function getIdInCharge()
    {
        return $this->idInCharge;
    }

    /**
     * Set the value of idInCharge
     *
     * @param  int  $idInCharge
     *
     * @return  self
     */ 
    public function setIdInCharge(int $idInCharge)
    {
        $this->idInCharge = $idInCharge;

        return $this;
    }

    /**
     * Get the value of inCharge
     *
     * @return  string
     */ 
    public function getInCharge()
    {
        return $this->inCharge;
    }

    /**
     * Set the value of inCharge
     *
     * @param  string  $inCharge
     *
     * @return  self
     */ 
    public function setInCharge(string $inCharge)
    {
        $this->inCharge = $inCharge;

        return $this;
    }

    /**
     * Get the value of inChargeType
     *
     * @return  string
     */ 
    public function getInChargeType()
    {
        return $this->inChargeType;
    }

    /**
     * Set the value of inChargeType
     *
     * @param  string  $inChargeType
     *
     * @return  self
     */ 
    public function setInChargeType(string $inChargeType)
    {
        $this->inChargeType = $inChargeType;

        return $this;
    }

    /**
     * Get the value of indInCharge
     *
     * @return  int
     */ 
    public function getIndInCharge()
    {
        return $this->indInCharge;
    }

    /**
     * Set the value of indInCharge
     *
     * @param  int  $indInCharge
     *
     * @return  self
     */ 
    public function setIndInCharge(int $indInCharge)
    {
        $this->indInCharge = $indInCharge;

        return $this;
    }

    /**
     * Get the value of expireDate
     *
     * @return  string
     */ 
    public function getExpireDate()
    {
        return $this->expireDate;
    }

    /**
     * Set the value of expireDate
     *
     * @param  string  $expireDate
     *
     * @return  self
     */ 
    public function setExpireDate(string $expireDate)
    {
        $this->expireDate = $expireDate;

        return $this;
    }

    /**
     * Get the value of expireDateFmt
     *
     * @return  string
     */ 
    public function getExpireDateFmt()
    {
        return $this->expireDateFmt;
    }

    /**
     * Set the value of expireDateFmt
     *
     * @param  string  $expireDateFmt
     *
     * @return  self
     */ 
    public function setExpireDateFmt(string $expireDateFmt)
    {
        $this->expireDateFmt = $expireDateFmt;

        return $this;
    }

    /**
     * Get the value of entryDate
     *
     * @return  string
     */ 
    public function getEntryDate()
    {
        return $this->entryDate;
    }

    /**
     * Set the value of entryDate
     *
     * @param  string  $entryDate
     *
     * @return  self
     */ 
    public function setEntryDate(string $entryDate)
    {
        $this->entryDate = $entryDate;

        return $this;
    }

    /**
     * Get the value of entryDateFmt
     *
     * @return  string
     */ 
    public function getEntryDateFmt()
    {
        return $this->entryDateFmt;
    }

    /**
     * Set the value of entryDateFmt
     *
     * @param  string  $entryDateFmt
     *
     * @return  self
     */ 
    public function setEntryDateFmt(string $entryDateFmt)
    {
        $this->entryDateFmt = $entryDateFmt;

        return $this;
    }

    /**
     * Get the value of idStatus
     *
     * @return  int
     */ 
    public function getIdStatus()
    {
        return $this->idStatus;
    }

    /**
     * Set the value of idStatus
     *
     * @param  int  $idStatus
     *
     * @return  self
     */ 
    public function setIdStatus(int $idStatus)
    {
        $this->idStatus = $idStatus;

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
     * Get the value of idOwner
     *
     * @return  int
     */ 
    public function getIdOwner()
    {
        return $this->idOwner;
    }

    /**
     * Set the value of idOwner
     *
     * @param  int  $idOwner
     *
     * @return  self
     */ 
    public function setIdOwner(int $idOwner)
    {
        $this->idOwner = $idOwner;

        return $this;
    }

    /**
     * Get the value of owner
     *
     * @return  string
     */ 
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set the value of owner
     *
     * @param  string  $owner
     *
     * @return  self
     */ 
    public function setOwner(string $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get the value of ownerEmail
     *
     * @return  string
     */ 
    public function getOwnerEmail()
    {
        return $this->ownerEmail;
    }

    /**
     * Set the value of ownerEmail
     *
     * @param  string  $ownerEmail
     *
     * @return  self
     */ 
    public function setOwnerEmail(string $ownerEmail)
    {
        $this->ownerEmail = $ownerEmail;

        return $this;
    }

    /**
     * Get the value of ownerPhone
     *
     * @return  string
     */ 
    public function getOwnerPhone()
    {
        return $this->ownerPhone;
    }

    /**
     * Set the value of ownerPhone
     *
     * @param  string  $ownerPhone
     *
     * @return  self
     */ 
    public function setOwnerPhone(string $ownerPhone)
    {
        $this->ownerPhone = $ownerPhone;

        return $this;
    }

    /**
     * Get the value of ownerBranch
     *
     * @return  string
     */ 
    public function getOwnerBranch()
    {
        return $this->ownerBranch;
    }

    /**
     * Set the value of ownerBranch
     *
     * @param  string  $ownerBranch
     *
     * @return  self
     */ 
    public function setOwnerBranch(string $ownerBranch)
    {
        $this->ownerBranch = $ownerBranch;

        return $this;
    }

    /**
     * Get the value of idDepartment
     *
     * @return  int
     */ 
    public function getIdDepartment()
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
    public function setIdDepartment(int $idDepartment)
    {
        $this->idDepartment = $idDepartment;

        return $this;
    }

    /**
     * Get the value of department
     *
     * @return  string
     */ 
    public function getDepartment()
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
    public function setDepartment(string $department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get the value of idCreator
     *
     * @return  int
     */ 
    public function getIdCreator()
    {
        return $this->idCreator;
    }

    /**
     * Set the value of idCreator
     *
     * @param  int  $idCreator
     *
     * @return  self
     */ 
    public function setIdCreator(int $idCreator)
    {
        $this->idCreator = $idCreator;

        return $this;
    }

    /**
     * Get the value of creator
     *
     * @return  string
     */ 
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set the value of creator
     *
     * @param  string  $creator
     *
     * @return  self
     */ 
    public function setCreator(string $creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get the value of creatorPhone
     *
     * @return  string
     */ 
    public function getCreatorPhone()
    {
        return $this->creatorPhone;
    }

    /**
     * Set the value of creatorPhone
     *
     * @param  string  $creatorPhone
     *
     * @return  self
     */ 
    public function setCreatorPhone(string $creatorPhone)
    {
        $this->creatorPhone = $creatorPhone;

        return $this;
    }

    /**
     * Get the value of creatorMobile
     *
     * @return  string
     */ 
    public function getCreatorMobile()
    {
        return $this->creatorMobile;
    }

    /**
     * Set the value of creatorMobile
     *
     * @param  string  $creatorMobile
     *
     * @return  self
     */ 
    public function setCreatorMobile(string $creatorMobile)
    {
        $this->creatorMobile = $creatorMobile;

        return $this;
    }

    /**
     * Get the value of creatorBranch
     *
     * @return  string
     */ 
    public function getCreatorBranch()
    {
        return $this->creatorBranch;
    }

    /**
     * Set the value of creatorBranch
     *
     * @param  string  $creatorBranch
     *
     * @return  self
     */ 
    public function setCreatorBranch(string $creatorBranch)
    {
        $this->creatorBranch = $creatorBranch;

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
     * Get the value of company
     *
     * @return  string
     */ 
    public function getCompany()
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
    public function setCompany(string $company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get the value of idSource
     *
     * @return  int
     */ 
    public function getIdSource()
    {
        return $this->idSource;
    }

    /**
     * Set the value of idSource
     *
     * @param  int  $idSource
     *
     * @return  self
     */ 
    public function setIdSource(int $idSource)
    {
        $this->idSource = $idSource;

        return $this;
    }

    /**
     * Get the value of source
     *
     * @return  string
     */ 
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the value of source
     *
     * @param  string  $source
     *
     * @return  self
     */ 
    public function setSource(string $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get the value of extensionsNumber
     *
     * @return  int
     */ 
    public function getExtensionsNumber()
    {
        return $this->extensionsNumber;
    }

    /**
     * Set the value of extensionsNumber
     *
     * @param  int  $extensionsNumber
     *
     * @return  self
     */ 
    public function setExtensionsNumber(int $extensionsNumber)
    {
        $this->extensionsNumber = $extensionsNumber;

        return $this;
    }

    /**
     * Get the value of idAttendanceWay
     *
     * @return  int
     */ 
    public function getIdAttendanceWay()
    {
        return $this->idAttendanceWay;
    }

    /**
     * Set the value of idAttendanceWay
     *
     * @param  int  $idAttendanceWay
     *
     * @return  self
     */ 
    public function setIdAttendanceWay(int $idAttendanceWay)
    {
        $this->idAttendanceWay = $idAttendanceWay;

        return $this;
    }

    /**
     * Get the value of attendanceWay
     *
     * @return  string
     */ 
    public function getAttendanceWay()
    {
        return $this->attendanceWay;
    }

    /**
     * Set the value of attendanceWay
     *
     * @param  string  $attendanceWay
     *
     * @return  self
     */ 
    public function setAttendanceWay(string $attendanceWay)
    {
        $this->attendanceWay = $attendanceWay;

        return $this;
    }

    /**
     * Get the value of osNumber
     *
     * @return  string
     */ 
    public function getOsNumber()
    {
        return $this->osNumber;
    }

    /**
     * Set the value of osNumber
     *
     * @param  string  $osNumber
     *
     * @return  self
     */ 
    public function setOsNumber(string $osNumber)
    {
        $this->osNumber = $osNumber;

        return $this;
    }

    /**
     * Get the value of serialNumber
     *
     * @return  string
     */ 
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * Set the value of serialNumber
     *
     * @param  string  $serialNumber
     *
     * @return  self
     */ 
    public function setSerialNumber(string $serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * Get the value of label
     *
     * @return  string
     */ 
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @param  string  $label
     *
     * @return  self
     */ 
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

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
     * Get the value of area
     *
     * @return  string
     */ 
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set the value of area
     *
     * @param  string  $area
     *
     * @return  self
     */ 
    public function setArea(string $area)
    {
        $this->area = $area;

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
     * Get the value of type
     *
     * @return  string
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param  string  $type
     *
     * @return  self
     */ 
    public function setType(string $type)
    {
        $this->type = $type;

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
     * Get the value of item
     *
     * @return  string
     */ 
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set the value of item
     *
     * @param  string  $item
     *
     * @return  self
     */ 
    public function setItem(string $item)
    {
        $this->item = $item;

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
     * Get the value of service
     *
     * @return  string
     */ 
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set the value of service
     *
     * @param  string  $service
     *
     * @return  self
     */ 
    public function setService(string $service)
    {
        $this->service = $service;

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
     * Get the value of priority
     *
     * @return  string
     */ 
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set the value of priority
     *
     * @param  string  $priority
     *
     * @return  self
     */ 
    public function setPriority(string $priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get the value of color
     *
     * @return  string
     */ 
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set the value of color
     *
     * @param  string  $color
     *
     * @return  self
     */ 
    public function setColor(string $color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get the value of idReason
     *
     * @return  int
     */ 
    public function getIdReason()
    {
        return $this->idReason;
    }

    /**
     * Set the value of idReason
     *
     * @param  int  $idReason
     *
     * @return  self
     */ 
    public function setIdReason(int $idReason)
    {
        $this->idReason = $idReason;

        return $this;
    }

    /**
     * Get the value of reason
     *
     * @return  string
     */ 
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set the value of reason
     *
     * @param  string  $reason
     *
     * @return  self
     */ 
    public function setReason(string $reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get the value of linkToken
     *
     * @return  string
     */ 
    public function getLinkToken()
    {
        return $this->linkToken;
    }

    /**
     * Set the value of linkToken
     *
     * @param  string  $linkToken
     *
     * @return  self
     */ 
    public function setLinkToken(string $linkToken)
    {
        $this->linkToken = $linkToken;

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
     * Get the value of recipientEmail
     *
     * @return  string
     */ 
    public function getRecipientEmail()
    {
        return $this->recipientEmail;
    }

    /**
     * Set the value of recipientEmail
     *
     * @param  string  $recipientEmail
     *
     * @return  self
     */ 
    public function setRecipientEmail(string $recipientEmail)
    {
        $this->recipientEmail = $recipientEmail;

        return $this;
    }
}