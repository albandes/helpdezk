<?php
 
namespace App\modules\admin\models\mysql;

final class moduleModel
{    
    /**
     * @var int
     */
    private $idmodule;

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
    private $headerlogo;
    
    /**
     * @var string
     */
    private $reportslogo;
    
    /**
     * @var string
     */
    private $tableprefix;
    
    /**
     * @var string
     */
    private $isdefault;


    /**
     * Get the value of idmodule
     *
     * @return  int
     */ 
    public function getIdmodule(): int
    {
        return $this->idmodule;
    }

    /**
     * Set the value of idmodule
     *
     * @param  int  $idmodule
     *
     * @return  self
     */ 
    public function setIdmodule(int $idmodule): self
    {
        $this->idmodule = $idmodule;

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
     * Get the value of headerlogo
     *
     * @return  string
     */ 
    public function getHeaderlogo(): string
    {
        return $this->headerlogo;
    }

    /**
     * Set the value of headerlogo
     *
     * @param  string  $headerlogo
     *
     * @return  self
     */ 
    public function setHeaderlogo(string $headerlogo): self
    {
        $this->headerlogo = $headerlogo;

        return $this;
    }

    /**
     * Get the value of reportslogo
     *
     * @return  string
     */ 
    public function getReportslogo(): string
    {
        return $this->reportslogo;
    }

    /**
     * Set the value of reportslogo
     *
     * @param  string  $reportslogo
     *
     * @return  self
     */ 
    public function setReportslogo(string $reportslogo): self
    {
        $this->reportslogo = $reportslogo;

        return $this;
    }

    /**
     * Get the value of tableprefix
     *
     * @return  string
     */ 
    public function getTableprefix(): string
    {
        return $this->tableprefix;
    }

    /**
     * Set the value of tableprefix
     *
     * @param  string  $tableprefix
     *
     * @return  self
     */ 
    public function setTableprefix(string $tableprefix): self
    {
        $this->tableprefix = $tableprefix;

        return $this;
    }

    /**
     * Get the value of isdefault
     *
     * @return  string
     */ 
    public function getIsdefault(): string
    {
        return $this->isdefault;
    }

    /**
     * Set the value of isdefault
     *
     * @param  string  $isdefault
     *
     * @return  self
     */ 
    public function setIsdefault(string $isdefault): self
    {
        $this->isdefault = $isdefault;

        return $this;
    }
    
}