$(document).ready(function(){

    $("#flexigrid2").flexigrid({
        url: 'typepersonpermission/json/',  
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        colModel : [
        {
            display: ' ', 
            name : 'icon', 
            width : 30, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name', 
            width : 250, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Module'].replace (/\"/g, ""), 
            name : 'module', 
            width : 250, 
            sortable : true, 
            align: 'left'
        }
    ],

        buttons : [
        {
            name: aLang['permissions'].replace (/\"/g, ""),  
            bclass: 'permission', 
            onpress: permission
        }
        ],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""),  
            name : 'tbp.name', 
            isdefault: true
        }
					
        ],

        sortname: "tbp.idprogram",
        sortorder: "DESC",
        usepager: true,
        title: ' :: '+aLang['pgr_type_permission'].replace (/\"/g, ""),
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: 'auto',
        height: $(window).height()-206,
        resizable: false,
        minimizado: false,
        singleSelect: true
    }); 
			 
			 
   
    function novo(com, grid){       
     if (access[1]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        if($('.trSelected',grid).length>0){
            var items = $('.trSelected',grid);
            var itemlist ='';
            for(i=0;i<items.length;i++){
                itemlist+= items[i].id.substr(3);
            }
            $("#content").load('typepersonpermission/insertperm/id/'+itemlist);            
        }
        else{
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}
                
		
		
		   
});//init		 
	
function permission(com,grid){
   if (access[2]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        if($('.trSelected',grid).length>0){
            var items = $('.trSelected',grid);
            var itemlist ='';
            for(i=0;i<items.length;i++){
                itemlist+= items[i].id.substr(3);
            }
            $("#content").load('typepersonpermission/manageperm/id/'+itemlist);
        }
        else{
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}