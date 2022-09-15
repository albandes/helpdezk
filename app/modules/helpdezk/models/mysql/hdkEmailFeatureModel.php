<?php

namespace App\modules\helpdezk\models\mysql;

final class hdkEmailFeatureModel
{    
    /**
     * @var int
     */
    private $idEmailTemplate;

    /**
     * @var string
     */
    private $sessionName;

    /**
     * @var int
     */
    private $idLocale;

     /**
     * @var string
     */
    private $localeName;
    
    /**
     * @var string
     */
    private $subject;    

    /**
     * @var string
     */
    private $body;

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
     * Get the value of idEmailTemplate
     *
     * @return  int
     */ 
    public function getIdEmailTemplate()
    {
        return $this->idEmailTemplate;
    }

    /**
     * Set the value of idEmailTemplate
     *
     * @param  int  $idEmailTemplate
     *
     * @return  self
     */ 
    public function setIdEmailTemplate(int $idEmailTemplate)
    {
        $this->idEmailTemplate = $idEmailTemplate;

        return $this;
    }

    /**
     * Get the value of sessionName
     *
     * @return  string
     */ 
    public function getSessionName()
    {
        return $this->sessionName;
    }

    /**
     * Set the value of sessionName
     *
     * @param  string  $sessionName
     *
     * @return  self
     */ 
    public function setSessionName(string $sessionName)
    {
        $this->sessionName = $sessionName;

        return $this;
    }

    /**
     * Get the value of idLocale
     *
     * @return  int
     */ 
    public function getIdLocale()
    {
        return $this->idLocale;
    }

    /**
     * Set the value of idLocale
     *
     * @param  int  $idLocale
     *
     * @return  self
     */ 
    public function setIdLocale(int $idLocale)
    {
        $this->idLocale = $idLocale;

        return $this;
    }

    /**
     * Get the value of localeName
     *
     * @return  string
     */ 
    public function getLocaleName()
    {
        return $this->localeName;
    }

    /**
     * Set the value of localeName
     *
     * @param  string  $localeName
     *
     * @return  self
     */ 
    public function setLocaleName(string $localeName)
    {
        $this->localeName = $localeName;

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
     * Get the value of body
     *
     * @return  string
     */ 
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the value of body
     *
     * @param  string  $body
     *
     * @return  self
     */ 
    public function setBody(string $body)
    {
        $this->body = $body;

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