<?php
 
namespace App\modules\helpdezk\models\mysql;

final class expireDateModel
{
    /**
     * @var array
     */
    private $businessDays;


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
}