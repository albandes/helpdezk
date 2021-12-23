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
}