<?php

namespace App\core;

use Monolog\Logger;
//use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;


class Database
{
    protected $DB_NAME;
    protected $DB_USER;
    protected $DB_PASSWORD;
    protected $DB_HOST;
    protected $DB_PORT;
    
    /**
     * @var \PDO
     */
    protected $db;

    public function __construct()
    {
        // When this class is instantiated, the variable $db is assigned the connection to the db
        $this->DB_NAME = $_ENV['DB_NAME'];
        $this->DB_USER = $_ENV['DB_USERNAME'];
        $this->DB_PASSWORD = $_ENV['DB_PASSWORD'];
        $this->DB_HOST = $_ENV['DB_HOSTNAME'];
        $this->DB_PORT = $_ENV['DB_PORT'];
        
        $DSN = "mysql:host={$this->DB_HOST};port={$this->DB_PORT};dbname={$this->DB_NAME}";
        try{
            $this->db = new \PDO($DSN,$this->DB_USER,$this->DB_PASSWORD);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
        }catch(\PDOException $ex){
            die("<br>Error connecting to database: " . $ex->getMessage() . " File: " . __FILE__ . " Line: " . __LINE__ );
        }

        // create a log channel
        $dateFormat = "d/m/Y H:i:s";
        $formatter = new LineFormatter(null, $dateFormat);

        $streamDB = new StreamHandler('storage/logs/helpdezk.log', Logger::DEBUG);
        $streamDB->setFormatter($formatter);


        $this->loggerDB  = new Logger('helpdezk');
        $this->loggerDB->pushHandler($streamDB);
    }

}