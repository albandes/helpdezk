<?php

namespace App\modules\helpdezk\models\mysql;

final class departmentModel
{    
    /**
     * @var int
     */
    private $idDepartment;

    /**
     * @var string
     */
    private $department;

    /**
     * @var int
     */
    private $idCompany;
    
    /**
     * @var string
     */
    private $company;    

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
     * Get the value of idDepartment
     *
     * @return  int
     */ 
    public function getIdDepartment()
    {
        return $this->idDepartment;
    }

    /**
     * Set the value of idDepartment
     *
     * @param  int  $idDepartment
     *
     * @return  self
     */ 
    public function setIdDepartment(int $idDepartment)
    {
        $this->idDepartment = $idDepartment;

        return $this;
    }

    /**
     * Get the value of department
     *
     * @return  string
     */ 
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set the value of department
     *
     * @param  string  $department
     *
     * @return  self
     */ 
    public function setDepartment(string $department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get the value of idCompany
     *
     * @return  int
     */ 
    public function getIdCompany()
    {
        return $this->idCompany;
    }

    /**
     * Set the value of idCompany
     *
     * @param  int  $idCompany
     *
     * @return  self
     */ 
    public function setIdCompany(int $idCompany)
    {
        $this->idCompany = $idCompany;

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