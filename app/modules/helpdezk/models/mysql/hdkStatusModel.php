<?php

namespace App\modules\helpdezk\models\mysql;

final class hdkStatusModel
{    
    /**
     * @var int
     */
    private $statusId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $requesterView;
    
    /**
     * @var string
     */
    private $color;
    
    /**
     * @var string
     */
    private $status;

    /**
     * @var int
     */
    private $statusSourceId;

    /**
     * @var int
     */
    private $stopSlaFlag;

    /**
     * @var array
     */
    private $gridList; 
    
    /**
    * @var int
    */
    private $totalRows;

    /**
     * @var array
     */
    private $LinkList;

    /**
     * Get the value of statusId
     *
     * @return  int
     */ 
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * Set the value of statusId
     *
     * @param  int  $statusId
     *
     * @return  self
     */ 
    public function setStatusId(int $statusId)
    {
        $this->statusId = $statusId;

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
     * Get the value of requesterView
     *
     * @return  string
     */ 
    public function getRequesterView()
    {
        return $this->requesterView;
    }

    /**
     * Set the value of requesterView
     *
     * @param  string  $requesterView
     *
     * @return  self
     */ 
    public function setRequesterView(string $requesterView)
    {
        $this->requesterView = $requesterView;

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
     * Get the value of statusSourceId
     *
     * @return  int
     */ 
    public function getStatusSourceId()
    {
        return $this->statusSourceId;
    }

    /**
     * Set the value of statusSourceId
     *
     * @param  int  $statusSourceId
     *
     * @return  self
     */ 
    public function setStatusSourceId(int $statusSourceId)
    {
        $this->statusSourceId = $statusSourceId;

        return $this;
    }

    /**
     * Get the value of stopSlaFlag
     *
     * @return  int
     */ 
    public function getStopSlaFlag()
    {
        return $this->stopSlaFlag;
    }

    /**
     * Set the value of stopSlaFlag
     *
     * @param  int  $stopSlaFlag
     *
     * @return  self
     */ 
    public function setStopSlaFlag(int $stopSlaFlag)
    {
        $this->stopSlaFlag = $stopSlaFlag;

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
     * Get the value of LinkList
     *
     * @return  array
     */ 
    public function getLinkList()
    {
        return $this->LinkList;
    }

    /**
     * Set the value of LinkList
     *
     * @param  array  $LinkList
     *
     * @return  self
     */ 
    public function setLinkList(array $LinkList)
    {
        $this->LinkList = $LinkList;

        return $this;
    }
}