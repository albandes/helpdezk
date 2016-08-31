

$(document).ready(function(){

    jQuery.noConflict();
		
    $("#flexigrid2").flexigrid({
        url: 'department/json/',  
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        colModel : [
        {
            display: aLang['Company'].replace (/\"/g, ""), 
            name : 'icon', 
            width : 120, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Department'].replace (/\"/g, ""), 
            name : 'answer', 
            width : 160, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['status'].replace (/\"/g, ""), 
            name : 'status', 
            width : 50, 
            sortable : true, 
            align: 'left'
        }
        ],

        buttons : [		
        {
            name: aLang['New'].replace (/\"/g, ""), 
            bclass: 'add', 
            onpress: novo
        },
        
        {
            separator:true
        },

        {
            name: aLang['edit'].replace (/\"/g, ""), 
            bclass: 'edit', 
            onpress: edit
        },

        {
            separator:true
        },

        {
            name: aLang['Deactivate'].replace (/\"/g, ""), 
            bclass: 'encerra', 
            onpress: disable
        },
        
        {
            separator:true
        },

        {
            name: aLang['Activate'].replace (/\"/g, ""), 
            bclass: 'activate', 
            onpress: enable
        },
        
        {
            separator:true
        }
        ,

        {
            name: aLang['Delete'].replace (/\"/g, ""), 
            bclass: 'delete', 
            onpress: encerra
        }
        ],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name', 
            isdefault: true
        }					
        ],
        sortname: "name",
        sortorder: "asc",
        usepager: true,
        title: ' :: '+aLang['Departments'].replace (/\"/g, ""),
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: '90%',
        height: 400,     
        resizable: false,
        minimizado: false		
    }
    ); 
			 
			 
    function mostra2(com, grid) {
        if (com == 'Jurídicas') {
            somapar2('N');
        } else if (com == 'Fisicas') {
            somapar2('S');
        }else if (com == 'Todos') {
            somapar2('A');
        }
    }
		
    function novo2() {
		
        var janela = window.open('person/forminsert','','width=600,height=210, left=parseInt((screen.availWidth/2)-(630/2)),left=parseInt((screen.availHeight/2)-(208/2))');
        janela.onbeforeunload = function() {
            setTimeout('refresh2()',1000);
        }
    }
		
		   
});//init		 
	
function somapar2(com2){
    $('#flexigrid2').flexOptions({
        newp:1, 
        params:[{
            name:'TIPE_PERSON', 
            value: com2
        }]
        });
    refresh2();
}	
	
function refresh2(){
    $('#flexigrid2').flexReload();
}	
        
function edita(){
    $('#flexigrid2').flexReload();
}
    
function encerra2(com, grid){
    if($('.trSelected',grid).length>0){
        if(confirm('Deletar ' + $('.trSelected',grid).length + ' items?')){
            var items = $('.trSelected',grid);
            var itemlist ='';
            for(i=0;i<items.length;i++){
                itemlist+= items[i].id.substr(3)+",";
            }
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "#",
                data: "items="+itemlist,
                success: function(data){
                    //alert("Query: "+data.query+" - Total affected rows: "+data.total);
                    alert("Deletado com sucesso!");
                    $("#flexigrid2").flexReload();
                }
            });
        }
    } else {
        return false;
    }
}
        
	