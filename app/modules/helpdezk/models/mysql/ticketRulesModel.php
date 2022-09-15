<?php
 
namespace App\modules\helpdezk\models\mysql;

final class ticketRulesModel
{
    /**
     * @var int
     */
    private $idApproval;

    /**
     * @var int
     */
    private $itemId;

    /**
     * @var string
     */
    private $itemName;

    /**
     * @var int
     */
    private $serviceId;

    /**
     * @var string
     */
    private $serviceName;
    
    /**
     * @var int
     */
    private $idPerson;

    /**
     * @var string
     */
    private $approverName;

    /**
     * @var int
     */
    private $order;

    /**
     * @var int
     */
    private $isRecalculate;

    /**
     * @var array
     */
    private $gridList;
    
    /**
     * @var int
     */
    private $totalRows;

    /**
     * @var string
     */
    private $ticketCode;

    /**
     * @var int
     */
    private $idTicketApproval;

    /**
     * Get the value of idApproval
     *
     * @return  int
     */ 
    public function getIdApproval()
    {
        return $this->idApproval;
    }

    /**
     * Set the value of idApproval
     *
     * @param  int  $idApproval
     *
     * @return  self
     */ 
    public function setIdApproval(int $idApproval)
    {
        $this->idApproval = $idApproval;

        return $this;
    }

    /**
     * Get the value of itemId
     *
     * @return  int
     */ 
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set the value of itemId
     *
     * @param  int  $itemId
     *
     * @return  self
     */ 
    public function setItemId(int $itemId)
    {
        $this->itemId = $itemId;

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
     * Get the value of serviceId
     *
     * @return  int
     */ 
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Set the value of serviceId
     *
     * @param  int  $serviceId
     *
     * @return  self
     */ 
    public function setServiceId(int $serviceId)
    {
        $this->serviceId = $serviceId;

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
     * Get the value of idPerson
     *
     * @return  int
     */ 
    public function getIdPerson()
    {
        return $this->idPerson;
    }

    /**
     * Set the value of idPerson
     *
     * @param  int  $idPerson
     *
     * @return  self
     */ 
    public function setIdPerson(int $idPerson)
    {
        $this->idPerson = $idPerson;

        return $this;
    }

    /**
     * Get the value of approverName
     *
     * @return  string
     */ 
    public function getApproverName()
    {
        return $this->approverName;
    }

    /**
     * Set the value of approverName
     *
     * @param  string  $approverName
     *
     * @return  self
     */ 
    public function setApproverName(string $approverName)
    {
        $this->approverName = $approverName;

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
     * Get the value of isRecalculate
     *
     * @return  int
     */ 
    public function getIsRecalculate()
    {
        return $this->isRecalculate;
    }

    /**
     * Set the value of isRecalculate
     *
     * @param  int  $isRecalculate
     *
     * @return  self
     */ 
    public function setIsRecalculate(int $isRecalculate)
    {
        $this->isRecalculate = $isRecalculate;

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
     * Get the value of ticketCode
     *
     * @return  string
     */ 
    public function getTicketCode()
    {
        return $this->ticketCode;
    }

    /**
     * Set the value of ticketCode
     *
     * @param  string  $ticketCode
     *
     * @return  self
     */ 
    public function setTicketCode(string $ticketCode)
    {
        $this->ticketCode = $ticketCode;

        return $this;
    }

    /**
     * Get the value of idTicketApproval
     *
     * @return  int
     */ 
    public function getIdTicketApproval()
    {
        return $this->idTicketApproval;
    }

    /**
     * Set the value of idTicketApproval
     *
     * @param  int  $idTicketApproval
     *
     * @return  self
     */ 
    public function setIdTicketApproval(int $idTicketApproval)
    {
        $this->idTicketApproval = $idTicketApproval;

        return $this;
    }
}