<?php

namespace App\modules\helpdezk\models\mysql;

final class reasonModel
{    
    /**
     * @var int
     */
    private $idReason;

    /**
     * @var string
     */
    private $reason;

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
}