<?php

namespace App\modules\admin\models\mysql;

final class emailSettingsModel
{    
    /**
     * title
     *
     * @var string
     */
    private $title;

    /**
     * domain
     *
     * @var string
     */
    private $domain;

    /**
     * sender
     *
     * @var string
     */
    private $sender;

    /**
     * header
     *
     * @var string
     */
    private $header;

    /**
     * footer
     *
     * @var string
     */
    private $footer;
    
    /**
     * tls
     *
     * @var int
     */
    private $tls;

    /**
     * auth
     *
     * @var int
     */
    private $auth;

    /**
     * Get title
     *
     * @return  string
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param  string  $title  title
     *
     * @return  self
     */ 
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get domain
     *
     * @return  string
     */ 
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set domain
     *
     * @param  string  $domain  domain
     *
     * @return  self
     */ 
    public function setDomain(string $domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get sender
     *
     * @return  string
     */ 
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set sender
     *
     * @param  string  $sender  sender
     *
     * @return  self
     */ 
    public function setSender(string $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get header
     *
     * @return  string
     */ 
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set header
     *
     * @param  string  $header  header
     *
     * @return  self
     */ 
    public function setHeader(string $header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get footer
     *
     * @return  string
     */ 
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Set footer
     *
     * @param  string  $footer  footer
     *
     * @return  self
     */ 
    public function setFooter(string $footer)
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * Get tls
     *
     * @return  int
     */ 
    public function getTls()
    {
        return $this->tls;
    }

    /**
     * Set tls
     *
     * @param  int  $tls  tls
     *
     * @return  self
     */ 
    public function setTls(int $tls)
    {
        $this->tls = $tls;

        return $this;
    }

    /**
     * Get auth
     *
     * @return  int
     */ 
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Set auth
     *
     * @param  int  $auth  auth
     *
     * @return  self
     */ 
    public function setAuth(int $auth)
    {
        $this->auth = $auth;

        return $this;
    }
}