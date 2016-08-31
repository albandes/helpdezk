$(document).ready(function(){
		
	
    $("#flexigrid2").flexigrid({
        url: 'user/json/',

        dataType: 'json',
        colModel : [
        {
            display: '&nbsp;', 
            name : 'des', 
            width : 34, 
            sortable : false, 
            align: 'left'
        },
					

        {
            display: 'N&deg;', 
            name : 'sol.COD_SOLICITACAO', 
            width : 85, 
            sortable : true, 
            align: 'left'
        },

        {
            display: 'Abertura', 
            name : 'sol.DAT_CADASTRO', 
            width : 110, 
            sortable : true, 
            align: 'left'
        },

        {
            display:  'De', 
            name : 'usu.NOM_USUARIO', 
            width : 240, 
            sortable : true, 
            align: 'left'
        },

        {
            display: 'Assunto', 
            name : 'sol.NOM_ASSUNTO', 
            width : 200, 
            sortable : true, 
            align: 'left'
        },

        {
            display: 'Prz. Entrega', 
            name : 'sol.DAT_VENCIMENTO_ATENDIMENTO', 
            width : 110, 
            sortable : true, 
            align: 'left'
        },

        {
            display: 'Respons&aacute;vel', 
            name : 'nom_responsavel', 
            width : 130, 
            sortable : false, 
            align: 'left'
        },

        {
            display: 'Status', 
            name : 'stat.DES_STATUS_USUARIO', 
            width : 155, 
            sortable : true, 
            align: 'left'
        }
        ],

        buttons : [
        {
            name: 'Novas', 
            bclass: 'novas', 
            onpress : mostra
        },

        {
            name: 'Em atendimento', 
            bclass: 'atendimento', 
            onpress : mostra
        },

//        {
//            name: 'Aguardando minha aprova&ccedil;&atilde;o', 
//            bclass: 'aguardando', 
//            onpress : mostra
//        },

        {
            name: 'Encerradas', 
            bclass: 'encerradas', 
            onpress : mostra
        },

        {
            name: 'Rejeitadas', 
            bclass: 'aprovadas', 
            onpress : mostra
        },

        {
            name: 'Todas', 
            bclass: 'todas', 
            onpress : mostra
        }
        ],

        searchitems : [
        {
            display: 'N&deg;', 
            name : 'code_request', 
            isdefault: true
        },

        {
            display: 'Assunto', 
            name : 'subject'
        },

        {
            display: 'Descri&ccedil;ao', 
            name : 'description'
        }
        ],

        sortname: "req.entry_date",
        sortorder: "asc",
        usepager: true,
        title: 'Minhas Solicita&ccedil;&otilde;es',
        useRp: true,
        rp: 15,
        showTableToggleBtn: true,
        width: 'auto',
        height: 512,					
        useFiltro: true,
        filtro: 0

					
    }
    ); 
    function mostra(com){
        if (com=='Em atendimento'){
            trocaclasse('btn2');
            somapar('3');
        }
        if (com=='Novas'){    
            trocaclasse('btn1');
            somapar('1');
        }
        if (com=='Aguardando minha aprova&ccedil;&atilde;o'){   
            trocaclasse('btn3');                            
            somapar('4');
        }
        if (com=='Encerradas'){                             
            trocaclasse('btn4');  
            somapar('5');
        }
        if (com=='Rejeitadas'){                   
            trocaclasse('btn5');
            somapar('6');
        }
        if (com=='Todas'){                   
            trocaclasse('btn6');
            somapar('0');
        }				
    }		  
  
    $(document.getElementById('btn1')).css({'background-color':'#FEC075'});
});//init		   
		
function refresh(){
    $('#flexigrid2').flexReload();
}

function somapar(com2){
    $('#flexigrid2').flexOptions({newp:1, params:[{name:'COD_STATUS', value: com2}]}).flexReload();
}	
function trocaclasse(btn){

    //remove a classe ativo 
    $('.tDiv2').find('.ativo').removeClass('ativo');
    //add a classe ativo ao btn clicado
    $(btn).addClass('ativo');
} 