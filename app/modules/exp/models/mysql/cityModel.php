<?php

namespace App\modules\exp\models\mysql;

final class cityModel
{    
    /**
     * @var int
     */
    private $idcity;

    /**
     * @var int
     */
    private $idstate;

    /**
     * @var string
     */
    private $statename;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $dtfoundation;

    /**
     * @var int
     */
    private $isdefault;

    /**
     * @var string
     */
    private $status;
        
    /**
     * @var int
     */
    private $idimage;

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
    private $filename;

    /**
     * @var string
     */
    private $newFileName;

    /**
     * Get idcity
     *
     * @return  int
     */ 
    public function getIdcity(): int
    {
        return $this->idcity;
    }

    /**
     * Set idcity
     *
     * @param  int  $idcity  idcity
     *
     * @return  self
     */ 
    public function setIdcity($idcity): self
    {
        $this->idcity = $idcity;

        return $this;
    }

    /**
     * Get idstate
     *
     * @return  int
     */ 
    public function getIdstate(): int
    {
        return $this->idstate;
    }

    /**
     * Set idstate
     *
     * @param  int  $idstate  idstate
     *
     * @return  self
     */ 
    public function setIdstate($idstate): self
    {
        $this->idstate = $idstate;

        return $this;
    }

    /**
     * Get statename
     *
     * @return  string
     */ 
    public function getStatename(): string
    {
        return $this->statename;
    }

    /**
     * Set statename
     *
     * @param  string  $statename  statename
     *
     * @return  self
     */ 
    public function setStatename($statename): self
    {
        $this->statename = $statename;

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
     * Get dtfoundation
     *
     * @return  string
     */ 
    public function getDtfoundation(): string
    {
        return $this->dtfoundation;
    }

    /**
     * Set dtfoundation
     *
     * @param  string  $dtfoundation  dtfoundation
     *
     * @return  self
     */ 
    public function setDtfoundation($dtfoundation): self
    {
        $this->dtfoundation = $dtfoundation;

        return $this;
    }

    /**
     * Get isdefault
     *
     * @return  int
     */ 
    public function getIsdefault(): int
    {
        return $this->isdefault;
    }

    /**
     * Set isdefault
     *
     * @param  int  $isdefault  isdefault
     *
     * @return  self
     */ 
    public function setIsdefault($isdefault): self
    {
        $this->isdefault = $isdefault;

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
     * Get the value of idimage
     *
     * @return  int
     */ 
    public function getIdimage(): int
    {
        return $this->idimage;
    }

    /**
     * Set the value of idimage
     *
     * @param  int  $idimage
     *
     * @return  self
     */ 
    public function setIdimage(int $idimage): self
    {
        $this->idimage = $idimage;

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
     * Get the value of filename
     *
     * @return  string
     */ 
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Set the value of filename
     *
     * @param  string  $filename
     *
     * @return  self
     */ 
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

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
}