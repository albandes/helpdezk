<?php

class inf_tempserver extends Controllers {

    public function home() {
        include 'includes/config/config.php';
		
		$idwidget = $this->getParam('idwidget');
		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));	
		
        $smarty = $this->retornaSmarty();
		$smarty->assign('path', path);
		$smarty->assign('idwidget', $idwidget);
		$smarty->assign('urlJSON', $rs->fields['field1']);
		$smarty->assign('refresh', $rs->fields['field2']);
		$smarty->assign('timeToRefresh', $rs->fields['field2']/60000);
		$smarty->assign('num', $rs->fields['field3']);
		$smarty->assign('ticks', $rs->fields['field4']);
        $smarty->display('inf_tempserver.tpl.html');
    }

		
	
	
}




?>