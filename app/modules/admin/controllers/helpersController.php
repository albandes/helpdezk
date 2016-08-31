<?php
class helpers extends Controllers {

    public function getDiasUteis(){
	
	// Armazena o horário de trabalho para cada dia útil da semana
	$db = new helpers();
	$rsUtil = $db->getDiasUteis();
	$DiasUteis = array();
	while (!$rsUtil->EOF){
		$INI_MANHA = sepHoraMin(addZero($rsUtil->fields['begin_morning']));
		$FIN_MANHA = sepHoraMin(addZero($rsUtil->fields['end_morning']));
		$INI_TARDE = sepHoraMin(addZero($rsUtil->fields['begin_afternoon']));
		$FIN_TARDE = sepHoraMin(addZero($rsUtil->fields['end_afternoon']));
	
		$DiasUteis[$rsUtil->fields['num_day_week']] = array(
		"DIA_SEMANA" => $rsUtil->fields['num_day_week'],
		"HOR_INI_MANHA" => $INI_MANHA[0],
		"MIN_INI_MANHA" => $INI_MANHA[1],
		"HOR_FIN_MANHA" => $FIN_MANHA[0],
		"MIN_FIN_MANHA" => $FIN_MANHA[1],
		"HOR_INI_TARDE" => $INI_TARDE[0],
		"MIN_INI_TARDE" => $INI_TARDE[1],
		"HOR_FIN_TARDE" => $FIN_TARDE[0],
		"MIN_FIN_TARDE" => $FIN_TARDE[1]);
		$rsUtil->MoveNext();
	}
	return $DiasUteis;
    }
    
    public function getFeriados(){
	// seleciona os feriados
	$db= new holidays_model();
        $where="holiday_date >='".date('y/m/d')."'";
	$rsFeriados = $db->selectHoliday($where);
	$Feriados = array();
	while (!$rsFeriados->EOF){
		//array_push($Feriados, $rsFeriados->Fields('DAT_FERIADO'));
		$Feriados[] = $rsFeriados->fields['holiday_date'];
		$rsFeriados->MoveNext();			
	}
	return $Feriados;
    }
    
