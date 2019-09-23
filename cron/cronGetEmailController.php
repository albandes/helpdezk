<?php
/**
 * Created by PhpStorm.
 * User: rogerio.albandes
 * Date: 23/09/2019
 * Time: 11:00
 */

require_once(HELPDEZK_PATH . '/cron/cronCommon.php');

class cronGetEmail extends cronCommon {
    /**
     * Create an instance, check session time
     * usage: /home/htdocs/git/helpdezk/cron/index.php getEmail/downloadEmail
     * @access public
     */
    public function __construct()
    {

        parent::__construct();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );


        $this->loadModel('helpdezk/ticket_model');
        $dbTicket = new ticket_model();
        $this->dbTicket = $dbTicket;

        $this->loadModel('helpdezk/requestemail_model');
        $dbGetEmail = new requestemail_model();
        $this->dbGetEmail = $dbGetEmail;
    }

    public function downloadEmail()
    {
        $ret = $this->dbGetEmail->getRequestEmail();
        var_dump($ret);
        while(!$ret->EOF){

            $ret->MoveNext();
        }
    }

}