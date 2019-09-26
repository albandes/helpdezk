<?php
/**
 * Created by PhpStorm.
 * User: rogerio.albandes
 * Date: 25/09/2019
 * Time: 08:20
 */

class ticket {

    public function __construct()
    {
        session_start();

        $this->_loadModel('helpdezk/ticket_model');
        $this->_dbTicket = new ticket_model();

        $this->_loadModel('admin/person_model');
        $this->_dbPerson = new person_model();

        $this->_loadModel('helpdezk/ticketrules_model');
        $this->_dbTicketRules = new ticketrules_model();

        $this->_loadModel('helpdezk/service_model');
        $this->_dbService = new service_model();

    }

    public function _loadModel($modelName)
    {
        $modelPath = '/app/modules/';

        $arrParts = explode("/", $modelName);
        $class = $arrParts[1];
        $file = HELPDEZK_PATH . $modelPath . $arrParts[0] . '/models/' . $class . '.php';

        spl_autoload_register(function ($class) use( &$file) {
            if (file_exists($file)) {
                require_once($file);
            } else {
                die ('The model file does not exist: ' . $file);
            }
        });

    }

    public function isVipUser($idPerson)
    {
        $rsVipuser = $this->_dbTicket->checksVipUser($idPerson);
        if ($rsVipuser->fields['rec_count'] > 0)
            return true;
        else
            return false;

    }

    public function getNumRules($idItem, $idService)
    {
        $rsRules = $this->_dbTicketRules->getRule($idItem, $idService);
        return $rsRules->RecordCount();

    }

    public function getAreaTypeItemByService($idservice)
    {
        $rsCore = $this->_dbService->getCoreByService($idservice);
        if(!$rsCore) {
            return false;
        } else {
            return $rsCore;
        }
    }

    public function createRequestCode(){
        $this->_dbTicket->BeginTrans();

        $rsCode = $this->_dbTicket->getCode();
        if(!$rsCode){
            $this->_dbTicket->RollbackTrans();
            return false;
        }
        // Count month code
        $rsCountCode = $this->_dbTicket->countGetCode();
        if(!$rsCountCode){
            $this->_dbTicket->RollbackTrans();
            return false;
        }
        // If have code request
        if ($rsCountCode->fields['total']) {
            $code_request = $rsCode->fields["cod_request"];
            // Will increase the code of request
            $rsIncressCode = $this->_dbTicket->increaseCode($code_request);
            if(!$rsIncressCode){
                $this->_dbTicket->RollbackTrans();
                return false;
            }
        }
        else {
            //If not have code request will create a new
            $code_request = 1;
            $rsCreateCode = $this->_dbTicket->createCode($code_request);
            if(!$rsCreateCode){
                $this->_dbTicket->RollbackTrans();
                return false;
            }
        }

        $code_request = str_pad($code_request, 6, '0', STR_PAD_LEFT);
        $code_request = date("Ym") . $code_request;
        $this->_dbTicket->CommitTrans();
        return $code_request;
    }

    public function getIdPersonJuridical($idperson)
    {
        $rsPerson = $this->_dbPerson->selectPerson(" AND tbp.idperson = $idperson");
        if(!$rsPerson) {
            return false;
        } else {
            return $rsPerson->fields['idcompany'];
        }

    }

    public function getRules($idItem,$idService)
    {
        $rsRules = $this->_dbTicketRules->getRule($idItem,$idService);
        if ($rsRules->RecordCount() > 0 )  // If have approval rule, put the status of the ticket as repassed (2)
            $idStatus = 2;
    }
}