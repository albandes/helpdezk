<?php

namespace App\modules\admin\models\mysql;

final class apiTokenModel
{
    /**
     * @var int
     */
    private $idApiToken;

    /**
     * @var string
     */
    private $apiToken;

    /**
     * @var string
     */
    private $app;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $validity;

     /**
     * @var string
     */
    private $cmbValidity;

    /**
     * @var int
     */
    private $numberValidity;

    /**
     * @var array
     */
    private $gridList;

    /**
     * @var int
     */
    private $totalRows; 

    /**
     * Get the value of idApiToken
     *
     * @return  int
     */ 
    public function getIdApiToken()
    {
        return $this->idApiToken;
    }

    /**
     * Set the value of idApiToken
     *
     * @param  int  $idApiToken
     *
     * @return  self
     */ 
    public function setIdApiToken(int $idApiToken)
    {
        $this->idApiToken = $idApiToken;

        return $this;
    }

    /**
     * Get the value of apiToken
     *
     * @return  string
     */ 
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Set the value of apiToken
     *
     * @param  string  $apiToken
     *
     * @return  self
     */ 
    public function setApiToken(string $apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Get the value of app
     *
     * @return  string
     */ 
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Set the value of app
     *
     * @param  string  $app
     *
     * @return  self
     */ 
    public function setApp(string $app)
    {
        $this->app = $app;

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
     * Get the value of email
     *
     * @return  string
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param  string  $email
     *
     * @return  self
     */ 
    public function setEmail(string $email)
    {
        $this->email = $email;

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
     * Get the value of numberValidity
     *
     * @return  int
     */ 
    public function getNumberValidity()
    {
        return $this->numberValidity;
    }

    /**
     * Set the value of numberValidity
     *
     * @param  int  $numberValidity
     *
     * @return  self
     */ 
    public function setNumberValidity(int $numberValidity)
    {
        $this->numberValidity = $numberValidity;

        return $this;
    }

    /**
     * Get the value of validity
     *
     * @return  string
     */ 
    public function getValidity()
    {
        return $this->validity;
    }

    /**
     * Set the value of validity
     *
     * @param  string  $validity
     *
     * @return  self
     */ 
    public function setValidity(string $validity)
    {
        $this->validity = $validity;

        return $this;
    }
    

    /**
     * Get the value of cmbValidity
     *
     * @return  string
     */ 
    public function getCmbValidity()
    {
        return $this->cmbValidity;
    }

    /**
     * Set the value of cmbValidity
     *
     * @param  string  $cmbValidity
     *
     * @return  self
     */ 
    public function setCmbValidity(string $cmbValidity)
    {
        $this->cmbValidity = $cmbValidity;

        return $this;
    }
}