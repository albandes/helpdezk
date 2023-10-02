<?php

namespace App\modules\helpdezk\models\mysql;

final class hdkRequestEmailModel
{    
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
    private $requestEmailId;

    /**
     * @var string
     */
    private $serverUrl;

    /**
     * @var string
     */
    private $serverType;

    /**
     * @var string
     */
    private $serverPort;

    /**
     * @var string
     */
    private $emailAccount;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $addUserFlag;

    /**
     * @var int
     */
    private $delFromServerFlag;

    /**
     * @var int
     */
    private $serviceId;

    /**
     * @var string
     */
    private $filterFrom;

    /**
     * @var string
     */
    private $filterSubject;

    /**
     * @var int
     */
    private $departmentId;

    /**
     * @var string
     */
    private $loginLayout;

    /**
     * @var string
     */
    private $responseNoteFlag;

    /**
     * @var int
     */
    private $companyId;

    /**
     * @var int
     */
    private $areaId;

    /**
     * @var int
     */
    private $typeId;

    /**
     * @var int
     */
    private $itemId;

    /**
     * @var string
     */
    private $emailCode;

    /**
     * @var int
     */
    private $ticketId;

    /**
     * @var string
     */
    private $ticketCode;

    /**
     * @var string
     */
    private $ticketCodeTmp;

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
     * Get the value of requestEmailId
     *
     * @return  int
     */ 
    public function getRequestEmailId()
    {
        return $this->requestEmailId;
    }

    /**
     * Set the value of requestEmailId
     *
     * @param  int  $requestEmailId
     *
     * @return  self
     */ 
    public function setRequestEmailId(int $requestEmailId)
    {
        $this->requestEmailId = $requestEmailId;

        return $this;
    }

    /**
     * Get the value of serverUrl
     *
     * @return  string
     */ 
    public function getServerUrl()
    {
        return $this->serverUrl;
    }

    /**
     * Set the value of serverUrl
     *
     * @param  string  $serverUrl
     *
     * @return  self
     */ 
    public function setServerUrl(string $serverUrl)
    {
        $this->serverUrl = $serverUrl;

        return $this;
    }

    /**
     * Get the value of serverType
     *
     * @return  string
     */ 
    public function getServerType()
    {
        return $this->serverType;
    }

    /**
     * Set the value of serverType
     *
     * @param  string  $serverType
     *
     * @return  self
     */ 
    public function setServerType(string $serverType)
    {
        $this->serverType = $serverType;

        return $this;
    }

    /**
     * Get the value of serverPort
     *
     * @return  string
     */ 
    public function getServerPort()
    {
        return $this->serverPort;
    }

    /**
     * Set the value of serverPort
     *
     * @param  string  $serverPort
     *
     * @return  self
     */ 
    public function setServerPort(string $serverPort)
    {
        $this->serverPort = $serverPort;

        return $this;
    }

    /**
     * Get the value of emailAccount
     *
     * @return  string
     */ 
    public function getEmailAccount()
    {
        return $this->emailAccount;
    }

    /**
     * Set the value of emailAccount
     *
     * @param  string  $emailAccount
     *
     * @return  self
     */ 
    public function setEmailAccount(string $emailAccount)
    {
        $this->emailAccount = $emailAccount;

        return $this;
    }

