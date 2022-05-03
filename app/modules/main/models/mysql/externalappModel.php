<?php

namespace App\modules\main\models\mysql;

final class externalappModel
{    
    /**
     * @var int
     */
    private $idExternalApp;

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
    private $idExternalSetting;


    /**
     * Get the value of idExternalApp
     *
     * @return  int
     */ 
    public function getIdExternalApp(): int
    {
        return $this->idExternalApp;
    }

    /**
     * Set the value of idexternalapp
     *
     * @param  int  $idexternalapp
     *
     * @return  self
     */ 
    public function setIdExternalApp(int $idExternalApp): self
    {
        $this->idExternalApp = $idExternalApp;

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
     * Get the value of idExternalSetting
     *
     * @return  int
     */ 
    public function getIdExternalSetting(): int
    {
        return $this->idExternalSetting;
    }

    /**
     * Set the value of idExternalSetting
     *
     * @param  int  $idExternalSetting
     *
     * @return  self
     */ 
    public function setIdExternalSetting(int $idExternalSetting): self
    {
        $this->idExternalSetting = $idExternalSetting;

        return $this;
    }
}