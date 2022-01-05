<?php
 
namespace App\modules\admin\models\mysql;

final class moduleModel
{    
    /**
     * @var int
     */
    private $idModule;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $status;
    
    /**
     * @var string
     */
    private $path;
    
    /**
     * @var string
     */
    private $smarty;
    
    /**
     * @var string
     */
    private $class;
    
    /**
     * @var string
     */
    private $headerLogo;
    
    /**
     * @var string
     */
    private $reportsLogo;
    
    /**
     * @var string
     */
    private $tablePrefix;
    
    /**
     * @var string
     */
    private $isDefault;

    /**
     * @var array
     */
    private $activeList;

    /**
     * @var int
     */
    private $userID;

    /**
     * @var int
     */
    private $userType;

    /**
     * @var int
     */
    private $categoryID;

    /**
     * @var array
     */
    private $categoriesList;

    /**
     * @var array
     */
    private $permissionsList;

    /**
     * @var array
     */
    private $settingsList;


    /**
     * Get the value of idModule
     *
     * @return  int
     */ 
    public function getIdModule(): int
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
    public function setIdModule(int $idModule): self
    {
        $this->idModule = $idModule;

        return $this;
    }

    /**
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */ 
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of index
     *
     * @return  int
     */ 
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * Set the value of index
     *
     * @param  int  $index
     *
     * @return  self
     */ 
    public function setIndex(int $index): self
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Get the value of status
     *
     * @return  string
     */ 
    public function getStatus():string
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
    public function setStatus(string $status): self
    {
        $this->status = $status;

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
     * Get the value of smarty
     *
     * @return  string
     */ 
    public function getSmarty(): string
    {
        return $this->smarty;
    }

    /**
     * Set the value of smarty
     *
     * @param  string  $smarty
     *
     * @return  self
     */ 
    public function setSmarty(string $smarty): self
    {
        $this->smarty = $smarty;

        return $this;
    }

    /**
     * Get the value of class
     *
     * @return  string
     */ 
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Set the value of class
     *
     * @param  string  $class
     *
     * @return  self
     */ 
    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get the value of headerLogo
     *
     * @return  string
     */ 
    public function getHeaderLogo(): string
    {
        return $this->headerLogo;
    }

    /**
     * Set the value of headerLogo
     *
     * @param  string  $headerLogo
     *
     * @return  self
     */ 
    public function setHeaderLogo(string $headerLogo): self
    {
        $this->headerLogo = $headerLogo;

        return $this;
    }

    /**
     * Get the value of reportsLogo
     *
     * @return  string
     */ 
    public function getReportsLogo(): string
    {
        return $this->reportsLogo;
    }

    /**
     * Set the value of reportsLogo
     *
     * @param  string  $reportsLogo
     *
     * @return  self
     */ 
    public function setReportsLogo(string $reportsLogo): self
    {
        $this->reportsLogo = $reportsLogo;

        return $this;
    }

    /**
     * Get the value of tablePrefix
     *
     * @return  string
     */ 
    public function getTablePrefix(): string
    {
        return $this->tablePrefix;
    }

    /**
     * Set the value of tablePrefix
     *
     * @param  string  $tablePrefix
     *
     * @return  self
     */ 
    public function setTablePrefix(string $tablePrefix): self
    {
        $this->tablePrefix = $tablePrefix;

        return $this;
    }

    /**
     * Get the value of isDefault
     *
     * @return  string
     */ 
    public function getIsDefault(): string
    {
        return $this->isDefault;
    }

    /**
     * Set the value of isDefault
     *
     * @param  string  $isDefault
     *
     * @return  self
     */ 
    public function setIsDefault(string $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }
    

    /**
     * Get the value of activeList
     *
     * @return  array
     */ 
    public function getActiveList(): array
    {
        return $this->activeList;
    }

    /**
     * Set the value of activeList
     *
     * @param  array  $activeList
     *
     * @return  self
     */ 
    public function setActiveList(array $activeList):self
    {
        $this->activeList = $activeList;

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
     * Get the value of categoryID
     *
     * @return  int
     */ 
    public function getCategoryID(): int
    {
        return $this->categoryID;
    }

    /**
     * Set the value of categoryID
     *
     * @param  int  $categoryID
     *
     * @return  self
     */ 
    public function setCategoryID(int $categoryID): self
    {
        $this->categoryID = $categoryID;

        return $this;
    }

    /**
     * Get the value of categoriesList
     *
     * @return  array
     */ 
    public function getCategoriesList(): array
    {
        return $this->categoriesList;
    }

    /**
     * Set the value of categoriesList
     *
     * @param  array  $categoriesList
     *
     * @return  self
     */ 
    public function setCategoriesList(array $categoriesList): self
    {
        $this->categoriesList = $categoriesList;

        return $this;
    }

    /**
     * Get the value of permissionsList
     *
     * @return  array
     */ 
    public function getPermissionsList(): array
    {
        return $this->permissionsList;
    }

    /**
     * Set the value of permissionsList
     *
     * @param  array  $permissionsList
     *
     * @return  self
     */ 
    public function setPermissionsList(array $permissionsList): self
    {
        $this->permissionsList = $permissionsList;

        return $this;
    }

    /**
     * Get the value of settingsList
     *
     * @return  array
     */ 
    public function getSettingsList(): array
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
    public function setSettingsList(array $settingsList): self
    {
        $this->settingsList = $settingsList;

        return $this;
    }
}