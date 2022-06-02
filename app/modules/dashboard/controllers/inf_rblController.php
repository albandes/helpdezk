<?php

class inf_rbl extends Controllers 
{

    public function home() 
	{
		$idwidget = $this->getParam('idwidget');

        include 'includes/config/config.php';
		if(substr($path_default, 0,1)!='/'){
			$path_default='/'.$path_default;
		}
		if ($path_default == "/..") {   
			define(path,"");
		} else {
			define(path,$path_default);
		}
		
		
				
		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));	

        $smarty = $this->retornaSmarty();
		$smarty->assign('path', path);
		$smarty->assign('idwidget', $idwidget);
		$smarty->assign('ip', $rs->fields['field1']);
		$smarty->assign('refresh', $rs->fields['field2']);
        $smarty->display('inf_rbl.tpl.html');
    }
    public function json() 
	{
		$rbl=array() ;
		//$rbl["SpamRats"]            = "all.spamrats.com";
		$rbl["Barracuda Central"]   = "b.barracudacentral.org";
		$rbl["SpamHaus"]            = "zen.spamhaus.org";
		//$rbl["Njabl"]               = "dnsbl.njabl.org";
		$rbl["SpamCop"]             = "bl.spamcop.net";
		$rbl["Sorbs"]               = "dnsbl.sorbs.net";
		//$rbl["Msrbl"]               = "combined.rbl.msrbl.net";
		//$rbl["Psbl"]                = "psbl.surriel.com";
		$rbl["Apews"]                           = "l2.apews.org";
		$rbl["Spamcannibal"]		= "spamcannibal.org";
	
        include 'includes/config/config.php';
		if(substr($path_default, 0,1)!='/'){
			$path_default='/'.$path_default;
		}

		if ($path_default == "/..") {   
			$path_root = "";
		} else {
			$path_root = $path_default;
		}

		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));
		$ip	= $rs->fields['field1'];

		$html = "
				<table id=\"hor-minimalist-b\" summary=\"Employee Pay Sheet\">
					<thead>
						<tr>
							<th scope=\"col\">Rbl</th>
							<th scope=\"col\">Status</th>
						</tr>
					</thead>
					<tbody>		
				";
				
		foreach( $rbl as $key => $dnsbl){
        
			/*
			$comando = "host " . $ip . "." . $dnsbl ;
			$result = exec($comando);
		
			if ( preg_match("/\s*has address/",$result)) {
                $gif = "not_available_wid.png" ;
            } else {
				$gif = "checked_wid.png" ;
			}      
			*/
			
			$host= $ip . "." . $dnsbl ;

			if ($host != gethostbyname($host)) {
                $gif = "not_available_wid.png" ;
            } else {
				$gif = "checked_wid.png" ;
			}   			
			$html .= 	"<tr><td>".$dnsbl."</td><td><img src=".$path_root."/app/themes/".$theme_default."/images/".$gif." width=\"12\" height=\"12\"></td></tr>";
		}
		
		$html .=	"
						</tbody>
					</table>	
					<p class=\"first\">Updated: ".date('H\h i\m\i\n')."</p>		
					";

		$arr = array ('linha'=>$html);
		
		echo json_encode($arr);	
	}
	
}




?>