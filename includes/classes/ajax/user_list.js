$(document).ready(function(){

    
    $("#flexigrid4").flexigrid({
        url: 'requestsearchuser/json/',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        colModel : [
        {
            display: aLang['User'].replace (/\"/g, ""), 
            name : 'person.name', 
            width : 250, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Company'].replace (/\"/g, ""), 
            name : 'juridical.name', 
            width : 150, 
            sortable : true, 
            align: 'left'
        }
        ],

        buttons : [		
        {name: 'A',  bclass: '', onpress: search},
        {name: 'B',  bclass: '', onpress: search},
        {name: 'C',  bclass: '', onpress: search},
        {name: 'D',  bclass: '', onpress: search},
        {name: 'E',  bclass: '', onpress: search},
        {name: 'F',  bclass: '', onpress: search},
        {name: 'G',  bclass: '', onpress: search},
        {name: 'H',  bclass: '', onpress: search},
        {name: 'I',  bclass: '', onpress: search},
        {name: 'J',  bclass: '', onpress: search},
        {name: 'K',  bclass: '', onpress: search},
        {name: 'L',  bclass: '', onpress: search},
        {name: 'M',  bclass: '', onpress: search},
        {name: 'N',  bclass: '', onpress: search},
        {name: 'O',  bclass: '', onpress: search},
        {name: 'P',  bclass: '', onpress: search},
        {name: 'R',  bclass: '', onpress: search},
        {name: 'S',  bclass: '', onpress: search},
        {name: 'T',  bclass: '', onpress: search},
        {name: 'U',  bclass: '', onpress: search},
        {name: 'V',  bclass: '', onpress: search},
        {name: 'W',  bclass: '', onpress: search},
        {name: 'X',  bclass: '', onpress: search},
        {name: 'Y',  bclass: '', onpress: search},
        {name: 'Z',  bclass: '', onpress: search},
        {separator: true},
        {name: 'OK     ',  bclass: 'text', onpress: troca}
        ],

        searchitems : [
        {
            display: 'Nome', 
            name : 'person.name', 
            isdefault: true
        }					
        ],
        sortname: "person.name",
        sortorder: "asc",
        usepager: true,
        title: ' :: Usuários',
        useRp: true,
        rp: 10,
        showTableToggleBtn: false,
        width: '91%',
        height: 250,     
        resizable: false,
        minimizado: false,
        singleSelect: true
    }
    ); 
			 
			 
    function somapar(com2){
        $('#flexigrid4').flexOptions({newp:1, params:[{name:'letter', value: com2}]});
        refresh();
    }
    function search(com3){                    	
        $('#flexigrid4').flexOptions({newp:1, params:[{name:'letter', value: com3}]}).flexReload();
    }
		
		   
});//init		 	
function somapar(com2){
    $('#flexigrid4').flexOptions({
        newp:1, 
        params:[{
            name:'letter', 
            value: com2
        }]
        });
    refresh2();
}		
function refresh(){
    $('#flexigrid4').flexReload();
}	

function troca(com, grid){
    if($('.trSelected',grid).length>0){
        if($('.trSelected',grid).length>1){
            alert("Selecione apenas 1 usuário por vez.");
        }
        else{
            var ids = $('.trSelected',grid);
            var idlist ='';
            for(i=0;i<ids.length;i++){                
                idlist+= ids[i].id.substr(3);                
            }
            $.ajax({
                type: "POST",
                dataType: "text",
                url: "requestsearchuser/getUser/",
                data: "id="+idlist,
                success: function(data){
                    var data = data.split('|'),
                    	$formReq = $(document.getElementById('formNewRequest'));
                   
                    $formReq.find('#person').val(data[1]);
                    $formReq.find('#idperson').val(data[0]);
                    $formReq.find('#idjuridical').val(data[2]);
                    $(document.getElementById('modalNewRequest')).css('display','none');
                    objModal.openModal("modalNewRequest");
                }
            });
            
            
        }
     }else{
        alert("Escolha um usuário.");
    }
    //$("#person").val("");
    //$("#personid").val("");
}




