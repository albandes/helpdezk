<?php
class Index extends Controllers{
   /*public function index(){
        $dados = $this->getParam();
        $mod = new System();
        $module =  $mod->_module;
        $smarty = $this->retornaSmarty();
        $smarty->display('admin.tpl.html');
    }*/
	public function valida(){
		session_start();
		return $this->validasessao(1);	
	}
}
?>
