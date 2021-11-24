<?php

namespace App\modules\admin\models\mysql;

final class holidayModel
{    
    /**
     * @var int
     */
    private $idholiday; 

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
    private $idcompany;
    
    /**
     * @var string
     */
    private $company;

    

    /**
     * Get the value of idholiday
     *
     * @return  int
     */ 
    public function getIdholiday(): int
    {
        return $this->idholiday;
    }

    /**
     * Set the value of idholiday
     *
     * @param  int  $idholiday
     *
     * @return  self
     */ 
    public function setIdholiday(int $idholiday): self
    {
        $this->idholiday = $idholiday;

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
     * Get the value of idcompany
     *
     * @return  int
     */ 
    public function getIdcompany(): int
    {
        return $this->idcompany;
    }

    /**
     * Set the value of idcompany
     *
     * @param  int  $idcompany
     *
     * @return  self
     */ 
    public function setIdcompany(int $idcompany): self
    {
        $this->idcompany = $idcompany;

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
}