    /**
     * Get the value of user
     *
     * @return  string
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @param  string  $user
     *
     * @return  self
     */ 
    public function setUser(string $user)
    {
        $this->user = $user;

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
     * Get the value of addUserFlag
     *
     * @return  int
     */ 
    public function getAddUserFlag()
    {
        return $this->addUserFlag;
    }

    /**
     * Set the value of addUserFlag
     *
     * @param  int  $addUserFlag
     *
     * @return  self
     */ 
    public function setAddUserFlag(int $addUserFlag)
    {
        $this->addUserFlag = $addUserFlag;

        return $this;
    }

    /**
     * Get the value of delFromServerFlag
     *
     * @return  int
     */ 
    public function getDelFromServerFlag()
    {
        return $this->delFromServerFlag;
    }

    /**
     * Set the value of delFromServerFlag
     *
     * @param  int  $delFromServerFlag
     *
     * @return  self
     */ 
    public function setDelFromServerFlag(int $delFromServerFlag)
    {
        $this->delFromServerFlag = $delFromServerFlag;

        return $this;
    }

    /**
     * Get the value of serviceId
     *
     * @return  int
     */ 
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Set the value of serviceId
     *
     * @param  int  $serviceId
     *
     * @return  self
     */ 
    public function setServiceId(int $serviceId)
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    /**
     * Get the value of filterFrom
     *
     * @return  string
     */ 
    public function getFilterFrom()
    {
        return $this->filterFrom;
    }

    /**
     * Set the value of filterFrom
     *
     * @param  string  $filterFrom
     *
     * @return  self
     */ 
    public function setFilterFrom(string $filterFrom)
    {
        $this->filterFrom = $filterFrom;

        return $this;
    }

    /**
     * Get the value of filterSubject
     *
     * @return  string
     */ 
    public function getFilterSubject()
    {
        return $this->filterSubject;
    }

    /**
     * Set the value of filterSubject
     *
     * @param  string  $filterSubject
     *
     * @return  self
     */ 
    public function setFilterSubject(string $filterSubject)
    {
        $this->filterSubject = $filterSubject;

        return $this;
    }

    /**
     * Get the value of departmentId
     *
     * @return  int
     */ 
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * Set the value of departmentId
     *
     * @param  int  $departmentId
     *
     * @return  self
     */ 
    public function setDepartmentId(int $departmentId)
    {
        $this->departmentId = $departmentId;

        return $this;
    }

    /**
     * Get the value of loginLayout
     *
     * @return  string
     */ 
    public function getLoginLayout()
    {
        return $this->loginLayout;
    }

    /**
     * Set the value of loginLayout
     *
     * @param  string  $loginLayout
     *
     * @return  self
     */ 
    public function setLoginLayout(string $loginLayout)
    {
        $this->loginLayout = $loginLayout;

        return $this;
    }

    /**
     * Get the value of responseNoteFlag
     *
     * @return  string
     */ 
    public function getResponseNoteFlag()
    {
        return $this->responseNoteFlag;
    }

    /**
     * Set the value of responseNoteFlag
     *
     * @param  string  $responseNoteFlag
     *
     * @return  self
     */ 
    public function setResponseNoteFlag(string $responseNoteFlag)
    {
        $this->responseNoteFlag = $responseNoteFlag;

        return $this;
    }

    /**
     * Get the value of companyId
     *
     * @return  int
     */ 
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set the value of companyId
     *
     * @param  int  $companyId
     *
     * @return  self
     */ 
    public function setCompanyId(int $companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * Get the value of areaId
     *
     * @return  int
     */ 
    public function getAreaId()
    {
        return $this->areaId;
    }

    /**
     * Set the value of areaId
     *
     * @param  int  $areaId
     *
     * @return  self
     */ 
    public function setAreaId(int $areaId)
    {
        $this->areaId = $areaId;

        return $this;
    }

    /**
     * Get the value of typeId
     *
     * @return  int
     */ 
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set the value of typeId
     *
     * @param  int  $typeId
     *
     * @return  self
     */ 
    public function setTypeId(int $typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Get the value of itemId
     *
     * @return  int
     */ 
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set the value of itemId
     *
     * @param  int  $itemId
     *
     * @return  self
     */ 
    public function setItemId(int $itemId)
    {
        $this->itemId = $itemId;

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
     * Get the value of ticketId
     *
     * @return  int
     */ 
    public function getTicketId()
    {
        return $this->ticketId;
    }

    /**
     * Set the value of ticketId
     *
     * @param  int  $ticketId
     *
     * @return  self
     */ 
    public function setTicketId(int $ticketId)
    {
        $this->ticketId = $ticketId;

        return $this;
    }

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
     * Get the value of ticketCodeTmp
     *
     * @return  string
     */ 
    public function getTicketCodeTmp()
    {
        return $this->ticketCodeTmp;
    }

    /**
     * Set the value of ticketCodeTmp
     *
     * @param  string  $ticketCodeTmp
     *
     * @return  self
     */ 
    public function setTicketCodeTmp(string $ticketCodeTmp)
    {
        $this->ticketCodeTmp = $ticketCodeTmp;

        return $this;
    }
}