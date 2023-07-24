<?php

namespace App\modules\helpdezk\models\mysql;

final class priorityModel
{    
    /**
     * @var int
     */
    private $idPriority;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $order;
    
    /**
     * @var string
     */
    private $color;
    
    /**
     * @var int
     */
    private $default;

    /**
     * @var int
     */
    private $vip;

    /**
     * @var int
     */
    private $limitHours;

    /**
     * @var int
     */
    private $limitDays;

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
     * @var int
     */
    private $defaultId;

    /**
     * @var array
     */
    private $linkList;

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
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of order
     *
     * @return  int
     */ 
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set the value of order
     *
     * @param  int  $order
     *
     * @return  self
     */ 
    public function setOrder(int $order)
    {
        $this->order = $order;

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
     * Get the value of default
     *
     * @return  int
     */ 
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set the value of default
     *
     * @param  int  $default
     *
     * @return  self
     */ 
    public function setDefault(int $default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Get the value of vip
     *
     * @return  int
     */ 
    public function getVip()
    {
        return $this->vip;
    }

    /**
     * Set the value of vip
     *
     * @param  int  $vip
     *
     * @return  self
     */ 
    public function setVip(int $vip)
    {
        $this->vip = $vip;

        return $this;
    }

    /**
     * Get the value of limitHours
     *
     * @return  int
     */ 
    public function getLimitHours()
    {
        return $this->limitHours;
    }

    /**
     * Set the value of limitHours
     *
     * @param  int  $limitHours
     *
     * @return  self
     */ 
    public function setLimitHours(int $limitHours)
    {
        $this->limitHours = $limitHours;

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

    /**
     * Get the value of defaultId
     *
     * @return  int
     */ 
    public function getDefaultId()
    {
        return $this->defaultId;
    }

    /**
     * Set the value of defaultId
     *
     * @param  int  $defaultId
     *
     * @return  self
     */ 
    public function setDefaultId(int $defaultId)
    {
        $this->defaultId = $defaultId;

        return $this;
    }

    /**
     * Get the value of linkList
     *
     * @return  array
     */ 
    public function getLinkList()
    {
        return $this->linkList;
    }

    /**
     * Set the value of linkList
     *
     * @param  array  $linkList
     *
     * @return  self
     */ 
    public function setLinkList(array $linkList)
    {
        $this->linkList = $linkList;

        return $this;
    }
}