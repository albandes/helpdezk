<?php

namespace App\modules\helpdezk\models\mysql;

final class attendanceTypeModel
{    
    /**
     * @var int
     */
    private $idAttendanceType;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $default;
    
    /**
     * @var array
     */
    private $gridList; 
    
    /**
    * @var int
    */
    private $totalRows;    

    /**
     * Get the value of idAttendanceType
     *
     * @return  int
     */ 
    public function getIdAttendanceType()
    {
        return $this->idAttendanceType;
    }

    /**
     * Set the value of idAttendanceType
     *
     * @param  int  $idAttendanceType
     *
     * @return  self
     */ 
    public function setIdAttendanceType(int $idAttendanceType)
    {
        $this->idAttendanceType = $idAttendanceType;

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
     * Get the value of default
     *
     * @return  string
     */ 
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set the value of default
     *
     * @param  string  $default
     *
     * @return  self
     */ 
    public function setDefault(string $default)
    {
        $this->default = $default;

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