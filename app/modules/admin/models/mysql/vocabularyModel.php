<?php

namespace App\modules\admin\models\mysql;

final class vocabularyModel
{    
    /**
     * @var int
     */
    private $idVocabulary; 

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
    private $idLocale;

    /**
     * @var string
     */
    private $localeName;

    /**
     * @var string
     */
    private $localeDescription;

    /**
     * @var array
     */
    private $gridList;

    /**
     * Get the value of idVocabulary
     *
     * @return  int
     */ 
    public function getIdVocabulary(): int
    {
        return $this->idVocabulary;
    }

    /**
     * Set the value of idVocabulary
     *
     * @param  int  $idVocabulary
     *
     * @return  self
     */ 
    public function setIdVocabulary(int $idVocabulary): self
    {
        $this->idVocabulary = $idVocabulary;

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
     * Get the value of idLocale
     *
     * @return  int
     */ 
    public function getIdLocale(): int
    {
        return $this->idLocale;
    }

    /**
     * Set the value of idLocale
     *
     * @param  int  $idLocale
     *
     * @return  self
     */ 
    public function setIdLocale(int $idLocale): self
    {
        $this->idLocale = $idLocale;

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
}
