<?php

namespace App\modules\admin\models\mysql;

final class localeModel
{    
    /**
     * @var int
     */
    private $idLocale;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $gridList;


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
     * Get the value of description
     *
     * @return  string
     */ 
    public function getDescription(): string
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
    public function setDescription(string $description): self
    {
        $this->description = $description;

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