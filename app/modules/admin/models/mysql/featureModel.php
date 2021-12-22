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
}