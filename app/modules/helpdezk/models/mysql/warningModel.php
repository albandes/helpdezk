<?php

namespace App\modules\helpdezk\models\mysql;

final class warningModel
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
    private $topicId;
    
    /**
     * @var string
     */
    private $topicTitle;
    
    /**
     * @var int
     */
    private $userId;
    
    /**
     * @var int
     */
    private $warningId;
    
    /**
     * @var string
     */
    private $warningTitle;
    
    /**
     * @var string
     */
    private $warningDescription;
    
    /**
     * @var string
     */
    private $createdDate;
    
    /**
     * @var string
     */
    private $startDate;
    
    /**
     * @var string
     */
    private $endDate;
    
    /**
     * @var string
     */
    private $flagSendEmail;
    
    /**
     * @var string
     */
    private $showIn;
    
    /**
     * @var int
     */
    private $flagEmailSent;
    
    /**
     * @var string
     */
    private $topicValidity;
    
    /**
     * @var string
     */
    private $topicFlagSendEmail;
    
    /**
     * @var array
     */
    private $topicGroups;
    
    /**
     * @var array
     */
    private $topicCompanies;
    
    /**
     * @var int
     */
    private $groupId;
    
    /**
     * @var int
     */
    private $companyId;

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
     * Get the value of topicId
     *
     * @return  int
     */ 
    public function getTopicId()
    {
        return $this->topicId;
    }

    /**
     * Set the value of topicId
     *
     * @param  int  $topicId
     *
     * @return  self
     */ 
    public function setTopicId(int $topicId)
    {
        $this->topicId = $topicId;

        return $this;
    }

    /**
     * Get the value of topicTitle
     *
     * @return  string
     */ 
    public function getTopicTitle()
    {
        return $this->topicTitle;
    }

    /**
     * Set the value of topicTitle
     *
     * @param  string  $topicTitle
     *
     * @return  self
     */ 
    public function setTopicTitle(string $topicTitle)
    {
        $this->topicTitle = $topicTitle;

        return $this;
    }

    /**
     * Get the value of userId
     *
     * @return  int
     */ 
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @param  int  $userId
     *
     * @return  self
     */ 
    public function setUserId(int $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get the value of warningId
     *
     * @return  int
     */ 
    public function getWarningId()
    {
        return $this->warningId;
    }

    /**
     * Set the value of warningId
     *
     * @param  int  $warningId
     *
     * @return  self
     */ 
    public function setWarningId(int $warningId)
    {
        $this->warningId = $warningId;

        return $this;
    }

    /**
     * Get the value of warningTitle
     *
     * @return  string
     */ 
    public function getWarningTitle()
    {
        return $this->warningTitle;
    }

    /**
     * Set the value of warningTitle
     *
     * @param  string  $warningTitle
     *
     * @return  self
     */ 
    public function setWarningTitle(string $warningTitle)
    {
        $this->warningTitle = $warningTitle;

        return $this;
    }

    /**
     * Get the value of warningDescription
     *
     * @return  string
     */ 
    public function getWarningDescription()
    {
        return $this->warningDescription;
    }

    /**
     * Set the value of warningDescription
     *
     * @param  string  $warningDescription
     *
     * @return  self
     */ 
    public function setWarningDescription(string $warningDescription)
    {
        $this->warningDescription = $warningDescription;

        return $this;
    }

    /**
     * Get the value of createdDate
     *
     * @return  string
     */ 
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set the value of createdDate
     *
     * @param  string  $createdDate
     *
     * @return  self
     */ 
    public function setCreatedDate(string $createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get the value of startDate
     *
     * @return  string
     */ 
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set the value of startDate
     *
     * @param  string  $startDate
     *
     * @return  self
     */ 
    public function setStartDate(string $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the value of endDate
     *
     * @return  string
     */ 
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set the value of endDate
     *
     * @param  string  $endDate
     *
     * @return  self
     */ 
    public function setEndDate(string $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get the value of flagSendEmail
     *
     * @return  string
     */ 
    public function getFlagSendEmail()
    {
        return $this->flagSendEmail;
    }

    /**
     * Set the value of flagSendEmail
     *
     * @param  string  $flagSendEmail
     *
     * @return  self
     */ 
    public function setFlagSendEmail(string $flagSendEmail)
    {
        $this->flagSendEmail = $flagSendEmail;

        return $this;
    }

    /**
     * Get the value of showIn
     *
     * @return  string
     */ 
    public function getShowIn()
    {
        return $this->showIn;
    }

    /**
     * Set the value of showIn
     *
     * @param  string  $showIn
     *
     * @return  self
     */ 
    public function setShowIn(string $showIn)
    {
        $this->showIn = $showIn;

        return $this;
    }

    /**
     * Get the value of flagEmailSent
     *
     * @return  int
     */ 
    public function getFlagEmailSent()
    {
        return $this->flagEmailSent;
    }

    /**
     * Set the value of flagEmailSent
     *
     * @param  int  $flagEmailSent
     *
     * @return  self
     */ 
    public function setFlagEmailSent(int $flagEmailSent)
    {
        $this->flagEmailSent = $flagEmailSent;

        return $this;
    }

    /**
     * Get the value of topicValidity
     *
     * @return  string
     */ 
    public function getTopicValidity()
    {
        return $this->topicValidity;
    }

    /**
     * Set the value of topicValidity
     *
     * @param  string  $topicValidity
     *
     * @return  self
     */ 
    public function setTopicValidity(string $topicValidity)
    {
        $this->topicValidity = $topicValidity;

        return $this;
    }

    /**
     * Get the value of topicFlagSendEmail
     *
     * @return  string
     */ 
    public function getTopicFlagSendEmail()
    {
        return $this->topicFlagSendEmail;
    }

    /**
     * Set the value of topicFlagSendEmail
     *
     * @param  string  $topicFlagSendEmail
     *
     * @return  self
     */ 
    public function setTopicFlagSendEmail(string $topicFlagSendEmail)
    {
        $this->topicFlagSendEmail = $topicFlagSendEmail;

        return $this;
    }

    /**
     * Get the value of topicGroups
     *
     * @return  array
     */ 
    public function getTopicGroups()
    {
        return $this->topicGroups;
    }

    /**
     * Set the value of topicGroups
     *
     * @param  array  $topicGroups
     *
     * @return  self
     */ 
    public function setTopicGroups(array $topicGroups)
    {
        $this->topicGroups = $topicGroups;

        return $this;
    }

    /**
     * Get the value of topicCompanies
     *
     * @return  array
     */ 
    public function getTopicCompanies()
    {
        return $this->topicCompanies;
    }

    /**
     * Set the value of topicCompanies
     *
     * @param  array  $topicCompanies
     *
     * @return  self
     */ 
    public function setTopicCompanies(array $topicCompanies)
    {
        $this->topicCompanies = $topicCompanies;

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
}