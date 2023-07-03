<?php
 
namespace App\modules\admin\models\mysql;

final class featureModel
{
    /**
     * @var array
     */
    private $globalSettingsList;

    /**
     * @var int
     */
    private $userID;

    /**
     * @var array
     */
    private $userSettingsList;
    
    /**
     * @var string
     */
    private $tableName;
    
    /**
     * @var bool
     */
    private $existTable;

    /**
     * @var int
     */
    private $userType;
    
    /**
     * @var string
     */
    private $path;

    /**
     * @var int
     */
    private $settingCatId;

    /**
     * @var array
     */
    private $settingsList;

    /**
     * @var string
     */
    private $sessionName;

    /**
     * @var string
     */
    private $settingValue;

    /**
     * @var array
     */
    private $settingsCatList;

    /**
     * @var int
     */
    private $settingId;

    /**
     * @var string
     */
    private $settingCatName;

    /**
     * @var string
     */
    private $settingCatFlgSetup;

    /**
     * @var string
     */
    private $settingCatLangKey;

    /**
     * @var string
     */
    private $settingName;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $fieldValue;

    /**
     * @var string
     */
    private $settingDescription;

    /**
     * @var string
     */
    private $settingLangKey;

    /**
     * @var string
     */
    private $fieldType;

    /**
     * @var string
     */
    private $flagDefault;

    /**
     * Get the value of globalSettingsList
     *
     * @return  array
     */ 
    public function getGlobalSettingsList(): array
    {
        return $this->globalSettingsList;
    }

