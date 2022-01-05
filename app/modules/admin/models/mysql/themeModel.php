<?php

namespace App\modules\admin\models\mysql;

final class themeModel
{    
    /**
     * @var int
     */
    private $idTheme;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $gridList;


    /**
     * Get the value of idTheme
     *
     * @return  int
     */ 
    public function getIdTheme(): int
    {
        return $this->idTheme;
    }

    /**
     * Set the value of idTheme
     *
     * @param  int  $idTheme
     *
     * @return  self
     */ 
    public function setIdTheme(int $idTheme): self
    {
        $this->idTheme = $idTheme;

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