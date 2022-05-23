<?php
 
namespace App\modules\helpdezk\models\mysql;

final class hdkServiceModel
{
    /**
     * @var int
     */
    private $idArea;

    /**
     * @var string
     */
    private $areaName;

    /**
     * @var int
     */
    private $idType;

    /**
     * @var string
     */
    private $typeName;

    /**
     * @var int
     */
    private $idItem;

    /**
     * @var string
     */
    private $itemName;

    /**
     * @var int
     */
    private $idService;

    /**
     * @var string
     */
    private $serviceName;

    /**
     * @var array
     */
    private $areaList;

    /**
     * @var array
     */
    private $typeList;

    /**
     * @var array
     */
    private $itemList;

    /**
     * @var array
     */
    private $serviceList;

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
     * Get the value of areaName
     *
     * @return  string
     */ 
    public function getAreaName()
    {
        return $this->areaName;
    }

    /**
     * Set the value of areaName
     *
     * @param  string  $areaName
     *
     * @return  self
     */ 
    public function setAreaName(string $areaName)
    {
        $this->areaName = $areaName;

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
     * Get the value of typeName
     *
     * @return  string
     */ 
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * Set the value of typeName
     *
     * @param  string  $typeName
     *
     * @return  self
     */ 
    public function setTypeName(string $typeName)
    {
        $this->typeName = $typeName;

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
     * Get the value of itemName
     *
     * @return  string
     */ 
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * Set the value of itemName
     *
     * @param  string  $itemName
     *
     * @return  self
     */ 
    public function setItemName(string $itemName)
    {
        $this->itemName = $itemName;

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
     * Get the value of serviceName
     *
     * @return  string
     */ 
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Set the value of serviceName
     *
     * @param  string  $serviceName
     *
     * @return  self
     */ 
    public function setServiceName(string $serviceName)
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    /**
     * Get the value of areaList
     *
     * @return  array
     */ 
    public function getAreaList()
    {
        return $this->areaList;
    }

    /**
     * Set the value of areaList
     *
     * @param  array  $areaList
     *
     * @return  self
     */ 
    public function setAreaList(array $areaList)
    {
        $this->areaList = $areaList;

        return $this;
    }

    /**
     * Get the value of typeList
     *
     * @return  array
     */ 
    public function getTypeList()
    {
        return $this->typeList;
    }

    /**
     * Set the value of typeList
     *
     * @param  array  $typeList
     *
     * @return  self
     */ 
    public function setTypeList(array $typeList)
    {
        $this->typeList = $typeList;

        return $this;
    }

    /**
     * Get the value of itemList
     *
     * @return  array
     */ 
    public function getItemList()
    {
        return $this->itemList;
    }

    /**
     * Set the value of itemList
     *
     * @param  array  $itemList
     *
     * @return  self
     */ 
    public function setItemList(array $itemList)
    {
        $this->itemList = $itemList;

        return $this;
    }

    /**
     * Get the value of serviceList
     *
     * @return  array
     */ 
    public function getServiceList()
    {
        return $this->serviceList;
    }

    /**
     * Set the value of serviceList
     *
     * @param  array  $serviceList
     *
     * @return  self
     */ 
    public function setServiceList(array $serviceList)
    {
        $this->serviceList = $serviceList;

        return $this;
    }
}