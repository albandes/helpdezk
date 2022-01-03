<?php

namespace App\modules\main\models\mysql;

final class usersettingsModel
{        
    /**
     * @var int
     */
    private $userID;
    
    /**
     * idlocale
     *
     * @var int
     */
    private $idlocale;
    
    /**
     * @var int
     */
    private $idtheme;
    
    /**
     * @var string
     */
    private $displayGrid;

    /**
     * @var int
     */
    private $userSettingID;

    /**
     * @var string
     */
    private $gridOperator;

    /**
     * @var string
     */
    private $gridOperatorWidth;

    /**
     * @var string
     */
    private $gridUser;

    /**
     * @var string
     */
    private $gridUserWidth;

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
     * Get idlocale
     *
     * @return  int
     */ 
    public function getIdlocale(): int
    {
        return $this->idlocale;
    }

    /**
     * Set idlocale
     *
     * @param  int  $idlocale  idlocale
     *
     * @return  self
     */ 
    public function setIdlocale(int $idlocale): self
    {
        $this->idlocale = $idlocale;

        return $this;
    }

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
     * Get the value of displayGrid
     *
     * @return  string
     */ 
    public function getDisplayGrid(): string
    {
        return $this->displayGrid;
    }

    /**
     * Set the value of displayGrid
     *
     * @param  string  $displayGrid
     *
     * @return  self
     */ 
    public function setDisplayGrid(string $displayGrid): self
    {
        $this->displayGrid = $displayGrid;

        return $this;
    }

    /**
     * Get the value of userSettingID
     *
     * @return  int
     */ 
    public function getUserSettingID(): int
    {
        return $this->userSettingID;
    }

    /**
     * Set the value of userSettingID
     *
     * @param  int  $userSettingID
     *
     * @return  self
     */ 
    public function setUserSettingID(int $userSettingID): self
    {
        $this->userSettingID = $userSettingID;

        return $this;
    }

    /**
     * Get the value of gridOperator
     *
     * @return  string
     */ 
    public function getGridOperator(): string
    {
        return $this->gridOperator;
    }

    /**
     * Set the value of gridOperator
     *
     * @param  string  $gridOperator
     *
     * @return  self
     */ 
    public function setGridOperator(string $gridOperator): self
    {
        $this->gridOperator = $gridOperator;

        return $this;
    }

    /**
     * Get the value of gridOperatorWidth
     *
     * @return  string
     */ 
    public function getGridOperatorWidth(): string
    {
        return $this->gridOperatorWidth;
    }

    /**
     * Set the value of gridOperatorWidth
     *
     * @param  string  $gridOperatorWidth
     *
     * @return  self
     */ 
    public function setGridOperatorWidth(string $gridOperatorWidth): self
    {
        $this->gridOperatorWidth = $gridOperatorWidth;

        return $this;
    }

    /**
     * Get the value of gridUser
     *
     * @return  string
     */ 
    public function getGridUser(): string
    {
        return $this->gridUser;
    }

    /**
     * Set the value of gridUser
     *
     * @param  string  $gridUser
     *
     * @return  self
     */ 
    public function setGridUser(string $gridUser): self
    {
        $this->gridUser = $gridUser;

        return $this;
    }

    /**
     * Get the value of gridUserWidth
     *
     * @return  string
     */ 
    public function getGridUserWidth(): string
    {
        return $this->gridUserWidth;
    }

    /**
     * Set the value of gridUserWidth
     *
     * @param  string  $gridUserWidth
     *
     * @return  self
     */ 
    public function setGridUserWidth(string $gridUserWidth): self
    {
        $this->gridUserWidth = $gridUserWidth;

        return $this;
    }
}