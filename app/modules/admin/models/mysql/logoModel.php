<?php

namespace App\modules\admin\models\mysql;

final class logoModel
{    
    /**
     * @var int
     */
    private $id;    
    
    /**
     * @var string
     */
    private $name;    
    
    /**
     * @var int
     */
    private $width;    
    
    /**
     * @var int
     */
    private $height; 

    /**
     * @var string
     */
    private $fileName;
    

    /**
     * Get the value of id
     *
     * @return  int
     */ 
    public function getId(): int
    {
        return $this->id;
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
     * Get the value of width
     *
     * @return  int
     */ 
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Get the value of height
     *
     * @return  int
     */ 
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Get the value of fileName
     *
     * @return  string
     */ 
    public function getFileName(): string
    {
        return $this->fileName;
    }
    
    /**
     * Set the value of id
     *
     * @param  int  $id
     * @return  self
     */ 
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     * @return  self
     * 
     */ 
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the value of width
     *
     * @param  int  $width
     * @return  self
     * 
     */ 
    public function setWidth(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Set the value of height
     *
     * @param  int  $height
     * @return  self
     * 
     */ 
    public function setHeight(int $height): self
    {
        $this->height = $height;
        return $this;
    }    

    /**
     * Set the value of fileName
     *
     * @param  string  $fileName
     * @return  self
     */ 
    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;
        return $this;
    }

}