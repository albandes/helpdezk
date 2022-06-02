<?php

class inf_embratel extends Controllers 
{

    public function home() 
	{
        //include 'includes/config/config.php';
		/*
		if(substr($path_default, 0,1)!='/'){
			$path_default='/'.$path_default;
		}

		if ($path_default == "/..") {   
			define(path,"");
		} else {
			define(path,$path_default);
		}
		
		
		*/
		$idwidget = $this->getParam('idwidget');
		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));	

        $smarty = $this->retornaSmarty();
		$smarty->assign('path', path);
		$smarty->assign('url', $rs->fields['field1']);
        $smarty->display('inf_embratel.tpl.html');
    }

	
}




?>