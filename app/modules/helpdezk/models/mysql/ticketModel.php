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
    private $isInCharge;

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
     * @var bool
     */
    private $itemException;

    /**
     * @var string
     */
    private $lastTicketCode;

    /**
     * @var string
     */
    private $tablePrefix;

    /**
     * @var string
     */
    private $isUserVip;

    /**
     * @var string
     */
    private $vipHasPriority;

    /**
     * @var int
     */
    private $idServiceGroup;

    /**
     * @var array
     */
    private $approvalList;

    /**
     * @var array
     */
    private $inChargeList;

    /**
     * @var int
     */
    private $idGroup;

    /**
     * @var int
     */
    private $userType;

    /**
     * @var array
     */
    private $noteList;

    /**
     * @var int
     */
    private $notePublic;

    /**
     * @var int
     */
    private $noteTypeID;

    /**
     * @var string
     */
    private $note;

    /**
     * @var string
     */
    private $noteDateTime;

    /**
     * @var int
     */
    private $noteIsOpen;

    /**
     * @var string
     */
    private $minExpendedTime;

    /**
     * @var string
     */
    private $minTelephoneTime;

    /**
     * @var string
     */
    private $emailCode;

    /**
     * @var string
     */
    private $isRepass;

    /**
     * @var int
     */
    private $isTrack;

    /**
     * @var int
     */
    private $isOperatorAux;

    /**
     * @var mixed
     */
    private $minOpeningTime;

    /**
     * @var mixed
     */
    private $minAttendanceTime;

    /**
     * @var mixed
     */
    private $minClosureTime;

    /**
     * @var mixed
     */
    private $logDate;

    /**
     * @var int
     */
    private $idUserLog;

    /**
     * @var mixed
     */
    private $forwardedDate;

    /**
     * @var mixed
     */
    private $approvalDate;

    /**
     * @var mixed
     */
    private $finishDate;

    /**
     * @var mixed
     */
    private $rejectionDate;

    /**
     * @var mixed
     */
    private $attendantPeriod;

    /**
     * @var mixed
     */
    private $chargingPeriod;

    /**
     * @var mixed
     */
    private $openingDate;

    /**
     * @var string
     */
    private $isReopened;

    /**
     * @var mixed
     */
    private $noteTotalMinutes;

    /**
     * @var mixed
     */
    private $noteStartHour;

    /**
     * @var mixed
     */
    private $noteFinishHour;
    
    /**
     * @var mixed
     */
    private $noteExecutionDate;

    /**
     * @var mixed
     */
    private $noteHourType;

    /**
     * @var mixed
     */
    private $noteServiceVal;

    /**
     * @var mixed
     */
    private $noteIpAddress;

    /**
     * @var mixed
     */
    private $noteIsCallback;

    /**
     * @var array
     */
    private $attachments;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var int
     */
    private $idAttachment;

    /**
     * @var string
     */
    private $newFileName;

    /**
     * @var int
     */
    private $idOperator;

    /**
     * @var string
     */
    private $ticketToken;

    /**
     * @var string
     */
    private $inChargeEmail;

    /**
     * @var int
     */
    private $idStatusSource;

    /**
     * @var string
     */
    private $statusSource;

    /**
     * @var int
     */
    private $idNote;

    /**
     * @var array
     */
    private $noteAttachmentsList;

    /**
     * @var int
     */
    private $idUser;

    /**
     * @var string
     */
    private $idGroupList;

    /**
     * @var array
     */
    private $ticketApproversList;

    /**
     * @var array
     */
    private $attendanceTypeList;

    /**
     * @var string
     */
    private $ticketDateField;

     /**
     * @var bool
     */
    private $inCond;

    /**
     * @var array
     */
    private $auxiliaryAttendantList;

    /**
     * @var array
     */
    private $noteTypeList;

    /**
     * @var array
     */
    private $groupRealIDList;

    /**
     * @var string
     */
    private $newDeadlineDate;

    /**
     * @var string
     */
    private $newDeadlineTime;

    /**
     * @var array
     */
    private $timesList;

    /**
     * @var string
     */
    private $ticketTimeField;

    /**
     * @var string
     */
    private $ticketTimeValue;

    /**
     * @var array
     */
    private $attendantList;

    /**
     * @var array
     */
    private $partnerList;

    /**
     * @var mixed
     */
    private $assumeDate;

    /**
     * @var array
     */
    private $sourceList;

    /**
     * @var int
     */
    private $ownerTypeId;

    /**
     * @var string
     */
    private $trelloCardId;

     /**
     * @var int
     */
    private $trelloUserId;

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
     * Get the value of isInCharge
     *
     * @return  int
     */ 
    public function getIsInCharge()
    {
        return $this->isInCharge;
    }

    /**
     * Set the value of isInCharge
     *
     * @param  int  $isInCharge
     *
     * @return  self
     */ 
    public function setIsInCharge(int $isInCharge)
    {
        $this->isInCharge = $isInCharge;

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

    /**
     * Get the value of itemException
     *
     * @return  bool
     */ 
    public function getItemException()
    {
        return $this->itemException;
    }

    /**
     * Set the value of itemException
     *
     * @param  bool  $itemException
     *
     * @return  self
     */ 
    public function setItemException(bool $itemException)
    {
        $this->itemException = $itemException;

        return $this;
    }

    /**
     * Get the value of lastTicketCode
     *
     * @return  string
     */ 
    public function getLastTicketCode()
    {
        return $this->lastTicketCode;
    }

    /**
     * Set the value of lastTicketCode
     *
     * @param  string  $lastTicketCode
     *
     * @return  self
     */ 
    public function setLastTicketCode(string $lastTicketCode)
    {
        $this->lastTicketCode = $lastTicketCode;

        return $this;
    }

    /**
     * Get the value of tablePrefix
     *
     * @return  string
     */ 
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * Set the value of tablePrefix
     *
     * @param  string  $tablePrefix
     *
     * @return  self
     */ 
    public function setTablePrefix(string $tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;

        return $this;
    }

    /**
     * Get the value of isUserVip
     *
     * @return  string
     */ 
    public function getIsUserVip()
    {
        return $this->isUserVip;
    }

    /**
     * Set the value of isUserVip
     *
     * @param  string  $isUserVip
     *
     * @return  self
     */ 
    public function setIsUserVip(string $isUserVip)
    {
        $this->isUserVip = $isUserVip;

        return $this;
    }

    /**
     * Get the value of vipHasPriority
     *
     * @return  string
     */ 
    public function getVipHasPriority()
    {
        return $this->vipHasPriority;
    }

    /**
     * Set the value of vipHasPriority
     *
     * @param  string  $vipHasPriority
     *
     * @return  self
     */ 
    public function setVipHasPriority(string $vipHasPriority)
    {
        $this->vipHasPriority = $vipHasPriority;

        return $this;
    }

    /**
     * Get the value of idServiceGroup
     *
     * @return  int
     */ 
    public function getIdServiceGroup()
    {
        return $this->idServiceGroup;
    }

    /**
     * Set the value of idServiceGroup
     *
     * @param  int  $idServiceGroup
     *
     * @return  self
     */ 
    public function setIdServiceGroup(int $idServiceGroup)
    {
        $this->idServiceGroup = $idServiceGroup;

        return $this;
    }

    /**
     * Get the value of approvalList
     *
     * @return  array
     */ 
    public function getApprovalList()
    {
        return $this->approvalList;
    }

    /**
     * Set the value of approvalList
     *
     * @param  array  $approvalList
     *
     * @return  self
     */ 
    public function setApprovalList(array $approvalList)
    {
        $this->approvalList = $approvalList;

        return $this;
    }

    /**
     * Get the value of inChargeList
     *
     * @return  array
     */ 
    public function getInChargeList()
    {
        return $this->inChargeList;
    }

    /**
     * Set the value of inChargeList
     *
     * @param  array  $inChargeList
     *
     * @return  self
     */ 
    public function setInChargeList(array $inChargeList)
    {
        $this->inChargeList = $inChargeList;

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
     * Get the value of userType
     *
     * @return  int
     */ 
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * Set the value of userType
     *
     * @param  int  $userType
     *
     * @return  self
     */ 
    public function setUserType(int $userType)
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * Get the value of noteList
     *
     * @return  array
     */ 
    public function getNoteList()
    {
        return $this->noteList;
    }

    /**
     * Set the value of noteList
     *
     * @param  array  $noteList
     *
     * @return  self
     */ 
    public function setNoteList(array $noteList)
    {
        $this->noteList = $noteList;

        return $this;
    }

    /**
     * Get the value of notePublic
     *
     * @return  int
     */ 
    public function getNotePublic()
    {
        return $this->notePublic;
    }

    /**
     * Set the value of notePublic
     *
     * @param  int  $notePublic
     *
     * @return  self
     */ 
    public function setNotePublic(int $notePublic)
    {
        $this->notePublic = $notePublic;

        return $this;
    }

    /**
     * Get the value of noteTypeID
     *
     * @return  int
     */ 
    public function getNoteTypeID()
    {
        return $this->noteTypeID;
    }

    /**
     * Set the value of noteTypeID
     *
     * @param  int  $noteTypeID
     *
     * @return  self
     */ 
    public function setNoteTypeID(int $noteTypeID)
    {
        $this->noteTypeID = $noteTypeID;

        return $this;
    }

    /**
     * Get the value of note
     *
     * @return  string
     */ 
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set the value of note
     *
     * @param  string  $note
     *
     * @return  self
     */ 
    public function setNote(string $note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get the value of noteDateTime
     *
     * @return  string
     */ 
    public function getNoteDateTime()
    {
        return $this->noteDateTime;
    }

    /**
     * Set the value of noteDateTime
     *
     * @param  string  $noteDateTime
     *
     * @return  self
     */ 
    public function setNoteDateTime(string $noteDateTime)
    {
        $this->noteDateTime = $noteDateTime;

        return $this;
    }

    /**
     * Get the value of noteIsOpen
     *
     * @return  int
     */ 
    public function getNoteIsOpen()
    {
        return $this->noteIsOpen;
    }

    /**
     * Set the value of noteIsOpen
     *
     * @param  int  $noteIsOpen
     *
     * @return  self
     */ 
    public function setNoteIsOpen(int $noteIsOpen)
    {
        $this->noteIsOpen = $noteIsOpen;

        return $this;
    }

    /**
     * Get the value of minExpendedTime
     *
     * @return  string
     */ 
    public function getMinExpendedTime()
    {
        return $this->minExpendedTime;
    }

    /**
     * Set the value of minExpendedTime
     *
     * @param  string  $minExpendedTime
     *
     * @return  self
     */ 
    public function setMinExpendedTime(string $minExpendedTime)
    {
        $this->minExpendedTime = $minExpendedTime;

        return $this;
    }

    /**
     * Get the value of minTelephoneTime
     *
     * @return  string
     */ 
    public function getMinTelephoneTime()
    {
        return $this->minTelephoneTime;
    }

    /**
     * Set the value of minTelephoneTime
     *
     * @param  string  $minTelephoneTime
     *
     * @return  self
     */ 
    public function setMinTelephoneTime(string $minTelephoneTime)
    {
        $this->minTelephoneTime = $minTelephoneTime;

        return $this;
    }

    /**
     * Get the value of emailCode
     *
     * @return  string
     */ 
    public function getEmailCode()
    {
        return $this->emailCode;
    }

    /**
     * Set the value of emailCode
     *
     * @param  string  $emailCode
     *
     * @return  self
     */ 
    public function setEmailCode(string $emailCode)
    {
        $this->emailCode = $emailCode;

        return $this;
    }

    /**
     * Get the value of isRepass
     *
     * @return  string
     */ 
    public function getIsRepass()
    {
        return $this->isRepass;
    }

    /**
     * Set the value of isRepass
     *
     * @param  string  $isRepass
     *
     * @return  self
     */ 
    public function setIsRepass(string $isRepass)
    {
        $this->isRepass = $isRepass;

        return $this;
    }

    /**
     * Get the value of isTrack
     *
     * @return  int
     */ 
    public function getIsTrack()
    {
        return $this->isTrack;
    }

    /**
     * Set the value of isTrack
     *
     * @param  int  $isTrack
     *
     * @return  self
     */ 
    public function setIsTrack(int $isTrack)
    {
        $this->isTrack = $isTrack;

        return $this;
    }

    /**
     * Get the value of isOperatorAux
     *
     * @return  int
     */ 
    public function getIsOperatorAux()
    {
        return $this->isOperatorAux;
    }

    /**
     * Set the value of isOperatorAux
     *
     * @param  int  $isOperatorAux
     *
     * @return  self
     */ 
    public function setIsOperatorAux(int $isOperatorAux)
    {
        $this->isOperatorAux = $isOperatorAux;

        return $this;
    }

    /**
     * Get the value of minOpeningTime
     *
     * @return  mixed
     */ 
    public function getMinOpeningTime()
    {
        return $this->minOpeningTime;
    }

    /**
     * Set the value of minOpeningTime
     *
     * @param  mixed  $minOpeningTime
     *
     * @return  self
     */ 
    public function setMinOpeningTime($minOpeningTime)
    {
        $this->minOpeningTime = $minOpeningTime;

        return $this;
    }

    /**
     * Get the value of minAttendanceTime
     *
     * @return  mixed
     */ 
    public function getMinAttendanceTime()
    {
        return $this->minAttendanceTime;
    }

    /**
     * Set the value of minAttendanceTime
     *
     * @param  mixed  $minAttendanceTime
     *
     * @return  self
     */ 
    public function setMinAttendanceTime($minAttendanceTime)
    {
        $this->minAttendanceTime = $minAttendanceTime;

        return $this;
    }

    /**
     * Get the value of minClosureTime
     *
     * @return  mixed
     */ 
    public function getMinClosureTime()
    {
        return $this->minClosureTime;
    }

    /**
     * Set the value of minClosureTime
     *
     * @param  mixed  $minClosureTime
     *
     * @return  self
     */ 
    public function setMinClosureTime($minClosureTime)
    {
        $this->minClosureTime = $minClosureTime;

        return $this;
    }

    /**
     * Get the value of logDate
     *
     * @return  mixed
     */ 
    public function getLogDate()
    {
        return $this->logDate;
    }

    /**
     * Set the value of logDate
     *
     * @param  mixed  $logDate
     *
     * @return  self
     */ 
    public function setLogDate($logDate)
    {
        $this->logDate = $logDate;

        return $this;
    }

    /**
     * Get the value of idUserLog
     *
     * @return  int
     */ 
    public function getIdUserLog()
    {
        return $this->idUserLog;
    }

    /**
     * Set the value of idUserLog
     *
     * @param  int  $idUserLog
     *
     * @return  self
     */ 
    public function setIdUserLog(int $idUserLog)
    {
        $this->idUserLog = $idUserLog;

        return $this;
    }

    /**
     * Get the value of forwardedDate
     *
     * @return  mixed
     */ 
    public function getForwardedDate()
    {
        return $this->forwardedDate;
    }

    /**
     * Set the value of forwardedDate
     *
     * @param  mixed  $forwardedDate
     *
     * @return  self
     */ 
    public function setForwardedDate($forwardedDate)
    {
        $this->forwardedDate = $forwardedDate;

        return $this;
    }

    /**
     * Get the value of approvalDate
     *
     * @return  mixed
     */ 
    public function getApprovalDate()
    {
        return $this->approvalDate;
    }

    /**
     * Set the value of approvalDate
     *
     * @param  mixed  $approvalDate
     *
     * @return  self
     */ 
    public function setApprovalDate($approvalDate)
    {
        $this->approvalDate = $approvalDate;

        return $this;
    }

    /**
     * Get the value of finishDate
     *
     * @return  mixed
     */ 
    public function getFinishDate()
    {
        return $this->finishDate;
    }

    /**
     * Set the value of finishDate
     *
     * @param  mixed  $finishDate
     *
     * @return  self
     */ 
    public function setFinishDate($finishDate)
    {
        $this->finishDate = $finishDate;

        return $this;
    }

    /**
     * Get the value of rejectionDate
     *
     * @return  mixed
     */ 
    public function getRejectionDate()
    {
        return $this->rejectionDate;
    }

    /**
     * Set the value of rejectionDate
     *
     * @param  mixed  $rejectionDate
     *
     * @return  self
     */ 
    public function setRejectionDate($rejectionDate)
    {
        $this->rejectionDate = $rejectionDate;

        return $this;
    }

    /**
     * Get the value of attendantPeriod
     *
     * @return  mixed
     */ 
    public function getAttendantPeriod()
    {
        return $this->attendantPeriod;
    }

    /**
     * Set the value of attendantPeriod
     *
     * @param  mixed  $attendantPeriod
     *
     * @return  self
     */ 
    public function setAttendantPeriod($attendantPeriod)
    {
        $this->attendantPeriod = $attendantPeriod;

        return $this;
    }

    /**
     * Get the value of chargingPeriod
     *
     * @return  mixed
     */ 
    public function getChargingPeriod()
    {
        return $this->chargingPeriod;
    }

    /**
     * Set the value of chargingPeriod
     *
     * @param  mixed  $chargingPeriod
     *
     * @return  self
     */ 
    public function setChargingPeriod($chargingPeriod)
    {
        $this->chargingPeriod = $chargingPeriod;

        return $this;
    }

    /**
     * Get the value of openingDate
     *
     * @return  mixed
     */ 
    public function getOpeningDate()
    {
        return $this->openingDate;
    }

    /**
     * Set the value of openingDate
     *
     * @param  mixed  $openingDate
     *
     * @return  self
     */ 
    public function setOpeningDate($openingDate)
    {
        $this->openingDate = $openingDate;

        return $this;
    }

    /**
     * Get the value of isReopened
     *
     * @return  string
     */ 
    public function getIsReopened()
    {
        return $this->isReopened;
    }

    /**
     * Set the value of isReopened
     *
     * @param  string  $isReopened
     *
     * @return  self
     */ 
    public function setIsReopened(string $isReopened)
    {
        $this->isReopened = $isReopened;

        return $this;
    }

    /**
     * Get the value of noteTotalMinutes
     *
     * @return  mixed
     */ 
    public function getNoteTotalMinutes()
    {
        return $this->noteTotalMinutes;
    }

    /**
     * Set the value of noteTotalMinutes
     *
     * @param  mixed  $noteTotalMinutes
     *
     * @return  self
     */ 
    public function setNoteTotalMinutes($noteTotalMinutes)
    {
        $this->noteTotalMinutes = $noteTotalMinutes;

        return $this;
    }

    /**
     * Get the value of noteStartHour
     *
     * @return  mixed
     */ 
    public function getNoteStartHour()
    {
        return $this->noteStartHour;
    }

    /**
     * Set the value of noteStartHour
     *
     * @param  mixed  $noteStartHour
     *
     * @return  self
     */ 
    public function setNoteStartHour($noteStartHour)
    {
        $this->noteStartHour = $noteStartHour;

        return $this;
    }

    /**
     * Get the value of noteFinishHour
     *
     * @return  mixed
     */ 
    public function getNoteFinishHour()
    {
        return $this->noteFinishHour;
    }

    /**
     * Set the value of noteFinishHour
     *
     * @param  mixed  $noteFinishHour
     *
     * @return  self
     */ 
    public function setNoteFinishHour($noteFinishHour)
    {
        $this->noteFinishHour = $noteFinishHour;

        return $this;
    }

    /**
     * Get the value of noteExecutionDate
     *
     * @return  mixed
     */ 
    public function getNoteExecutionDate()
    {
        return $this->noteExecutionDate;
    }

    /**
     * Set the value of noteExecutionDate
     *
     * @param  mixed  $noteExecutionDate
     *
     * @return  self
     */ 
    public function setNoteExecutionDate($noteExecutionDate)
    {
        $this->noteExecutionDate = $noteExecutionDate;

        return $this;
    }

    /**
     * Get the value of noteHourType
     *
     * @return  mixed
     */ 
    public function getNoteHourType()
    {
        return $this->noteHourType;
    }

    /**
     * Set the value of noteHourType
     *
     * @param  mixed  $noteHourType
     *
     * @return  self
     */ 
    public function setNoteHourType($noteHourType)
    {
        $this->noteHourType = $noteHourType;

        return $this;
    }

    /**
     * Get the value of noteServiceVal
     *
     * @return  mixed
     */ 
    public function getNoteServiceVal()
    {
        return $this->noteServiceVal;
    }

    /**
     * Set the value of noteServiceVal
     *
     * @param  mixed  $noteServiceVal
     *
     * @return  self
     */ 
    public function setNoteServiceVal($noteServiceVal)
    {
        $this->noteServiceVal = $noteServiceVal;

        return $this;
    }

    /**
     * Get the value of noteIpAddress
     *
     * @return  mixed
     */ 
    public function getNoteIpAddress()
    {
        return $this->noteIpAddress;
    }

    /**
     * Set the value of noteIpAddress
     *
     * @param  mixed  $noteIpAddress
     *
     * @return  self
     */ 
    public function setNoteIpAddress($noteIpAddress)
    {
        $this->noteIpAddress = $noteIpAddress;

        return $this;
    }

    /**
     * Get the value of noteIsCallback
     *
     * @return  mixed
     */ 
    public function getNoteIsCallback()
    {
        return $this->noteIsCallback;
    }

    /**
     * Set the value of noteIsCallback
     *
     * @param  mixed  $noteIsCallback
     *
     * @return  self
     */ 
    public function setNoteIsCallback($noteIsCallback)
    {
        $this->noteIsCallback = $noteIsCallback;

        return $this;
    }

    /**
     * Get the value of attachments
     *
     * @return  array
     */ 
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Set the value of attachments
     *
     * @param  array  $attachments
     *
     * @return  self
     */ 
    public function setAttachments(array $attachments)
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * Get the value of fileName
     *
     * @return  string
     */ 
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set the value of fileName
     *
     * @param  string  $fileName
     *
     * @return  self
     */ 
    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get the value of idAttachment
     *
     * @return  int
     */ 
    public function getIdAttachment()
    {
        return $this->idAttachment;
    }

    /**
     * Set the value of idAttachment
     *
     * @param  int  $idAttachment
     *
     * @return  self
     */ 
    public function setIdAttachment(int $idAttachment)
    {
        $this->idAttachment = $idAttachment;

        return $this;
    }

    /**
     * Get the value of newFileName
     *
     * @return  string
     */ 
    public function getNewFileName()
    {
        return $this->newFileName;
    }

    /**
     * Set the value of newFileName
     *
     * @param  string  $newFileName
     *
     * @return  self
     */ 
    public function setNewFileName(string $newFileName)
    {
        $this->newFileName = $newFileName;

        return $this;
    }

    /**
     * Get the value of idOperator
     *
     * @return  int
     */ 
    public function getIdOperator()
    {
        return $this->idOperator;
    }

    /**
     * Set the value of idOperator
     *
     * @param  int  $idOperator
     *
     * @return  self
     */ 
    public function setIdOperator(int $idOperator)
    {
        $this->idOperator = $idOperator;

        return $this;
    }

    /**
     * Get the value of ticketToken
     *
     * @return  string
     */ 
    public function getTicketToken()
    {
        return $this->ticketToken;
    }

    /**
     * Set the value of ticketToken
     *
     * @param  string  $ticketToken
     *
     * @return  self
     */ 
    public function setTicketToken(string $ticketToken)
    {
        $this->ticketToken = $ticketToken;

        return $this;
    }

    /**
     * Get the value of inChargeEmail
     *
     * @return  string
     */ 
    public function getInChargeEmail()
    {
        return $this->inChargeEmail;
    }

    /**
     * Set the value of inChargeEmail
     *
     * @param  string  $inChargeEmail
     *
     * @return  self
     */ 
    public function setInChargeEmail(string $inChargeEmail)
    {
        $this->inChargeEmail = $inChargeEmail;

        return $this;
    }

    /**
     * Get the value of idStatusSource
     *
     * @return  int
     */ 
    public function getIdStatusSource()
    {
        return $this->idStatusSource;
    }

    /**
     * Set the value of idStatusSource
     *
     * @param  int  $idStatusSource
     *
     * @return  self
     */ 
    public function setIdStatusSource(int $idStatusSource)
    {
        $this->idStatusSource = $idStatusSource;

        return $this;
    }

    /**
     * Get the value of statusSource
     *
     * @return  string
     */ 
    public function getStatusSource()
    {
        return $this->statusSource;
    }

    /**
     * Set the value of statusSource
     *
     * @param  string  $statusSource
     *
     * @return  self
     */ 
    public function setStatusSource(string $statusSource)
    {
        $this->statusSource = $statusSource;

        return $this;
    }

    /**
     * Get the value of idNote
     *
     * @return  int
     */ 
    public function getIdNote()
    {
        return $this->idNote;
    }

    /**
     * Set the value of idNote
     *
     * @param  int  $idNote
     *
     * @return  self
     */ 
    public function setIdNote(int $idNote)
    {
        $this->idNote = $idNote;

        return $this;
    }

    /**
     * Get the value of noteAttachmentsList
     *
     * @return  array
     */ 
    public function getNoteAttachmentsList()
    {
        return $this->noteAttachmentsList;
    }

    /**
     * Set the value of noteAttachmentsList
     *
     * @param  array  $noteAttachmentsList
     *
     * @return  self
     */ 
    public function setNoteAttachmentsList(array $noteAttachmentsList)
    {
        $this->noteAttachmentsList = $noteAttachmentsList;

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
     * Get the value of idGroupList
     *
     * @return  string
     */ 
    public function getIdGroupList()
    {
        return $this->idGroupList;
    }

    /**
     * Set the value of idGroupList
     *
     * @param  string  $idGroupList
     *
     * @return  self
     */ 
    public function setIdGroupList(string $idGroupList)
    {
        $this->idGroupList = $idGroupList;

        return $this;
    }

    /**
     * Get the value of ticketApproversList
     *
     * @return  array
     */ 
    public function getTicketApproversList()
    {
        return $this->ticketApproversList;
    }

    /**
     * Set the value of ticketApproversList
     *
     * @param  array  $ticketApproversList
     *
     * @return  self
     */ 
    public function setTicketApproversList(array $ticketApproversList)
    {
        $this->ticketApproversList = $ticketApproversList;

        return $this;
    }

    /**
     * Get the value of attendanceTypeList
     *
     * @return  array
     */ 
    public function getAttendanceTypeList()
    {
        return $this->attendanceTypeList;
    }

    /**
     * Set the value of attendanceTypeList
     *
     * @param  array  $attendanceTypeList
     *
     * @return  self
     */ 
    public function setAttendanceTypeList(array $attendanceTypeList)
    {
        $this->attendanceTypeList = $attendanceTypeList;

        return $this;
    }

    /**
     * Get the value of ticketDateField
     *
     * @return  string
     */ 
    public function getTicketDateField()
    {
        return $this->ticketDateField;
    }

    /**
     * Set the value of ticketDateField
     *
     * @param  string  $ticketDateField
     *
     * @return  self
     */ 
    public function setTicketDateField(string $ticketDateField)
    {
        $this->ticketDateField = $ticketDateField;

        return $this;
    }

    /**
     * Get the value of inCond
     *
     * @return  bool
     */ 
    public function getInCond()
    {
        return $this->inCond;
    }

    /**
     * Set the value of inCond
     *
     * @param  bool  $inCond
     *
     * @return  self
     */ 
    public function setInCond(bool $inCond)
    {
        $this->inCond = $inCond;

        return $this;
    }
    
    /**
     * Get the value of auxiliaryAttendantList
     *
     * @return  array
     */ 
    public function getAuxiliaryAttendantList()
    {
        return $this->auxiliaryAttendantList;
    }

    /**
     * Set the value of auxiliaryAttendantList
     *
     * @param  array  $auxiliaryAttendantList
     *
     * @return  self
     */ 
    public function setAuxiliaryAttendantList(array $auxiliaryAttendantList)
    {
        $this->auxiliaryAttendantList = $auxiliaryAttendantList;

        return $this;
    }

    /**
     * Get the value of noteTypeList
     *
     * @return  array
     */ 
    public function getNoteTypeList()
    {
        return $this->noteTypeList;
    }

    /**
     * Set the value of noteTypeList
     *
     * @param  array  $noteTypeList
     *
     * @return  self
     */ 
    public function setNoteTypeList(array $noteTypeList)
    {
        $this->noteTypeList = $noteTypeList;

        return $this;
    }

    /**
     * Get the value of groupRealIDList
     *
     * @return  array
     */ 
    public function getGroupRealIDList()
    {
        return $this->groupRealIDList;
    }

    /**
     * Set the value of groupRealIDList
     *
     * @param  array  $groupRealIDList
     *
     * @return  self
     */ 
    public function setGroupRealIDList(array $groupRealIDList)
    {
        $this->groupRealIDList = $groupRealIDList;

        return $this;
    }

    /**
     * Get the value of newDeadlineDate
     *
     * @return  string
     */ 
    public function getNewDeadlineDate()
    {
        return $this->newDeadlineDate;
    }

    /**
     * Set the value of newDeadlineDate
     *
     * @param  string  $newDeadlineDate
     *
     * @return  self
     */ 
    public function setNewDeadlineDate(string $newDeadlineDate)
    {
        $this->newDeadlineDate = $newDeadlineDate;

        return $this;
    }

    /**
     * Get the value of newDeadlineTime
     *
     * @return  string
     */ 
    public function getNewDeadlineTime()
    {
        return $this->newDeadlineTime;
    }

    /**
     * Set the value of newDeadlineTime
     *
     * @param  string  $newDeadlineTime
     *
     * @return  self
     */ 
    public function setNewDeadlineTime(string $newDeadlineTime)
    {
        $this->newDeadlineTime = $newDeadlineTime;

        return $this;
    }

    /**
     * Get the value of timesList
     *
     * @return  array
     */ 
    public function getTimesList()
    {
        return $this->timesList;
    }

    /**
     * Set the value of timesList
     *
     * @param  array  $timesList
     *
     * @return  self
     */ 
    public function setTimesList(array $timesList)
    {
        $this->timesList = $timesList;

        return $this;
    }

    /**
     * Get the value of ticketTimeField
     *
     * @return  string
     */ 
    public function getTicketTimeField()
    {
        return $this->ticketTimeField;
    }

    /**
     * Set the value of ticketTimeField
     *
     * @param  string  $ticketTimeField
     *
     * @return  self
     */ 
    public function setTicketTimeField(string $ticketTimeField)
    {
        $this->ticketTimeField = $ticketTimeField;

        return $this;
    }

    /**
     * Get the value of ticketTimeValue
     *
     * @return  string
     */ 
    public function getTicketTimeValue()
    {
        return $this->ticketTimeValue;
    }

    /**
     * Set the value of ticketTimeValue
     *
     * @param  string  $ticketTimeValue
     *
     * @return  self
     */ 
    public function setTicketTimeValue(string $ticketTimeValue)
    {
        $this->ticketTimeValue = $ticketTimeValue;

        return $this;
    }

    /**
     * Get the value of attendantList
     *
     * @return  array
     */ 
    public function getAttendantList()
    {
        return $this->attendantList;
    }

    /**
     * Set the value of attendantList
     *
     * @param  array  $attendantList
     *
     * @return  self
     */ 
    public function setAttendantList(array $attendantList)
    {
        $this->attendantList = $attendantList;

        return $this;
    }

    /**
     * Get the value of partnerList
     *
     * @return  array
     */ 
    public function getPartnerList()
    {
        return $this->partnerList;
    }

    /**
     * Set the value of partnerList
     *
     * @param  array  $partnerList
     *
     * @return  self
     */ 
    public function setPartnerList(array $partnerList)
    {
        $this->partnerList = $partnerList;

        return $this;
    }

    /**
     * Get the value of assumeDate
     *
     * @return  mixed
     */ 
    public function getAssumeDate()
    {
        return $this->assumeDate;
    }

    /**
     * Set the value of assumeDate
     *
     * @param  mixed  $assumeDate
     *
     * @return  self
     */ 
    public function setAssumeDate($assumeDate)
    {
        $this->assumeDate = $assumeDate;

        return $this;
    }

    /**
     * Get the value of sourceList
     *
     * @return  array
     */ 
    public function getSourceList()
    {
        return $this->sourceList;
    }

    /**
     * Set the value of sourceList
     *
     * @param  array  $sourceList
     *
     * @return  self
     */ 
    public function setSourceList(array $sourceList)
    {
        $this->sourceList = $sourceList;

        return $this;
    }

    /**
     * Get the value of ownerTypeId
     *
     * @return  int
     */ 
    public function getOwnerTypeId()
    {
        return $this->ownerTypeId;
    }

    /**
     * Set the value of ownerTypeId
     *
     * @param  int  $ownerTypeId
     *
     * @return  self
     */ 
    public function setOwnerTypeId(int $ownerTypeId)
    {
        $this->ownerTypeId = $ownerTypeId;

        return $this;
    }

    /**
     * Get the value of trelloCardId
     *
     * @return  string
     */ 
    public function getTrelloCardId()
    {
        return $this->trelloCardId;
    }

    /**
     * Set the value of trelloCardId
     *
     * @param  string  $trelloCardId
     *
     * @return  self
     */ 
    public function setTrelloCardId(string $trelloCardId)
    {
        $this->trelloCardId = $trelloCardId;

        return $this;
    }

    /**
     * Get the value of trelloUserId
     *
     * @return  int
     */ 
    public function getTrelloUserId()
    {
        return $this->trelloUserId;
    }

    /**
     * Set the value of trelloUserId
     *
     * @param  int  $trelloUserId
     *
     * @return  self
     */ 
    public function setTrelloUserId(int $trelloUserId)
    {
        $this->trelloUserId = $trelloUserId;

        return $this;
    }
}