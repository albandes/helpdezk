<?php

namespace App\modules\admin\models\mysql;

final class holidayModel
{    
    /**
     * @var int
     */
    private $idHoliday; 

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $idCompany;
    
    /**
     * @var string
     */
    private $company;

    /**
     * @var array
     */
    private $gridList;

    /**
     * @var int
     */
    private $year;

    /**
     * @var int
     */
    private $nextYear;

    /**
     * @var array
     */
    private $yearList;

    /**
     * Get the value of idHoliday
     *
     * @return  int
     */ 
    public function getIdHoliday(): int
    {
        return $this->idHoliday;
    }

    /**
     * Set the value of idHoliday
     *
     * @param  int  $idHoliday
     *
     * @return  self
     */ 
    public function setIdHoliday(int $idHoliday): self
    {
        $this->idHoliday = $idHoliday;

        return $this;
    }

    /**
     * Get the value of date
     *
     * @return  string
     */ 
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @param  string  $date
     *
     * @return  self
     */ 
    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return  string
     */ 
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string  $description
     *
     * @return  self
     */ 
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of idCompany
     *
     * @return  int
     */ 
    public function getIdCompany(): int
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
    public function setIdCompany(int $idCompany): self
    {
        $this->idCompany = $idCompany;

        return $this;
    }

    /**
     * Get the value of company
     *
     * @return  string
     */ 
    public function getCompany(): string
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
    public function setCompany(string $company): self
    {
        $this->company = $company;

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
     * Get the value of year
     *
     * @return  int
     */ 
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Set the value of year
     *
     * @param  int  $year
     *
     * @return  self
     */ 
    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get the value of nextYear
     *
     * @return  int
     */ 
    public function getNextYear(): int
    {
        return $this->nextYear;
    }

    /**
     * Set the value of nextYear
     *
     * @param  int  $nextYear
     *
     * @return  self
     */ 
    public function setNextYear(int $nextYear): self
    {
        $this->nextYear = $nextYear;

        return $this;
    }

    /**
     * Get the value of yearList
     *
     * @return  array
     */ 
    public function getYearList(): array
    {
        return $this->yearList;
    }

    /**
     * Set the value of yearList
     *
     * @param  array  $yearList
     *
     * @return  self
     */ 
    public function setYearList(array $yearList): self
    {
        $this->yearList = $yearList;

        return $this;
    }
}
