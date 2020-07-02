<?php


class Auth extends apiController{

    public function __construct()
    {

        parent::__construct();

        $this->_log = true ;
        $this->_logFile  = $this->getApiLog();


        if(class_exists('Controllers')) {
            session_start();
            $this->validasessao();
        }

    }

    // OK
	public function post_login($arrParam)
    {

        if (version_compare($this->_helpdezkVersionNumber, '1.1.6', '<=' )) {
            $dbModel = new apiModel();
            $dbModel->fixTokenColumnTbPerson();
        }

        if ((empty($arrParam['login'])) or (empty($arrParam['password']))) {
            if($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - Error: Username or password is empty" , 'INFO', $this->_logFile);
            $check['error'] = 'Username and password must be informed.';
            return $check;
        }

        $login      = $arrParam['login'];
        $passMD5    = md5($arrParam['password']);
        $password   = $arrParam['password'];


        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/auth - Login: " . $login, 'INFO', $this->_logFile);

        $bd = new index_model();

        $logintype = $bd->getTypeLogin($login);

		if ($logintype->fields) {
            $logintype = $logintype->fields['idtypelogin'];
        } else {
            if($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - Bad login: " . $login, 'INFO', $this->_logFile);

            $check['error'] = 'The username or password you entered is incorrect';
            return $check;
        }


		switch ($logintype) {
            case '3': // HelpDEZk                
                $idperson = $bd->selectDataLogin($login, $passMD5);

                if ($idperson) {
                    $loginOK = true;
                } else {
                    $check['error'] = 'The username or password you entered is incorrect';
                    $loginOK = false ;
                }
                break;
            case '1': // Pop/Imap Server
                $bd_cfg = new features_model();
                $popconfigs = $bd_cfg->getPopConfigs();

                $host = $popconfigs['POP_HOST'];
                $port = $popconfigs['POP_PORT'];
                $type = $popconfigs['POP_TYPE'];
                
                if ($type == 'POP'){
                    $hostname = '{' . $host . ':'.$port.'/pop3}INBOX' ;
                } elseif ($type == 'GMAIL') {
                    $hostname = '{imap.gmail.com:'.$port.'/imap/ssl/novalidate-cert}INBOX';
                }

                /* try to connect */
                $mbox = imap_open($hostname,$login,$password);
                if($mbox) {                    
                    $idperson = $bd->getIdPerson($login);
                    imap_close($mbox);
                    $loginOK = true;
                } else {
                    $loginOK = false ;
                }                
                
                break;

            case '2': // AD/LDAP
                if ($idperson) {
                    $loginOK = true;
                } else {
                    $loginOK = false ;
                }
                
                break;
			default: 
				$loginOK = false;
				break;
        }

        $token = hash('sha512',rand(100,1000));

        if ($loginOK) {

            if($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - Login OK: " . $login, 'INFO', $this->_logFile);

            $type = $bd->selectTypePerson($idperson);

            if(!$type)
                return false;

            switch ($type->fields['idtypeperson']) {               
				case "2":
				     /*
						$this->startSession($idperson);
                    	$this->getConfigSession();
						if($_SESSION['SES_MAINTENANCE'] == 1){
							$check['error'] = $_SESSION['SES_MAINTENANCE_MSG'];
	                    }else{
                            $token = hash('sha512',rand(100,1000));
                            $setToken = $bd->setToken($idperson,$token);
                            if($setToken){
                                $check['success'] = array(
                                                        "token"     => $token,
                                                        "name"      => $_SESSION['SES_NAME_PERSON'],
                                                        "idtype"    => $type->fields['idtypeperson'],
                                                         "typename" => $type->fields['name'],
                                                        "license"   => $this->getConfig('license')
                                                         );
                            }else{
                                $check['error'] = "Error generating token, please try again .";
                            }
						}
						*/

				    if ($this->setLogin($idperson,$token) != false) {
                        $check['success'] = array( "token"     => $token,
                                                   "name"      => $_SESSION['SES_NAME_PERSON'],
                                                   "idtype"    => $type->fields['idtypeperson'],
                                                   "typename"  => $type->fields['name'],
                                                   "license"   => $this->getConfig('license') );
                    } else {
                        $check['error'] = "Error generating token, please try again .";
                    }

				     break;

				case "3":					
					$check['error'] = "Esta versão não está disponível para atendentes!";
					break;
				default:
                    if ($this->setLogin($idperson,$token) != false) {
                        $check['success'] = array( "token"     => $token,
                                                   "name"      => $_SESSION['SES_NAME_PERSON'],
                                                   "idtype"    => $type->fields['idtypeperson'],
                                                   "typename" => $type->fields['name'],
                                                   "license"   => $this->getConfig('license') );
                    } else {
                        $check['error'] = "Error generating token, please try again .";
                    }

				break;
			}
        } else {
            if($this->_log)
                $this->log("Remote Addr: " . $_SERVER["REMOTE_ADDR"] . " - Bad login: " . $login, 'INFO', $this->_logFile);
        }
		return $check;
		
	}

	public function startSession($idperson) {
        $_SESSION['SES_COD_USUARIO'] = $idperson;
        $_SESSION['REFRESH'] = false;
        
        //SAVE THE CUSTOMER'S LICENSE
        $_SESSION['SES_LICENSE']    = $this->getLicense();
        $_SESSION['SES_ENTERPRISE'] = $this->getEnterprise();
        
        $bd = new index_model();
        if ($_SESSION['SES_COD_USUARIO'] != 1) {
            $typeuser = $bd->selectDataSession($idperson);

            $_SESSION['SES_NAME_PERSON'] = $typeuser->fields['name'];
            $_SESSION['SES_TYPE_PERSON'] = $typeuser->fields['idtypeperson'];
            $_SESSION['SES_IND_CODIGO_ANOMES'] = true;
            $_SESSION['SES_COD_EMPRESA'] = $typeuser->fields['idjuridical'];
            $_SESSION['SES_COD_TIPO'] = $typeuser->fields['idtypeperson'];

        }
    }

    public function getConfigSession() {
        $bd = new index_model();
        $data = $bd->getConfigData();
        while (!$data->EOF) {
            $ses = $data->fields['session_name'];
            $val = $data->fields['value'];
            $_SESSION[$ses] = $val;
            $data->MoveNext();
        }
    }
	
    public function post_logout($user){
        $token = $user->token;
        $cod_usu = $this->getUserByToken($token);
        if(!$cod_usu) return array('error'=> 'Não logado');
        $token = "";
        $bd = new index_model();
        $setToken = $bd->setToken($cod_usu,$token);
        if($setToken)
            return array('success');
        else
            return array('error');
    }
    
    function getUserByToken($token){
        $db = new index_model();
        $idperson = $db->getUserIdByToken($token);
        if($idperson)
            return $idperson;
        else
            return false;
    }

    function setLogin($idPerson,$token)
    {
        $db = new index_model();

        $this->startSession($idPerson);
        $this->getConfigSession();
        if($_SESSION['SES_MAINTENANCE'] == 1){
            $check['error'] = $_SESSION['SES_MAINTENANCE_MSG'];
        }else{

            $setToken = $db->setToken($idPerson,$token);
            if($setToken){
                return $token;
            }else{
                return false;
            }
        }

    }
}
