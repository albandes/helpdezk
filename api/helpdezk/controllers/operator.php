<?php
/**
 * Created by PhpStorm.
 * User: Rogério
 * Date: 28/05/2017
 * Time: 20:14
 */


class Operator extends apiController
{

    public function __construct()
    {
        parent::__construct();
        $this->_log = true;
        $this->_logFile = $this->getApiLog();

    }

    public function post_assumerequest($arrParam)
    {

        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/assumerequest", 'INFO', $this->_logFile);

        $idPerson = $this->_isLoged($arrParam['token']);

        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/assumerequest - " . $this->_getLangVar('API_Error_token'), 'ERROR', $this->_logFile);
            return array('error' => $this->_getLangVar('API_Error_token'));
        }

        $arrParam['idperson'] = $idPerson;

        $ret = $this->_assumeRequest($arrParam);

        if(!$ret) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/assumerequest - Error processing request save.", 'ERROR', $this->_logFile);
            return array('error' => 'Error processing assume request .');

        } else {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/assumerequest - Request included successfully: ".$ret, 'INFO', $this->_logFile);
            return array('success'=> 'Request assumed successfully.');
        }

    }

}

