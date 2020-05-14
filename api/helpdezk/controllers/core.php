<?php

class core extends apiController
{

    public function __construct()
{

parent::__construct();

$this->_log = true ;
$this->_logFile  = $this->getApiLog();

}

    public function get_area($arrParam)
    {
        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/core/area " , 'INFO', $this->_logFile);

        $token = $arrParam['token'];

        if(!$this->_isLoged($token)) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/area - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        } else {
            return $this->_getArea();
        }

    }

    public function get_type($arrParam)
    {

        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/core/type " , 'INFO', $this->_logFile);

        if(!$this->_isLoged($arrParam['token'])) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/type - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        if(array_key_exists('area',$arrParam)) {
            $idArea = $arrParam['area'];
        } else {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/type - Area ID is empty, needs to be informed.", 'ERROR', $this->_logFile);
            return array('error' => 'Area ID is empty, needs to be informed.');
        }


        $rsType = $this->_getType($idArea);
        if ($rsType->RecordCount() == 0) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/type - Area ID not found.", 'ERROR', $this->_logFile);
            return array('error' => 'Area ID not found.');
        }


        while (!$rsType->EOF) {
            $result[] = array(
                "id" => $rsType->fields['idtype'],
                "name" => $rsType->fields['name'],
                "default" => $rsType->fields['default'],
            );
            $rsType->MoveNext();
        }
        return $result;
    }

    public function get_item($arrParam)
    {
        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/core/item " , 'INFO', $this->_logFile);

        if(!$this->_isLoged($arrParam['token'])) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/item - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        if(array_key_exists('type',$arrParam)) {
            $idType = $arrParam['type'];
        } else {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/item - Type ID is empty, needs to be informed.", 'ERROR', $this->_logFile);
            return array('error' => 'Type ID is empty, needs to be informed.');
        }

        $rsItem = $this->_getItem($idType);
        if ($rsItem->RecordCount() == 0) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/item - Type ID not found.", 'ERROR', $this->_logFile);
            return array('error' => 'Type ID not found.');
        }

        while (!$rsItem->EOF) {
            $result[] = array(
                    "id" => $rsItem->fields['iditem'],
                    "name" => $rsItem->fields['name'],
                    "default" => $rsItem->fields['default'],
            );
            $rsItem->MoveNext();
        }

        return $result;

    }

    public function get_service($arrParam)
    {
        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/core/service " , 'INFO', $this->_logFile);

        if(!$this->_isLoged($arrParam['token'])) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/service - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        if(array_key_exists('item',$arrParam)) {
            $idItem = $arrParam['item'];
        } else {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/service - Item ID is empty, needs to be informed.", 'ERROR', $this->_logFile);
            return array('error' => 'Item ID is empty, needs to be informed.');
        }

        $rsService = $this->_getService($idItem);
        if ($rsService->RecordCount() == 0) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/service - Item ID not found.", 'ERROR', $this->_logFile);
            return array('error' => 'Item ID not found.');
        }

        while (!$rsService->EOF) {
            $result[] = array(
                "id" => $rsService->fields['idservice'],
                "name" => $rsService->fields['name'],
                "default" => $rsService->fields['default']
            );
            $rsService->MoveNext();
        }

        return $result;

    }

    public function get_reason($arrParam)
    {
        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/core/reason " , 'INFO', $this->_logFile);

        if(!$this->_isLoged($arrParam['token'])) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/reason - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        if(array_key_exists('service',$arrParam)) {
            $idService = $arrParam['service'];
        } else {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/reason - Service ID is empty, needs to be informed.", 'ERROR', $this->_logFile);
            return array('error' => 'Service ID is empty, needs to be informed.');
        }

        $rsReason = $this->_getReason($idService);
        if ($rsReason->RecordCount() == 0) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/core/reason - Service ID not found.", 'ERROR', $this->_logFile);
            return array('error' => 'Service ID not found.');
        }

        while (!$rsReason->EOF) {
            $result[] = array(
                "id" => $rsReason->fields['idreason'],
                "name" => $rsReason->fields['name'],
                "default" => $rsReason->fields['default']
            );
            $rsReason->MoveNext();
        }

        return $result;

    }


}
