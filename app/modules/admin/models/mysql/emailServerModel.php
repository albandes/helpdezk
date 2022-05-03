<?php

namespace App\modules\admin\models\mysql;

final class emailServerModel
{    
    /**
     * @var int
     */
    private $idEmailServer;

    /**
     * @var int
     */
    private $idServerType;

    /**
     * @var string
     */
    private $serverType;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiSecret;

    /**
     * @var string
     */
    private $apiEndpoint;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $default;

    /**
     * @var array
     */
    private $gridList;

    /**
     * @var int
     */
    private $totalRows;

    /**
     * Get the value of idEmailServer
     *
     * @return  int
     */ 
    public function getIdEmailServer(): int
    {
        return $this->idEmailServer;
    }

    /**
     * Set the value of idEmailServer
     *
     * @param  int  $idEmailServer
     *
     * @return  self
     */ 
    public function setIdEmailServer(int $idEmailServer): self
    {
        $this->idEmailServer = $idEmailServer;

        return $this;
    }

    /**
     * Get the value of idServerType
     *
     * @return  int
     */ 
    public function getIdServerType(): int
    {
        return $this->idServerType;
    }

    /**
     * Set the value of idServerType
     *
     * @param  int  $idServerType
     *
     * @return  self
     */ 
    public function setIdServerType(int $idServerType): self
    {
        $this->idServerType = $idServerType;

        return $this;
    }

    /**
     * Get the value of serverType
     *
     * @return  string
     */ 
    public function getServerType(): string
    {
        return $this->serverType;
    }

    /**
     * Set the value of serverType
     *
     * @param  string  $serverType
     *
     * @return  self
     */ 
    public function setServerType(string $serverType): self
    {
        $this->serverType = $serverType;

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
     * Get the value of user
     *
     * @return  string
     */ 
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @param  string  $user
     *
     * @return  self
     */ 
    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of password
     *
     * @return  string
     */ 
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @param  string  $password
     *
     * @return  self
     */ 
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of port
     *
     * @return  string
     */ 
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * Set the value of port
     *
     * @param  string  $port
     *
     * @return  self
     */ 
    public function setPort(string $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the value of apiKey
     *
     * @return  string
     */ 
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Set the value of apiKey
     *
     * @param  string  $apiKey
     *
     * @return  self
     */ 
    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get the value of apiSecret
     *
     * @return  string
     */ 
    public function getApiSecret(): string
    {
        return $this->apiSecret;
    }

    /**
     * Set the value of apiSecret
     *
     * @param  string  $apiSecret
     *
     * @return  self
     */ 
    public function setApiSecret(string $apiSecret): self
    {
        $this->apiSecret = $apiSecret;

        return $this;
    }

    /**
     * Get the value of apiEndpoint
     *
     * @return  string
     */ 
    public function getApiEndpoint(): string
    {
        return $this->apiEndpoint;
    }

    /**
     * Set the value of apiEndpoint
     *
     * @param  string  $apiEndpoint
     *
     * @return  self
     */ 
    public function setApiEndpoint(string $apiEndpoint): self
    {
        $this->apiEndpoint = $apiEndpoint;

        return $this;
    }

    /**
     * Get the value of status
     *
     * @return  string
     */ 
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  string  $status
     *
     * @return  self
     */ 
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of default
     *
     * @return  string
     */ 
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * Set the value of default
     *
     * @param  string  $default
     *
     * @return  self
     */ 
    public function setDefault(string $default): self
    {
        $this->default = $default;

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