    /**
     * Set the value of globalSettingsList
     *
     * @param  array  $globalSettingsList
     *
     * @return  self
     */ 
    public function setGlobalSettingsList(array $globalSettingsList): self
    {
        $this->globalSettingsList = $globalSettingsList;

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
     * Get the value of userSettingsList
     *
     * @return  array
     */ 
    public function getUserSettingsList(): array
    {
        return $this->userSettingsList;
    }

    /**
     * Set the value of userSettingsList
     *
     * @param  array  $userSettingsList
     *
     * @return  self
     */ 
    public function setUserSettingsList(array $userSettingsList): self
    {
        $this->userSettingsList = $userSettingsList;

        return $this;
    }

    /**
     * Get the value of tableName
     *
     * @return  string
     */ 
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Set the value of tableName
     *
     * @param  string  $tableName
     *
     * @return  self
     */ 
    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * Get the value of existTable
     *
     * @return  bool
     */ 
    public function getExistTable(): bool
    {
        return $this->existTable;
    }

    /**
     * Set the value of existTable
     *
     * @param  bool  $existTable
     *
     * @return  self
     */ 
    public function setExistTable(bool $existTable): self
    {
        $this->existTable = $existTable;

        return $this;
    }

    /**
     * Get the value of userType
     *
     * @return  int
     */ 
    public function getUserType(): int
    {
        return $this->userType;
    }

    /**
     * Set the value of userType
     *
     * @param  int  $userType
     *
     * @return  self
     */ 
    public function setUserType(int $userType): self
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * Get the value of path
     *
     * @return  string
     */ 
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @param  string  $path
     *
     * @return  self
     */ 
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of settingCatId
     *
     * @return  int
     */ 
    public function getSettingCatId()
    {
        return $this->settingCatId;
    }

    /**
     * Set the value of settingCatId
     *
     * @param  int  $settingCatId
     *
     * @return  self
     */ 
    public function setSettingCatId(int $settingCatId)
    {
        $this->settingCatId = $settingCatId;

        return $this;
    }

    /**
     * Get the value of settingsList
     *
     * @return  array
     */ 
    public function getSettingsList()
    {
        return $this->settingsList;
    }

    /**
     * Set the value of settingsList
     *
     * @param  array  $settingsList
     *
     * @return  self
     */ 
    public function setSettingsList(array $settingsList)
    {
        $this->settingsList = $settingsList;

        return $this;
    }

    /**
     * Get the value of sessionName
     *
     * @return  string
     */ 
    public function getSessionName()
    {
        return $this->sessionName;
    }

    /**
     * Set the value of sessionName
     *
     * @param  string  $sessionName
     *
     * @return  self
     */ 
    public function setSessionName(string $sessionName)
    {
        $this->sessionName = $sessionName;

        return $this;
    }

    /**
     * Get the value of settingValue
     *
     * @return  string
     */ 
    public function getSettingValue()
    {
        return $this->settingValue;
    }

    /**
     * Set the value of settingValue
     *
     * @param  string  $settingValue
     *
     * @return  self
     */ 
    public function setSettingValue(string $settingValue)
    {
        $this->settingValue = $settingValue;

        return $this;
    }

    /**
     * Get the value of settingsCatList
     *
     * @return  array
     */ 
    public function getSettingsCatList()
    {
        return $this->settingsCatList;
    }

    /**
     * Set the value of settingsCatList
     *
     * @param  array  $settingsCatList
     *
     * @return  self
     */ 
    public function setSettingsCatList(array $settingsCatList)
    {
        $this->settingsCatList = $settingsCatList;

        return $this;
    }

    /**
     * Get the value of settingId
     *
     * @return  int
     */ 
    public function getSettingId()
    {
        return $this->settingId;
    }

    /**
     * Set the value of settingId
     *
     * @param  int  $settingId
     *
     * @return  self
     */ 
    public function setSettingId(int $settingId)
    {
        $this->settingId = $settingId;

        return $this;
    }

    /**
     * Get the value of settingCatName
     *
     * @return  string
     */ 
    public function getSettingCatName()
    {
        return $this->settingCatName;
    }

    /**
     * Set the value of settingCatName
     *
     * @param  string  $settingCatName
     *
     * @return  self
     */ 
    public function setSettingCatName(string $settingCatName)
    {
        $this->settingCatName = $settingCatName;

        return $this;
    }

    /**
     * Get the value of settingCatFlgSetup
     *
     * @return  string
     */ 
    public function getSettingCatFlgSetup()
    {
        return $this->settingCatFlgSetup;
    }

    /**
     * Set the value of settingCatFlgSetup
     *
     * @param  string  $settingCatFlgSetup
     *
     * @return  self
     */ 
    public function setSettingCatFlgSetup(string $settingCatFlgSetup)
    {
        $this->settingCatFlgSetup = $settingCatFlgSetup;

        return $this;
    }

    /**
     * Get the value of settingCatLangKey
     *
     * @return  string
     */ 
    public function getSettingCatLangKey()
    {
        return $this->settingCatLangKey;
    }

    /**
     * Set the value of settingCatLangKey
     *
     * @param  string  $settingCatLangKey
     *
     * @return  self
     */ 
    public function setSettingCatLangKey(string $settingCatLangKey)
    {
        $this->settingCatLangKey = $settingCatLangKey;

        return $this;
    }

    /**
     * Get the value of settingName
     *
     * @return  string
     */ 
    public function getSettingName()
    {
        return $this->settingName;
    }

    /**
     * Set the value of settingName
     *
     * @param  string  $settingName
     *
     * @return  self
     */ 
    public function setSettingName(string $settingName)
    {
        $this->settingName = $settingName;

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

    /**
     * Get the value of settingDescription
     *
     * @return  string
     */ 
    public function getSettingDescription()
    {
        return $this->settingDescription;
    }

    /**
     * Set the value of settingDescription
     *
     * @param  string  $settingDescription
     *
     * @return  self
     */ 
    public function setSettingDescription(string $settingDescription)
    {
        $this->settingDescription = $settingDescription;

        return $this;
    }

    /**
     * Get the value of settingLangKey
     *
     * @return  string
     */ 
    public function getSettingLangKey()
    {
        return $this->settingLangKey;
    }

    /**
     * Set the value of settingLangKey
     *
     * @param  string  $settingLangKey
     *
     * @return  self
     */ 
    public function setSettingLangKey(string $settingLangKey)
    {
        $this->settingLangKey = $settingLangKey;

        return $this;
    }

    /**
     * Get the value of fieldType
     *
     * @return  string
     */ 
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * Set the value of fieldType
     *
     * @param  string  $fieldType
     *
     * @return  self
     */ 
    public function setFieldType(string $fieldType)
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    /**
     * Get the value of flagDefault
     *
     * @return  string
     */ 
    public function getFlagDefault()
    {
        return $this->flagDefault;
    }

    /**
     * Set the value of flagDefault
     *
     * @param  string  $flagDefault
     *
     * @return  self
     */ 
    public function setFlagDefault(string $flagDefault)
    {
        $this->flagDefault = $flagDefault;

        return $this;
    }
}