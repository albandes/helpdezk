<?php

namespace App\modules\main\models\mysql;

final class externalappfieldModel
{    
    /**
     * @var int
     */
    private $idExternalSetting;

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
     * @var int
     */
    private $idExternalField;

    /**
     * Get the value of idExternalSetting
     *
     * @return  int
     */ 
    public function getIdExternalSetting(): int
    {
        return $this->idExternalSetting;
    }

    /**
     * Set the value of idExternalSetting
     *
     * @param  int  $idExternalSetting
     *
     * @return  self
     */ 
    public function setIdExternalSetting(int $idExternalSetting): self
    {
        $this->idExternalSetting = $idExternalSetting;

        return $this;
    }

    /**
     * Get the value of userID
     *
     * @return  int
     */ 
    public function getUserID(): int
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
    public function setUserID(int $userID): self
    {
        $this->userID = $userID;

        return $this;
    }

    /**
     * Get the value of fieldName
     *
     * @return  string
     */ 
    public function getFieldName(): string
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
    public function setFieldName(string $fieldName): self
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * Get the value of fieldValue
     *
     * @return  string
     */ 
    public function getFieldValue(): string
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
    public function setFieldValue(string $fieldValue): self
    {
        $this->fieldValue = $fieldValue;

        return $this;
    }

    /**
     * Get the value of idExternalField
     *
     * @return  int
     */ 
    public function getIdExternalField(): int
    {
        return $this->idExternalField;
    }

    /**
     * Set the value of idExternalField
     *
     * @param  int  $idExternalField
     *
     * @return  self
     */ 
    public function setIdExternalField(int $idExternalField): self
    {
        $this->idExternalField = $idExternalField;

        return $this;
    }
}