<?php

	// Configurations
	$initial_year 	= 2009; // sets the first year to show on select options
	$ga_email 		= 'rogerio.albandes@gmail.com'; // email with a google analytics account
	$ga_password 	= 'Piroc@.2009';
	$uids 			= array(
							"19222677", // www.futeboldaqui.com.br
							"36348798", // wp.futeboldaqui.com.br/manoel
							"36372878", // wp.futeboldaqui.com.br/silviocastro
							"36412555", // wp.futeboldaqui.com.br/votto
							"36544647"	// wp.futeboldaqui.com/grandearea
							);
				
	require_once("../classes/gapi/gapi.class.php");	
	
	// Authentication
	$ga = new gapi($ga_email, $ga_password);

	// Defines report period
	$month = isset($_POST['month']) ? $_POST['month'] : date("m", mktime()); //actual month
	$year = isset($_POST['year']) ? $_POST['year'] : date("Y", mktime()); // actual year

	/* -- AdSense -- */	
	$meses = array (1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro");
	/* ------------- */
	
	
	$begin = $year.'-'.$month.'-01';
	$end = $year.'-'.$month.'-'.date("t", mktime(0, 0, 0, $month, 1, $year));
	
		if (isset($_POST['id'])) {
		// Gets total visits and pageviews
		$ga->requestReportData($_POST['id'], 'month', array('pageviews', 'visits'), null, null, $begin, $end);
		foreach ($ga->getResults() as $data) {
			$total_visits = $data->getVisits();
			$total_pageviews = $data->getPageviews();
		}

		// Gets selected month's visits and pageviews day by day
		$ga->requestReportData($_POST['id'], 'day', array('pageviews', 'visits'), 'day', null, $begin, $end, 1, 50);
		foreach ($ga->getResults() as $data) {
			// creating Flot data
			$d1 .= '['.$data.','.$data->getPageviews().'],';
			$d2 .= '['.$data.','.$data->getVisits().'],';
		}
	}
	

$tela .= "
	<form method=\"post\">
		Estatísticas: 
		<select name=\"id\" class=\"dash_select\">
		";
		
		
				// Gets accounts listing
				$ga->requestAccountData();
				foreach($ga->getResults() as $result) {
					$indice = array_search($result->getProfileId(), $uids);
					if(is_numeric($indice)) 
					{
						$selected = ($_POST['id'] == $result->getProfileId()) ? 'SELECTED' : '';
						$tela.= '<option value="' . $result->getProfileId() . '" ' . $selected . '>' . $result . '</option>';
					}
				}
$tela .= "		
		</select>
		<select name=\"month\" class=\"dash_select\">
			<option value=\"01\"  >Janeiro</option>
			<option value=\"02\"  >Fevereiro</option>
			<option value=\"03\"  >Março</option>
			<option value=\"04\"  >Abril</option>
			<option value=\"05\"  >Maio</option>
			<option value=\"06\"  >Junho</option>
			<option value=\"07\"  >Julho</option>
			<option value=\"08\"  >Agosto</option>
			<option value=\"09\"  >Setembro</option>
			<option value=\"10\"  >Outubro</option>
			<option value=\"11\"  >Novembro</option>
			<option value=\"12\"  >Dezembro</option>
		</select>
		<select name=\"year\" class=\"dash_select\">
		";
		
		
				// Shows years from $initial_year to actual
				for ($i=$initial_year; $i<=date("Y", mktime()); $i++) {
					$selected = ($i == $year) ? 'SELECTED' : '';
					$tela .= '<option value="'.$i.'"'.  $selected  .'>'.$i.'</option>';
				}
$tela .= "
		</select>
		<input type=\"submit\" value=\"ok\" class=\"dash_groovybutton\">
	</form>
";

print $tela ;

?>	

	
