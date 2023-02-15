<?php
 
namespace App\modules\admin\models\mysql;

final class errorMessageModel
{    
    /**
     * @var int
     */
    private $errorMesssageId;

    /**
     * @var string
     */
    private $name;

     /**
     * @var string
     */
    private $description;
    
    /**
     * @var string
     */
    private $errorCode;

    /**
     * @var int
     */
    private $idModule;

    /**
     * @var string
     */
    private $moduleName;
    
    /**
     * @var string
     */
    private $languageKeyName;

    /**
     * @var string
     */
    private $modulePath;

     /**
     * @var string
     */
    private $formatedCode;
    
    

    /**
     * Get the value of errorMesssageId
     *
     * @return  int
     */ 
    public function getErrorMesssageId()
    {
        return $this->errorMesssageId;
    }

    /**
     * Set the value of errorMesssageId
     *
     * @param  int  $errorMesssageId
     *
     * @return  self
     */ 
    public function setErrorMesssageId(int $errorMesssageId)
    {
        $this->errorMesssageId = $errorMesssageId;

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
     * Get the value of description
     *
     * @return  string
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string  $description
     *
     * @return  self
     */ 
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of errorCode
     *
     * @return  string
     */ 
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Set the value of errorCode
     *
     * @param  string  $errorCode
     *
     * @return  self
     */ 
    public function setErrorCode(string $errorCode)
    {
        $this->errorCode = $errorCode;

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
     * Get the value of modulePath
     *
     * @return  string
     */ 
    public function getModulePath()
    {
        return $this->modulePath;
    }

    /**
     * Set the value of modulePath
     *
     * @param  string  $modulePath
     *
     * @return  self
     */ 
    public function setModulePath(string $modulePath)
    {
        $this->modulePath = $modulePath;

        return $this;
    }

    /**
     * Get the value of formatedCode
     *
     * @return  string
     */ 
    public function getFormatedCode()
    {
        return $this->formatedCode;
    }

    /**
     * Set the value of formatedCode
     *
     * @param  string  $formatedCode
     *
     * @return  self
     */ 
    public function setFormatedCode(string $formatedCode)
    {
        $this->formatedCode = $formatedCode;

        return $this;
    }
}