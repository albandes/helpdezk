$(document).ready(function(){
    
		
    $("#flexigrid2").flexigrid({
        url: 'operator/json/',  

        dataType: 'json',
        colModel : [
        {
            display: '&nbsp;', 
            name : 'des', 
            width : 34, 
            sortable : false, 
            align: 'center'
        },					

        {
            display: '<img src="../includes/images/ico_anexos.gif" >', 
            name : 'anexo', 
            width : 18, 
            sortable : false, 
            align: 'center'
        },

        {
            display: 'N&deg;', 
            name : 'req.code_request', 
            width : 85, 
            sortable : true, 
            align: 'left'
        },

        {
            display: 'Abertura', 
            name : 'req.entry_date', 
            width : 115, 
            sortable : true, 
            align: 'center'
        },

        {
            display: 'Empresa', 
            name : 'comp.idperson', 
            width : 70, 
            sortable : true, 
            align: 'left'
        },

        {
            display: 'De', 
            name : 'pers.idperson', 
            width : 110, 
            sortable : true, 
            align: 'left'
        },

        {
            display: 'Tipo', 
            name : 'req_type.idtypeO', 
            width : 90, 
            sortable : true, 
            align: 'left'
        },

        {
            display: 'Item', 
            name : 'item.iditem', 
            width : 90, 
            sortable : true, 
            align: 'left'
        },

        {
            display: 'Servi&ccedil;o', 
            name : 'serv.idservice', 
            width : 80, 
            sortable : true, 
            align: 'left'
        },

        {
            display: 'Assunto', 
            name : 'req.subject', 
            width : 125, 
            sortable : true, 
            align: 'left'
        },

        {
            display: 'Status', 
            name : 'req.idstatus', 
            width : 150, 
            sortable : true, 
            align: 'left'
        },

        {
            display: 'Respons&aacute;vel', 
            name : 'resp.name', 
            width : 80, 
            sortable : false, 
            align: 'left'
        },

        {
            display: 'Prioridade', 
            name : 'req.idpriority', 
            width : 70, 
            sortable : true, 
            align: 'center'
        },

        {
            display: 'Vencimento', 
            name : 'req.expire_date', 
            width : 115, 
            sortable : true, 
            align: 'center'
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

        {
            name: 'Aguardando minha aprova&ccedil;&atilde;o', 
            bclass: 'aguardando', 
            onpress : mostra
        },

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
            name : 'COD_SOLICITACAO', 
            isdefault: true
        },

        {
            display: 'Assunto', 
            name : 'NOM_ASSUNTO'
        },

        {
            display: 'Descri&ccedil;ao', 
            name : 'DES_SOLICITACAO'
        },

        {
            display: 'Etiqueta', 
            name : 'NUM_ETIQUETA'
        },

        {
            display: 'N&deg; serie equipamento', 
            name : 'NUM_SERIE'
        }
        ],

        sortname: "DAT_ORDER",
        usepager: true,
        title: 'Solicita&ccedil;&otilde;es',
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: 'auto',
        height: 435,
        useTipconsulta: true,
        tipconsulta: 102,
        useDatvencimento: true,
        datvencimento: 0

					
    }
    ); 
    function mostra(com){
        if (com=='Em atendimento'){
            trocaclasse(this);
            somapar('3');
        }
        if (com=='Novas'){                    
            //$('btn2', 'btn3', 'btn4', 'btn5', 'btn6').css({'background-color':'#E9E9E9'})                     
            //$(btn1).css({'background-color':'#FEC075'})
            trocaclasse(this);
            somapar('1');
        }
        if (com=='Aguardando minha aprova&ccedil;&atilde;o'){   
            trocaclasse(this);                         
            somapar('4');
        }
        if (com=='Encerradas'){                             
            trocaclasse(this);
            somapar('5');
        }
        if (com=='Rejeitadas'){                   
            trocaclasse(this);
            somapar('6');
        }
        if (com=='Todas'){                   
            trocaclasse(this);
            somapar('0');
        }				
    }
    
    $(document.getElementById('btn1')).addClass('ativo');
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