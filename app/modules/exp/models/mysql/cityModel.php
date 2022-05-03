<?php

namespace App\modules\exp\models\mysql;

final class cityModel
{    
    /**
     * @var int
     */
    private $idCity;

    /**
     * @var int
     */
    private $idState;

    /**
     * @var string
     */
    private $stateName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $dtFoundation;

    /**
     * @var int
     */
    private $isDefault;

    /**
     * @var string
     */
    private $status;
        
    /**
     * @var int
     */
    private $idImage;

    /**
     * @var array
     */
    private $gridList;

    /**
     * @var array
     */
    private $attachments;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $newFileName;

    /**
     * @var int
     */
    private $totalRows;

    /**
     * Get idCity
     *
     * @return  int
     */ 
    public function getIdCity(): int
    {
        return $this->idCity;
    }

    /**
     * Set idCity
     *
     * @param  int  $idCity  idCity
     *
     * @return  self
     */ 
    public function setIdCity($idCity): self
    {
        $this->idCity = $idCity;

        return $this;
    }

    /**
     * Get idState
     *
     * @return  int
     */ 
    public function getIdState(): int
    {
        return $this->idState;
    }

    /**
     * Set idState
     *
     * @param  int  $idState  idState
     *
     * @return  self
     */ 
    public function setIdState($idState): self
    {
        $this->idState = $idState;

        return $this;
    }

    /**
     * Get stateName
     *
     * @return  string
     */ 
    public function getStateName(): string
    {
        return $this->stateName;
    }

    /**
     * Set stateName
     *
     * @param  string  $stateName  stateName
     *
     * @return  self
     */ 
    public function setStateName($stateName): self
    {
        $this->stateName = $stateName;

        return $this;
    }

    /**
     * Get name
     *
     * @return  string
     */ 
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param  string  $name  name
     *
     * @return  self
     */ 
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get dtFoundation
     *
     * @return  string
     */ 
    public function getDtFoundation(): string
    {
        return $this->dtFoundation;
    }

    /**
     * Set dtFoundation
     *
     * @param  string  $dtFoundation  dtFoundation
     *
     * @return  self
     */ 
    public function setDtFoundation($dtFoundation): self
    {
        $this->dtFoundation = $dtFoundation;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return  int
     */ 
    public function getIsDefault(): int
    {
        return $this->isDefault;
    }

    /**
     * Set isDefault
     *
     * @param  int  $isDefault  isDefault
     *
     * @return  self
     */ 
    public function setIsDefault($isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get status
     *
     * @return  string
     */ 
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param  string  $status  status
     *
     * @return  self
     */ 
    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of idImage
     *
     * @return  int
     */ 
    public function getIdImage(): int
    {
        return $this->idImage;
    }

    /**
     * Set the value of idImage
     *
     * @param  int  $idImage
     *
     * @return  self
     */ 
    public function setIdImage(int $idImage): self
    {
        $this->idImage = $idImage;

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

    /**
     * Get the value of attachments
     *
     * @return  array
     */ 
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * Set the value of attachments
     *
     * @param  array  $attachments
     *
     * @return  self
     */ 
    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
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
     * Set the value of fileName
     *
     * @param  string  $fileName
     *
     * @return  self
     */ 
    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get the value of newFileName
     *
     * @return  string
     */ 
    public function getNewFileName(): string
    {
        return $this->newFileName;
    }

    /**
     * Set the value of newFileName
     *
     * @param  string  $newFileName
     *
     * @return  self
     */ 
    public function setNewFileName(string $newFileName): self
    {
        $this->newFileName = $newFileName;

        return $this;
    }

    /**
     * Get the value of totalRows
     *
     * @return  int
     */ 
    public function getTotalRows(): int
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
    public function setTotalRows(int $totalRows): self
    {
        $this->totalRows = $totalRows;

        return $this;
    }
}