    public function getDataVcto($DAT_INICIAL, $COD_PATRIMONIO, $COD_ITEM, $COD_PRIORIDADE, $COD_SERVICO = false ){
        /********************************************************************************************************
        A logica da função é recebe um prazo e a data inicial.
        Ao longo da função, a data inicial vai sendo acrescida até se transformar na data de vencimento
        a mesma medida em que a data vai sendo acrescida, o prazo vai diminuindo até zerar
        Assim, enquanto houver um valor no prazo, vai processando...
        ********************************************************************************************************/
        $GLOBALS["DiasUteis"] = getDiasUteis();
	$GLOBALS["Feriados"] = getFeriados();
       // a DAT_INICIAL chega no formato AAAAMMDDHHMM ano mes dia hora minuto e ï¿½ convertida pra timestamp
	$DAT_INICIAL = converteTimeStamp($DAT_INICIAL); 
	

	list($PRAZO_EM_DIAS, $PRAZO_EM_MINUTOS) = getPrazo($COD_PATRIMONIO, $COD_ITEM, $COD_PRIORIDADE, $COD_SERVICO); // pega o prazo em dias e/ou horas e transforma em min
	
	// Verifica se a data inicial nï¿½o cai num feriado ou dia nï¿½o-ï¿½til. Se cair, busca o próximo dia vï¿½lido
	$DAT_INICIAL = pulaDias($DAT_INICIAL, $PRAZO_EM_DIAS);

	// pega o horï¿½rio de trabalho data de vencimento. $HOR passa a ser um array com todas as horas e minutos limites de trabalho
	$HOR = getHorarioTrabalho($DAT_INICIAL); 
	
	$processaTarde = true;
	while ($PRAZO_EM_MINUTOS > 0){
		// armazena o horï¿½rio final de trabalho no perï¿½odo matutino
		$FIN_MANHA = converteTimeStamp(strftime("%Y%m%d",$DAT_INICIAL).$HOR["HOR_FIN_MANHA"].$HOR["MIN_FIN_MANHA"]);

		//if (strftime("%H%M", $DAT_INICIAL) < $HOR["HOR_FIN_MANHA"].$HOR["MIN_FIN_MANHA"]){
		// se a hora de abertura for menor do que a hora final de trabalho da manhï¿½, tem um tempo pra resolver jï¿½ de manhï¿½
		if (dateDiff("n", $DAT_INICIAL, $FIN_MANHA) > 0){
			// TEMPO_MANHA armazena quanto tempo tem pra resolver atï¿½ o final da manhï¿½
			$TEMPO_MANHA = dateDiff("n", $DAT_INICIAL, $FIN_MANHA);
			
			// se tiver tempo suficiente pra resolver de manhï¿½, ou seja, o tempo disponï¿½vel ï¿½ maior que o prazo
			if ($TEMPO_MANHA >= $PRAZO_EM_MINUTOS){
				// a data do vencimento passa a ser a data de abertura acrescida do prazo
				$DAT_INICIAL = mktime(extHora($DAT_INICIAL), extMin($DAT_INICIAL)+$PRAZO_EM_MINUTOS, 0, extMes($DAT_INICIAL), extDia($DAT_INICIAL), extAno($DAT_INICIAL));				
				$PRAZO_EM_MINUTOS = 0;
			}else{
				// se nï¿½o puder ser resolvido sï¿½ de manhï¿½, a data ï¿½ acrescida do tempo que tem-se de manhï¿½
				$DAT_INICIAL = mktime(extHora($DAT_INICIAL), extMin($DAT_INICIAL)+$TEMPO_MANHA, 0, extMes($DAT_INICIAL), extDia($DAT_INICIAL), extAno($DAT_INICIAL));
				// esse tempo que foi acrescido ï¿½ data, ï¿½ retirado do prazo
				$PRAZO_EM_MINUTOS -= $TEMPO_MANHA;
			}
		}

		// apï¿½s verificar o perï¿½odo da manhï¿½, se ainda tiver prazo pra fazer...
		if ($PRAZO_EM_MINUTOS > 0){	
			// armazena o perï¿½odo inicial e final da tarde
			$INI_TARDE = converteTimeStamp(strftime("%Y%m%d",$DAT_INICIAL).$HOR["HOR_INI_TARDE"].$HOR["MIN_INI_TARDE"]);
			$FIN_TARDE = converteTimeStamp(strftime("%Y%m%d",$DAT_INICIAL).$HOR["HOR_FIN_TARDE"].$HOR["MIN_FIN_TARDE"]);
			// quanto tempo (em minutos) tem pra fazer ï¿½ tarde
			$TEMPO_TARDE = dateDiff("n", $INI_TARDE, $FIN_TARDE);

			// se a data inicial form maior que o inï¿½cio da tarde, tï¿½ tranquilo, ï¿½ sï¿½ comeï¿½ar
			//if (strftime("%H%M", $DAT_INICIAL) > strftime("%H%M", $INI_TARDE)){
			if (dateDiff("n", $INI_TARDE, $DAT_INICIAL) > 0){
				// se a solcitaï¿½ï¿½o foi aberta depois do expediente, comeï¿½a no outro dia
				if (strftime("%H%M", $DAT_INICIAL) > strftime("%H%M", $FIN_TARDE)){
					// acresencta um dia na data inicial					
					$DAT_INICIAL = mktime(extHora($DAT_INICIAL), extMin($DAT_INICIAL), 0, extMes($DAT_INICIAL), extDia($DAT_INICIAL)+1, extAno($DAT_INICIAL));					
					// se o dia seguinte (acresentado acima) for feriado ou dia nï¿½o-ï¿½til, pula
					$DAT_INICIAL = pulaDias($DAT_INICIAL, 0);
					$HOR = getHorarioTrabalho($DAT_INICIAL); // pega o horï¿½rio de trabalho nova data
					// o inï¿½cio passa a ser o horï¿½rio inicial de trabalho do dia seguinte (jï¿½ calculado)
					$DAT_INICIAL = mktime($HOR["HOR_INI_MANHA"], $HOR["MIN_INI_MANHA"], 0, extMes($DAT_INICIAL), extDia($DAT_INICIAL), extAno($DAT_INICIAL));
					// a variï¿½vel abaixo serve para, logo abaixo, nï¿½o ser processada a tarde, caso pule pro outro dia
					$processaTarde = false;					
				}else{ // se tem tempo ï¿½ tarde pra resolver, calcula quanto tempo tem					
					$TEMPO_TARDE = dateDiff("n", $DAT_INICIAL, $FIN_TARDE);
				}
			}else if ($TEMPO_TARDE){ // se a data nï¿½o ï¿½ maior que o inï¿½cio da tarde, ï¿½ por que caiu no intervalo do meio dia
			
				$TEMPO_INTERVALO = dateDiff("n", $DAT_INICIAL, $INI_TARDE);
				$DAT_INICIAL = mktime(extHora($DAT_INICIAL), extMin($DAT_INICIAL)+$TEMPO_INTERVALO, 0, extMes($DAT_INICIAL), extDia($DAT_INICIAL), extAno($DAT_INICIAL));
				$TEMPO_TARDE = dateDiff("n", $DAT_INICIAL, $FIN_TARDE);

			}
			
			// se a data caiu dentro do perï¿½odo da tarde....
			if ($processaTarde){ 
				// se o tempo que se tem pra resolver ï¿½ tarde ï¿½ maior do que meu prazo, tï¿½ tranquilo...
				if ($TEMPO_TARDE >= $PRAZO_EM_MINUTOS){
					// a data inicial ï¿½ acrescida de quantos minutos eu tenho pra resolver, gerando a data finall
					$DAT_INICIAL = mktime(extHora($DAT_INICIAL), extMin($DAT_INICIAL)+$PRAZO_EM_MINUTOS, 0, extMes($DAT_INICIAL), extDia($DAT_INICIAL), extAno($DAT_INICIAL));
					$PRAZO_EM_MINUTOS = 0;
				}else{// se o tempo que eu tenho pra resolver ï¿½ tarde, nï¿½o basta....
					// desconta todo o tempo que eu tenho ï¿½ tarde do tempo que eu tenho pra resolver	
					$PRAZO_EM_MINUTOS -= $TEMPO_TARDE;	
					// pual pro dia seguinte					
					$DAT_INICIAL = mktime(0, 0, 0, extMes($DAT_INICIAL), extDia($DAT_INICIAL)+1, extAno($DAT_INICIAL));					
					$DAT_INICIAL = pulaDias($DAT_INICIAL, 0); // verifica se o dia seguinte nï¿½o ï¿½ feriado nem dia nï¿½o-ï¿½til
					$HOR = getHorarioTrabalho($DAT_INICIAL); // pega o horï¿½rio de trabalho data de vencimento
					// a data inicial passa a ser o perï¿½odo inicial de trabalho do dia do vencimento
					$DAT_INICIAL = mktime($HOR["HOR_INI_MANHA"], $HOR["MIN_INI_MANHA"], 0, extMes($DAT_INICIAL), extDia($DAT_INICIAL), extAno($DAT_INICIAL));					
				}
			}
		}
		$processaTarde = true;
	}
	if ($banco_novo == true){
		$DAT_VENCIMENTO = date("YmdHi00",$DAT_INICIAL);
	}else{
		$DAT_VENCIMENTO = date("YmdHi",$DAT_INICIAL);
	}
	return $DAT_VENCIMENTO;
    }
    function addZero($HORA){
		if (strlen($HORA)==3){
			return "0".$HORA;
		}elseif (strlen($HORA)==4){	
			return $HORA;
		}else{
			return 0;
		}
    }
    function sepHoraMin($HORA){
	$retorno[] = substr($HORA, 0,2);
	$retorno[] = substr($HORA,2,2);
	return $retorno;
    }
    
