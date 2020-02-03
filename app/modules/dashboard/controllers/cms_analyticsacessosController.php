<?php

class cms_analyticsacessos extends Controllers {

    public function home() {
        include 'includes/config/config.php';
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
		$smarty->assign('idwidget', $idwidget);
		$smarty->assign('refresh', $rs->fields['field4']);
        $smarty->display('cms_analytics_acessos.tpl.html');
    }

    public function json() 
	{

        include 'includes/config/config.php';
		require 'includes/classes/gapi/gapi.class.php';

		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));

		date_default_timezone_set('America/Sao_Paulo');

		$idGoogle   = $rs->fields['field3'] ;
		$UserGoogle = $rs->fields['field1'] ;
		$PassGoogle = $rs->fields['field2'] ;

		$ga = new gapi($UserGoogle,$PassGoogle);

        $inicio = '2016-05-10';
        $fim = '2016-05-13';



		// Busca os pageviews e visitas (Ultimos 30 dias)  
		$ga->requestReportData($idGoogle, 'date', array('pageviews', 'visits'), date, null, $inicio, $fim,null,null);

		$i=1;
		foreach ($ga->getResults() as $dados) 
		{
			//echo  date('Y-m-d', strtotime('+'. $i .'days')). "<br>";
			//echo 'Dia ' . date('Y-m-d', strtotime($dados)) .  ': ' . $dados->getVisits() . ' Visita(s) e ' . $dados->getPageviews() . ' Pageview(s)<br />';
			$acessos[$i] 	= $dados->getVisits() ;
			$pageviews[$i] 	= $dados->getPageviews() ;
			$data[$i] 		= strtotime(date('Y-m-d', strtotime($dados)) . " UTC") * 1000 ;
			$i++;
		}

		$json  = "[";
		$json .= $this->montajson(utf8_encode("Visitas"),$acessos,$data);
		$json .= ",";
		$json .= $this->montajson(utf8_encode("Page Views"),$pageviews,$data);
		$json .= "]";

		echo $json;
		
    }
		
	function montajson($label,$array,$hora) {
		$max = count($array);
		$json .= "{";	
		$json .= " \"label\": \"" . $label . "\",";
		$k=0;
		for($i=$max; $i>=1; $i--)
		{
			if ($i==$max) {
				$json .= "\"data\":[";
			}
			$json .= "[" . $hora[$i] . "," . $array[$i] . "]";
			if ($i > 1) 
			{
				$json .= ", " ; 
			}
			if ($i == 1) 
			{
				$json .= "]" ; 
			}
			$k++;
		}
		$json .= "}";	
		return $json;
	}	
	
	
}




?>