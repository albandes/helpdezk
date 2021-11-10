<?php

namespace App\modules\admin\models\mysql;

final class vocabularyModel
{    
    /**
     * @var int
     */
    private $idvocabulary; 

    /**
     * @var string
     */
    private $keyName;

    /**
     * @var string
     */
    private $keyValue;

    /**
     * Get the value of idvocabulary
     *
     * @return  int
     */ 
    public function getIdvocabulary(): int
    {
        return $this->idvocabulary;
    }

    /**
     * Set the value of idvocabulary
     *
     * @param  int  $idvocabulary
     *
     * @return  self
     */ 
    public function setIdvocabulary(int $idvocabulary): self
    {
        $this->idvocabulary = $idvocabulary;

        return $this;
    }

    /**
     * Get the value of keyName
     *
     * @return  string
     */ 
    public function getKeyName(): string
    {
        return $this->keyName;
    }

    /**
     * Set the value of keyName
     *
     * @param  string  $keyName
     *
     * @return  self
     */ 
    public function setKeyName(string $keyName): self
    {
        $this->keyName = $keyName;

        return $this;
    }

    /**
     * Get the value of keyValue
     *
     * @return  string
     */ 
    public function getKeyValue(): string
    {
        return $this->keyValue;
    }

    /**
     * Set the value of keyValue
     *
     * @param  string  $keyValue
     *
     * @return  self
     */ 
    public function setKeyValue(string $keyValue): self
    {
        $this->keyValue = $keyValue;

        return $this;
    }
}
