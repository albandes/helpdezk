<?php

namespace App\modules\admin\models\mysql;

final class trackerModel
{    
    /**
     * @var int
     */
    private $idEmail;

    /**
     * @var int
     */
    private $idModule;

    /**
     * @var string
     */
    private $sender;

    /**
     * @var string
     */
    private $recipient;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $content;

    /**
     * @var int
     */
    private $idMandrill;

    /**
     * Get the value of idEmail
     *
     * @return  int
     */ 
    public function getIdEmail(): int
    {
        return $this->idEmail;
    }

    /**
     * Set the value of idEmail
     *
     * @param  int  $idEmail
     *
     * @return  self
     */ 
    public function setIdEmail(int $idEmail): self
    {
        $this->idEmail = $idEmail;

        return $this;
    }

    /**
     * Get the value of idModule
     *
     * @return  int
     */ 
    public function getIdModule(): int
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
    public function setIdModule(int $idModule): self
    {
        $this->idModule = $idModule;

        return $this;
    }

    /**
     * Get the value of sender
     *
     * @return  string
     */ 
    public function getSender(): string
    {
        return $this->sender;
    }

    /**
     * Set the value of sender
     *
     * @param  string  $sender
     *
     * @return  self
     */ 
    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get the value of recipient
     *
     * @return  string
     */ 
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * Set the value of recipient
     *
     * @param  string  $recipient
     *
     * @return  self
     */ 
    public function setRecipient(string $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get the value of subject
     *
     * @return  string
     */ 
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Set the value of subject
     *
     * @param  string  $subject
     *
     * @return  self
     */ 
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the value of content
     *
     * @return  string
     */ 
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @param  string  $content
     *
     * @return  self
     */ 
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of idMandrill
     *
     * @return  int
     */ 
    public function getIdMandrill(): int
    {
        return $this->idMandrill;
    }

    /**
     * Set the value of idMandrill
     *
     * @param  int  $idMandrill
     *
     * @return  self
     */ 
    public function setIdMandrill(int $idMandrill): self
    {
        $this->idMandrill = $idMandrill;

        return $this;
    }
}
