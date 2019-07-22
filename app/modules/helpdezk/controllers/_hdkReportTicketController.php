<?php

require_once(HELPDEZK_PATH . '/app/modules/helpdezk/controllers/hdkCommonController.php');

class hdkReportTicket extends hdkCommon
{
    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->sessionValidate();

        $this->logfile = $this->helpdezkPath . '/logs/request.log';
        $this->program = basename(__FILE__);

        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()');

        $dbHome = new home_model();
        $this->dbHome = $dbHome;

        $dbTicket = new ticket_model();
        $this->dbTicket = $dbTicket;



    }

    public function makeReport()
    {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $code_request = $_POST['code_request'];

        $where = 'WHERE code_request = '.$code_request;
        $rsTicket = $this->dbTicket->getRequestData($where);

        $idperson = $_SESSION['SES_COD_USUARIO'];
        $idowner  = $rsTicket->fields['idperson_owner'];

        if($idperson != $idowner) die ($langVars['Access_denied']);

        $output[] = array( "colLeft" => $rsTicket->fields['code_request'], "colRight" => $rsTicket->fields['name_creator']) ;
        $output[] = array( "colLeft" => $rsTicket->fields['personname'],   "colRight" => $rsTicket->fields['source']) ;
        $output[] = array( "colLeft" => $rsTicket->fields['company'],      "colRight" => '') ;
        $output[] = array( "colLeft" => $rsTicket->fields['department'],   "colRight" => $rsTicket->fields['status']) ;

        //session_start();
        $_SESSION['reportData'] = $output ;

        echo json_encode($output);

        $this->retornaFpdf();

    }
}
