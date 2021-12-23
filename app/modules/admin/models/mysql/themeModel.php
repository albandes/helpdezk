<?php

namespace App\modules\admin\models\mysql;

final class themeModel
{    
    /**
     * @var int
     */
    private $idtheme;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $gridList;


    /**
     * Get the value of idtheme
     *
     * @return  int
     */ 
    public function getIdtheme(): int
    {
        return $this->idtheme;
    }

    /**
     * Set the value of idtheme
     *
     * @param  int  $idtheme
     *
     * @return  self
     */ 
    public function setIdtheme(int $idtheme): self
    {
        $this->idtheme = $idtheme;

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