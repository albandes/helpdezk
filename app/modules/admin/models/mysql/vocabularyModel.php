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
     * @var int
     */
    private $idlocale;

    /**
     * @var string
     */
    private $localeName;

    /**
     * @var string
     */
    private $localeDescription;

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

    /**
     * Get the value of idlocale
     *
     * @return  int
     */ 
    public function getIdlocale(): int
    {
        return $this->idlocale;
    }

    /**
     * Set the value of idlocale
     *
     * @param  int  $idlocale
     *
     * @return  self
     */ 
    public function setIdlocale(int $idlocale): self
    {
        $this->idlocale = $idlocale;

        return $this;
    }

    /**
     * Get the value of localeName
     *
     * @return  string
     */ 
    public function getLocaleName(): string
    {
        return $this->localeName;
    }

    /**
     * Set the value of localeName
     *
     * @param  string  $localeName
     *
     * @return  self
     */ 
    public function setLocaleName(string $localeName): self
    {
        $this->localeName = $localeName;

        return $this;
    }

    /**
     * Get the value of localeDescription
     *
     * @return  string
     */ 
    public function getLocaleDescription(): string
    {
        return $this->localeDescription;
    }

    /**
     * Set the value of localeDescription
     *
     * @param  string  $localeDescription
     *
     * @return  self
     */ 
    public function setLocaleDescription(string $localeDescription): self
    {
        $this->localeDescription = $localeDescription;

        return $this;
    }
}
