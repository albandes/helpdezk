<?php

namespace App\modules\main\models\mysql;

final class externalappModel
{    
    /**
     * @var int
     */
    private $idexternalapp;

    /**
     * @var string
     */
    private $appName;

    /**
     * @var string
     */
    private $appUrl;

    /**
     * @var array
     */
    private $gridList;

    /**
     * @var int
     */
    private $userID;

    /**
     * @var array
     */
    private $settingsList;

     /**
     * @var int
     */
    private $idexternalsetting;


    /**
     * Get the value of idexternalapp
     *
     * @return  int
     */ 
    public function getIdexternalapp(): int
    {
        return $this->idexternalapp;
    }

    /**
     * Set the value of idexternalapp
     *
     * @param  int  $idexternalapp
     *
     * @return  self
     */ 
    public function setIdexternalapp(int $idexternalapp): self
    {
        $this->idexternalapp = $idexternalapp;

        return $this;
    }

    /**
     * Get the value of appName
     *
     * @return  string
     */ 
    public function getAppName(): string
    {
        return $this->appName;
    }

    /**
     * Set the value of appName
     *
     * @param  string  $appName
     *
     * @return  self
     */ 
    public function setAppName(string $appName): self
    {
        $this->appName = $appName;

        return $this;
    }

    /**
     * Get the value of appUrl
     *
     * @return  string
     */ 
    public function getAppUrl(): string
    {
        return $this->appUrl;
    }

    /**
     * Set the value of appUrl
     *
     * @param  string  $appUrl
     *
     * @return  self
     */ 
    public function setAppUrl(string $appUrl): self
    {
        $this->appUrl = $appUrl;

        return $this;
    }

    /**
     * Get the value of gridList
     *
     * @return  array
     */ 
    public function getGridList(): array
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
    public function setGridList(array $gridList): self
    {
        $this->gridList = $gridList;

        return $this;
    }

    /**
     * Get the value of userID
     *
     * @return  int
     */ 
    public function getUserID(): int
    {
        return $this->userID;
    }

    /**
     * Set the value of userID
     *
     * @param  int  $userID
     *
     * @return  self
     */ 
    public function setUserID(int $userID): self
    {
        $this->userID = $userID;

        return $this;
    }

    /**
     * Get the value of settingsList
     *
     * @return  array
     */ 
    public function getSettingsList(): array
    {
        return $this->settingsList;
    }

    /**
     * Set the value of settingsList
     *
     * @param  array  $settingsList
     *
     * @return  self
     */ 
    public function setSettingsList(array $settingsList): self
    {
        $this->settingsList = $settingsList;

        return $this;
    }

    /**
     * Get the value of idexternalsetting
     *
     * @return  int
     */ 
    public function getIdexternalsetting(): int
    {
        return $this->idexternalsetting;
    }

    /**
     * Set the value of idexternalsetting
     *
     * @param  int  $idexternalsetting
     *
     * @return  self
     */ 
    public function setIdexternalsetting(int $idexternalsetting): self
    {
        $this->idexternalsetting = $idexternalsetting;

        return $this;
    }
}