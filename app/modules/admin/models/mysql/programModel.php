<?php
 
namespace App\modules\admin\models\mysql;

final class programModel
{    
    /**
     * @var int
     */
    private $programId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $moduleId;

    /**
     * @var string
     */
    private $module;

    /**
     * @var int
     */
    private $programCategoryId;

    /**
     * @var string
     */
    private $programCategory;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $languageKeyName;

    /**
     * @var array
     */
    private $gridList;

    /**
     * @var int
     */
    private $totalRows;

    /**
     * Get the value of programId
     *
     * @return  int
     */ 
    public function getProgramId()
    {
        return $this->programId;
    }

    /**
     * Set the value of programId
     *
     * @param  int  $programId
     *
     * @return  self
     */ 
    public function setProgramId(int $programId)
    {
        $this->programId = $programId;

        return $this;
    }

    /**
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName()
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
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of moduleId
     *
     * @return  int
     */ 
    public function getModuleId()
    {
        return $this->moduleId;
    }

    /**
     * Set the value of moduleId
     *
     * @param  int  $moduleId
     *
     * @return  self
     */ 
    public function setModuleId(int $moduleId)
    {
        $this->moduleId = $moduleId;

        return $this;
    }

    /**
     * Get the value of module
     *
     * @return  string
     */ 
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set the value of module
     *
     * @param  string  $module
     *
     * @return  self
     */ 
    public function setModule(string $module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get the value of programCategoryId
     *
     * @return  int
     */ 
    public function getProgramCategoryId()
    {
        return $this->programCategoryId;
    }

    /**
     * Set the value of programCategoryId
     *
     * @param  int  $programCategoryId
     *
     * @return  self
     */ 
    public function setProgramCategoryId(int $programCategoryId)
    {
        $this->programCategoryId = $programCategoryId;

        return $this;
    }

    /**
     * Get the value of programCategory
     *
     * @return  string
     */ 
    public function getProgramCategory()
    {
        return $this->programCategory;
    }

    /**
     * Set the value of programCategory
     *
     * @param  string  $programCategory
     *
     * @return  self
     */ 
    public function setProgramCategory(string $programCategory)
    {
        $this->programCategory = $programCategory;

        return $this;
    }

    /**
     * Get the value of controller
     *
     * @return  string
     */ 
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set the value of controller
     *
     * @param  string  $controller
     *
     * @return  self
     */ 
    public function setController(string $controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Get the value of index
     *
     * @return  int
     */ 
    public function getIndex()
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
    public function setIndex(int $index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Get the value of languageKeyName
     *
     * @return  string
     */ 
    public function getLanguageKeyName()
    {
        return $this->languageKeyName;
    }

    /**
     * Set the value of languageKeyName
     *
     * @param  string  $languageKeyName
     *
     * @return  self
     */ 
    public function setLanguageKeyName(string $languageKeyName)
    {
        $this->languageKeyName = $languageKeyName;

        return $this;
    }

    /**
     * Get the value of gridList
     *
     * @return  array
     */ 
    public function getGridList()
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
    public function setGridList(array $gridList)
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
}