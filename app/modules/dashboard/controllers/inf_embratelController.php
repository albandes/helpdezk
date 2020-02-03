<?php

class inf_embratel extends Controllers 
{

    public function home() 
	{



		$idwidget = $this->getParam('idwidget');
		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));	
		

        $smarty = $this->retornaSmarty();
		$smarty->assign('path', path);
		$smarty->assign('url', $rs->fields['field1']);
		$smarty->assign('refresh', $rs->fields['field2']);
        $smarty->display('inf_embratel.tpl.html');
    }

	
}




?>