    function getPrazo($COD_PATRIMONIO, $COD_ITEM, $COD_PRIORIDADE, $COD_SERVICO = false){
	
	// se tiver COD_PATRIMONIO e este patrimï¿½nio tiver tempo, pega pelo tempo do patrimï¿½nio
	if ($COD_PATRIMONIO){
		$SQL = " select NUM_DIA_ATENDIMENTO, NUM_HORA_ATENDIMENTO from hdk_patrimonio where COD_PATRIMONIO = " . $COD_PATRIMONIO;
		$rsPatrimonio = $conexao->Execute($SQL) or die("<b>$SQL</b>".$conexao->ErrorMsg());
		$rsPatrimonioRows = $rsPatrimonio->RecordCount();
		if ($rsPatrimonioRows && ($rsPatrimonio->Fields("NUM_DIA_ATENDIMENTO") || $rsPatrimonio->Fields("NUM_HORA_ATENDIMENTO"))){
			$NUM_DIA_ATENDIMENTO = $rsPatrimonio->Fields("NUM_DIA_ATENDIMENTO") ? $rsPatrimonio->Fields("NUM_DIA_ATENDIMENTO") : 0;
			$NUM_HORA_ATENDIMENTO = $rsPatrimonio->Fields("NUM_HORA_ATENDIMENTO") ? $rsPatrimonio->Fields("NUM_HORA_ATENDIMENTO") : 0;
//			$retorno = ($NUM_DIA_ATENDIMENTO * 24 * 60) + ($NUM_HORA_ATENDIMENTO * 60);
			$retorno[] = $NUM_DIA_ATENDIMENTO ? $NUM_DIA_ATENDIMENTO : 0;
			$retorno[] = $NUM_HORA_ATENDIMENTO * 60;
			return $retorno;
			return false;
		}
	}

	// só vai chegar aki se não tiver código do patrimônio OU se o patrimônio não tiver nem hora nem dia
	// se vier patrimônio e este tiver OU hora OU dia, pega esses valores e já dão o return na função, parando por ali.
	// seleciona, ou o tempo do Item, ou o tempo da Prioridade
            ############ ATENÇÃO #############
	/**
	  * Os dados de tempo de atendimento e de prioridade agora são resgatados a partir do serviço.
	  * Como esta função deverá ser reescrita na nova versão, apenas alterei a query e mantive todo os resto
	  * quando for reescrita essas informações deverão ser obtidas através de um objeto Servico.
	  * @since 2008-11-12
	  */
	if (isset($COD_SERVICO)) {
		$SQL = "SELECT NUM_DIA_ATENDIMENTO, NUM_HORA_ATENDIMENTO, COD_PRIORIDADE FROM hdk_servico WHERE COD_SERVICO = ".$COD_SERVICO;	
	} else {
		///programas que ainda não foram alterados podem manter-se usando o código do item
		$SQL = "SELECT NUM_DIA_ATENDIMENTO, NUM_HORA_ATENDIMENTO, COD_PRIORIDADE FROM hdk_servico WHERE COD_ITEM = ".$COD_ITEM;	
	}	
	$rsItem = $conexao->Execute($SQL) or die("<b>$SQL</b>".$conexao->ErrorMsg().__FILE__.'::'.__LINE__);

	// se tiver tanto a qtd de dias de atendimento ou a qtd de horas...
	if ($rsItem->Fields("NUM_DIA_ATENDIMENTO") || $rsItem->Fields("NUM_HORA_ATENDIMENTO")){
		$NUM_DIA_ATENDIMENTO = $rsItem->Fields("NUM_DIA_ATENDIMENTO");
		$NUM_HORA_ATENDIMENTO = $rsItem->Fields("NUM_HORA_ATENDIMENTO");
	}
	// se nï¿½o houver nem qtd de dia nem de horas, porï¿½m houver um uma prioridade cadastrada para o ITEM...
	else if ($rsItem->Fields('COD_PRIORIDADE')){
		$SQL = "select NUM_HORA_ATENDIMENTO, NUM_DIA_ATENDIMENTO from hdk_prioridade
				where COD_PRIORIDADE = ".$rsItem->Fields('COD_PRIORIDADE');
		$rsPrior = $conexao->Execute($SQL) or die("<b>$SQL</b>".$conexao->ErrorMsg());
		$NUM_DIA_ATENDIMENTO = $rsPrior->Fields("NUM_DIA_ATENDIMENTO");
		$NUM_HORA_ATENDIMENTO = $rsPrior->Fields("NUM_HORA_ATENDIMENTO");
	}else{ // se nï¿½o houver tempo de atendimento nem prioridade do item, pega o tempo do cadastro de prioridade
		$SQL = "select NUM_HORA_ATENDIMENTO, NUM_DIA_ATENDIMENTO from hdk_prioridade
				where COD_PRIORIDADE = " . $COD_PRIORIDADE;
		$rsPrior2 = $conexao->Execute($SQL) or die("<b>$SQL</b>".$conexao->ErrorMsg());
		// Se nï¿½o tiver registro, ou nï¿½o tiver nem dia nem hora, zera...
		if ($rsPrior2->EOF || (!$rsPrior2->Fields("NUM_DIA_ATENDIMENTO") && !$rsPrior2->Fields("NUM_HORA_ATENDIMENTO"))){
			$NUM_DIA_ATENDIMENTO = 0;
			$NUM_HORA_ATENDIMENTO = 0;			
		}else{
			$NUM_DIA_ATENDIMENTO = $rsPrior2->Fields("NUM_DIA_ATENDIMENTO");			
			$NUM_HORA_ATENDIMENTO = $rsPrior2->Fields("NUM_HORA_ATENDIMENTO");
		}
	}

	//$retorno = ($NUM_DIA_ATENDIMENTO * 24 * 60) + ($NUM_HORA_ATENDIMENTO * 60);
	$retorno[] = $NUM_DIA_ATENDIMENTO ? $NUM_DIA_ATENDIMENTO : 0;
	$retorno[] = $NUM_HORA_ATENDIMENTO * 60;
	return $retorno;
    }
}
?>
