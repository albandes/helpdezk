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
     * @var int
     */
    private $totalRows;

    /**
     * @var int
     */
    private $idModule;

    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var array
     */
    private $localeList;

    /**
     * @var array
     */
    private $keyValueList;

    /**
     * @var array
     */
    private $vocabularyIdList;

    /**
     * @var string
     */
    private $status;

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

    /**
     * Get the value of idModule
     *
     * @return  int
     */ 
    public function getIdModule()
    {
        return $this->idModule;
    }

    /**
     * Set the value of idModule
     *
     * @param  int  $idModule
     *
     * @return  self
     */ 
    public function setIdModule(int $idModule)
    {
        $this->idModule = $idModule;

        return $this;
    }

    /**
     * Get the value of moduleName
     *
     * @return  string
     */ 
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Set the value of moduleName
     *
     * @param  string  $moduleName
     *
     * @return  self
     */ 
    public function setModuleName(string $moduleName)
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * Get the value of localeList
     *
     * @return  array
     */ 
    public function getLocaleList()
    {
        return $this->localeList;
    }

    /**
     * Set the value of localeList
     *
     * @param  array  $localeList
     *
     * @return  self
     */ 
    public function setLocaleList(array $localeList)
    {
        $this->localeList = $localeList;

        return $this;
    }

    /**
     * Get the value of keyValueList
     *
     * @return  array
     */ 
    public function getKeyValueList()
    {
        return $this->keyValueList;
    }

    /**
     * Set the value of keyValueList
     *
     * @param  array  $keyValueList
     *
     * @return  self
     */ 
    public function setKeyValueList(array $keyValueList)
    {
        $this->keyValueList = $keyValueList;

        return $this;
    }

    /**
     * Get the value of vocabularyIdList
     *
     * @return  array
     */ 
    public function getVocabularyIdList()
    {
        return $this->vocabularyIdList;
    }

    /**
     * Set the value of vocabularyIdList
     *
     * @param  array  $vocabularyIdList
     *
     * @return  self
     */ 
    public function setVocabularyIdList(array $vocabularyIdList)
    {
        $this->vocabularyIdList = $vocabularyIdList;

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
}
