<?php
/**
 *
 * Class to log,  used in  cron or webhooks
 *
 * User: rogerio.albandes
 * Date: 28/06/2019
 * Time: 08:51
 */

class log {

    public $logFile = '';

    public function __construct($logFile)
    {
        $this->logFile = $logFile;
    }


    function logIt($msg,$logLevel,$line = null)
    {

        $levelStr = '';
        switch ( $logLevel ) {
            case '0':
                $levelStr = 'EMERG';
                break;
            case '1':
                $levelStr = 'ALERT';
                break;
            case '2':
                $levelStr = 'CRIT';
                break;
            case '3':
                $levelStr = 'ERR';
                break;
            case '4':
                $levelStr = 'WARNING';
                break;
            case '5':
                $levelStr = 'NOTICE';
                break;
            case '6':
                $levelStr = 'INFO';
                break;
            case '7':
                $levelStr = 'DEBUG';
                break;
        }

        $date = date($this->getlogDateHour());

        if($line)
            $msg .= ' line '. $line;

        $msg = sprintf( "[%s] [%s]: %s%s", $date, $levelStr, $msg, PHP_EOL );

        $file = $this->logFile;

        file_put_contents( $file, $msg, FILE_APPEND );

    }

    function getlogDateHour()
    {
        return "d/m/Y H:i:s";
    }


}