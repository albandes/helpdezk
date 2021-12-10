<?php

namespace App\core;

use App\src\appServices;
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

    /**
     * @var object
     */
    protected $loggerDB;
    /**
     * @var object
     */
    protected $emailLoggerDB;

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

        $appSrc = new appServices();

        // create a log channel
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);

        $streamDB = $appSrc->_getStreamHandler();
        $streamDB->setFormatter($formatter);


        $this->loggerDB  = new Logger('helpdezk');
        $this->loggerDB->pushHandler($streamDB);

        // Clone the first one to only change the channel
        $this->emailLoggerDB = $this->loggerDB->withName('email');
    }

}