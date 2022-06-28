<?php
 
namespace App\modules\helpdezk\models\mysql;

final class expireDateModel
{
    /**
     * @var array
     */
    private $businessDays;

    /**
     * @var int
     */
    private $idCustomer;

    /**
     * @var int
     */
    private $idService;

    /**
     * @var int
     */
    private $attendanceDays;

    /**
     * @var int
     */
    private $attendanceHours;

    /**
     * @var string
     */
    private $timeType;

    /**
     * @var int
     */
    private $idPriority;

    /**
     * Get the value of businessDays
     *
     * @return  array
     */ 
    public function getBusinessDays(): array
    {
        return $this->businessDays;
    }

    /**
     * Set the value of businessDays
     *
     * @param  array  $businessDays
     *
     * @return  self
     */ 
    public function setBusinessDays(array $businessDays): self
    {
        $this->businessDays = $businessDays;

        return $this;
    }

    /**
     * Get the value of idCustomer
     *
     * @return  int
     */ 
    public function getIdCustomer(): int
    {
        return $this->idCustomer;
    }

    /**
     * Set the value of idCustomer
     *
     * @param  int  $idCustomer
     *
     * @return  self
     */ 
    public function setIdCustomer(int $idCustomer): self
    {
        $this->idCustomer = $idCustomer;

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
     * Get the value of attendanceDays
     *
     * @return  int
     */ 
    public function getAttendanceDays()
    {
        return $this->attendanceDays;
    }

    /**
     * Set the value of attendanceDays
     *
     * @param  int  $attendanceDays
     *
     * @return  self
     */ 
    public function setAttendanceDays(int $attendanceDays)
    {
        $this->attendanceDays = $attendanceDays;

        return $this;
    }

    /**
     * Get the value of attendanceHours
     *
     * @return  int
     */ 
    public function getAttendanceHours()
    {
        return $this->attendanceHours;
    }

    /**
     * Set the value of attendanceHours
     *
     * @param  int  $attendanceHours
     *
     * @return  self
     */ 
    public function setAttendanceHours(int $attendanceHours)
    {
        $this->attendanceHours = $attendanceHours;

        return $this;
    }

    /**
     * Get the value of timeType
     *
     * @return  string
     */ 
    public function getTimeType()
    {
        return $this->timeType;
    }

    /**
     * Set the value of timeType
     *
     * @param  string  $timeType
     *
     * @return  self
     */ 
    public function setTimeType(string $timeType)
    {
        $this->timeType = $timeType;

        return $this;
    }

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
}