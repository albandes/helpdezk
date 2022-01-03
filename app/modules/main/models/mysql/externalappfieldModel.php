<?php

namespace App\modules\main\models\mysql;

final class externalappfieldModel
{    
    /**
     * @var int
     */
    private $idexternalsetting;

    /**
     * @var int
     */
    private $userID;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $fieldValue;   

    /**
     * Get the value of idexternalsetting
     *
     * @return  int
     */ 
    public function getIdexternalsetting()
    {
        return $this->idexternalsetting;
    }

    /**
     * Set the value of idexternalsetting
     *
     * @param  int  $idexternalsetting
     *
     * @return  self
     */ 
    public function setIdexternalsetting(int $idexternalsetting)
    {
        $this->idexternalsetting = $idexternalsetting;

        return $this;
    }

    /**
     * Get the value of userID
     *
     * @return  int
     */ 
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Set the value of userID
     *
     * @param  int  $userID
     *
     * @return  self
     */ 
    public function setUserID(int $userID)
    {
        $this->userID = $userID;

        return $this;
    }

    /**
     * Get the value of fieldName
     *
     * @return  string
     */ 
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Set the value of fieldName
     *
     * @param  string  $fieldName
     *
     * @return  self
     */ 
    public function setFieldName(string $fieldName)
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * Get the value of fieldValue
     *
     * @return  string
     */ 
    public function getFieldValue()
    {
        return $this->fieldValue;
    }

    /**
     * Set the value of fieldValue
     *
     * @param  string  $fieldValue
     *
     * @return  self
     */ 
    public function setFieldValue(string $fieldValue)
    {
        $this->fieldValue = $fieldValue;

        return $this;
    }
}