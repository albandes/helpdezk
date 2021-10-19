<?php

namespace App\modules\admin\models\mysql;

final class LogoModel
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
    public function getId()
    {
        return $this->id;
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
     * Get the value of width
     *
     * @return  int
     */ 
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get the value of height
     *
     * @return  int
     */ 
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get the value of fileName
     *
     * @return  string
     */ 
    public function getFileName()
    {
        return $this->fileName;
    }



    /**
     * Set the value of name
     *
     * @param  string  $name
     * @return  LogoModel
     * 
     */ 
    public function setName(string $name): LogoModel
    {
        $this->name = $name;
    }

    /**
     * Set the value of width
     *
     * @param  int  $width
     * @return  LogoModel
     * 
     */ 
    public function setWidth(int $width): LogoModel
    {
        $this->width = $width;
    }

    /**
     * Set the value of height
     *
     * @param  int  $height
     * @return  LogoModel
     * 
     */ 
    public function setHeight(int $height): LogoModel
    {
        $this->height = $height;
    }

    /**
     * Set the value of fileName
     *
     * @param  string  $fileName
     * @return  LogoModel
     * 
     */ 
    public function setFileName(string $fileName): LogoModel
    {
        $this->fileName = $fileName;
    }
}