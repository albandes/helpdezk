<?php
require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');
//error_hdk_avisosing(E_ALL);


$sql =	"
		SELECT
		   topico_alerta_grupos.COD_GRUPO,
		   topico_alerta_empresas.COD_EMPRESA,
		   alertas.TIT_ALERTA,
		   alertas.DES_ALERTA,
		   date_format(alertas.DAT_CADASTRO,'%d/%m/%Y') as DAT_CADASTRO
		FROM topico_alerta_grupos
		   RIGHT JOIN topico_alerta
			 ON (topico_alerta.COD_TOPICO = topico_alerta_grupos.COD_TOPICO)
		   JOIN alertas
			 ON (topico_alerta.COD_TOPICO = alertas.COD_TOPICO)
		   LEFT JOIN topico_alerta_empresas
			 ON (topico_alerta.COD_TOPICO = topico_alerta_empresas.COD_TOPICO)
		where (COD_TIPO_VISUALIZACAO = 2
				OR COD_TIPO_VISUALIZACAO = 3)
		";
$rs = $conexao->Execute($sql);

/*
		--      AND (alertas.DAT_FIM_VALIDADE > NOW()
		--           OR alertas.DAT_FIM_VALIDADE IS NULL)

*/

//die($sql);	

if(!$rs) die("Erro: " . $conexao->ErrorMsg() . "<br>" . $sql );

$html =	"
		<script type=\"text/javascript\">  
			$(document).ready(function(){
				$('#hdk_avisos tr:odd').addClass('odd');
				$('#hdk_avisos tr:not(.odd)').hide();
				$('#hdk_avisos tr:first-child').show();
				
				$('#hdk_avisos tr.odd').click(function(){
					
					/*
					Isto para o IE 8, se não for o ie 8, bastaria:
					$(this).next('tr').show(); 
					$(this).find('.arrow').toggleClass('up'); 
					*/
					
					var tr = $(this).next(\"tr\"); 
				   
					if(tr.css(\"display\") == \"none\") 
						tr.css(\"display\", \"table-row\"); 
					else 
						tr.css(\"display\", \"none\"); 
				   
					$(this).find(\".arrow\").toggleClass(\"up\"); 

				});
			});
		</script>    
		";
		

$html .= 	"
    <table id=\"hdk_avisos\">
        <tr>
            <th>Data</th>
            <th>Alerta</th>
            <th></th>
        </tr>
			";

while (!$rs->EOF) {
	$html .= "
        <tr>
            <td>".$rs->fields['DAT_CADASTRO']."</td>
            <td>".$rs->fields['TIT_ALERTA']."</td>
            <td><div class=\"arrow\"></div></td>
        </tr>
        <tr>
            <td colspan=\"3\">
                <h4>Descrição do Alerta</h4>
                <ul>
                    <li>".$rs->fields['DES_ALERTA']."</li>
                </ul>   
            </td>
        </tr>


			";
	$rs->MoveNext();
}

$html .= "</table>";

$arr = array ('tabela'=> utf8_encode($html));

echo json_encode($arr);
	
//echo $json;


?>