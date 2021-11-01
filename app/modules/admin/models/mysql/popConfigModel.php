<?php

namespace App\modules\admin\models\mysql;

final class popConfigModel
{    
    /**
     * host
     *
     * @var string
     */
    private $host;
    
    /**
     * port
     *
     * @var int
     */
    private $port;
    
    /**
     * type
     *
     * @var string
     */
    private $type;
        
    /**
     * domain
     *
     * @var string
     */
    private $domain;

    /**
     * Get host
     *
     * @return  string
     */ 
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Set host
     *
     * @param  string  $host  host
     *
     * @return  self
     */ 
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get port
     *
     * @return  int
     */ 
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Set port
     *
     * @param  int  $port  port
     *
     * @return  self
     */ 
    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get type
     *
     * @return  string
     */ 
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param  string  $type  type
     *
     * @return  self
     */ 
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get domain
     *
     * @return  string
     */ 
    public function getDomain(): string
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
    public function setDomain(string $domain):self
    {
        $this->domain = $domain;

        return $this;
    }
}