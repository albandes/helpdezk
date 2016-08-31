<?php

class Config extends Controllers {
 
 	private $id;
 	
	public function __construct(){
		session_start();
		$this->validasessao();
		$this->id = $this->check();
	}
	
	public function check(){
		$smarty = $this->retornaSmarty();
		$idperson = $_SESSION['SES_COD_USUARIO'];
		$db = new userconfig_model();
        $check = $db->checkConf($idperson);
		return $check;
	}
	
	public function setConfig(){
		$id = $this->id;
		$value = $_POST['value'];
		$field = $_POST['field'];
		
		if(!$value) return false;
		$db = new userconfig_model();
        $set = $db->setConfigValue($id, $value, $field);
		if($set){
			$_SESSION['SES_PERSONAL_USER_CONFIG'][$field] = $value;
			echo "ok";
		}
		else return false;
	}
    
}







