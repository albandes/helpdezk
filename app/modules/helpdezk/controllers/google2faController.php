<?php
class google2fa extends Controllers {
    public function __construct(){
        parent::__construct();
        session_start();
        $this->validasessao();
        $this->database = $this->getConfig('db_connect');
    }

    public function index() {
        include 'includes/classes/GoogleAuthenticator/GoogleAuthenticator.php';

        $smarty = $this->retornaSmarty();

        $db = new person_model();
        $ga = new PHPGangsta_GoogleAuthenticator();

        $secret = $ga->createSecret();

        $ret = $db->selectPersonData($_SESSION['SES_COD_USUARIO']);

        $login = $ret->fields['login'];

        $qrCodeUrl = $ga->getQRCodeGoogleUrl($login, $secret, 'Helpdezk');

        $smarty->assign(qrCodeUrl, $qrCodeUrl) ;
        $smarty->assign(idperson,$_SESSION['SES_COD_USUARIO'] );
        $smarty->assign(secret,$secret);
        $smarty->display("modais/google2fa.tpl.html");
    }

    public function insertToken() {
        $aError = array ("success" => true);
        //echo json_encode($aError);
        $dbPerson = new person_model();
        $ret = $dbPerson->updateToken($_POST['idperson'], $_POST['secret']);
        echo true;
    }
}