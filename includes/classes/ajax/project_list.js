$(document).ready(function(){
    jQuery.noConflict();
		
    $("#flexigrid2").flexigrid({
        url: 'projects/json/',  
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        colModel : [
        {
            display: aLang['percent_complete'].replace (/\"/g, ""), 
            name : 'percent', 
            width : 80, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Active'].replace (/\"/g, ""), 
            name : 'active', 
            width : 30, 
            sortable : true, 
            align: 'center'
        },
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name_project', 
            width : 250, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Permission_group'].replace (/\"/g, ""), 
            name : 'group_name', 
            width : 135, 
            sortable : true, 
            align: 'center'
        },
        {
            display: aLang['Company'].replace (/\"/g, ""), 
            name : 'company', 
            width : 80, 
            sortable : true, 
            align: 'center'
        },
        {
            display: aLang['Initial_date'].replace (/\"/g, ""), 
            name : 'begin_date', 
            width : 80, 
            sortable : true, 
            align: 'center'
        },
        {
            display: aLang['Finish_date'].replace (/\"/g, ""), 
            name : 'end_date', 
            width : 80, 
            sortable : true, 
            align: 'center'
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
            name: aLang['Delete'].replace (/\"/g, ""),  
            bclass: 'delete', 
            onpress: encerra
        }
        ],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name_project', 
            isdefault: true
        }					
        ],
        sortname: "name_project",
        sortorder: "asc",
        usepager: true,
        title: ' :: '+aLang['Projects'].replace (/\"/g, "") ,
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: '90%',
        height: 400,     
        resizable: false,
        minimizado: false,
        singleSelect : true
    }
    ); 
			 
			 
    function mostra(com, grid) {
        if (com == 'Em aberto') {
            somapar2('N');
        } else if (com == 'Encerrados') {
            somapar2('S');
        }else if (com == 'Todos') {
            somapar2('A');
        }
    }
    
    function novo(){
//        if (access[1]=='N'){
//            alert(aLang['Alert_no_permission'].replace (/\"/g, ""));
//        }
//        else{
            $("#pop").fadeIn(300, function(){
                $("#pop").load('projects/projectInsert/');
            });
//        }
    }
		
		   
});//init		 
	
function somapar(com2){
    $('#flexigrid2').flexOptions({
        newp:1, 
        params:[{
            name:'COD_STATUS', 
            value: com2
        }]
    });
    refresh2();
}           
	
function refresh(){
    $('#flexigrid2').flexReload();
}	

function encerra(com, grid){
    
    if($('.trSelected',grid).length>0){
        if(confirm(aLang['Delete'].replace (/\"/g, "") +" "+ $('.trSelected',grid).length +" "+ aLang['Items'].replace (/\"/g, ""))){
            var items = $('.trSelected',grid),
                id = items[0].id.substr(3);
            $.ajax({
                type: "POST",
                url: "projects/delete",
                data: "id="+id,
                success: function(data){
                    console.log(data);
                    alert(aLang['Alert_deleted'].replace (/\"/g, ""));
                    $("#flexigrid2").flexReload();
                }
            });
        }
    }
}
function edit(com,grid){

    if($('.trSelected',grid).length>0){
        if($('.trSelected',grid).length>1){
            alert(aLang['Alert_select_just_one'].replace (/\"/g, ""));
        }
        else{
            var items = $('.trSelected',grid);
            var itemlist ='';
            for(i=0;i<items.length;i++){
                itemlist+= items[i].id.substr(3);
            }
            window.open('projects/editform/id/'+itemlist,'','height = 400, width = 740');
        }
    }
    else{
        alert(aLang['Alert_select_one'].replace (/\"/g, ""));
    }
}
function disable(com, grid){
    if($('.trSelected',grid).length>0){
        if(confirm(aLang['Deactivate'].replace (/\"/g, "") +" "+ $('.trSelected',grid).length +" "+ aLang['Items'].replace (/\"/g, "")+'?')){
            var items = $('.trSelected',grid),
                id = items[0].id.substr(3);
            $.ajax({
                type: "POST",
                url: "projects/deactivate",
                data: "id="+id,
                success: function(data){
                    alert(aLang['Alert_deactivated'].replace (/\"/g, ""));
                    $("#flexigrid2").flexReload();
                }
            });
        }
    }
}
function enable(com, grid){
    if($('.trSelected',grid).length>0){
        if(confirm(aLang['Activate'].replace (/\"/g, "") +" "+ $('.trSelected',grid).length +" "+ aLang['Items'].replace (/\"/g, "")+'?')){
            var items = $('.trSelected',grid),
                id = items[0].id.substr(3);
            $.ajax({
                type: "POST",
                url: "projects/enable",
                data: "id="+id,
                success: function(data){
                    alert(aLang['Alert_activated'].replace (/\"/g, ""));
                    $("#flexigrid2").flexReload();
                }
            });
        }
    }
}	
	